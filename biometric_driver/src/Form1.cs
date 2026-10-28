using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;
using libzkfpcsharp;
using System.Runtime.InteropServices;
using System.Threading;
using System.IO;
using System.Diagnostics;
using Sample;

namespace Demo
{
    public class TemplateMeta
    {
        public int Fid;         // 1-based for ZKTeco DB engine
        public int ArrayIndex;  // 0-based for JavaScript client array
        public string PersonnelId;
        public string BiometricId;
        public string RFTagId;
        public string TemplateBase64;
    }

    public partial class Form1 : Form
    {
        IntPtr mDevHandle = IntPtr.Zero;
        IntPtr mDBHandle = IntPtr.Zero;
        IntPtr FormHandle = IntPtr.Zero;
        bool bIsTimeToDie = false;
        bool IsRegister = false;
        bool bIdentify = true;
        byte[] FPBuffer;
        int RegisterCount = 0;
        const int REGISTER_FINGER_COUNT = 3;

        byte[][] RegTmps = new byte[3][];
        byte[] RegTmp = new byte[2048];
        byte[] CapTmp = new byte[2048];
        int cbCapTmp = 2048;
        int cbRegTmp = 0;
        int iFid = 1;
        Thread captureThread = null;
        Thread reconnectThread = null;

        private int mfpWidth = 0;
        private int mfpHeight = 0;

        public string DeviceModel = "Unknown";
        public string DeviceSerial = "Unknown";
        public string DeviceManufacturer = "ZKTeco";

        private WebServer _webServer;
        private string _lastCapturedTemplateBase64 = "";
        private int _lastMatchedArrayIndex = -1;
        private int _lastMatchedScore = 0;
        private string _lastMatchedPersonnelId = "";
        private string _lastMatchedBiometricId = "";
        private long _lastMatchTimeMs = 0;
        private bool _lastIsUnrecognized = false;

        private readonly object _templateLock = new object();
        private readonly object _initLock = new object();
        private readonly object _dbLock = new object();

        private Dictionary<int, TemplateMeta> _templateStore = new Dictionary<int, TemplateMeta>();

        public Form1()
        {
            InitializeComponent();
        }

        public void DrawPlaceholderImage(string title, string subtitle, bool active)
        {
            try
            {
                if (!this.IsHandleCreated || this.IsDisposed) return;
                int w = picFPImg.Width > 50 ? picFPImg.Width : 200;
                int h = picFPImg.Height > 50 ? picFPImg.Height : 240;
                Bitmap bmp = new Bitmap(w, h);
                using (Graphics g = Graphics.FromImage(bmp))
                {
                    g.SmoothingMode = System.Drawing.Drawing2D.SmoothingMode.AntiAlias;
                    g.Clear(Color.FromArgb(15, 23, 42)); // Slate dark background

                    // Draw biometric sensor glass area
                    int padW = Math.Min(w - 40, 160);
                    int padH = Math.Min(h - 80, 200);
                    if (padW < 40) padW = 40;
                    if (padH < 40) padH = 40;
                    int padX = (w - padW) / 2;
                    int padY = Math.Max(10, (h - padH - 55) / 2);

                    using (var pen = new Pen(active ? Color.FromArgb(16, 185, 129) : Color.FromArgb(239, 68, 68), 2))
                    {
                        g.DrawRectangle(pen, padX, padY, padW, padH);

                        // Draw decorative biometric scanner ripples
                        Color rippleColor = active ? Color.FromArgb(45, 16, 185, 129) : Color.FromArgb(45, 239, 68, 68);
                        using (var ripplePen = new Pen(rippleColor, 2))
                        {
                            for (int r = 15; r < padW / 2 - 5; r += 15)
                            {
                                g.DrawEllipse(ripplePen, padX + padW / 2 - r, padY + padH / 2 - (int)(r * 1.25f), r * 2, (int)(r * 2.5f));
                            }
                        }
                    }

                    // Draw status texts
                    using (var font = new Font("Segoe UI", 9.5f, FontStyle.Bold))
                    using (var subFont = new Font("Segoe UI", 8f, FontStyle.Regular))
                    using (var brush = new SolidBrush(active ? Color.FromArgb(52, 211, 153) : Color.FromArgb(248, 113, 113)))
                    using (var subBrush = new SolidBrush(Color.FromArgb(148, 163, 184)))
                    {
                        StringFormat sf = new StringFormat { Alignment = StringAlignment.Center };
                        g.DrawString(title, font, brush, w / 2, h - 45, sf);
                        g.DrawString(subtitle, subFont, subBrush, w / 2, h - 25, sf);
                    }
                }

                Action act = () =>
                {
                    try
                    {
                        if (picFPImg.Image != null)
                        {
                            var old = picFPImg.Image;
                            picFPImg.Image = null;
                            old.Dispose();
                        }
                        picFPImg.Image = bmp;
                        picFPImg.Refresh();
                    }
                    catch { }
                };
                if (this.InvokeRequired) this.BeginInvoke(act);
                else act();
            }
            catch { }
        }

        public static void Log(string msg)
        {
            try
            {
                string logLine = string.Format("[{0:yyyy-MM-dd HH:mm:ss.fff}] {1}", DateTime.Now, msg);
                string path = Path.Combine(AppDomain.CurrentDomain.BaseDirectory, "server.log");
                File.AppendAllText(path, logLine + Environment.NewLine);
            }
            catch {}
        }

        private void Form1_Load(object sender, EventArgs e)
        {
            FormHandle = this.Handle;
            Log("Form1_Load started.");
            
            DrawPlaceholderImage("INITIALIZING SCANNER...", "Connecting to Live 20R / SLK 20R", false);

            _webServer = new WebServer(this);
            _webServer.Start();
            textRes.Text = "WebServer started on port 15270";
            
            // Auto init for connected device (Live 20R or SLK 20R)
            AutoInit();

            // Background auto-reconnect watcher thread in case device is unplugged/replugged
            reconnectThread = new Thread(AutoReconnectWorker);
            reconnectThread.IsBackground = true;
            reconnectThread.Start();
            Log("Form1_Load finished. Initialized: " + IsDeviceInitialized);
        }

        private void Form1_FormClosed(object sender, FormClosedEventArgs e)
        {
            Log("Form1 closing, terminating services...");
            bIsTimeToDie = true;
            if (_webServer != null) _webServer.Stop();
            CloseDevice();
            bnFree_Click(null, null);
        }

        public bool IsDeviceInitialized
        {
            get { return mDevHandle != IntPtr.Zero && mDBHandle != IntPtr.Zero; }
        }

        public int CachedTemplatesCount
        {
            get
            {
                lock (_dbLock)
                {
                    return _templateStore.Count;
                }
            }
        }

        public bool AutoInit()
        {
            lock (_initLock)
            {
                if (IsDeviceInitialized)
                {
                    Log("AutoInit: device already initialized (" + DeviceModel + ", " + DeviceSerial + ")");
                    return true;
                }

                Log("AutoInit starting...");
                try
                {
                    int ret = zkfp2.Init();
                    Log("zkfp2.Init returned: " + ret);
                    if (ret == zkfperrdef.ZKFP_ERR_OK || ret == zkfperrdef.ZKFP_ERR_ALREADY_INIT)
                    {
                        int nCount = zkfp2.GetDeviceCount();
                        Log("zkfp2.GetDeviceCount: " + nCount);
                        if (nCount > 0)
                        {
                            OpenDeviceInternal(0);
                            return IsDeviceInitialized;
                        }
                        else
                        {
                            Log("AutoInit: No ZKTeco device detected yet.");
                        }
                    }
                }
                catch (Exception ex)
                {
                    Log("AutoInit Exception: " + ex.ToString());
                }
                return false;
            }
        }

        private void AutoReconnectWorker()
        {
            while (!bIsTimeToDie)
            {
                Thread.Sleep(3000);
                if (!IsDeviceInitialized && !bIsTimeToDie)
                {
                    AutoInit();
                }
            }
        }

        public string GetLastTemplate()
        {
            lock (_templateLock)
            {
                return _lastCapturedTemplateBase64;
            }
        }

        public string GetLastCapturedTemplate(out int matchIndex, out int matchScore)
        {
            lock (_templateLock)
            {
                string tmp = _lastCapturedTemplateBase64;
                matchIndex = _lastMatchedArrayIndex;
                matchScore = _lastMatchedScore;
                _lastCapturedTemplateBase64 = "";
                _lastMatchedArrayIndex = -1;
                _lastMatchedScore = 0;
                return tmp;
            }
        }

        public string GetLastCapturedTemplate()
        {
            int dummyIdx, dummyScore;
            return GetLastCapturedTemplate(out dummyIdx, out dummyScore);
        }

        public void ClearLastTemplate()
        {
            lock (_templateLock)
            {
                _lastCapturedTemplateBase64 = "";
                _lastMatchedArrayIndex = -1;
                _lastMatchedScore = 0;
                _lastMatchedPersonnelId = "";
                _lastMatchedBiometricId = "";
                _lastMatchTimeMs = 0;
                _lastIsUnrecognized = false;
            }
        }

        public int SyncTemplates(List<TemplateMeta> templates)
        {
            lock (_dbLock)
            {
                if (mDBHandle == IntPtr.Zero)
                {
                    Log("SyncTemplates: DB handle is zero.");
                    return -1;
                }

                Stopwatch sw = Stopwatch.StartNew();
                zkfp2.DBClear(mDBHandle);
                _templateStore.Clear();

                int successCount = 0;
                int currentFid = 1; // ZKTeco DB requires 1-based ID (> 0)
                int firstBlobLen = -1;
                int firstRet = -999;
                string firstErr = "";

                foreach (var t in templates)
                {
                    if (string.IsNullOrEmpty(t.TemplateBase64))
                    {
                        if (currentFid == 1) firstErr = "TemplateBase64 is empty/null";
                        continue;
                    }
                    byte[] blob = WebServer.GetCachedOrDecodeBlob(t.TemplateBase64);
                    if (blob != null && blob.Length > 0)
                    {
                        t.Fid = currentFid;
                        int ret = zkfp2.DBAdd(mDBHandle, currentFid, blob);
                        if (currentFid == 1)
                        {
                            firstBlobLen = blob.Length;
                            firstRet = ret;
                        }
                        if (ret == zkfperrdef.ZKFP_ERR_OK)
                        {
                            _templateStore[currentFid] = t;
                            successCount++;
                        }
                        currentFid++;
                    }
                    else
                    {
                        if (currentFid == 1) firstErr = "GetCachedOrDecodeBlob returned null or 0 (base64 len=" + t.TemplateBase64.Length + ")";
                    }
                }
                sw.Stop();
                Log(string.Format("SyncTemplates: Added {0}/{1} templates into native DB cache in {2} ms. (First item: blobLen={3}, ret={4}, err='{5}')", successCount, templates.Count, sw.ElapsedMilliseconds, firstBlobLen, firstRet, firstErr));
                return successCount;
            }
        }

        public int IdentifyBlob(byte[] capBlob, out int score)
        {
            score = 0;
            lock (_dbLock)
            {
                if (mDBHandle == IntPtr.Zero || _templateStore.Count == 0 || capBlob == null || capBlob.Length == 0)
                {
                    return -1;
                }

                int fid = -1;
                int matchedScore = 0;
                int ret = zkfp2.DBIdentify(mDBHandle, capBlob, ref fid, ref matchedScore);
                if (ret == zkfperrdef.ZKFP_ERR_OK && fid >= 1 && _templateStore.ContainsKey(fid) && matchedScore >= 35)
                {
                    score = matchedScore;
                    return _templateStore[fid].ArrayIndex; // Return 0-based index for client array
                }
                return -1;
            }
        }

        public void GetMetaByIndex(int arrayIndex, out string personnelId, out string biometricId)
        {
            personnelId = "";
            biometricId = "";
            lock (_dbLock)
            {
                foreach (var kvp in _templateStore)
                {
                    if (kvp.Value.ArrayIndex == arrayIndex)
                    {
                        personnelId = kvp.Value.PersonnelId;
                        biometricId = kvp.Value.BiometricId;
                        return;
                    }
                }
            }
        }

        public int VerifyTemplates(string t1Base64, string t2Base64)
        {
            byte[] blob1 = WebServer.GetCachedOrDecodeBlob(t1Base64);
            byte[] blob2 = WebServer.GetCachedOrDecodeBlob(t2Base64);
            return VerifyBlobs(blob1, blob2);
        }

        public int VerifyBlobs(byte[] blob1, byte[] blob2)
        {
            if (blob1 == null || blob2 == null || mDBHandle == IntPtr.Zero) return 0;
            return zkfp2.DBMatch(mDBHandle, blob1, blob2);
        }

        public string MergeTemplates(string t1Base64, string t2Base64, string t3Base64)
        {
            try {
                byte[] blob1 = WebServer.GetCachedOrDecodeBlob(t1Base64);
                byte[] blob2 = WebServer.GetCachedOrDecodeBlob(t2Base64);
                byte[] blob3 = WebServer.GetCachedOrDecodeBlob(t3Base64);
                
                byte[] regTmp = new byte[2048];
                int cbRegTmp = 2048;
                
                int ret = zkfp2.DBMerge(mDBHandle, blob1, blob2, blob3, regTmp, ref cbRegTmp);
                if (ret == zkfp.ZKFP_ERR_OK) {
                    return zkfp2.BlobToBase64(regTmp, cbRegTmp);
                }
            } catch (Exception ex) {
                Log("MergeTemplates exception: " + ex.Message);
            }
            return null;
        }

        private void QueryDeviceDetails()
        {
            try
            {
                byte[] paramValue = new byte[128];
                int size = 128;
                if (zkfp2.GetParameters(mDevHandle, 1102, paramValue, ref size) == zkfperrdef.ZKFP_ERR_OK && size > 0)
                {
                    DeviceModel = Encoding.ASCII.GetString(paramValue, 0, size).TrimEnd('\0', ' ', '\r', '\n');
                }

                size = 128;
                if (zkfp2.GetParameters(mDevHandle, 1103, paramValue, ref size) == zkfperrdef.ZKFP_ERR_OK && size > 0)
                {
                    DeviceSerial = Encoding.ASCII.GetString(paramValue, 0, size).TrimEnd('\0', ' ', '\r', '\n');
                }

                size = 128;
                if (zkfp2.GetParameters(mDevHandle, 1101, paramValue, ref size) == zkfperrdef.ZKFP_ERR_OK && size > 0)
                {
                    DeviceManufacturer = Encoding.ASCII.GetString(paramValue, 0, size).TrimEnd('\0', ' ', '\r', '\n');
                }

                Log(string.Format("Detected device: Model={0}, Serial={1}, Manufacturer={2}", DeviceModel, DeviceSerial, DeviceManufacturer));
            }
            catch (Exception ex)
            {
                Log("QueryDeviceDetails Exception: " + ex.Message);
            }
        }

        private void OpenDeviceInternal(int index = 0)
        {
            try
            {
                mDevHandle = zkfp2.OpenDevice(index);
                if (IntPtr.Zero == mDevHandle)
                {
                    Log("OpenDevice failed for index " + index);
                    return;
                }

                mDBHandle = zkfp2.DBInit();
                if (IntPtr.Zero == mDBHandle)
                {
                    Log("Init DB fail");
                    zkfp2.CloseDevice(mDevHandle);
                    mDevHandle = IntPtr.Zero;
                    return;
                }

                // Set verification thresholds for high sensitivity and high speed (35 standard for ZKTeco)
                zkfp2.DBSetParameter(mDBHandle, 1, 35); // 1:1 threshold
                zkfp2.DBSetParameter(mDBHandle, 2, 35); // 1:N threshold

                QueryDeviceDetails();

                RegisterCount = 0;
                cbRegTmp = 0;
                iFid = 1;
                for (int i = 0; i < 3; i++)
                {
                    RegTmps[i] = new byte[2048];
                }
                byte[] paramValue = new byte[4];
                int size = 4;
                zkfp2.GetParameters(mDevHandle, 1, paramValue, ref size);
                zkfp2.ByteArray2Int(paramValue, ref mfpWidth);

                size = 4;
                zkfp2.GetParameters(mDevHandle, 2, paramValue, ref size);
                zkfp2.ByteArray2Int(paramValue, ref mfpHeight);

                FPBuffer = new byte[mfpWidth * mfpHeight];

                bIsTimeToDie = false;
                captureThread = new Thread(new ThreadStart(DoCapture));
                captureThread.IsBackground = true;
                captureThread.Priority = ThreadPriority.AboveNormal;
                captureThread.Start();

                Log(string.Format("Capture thread started. Dimensions: {0}x{1}", mfpWidth, mfpHeight));

                // Safe UI updates if Form has a handle
                Action updateUI = () => {
                    try {
                        bnInit.Enabled = false;
                        bnFree.Enabled = true;
                        bnOpen.Enabled = false;
                        bnClose.Enabled = true;
                        bnEnroll.Enabled = true;
                        bnVerify.Enabled = true;
                        bnIdentify.Enabled = true;
                        if (cmbIdx.Items.Count == 0) cmbIdx.Items.Add(index.ToString());
                        cmbIdx.SelectedIndex = 0;
                        DrawPlaceholderImage("SCANNER READY", DeviceModel + " (" + DeviceSerial + ")", true);
                    } catch {}
                };

                if (this.InvokeRequired) this.BeginInvoke(updateUI);
                else updateUI();
            }
            catch (Exception ex)
            {
                Log("OpenDeviceInternal Exception: " + ex.ToString());
            }
        }

        private void bnInit_Click(object sender, EventArgs e)
        {
            cmbIdx.Items.Clear();
            int ret = zkfperrdef.ZKFP_ERR_OK;
            if ((ret = zkfp2.Init()) == zkfperrdef.ZKFP_ERR_OK || ret == zkfperrdef.ZKFP_ERR_ALREADY_INIT)
            {
                int nCount = zkfp2.GetDeviceCount();
                if (nCount > 0)
                {
                    for (int i = 0; i < nCount; i++)
                    {
                        cmbIdx.Items.Add(i.ToString());
                    }
                    cmbIdx.SelectedIndex = 0;
                    bnInit.Enabled = false;
                    bnFree.Enabled = true;
                    bnOpen.Enabled = true;
                }
                else
                {
                    zkfp2.Terminate();
                    if (sender != null) MessageBox.Show("No device connected!");
                }
            }
            else
            {
                if (sender != null) MessageBox.Show("Initialize fail, ret=" + ret + " !");
            }
        }

        private void bnFree_Click(object sender, EventArgs e)
        {
            CloseDevice();
            zkfp2.Terminate();
            cbRegTmp = 0;
            bnInit.Enabled = true;
            bnFree.Enabled = false;
            bnOpen.Enabled = false;
            bnClose.Enabled = false;
            bnEnroll.Enabled = false;
            bnVerify.Enabled = false;
            bnIdentify.Enabled = false;
        }

        private void bnOpen_Click(object sender, EventArgs e)
        {
            int idx = cmbIdx.SelectedIndex >= 0 ? cmbIdx.SelectedIndex : 0;
            OpenDeviceInternal(idx);
        }

        private void DoCapture()
        {
            Stopwatch matchSw = new Stopwatch();
            byte[] dummyCap = new byte[2048];
            while (!bIsTimeToDie)
            {
                if (mDevHandle == IntPtr.Zero)
                {
                    Thread.Sleep(200);
                    continue;
                }

                cbCapTmp = 2048;
                int ret = zkfp2.AcquireFingerprint(mDevHandle, FPBuffer, CapTmp, ref cbCapTmp);
                if (ret == zkfp.ZKFP_ERR_OK && cbCapTmp > 0)
                {
                    string strBase64 = zkfp2.BlobToBase64(CapTmp, cbCapTmp);
                    int matchedScore = 0;
                    int matchedIdx = IdentifyBlob(CapTmp, out matchedScore);

                    lock (_templateLock)
                    {
                        _lastCapturedTemplateBase64 = strBase64;
                        _lastMatchedArrayIndex = matchedIdx;
                        _lastMatchedScore = matchedScore;
                    }

                    // Render fingerprint bitmap to PictureBox asynchronously on ThreadPool so capture thread never blocks
                    if (this.IsHandleCreated && !this.IsDisposed)
                    {
                        byte[] bufferCopy = new byte[mfpWidth * mfpHeight];
                        Array.Copy(FPBuffer, bufferCopy, bufferCopy.Length);
                        ThreadPool.QueueUserWorkItem((state) =>
                        {
                            try
                            {
                                byte[] buf = (byte[])state;
                                MemoryStream ms = new MemoryStream();
                                BitmapFormat.GetBitmap(buf, mfpWidth, mfpHeight, ref ms);
                                Bitmap bmp = new Bitmap(ms);

                                Action renderImg = () =>
                                {
                                    try
                                    {
                                        if (this.picFPImg.Image != null)
                                        {
                                            var old = this.picFPImg.Image;
                                            this.picFPImg.Image = null;
                                            old.Dispose();
                                        }
                                        this.picFPImg.Image = bmp;
                                        this.picFPImg.Refresh();
                                        textRes.Text = "Scanned: " + DateTime.Now.ToString("HH:mm:ss.fff") + " (" + DeviceModel + (matchedIdx >= 0 ? " Match Score: " + matchedScore : "") + ")";
                                    }
                                    catch {}
                                };

                                if (this.InvokeRequired) this.BeginInvoke(renderImg);
                                else renderImg();
                            }
                            catch {}
                        }, bufferCopy);
                    }

                    // Ultra-fast debounce: wait until finger lifts off the glass, max 100ms
                    Stopwatch debounceSw = Stopwatch.StartNew();
                    while (!bIsTimeToDie && debounceSw.ElapsedMilliseconds < 100)
                    {
                        int dummyLen = 2048;
                        int pollRet = zkfp2.AcquireFingerprint(mDevHandle, FPBuffer, dummyCap, ref dummyLen);
                        if (pollRet != zkfp.ZKFP_ERR_OK) break; // Finger lifted off glass!
                        Thread.Sleep(5);
                    }
                }
                else if (ret == zkfperrdef.ZKFP_ERR_INVALID_HANDLE || ret == zkfperrdef.ZKFP_ERR_NOT_OPENED)
                {
                    Log("Capture error (device disconnected): " + ret);
                    CloseDevice();
                    break;
                }

                // Ultra-low 1ms idle sleep ensures instantaneous sub-millisecond response when finger touches sensor
                Thread.Sleep(1);
            }
        }

        private void CloseDevice()
        {
            if (IntPtr.Zero != mDevHandle)
            {
                bIsTimeToDie = true;
                if (captureThread != null && captureThread.IsAlive)
                {
                    captureThread.Join(500);
                }
                if (mDBHandle != IntPtr.Zero)
                {
                    zkfp2.DBFree(mDBHandle);
                    mDBHandle = IntPtr.Zero;
                }
                zkfp2.CloseDevice(mDevHandle);
                mDevHandle = IntPtr.Zero;
                Log("Device closed.");
                DrawPlaceholderImage("SCANNER CLOSED", "Waiting for reconnect...", false);
            }
        }

        private void bnClose_Click(object sender, EventArgs e)
        {
            CloseDevice();
            RegisterCount = 0;
            bnInit.Enabled = false;
            bnFree.Enabled = true;
            bnOpen.Enabled = true;
            bnClose.Enabled = false;
            bnEnroll.Enabled = false;
            bnVerify.Enabled = false;
            bnIdentify.Enabled = false;
        }

        private void bnEnroll_Click(object sender, EventArgs e)
        {
            if (!IsRegister)
            {
                IsRegister = true;
                RegisterCount = 0;
                cbRegTmp = 0;
                textRes.Text = "Please press your finger 3 times!";
            }
        }

        private void bnIdentify_Click(object sender, EventArgs e)
        {
            if (!bIdentify)
            {
                bIdentify = true;
                textRes.Text = "Please press your finger!";
            }
        }

        private void bnVerify_Click(object sender, EventArgs e)
        {
            if (bIdentify)
            {
                bIdentify = false;
                textRes.Text = "Please press your finger!";
            }
        }
    }
}
