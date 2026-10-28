using System;
using System.Collections.Generic;
using System.Net;
using System.Threading;
using System.Text;
using System.IO;
using System.Web;
using libzkfpcsharp;
using System.Text.RegularExpressions;

namespace Demo
{
    public class WebServer
    {
        private HttpListener _listener;
        private Thread _serverThread;
        private bool _isRunning = false;
        private Form1 _mainForm;

        private static Dictionary<string, byte[]> _blobCache = new Dictionary<string, byte[]>();
        private static readonly object _cacheLock = new object();

        public WebServer(Form1 form)
        {
            _mainForm = form;
            _listener = new HttpListener();
            _listener.Prefixes.Add("http://127.0.0.1:15270/ZK_Finger/");
        }

        public void Start()
        {
            try
            {
                _listener.Start();
                _isRunning = true;
                _serverThread = new Thread(Listen);
                _serverThread.IsBackground = true;
                _serverThread.Priority = ThreadPriority.AboveNormal;
                _serverThread.Start();
                Form1.Log("WebServer successfully started on http://127.0.0.1:15270/ZK_Finger/");
            }
            catch (Exception ex)
            {
                Form1.Log("WebServer Start Error: " + ex.Message);
            }
        }

        public void Stop()
        {
            _isRunning = false;
            try
            {
                if (_listener != null && _listener.IsListening)
                {
                    _listener.Stop();
                }
            }
            catch {}
        }

        private void Listen()
        {
            while (_isRunning)
            {
                try
                {
                    HttpListenerContext context = _listener.GetContext();
                    ThreadPool.QueueUserWorkItem(ExecuteRequest, context);
                }
                catch (Exception)
                {
                    // Ignore exceptions during shutdown
                }
            }
        }

        private void ExecuteRequest(object state)
        {
            HttpListenerContext context = (HttpListenerContext)state;
            try
            {
                ProcessRequest(context);
            }
            catch (Exception ex)
            {
                Form1.Log("ProcessRequest unhandled error: " + ex.Message);
                try { SendResponse(context.Response, "{\"ret\": -2, \"msg\": \"Server error\"}"); } catch {}
            }
        }

        public static byte[] GetCachedOrDecodeBlob(string base64)
        {
            if (string.IsNullOrEmpty(base64)) return null;
            base64 = base64.Replace("\\/", "/").Replace(" ", "+").Trim();
            byte[] cached;
            lock (_cacheLock)
            {
                if (_blobCache.TryGetValue(base64, out cached)) return cached;
            }
            try
            {
                byte[] blob = zkfp2.Base64ToBlob(base64);
                if (blob != null && blob.Length > 0)
                {
                    lock (_cacheLock)
                    {
                        if (_blobCache.Count > 10000) _blobCache.Clear();
                        _blobCache[base64] = blob;
                    }
                    return blob;
                }
            }
            catch (Exception ex)
            {
                Form1.Log("GetCachedOrDecodeBlob decode error: " + ex.Message);
            }
            return null;
        }

        private void ProcessRequest(HttpListenerContext context)
        {
            HttpListenerRequest request = context.Request;
            HttpListenerResponse response = context.Response;

            // Full CORS Support
            response.AppendHeader("Access-Control-Allow-Origin", "*");
            response.AppendHeader("Access-Control-Allow-Methods", "GET, POST, OPTIONS");
            response.AppendHeader("Access-Control-Allow-Headers", "Content-Type, X-Requested-With");

            if (request.HttpMethod == "OPTIONS")
            {
                SendResponse(response, "");
                return;
            }

            string responseString = "{\"ret\": -1}";
            string path = request.Url.AbsolutePath.ToLower();

            try
            {
                if (path.Contains("/init"))
                {
                    if (!_mainForm.IsDeviceInitialized)
                    {
                        _mainForm.AutoInit();
                    }

                    if (_mainForm.IsDeviceInitialized)
                    {
                        responseString = string.Format(
                            "{{\"ret\": 0, \"device\": \"{0}\", \"serial\": \"{1}\", \"manufacturer\": \"{2}\", \"fast_mode\": true, \"templates_cached\": {3}}}",
                            _mainForm.DeviceModel, _mainForm.DeviceSerial, _mainForm.DeviceManufacturer, _mainForm.CachedTemplatesCount
                        );
                    }
                    else
                    {
                        responseString = "{\"ret\": -1, \"msg\": \"Device not initialized or not detected.\"}";
                    }
                }
                else if (path.Contains("/status"))
                {
                    responseString = string.Format(
                        "{{\"ret\": 0, \"device\": \"{0}\", \"serial\": \"{1}\", \"manufacturer\": \"{2}\", \"templates_cached\": {3}, \"initialized\": {4}}}",
                        _mainForm.DeviceModel, _mainForm.DeviceSerial, _mainForm.DeviceManufacturer, _mainForm.CachedTemplatesCount, _mainForm.IsDeviceInitialized ? "true" : "false"
                    );
                }
                else if (path.Contains("/begincapture") || path.Contains("/beginenroll"))
                {
                    _mainForm.ClearLastTemplate();
                    responseString = "{\"ret\": 0}";
                }
                else if (path.Contains("/gettemplate") || path.Contains("/getenrolltemplate"))
                {
                    int matchIdx = -1;
                    int score = 0;
                    string template = _mainForm.GetLastCapturedTemplate(out matchIdx, out score);
                    if (!string.IsNullOrEmpty(template))
                    {
                        responseString = string.Format("{{\"ret\": 0, \"template\": \"{0}\", \"match_index\": {1}, \"score\": {2}}}", template, matchIdx, score);
                    }
                    else
                    {
                        responseString = "{\"ret\": 1}";
                    }
                }
                else if (path.Contains("/synctemplates"))
                {
                    using (var reader = new StreamReader(request.InputStream, request.ContentEncoding))
                    {
                        string body = reader.ReadToEnd();
                        List<TemplateMeta> list = ParseTemplatesFromBody(body);
                        int count = _mainForm.SyncTemplates(list);
                        responseString = string.Format("{{\"ret\": 0, \"count\": {0}}}", count);
                    }
                }
                else if (path.Contains("/verifymulti"))
                {
                    if (request.HttpMethod == "POST")
                    {
                        using (var reader = new StreamReader(request.InputStream, request.ContentEncoding))
                        {
                            string body = reader.ReadToEnd();
                            var args = HttpUtility.ParseQueryString(body);
                            string cap = args["capTemplate"];
                            string regs = args["regTemplates"];

                            if (!string.IsNullOrEmpty(cap) && !string.IsNullOrEmpty(regs))
                            {
                                cap = cap.Replace(" ", "+");
                                regs = regs.Replace(" ", "+");

                                byte[] capBlob = GetCachedOrDecodeBlob(cap);
                                if (capBlob != null && capBlob.Length > 0)
                                {
                                    string[] regArray = regs.Split('|');
                                    int bestMatchIndex = -1;
                                    int bestScore = 0;

                                    for (int i = 0; i < regArray.Length; i++)
                                    {
                                        string regStr = regArray[i];
                                        if (string.IsNullOrEmpty(regStr)) continue;

                                        byte[] regBlob = GetCachedOrDecodeBlob(regStr);
                                        if (regBlob == null) continue;

                                        int matchScore = _mainForm.VerifyBlobs(capBlob, regBlob);
                                        // Strict matching: confident match at >= 60, strict threshold >= 55
                                        if (matchScore >= 60)
                                        {
                                            bestScore = matchScore;
                                            bestMatchIndex = i;
                                            break; // Confident match! Stop scanning remainder of chunk.
                                        }
                                        if (matchScore >= 55 && matchScore > bestScore)
                                        {
                                            bestScore = matchScore;
                                            bestMatchIndex = i;
                                        }
                                    }

                                    if (bestMatchIndex != -1 && bestScore >= 55)
                                    {
                                        responseString = string.Format("{{\"ret\": 0, \"score\": {0}, \"match_index\": {1}}}", bestScore, bestMatchIndex);
                                    }
                                    else
                                    {
                                        responseString = "{\"ret\": 1, \"score\": 0}";
                                    }
                                }
                            }
                        }
                    }
                }
                else if (path.Contains("/verify"))
                {
                    if (request.HttpMethod == "POST")
                    {
                        using (var reader = new StreamReader(request.InputStream, request.ContentEncoding))
                        {
                            string body = reader.ReadToEnd();
                            var args = HttpUtility.ParseQueryString(body);
                            string t1 = args["template1"];
                            string t2 = args["template2"];

                            if (!string.IsNullOrEmpty(t1) && !string.IsNullOrEmpty(t2))
                            {
                                t1 = t1.Replace(" ", "+");
                                t2 = t2.Replace(" ", "+");

                                int matchScore = _mainForm.VerifyTemplates(t1, t2);
                                if (matchScore >= 35)
                                {
                                    responseString = "{\"ret\": 0, \"score\": " + matchScore + "}";
                                }
                                else
                                {
                                    responseString = "{\"ret\": 1, \"score\": " + matchScore + "}";
                                }
                            }
                        }
                    }
                }
                else if (path.Contains("/merge"))
                {
                    using (var reader = new StreamReader(request.InputStream, request.ContentEncoding))
                    {
                        string body = reader.ReadToEnd();
                        var args = HttpUtility.ParseQueryString(body);
                        string t1 = args["t1"] != null ? args["t1"].Replace(" ", "+") : null;
                        string t2 = args["t2"] != null ? args["t2"].Replace(" ", "+") : null;
                        string t3 = args["t3"] != null ? args["t3"].Replace(" ", "+") : null;

                        if (!string.IsNullOrEmpty(t1) && !string.IsNullOrEmpty(t2) && !string.IsNullOrEmpty(t3))
                        {
                            string regTmp = _mainForm.MergeTemplates(t1, t2, t3);
                            if (!string.IsNullOrEmpty(regTmp))
                            {
                                responseString = "{\"ret\": 0, \"template\": \"" + regTmp + "\"}";
                            }
                            else
                            {
                                responseString = "{\"ret\": 1, \"error\": \"Merge failed\"}";
                            }
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                responseString = "{\"ret\": -2, \"msg\": \"" + ex.Message.Replace("\"", "\\\"") + "\"}";
            }

            SendResponse(response, responseString);
        }

        private List<TemplateMeta> ParseTemplatesFromBody(string body)
        {
            List<TemplateMeta> list = new List<TemplateMeta>();
            if (string.IsNullOrEmpty(body)) return list;

            // Check if form urlencoded
            if (body.StartsWith("templates="))
            {
                body = HttpUtility.UrlDecode(body.Substring("templates=".Length));
            }

            // Quick JSON regex parsing
            var matches = Regex.Matches(body, @"\{[^{}]*\}");
            int autoIndex = 0;
            foreach (Match m in matches)
            {
                string objStr = m.Value;
                string tmpl = ExtractJsonField(objStr, "template");
                if (string.IsNullOrEmpty(tmpl)) tmpl = ExtractJsonField(objStr, "fingerprint_template");
                if (string.IsNullOrEmpty(tmpl)) continue;

                string idStr = ExtractJsonField(objStr, "id");
                if (string.IsNullOrEmpty(idStr)) idStr = ExtractJsonField(objStr, "fid");
                int arrIdx = autoIndex;
                if (!string.IsNullOrEmpty(idStr) && int.TryParse(idStr, out arrIdx))
                {
                    // use parsed index
                }
                else
                {
                    arrIdx = autoIndex;
                }

                string pid = ExtractJsonField(objStr, "personnel_id");
                string bid = ExtractJsonField(objStr, "biometric_id");
                string rftag = ExtractJsonField(objStr, "RFTag_id");

                list.Add(new TemplateMeta {
                    ArrayIndex = arrIdx,
                    PersonnelId = pid,
                    BiometricId = bid,
                    RFTagId = rftag,
                    TemplateBase64 = tmpl.Replace("\\/", "/").Replace(" ", "+").Trim()
                });
                autoIndex++;
            }

            // Fallback: If not JSON array, check pipe-delimited templates
            if (list.Count == 0 && body.Contains("|"))
            {
                string[] parts = body.Split('|');
                for (int i = 0; i < parts.Length; i++)
                {
                    string p = parts[i].Trim();
                    if (p.Length > 100)
                    {
                        list.Add(new TemplateMeta {
                            ArrayIndex = i,
                            PersonnelId = i.ToString(),
                            BiometricId = "",
                            TemplateBase64 = p.Replace(" ", "+")
                        });
                    }
                }
            }

            return list;
        }

        private string ExtractJsonField(string json, string field)
        {
            var match = Regex.Match(json, "\"" + field + "\"\\s*:\\s*\"?([^,\"}]+)\"?");
            if (match.Success)
            {
                string val = match.Groups[1].Value.Trim('"', ' ');
                return val.Replace("\\/", "/").Replace("\\\"", "\"").Replace("\\\\", "\\");
            }
            return "";
        }

        private void SendResponse(HttpListenerResponse response, string responseString)
        {
            try
            {
                byte[] buffer = Encoding.UTF8.GetBytes(responseString);
                response.ContentLength64 = buffer.Length;
                response.ContentType = "application/json";
                Stream output = response.OutputStream;
                output.Write(buffer, 0, buffer.Length);
                output.Close();
            }
            catch {}
        }
    }
}
