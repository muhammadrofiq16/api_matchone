# Orders Module Documentation

## Overview
The Orders module has been successfully implemented for the MatchOne API. This module manages customer orders with support for order creation, retrieval, status updates, and order tracking by users and administrators.

## Database Schema

### Orders Table Columns

| Column | Type | Description | Constraints |
|--------|------|-------------|-------------|
| `id` | BIGINT | Primary key | AUTO_INCREMENT |
| `user_id` | BIGINT | Foreign key to users table | NOT NULL, FOREIGN KEY |
| `invoice_number` | VARCHAR(255) | Unique invoice identifier | UNIQUE, NOT NULL |
| `total_price` | DECIMAL(12,2) | Order total amount | NOT NULL |
| `status` | ENUM | Order status | pending, paid, processing, completed, cancelled (DEFAULT: pending) |
| `payment_method` | VARCHAR(255) | Payment method used | NOT NULL |
| `created_at` | TIMESTAMP | Order creation time | |
| `updated_at` | TIMESTAMP | Last update time | |

## Files Created/Modified

### New Files
1. **Migration**: `database/migrations/2026_05_10_124006_create_orders_table.php`
   - Creates the orders table with all necessary columns
   - Establishes foreign key relationship with users table

2. **Model**: `app/Models/Order.php`
   - Eloquent model with mass assignment protection
   - Relationship to User model (belongsTo)
   - Type casting for decimal and datetime fields

3. **Controller**: `app/Http/Controllers/OrderController.php`
   - RESTful API controller with 5 methods
   - Built-in authorization checks for users vs admins
   - Comprehensive validation for all operations

4. **Factory**: `database/factories/OrderFactory.php`
   - Generates realistic test data for orders
   - Creates valid invoice numbers and random statuses
   - Links to existing users

5. **Seeder**: `database/seeders/OrderSeeder.php`
   - Seeds 30 test orders into the database
   - Integrates with DatabaseSeeder

### Modified Files
1. **Model**: `app/Models/User.php`
   - Added `orders()` relationship method
   - Users can now access their orders through `user->orders`

2. **Routes**: `routes/api.php`
   - Added OrderController import
   - Added protected order routes for authenticated users
   - Added admin-only order update route

3. **Seeder**: `database/seeders/DatabaseSeeder.php`
   - Added OrderSeeder to the call stack

## API Endpoints

### Public Endpoints
None - All order endpoints require authentication

### Protected Endpoints (Authenticated Users)

#### Get Orders
- **Endpoint**: `GET /api/orders`
- **Auth**: Required (Sanctum)
- **Description**: 
  - Customers see only their own orders
  - Admins see all orders
- **Query Parameters**: 
  - `status`: Filter by order status (optional)
  - `payment_method`: Filter by payment method (optional)
- **Response**: 
  ```json
  {
    "message": "Orders retrieved successfully",
    "data": [
      {
        "id": 1,
        "user_id": 2,
        "invoice_number": "INV-AB12CD34",
        "total_price": 150.50,
        "status": "completed",
        "payment_method": "credit_card",
        "created_at": "2026-05-10T10:30:00Z",
        "updated_at": "2026-05-10T10:35:00Z"
      }
    ]
  }
  ```

#### Create Order
- **Endpoint**: `POST /api/orders`
- **Auth**: Required (Sanctum)
- **Description**: Customer creates a new order
- **Request Body**:
  ```json
  {
    "invoice_number": "INV-AB12CD34",
    "total_price": 150.50,
    "status": "pending",
    "payment_method": "credit_card"
  }
  ```
- **Validation**:
  - `invoice_number`: Required, unique string
  - `total_price`: Required, numeric, minimum 0
  - `status`: Required, must be one of [pending, paid, processing, completed, cancelled]
  - `payment_method`: Required string

#### Get Single Order
- **Endpoint**: `GET /api/orders/{id}`
- **Auth**: Required (Sanctum)
- **Description**: 
  - Customers can only view their own orders
  - Admins can view any order
- **Response**: Returns single order object with user relationship

#### Delete Order
- **Endpoint**: `DELETE /api/orders/{id}`
- **Auth**: Required (Sanctum)
- **Description**: 
  - Customers can delete their own orders
  - Admins can delete any order

### Admin-Only Endpoints

#### Update Order Status
- **Endpoint**: `PUT /api/orders/{id}`
- **Auth**: Required (Sanctum + Admin role)
- **Description**: Admins only - update order status and details
- **Request Body**:
  ```json
  {
    "status": "processing",
    "payment_method": "bank_transfer",
    "total_price": 150.50
  }
  ```
- **Validation**:
  - `status`: Required, must be one of [pending, paid, processing, completed, cancelled]
  - All other fields are optional

## Order Status Flow

```
pending → paid → processing → completed
       ↘ cancelled
```

### Status Descriptions
- **pending**: Order created but not yet paid
- **paid**: Payment has been received
- **processing**: Order is being prepared/shipped
- **completed**: Order has been delivered/fulfilled
- **cancelled**: Order has been cancelled by user or admin

## Relationships

### User → Order (One-to-Many)
```php
// Get all orders for a user
$user->orders; // Returns Collection of Order models

// Create a new order for a user
$order = $user->orders()->create([...]);
```

### Order → User (Many-to-One)
```php
// Get the user who made the order
$order->user; // Returns User model
```

## Authorization Rules

| Action | Customer | Admin |
|--------|----------|-------|
| View own orders | ✅ | ✅ |
| View all orders | ❌ | ✅ |
| Create order | ✅ | ✅ |
| View own order details | ✅ | ✅ |
| View any order details | ❌ | ✅ |
| Update order status | ❌ | ✅ |
| Delete own order | ✅ | ✅ |
| Delete any order | ❌ | ✅ |

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated"
}
```

### 403 Forbidden
```json
{
  "message": "Unauthorized to access this order"
}
```

### 404 Not Found
```json
{
  "message": "Order not found"
}
```

### 422 Validation Failed
```json
{
  "message": "Validation failed",
  "errors": {
    "invoice_number": ["The invoice number field is required."],
    "total_price": ["The total price must be at least 0."]
  }
}
```

## Testing the Orders API

### 1. Create an Order
```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "invoice_number": "INV-TEST-001",
    "total_price": 99.99,
    "status": "pending",
    "payment_method": "credit_card"
  }'
```

### 2. Get All Orders
```bash
curl -X GET http://localhost:8000/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 3. Get Single Order
```bash
curl -X GET http://localhost:8000/api/orders/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 4. Update Order (Admin Only)
```bash
curl -X PUT http://localhost:8000/api/orders/1 \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "processing"
  }'
```

### 5. Delete Order
```bash
curl -X DELETE http://localhost:8000/api/orders/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Database Seeding

The OrderSeeder creates 30 sample orders with:
- Random users from the database
- Unique invoice numbers
- Random order statuses
- Random payment methods
- Random total prices between $10 and $5000

To seed only orders:
```bash
php artisan db:seed OrderSeeder
```

## Future Enhancements

Potential features to add in the future:
1. Order items/line items table for tracking individual products
2. Order tracking/shipment information
3. Order notifications (via email/SMS)
4. Order cancellation reasons
5. Order history and status change logs
6. Payment gateway integration
7. Order receipts/invoicing
8. Return/refund management
9. Order statistics and reporting
10. Bulk order operations
