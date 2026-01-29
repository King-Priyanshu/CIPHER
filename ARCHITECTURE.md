# System Architecture

## Overview
CIPHER is built on a robust, scalable LAMP/LEMP stack (Linux, Apache/Nginx, MySQL/PostgreSQL, PHP) leveraging the Laravel framework for the backend and Tailwind CSS for the frontend.

## Technology Stack

### Backend
- **Framework**: Laravel (latest stable version)
- **Language**: PHP
- **Key Features Used**:
    - Eloquent ORM for database interaction
    - Laravel Sanctum/Passport for authentication
    - Laravel Cashier (optional) for subscription management
    - Queue system for asynchronous tasks (emails, notifications)

### Frontend
- **Framework**: Blade Templates (server-side rendering) with Vue.js/Alpine.js (if interactivity is needed)
- **Styling**: Tailwind CSS
- **Responsive Design**: Mobile-first approach

### Database
- **Primary**: MySQL or PostgreSQL
- **Schema Design**:
    - Users (id, name, email, password, role, etc.)
    - Subscriptions (user_id, plan_id, status, start_date, end_date)
    - Payments (subscription_id, amount, status, transaction_id)
    - Projects (title, description, fund_goal, current_fund)
    - Rewards (user_id, project_id, amount, type)

### Infrastructure
- **Server**: VPS / Cloud (AWS, DigitalOcean, Linode)
- **Web Server**: Nginx or Apache
- **Cache**: Redis / Memcached (for session and cache management)
- **SSL**: Let's Encrypt

## Key Components

### Subscription Engine
Handles plan creation, user enrollment, recurring billing logic, and payment gateway webhooks.

### Project & Reward System
Manages the lifecycle of community projects, fund tracking, and the logic for distributing rewards to subscribers based on rules.

### Administration Panel
Provides a secure interface for admins to manage users, finance, and content.

## Security Architecture
- CSRF Protection (Laravel built-in)
- XSS Prevention (Blade escaping)
- SQL Injection Protection (Eloquent ORM)
- Secure Password Hashing (Bcrypt/Argon2)
- HTTPS enforcement

---

# Laravel Project Structure (Production-Ready)

## Folder Tree
```
app/
├─ Console/
├─ Events/
│  ├─ Payments/
│  │  ├─ PaymentFailed.php
│  │  └─ PaymentSucceeded.php
│  ├─ Rewards/
│  │  ├─ RewardCalculated.php
│  │  └─ RewardDistributed.php
│  └─ Subscriptions/
│     ├─ SubscriptionActivated.php
│     └─ SubscriptionCancelled.php
├─ Http/
│  ├─ Controllers/
│  │  ├─ Admin/
│  │  │  ├─ AdminDashboardController.php
│  │  │  ├─ ContentPageController.php
│  │  │  ├─ FundPoolController.php
│  │  │  ├─ PaymentController.php
│  │  │  ├─ ProjectController.php
│  │  │  ├─ ProjectUpdateController.php
│  │  │  ├─ RewardPoolController.php
│  │  │  ├─ RoleController.php
│  │  │  ├─ SubscriptionPlanController.php
│  │  │  └─ UserController.php
│  │  ├─ Public/
│  │  │  ├─ AuthController.php
│  │  │  ├─ HomeController.php
│  │  │  ├─ PricingController.php
│  │  │  └─ PublicPageController.php
│  │  └─ Subscriber/
│  │     ├─ SubscriberDashboardController.php
│  │     ├─ SubscriptionController.php
│  │     ├─ PaymentMethodController.php
│  │     ├─ ProjectController.php
│  │     └─ RewardController.php
│  ├─ Middleware/
│  │  ├─ EnsureAdmin.php
│  │  ├─ EnsureSubscriber.php
│  │  └─ EnsureTermsAccepted.php
│  └─ Requests/
│     ├─ Admin/
│     │  ├─ StoreProjectRequest.php
│     │  ├─ UpdateProjectRequest.php
│     │  ├─ StoreSubscriptionPlanRequest.php
│     │  ├─ UpdateSubscriptionPlanRequest.php
│     │  └─ StoreContentPageRequest.php
│     ├─ Subscriber/
│     │  ├─ UpdateSubscriptionRequest.php
│     │  └─ StorePaymentMethodRequest.php
│     └─ Public/
│        ├─ RegisterRequest.php
│        └─ LoginRequest.php
├─ Listeners/
│  ├─ Payments/
│  │  ├─ RecordInvoice.php
│  │  └─ UpdatePaymentStatus.php
│  ├─ Rewards/
│  │  ├─ AllocateRewardPool.php
│  │  └─ PersistRewardDistribution.php
│  └─ Subscriptions/
│     ├─ ActivateSubscription.php
│     └─ CancelSubscription.php
├─ Models/
│  ├─ ActivityLog.php
│  ├─ ContentPage.php
│  ├─ FundPool.php
│  ├─ Invoice.php
│  ├─ Payment.php
│  ├─ Project.php
│  ├─ ProjectUpdate.php
│  ├─ Reward.php
│  ├─ RewardPool.php
│  ├─ Role.php
│  ├─ Subscription.php
│  ├─ SubscriptionPlan.php
│  ├─ User.php
│  └─ UserSubscription.php
├─ Notifications/
│  ├─ PaymentFailedNotification.php
│  ├─ PaymentSucceededNotification.php
│  ├─ RewardDistributedNotification.php
│  └─ SubscriptionStatusChangedNotification.php
├─ Policies/
│  ├─ ContentPagePolicy.php
│  ├─ FundPoolPolicy.php
│  ├─ ProjectPolicy.php
│  ├─ RewardPolicy.php
│  ├─ SubscriptionPolicy.php
│  └─ UserPolicy.php
├─ Providers/
├─ Services/
│  ├─ Payments/
│  │  ├─ PaymentGatewayInterface.php
│  │  ├─ StripePaymentGateway.php
│  │  └─ RazorpayPaymentGateway.php
│  ├─ Subscriptions/
│  │  ├─ SubscriptionService.php
│  │  └─ SubscriptionPlanService.php
│  ├─ Funds/
│  │  ├─ FundPoolService.php
│  │  └─ FundAllocationService.php
│  └─ Rewards/
│     ├─ RewardCalculationService.php
│     └─ RewardDistributionService.php
routes/
├─ admin.php
├─ api.php
└─ web.php
database/
├─ factories/
├─ migrations/
│  ├─ 2026_01_29_000001_create_roles_table.php
│  ├─ 2026_01_29_000002_create_subscription_plans_table.php
│  ├─ 2026_01_29_000003_create_user_subscriptions_table.php
│  ├─ 2026_01_29_000004_create_payments_table.php
│  ├─ 2026_01_29_000005_create_invoices_table.php
│  ├─ 2026_01_29_000006_create_projects_table.php
│  ├─ 2026_01_29_000007_create_project_updates_table.php
│  ├─ 2026_01_29_000008_create_fund_pools_table.php
│  ├─ 2026_01_29_000009_create_reward_pools_table.php
│  ├─ 2026_01_29_000010_create_rewards_table.php
│  ├─ 2026_01_29_000011_create_activity_logs_table.php
│  └─ 2026_01_29_000012_create_content_pages_table.php
resources/
├─ views/
│  ├─ layouts/
│  │  ├─ admin.blade.php
│  │  ├─ app.blade.php
│  │  └─ subscriber.blade.php
│  ├─ components/
│  │  ├─ alert.blade.php
│  │  ├─ badge.blade.php
│  │  └─ table.blade.php
│  ├─ admin/
│  │  ├─ dashboard.blade.php
│  │  ├─ projects/
│  │  ├─ fund-pools/
│  │  ├─ reward-pools/
│  │  ├─ subscriptions/
│  │  ├─ users/
│  │  └─ content/
│  ├─ subscriber/
│  │  ├─ dashboard.blade.php
│  │  ├─ subscriptions/
│  │  ├─ projects/
│  │  ├─ rewards/
│  │  └─ payments/
│  ├─ public/
│  │  ├─ home.blade.php
│  │  ├─ pricing.blade.php
│  │  └─ pages/
│  └─ auth/
│     ├─ login.blade.php
│     └─ register.blade.php
```

## Major Modules and Responsibilities

### Controllers (Role-Separated)
- Admin controllers manage platform operations (projects, fund pools, reward pools, subscriptions, users, content).
- Subscriber controllers manage subscriber-facing dashboards, subscription lifecycle, rewards, and payment methods.
- Public controllers handle marketing/public pages and authentication endpoints.

### Services (Business Logic)
- Payments: gateway abstractions and recurring payment handling.
- Subscriptions: plan management and subscription lifecycle.
- Funds: pooled fund tracking and allocation workflows.
- Rewards: reward calculation and distribution orchestration.

### Events & Listeners
- Payment, subscription, and reward events emit domain changes.
- Listeners update invoices, fund pools, rewards, and audit trails.

### Policies & Middleware
- Policies enforce resource-level authorization.
- Middleware gates route access by role and mandatory consent (terms).

### Models & Database Layer
- Models represent each domain module: users, roles, subscriptions, payments, invoices, projects, fund pools, rewards, content pages, and audit logs.
- Migrations are timestamped and named for each module.

### Views
- Role-based layout files with dedicated admin/subscriber/public view directories.
- Components provide reusable UI building blocks.

### Routes
- Public routes in `routes/web.php`.
- Admin-only routes in `routes/admin.php`.
- API-ready endpoints in `routes/api.php` for future mobile app integration.
