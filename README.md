# 🔐 CIPHER
### Subscription-Based Community Project Platform

CIPHER is a transparent, community-driven platform where subscribers pool funds to support real-world projects and share in the value generated.

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![PHP](https://img.shields.io/badge/php-8.2+-purple.svg)
![Laravel](https://img.shields.io/badge/laravel-11.x-red.svg)

---

## 🚀 Getting Started

This project is fully scaffolded and ready for your local environment.

### Prerequisites
- **PHP 8.2** or higher
- **Composer**
- **Node.js** & **NPM**
- **MySQL** or **PostgreSQL**

### 📦 Installation

1.  **Clone the repository** (if you haven't already):
    ```bash
    git clone https://github.com/your-username/cipher.git
    cd cipher
    ```

2.  **Install PHP Dependencies**:
    ```bash
    composer install
    ```

3.  **Configure Environment**:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Edit `.env` and set your database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).*

4.  **Setup Database & Seed Data**:
    ```bash
    php artisan migrate --seed
    ```
    *This creates the schema and populates it with Plans, Roles, and a Super Admin user.*

5.  **Install Frontend Assets**:
    ```bash
    npm install
    npm run build
    ```

6.  **Run the Application**:
    ```bash
    php artisan serve
    ```
    Visit `http://localhost:8000` in your browser.

---

## 🔑 Default Credentials

**Super Admin**
- **Email**: `kya kro ge jaan ke `
- **Password**: `btaya nhi jata `

---

## 🏗️ Architecture

- **Backend**: Laravel 11, Event-Driven Architecture
- **Frontend**: Blade Templates + Tailwind CSS (Premium "Indigo" Theme)
- **Database**: MySQL/PostgreSQL (14 Tables)
- **Security**: Role-Based Access Control (RBAC), Policies, Audit Logs

### Core Modules
1.  **Subscriptions**: Recurring billing with monthly/quarterly/annual plans.
2.  **Projects**: Community funded initiatives with transparency tracking.
3.  **Funds & Rewards**: automated logic for determining and distributing pool value.
4.  **CMS**: Static content page management.

---

## 🧪 Running Tests

The project includes Feature and Unit tests for critical financial logic.

```bash
php artisan test
```

---

## 📚 Documentation

Detailed documentation can be found in the repository:
- **[ARCHITECTURE.md](ARCHITECTURE.md)**: System design and folder structure.
- **[BUILD_ROADMAP.md](BUILD_ROADMAP.md)**: The 13-Phase execution plan used to build this.
- **[ANALYSIS_REPORT.md](ANALYSIS_REPORT.md)**: Gap analysis and technical verification.
- **[database.md](database.md)**: Full schema reference.
