@echo off
echo Compiling ZKBiometricAPI.exe (32-bit x86 Native Matching Bridge with /optimize+)...
C:\Windows\Microsoft.NET\Framework\v4.0.30319\csc.exe /nologo /optimize+ /target:winexe /platform:x86 /r:..\libzkfpcsharp.dll /r:System.Web.dll /resource:Demo.Form1.resources,Demo.Form1.resources /out:..\ZKBiometricAPI.exe *.cs
if %ERRORLEVEL% EQU 0 (
    echo Build SUCCESS! Output: biometric_driver\ZKBiometricAPI.exe
) else (
    echo Build FAILED with code %ERRORLEVEL%
)
pause
