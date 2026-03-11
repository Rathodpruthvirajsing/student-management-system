#!/usr/bin/env powershell
<#
 .SYNOPSIS
    Student Management System - Health Check & Setup Verification
 .DESCRIPTION
    Verifies that all project files exist and are properly configured
#>

Write-Host "`n================================" -ForegroundColor Cyan
Write-Host "🔍 Student Management System" -ForegroundColor Cyan
Write-Host "   Health Check & Setup" -ForegroundColor Cyan
Write-Host "================================`n" -ForegroundColor Cyan

$projectRoot = "c:\xampp\htdocs\student-management-system"
$checksPass = 0
$checksTotal = 0

# Color functions
function Check-Item {
    param($Name, $Path, $Type = "file")
    
    $checksTotal++
    $exists = $false
    
    if ($Type -eq "folder") {
        $exists = Test-Path $Path -PathType Container
    } else {
        $exists = Test-Path $Path -PathType Leaf
    }
    
    if ($exists) {
        Write-Host "✅ $Name" -ForegroundColor Green
        $checksPass++
        return $true
    } else {
        Write-Host "❌ $Name - MISSING!" -ForegroundColor Red
        return $false
    }
}

# ========= CORE FILES =========
Write-Host "`n📁 Core Files:" -ForegroundColor Yellow
Check-Item "setup.php" "$projectRoot\setup.php"
Check-Item "index.php" "$projectRoot\index.php"
Check-Item "dashboard.php" "$projectRoot\dashboard.php"
Check-Item "config/db.php" "$projectRoot\config\db.php"

# ========= AUTHENTICATION =========
Write-Host "`n🔐 Authentication:" -ForegroundColor Yellow
Check-Item "auth/login.php" "$projectRoot\auth\login.php"
Check-Item "auth/logout.php" "$projectRoot\auth\logout.php"
Check-Item "auth/session.php" "$projectRoot\auth\session.php"

# ========= INCLUDES =========
Write-Host "`n📄 Includes:" -ForegroundColor Yellow
Check-Item "includes/header.php" "$projectRoot\includes\header.php"
Check-Item "includes/footer.php" "$projectRoot\includes\footer.php"
Check-Item "includes/sidebar.php" "$projectRoot\includes\sidebar.php"

# ========= MODULES =========
Write-Host "`n📦 Modules:" -ForegroundColor Yellow
Check-Item "modules/students/view.php" "$projectRoot\modules\students\view.php"
Check-Item "modules/students/add.php" "$projectRoot\modules\students\add.php"
Check-Item "modules/courses/view.php" "$projectRoot\modules\courses\view.php"
Check-Item "modules/attendance/mark.php" "$projectRoot\modules\attendance\mark.php"
Check-Item "modules/exams/add_exam.php" "$projectRoot\modules\exams\add_exam.php"
Check-Item "modules/fees/payment.php" "$projectRoot\modules\fees\payment.php"

# ========= ASSETS =========
Write-Host "`n🎨 Assets:" -ForegroundColor Yellow
Check-Item "assets/css/style.css" "$projectRoot\assets\css\style.css"
Check-Item "assets/js/validation.js" "$projectRoot\assets\js\validation.js"

# ========= DIRECTORIES =========
Write-Host "`n📂 Directories:" -ForegroundColor Yellow
Check-Item "logs/" "$projectRoot\logs" "folder"
Check-Item "uploads/student_photos/" "$projectRoot\uploads\student_photos" "folder"

# ========= REPORTS =========
Write-Host "`n📊 Reports:" -ForegroundColor Yellow
Check-Item "reports/student-report.php" "$projectRoot\reports\student-report.php"
Check-Item "reports/attendance-report.php" "$projectRoot\reports\attendance-report.php"
Check-Item "reports/exam-report.php" "$projectRoot\reports\exam-report.php"
Check-Item "reports/fees-report.php" "$projectRoot\reports\fees-report.php"

# ========= DOCUMENTATION =========
Write-Host "`n📚 Documentation:" -ForegroundColor Yellow
Check-Item "README.md" "$projectRoot\README.md"
Check-Item "PROJECT_AUDIT_REPORT.md" "$projectRoot\PROJECT_AUDIT_REPORT.md"
Check-Item "QUICK_START.md" "$projectRoot\QUICK_START.md"

# ========= SUMMARY =========
Write-Host "`n================================" -ForegroundColor Cyan
Write-Host "📈 Summary" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host "Checks Passed: $checksPass/$checksTotal" -ForegroundColor $(if($checksPass -eq $checksTotal) { "Green" } else { "Yellow" })

if ($checksPass -eq $checksTotal) {
    Write-Host "`n✅ All files are present!" -ForegroundColor Green
    Write-Host "`n🚀 Next Steps:" -ForegroundColor Cyan
    Write-Host "  1. Start XAMPP (Apache + MySQL)" -ForegroundColor Gray
    Write-Host "  2. Open: http://localhost/student-management-system/setup.php" -ForegroundColor Gray
    Write-Host "  3. Login with: admin@example.com / admin123" -ForegroundColor Gray
    Write-Host "  4. Start using the system!" -ForegroundColor Gray
} else {
    Write-Host "`n⚠️  Some files are missing!" -ForegroundColor Red
    Write-Host "`nTo restore, re-extract the project files to:" -ForegroundColor Yellow
    Write-Host "  $projectRoot" -ForegroundColor Yellow
}

Write-Host "`n"
