# Fix Font Awesome icons in payroll system files
# Replace FA 5+ (fas) with FA 4.7.0 (fa) compatible icons

$files = @(
    "list_payroll_profiles.php",
    "generate_payroll_from_profile.php",
    "list_payroll_history.php"
)

foreach ($file in $files) {
    Write-Host "Processing $file..." -ForegroundColor Yellow
    
    $content = Get-Content $file -Raw -Encoding UTF8
    
    # Replace fas with fa
    $content = $content -replace 'class="fas fa-', 'class="fa fa-'
    
    # Save file
    Set-Content $file -Value $content -Encoding UTF8 -NoNewline
    
    Write-Host "Completed $file" -ForegroundColor Green
}

Write-Host "All files updated successfully!" -ForegroundColor Green
