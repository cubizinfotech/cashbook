# Cash Book Management System

A Laravel + Vue 3 application for managing Businesses with Members and Cashbooks. Each Business has many Members and Cashbooks, and each Cashbook has Transactions of type "in" (Cash In) and "out" (Cash Out).

## Features

- **Business Management**: Create, read, update, and delete businesses
- **Member Management**: Manage members associated with businesses
- **Cashbook Management**: Create and manage cashbooks for businesses
- **Transaction Management**: Track cash in and cash out transactions
- **RESTful API**: Complete CRUD operations via API endpoints
- **Vue 3 SPA**: Modern single-page application with Vue 3, Vue Router, and Pinia
- **Form Validation**: Server-side validation using FormRequests
- **API Resources**: Structured JSON responses
- **Feature Tests**: Comprehensive test coverage

## Tech Stack

- **Backend**: Laravel 10
- **Frontend**: Vue 3, Vue Router, Pinia
- **HTTP Client**: Axios
- **Testing**: Pest PHP
- **Authentication**: Laravel Sanctum

## Prerequisites

- PHP >= 8.1
- Composer
- Node.js >= 16.x and npm
- MySQL/PostgreSQL database
- Laravel Sanctum (included)

## Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd cash-book
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install Node.js dependencies

```bash
npm install
```

### 4. Environment setup

Copy the `.env.example` file to `.env`:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Configure your database in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cash_book
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run migrations

```bash
php artisan migrate
```

### 6. Seed the database (optional)

```bash
php artisan db:seed
```

### 7. Build frontend assets

For development:

```bash
npm run dev
```

For production:

```bash
npm run build
```

### 8. Start the development server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Running Tests

Run all tests:

```bash
php artisan test
```

Run specific test file:

```bash
php artisan test tests/Feature/Api/BusinessTest.php
```

## API Documentation

All API endpoints require authentication via Laravel Sanctum. Include the authentication token in the `Authorization` header:

```
Authorization: Bearer {token}
```

### Base URL

```
http://localhost:8000/api
```

### Authentication

First, you need to authenticate and get a token. This depends on your authentication setup (Sanctum).

### Businesses

#### List Businesses

```http
GET /api/businesses
```

**Query Parameters:**
- `search` (optional): Search by name, email, or phone
- `status` (optional): Filter by status (active, inactive, pending, suspended)
- `per_page` (optional): Items per page (default: 15)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Acme Corporation",
      "email": "contact@acme.com",
      "phone": "1234567890",
      "status": "active",
      "members": [...],
      "cashbooks": [...]
    }
  ]
}
```

#### Create Business

```http
POST /api/businesses
Content-Type: application/json

{
  "name": "Acme Corporation",
  "description": "A sample business",
  "email": "contact@acme.com",
  "phone": "1234567890",
  "address": "123 Main St",
  "status": "active"
}
```

**Response:**
```json
{
  "message": "Business created successfully",
  "data": {
    "id": 1,
    "name": "Acme Corporation",
    ...
  }
}
```

#### Get Business

```http
GET /api/businesses/{id}
```

#### Update Business

```http
PUT /api/businesses/{id}
Content-Type: application/json

{
  "name": "Updated Business Name",
  "email": "newemail@acme.com"
}
```

#### Delete Business

```http
DELETE /api/businesses/{id}
```

### Members

#### List Members

```http
GET /api/members
```

**Query Parameters:**
- `business_id` (optional): Filter by business
- `status` (optional): Filter by status
- `search` (optional): Search by name, email, or phone

#### Create Member

```http
POST /api/members
Content-Type: application/json

{
  "business_id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "9876543210",
  "date_of_birth": "1990-01-01",
  "gender": "male",
  "status": "active"
}
```

#### Get Member

```http
GET /api/members/{id}
```

#### Update Member

```http
PUT /api/members/{id}
Content-Type: application/json

{
  "name": "John Updated",
  "email": "johnupdated@example.com"
}
```

#### Delete Member

```http
DELETE /api/members/{id}
```

### Cashbooks

#### List Cashbooks

```http
GET /api/cashbooks
```

**Query Parameters:**
- `business_id` (optional): Filter by business
- `status` (optional): Filter by status
- `search` (optional): Search by title or description

#### Create Cashbook

```http
POST /api/cashbooks
Content-Type: application/json

{
  "business_id": 1,
  "title": "Main Cashbook",
  "description": "Primary cashbook for operations",
  "status": "active"
}
```

#### Get Cashbook

```http
GET /api/cashbooks/{id}
```

#### Update Cashbook

```http
PUT /api/cashbooks/{id}
Content-Type: application/json

{
  "title": "Updated Cashbook Title",
  "description": "Updated description"
}
```

#### Delete Cashbook

```http
DELETE /api/cashbooks/{id}
```

### Transactions

#### List Transactions

```http
GET /api/transactions
```

**Query Parameters:**
- `cashbook_id` (optional): Filter by cashbook
- `type` (optional): Filter by type (in, out)
- `status` (optional): Filter by status
- `date_from` (optional): Filter from date (YYYY-MM-DD)
- `date_to` (optional): Filter to date (YYYY-MM-DD)
- `search` (optional): Search by party name, description, or remark

#### Create Transaction

```http
POST /api/transactions
Content-Type: application/json

{
  "cashbook_id": 1,
  "type": "in",
  "amount": 1000.00,
  "party_name": "Customer ABC",
  "transaction_datetime": "2024-01-15 10:30:00",
  "description": "Payment received",
  "remark": "Invoice #123",
  "status": "active"
}
```

**Response:**
```json
{
  "message": "Transaction created successfully",
  "data": {
    "id": 1,
    "cashbook_id": 1,
    "type": "in",
    "amount": 1000.00,
    "party_name": "Customer ABC",
    ...
  }
}
```

#### Get Transaction

```http
GET /api/transactions/{id}
```

#### Update Transaction

```http
PUT /api/transactions/{id}
Content-Type: application/json

{
  "amount": 1500.00,
  "party_name": "Updated Party Name"
}
```

#### Delete Transaction

```http
DELETE /api/transactions/{id}
```

## Example API Calls

### Using cURL

#### Create a Business

```bash
curl -X POST http://localhost:8000/api/businesses \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "name": "Acme Corporation",
    "email": "contact@acme.com",
    "phone": "1234567890",
    "status": "active"
  }'
```

#### Create a Member

```bash
curl -X POST http://localhost:8000/api/members \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "business_id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "9876543210",
    "status": "active"
  }'
```

#### Create a Cashbook

```bash
curl -X POST http://localhost:8000/api/cashbooks \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "business_id": 1,
    "title": "Main Cashbook",
    "description": "Primary cashbook",
    "status": "active"
  }'
```

#### Create a Transaction (Cash In)

```bash
curl -X POST http://localhost:8000/api/transactions \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "cashbook_id": 1,
    "type": "in",
    "amount": 1000.00,
    "party_name": "Customer ABC",
    "transaction_datetime": "2024-01-15 10:30:00",
    "description": "Payment received"
  }'
```

#### Create a Transaction (Cash Out)

```bash
curl -X POST http://localhost:8000/api/transactions \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "cashbook_id": 1,
    "type": "out",
    "amount": 500.00,
    "party_name": "Supplier XYZ",
    "transaction_datetime": "2024-01-15 14:00:00",
    "description": "Payment made"
  }'
```

### Using JavaScript (Axios)

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${token}`
  }
});

// Create a business
const business = await api.post('/businesses', {
  name: 'Acme Corporation',
  email: 'contact@acme.com',
  phone: '1234567890',
  status: 'active'
});

// List businesses
const businesses = await api.get('/businesses');

// Create a transaction
const transaction = await api.post('/transactions', {
  cashbook_id: 1,
  type: 'in',
  amount: 1000.00,
  party_name: 'Customer ABC',
  transaction_datetime: '2024-01-15 10:30:00',
  description: 'Payment received'
});
```

## Frontend Structure

The Vue 3 SPA is located in `resources/js/`:

- `app.js`: Main application entry point
- `App.vue`: Root component
- `router/`: Vue Router configuration
- `stores/`: Pinia stores (business, member, cashbook, transaction)
- `pages/`: Page components (BusinessList, BusinessView)
- `components/`: Reusable components (BusinessForm, MemberForm, CashbookForm, CashbookView)

## Project Structure

```
cash-book/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/          # API Controllers
│   │   ├── Requests/         # FormRequest validation
│   │   └── Resources/        # API Resources
│   └── Models/               # Eloquent models
├── database/
│   ├── factories/             # Model factories
│   └── migrations/            # Database migrations
├── resources/
│   ├── js/                    # Vue 3 application
│   └── views/
│       └── app.blade.php      # SPA entry point
├── routes/
│   └── api.php                # API routes
└── tests/
    └── Feature/
        └── Api/                # Feature tests
```

## Error Handling

All API endpoints return appropriate HTTP status codes:

- `200`: Success
- `201`: Created
- `422`: Validation error
- `404`: Not found
- `500`: Server error

Validation errors are returned in the following format:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["The name field is required."],
    "email": ["The email must be a valid email address."]
  }
}
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
