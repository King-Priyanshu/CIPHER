# API Documentation
Base URL: `http://localhost:8000/api`

## Authentication
- **Login**: `POST /login`
  - Body: `{ "email": "user@example.com", "password": "password" }`
- **Register**: `POST /register`
  - Body: `{ "name": "User", "email": "...", "password": "...", "password_confirmation": "...", "referral_code": "OPTIONAL" }`

## User
- **Profile**: `GET /user`
  - Headers: `Authorization: Bearer <token>`
- **Wallet**: `GET /user/wallet`
- **Transactions**: `GET /user/wallet/transactions`
- **Investments**: `GET /user/investments`

## Projects
- **List**: `GET /projects`
- **Details**: `GET /projects/{id}`
- **Invest**: `POST /projects/{id}/invest`
  - Body: `{ "amount": 5000, "plan_id": 1 }`

## Subscriptions
- **Plans**: `GET /plans`
- **Subscribe**: `POST /subscriptions`
  - Body: `{ "plan_id": 1 }`

## Webhooks
- **Razorpay**: `POST /webhooks/razorpay`
  - Headers: `X-Razorpay-Signature: <sig>`
