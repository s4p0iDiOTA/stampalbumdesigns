# 🚀 Quick Production Deployment Guide

**TL;DR:** Essential steps to deploy to production. See `DEPLOYMENT_CHECKLIST.md` for detailed instructions.

---

## ⚡ Quick Steps (30 minutes)

### 1. Server Setup (5 min)
```bash
# Clone repository
cd /var/www
git clone <your-repo> stampalbumdesigns
cd stampalbumdesigns

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Set permissions
chown -R www-data:www-data .
chmod -R 775 storage bootstrap/cache
```

### 2. Environment (5 min)
```bash
# Create .env
cp .env.example .env
php artisan key:generate

# Edit .env - CRITICAL SETTINGS:
# APP_ENV=production
# APP_DEBUG=false
# APP_URL=https://yourdomain.com
# DB_CONNECTION=mysql
# DB_DATABASE=stampalbumdesigns_production
# DB_USERNAME=production_user
# DB_PASSWORD=your_secure_password
```

### 3. Database (5 min)
```bash
# Create database
mysql -u root -p
CREATE DATABASE stampalbumdesigns_production;
CREATE USER 'production_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON stampalbumdesigns_production.* TO 'production_user'@'localhost';
EXIT;

# Run migrations
php artisan migrate --force
```

### 4. Create Admin User (2 min)
```bash
php artisan user:create-admin
# Enter: name, email, strong password
```

### 5. Optimize & Secure (5 min)
```bash
# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verify security
# ✅ APP_DEBUG=false
# ✅ .env permissions: chmod 600 .env
```

### 6. Configure Web Server (5 min)

**Nginx:** Point root to `/var/www/stampalbumdesigns/public`
**Apache:** Point DocumentRoot to `/var/www/stampalbumdesigns/public`

Enable SSL, restart web server.

### 7. Test (3 min)
```bash
# Run tests
php artisan test

# Manual checks:
# ✅ Visit homepage
# ✅ Login as admin
# ✅ Access /lunar
# ✅ Create test order
```

---

## 🔐 Production .env Critical Settings

```env
APP_ENV=production          # ⚠️ MUST be production
APP_DEBUG=false            # ⚠️ MUST be false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql        # ⚠️ Don't use SQLite
DB_DATABASE=stampalbumdesigns_production
DB_USERNAME=production_user
DB_PASSWORD=secure_password_here

CACHE_DRIVER=redis        # Recommended for production
SESSION_DRIVER=redis      # Recommended for production
```

---

## ✅ Pre-Launch Checklist

Quick verification before going live:

**Code:**
- [ ] All tests passing (`php artisan test`)
- [ ] Git repository clean
- [ ] No debug code or dd() statements

**Environment:**
- [ ] APP_DEBUG=false
- [ ] APP_ENV=production
- [ ] Strong APP_KEY generated
- [ ] Database connection working

**Security:**
- [ ] SSL certificate installed
- [ ] .env file permissions: 600
- [ ] Admin user created with strong password
- [ ] Web server configured correctly

**Testing:**
- [ ] Homepage loads
- [ ] Admin can login to /lunar
- [ ] Customer can place order
- [ ] Orders save with JSON data

---

## 🔄 Future Updates

```bash
php artisan down
git pull origin main
composer install --no-dev
npm install && npm run build
php artisan migrate --force
php artisan optimize
php artisan up
```

---

## 📞 Emergency Commands

**Site down? Quick fixes:**

```bash
# Clear all caches
php artisan optimize:clear

# Check logs
tail -f storage/logs/laravel.log

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx

# Database issues
php artisan migrate:status
php artisan tinker --execute="echo \Lunar\Models\Currency::count();"
```

---

## 🎯 What's Included

After deployment, you'll have:

✅ **Lunar PHP v1.1.0** - Full e-commerce platform
✅ **Order Management** - Admin panel at `/lunar`
✅ **Customer Portal** - Order history at `/my-orders`
✅ **Role-Based Access** - Admin and Customer roles
✅ **Order Data Capture** - Complete JSON data from order builder
✅ **Session Cart** - Persistent shopping cart
✅ **Checkout System** - Complete checkout flow

**Not Yet Included (Phase 2):**
- ❌ Stripe payments (manual for now)
- ❌ Email notifications
- ❌ PDF invoices

---

## 📚 Full Documentation

See `DEPLOYMENT_CHECKLIST.md` for:
- Detailed step-by-step instructions
- Security hardening guide
- Backup configuration
- Monitoring setup
- Web server configurations
- Troubleshooting guide

---

## 🆘 Need Help?

1. Check `storage/logs/laravel.log`
2. Review `DEPLOYMENT_CHECKLIST.md`
3. Run `php artisan test` to verify integrity
4. Check web server error logs

**Test Locally First:**
```bash
php artisan setup:test-data --fresh
php artisan show:test-credentials
php artisan test
```

---

**Version:** 2.0.0 (Lunar Phase 1)
**Deployment Time:** ~30 minutes
**Next Phase:** Stripe Payment Integration
