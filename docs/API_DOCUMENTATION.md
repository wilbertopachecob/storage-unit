# Storage Unit API Documentation

## Overview

The Storage Unit API provides a RESTful interface for managing storage items, categories, locations, and analytics. The API follows REST conventions and uses JSON for data exchange.

**Base URL:** `https://yourdomain.com/api/v1`

## Authentication

The API uses session-based authentication. Users must be logged in to access protected endpoints.

### Authentication Endpoints

#### Login
```http
POST /api/v1/auth/login
```

**Request Body:**
```json
{
  "username": "string",
  "password": "string"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "id": 1,
    "username": "john_doe",
    "email": "john@example.com",
    "storage_unit_name": "My Storage Unit",
    "profile_picture": "avatar.jpg",
    "created_at": "2024-01-01T00:00:00Z"
  },
  "code": 200,
  "timestamp": "2024-01-01T00:00:00Z"
}
```

#### Register
```http
POST /api/v1/auth/register
```

**Request Body:**
```json
{
  "username": "string",
  "email": "string",
  "password": "string",
  "confirm_password": "string"
}
```

#### Logout
```http
POST /api/v1/auth/logout
```

#### Get Current User
```http
GET /api/v1/auth/me
```

#### Refresh Session
```http
POST /api/v1/auth/refresh
```

## Items API

### Get All Items
```http
GET /api/v1/items
```

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `limit` (optional): Items per page (default: 20, max: 100)
- `search` (optional): Search term
- `category_id` (optional): Filter by category
- `location_id` (optional): Filter by location
- `sort_by` (optional): Sort field (title, created_at, updated_at, qty)
- `sort_order` (optional): Sort order (asc, desc)

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "items": [
      {
        "id": 1,
        "title": "Hammer",
        "description": "Heavy duty hammer",
        "qty": 1,
        "img": "hammer.jpg",
        "category_id": 1,
        "location_id": 1,
        "created_at": "2024-01-01T00:00:00Z",
        "updated_at": "2024-01-01T00:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 1,
      "total_pages": 1,
      "has_next": false,
      "has_prev": false
    }
  },
  "code": 200,
  "timestamp": "2024-01-01T00:00:00Z"
}
```

### Get Item by ID
```http
GET /api/v1/items/{id}
```

### Create Item
```http
POST /api/v1/items
```

**Request Body:**
```json
{
  "title": "string (required)",
  "description": "string (optional)",
  "qty": "integer (optional, default: 1)",
  "img": "string (optional)",
  "category_id": "integer (optional)",
  "location_id": "integer (optional)"
}
```

### Update Item
```http
PUT /api/v1/items/{id}
```

**Request Body:** Same as create, all fields required

### Partially Update Item
```http
PATCH /api/v1/items/{id}
```

**Request Body:** Any subset of fields from create

### Delete Item
```http
DELETE /api/v1/items/{id}
```

**Response:** 204 No Content

## Categories API

### Get All Categories
```http
GET /api/v1/categories
```

**Query Parameters:**
- `page` (optional): Page number
- `limit` (optional): Items per page
- `search` (optional): Search term
- `sort_by` (optional): Sort field (name, created_at, updated_at, item_count)
- `sort_order` (optional): Sort order (asc, desc)

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "items": [
      {
        "id": 1,
        "name": "Tools",
        "color": "#007bff",
        "icon": "fas fa-tools",
        "item_count": 5,
        "created_at": "2024-01-01T00:00:00Z",
        "updated_at": "2024-01-01T00:00:00Z"
      }
    ],
    "pagination": { ... }
  },
  "code": 200,
  "timestamp": "2024-01-01T00:00:00Z"
}
```

### Get Category by ID
```http
GET /api/v1/categories/{id}
```

**Response includes items in the category:**
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "id": 1,
    "name": "Tools",
    "color": "#007bff",
    "icon": "fas fa-tools",
    "item_count": 2,
    "items": [
      {
        "id": 1,
        "title": "Hammer",
        "description": "Heavy duty hammer",
        "qty": 1,
        "img": "hammer.jpg"
      }
    ],
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-01-01T00:00:00Z"
  },
  "code": 200,
  "timestamp": "2024-01-01T00:00:00Z"
}
```

### Create Category
```http
POST /api/v1/categories
```

**Request Body:**
```json
{
  "name": "string (required)",
  "color": "string (optional, hex color, default: #007bff)",
  "icon": "string (optional, FontAwesome class, default: fas fa-box)"
}
```

### Update Category
```http
PUT /api/v1/categories/{id}
```

### Partially Update Category
```http
PATCH /api/v1/categories/{id}
```

### Delete Category
```http
DELETE /api/v1/categories/{id}
```

**Note:** Cannot delete categories that contain items.

## Locations API

### Get All Locations
```http
GET /api/v1/locations
```

**Query Parameters:** Same as categories

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "items": [
      {
        "id": 1,
        "name": "Garage",
        "description": "Main garage storage",
        "address": "123 Main St",
        "latitude": 40.7128,
        "longitude": -74.0060,
        "item_count": 3,
        "created_at": "2024-01-01T00:00:00Z",
        "updated_at": "2024-01-01T00:00:00Z"
      }
    ],
    "pagination": { ... }
  },
  "code": 200,
  "timestamp": "2024-01-01T00:00:00Z"
}
```

### Get Location by ID
```http
GET /api/v1/locations/{id}
```

### Create Location
```http
POST /api/v1/locations
```

**Request Body:**
```json
{
  "name": "string (required)",
  "description": "string (optional)",
  "address": "string (optional)",
  "latitude": "number (optional, -90 to 90)",
  "longitude": "number (optional, -180 to 180)"
}
```

### Update Location
```http
PUT /api/v1/locations/{id}
```

### Partially Update Location
```http
PATCH /api/v1/locations/{id}
```

### Delete Location
```http
DELETE /api/v1/locations/{id}
```

**Note:** Cannot delete locations that contain items.

## Analytics API

### Get Analytics Data
```http
GET /api/v1/analytics
```

**Query Parameters:**
- `period` (optional): Time period (all, month, year, week, day)
- `include_charts` (optional): Include chart data (true/false, default: true)
- `include_recent` (optional): Include recent items (true/false, default: true)

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "total_items": 25,
    "total_quantity": 50,
    "total_categories": 5,
    "total_locations": 3,
    "image_coverage": 80.0,
    "avg_quantity": 2.0,
    "monthly_data": {
      "2024-01": 5,
      "2024-02": 8,
      "2024-03": 12
    },
    "recent_items": [
      {
        "id": 1,
        "title": "New Item",
        "created_at": "2024-03-01T00:00:00Z"
      }
    ]
  },
  "code": 200,
  "timestamp": "2024-01-01T00:00:00Z"
}
```

### Get Analytics Summary
```http
GET /api/v1/analytics/summary
```

### Get Chart Data
```http
GET /api/v1/analytics/charts
```

**Query Parameters:**
- `period` (optional): Time period
- `chart_type` (optional): Chart type (all, monthly, category, location)

## Users API

### Get User Profile
```http
GET /api/v1/users/profile
```

### Update User Profile
```http
PUT /api/v1/users/profile
```

**Request Body:**
```json
{
  "username": "string (optional)",
  "email": "string (optional)",
  "storage_unit_name": "string (optional)",
  "profile_picture": "string (optional)"
}
```

### Partially Update User Profile
```http
PATCH /api/v1/users/profile
```

### Change Password
```http
POST /api/v1/users/change-password
```

**Request Body:**
```json
{
  "current_password": "string (required)",
  "new_password": "string (required)",
  "confirm_password": "string (required)"
}
```

### Get User Statistics
```http
GET /api/v1/users/stats
```

## Error Responses

All error responses follow this format:

```json
{
  "success": false,
  "error": "Error Type",
  "message": "Human readable error message",
  "code": 400,
  "details": {
    "field_name": "Specific field error"
  },
  "timestamp": "2024-01-01T00:00:00Z"
}
```

### Common HTTP Status Codes

- `200 OK` - Request successful
- `201 Created` - Resource created successfully
- `204 No Content` - Request successful, no content returned
- `400 Bad Request` - Invalid request data
- `401 Unauthorized` - Authentication required
- `403 Forbidden` - Access denied
- `404 Not Found` - Resource not found
- `405 Method Not Allowed` - HTTP method not allowed for this endpoint
- `409 Conflict` - Resource conflict (e.g., trying to delete category with items)
- `422 Unprocessable Entity` - Validation errors
- `500 Internal Server Error` - Server error

## Rate Limiting

Currently no rate limiting is implemented, but it's recommended to implement rate limiting in production.

## CORS

The API includes CORS headers to allow cross-origin requests from web applications.

## Examples

### Creating a new item with category and location

```bash
curl -X POST https://yourdomain.com/api/v1/items \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Drill",
    "description": "Cordless drill",
    "qty": 1,
    "category_id": 1,
    "location_id": 1
  }'
```

### Searching items

```bash
curl "https://yourdomain.com/api/v1/items?search=hammer&category_id=1&sort_by=title&sort_order=asc"
```

### Getting paginated results

```bash
curl "https://yourdomain.com/api/v1/items?page=2&limit=10"
```

## SDK Examples

### JavaScript/Fetch

```javascript
// Get all items
const response = await fetch('/api/v1/items', {
  method: 'GET',
  credentials: 'include'
});
const data = await response.json();

// Create new item
const newItem = await fetch('/api/v1/items', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  credentials: 'include',
  body: JSON.stringify({
    title: 'New Item',
    description: 'Item description',
    qty: 1
  })
});
```

### PHP/cURL

```php
// Get all items
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://yourdomain.com/api/v1/items');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);
```

## Changelog

### Version 1.0.0
- Initial API release
- Full CRUD operations for items, categories, locations
- Authentication system
- Analytics endpoints
- User management
- Comprehensive error handling
- Pagination support
- Search and filtering
