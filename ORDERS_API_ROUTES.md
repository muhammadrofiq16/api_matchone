# Orders API Implementation Summary

## Database Table Structure

### Orders Table
```
Columns: id, user_id, invoice_number, total_price, status, payment_method
Primary Keys: id (BIGINT AUTO_INCREMENT)
Foreign Keys: user_id → users.id (CASCADE DELETE)
```

#### Column Details
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Order identifier |
| `user_id` | BIGINT | FOREIGN KEY, NOT NULL | Reference to user who made order |
| `invoice_number` | VARCHAR(255) | UNIQUE, NOT NULL | Unique invoice identifier |
| `total_price` | DECIMAL(12,2) | NOT NULL | Order total amount |
| `status` | ENUM | NOT NULL, DEFAULT: 'pending' | Order status (pending, paid, processing, completed, cancelled) |
| `payment_method` | VARCHAR(255) | NOT NULL | Payment method used |
| `created_at` | TIMESTAMP | | Order creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

#### Status Values
- **pending**: Order created but not yet paid
- **paid**: Payment has been received
- **processing**: Order is being prepared/shipped
- **completed**: Order has been delivered/fulfilled
- **cancelled**: Order has been cancelled

---

## API Routes

### Authentication Required (Sanctum)

#### 1. List Orders
- **Endpoint**: `GET /api/orders`
- **Auth**: Required (Sanctum)
- **Description**: 
  - Customers see only their own orders
  - Admins see all orders
- **Query Parameters**: 
  - `status`: Filter by order status (optional)
  - `payment_method`: Filter by payment method (optional)
- **Response** (200):
```json
{
  "message": "Orders retrieved successfully",
  "data": [
    {
      "id": 1,
      "user_id": 2,
      "invoice_number": "INV-AB12CD34",
      "total_price": "150.50",
      "status": "completed",
      "payment_method": "credit_card",
      "created_at": "2026-05-10T10:30:00Z",
      "updated_at": "2026-05-10T10:35:00Z",
      "user": { ... }
    }
  ]
}
```

#### 2. Create Order
- **Endpoint**: `POST /api/orders`
- **Auth**: Required (Sanctum)
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
  - `payment_method`: Required string, max 255 characters
- **Response** (201):
```json
{
  "message": "Order created successfully",
  "data": { ... }
}
```
- **Error Response** (422):
```json
{
  "message": "Validation failed",
  "errors": { ... }
}
```

#### 3. Get Single Order
- **Endpoint**: `GET /api/orders/{id}`
- **Auth**: Required (Sanctum)
- **Authorization**:
  - Customers can only view their own orders
  - Admins can view any order
- **Response** (200):
```json
{
  "message": "Order retrieved successfully",
  "data": { ... }
}
```
- **Error Response** (404):
```json
{
  "message": "Order not found"
}
```
- **Error Response** (403):
```json
{
  "message": "Unauthorized to access this order"
}
```

#### 4. Delete Order
- **Endpoint**: `DELETE /api/orders/{id}`
- **Auth**: Required (Sanctum)
- **Authorization**:
  - Customers can delete their own orders
  - Admins can delete any order
- **Response** (200):
```json
{
  "message": "Order deleted successfully"
}
```

### Admin-Only Routes (Sanctum + Admin Role)

#### 5. Update Order Status
- **Endpoint**: `PUT /api/orders/{id}`
- **Auth**: Required (Sanctum + Admin role)
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
  - `payment_method`: Optional string, max 255 characters
  - `total_price`: Optional numeric, minimum 0
- **Response** (200):
```json
{
  "message": "Order updated successfully",
  "data": { ... }
}
```

---

## Authorization Matrix

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

---

## Models

### Order Model
- **Location**: `app/Models/Order.php`
- **Fillable Fields**: user_id, invoice_number, total_price, status, payment_method
- **Casts**: total_price (decimal:2), created_at (datetime), updated_at (datetime)
- **Relationships**: 
  - `user()` - belongsTo User

### User Model
- **Updated**: Added `orders()` relationship
- **Relationships**: 
  - `orders()` - hasMany Order

---

## Testing Examples

### 1. Login and Get Token
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password"
  }'
```

### 2. Create Order
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

### 3. Get All Orders
```bash
curl -X GET http://localhost:8000/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 4. Get Orders by Status
```bash
curl -X GET "http://localhost:8000/api/orders?status=processing" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 5. Get Single Order
```bash
curl -X GET http://localhost:8000/api/orders/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 6. Update Order (Admin Only)
```bash
curl -X PUT http://localhost:8000/api/orders/1 \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "completed"
  }'
```

### 7. Delete Order
```bash
curl -X DELETE http://localhost:8000/api/orders/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Files Modified/Created

### Created Files
- ✅ Migration: `database/migrations/2026_05_10_124006_create_orders_table.php`
- ✅ Model: `app/Models/Order.php`
- ✅ Controller: `app/Http/Controllers/OrderController.php`
- ✅ Factory: `database/factories/OrderFactory.php`
- ✅ Seeder: `database/seeders/OrderSeeder.php`

### Modified Files
- ✅ `routes/api.php` - Added OrderController import and order routes
- ✅ `app/Models/User.php` - Added orders() relationship
- ✅ `app/Http/Controllers/Web/AdminController.php` - Added Order statistics
- ✅ `resources/views/admin/dashboard.blade.php` - Display dynamic active orders

---

## Status Flow Diagram

```
┌─────────────────────────────────────────────────────────┐
│                    Order Lifecycle                      │
└─────────────────────────────────────────────────────────┘

    ┌──────────┐
    │ PENDING  │ ← Order created, awaiting payment
    └────┬─────┘
         │
         ├─→ ┌──────────┐
         │   │   PAID   │ ← Payment received
         │   └────┬─────┘
         │        │
         │        └─→ ┌──────────────┐
         │            │ PROCESSING   │ ← Being prepared/shipped
         │            └────┬─────────┘
         │                 │
         │                 └─→ ┌──────────────┐
         │                     │ COMPLETED    │ ← Delivered/Fulfilled
         │                     └──────────────┘
         │
         └─→ ┌──────────────┐
             │  CANCELLED   │ ← Cancelled by user/admin
             └──────────────┘
```

---

## Future Enhancements

Potential features to add:
1. Order items/line items table
2. Order tracking/shipment information
3. Order notifications (email/SMS)
4. Order cancellation reasons
5. Order history and status change logs
6. Payment gateway integration
7. Order receipts/invoicing
8. Return/refund management
9. Order statistics and reporting
10. Bulk order operations
