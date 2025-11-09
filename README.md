# 🎨 Design AI - SaaS Dashboard

Welcome to **Design AI**, a modern SaaS platform built with Laravel 12 and Vue 3, designed to provide powerful AI-powered design tools with a beautiful, intuitive interface.

## 🌟 Welcome

Thank you for choosing Design AI! This platform combines the robustness of Laravel with the reactivity of Vue 3 to deliver a seamless experience for both administrators and clients. Whether you're managing users, generating AI-powered designs, or customizing your workflow, Design AI has you covered.

## ✨ Features

- **🔐 Secure Authentication**: Sanctum-based authentication with OTP password recovery
- **👥 Role-Based Access Control**: Powered by Spatie permissions (Admin/Client roles)
- **🌙 Dark Mode**: Beautiful dark theme support across the entire application
- **🌍 Internationalization**: Full support for English and Arabic with RTL/LTR layouts
- **📊 Advanced Data Tables**: Built-in search, sort, filter, and pagination
- **🎨 Modern UI**: Vue 3 components with Tailwind CSS 4.0
- **⚡ Real-time Updates**: Queue system for background processing
- **📱 Responsive Design**: Mobile-friendly interface

## 🚀 Quick Start

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & npm
- SQLite (default) or any supported database

### Installation

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations and seeders
php artisan migrate --seed

# Start development servers
composer dev
```

The `composer dev` command starts all services: Laravel server, queue worker, Pail logs, and Vite dev server.

### Default Credentials

**Admin Account:**
- Email: `admin@example.com`
- Password: `password`

**Client Account:**
- Email: `client@example.com`
- Password: `password`

⚠️ **Important**: Change these credentials in production!

## 📚 Documentation

For detailed development guidelines, architecture patterns, and best practices, see [CLAUDE.md](CLAUDE.md).

### Key Commands

```bash
composer dev          # Start all development services
npm run dev           # Vite dev server only
composer test         # Run tests
npm run build         # Production build
vendor/bin/pint       # Format code (Laravel Pint)
```

## 🏗️ Tech Stack

### Backend
- **Laravel 12** - PHP framework
- **Laravel Sanctum** - API authentication
- **Spatie Permissions** - Role & permission management
- **SQLite** - Default database

### Frontend
- **Vue 3** - Progressive JavaScript framework
- **Vite** - Next generation frontend tooling
- **Tailwind CSS 4.0** - Utility-first CSS framework
- **Pinia** - State management
- **Vue Router** - Official router
- **Vue I18n** - Internationalization

## 🎯 Project Structure

```
design-ai/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/          # Admin controllers
│   │   └── Client/         # Client controllers
│   ├── Services/           # Business logic layer
│   ├── Http/Requests/      # Validation rules
│   └── Http/Resources/     # API responses
│
├── resources/js/
│   ├── pages/
│   │   └── Modules/
│   │       ├── admin/      # Admin pages
│   │       └── client/     # Client pages
│   ├── services/           # API services
│   ├── store/              # Pinia stores
│   ├── components/         # Reusable components
│   └── i18n/locales/       # Translations
│
└── routes/
    └── api.php             # API routes
```

## 🌐 Supported Languages

- 🇬🇧 English (EN)
- 🇸🇦 Arabic (AR) with full RTL support

Switch languages using the language switcher in the navigation bar.

## 🛠️ Development

This project follows a strict architectural pattern:

- **Controllers**: HTTP handling only (use `HasDataTable` trait)
- **Services**: All business logic and database transactions
- **Requests**: Validation and authorization
- **Resources**: API response formatting

Every feature includes:
- ✅ Dark mode support
- ✅ Bilingual translations (EN/AR)
- ✅ RTL/LTR compatibility
- ✅ Responsive design
- ✅ Role-based permissions

## 📝 License

This project is proprietary software. All rights reserved.

## 🤝 Support

For issues, questions, or contributions, please contact the development team.

---

**Built with ❤️ using Laravel & Vue**
