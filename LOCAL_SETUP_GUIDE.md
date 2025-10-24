# Local Development Environment Setup Guide

**Project**: Stamp Album Designs  
**Framework**: Laravel 11.9  
**PHP Version**: 8.2+  
**Date**: October 17, 2025

---

## Prerequisites

### Required Software

1. **PHP 8.2 or higher**
   - Download: https://windows.php.net/download/
   - Choose "Thread Safe" version for Windows
   - Extensions needed: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo, SQLite

2. **Composer** (PHP Package Manager)
   - Download: https://getcomposer.org/download/
   - Run the Windows installer

3. **Node.js & NPM** (for frontend assets)
   - Download: https://nodejs.org/ (LTS version recommended)
   - Version 18+ recommended

4. **Git** (if not already installed)
   - Download: https://git-scm.com/download/win

---

## Step-by-Step Setup

### 1. Install PHP

```powershell
# After installing PHP, add it to your PATH
# Example: C:\php (wherever you extracted PHP)

# Verify installation
php --version
# Should show: PHP 8.2.x or higher
```

**Configure PHP:**
- Copy `php.ini-development` to `php.ini`
- Enable required extensions by uncommenting these lines:
  ```ini
  extension=openssl
  extension=pdo_sqlite
  extension=sqlite3
  extension=mbstring
  extension=fileinfo
  ```

### 2. Install Composer

```powershell
# After installation, verify
composer --version
# Should show: Composer version 2.x.x
```

### 3. Install Node.js

```powershell
# Verify installation
node --version
npm --version
```

### 4. Clone & Install Project Dependencies

```powershell
# Navigate to project directory
cd E:\Development\stampalbumdesigns

# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### 5. Environment Configuration

```powershell
# Copy environment file
copy .env.example .env

# Generate application key
php artisan key:generate
```

**Edit `.env` file** with your local settings:

```env
APP_NAME="Stamp Album Designs"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (SQLite - no setup needed)
DB_CONNECTION=sqlite

# Session
SESSION_DRIVER=database

# Mail (for testing, use log driver)
MAIL_MAILER=log

# Endicia API (optional for development)
ENDICIA_API_URL=https://elstestserver.endicia.com/LabelService/EwsLabelService.asmx
ENDICIA_ACCOUNT_ID=your_account_id_here
ENDICIA_PASS_PHRASE=your_pass_phrase_here
ENDICIA_FROM_ZIP=90210
ENDICIA_TEST_MODE=true
```

### 6. Database Setup

```powershell
# Create SQLite database file
New-Item -Path "database\database.sqlite" -ItemType File -Force

# Run migrations
php artisan migrate

# (Optional) Seed database with test data
php artisan db:seed
```

### 7. Storage Link

```powershell
# Create symbolic link for public storage
php artisan storage:link
```

### 8. Build Frontend Assets

```powershell
# Development build (with hot reload)
npm run dev

# Or production build
npm run build
```

### 9. Start Development Server

```powershell
# In a new terminal window
php artisan serve

# Application will be available at:
# http://localhost:8000
```

---

## Verification Checklist

Run these commands to verify everything is working:

```powershell
# Check PHP version
php --version

# Check Composer
composer --version

# Check Node & NPM
node --version
npm --version

# Check Laravel installation
php artisan --version

# List routes (should show all API and web routes)
php artisan route:list

# Check configuration
php artisan config:show app

# Run tests (optional)
php artisan test
```

---

## Common Issues & Solutions

### Issue 1: PHP not found
**Solution**: Add PHP to your system PATH
```powershell
# Windows: System Properties > Environment Variables > Path
# Add: C:\php (or wherever PHP is installed)
```

### Issue 2: SQLite extension not enabled
**Solution**: Edit `php.ini` and uncomment:
```ini
extension=pdo_sqlite
extension=sqlite3
```

### Issue 3: Composer not found
**Solution**: Restart PowerShell after installation or add Composer to PATH

### Issue 4: Permission errors on Windows
**Solution**: Run PowerShell as Administrator
```powershell
# Right-click PowerShell > Run as Administrator
```

### Issue 5: "Please provide a valid cache path"
**Solution**: Create cache directories
```powershell
New-Item -Path "storage\framework\cache\data" -ItemType Directory -Force
New-Item -Path "storage\framework\sessions" -ItemType Directory -Force
New-Item -Path "storage\framework\views" -ItemType Directory -Force
```

### Issue 6: npm install fails
**Solution**: Clear npm cache and retry
```powershell
npm cache clean --force
npm install
```

---

## Quick Start Commands

```powershell
# Full setup from scratch (run in order)
composer install
npm install
copy .env.example .env
php artisan key:generate
New-Item -Path "database\database.sqlite" -ItemType File -Force
php artisan migrate
php artisan storage:link
npm run build
php artisan serve
```

---

## Development Workflow

### Start Development Session
```powershell
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Watch frontend assets (optional)
npm run dev
```

### Access Application
- **Main Site**: http://localhost:8000
- **Order Page**: http://localhost:8000/order
- **Cart**: http://localhost:8000/cart
- **Checkout**: http://localhost:8000/checkout

### Clear Caches (when needed)
```powershell
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Run Database Migrations
```powershell
# Run new migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Fresh database (WARNING: deletes all data)
php artisan migrate:fresh
```

### Test Paper Configuration API
```powershell
# Test paper sizes endpoint
Invoke-WebRequest -Uri "http://localhost:8000/api/paper-sizes" -Method GET

# Test paper options
Invoke-WebRequest -Uri "http://localhost:8000/api/paper-sizes/8.5x11/options" -Method GET
```

---

## IDE Setup (Optional but Recommended)

### Visual Studio Code Extensions
- PHP Intelephense
- Laravel Extension Pack
- Laravel Blade Snippets
- Tailwind CSS IntelliSense
- ESLint

### VS Code Settings
Create `.vscode/settings.json`:
```json
{
    "php.validate.executablePath": "C:\\php\\php.exe",
    "intelephense.files.maxSize": 5000000,
    "editor.formatOnSave": true
}
```

---

## Testing the Paper Options System

### 1. Test API Endpoints

```powershell
# Get available paper sizes
curl http://localhost:8000/api/paper-sizes

# Get options for a specific size
curl http://localhost:8000/api/paper-sizes/8.5x11/options

# Calculate price for a configuration
curl -X POST http://localhost:8000/api/paper-configurations/calculate `
  -H "Content-Type: application/json" `
  -d '{
    "size": "8.5x11",
    "options": {
      "paper_weight": "67lb",
      "color": "cream",
      "punches": "3-hole",
      "corners": "square",
      "protection": "none"
    },
    "pages": 50
  }'
```

### 2. Test Frontend
1. Visit http://localhost:8000/order
2. Select a paper size (e.g., "8.5x11")
3. Customize options (weight, color, punches, etc.)
4. See live price update
5. Select a country and years
6. Add to cart
7. Check cart and checkout

---

## Project Structure

```
stampalbumdesigns/
├── app/
│   ├── Http/Controllers/
│   │   ├── PaperSizeController.php        # New: Paper size API
│   │   ├── PaperConfigurationController.php # New: Paper config API
│   │   └── ShippingRateController.php     # Endicia integration
│   ├── Models/
│   │   ├── PaperSize.php                  # New: Paper size model
│   │   └── PaperConfiguration.php         # New: Paper config model
│   └── Services/
│       ├── ShippingCalculator.php         # Weight/dimension calculations
│       └── EndiciaService.php             # Endicia API integration
├── config/
│   └── paper.php                          # New: Paper sizes and options config
├── database/
│   └── database.sqlite                    # SQLite database file
├── public/
│   ├── country_year_page_dict.json       # Country/year data
│   └── page_count_per_file_per_country.json
├── resources/
│   └── views/
│       └── order.blade.php               # Updated: New paper selection UI
└── routes/
    └── web.php                           # Updated: New API routes
```

---

## Environment Variables Reference

### Required Variables
```env
APP_KEY=                    # Generated by artisan key:generate
APP_URL=http://localhost:8000
DB_CONNECTION=sqlite
```

### Optional Variables (for full functionality)
```env
# Endicia Shipping API
ENDICIA_API_URL=
ENDICIA_ACCOUNT_ID=
ENDICIA_PASS_PHRASE=
ENDICIA_FROM_ZIP=
ENDICIA_TEST_MODE=true

# Stripe Payment (for checkout)
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=

# Email (for order confirmations)
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
```

---

## Next Steps After Setup

1. **Test Paper Configuration System**
   - Visit `/order`
   - Try different paper configurations
   - Verify pricing calculations

2. **Test Shipping Calculator**
   - Add items to cart
   - Go to checkout
   - See shipping rates (requires Endicia credentials)

3. **Review Documentation**
   - Read `PAPER_OPTIONS_IMPLEMENTATION_COMPLETE.md`
   - Review `ENDICIA_INTEGRATION_GUIDE.md`
   - Check `TESTING_GUIDE.md`

4. **Configure Endicia API** (if needed)
   - Get test credentials from Endicia
   - Update `.env` with credentials
   - Test with: `php artisan endicia:test`

5. **Set Up Stripe** (for payments)
   - Get test API keys from Stripe Dashboard
   - Update `.env` with test keys
   - Test checkout flow

---

## Troubleshooting

### Laravel Error: "No application encryption key"
```powershell
php artisan key:generate
```

### Database Error: "Database not found"
```powershell
New-Item -Path "database\database.sqlite" -ItemType File -Force
php artisan migrate
```

### Frontend Not Loading Properly
```powershell
npm run build
php artisan config:clear
```

### Routes Not Working
```powershell
php artisan route:clear
php artisan cache:clear
```

### Still Having Issues?
```powershell
# Nuclear option: clear everything
php artisan optimize:clear
composer dump-autoload
npm run build
```

---

## Development Tips

1. **Use Artisan Tinker** for quick testing:
   ```powershell
   php artisan tinker
   >>> App\Models\PaperSize::all()
   >>> App\Models\PaperConfiguration::fromArray(['size' => '8.5x11', 'options' => []])
   ```

2. **Watch Logs** for debugging:
   ```powershell
   Get-Content storage\logs\laravel.log -Tail 50 -Wait
   ```

3. **Use SQLite Browser** to inspect database:
   - Download: https://sqlitebrowser.org/

4. **API Testing with Postman**:
   - Import collection from endpoints
   - Test all paper configuration APIs

---

## Success Criteria

✅ PHP 8.2+ installed and in PATH  
✅ Composer installed and working  
✅ Node.js & NPM installed  
✅ Dependencies installed (`composer install`, `npm install`)  
✅ `.env` file configured  
✅ Database created and migrated  
✅ Application key generated  
✅ Storage linked  
✅ Assets built  
✅ Server running on http://localhost:8000  
✅ Paper configuration UI loads at `/order`  
✅ API endpoints respond correctly  

**You're ready to develop! 🎉**

---

## Getting Help

- **Laravel Documentation**: https://laravel.com/docs/11.x
- **Lunar PHP Documentation**: https://docs.lunarphp.io/
- **Project README**: See `README.md`
- **Implementation Docs**: See `PAPER_OPTIONS_IMPLEMENTATION_COMPLETE.md`
