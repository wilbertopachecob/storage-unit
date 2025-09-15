# API Setup Guide

## Overview

This guide explains how to set up and use the new RESTful API for the Storage Unit application.

## API Structure

The API is organized into versioned endpoints:

- **Base URL**: `/api/v1/`
- **Authentication**: Session-based
- **Data Format**: JSON
- **HTTP Methods**: GET, POST, PUT, PATCH, DELETE

## Available Endpoints

### Authentication (`/api/v1/auth/`)
- `POST /auth/register` - Register new user
- `POST /auth/login` - Login user
- `POST /auth/logout` - Logout user
- `GET /auth/me` - Get current user
- `POST /auth/refresh` - Refresh session

### Items (`/api/v1/items/`)
- `GET /items` - Get all items (with pagination, search, filtering)
- `GET /items/{id}` - Get specific item
- `POST /items` - Create new item
- `PUT /items/{id}` - Update item (full update)
- `PATCH /items/{id}` - Update item (partial update)
- `DELETE /items/{id}` - Delete item

### Categories (`/api/v1/categories/`)
- `GET /categories` - Get all categories
- `GET /categories/{id}` - Get specific category
- `POST /categories` - Create new category
- `PUT /categories/{id}` - Update category
- `PATCH /categories/{id}` - Update category
- `DELETE /categories/{id}` - Delete category

### Locations (`/api/v1/locations/`)
- `GET /locations` - Get all locations
- `GET /locations/{id}` - Get specific location
- `POST /locations` - Create new location
- `PUT /locations/{id}` - Update location
- `PATCH /locations/{id}` - Update location
- `DELETE /locations/{id}` - Delete location

### Analytics (`/api/v1/analytics/`)
- `GET /analytics` - Get analytics data
- `GET /analytics/summary` - Get analytics summary
- `GET /analytics/charts` - Get chart data

### Users (`/api/v1/users/`)
- `GET /users/profile` - Get user profile
- `PUT /users/profile` - Update user profile
- `PATCH /users/profile` - Update user profile
- `POST /users/change-password` - Change password
- `GET /users/stats` - Get user statistics

## Setup Instructions

### 1. Database Schema Updates

The API requires some database schema updates. Make sure your database has the following columns in the `locations` table:

```sql
ALTER TABLE locations ADD COLUMN description TEXT;
ALTER TABLE locations ADD COLUMN address VARCHAR(255);
ALTER TABLE locations ADD COLUMN latitude DECIMAL(10, 8);
ALTER TABLE locations ADD COLUMN longitude DECIMAL(11, 8);
```

### 2. File Structure

Ensure the following files are in place:

```
public/api/v1/
├── index.php          # Main API router
├── items.php          # Items API endpoints
├── categories.php     # Categories API endpoints
├── locations.php      # Locations API endpoints
├── analytics.php      # Analytics API endpoints
├── users.php          # Users API endpoints
└── auth.php           # Authentication API endpoints

app/Core/
├── ApiResponse.php    # API response handler
└── ...

app/Controllers/
├── ApiController.php  # Base API controller
└── ...
```

### 3. Testing the API

Run the test script to verify everything works:

```bash
php test_api.php
```

This will test all major API endpoints and verify they're working correctly.

### 4. Frontend Integration

Update your React frontend to use the new API endpoints. The API provides:

- Consistent JSON responses
- Proper HTTP status codes
- Pagination support
- Search and filtering
- Error handling

## Example Usage

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
    qty: 1,
    category_id: 1,
    location_id: 1
  })
});
```

### cURL

```bash
# Get all items
curl -X GET "http://localhost:8080/api/v1/items" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"

# Create new item
curl -X POST "http://localhost:8080/api/v1/items" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id" \
  -d '{
    "title": "New Item",
    "description": "Item description",
    "qty": 1
  }'
```

## Error Handling

All API endpoints return consistent error responses:

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

## HTTP Status Codes

- `200 OK` - Request successful
- `201 Created` - Resource created successfully
- `204 No Content` - Request successful, no content returned
- `400 Bad Request` - Invalid request data
- `401 Unauthorized` - Authentication required
- `403 Forbidden` - Access denied
- `404 Not Found` - Resource not found
- `405 Method Not Allowed` - HTTP method not allowed
- `409 Conflict` - Resource conflict
- `422 Unprocessable Entity` - Validation errors
- `500 Internal Server Error` - Server error

## Migration from Old API

The old API endpoints in `/public/api/` are still available for backward compatibility, but it's recommended to migrate to the new versioned API at `/api/v1/`.

## Support

For issues or questions about the API, refer to the main API documentation at `docs/API_DOCUMENTATION.md`.
