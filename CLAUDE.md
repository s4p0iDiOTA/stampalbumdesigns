# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 11 application for stamp album designs with the following key features:
- User authentication and profile management
- Country and period management for stamp collections
- Shopping cart functionality for stamp albums
- Contact form system
- Admin dashboard for authenticated users

## Development Commands

### Laravel/PHP Commands
- `php artisan serve` - Start the Laravel development server
- `php artisan migrate` - Run database migrations
- `php artisan migrate:fresh --seed` - Reset database and run seeders
- `php artisan tinker` - Access Laravel's interactive shell
- `vendor/bin/pint` - Run Laravel Pint (PHP code formatter)

### Frontend Commands
- `npm run dev` - Start Vite development server with hot reload
- `npm run build` - Build frontend assets for production

### Testing
- `php artisan test` - Run all tests using Pest PHP
- `php artisan test --filter=ExampleTest` - Run specific test
- `vendor/bin/pest` - Run Pest tests directly

## Architecture

### Models and Relationships
- **User**: Standard Laravel user model with authentication
- **Country**: Represents stamp-issuing countries
- **Period**: Belongs to Country, represents time periods for stamp collections
- **Relationship**: Country hasMany Periods

### Controllers
- **AuthController**: Handles user authentication (Laravel Breeze)
- **CartController**: Manages shopping cart operations
- **CountryController**: Handles country search and listing
- **ContactController**: Manages contact form submissions
- **ProfileController**: User profile management

### Frontend Stack
- **Blade Templates**: Laravel's templating engine
- **Tailwind CSS**: Utility-first CSS framework
- **Alpine.js**: Lightweight JavaScript framework
- **Vite**: Frontend build tool

### Key Routes
- `/` - Home page
- `/products` - Product catalog with cart functionality
- `/cart` - Shopping cart management
- `/contact` - Contact form
- `/dashboard` - Admin dashboard (requires auth)
- `/search-country` - AJAX country search
- `/countries` - List country names

### Database
- Uses standard Laravel migrations
- Key tables: users, countries, periods
- SQLite database for development (typical Laravel setup)

### Testing Framework
- **Pest PHP**: Modern testing framework
- Tests organized in Feature and Unit directories
- Includes authentication tests and profile management tests

## Development Notes

### Authentication
- Uses Laravel Breeze for authentication scaffolding
- Email verification available but not required
- Standard password reset functionality

### Frontend Development
- Blade components are used for reusable UI elements
- JavaScript helpers available in `public/js/helpers.js`
- Custom CSS in `public/css/main.css`
- Country data available in `public/countrylist_datafile.js`

### Cart System
- Session-based shopping cart
- AJAX operations for cart updates
- Supports add, update, remove, and clear operations