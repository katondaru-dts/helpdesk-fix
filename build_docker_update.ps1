# PowerShell script to rebuild docker_update_layout.sh
$out = 'docker_update_layout.sh'
$header = 'cat << ''EOF'' > /var/www/html/'

Set-Content -Path $out -Value ''

$files = @(
    'app/Views/layouts/main.php',
    'app/Helpers/auth_helper.php',
    'app/Controllers/BaseController.php',
    'app/Controllers/Dashboard.php',
    'app/Controllers/Auth.php',
    'app/Views/admin/reports/index.php',
    'app/Views/admin/reports/print_report.php',
    'app/Controllers/Admin/Reports.php',
    'app/Config/Routes.php'
)

foreach ($f in $files) {
    Add-Content -Path $out -Value ($header + $f)
    if ($f -eq 'app/Views/admin/reports/index.php') {
        $content = Get-Content -Path "C:\Users\Public\index_temp_correct.php" -Raw
    } else {
        $content = Get-Content -Path "g:\My Drive\PROJECK KATON\helpdesk-v2\$f" -Raw
    }
    Add-Content -Path $out -Value $content
    Add-Content -Path $out -Value "EOF`n"
}
