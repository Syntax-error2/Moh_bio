using System;
using System.Windows.Forms;
using System.Threading;
using System.Runtime.InteropServices;

namespace Demo
{
    static class Program
    {
        [DllImport("user32.dll")]
        private static extern bool SetForegroundWindow(IntPtr hWnd);

        [DllImport("user32.dll")]
        private static extern bool ShowWindow(IntPtr hWnd, int nCmdShow);

        [DllImport("user32.dll", SetLastError = true)]
        private static extern IntPtr FindWindow(string lpClassName, string lpWindowName);

        private const int SW_RESTORE = 9;

        [STAThread]
        static void Main()
        {
            bool isNewInstance = false;
            using (Mutex mutex = new Mutex(true, "Global\\ZKBiometricAPI_SingleInstance_Mutex", out isNewInstance))
            {
                if (!isNewInstance)
                {
                    IntPtr hWnd = FindWindow(null, "ZKTeco Biometric Bridge - Live 20R / SLK 20R");
                    if (hWnd == IntPtr.Zero)
                    {
                        hWnd = FindWindow(null, "Form1");
                    }
                    if (hWnd != IntPtr.Zero)
                    {
                        ShowWindow(hWnd, SW_RESTORE);
                        SetForegroundWindow(hWnd);
                    }
                    return;
                }

                AppDomain.CurrentDomain.UnhandledException += (s, ev) => {
                    Form1.Log("AppDomain UnhandledException: " + (ev.ExceptionObject != null ? ev.ExceptionObject.ToString() : "unknown"));
                };
                Application.ThreadException += (s, ev) => {
                    Form1.Log("Application ThreadException: " + (ev.Exception != null ? ev.Exception.ToString() : "unknown"));
                };

                try
                {
                    using (var currentProc = System.Diagnostics.Process.GetCurrentProcess())
                    {
                        currentProc.PriorityClass = System.Diagnostics.ProcessPriorityClass.AboveNormal;
                    }
                }
                catch {}

                Form1.Log("Program.Main started.");

                try
                {
                    Application.EnableVisualStyles();
                    Application.SetCompatibleTextRenderingDefault(false);
                    Form1 form = new Form1();
                    Application.Run(form);
                }
                catch (Exception ex)
                {
                    Form1.Log("Main catch exception: " + ex.ToString());
                }
                Form1.Log("Program.Main finished.");
            }
        }
    }
}
