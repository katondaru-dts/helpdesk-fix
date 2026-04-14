const fs = require('fs');

const outFile = 'docker_update_layout.sh';
const header = "cat << 'EOF' > /var/www/html/";

const files = [
    'app/Views/layouts/main.php',
    'app/Helpers/auth_helper.php',
    'app/Controllers/BaseController.php',
    'app/Controllers/Dashboard.php',
    'app/Controllers/Auth.php',
    'app/Views/admin/reports/index.php',
    'app/Views/admin/reports/print_report.php',
    'app/Controllers/Admin/Reports.php',
    'app/Config/Routes.php'
];

let finalContent = '';
for (const file of files) {
    if (fs.existsSync(file)) {
        finalContent += `${header}${file}\n`;
        finalContent += fs.readFileSync(file, 'utf8');
        finalContent += "\nEOF\n\n";
    }
}

fs.writeFileSync(outFile, finalContent);
console.log('Successfully rebuilt docker_update_layout.sh');
