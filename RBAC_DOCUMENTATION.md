# Role-Based Access Control (RBAC) Implementation

## Overview
This document describes the role-based access control system implemented in the MatchOne API.

## Roles
The system supports two roles:

### 1. **Admin** (admin)
- Can create, read, update, and delete categories
- Can create, read, update, and delete products
- Can toggle product availability
- Can manage all dashboard operations

### 2. **Customer** (customer)
- Can view categories (read-only)
- Can view products (read-only)
- Can update their own profile
- Cannot access admin operations

## Database Schema

### Users Table
```sql
- id (BIGINT, PK)
- name (VARCHAR)
- email (VARCHAR, UNIQUE)
- password (VARCHAR)
- phone (VARCHAR, NULLABLE)
- points (INT, DEFAULT 0)
- role (VARCHAR, DEFAULT 'customer') -- 'admin' or 'customer'
- remember_token (VARCHAR, NULLABLE)
- email_verified_at (TIMESTAMP, NULLABLE)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

## Implementation Details

### Files Modified/Created:

#### 1. **app/Http/Middleware/AdminMiddleware.php** (NEW)
- Validates that the authenticated user has admin role
- Returns 401 if user is not authenticated
- Returns 403 if user is not an admin
- Usage: Applied to admin-only routes

#### 2. **app/Models/User.php** (UPDATED)
Added helper methods for role checking:
- `isAdmin()` - Returns true if user is admin
- `isCustomer()` - Returns true if user is customer
- `hasRole(string $role)` - Returns true if user has specified role

#### 3. **bootstrap/app.php** (UPDATED)
- Registered AdminMiddleware as 'admin' alias
- Allows easy use in routes via middleware('admin')

#### 4. **routes/api.php** (UPDATED)
- Protected admin routes with middleware('admin')
- Admin operations now require both authentication AND admin role:
  - POST /categories - Create category
  - PUT /categories/{id} - Update category
  - DELETE /categories/{id} - Delete category
  - POST /products - Create product
  - PUT /products/{id} - Update product
  - DELETE /products/{id} - Delete product
  - PATCH /products/{id}/toggle-availability - Toggle availability

#### 5. **database/seeders/DatabaseSeeder.php** (UPDATED)
- Creates sample admin user: admin@example.com (password: password)
- Creates sample customer user: test@example.com (password: password)

## API Usage Examples

### Login as Admin
```bash
POST /api/auth/login
{
    "email": "admin@example.com",
    "password": "password"
}

Response:
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@example.com",
        "role": "admin",
        ...
    },
    "token": "your_api_token"
}
```

### Access Admin Route (Create Category)
```bash
POST /api/categories
Headers:
  Authorization: Bearer {token}

{
    "name": "Electronics",
    "description": "Electronic products"
}

Response (201 Created):
{
    "message": "Category created successfully",
    ...
}
```

### Customer Attempting Admin Operation
```bash
POST /api/categories
Headers:
  Authorization: Bearer {customer_token}

{
    "name": "Electronics",
    "description": "Electronic products"
}

Response (403 Forbidden):
{
    "message": "Unauthorized - Admin access required",
    "current_role": "customer"
}
```

## Usage in Controllers

### Example: Using role helper methods
```php
public function someAction(Request $request)
{
    $user = $request->user();
    
    // Check if admin
    if ($user->isAdmin()) {
        // Allow admin operation
    }
    
    // Check if customer
    if ($user->isCustomer()) {
        // Allow customer operation
    }
    
    // Check specific role
    if ($user->hasRole('admin')) {
        // Allow admin operation
    }
}
```

## Error Responses

### 401 Unauthorized (No Authentication)
```json
{
    "message": "Unauthenticated"
}
```

### 403 Forbidden (Insufficient Permissions)
```json
{
    "message": "Unauthorized - Admin access required",
    "current_role": "customer"
}
```

## Default Behavior

- **New registrations** are created with `role = 'customer'` by default
- **Admin users** must be created manually or via database seeding
- **Role cannot be changed** during registration (only by direct database update or admin action)

## Future Enhancements

1. Create an admin endpoint to promote/demote users
2. Add role-based notifications
3. Implement audit logging for admin actions
4. Add more granular permissions (e.g., edit-only vs delete)
5. Create separate middleware for specific roles or permissions
