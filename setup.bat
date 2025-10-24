@echo off
REM Stamp Album Designs - Quick Setup Script for Windows
REM Run this as Administrator if you encounter permission issues

echo =====================================
echo Stamp Album Designs - Local Setup
echo =====================================
echo.

REM Check if PHP is available
where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] PHP is not installed or not in PATH
    echo Download from: https://windows.php.net/download/
    pause
    exit /b 1
)
echo [OK] PHP is installed

REM Check if Composer is available
where composer >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Composer is not installed or not in PATH
    echo Download from: https://getcomposer.org/download/
    pause
    exit /b 1
)
echo [OK] Composer is installed

REM Check if Node is available
where node >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Node.js is not installed or not in PATH
    echo Download from: https://nodejs.org/
    pause
    exit /b 1
)
echo [OK] Node.js is installed
echo.

echo All prerequisites met! Starting setup...
echo.

REM Install Composer dependencies
echo Step 1: Installing PHP dependencies...
if exist vendor (
    echo   Updating existing dependencies...
    call composer update --no-interaction
) else (
    echo   Installing fresh dependencies...
    call composer install --no-interaction
)
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Composer install failed
    pause
    exit /b 1
)
echo [OK] PHP dependencies installed
echo.

REM Install NPM dependencies
echo Step 2: Installing JavaScript dependencies...
if exist node_modules (
    echo   Updating existing dependencies...
    call npm update
) else (
    echo   Installing fresh dependencies...
    call npm install
)
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] NPM install failed
    pause
    exit /b 1
)
echo [OK] JavaScript dependencies installed
echo.

REM Setup environment file
echo Step 3: Setting up environment file...
if exist .env (
    echo   .env file already exists, skipping...
) else (
    copy .env.example .env
    echo [OK] .env file created
)
echo.

REM Generate application key
echo Step 4: Generating application key...
php artisan key:generate --force
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Key generation failed
    pause
    exit /b 1
)
echo [OK] Application key generated
echo.

REM Setup database
echo Step 5: Setting up database...
if not exist database\database.sqlite (
    type nul > database\database.sqlite
    echo [OK] Database file created
) else (
    echo   Database file already exists
)
echo.

REM Create storage directories
echo Step 6: Creating storage directories...
if not exist "storage\framework\cache\data" mkdir "storage\framework\cache\data"
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions"
if not exist "storage\framework\views" mkdir "storage\framework\views"
if not exist "storage\app\public" mkdir "storage\app\public"
if not exist "storage\logs" mkdir "storage\logs"
echo [OK] Storage directories created
echo.

REM Run migrations
echo Step 7: Running database migrations...
php artisan migrate --force
if %ERRORLEVEL% NEQ 0 (
    echo [WARNING] Migration had issues
    echo   You may need to run 'php artisan migrate' manually
) else (
    echo [OK] Database migrations completed
)
echo.

REM Create storage link
echo Step 8: Creating storage link...
php artisan storage:link
echo [OK] Storage link created
echo.

REM Build assets
echo Step 9: Building frontend assets...
call npm run build
if %ERRORLEVEL% NEQ 0 (
    echo [WARNING] Asset build had issues
    echo   You may need to run 'npm run build' manually
) else (
    echo [OK] Frontend assets built
)
echo.

REM Clear caches
echo Step 10: Clearing caches...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo [OK] Caches cleared
echo.

echo =====================================
echo Setup Complete!
echo =====================================
echo.
echo To start the development server, run:
echo   php artisan serve
echo.
echo Then visit:
echo   http://localhost:8000
echo.
echo Useful pages:
echo   - Order page: http://localhost:8000/order
echo   - Cart: http://localhost:8000/cart
echo   - Checkout: http://localhost:8000/checkout
echo.
echo For more information, see LOCAL_SETUP_GUIDE.md
echo.
pause
