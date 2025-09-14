# React-PHP Integration Guide

This document explains how the React frontend is integrated with the PHP backend in the Storage Unit Management System.

## Architecture Overview

The system follows a **separation of concerns** pattern where:

- **PHP Backend**: Handles authentication, API endpoints, database operations, and serves the main application
- **React Frontend**: Provides interactive UI components for the analytics dashboard
- **Integration Layer**: PHP serves the React app as static files with proper authentication

## File Structure

```
storage-unit/
├── public/
│   ├── analytics.php          # PHP entry point with authentication
│   ├── api/
│   │   └── analytics.php      # API endpoint for analytics data
│   └── static/                # React build files
│       ├── css/
│       ├── js/
│       └── media/
├── react-frontend/            # React application
│   ├── src/
│   │   ├── components/
│   │   │   └── AnalyticsDashboard.tsx
│   │   ├── services/
│   │   │   └── api.ts
│   │   └── types/
│   │       └── index.ts
│   └── package.json
└── scripts/
    ├── build-react.sh         # Build and deploy script
    └── setup-react.sh         # Setup script
```

## How It Works

### 1. Authentication Flow

1. User visits `/analytics.php`
2. PHP checks if user is logged in using `isloggedIn()`
3. If not authenticated, redirects to `/signin.php`
4. If authenticated, serves the React app container

### 2. React App Loading

1. PHP serves a container div with id="root"
2. JavaScript dynamically loads the React app bundle
3. React app fetches data from `/api/analytics.php`
4. React renders the analytics dashboard

### 3. API Communication

- React app uses Axios for HTTP requests
- API endpoints return JSON with `{success: boolean, data: any, message?: string}` format
- CORS headers are properly configured
- Authentication is handled via PHP sessions

## Setup Instructions

### Prerequisites

- Node.js 16+ installed
- PHP 7.4+ with required extensions
- Composer for PHP dependencies

### 1. Setup React Frontend

```bash
# Run the setup script
./scripts/setup-react.sh

# Or manually:
cd react-frontend
npm install
```

### 2. Build and Deploy

```bash
# Build React app and copy to public directory
./scripts/build-react.sh

# Or manually:
cd react-frontend
npm run build
cd ..
cp -r react-frontend/build/* public/
```

### 3. Development Mode

For development, you can run both servers:

```bash
# Terminal 1: Start PHP backend
docker-compose up -d

# Terminal 2: Start React dev server
cd react-frontend
npm start
```

## API Endpoints

### GET /api/analytics.php

Returns analytics data for the dashboard.

**Response Format:**
```json
{
  "success": true,
  "data": {
    "total_items": 150,
    "total_quantity": 500,
    "items_by_category": [...],
    "items_by_location": [...],
    "recent_items": [...],
    "monthly_data": {...},
    "items_without_images": 10,
    "items_with_images": 140,
    "image_coverage": 93.3,
    "avg_quantity": 3.3
  }
}
```

## React Components

### AnalyticsDashboard

Main component that renders the analytics dashboard with:
- Key metrics cards
- Category and location charts
- Monthly trends chart
- Quick stats panel
- Recent items list

### API Service

Located in `react-frontend/src/services/api.ts`:
- Configured Axios instance
- Request/response interceptors
- Error handling
- TypeScript types

## Build Process

The build process is automated via `scripts/build-react.sh`:

1. **Install Dependencies**: Checks and installs npm packages
2. **Build React App**: Runs `npm run build`
3. **Create Directories**: Creates static asset directories
4. **Copy Files**: Copies built files to public directory
5. **Generate Manifest**: Creates `react-files.json` with actual file names

## File Manifest

The build process creates `public/react-files.json` with the actual hashed filenames:

```json
{
  "css": "main.f544adf9.css",
  "js": "main.b901a507.js"
}
```

This allows the PHP page to dynamically load the correct files.

## Development vs Production

### Development
- React dev server runs on port 3000
- Hot reloading enabled
- Source maps available
- API calls proxied to PHP backend

### Production
- React app built and served as static files
- Optimized bundles with hashed filenames
- No hot reloading
- Direct API calls to PHP backend

## Troubleshooting

### React App Not Loading

1. Check if build files exist in `public/static/`
2. Verify `react-files.json` exists and has correct filenames
3. Check browser console for errors
4. Ensure PHP backend is running

### API Errors

1. Check if user is authenticated
2. Verify API endpoint is accessible
3. Check CORS headers
4. Review PHP error logs

### Build Issues

1. Ensure Node.js 16+ is installed
2. Run `npm install` in react-frontend directory
3. Check for TypeScript errors
4. Verify all dependencies are installed

## Best Practices

1. **Separation of Concerns**: Keep PHP for backend logic, React for UI
2. **Type Safety**: Use TypeScript for React components
3. **Error Handling**: Implement proper error boundaries and API error handling
4. **Performance**: Use React.memo and useMemo for optimization
5. **Security**: Validate all API inputs on PHP side
6. **Testing**: Write unit tests for React components and API endpoints

## Future Improvements

1. **State Management**: Consider Redux or Zustand for complex state
2. **Caching**: Implement API response caching
3. **PWA**: Add service worker for offline functionality
4. **SSR**: Consider Next.js for server-side rendering
5. **Microservices**: Split API into separate microservices

## Related Documentation

- [Development Setup](DEVELOPMENT.md)
- [Docker Setup](DOCKER_SETUP_COMPLETE.md)
- [API Documentation](API.md)
- [Testing Guide](TESTING.md)
