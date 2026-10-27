<#
.SYNOPSIS
    Automated Windows remediation script for CVE-2026-109618 & CVE-2026-109619 (PHP2Shell, CVSS 9.2).
.DESCRIPTION
    Scans for PHP installations on Windows (XAMPP, WampServer, Laragon, Herd, IIS, standalone PHP),
    safely disables session.upload_progress.enabled (setting it to Off),
    and restarts active web services (Apache / IIS).
#>

[CmdletBinding()]
param ()

Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host " PHP2Shell (CVE-2026-109618 & CVE-2026-109619) Remediation" -ForegroundColor Cyan
Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host ""

# Check Admin privileges
$isAdmin = ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
if (-not $isAdmin) {
    Write-Warning "Running without Administrator privileges. Service restart and some system folders may require elevated permissions."
    Write-Warning "If any step fails, please right-click PowerShell or the .bat file and choose 'Run as Administrator'."
    Write-Host ""
}

# 1. Collect candidate php.ini paths
$candidatePaths = [System.Collections.Generic.List[string]]::new()

# Check currently loaded php.ini from CLI php if available
try {
    $cliIni = (php --ini 2>$null | Select-String "Loaded Configuration File:\s*(.*)" | ForEach-Object { $_.Matches.Groups[1].Value.Trim() })
    if ($cliIni -and (Test-Path $cliIni)) {
        $candidatePaths.Add((Resolve-Path $cliIni).Path)
    }
} catch {}

# Common Windows PHP locations across drives C:, D:, E:
$searchPatterns = @(
    "C:\xampp\php\php.ini",
    "D:\xampp\php\php.ini",
    "E:\xampp\php\php.ini",
    "C:\wamp64\bin\php\*\php.ini",
    "C:\wamp\bin\php\*\php.ini",
    "C:\laragon\bin\php\*\php.ini",
    "C:\tools\php*\php.ini",
    "C:\php*\php.ini",
    "C:\Program Files\PHP*\php.ini",
    "C:\Program Files (x86)\PHP*\php.ini",
    "$env:USERPROFILE\.config\herd\bin\*\php.ini"
)

foreach ($glob in $searchPatterns) {
    Get-ChildItem -Path $glob -ErrorAction SilentlyContinue | ForEach-Object {
        if (-not $candidatePaths.Contains($_.FullName)) {
            $candidatePaths.Add($_.FullName)
        }
    }
}

if ($candidatePaths.Count -eq 0) {
    Write-Host "[!] No php.ini found in standard locations. Searching C:\ (top 3 levels)..." -ForegroundColor Yellow
    Get-ChildItem -Path "C:\" -Filter "php.ini" -Depth 3 -ErrorAction SilentlyContinue | ForEach-Object {
        if (-not $candidatePaths.Contains($_.FullName)) {
            $candidatePaths.Add($_.FullName)
        }
    }
}

Write-Host "[*] Found $($candidatePaths.Count) php.ini configuration file(s):" -ForegroundColor Green
foreach ($p in $candidatePaths) {
    Write-Host "    - $p" -ForegroundColor Gray
}
Write-Host ""

# 2. Patch each php.ini
$patchedCount = 0
$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"

foreach ($iniPath in $candidatePaths) {
    Write-Host "[*] Processing: $iniPath" -ForegroundColor Yellow
    try {
        # Backup
        $backupPath = "$iniPath.bak_$timestamp"
        Copy-Item -Path $iniPath -Destination $backupPath -Force
        Write-Host "    Backup created: $backupPath" -ForegroundColor DarkGray

        $content = Get-Content -Path $iniPath -Raw -ErrorAction Stop

        if ($content -match '(?m)^\s*session\.upload_progress\.enabled\s*=\s*(Off|0|off)') {
            Write-Host "    [OK] Already disabled in this file." -ForegroundColor Green
            $patchedCount++
            continue
        }

        if ($content -match '(?m)^\s*;?\s*session\.upload_progress\.enabled\s*=.*$') {
            $newContent = [regex]::Replace($content, '(?m)^\s*;?\s*session\.upload_progress\.enabled\s*=.*$', 'session.upload_progress.enabled = Off')
        } else {
            $newContent = $content + "`r`n`r`n; Fix for CVE-2026-109618 / CVE-2026-109619`r`nsession.upload_progress.enabled = Off`r`n"
        }

        Set-Content -Path $iniPath -Value $newContent -NoNewline -ErrorAction Stop
        Write-Host "    [SUCCESS] Patched: session.upload_progress.enabled = Off" -ForegroundColor Green
        $patchedCount++
    } catch {
        Write-Error "    [FAILED] Error processing $iniPath : $_"
    }
}

Write-Host ""
Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host " Restarting Web Services" -ForegroundColor Cyan
Write-Host "==========================================================" -ForegroundColor Cyan

# Restart Apache services (e.g., Apache2.4, Apache, httpd)
$apacheServices = Get-Service | Where-Object { $_.Name -match "apache|httpd" -and $_.Status -eq 'Running' }
foreach ($svc in $apacheServices) {
    Write-Host "[*] Restarting service: $($svc.Name)..." -ForegroundColor Yellow
    try {
        Restart-Service -Name $svc.Name -Force -ErrorAction Stop
        Write-Host "    [SUCCESS] Service $($svc.Name) restarted." -ForegroundColor Green
    } catch {
        # Fallback to net stop/net start
        cmd.exe /c "net stop $($svc.Name) && net start $($svc.Name)" 2>$null
        Write-Host "    [INFO] Attempted restart via net command." -ForegroundColor Gray
    }
}

# Restart IIS if running
$iis = Get-Service -Name "W3SVC" -ErrorAction SilentlyContinue
if ($iis -and $iis.Status -eq 'Running') {
    Write-Host "[*] Restarting IIS..." -ForegroundColor Yellow
    try {
        & iisreset.exe /restart | Out-Null
        Write-Host "    [SUCCESS] IIS restarted successfully." -ForegroundColor Green
    } catch {
        Write-Warning "    [WARN] iisreset failed: $_"
    }
}

Write-Host ""
Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host " Current PHP Environment Status" -ForegroundColor Cyan
Write-Host "==========================================================" -ForegroundColor Cyan
try {
    & php -v
    Write-Host ""
    $checkVal = & php -r "echo 'session.upload_progress.enabled: ' . (ini_get('session.upload_progress.enabled') ? 'ENABLED (Vulnerable)' : 'DISABLED (Secure)') . PHP_EOL;"
    Write-Host $checkVal -ForegroundColor $(if ($checkVal -match "Secure") { [ConsoleColor]::Green } else { [ConsoleColor]::Red })
} catch {
    Write-Host "PHP CLI not found in PATH." -ForegroundColor Gray
}

Write-Host ""
Write-Host "[DONE] Completed remediation for $patchedCount configuration(s)." -ForegroundColor Green
