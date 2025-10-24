# Quick Reference - Stamp Album Designs

## 🚀 Quick Setup (Choose One)

### Option 1: PowerShell Script
```powershell
.\setup.ps1
```

### Option 2: Batch File
```cmd
setup.bat
```

### Option 3: Manual
```powershell
composer install
npm install
copy .env.example .env
php artisan key:generate
New-Item database\database.sqlite
php artisan migrate
php artisan storage:link
npm run build
```

---

## 🏃 Running the Application

### Start Server
```powershell
php artisan serve
```
Visit: **http://localhost:8000**

### Watch Assets (Optional)
```powershell
# In a separate terminal
npm run dev
```

---

## 📍 Important URLs

| Page | URL |
|------|-----|
| Home | http://localhost:8000 |
| Order Builder | http://localhost:8000/order |
| Cart | http://localhost:8000/cart |
| Checkout | http://localhost:8000/checkout |
| Admin Dashboard | http://localhost:8000/dashboard |

---

## 🔧 Common Commands

### Clear Caches
```powershell
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Or all at once:
php artisan optimize:clear
```

### Database
```powershell
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Fresh database (deletes all data!)
php artisan migrate:fresh

# Seed database
php artisan db:seed
```

### Assets
```powershell
# Build for production
npm run build

# Development with hot reload
npm run dev

# Watch mode
npm run watch
```

### Testing
```powershell
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=PaperConfigurationTest

# Test with coverage
php artisan test --coverage
```

---

## 🧪 Test Paper Configuration

### API Tests (PowerShell)
```powershell
# Get all paper sizes
Invoke-RestMethod -Uri "http://localhost:8000/api/paper-sizes"

# Get options for size
Invoke-RestMethod -Uri "http://localhost:8000/api/paper-sizes/8.5x11/options"

# Calculate price
Invoke-RestMethod -Uri "http://localhost:8000/api/paper-configurations/calculate" `
  -Method POST `
  -ContentType "application/json" `
  -Body (@{
    size = "8.5x11"
    options = @{
      paper_weight = "67lb"
      color = "cream"
      punches = "3-hole"
      corners = "square"
      protection = "none"
    }
    pages = 50
  } | ConvertTo-Json)
```

### Test Endicia Integration
```powershell
php artisan endicia:test --pages=50 --paper=0.25
```

---

## 📦 Paper Configuration System

### Available Sizes
- **8.5x11** - Standard US Letter ($0.20/page base)
- **minkus** - Minkus Global 9.5x11.25 ($0.30/page base)
- **international** - Scott Int'l 9.25x11.25 ($0.30/page base)
- **specialized** - Premium 8.5x11 ($0.35/page base)

### Customization Options
| Option | Choices |
|--------|---------|
| Weight | 67lb, 80lb, 110lb |
| Color | Cream, White, Cougar Natural |
| Punches | None, 2-hole, 2-hole rect, 3-hole |
| Corners | Square, Rounded |
| Protection | Standard, Hingeless |

### Price Modifiers
- 80lb paper: **+$0.05**
- 110lb paper: **+$0.15**
- Cougar Natural: **+$0.02**
- No holes: **-$0.02**
- Rounded corners: **+$0.03**
- Hingeless mounts: **+$0.50**

---

## 🐛 Troubleshooting

### "No application encryption key"
```powershell
php artisan key:generate
```

### "Database not found"
```powershell
New-Item -Path "database\database.sqlite" -ItemType File -Force
php artisan migrate
```

### "Permission denied"
```powershell
# Run PowerShell as Administrator
# Or fix permissions:
icacls storage /grant Everyone:F /T
icacls bootstrap\cache /grant Everyone:F /T
```

### Routes not working
```powershell
php artisan route:clear
php artisan config:clear
```

### Frontend not updating
```powershell
npm run build
php artisan view:clear
# Then hard refresh browser (Ctrl+Shift+R)
```

### Composer/NPM errors
```powershell
# Composer
composer clear-cache
composer install

# NPM
npm cache clean --force
npm install
```

---

## 📖 Documentation Files

| File | Description |
|------|-------------|
| `LOCAL_SETUP_GUIDE.md` | Complete setup instructions |
| `PAPER_OPTIONS_IMPLEMENTATION_COMPLETE.md` | Paper system documentation |
| `ENDICIA_INTEGRATION_GUIDE.md` | Shipping integration guide |
| `TESTING_GUIDE.md` | Testing procedures |
| `README.md` | Project overview |

---

## 🔑 Environment Variables

### Required
```env
APP_KEY=base64:...           # Auto-generated
APP_URL=http://localhost:8000
DB_CONNECTION=sqlite
```

### Optional (Endicia)
```env
ENDICIA_API_URL=https://elstestserver.endicia.com/LabelService/EwsLabelService.asmx
ENDICIA_ACCOUNT_ID=your_account
ENDICIA_PASS_PHRASE=your_passphrase
ENDICIA_FROM_ZIP=90210
ENDICIA_TEST_MODE=true
```

### Optional (Stripe)
```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
```

---

## 🎯 Next Steps

1. ✅ Complete setup using `setup.ps1` or `setup.bat`
2. ✅ Start server with `php artisan serve`
3. ✅ Visit http://localhost:8000/order
4. ✅ Test paper configuration UI
5. ✅ Add items to cart
6. ✅ Test checkout flow
7. ⚠️ Configure Endicia credentials (optional)
8. ⚠️ Configure Stripe keys (optional)

---

## 💡 Development Tips

### Use Tinker for Quick Tests
```powershell
php artisan tinker

>>> App\Models\PaperSize::all()
>>> App\Models\PaperSize::find('8.5x11')
>>> $config = new App\Models\PaperConfiguration('8.5x11', ['color' => 'cream'])
>>> $config->calculatePricePerPage()
```

### Watch Logs
```powershell
Get-Content storage\logs\laravel.log -Tail 50 -Wait
```

### Database Browser
Download **DB Browser for SQLite**: https://sqlitebrowser.org/

### API Testing
Use **Postman** or **Thunder Client** (VS Code extension)

---

## ✅ Success Checklist

- [ ] PHP 8.2+ installed
- [ ] Composer installed
- [ ] Node.js installed
- [ ] Dependencies installed (`vendor/`, `node_modules/`)
- [ ] `.env` file exists
- [ ] Application key generated
- [ ] Database created and migrated
- [ ] Assets built
- [ ] Server runs without errors
- [ ] Paper sizes API responds: http://localhost:8000/api/paper-sizes
- [ ] Order page loads: http://localhost:8000/order
- [ ] Paper options UI works

---

**Need Help?** Check `LOCAL_SETUP_GUIDE.md` for detailed instructions!
