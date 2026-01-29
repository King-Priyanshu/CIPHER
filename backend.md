# CIPHER Backend Documentation

## Architecture Overview

CIPHER uses a **modular SaaS backend** built with Laravel, featuring role-based access control and comprehensive audit logging.

---

## Authentication

| Feature | Implementation |
|---------|----------------|
| Method | Email + Password |
| Password Reset | ✅ Implemented |
| Session Auth | Laravel Session |
| Role Authorization | Middleware-based |

### Roles
- **Super Admin** - Full platform control
- **Subscriber** - Dashboard & rewards access
- **Guest** - Public pages only

---

## Modules

### Users
- Registration with email verification
- Profile management
- Role assignment via `roles` table
- Terms acceptance tracking

### Subscriptions
- Monthly, Quarterly, Yearly plans
- Auto-renewal support
- Upgrade/Downgrade workflow
- Pause and Cancel options
- Billing cycle via `user_subscriptions` table

### Payments
- Gateway abstraction (`PaymentGatewayInterface`)
- Stripe & Razorpay (skeleton)
- Invoice generation
- Failed payment handling

### Fund Pool
- Central pooled fund from subscriptions
- Admin-controlled allocation
- Complete audit trail via `fund_allocations`

### Projects
- CRUD in admin panel
- Fund allocation tracking
- Project lifecycle (draft → active → completed)
- Profit declaration

### Rewards
- `RewardCalculationService` for logic
- `RewardDistributionService` for payouts
- Per-subscriber distribution history
- Reward pools linked to projects

### Notifications
- Email (Laravel Mail)
- In-app (future)
- System announcements via ContentPages

### Admin Panel
- User management CRUD
- Subscription plan management
- Project control & fund oversight
- Reward pool management
- Content page CMS

---

## Database Schema Summary

```
users → roles (belongsTo)
users → user_subscriptions (hasMany)
users → payments (hasMany)
users → rewards (hasMany)

subscription_plans → user_subscriptions (hasMany)
fund_pools → fund_allocations (hasMany)
projects → fund_allocations (hasMany)
projects → reward_pools (hasMany)
reward_pools → rewards (hasMany)
```

---

## Security Features
- CSRF protection
- Rate limiting on auth routes
- Password hashing (bcrypt)
- Signed URLs for email verification
- Role-based middleware (`EnsureAdmin`, `EnsureSubscriber`)

---

## API Readiness
Current implementation uses Blade views. API layer is marked for **Phase 11 (Post-MVP)** in `BUILD_ROADMAP.md`.
