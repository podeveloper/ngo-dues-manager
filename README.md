# NGO Dues Manager

A practical billing and payment management system for Non-Governmental Organizations (NGOs) built with Laravel. It automates monthly membership dues generation and supports multi-gateway payment processing.

**Built for Search and Rescue Organizations**: Addresses real-world needs of Search and Rescue teams with tiered membership billing, equipment fees, and flexible payment processing.

> **🎯 Interview Project**: This project was created as a technical interview demonstration focusing on clear architecture, maintainability, and testing fundamentals.

[![Tests](https://img.shields.io/badge/tests-65%20passed-success)](https://github.com/podeveloper/ngo-dues-manager)
[![PHP](https://img.shields.io/badge/php-%5E8.2-blue)](https://php.net)
[![Laravel](https://img.shields.io/badge/laravel-%5E12.0-red)](https://laravel.com)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

## 📋 Table of Contents

- [About This Project](#about-this-project)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
- [Testing](#testing)
- [License](#license)

## 🎓 About This Project

### Real-World Use Case

This project was developed to address a real-world need in **Search and Rescue (SAR) organizations**. These NGOs have specific billing requirements:

- **Different Membership Tiers**: Official members and candidate members pay different monthly dues
- **Equipment Fees**: Radio usage fees are charged only to official members who have radio equipment assigned
- **Automated Billing**: Monthly dues need to be automatically generated and tracked

The system was built as a proof-of-concept to explore how such requirements can be handled with modern web technologies.

### Technical Demonstration

This project is used for interview discussions and focuses on:

- **Architecture**: A simple service-oriented structure
- **Patterns**: Strategy and Factory usage where it fits
- **Testing**: Unit and feature tests (65 tests, 161 assertions)
- **Database**: Relational structure with Eloquent ORM
- **Code Style**: Laravel conventions
- **Documentation**: Short project notes

### Development Approach

I focused on understanding the core flows. I am not a test expert; in previous roles I focused more on building. Many tests were AI-generated, but I can explain what each test is intended to verify. AI assistance was mainly used for:

- Test case suggestions
- Documentation formatting
- Code optimization recommendations

## ✨ Features
- Monthly dues generation based on membership type
- Stripe & Iyzico payments (pluggable gateways)
- REST API with Sanctum auth
- Invoices, items, and payment tracking

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 12.x
- **Language**: PHP 8.2+
- **Database**: MySQL 8.0+
- **ORM**: Eloquent
- **Authentication**: Laravel Sanctum (token-based API auth)

### Frontend
- **Admin Panel**: Filament 3.2
- **UI Framework**: Livewire
- **Build Tool**: Vite
- **CSS**: Tailwind CSS (via Filament)

### Payment Gateways
- **Stripe**: International payment processing
- **Iyzico**: Turkish payment gateway integration

### Testing
- **Framework**: PHPUnit 11.x
- **Coverage**: 65 tests, 159 assertions
- **Types**: Unit, Integration, Feature tests

### Development Tools
- **Code Style**: Laravel Pint
- **Containerization**: Docker (compose.yaml)
- **Package Manager**: Composer, NPM

## 📦 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 8.0 or higher
- Node.js 18+ and NPM

### Setup Steps

1. **Clone the repository**
```bash
git clone https://github.com/podeveloper/ngo-dues-manager.git
cd ngo-dues-manager
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment configuration**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ngo_dues_manager
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Configure payment gateways (optional)**

If you want to try real gateway calls, set credentials in your `.env` file.

6. **Run migrations and seeders**
```bash
php artisan migrate
php artisan db:seed
```

7. **Build frontend assets**
```bash
npm run build
```

8. **Start development server**
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

### Running with Docker

```bash
docker-compose up -d
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

## 🧪 Testing

Run tests:

```bash
php artisan test
```

## 📝 License

This project is open-sourced software licensed under the [MIT license](LICENSE).
