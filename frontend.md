# CIPHER Frontend Documentation

## Current Implementation

The MVP uses **Laravel Blade** templates with **Tailwind CSS** and **Alpine.js** for interactivity.

> **Note:** React-based SPA is planned for Post-MVP (Phase 11) per `BUILD_ROADMAP.md`.

---

## Design System

### Theme
- **Dark Mode** inspired by Stake.com
- Primary: Indigo/Purple accent
- Background: Gray-800/900
- Text: Gray-100/200/400

### Typography
- System fonts via Tailwind defaults
- Headers: `text-xl`, `text-2xl`
- Body: `text-sm`, `text-base`

---

## Layouts

| Layout | Purpose | File |
|--------|---------|------|
| `guest` | Public pages (login, register, home) | `layouts/guest.blade.php` |
| `app` | Subscriber dashboard | `layouts/app.blade.php` |
| `admin` | Admin panel | `layouts/admin.blade.php` |

---

## View Structure

```
resources/views/
├── auth/
│   ├── login.blade.php
│   ├── register.blade.php
│   ├── forgot-password.blade.php
│   ├── reset-password.blade.php
│   └── verify-email.blade.php
├── public/
│   ├── home.blade.php
│   └── page.blade.php
├── subscriber/
│   ├── dashboard.blade.php
│   ├── projects/index.blade.php
│   └── rewards/index.blade.php
├── checkout/
│   └── index.blade.php
├── admin/
│   ├── dashboard.blade.php
│   ├── users/{index,show,edit}.blade.php
│   ├── projects/{index,create,edit}.blade.php
│   ├── plans/{index,create,edit}.blade.php
│   ├── pools/{index,create,edit}.blade.php
│   ├── reward-pools/{index,create,edit}.blade.php
│   └── pages/{index,create,edit}.blade.php
└── components/
    ├── layouts/
    ├── primary-button.blade.php
    ├── text-input.blade.php
    └── ...
```

---

## Key Components

- `x-primary-button` - Styled submit buttons
- `x-text-input` - Form inputs with dark theme
- `x-input-label` - Form labels
- `x-input-error` - Validation error display
- `x-auth-session-status` - Status messages

---

## Responsive Design

- Mobile-first with Tailwind breakpoints
- Mobile hamburger menu in `guest` layout
- Responsive tables in admin panel
- Grid layouts for dashboard cards

---

## Future: React SPA (Phase 11+)

Planned features:
- API-driven React frontend
- Dashboard widgets
- Real-time notifications
- Advanced charting/analytics

Technology options:
- Next.js or Vite + React
- React Query for data fetching
- Tailwind CSS (continued)
