# Stamp Album Designs - Quick Setup Script
# Run this in PowerShell (as Administrator if needed)

Write-Host "=====================================" -ForegroundColor Cyan
Write-Host "Stamp Album Designs - Local Setup" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan
Write-Host ""

# Check prerequisites
Write-Host "Checking prerequisites..." -ForegroundColor Yellow

# Check PHP
Write-Host "Checking PHP..." -ForegroundColor Yellow
$phpInstalled = $false
try {
    $phpVersion = & php --version 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "[OK] PHP is installed" -ForegroundColor Green
        Write-Host "     $($phpVersion[0])" -ForegroundColor Gray
        $phpInstalled = $true
    }
} catch {
    # Command not found
}

if (-not $phpInstalled) {
    Write-Host "[ERROR] PHP is not installed or not in PATH" -ForegroundColor Red
    Write-Host "        Download from: https://windows.php.net/download/" -ForegroundColor Yellow
    exit 1
}

# Check Composer
Write-Host "Checking Composer..." -ForegroundColor Yellow
$composerInstalled = $false
try {
    $composerVersion = & composer --version 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "[OK] Composer is installed" -ForegroundColor Green
        Write-Host "     $composerVersion" -ForegroundColor Gray
        $composerInstalled = $true
    }
} catch {
    # Command not found
}

if (-not $composerInstalled) {
    Write-Host "[ERROR] Composer is not installed or not in PATH" -ForegroundColor Red
    Write-Host "        Download from: https://getcomposer.org/download/" -ForegroundColor Yellow
    exit 1
}

# Check Node
Write-Host "Checking Node.js..." -ForegroundColor Yellow
$nodeInstalled = $false
try {
    $nodeVersion = & node --version 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "[OK] Node.js is installed" -ForegroundColor Green
        Write-Host "     Version: $nodeVersion" -ForegroundColor Gray
        $nodeInstalled = $true
    }
} catch {
    # Command not found
}

if (-not $nodeInstalled) {
    Write-Host "[ERROR] Node.js is not installed or not in PATH" -ForegroundColor Red
    Write-Host "        Download from: https://nodejs.org/" -ForegroundColor Yellow
    exit 1
}

Write-Host ""
Write-Host "All prerequisites met! Starting setup..." -ForegroundColor Green
Write-Host ""

# Step 1: Install Composer dependencies
Write-Host "Step 1: Installing PHP dependencies..." -ForegroundColor Cyan
if (Test-Path "vendor") {
    Write-Host "  Vendor directory exists, updating..." -ForegroundColor Yellow
    composer update --no-interaction
} else {
    Write-Host "  Installing fresh dependencies..." -ForegroundColor Yellow
    composer install --no-interaction
}

if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERROR] Composer install failed" -ForegroundColor Red
    exit 1
}
Write-Host "[OK] PHP dependencies installed" -ForegroundColor Green
Write-Host ""

# Step 2: Install NPM dependencies
Write-Host "Step 2: Installing JavaScript dependencies..." -ForegroundColor Cyan
if (Test-Path "node_modules") {
    Write-Host "  Node modules exist, updating..." -ForegroundColor Yellow
    npm update
} else {
    Write-Host "  Installing fresh dependencies..." -ForegroundColor Yellow
    npm install
}

if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERROR] NPM install failed" -ForegroundColor Red
    exit 1
}
Write-Host "[OK] JavaScript dependencies installed" -ForegroundColor Green
Write-Host ""

# Step 3: Setup environment file
Write-Host "Step 3: Setting up environment file..." -ForegroundColor Cyan
if (Test-Path ".env") {
    Write-Host "  .env file already exists, skipping..." -ForegroundColor Yellow
} else {
    Copy-Item ".env.example" ".env"
    Write-Host "[OK] .env file created from .env.example" -ForegroundColor Green
}
Write-Host ""

# Step 4: Generate application key
Write-Host "Step 4: Generating application key..." -ForegroundColor Cyan
php artisan key:generate --force
if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERROR] Key generation failed" -ForegroundColor Red
    exit 1
}
Write-Host "[OK] Application key generated" -ForegroundColor Green
Write-Host ""

# Step 5: Setup database
Write-Host "Step 5: Setting up database..." -ForegroundColor Cyan
$dbPath = "database\database.sqlite"
if (Test-Path $dbPath) {
    Write-Host "  Database file already exists" -ForegroundColor Yellow
    $response = Read-Host "  Recreate database? This will delete all data! (y/N)"
    if ($response -eq "y" -or $response -eq "Y") {
        Remove-Item $dbPath -Force
        New-Item -Path $dbPath -ItemType File -Force | Out-Null
        Write-Host "  [OK] Database file recreated" -ForegroundColor Green
    }
} else {
    New-Item -Path $dbPath -ItemType File -Force | Out-Null
    Write-Host "[OK] Database file created" -ForegroundColor Green
}
Write-Host ""

# Step 6: Create storage directories
Write-Host "Step 6: Creating storage directories..." -ForegroundColor Cyan
$storageDirs = @(
    "storage\framework\cache\data",
    "storage\framework\sessions",
    "storage\framework\views",
    "storage\app\public",
    "storage\logs"
)

foreach ($dir in $storageDirs) {
    if (-not (Test-Path $dir)) {
        New-Item -Path $dir -ItemType Directory -Force | Out-Null
    }
}
Write-Host "[OK] Storage directories created" -ForegroundColor Green
Write-Host ""

# Step 7: Run migrations
Write-Host "Step 7: Running database migrations..." -ForegroundColor Cyan
php artisan migrate --force
if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERROR] Migration failed" -ForegroundColor Red
    Write-Host "        You may need to run 'php artisan migrate' manually" -ForegroundColor Yellow
} else {
    Write-Host "[OK] Database migrations completed" -ForegroundColor Green
}
Write-Host ""

# Step 8: Create storage link
Write-Host "Step 8: Creating storage link..." -ForegroundColor Cyan
php artisan storage:link
if ($LASTEXITCODE -ne 0) {
    Write-Host "[WARNING] Storage link creation had issues (this is often fine)" -ForegroundColor Yellow
} else {
    Write-Host "[OK] Storage link created" -ForegroundColor Green
}
Write-Host ""

# Step 9: Build assets
Write-Host "Step 9: Building frontend assets..." -ForegroundColor Cyan
npm run build
if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERROR] Asset build failed" -ForegroundColor Red
    Write-Host "        You may need to run 'npm run build' manually" -ForegroundColor Yellow
} else {
    Write-Host "[OK] Frontend assets built" -ForegroundColor Green
}
Write-Host ""

# Step 10: Clear caches
Write-Host "Step 10: Clearing caches..." -ForegroundColor Cyan
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
Write-Host "[OK] Caches cleared" -ForegroundColor Green
Write-Host ""

# Setup complete
Write-Host "=====================================" -ForegroundColor Green
Write-Host "Setup Complete!" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Green
Write-Host ""
Write-Host "To start the development server, run:" -ForegroundColor Cyan
Write-Host "  php artisan serve" -ForegroundColor Yellow
Write-Host ""
Write-Host "Then visit:" -ForegroundColor Cyan
Write-Host "  http://localhost:8000" -ForegroundColor Yellow
Write-Host ""
Write-Host "Useful pages:" -ForegroundColor Cyan
Write-Host "  - Order page: http://localhost:8000/order" -ForegroundColor Gray
Write-Host "  - Cart: http://localhost:8000/cart" -ForegroundColor Gray
Write-Host "  - Checkout: http://localhost:8000/checkout" -ForegroundColor Gray
Write-Host ""
Write-Host "To watch frontend assets during development:" -ForegroundColor Cyan
Write-Host "  npm run dev" -ForegroundColor Yellow
Write-Host ""
Write-Host "For more information, see LOCAL_SETUP_GUIDE.md" -ForegroundColor Cyan
Write-Host ""
