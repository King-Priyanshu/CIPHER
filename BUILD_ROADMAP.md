# CIPHER Build Roadmap

## MVP-to-Production Development Plan

This document outlines a step-by-step roadmap for building CIPHER from MVP to a scalable production SaaS platform. Each phase builds incrementally on the previous, ensuring modular development and real-world feasibility.

---

## Phase 0: Foundation & Scope Lock

### Goal
Define MVP boundaries, legal-safe terminology, and system goals before writing any code.

### Why This Phase Matters
- Prevents scope creep during development
- Ensures legal compliance from day one (no "investment" or "guaranteed returns" language)
- Aligns all stakeholders on what MVP includes and excludes

### Deliverables
- [ ] Finalize feature list for MVP vs. future phases
- [ ] Document legal-safe terminology (rewards, contributions, participation)
- [ ] Define user roles and their permissions matrix
- [ ] Create wireframes for core user flows
- [ ] Sign off on tech stack decisions

### Scalability Consideration
Establish naming conventions and domain language that won't require refactoring as the platform grows.

---

## Phase 1: Technical Setup

### Goal
Initialize Laravel project, environment configuration, and frontend tooling.

### Why This Phase Matters
- Creates a solid foundation for all subsequent development
- Ensures consistent development environment across team members
- Sets up CI/CD pipeline early for continuous integration

### Deliverables
- [ ] Initialize Laravel project (latest stable)
- [ ] Configure environment files (.env.example with all required keys)
- [ ] Set up Tailwind CSS with Laravel Mix/Vite
- [ ] Configure database connections (MySQL/PostgreSQL)
- [ ] Set up Git repository with branching strategy
- [ ] Configure code quality tools (PHPStan, Laravel Pint)
- [ ] Create base layout templates (app, admin, subscriber)
- [ ] Set up logging and error tracking (Laravel Telescope for dev)

### Scalability Consideration
Structure the project for service-based architecture from the start. Create the Services directory and establish patterns for dependency injection.

---

## Phase 2: Authentication & Roles

### Goal
Implement secure user registration, login, password recovery, and role-based access control.

### Why This Phase Matters
- Security is foundational—must be correct from the start
- Role-based access prevents unauthorized actions
- Terms acceptance creates legal protection

### Deliverables
- [ ] User registration with email verification
- [ ] Login with rate limiting and brute-force protection
- [ ] Password reset flow
- [ ] Terms & Conditions acceptance (mandatory checkbox)
- [ ] Create Role model and migration
- [ ] Implement role assignment (Admin, Subscriber, Guest)
- [ ] Create EnsureAdmin and EnsureSubscriber middleware
- [ ] Create EnsureTermsAccepted middleware
- [ ] Set up UserPolicy for authorization
- [ ] Build basic profile management

### Database Schema
```
users: id, name, email, password, email_verified_at, role_id, terms_accepted_at, timestamps
roles: id, name, slug, description, timestamps
```

### Scalability Consideration
Design the roles table to support future permission granularity. Consider adding a permissions pivot table in later phases.

---

## Phase 3: Subscription & Billing

### Goal
Implement subscription plans, recurring payments, invoices, and payment failure handling.

### Why This Phase Matters
- Core revenue model of the platform
- Must handle edge cases (failed payments, cancellations, upgrades)
- Payment security is critical for user trust

### Deliverables
- [ ] Create SubscriptionPlan model and migration
- [ ] Create UserSubscription model and migration
- [ ] Create Payment model and migration
- [ ] Create Invoice model and migration
- [ ] Implement PaymentGatewayInterface (abstraction layer)
- [ ] Implement StripePaymentGateway service
- [ ] Implement RazorpayPaymentGateway service (placeholder)
- [ ] Create SubscriptionService for business logic
- [ ] Handle subscription lifecycle (create, upgrade, downgrade, cancel, pause)
- [ ] Implement webhook handlers for payment events
- [ ] Create PaymentSucceeded and PaymentFailed events
- [ ] Create invoice generation on successful payment
- [ ] Implement retry logic for failed payments
- [ ] Build subscription checkout flow UI

### Database Schema
```
subscription_plans: id, name, slug, description, price, currency, interval (monthly/quarterly/annual), trial_days, is_active, timestamps
user_subscriptions: id, user_id, plan_id, status, starts_at, ends_at, cancelled_at, timestamps
payments: id, user_id, subscription_id, gateway, gateway_transaction_id, amount, currency, status, paid_at, timestamps
invoices: id, user_id, payment_id, invoice_number, amount, tax, total, issued_at, timestamps
```

### Scalability Consideration
Abstract payment gateway logic behind an interface to easily add new providers. Store all payment events for audit trails.

---

## Phase 4: Subscriber Dashboard

### Goal
Provide subscribers with visibility into their subscription, billing history, projects, and notifications.

### Why This Phase Matters
- Transparency builds trust
- Self-service reduces support burden
- Clear visibility into participation status

### Deliverables
- [ ] Create SubscriberDashboardController
- [ ] Build dashboard overview (subscription status, next billing date)
- [ ] Display billing and payment history
- [ ] Show active and upcoming projects (read-only for now)
- [ ] Display reward history (placeholder until Phase 7)
- [ ] Implement notification center
- [ ] Create SubscriptionStatusChangedNotification
- [ ] Build subscription management UI (upgrade/downgrade/cancel)
- [ ] Add payment method management

### Views Structure
```
subscriber/
├── dashboard.blade.php
├── subscriptions/
│   ├── index.blade.php
│   ├── show.blade.php
│   └── manage.blade.php
├── payments/
│   ├── index.blade.php
│   └── invoices.blade.php
└── notifications/
    └── index.blade.php
```

### Scalability Consideration
Design the dashboard to accommodate future widgets and modules without major refactoring.

---

## Phase 5: Admin Panel

### Goal
Provide administrators with tools to manage users, subscriptions, and system overview.

### Why This Phase Matters
- Operational control is essential for platform management
- User management enables support and moderation
- System overview provides business intelligence

### Deliverables
- [ ] Create AdminDashboardController
- [ ] Build admin dashboard with key metrics
- [ ] Implement user management (list, view, edit, suspend)
- [ ] Implement subscription plan management (CRUD)
- [ ] Create user subscription management (view, cancel, extend)
- [ ] Build payment and invoice overview
- [ ] Implement activity log viewer
- [ ] Create admin-specific validation requests
- [ ] Set up admin route group with middleware

### Views Structure
```
admin/
├── dashboard.blade.php
├── users/
│   ├── index.blade.php
│   ├── show.blade.php
│   └── edit.blade.php
├── subscriptions/
│   ├── plans/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   └── user-subscriptions/
│       └── index.blade.php
└── payments/
    └── index.blade.php
```

### Scalability Consideration
Build the admin panel with component-based architecture for easy extension. Consider role-based admin permissions for future team growth.

---

## Phase 6: Project Management

### Goal
Enable project creation, activation, fund allocation tracking, and project updates.

### Why This Phase Matters
- Projects are the core value proposition
- Fund allocation transparency builds trust
- Project updates keep subscribers engaged

### Deliverables
- [ ] Create Project model and migration
- [ ] Create ProjectUpdate model and migration
- [ ] Create FundPool model and migration
- [ ] Implement ProjectController (Admin)
- [ ] Implement ProjectUpdateController (Admin)
- [ ] Implement FundPoolController (Admin)
- [ ] Create FundPoolService for allocation logic
- [ ] Create FundAllocationService for tracking
- [ ] Build project CRUD in admin panel
- [ ] Build project update management
- [ ] Display projects in subscriber dashboard
- [ ] Create ProjectPolicy for authorization
- [ ] Implement fund allocation workflow

### Database Schema
```
projects: id, title, slug, description, status (draft/active/completed/cancelled), fund_goal, current_fund, starts_at, ends_at, timestamps
project_updates: id, project_id, title, content, published_at, timestamps
fund_pools: id, name, total_amount, allocated_amount, period_start, period_end, timestamps
fund_allocations: id, fund_pool_id, project_id, amount, allocated_at, timestamps
```

### Scalability Consideration
Design project status workflow to support future states. Keep fund tracking granular for audit purposes.

---

## Phase 7: Reward System

### Goal
Implement reward pool creation, distribution logic, and reward history visibility.

### Why This Phase Matters
- Rewards are the subscriber value proposition
- Transparent distribution builds trust
- Clear history enables accountability

### Deliverables
- [ ] Create RewardPool model and migration
- [ ] Create Reward model and migration
- [ ] Implement RewardPoolController (Admin)
- [ ] Create RewardCalculationService
- [ ] Create RewardDistributionService
- [ ] Create RewardCalculated event
- [ ] Create RewardDistributed event
- [ ] Create AllocateRewardPool listener
- [ ] Create PersistRewardDistribution listener
- [ ] Build reward pool management in admin
- [ ] Display reward history in subscriber dashboard
- [ ] Create RewardDistributedNotification
- [ ] Implement RewardPolicy

### Database Schema
```
reward_pools: id, project_id, total_amount, distributed_amount, distribution_date, status, timestamps
rewards: id, user_id, reward_pool_id, amount, status (pending/distributed/cancelled), distributed_at, timestamps
```

### Distribution Logic Considerations
- Distribution based on subscription tier
- Distribution based on subscription duration
- Pro-rata calculations for partial periods
- No guaranteed returns—rewards are discretionary

### Scalability Consideration
Keep reward calculation logic in services for easy modification. Store calculation parameters for audit trails.

---

## Phase 8: Content, Trust & SEO

### Goal
Build public pages, legal pages, SEO structure, and transparency content.

### Why This Phase Matters
- Public pages drive conversions
- Legal pages provide protection
- SEO enables organic discovery
- Transparency content builds trust

### Deliverables
- [ ] Create ContentPage model and migration
- [ ] Implement ContentPageController (Admin)
- [ ] Build CMS for static pages
- [ ] Create public home page
- [ ] Create pricing page
- [ ] Create About/How It Works page
- [ ] Create Terms & Conditions page
- [ ] Create Privacy Policy page
- [ ] Create FAQ page
- [ ] Implement SEO meta tags system
- [ ] Create sitemap generation
- [ ] Set up robots.txt
- [ ] Implement Open Graph tags
- [ ] Create trust indicators (transparency reports placeholder)

### Database Schema
```
content_pages: id, title, slug, content, meta_title, meta_description, is_published, timestamps
```

### Scalability Consideration
Design CMS to support future content types (blog posts, announcements). Keep SEO configuration flexible.

---

## Phase 9: Security & Testing

### Goal
Harden security, validate access control, and test subscription flows.

### Why This Phase Matters
- Security vulnerabilities can destroy trust
- Testing prevents production issues
- Compliance requires documented security measures

### Deliverables
- [ ] Security audit of authentication flows
- [ ] Validate all authorization policies
- [ ] Test role-based access control
- [ ] Implement rate limiting on sensitive endpoints
- [ ] Add CSRF protection verification
- [ ] Implement input sanitization review
- [ ] Set up SQL injection prevention verification
- [ ] Configure secure headers (CSP, HSTS)
- [ ] Write feature tests for subscription flows
- [ ] Write feature tests for payment flows
- [ ] Write unit tests for services
- [ ] Test webhook handlers
- [ ] Perform payment gateway sandbox testing
- [ ] Document security measures

### Testing Coverage
- Authentication flows (register, login, password reset)
- Subscription lifecycle (create, upgrade, cancel)
- Payment processing (success, failure, retry)
- Role-based access (admin, subscriber, guest)
- Policy enforcement

### Scalability Consideration
Establish testing patterns that scale with the codebase. Set up CI pipeline to run tests on every commit.

---

## Phase 10: Deployment & Go-Live

### Goal
Deploy to production with SSL, cron jobs, monitoring, and documentation.

### Why This Phase Matters
- Production environment must be stable and secure
- Monitoring enables proactive issue resolution
- Documentation enables team scaling

### Deliverables
- [ ] Set up production server (VPS/Cloud)
- [ ] Configure web server (Nginx)
- [ ] Set up SSL certificate (Let's Encrypt)
- [ ] Configure production database
- [ ] Set up Redis for cache and sessions
- [ ] Configure queue worker (Supervisor)
- [ ] Set up cron jobs (scheduler)
- [ ] Configure backup system
- [ ] Set up monitoring and alerting
- [ ] Configure error tracking (Sentry/Bugsnag)
- [ ] Create deployment script/pipeline
- [ ] Write admin documentation
- [ ] Create runbook for common operations
- [ ] Perform load testing
- [ ] Execute go-live checklist

### Production Checklist
- [ ] APP_ENV=production
- [ ] APP_DEBUG=false
- [ ] Secure APP_KEY
- [ ] Database credentials secured
- [ ] Payment gateway in live mode
- [ ] SSL configured and forced
- [ ] Backups verified
- [ ] Monitoring active

### Scalability Consideration
Design infrastructure for horizontal scaling. Use environment-based configuration for easy staging/production parity.

---

## Post-MVP Roadmap (Future Phases)

### Phase 11: API Layer
- RESTful API for mobile app
- API authentication (Sanctum/Passport)
- API documentation (OpenAPI/Swagger)

### Phase 12: Advanced Features
- Referral system
- Coupon and discount management
- Advanced analytics dashboard
- Multi-currency support

### Phase 13: Automation
- Automated reward calculations
- Scheduled reports
- Smart notifications

### Phase 14: Mobile App
- React Native / Flutter app
- Push notifications
- Biometric authentication

---

## Success Metrics

### MVP Success Criteria
- Users can register and subscribe
- Payments process successfully
- Subscribers can view their dashboard
- Admins can manage the platform
- Projects can be created and tracked
- Rewards can be distributed

### Production Readiness Criteria
- 99.9% uptime target
- Sub-3-second page loads
- Zero critical security vulnerabilities
- Automated backups verified
- Monitoring and alerting active
- Documentation complete

---

## Timeline Estimate

| Phase | Estimated Duration |
|-------|-------------------|
| Phase 0 | 1 week |
| Phase 1 | 1 week |
| Phase 2 | 1-2 weeks |
| Phase 3 | 2-3 weeks |
| Phase 4 | 1-2 weeks |
| Phase 5 | 1-2 weeks |
| Phase 6 | 2 weeks |
| Phase 7 | 1-2 weeks |
| Phase 8 | 1 week |
| Phase 9 | 1-2 weeks |
| Phase 10 | 1 week |
| **Total MVP** | **13-19 weeks** |

*Timeline assumes a small team (1-2 developers) working full-time.*

---

## Notes

- This roadmap prioritizes MVP-first delivery
- Each phase can be deployed incrementally
- Security and testing are integrated throughout
- No marketing, content creation, or legal advice included
- Mobile app is explicitly out of scope for MVP
