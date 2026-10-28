$proc = Start-Process -FilePath "c:\xampp\htdocs\mob_bio\biometric_driver\ZKBiometricAPI.exe" -WorkingDirectory "c:\xampp\htdocs\mob_bio\biometric_driver" -PassThru
Write-Host "Started ZKBiometricAPI with PID: $($proc.Id)"
Wait-Process -Id $proc.Id
