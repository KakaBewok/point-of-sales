const fs = require('fs');
const path = require('path');

const viewsDir = path.join(__dirname, 'resources', 'views', 'livewire');
const classDir = path.join(__dirname, 'app', 'Livewire');

const components = [
    { view: 'dashboard.blade.php', class: 'Dashboard.php', title: 'Dashboard' },
    { view: 'pos-screen.blade.php', class: 'PosScreen.php', title: 'Kasir (POS)' },
    { view: 'product-manager.blade.php', class: 'ProductManager.php', title: 'Produk' },
    { view: 'category-manager.blade.php', class: 'CategoryManager.php', title: 'Kategori' },
    { view: 'stock-manager.blade.php', class: 'StockManager.php', title: 'Stok' },
    { view: 'stock-log-viewer.blade.php', class: 'StockLogViewer.php', title: 'Riwayat Stok' },
    { view: 'voucher-manager.blade.php', class: 'VoucherManager.php', title: 'Voucher' },
    { view: 'report-manager.blade.php', class: 'ReportManager.php', title: 'Laporan' },
    { view: 'user-manager.blade.php', class: 'UserManager.php', title: 'Pengguna' },
    { view: 'settings-manager.blade.php', class: 'SettingsManager.php', title: 'Pengaturan' }
];

components.forEach(comp => {
    // Fix View
    const viewPath = path.join(viewsDir, comp.view);
    if (fs.existsSync(viewPath)) {
        let content = fs.readFileSync(viewPath, 'utf8');
        // Remove <x-layouts::app ...> and </x-layouts::app>
        content = content.replace(/<x-layouts::app[^>]*>/g, '');
        content = content.replace(/<\/x-layouts::app>/g, '');
        fs.writeFileSync(viewPath, content.trim() + '\n');
        console.log(`Cleaned view: ${comp.view}`);
    }

    // Fix Class
    const classPath = path.join(classDir, comp.class);
    if (fs.existsSync(classPath)) {
        let content = fs.readFileSync(classPath, 'utf8');
        
        let needsSave = false;
        
        // Ensure imports exist
        if (!content.includes('use Livewire\\Attributes\\Layout;')) {
            content = content.replace('use Livewire\\Component;', "use Livewire\\Attributes\\Layout;\nuse Livewire\\Attributes\\Title;\nuse Livewire\\Component;");
            needsSave = true;
        }

        // Ensure attributes exist
        if (!content.includes('#[Layout(')) {
            content = content.replace(/class (\w+) extends Component/, `#[Layout('layouts.app')]\n#[Title('${comp.title}')]\nclass $1 extends Component`);
            needsSave = true;
        }

        if (needsSave) {
            fs.writeFileSync(classPath, content);
            console.log(`Updated class: ${comp.class}`);
        }
    }
});
