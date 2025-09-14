# React Frontend Setup Guide

This guide explains how to set up and run the React frontend for the Storage Unit Management System.

## 🎯 Overview

The React frontend is a proof of concept that demonstrates how to integrate a modern React application with the existing PHP backend. It focuses on the analytics dashboard feature as the simplest and most visual component.

## 📋 Prerequisites

- **Node.js 16+**: Download from [nodejs.org](https://nodejs.org/)
- **PHP Backend**: Running via Docker (see main README)
- **Git**: For cloning the repository

## 🚀 Quick Setup

### 1. Start the PHP Backend

```bash
# Start Docker containers
docker-compose up -d

# Run database migrations
./scripts/docker-migrate.sh

# Verify backend is running
curl http://localhost:8080
```

### 2. Setup React Frontend

```bash
# Run the automated setup script
./scripts/setup-react.sh
```

This script will:
- Check Node.js version
- Install dependencies
- Create environment configuration
- Set up the development environment

### 3. Start Development

```bash
# Start React development server
./scripts/start-react.sh
```

Or manually:
```bash
cd react-frontend
npm start
```

### 4. Access the Application

- **React App**: http://localhost:3000
- **PHP Backend**: http://localhost:8080
- **API Endpoints**: http://localhost:8080/api/

## 🏗️ Project Structure

```
react-frontend/
├── public/
│   ├── index.html          # Main HTML template
│   └── manifest.json       # PWA manifest
├── src/
│   ├── components/         # React components
│   │   ├── AnalyticsDashboard.js    # Main dashboard
│   │   ├── AnalyticsDashboard.css   # Dashboard styles
│   │   ├── MetricCard.js            # Metric display cards
│   │   ├── MetricCard.css           # Card styles
│   │   ├── QuickStats.js            # Quick statistics
│   │   ├── QuickStats.css           # Stats styles
│   │   ├── RecentItems.js           # Recent items display
│   │   └── RecentItems.css          # Items styles
│   ├── services/
│   │   └── api.js          # API service layer
│   ├── App.js              # Main app component
│   ├── App.css             # App styles
│   ├── index.js            # Entry point
│   └── index.css           # Global styles
├── package.json            # Dependencies and scripts
└── .env                    # Environment variables
```

## 🔧 Available Scripts

### Development Scripts

```bash
npm start                   # Start development server
npm run dev                # Start with API URL configured
npm test                   # Run tests
npm run eject              # Eject from Create React App
```

### Production Scripts

```bash
npm run build              # Build for production
npm run build:prod         # Build with production API URL
```

### Custom Scripts

```bash
# Setup (from project root)
./scripts/setup-react.sh   # Initial setup
./scripts/start-react.sh   # Start development
./scripts/build-react.sh   # Build for production
```

## 🌐 API Integration

The React app communicates with the PHP backend through REST API endpoints:

### Available Endpoints

- `GET /api/analytics` - Get analytics dashboard data
- `GET /api/items` - Get all items for the user
- `POST /api/items` - Create a new item
- `GET /api/categories` - Get categories
- `GET /api/locations` - Get locations

### API Service

The `src/services/api.js` file handles all API communication:

```javascript
import { analyticsAPI } from '../services/api';

// Get analytics data
const data = await analyticsAPI.getAnalytics();
```

### Error Handling

The API service includes:
- Request/response interceptors
- Error handling
- Authentication token management
- CORS support

## 🎨 Features Implemented

### Analytics Dashboard

- **Key Metrics Cards**: Total items, quantity, categories, locations
- **Interactive Charts**: 
  - Doughnut chart for category distribution
  - Bar chart for location analysis
  - Line chart for time series data
- **Quick Statistics**: Image coverage, average quantity
- **Recent Items**: Display of latest added items

### UI/UX Features

- **Responsive Design**: Mobile-friendly interface
- **Modern Styling**: Bootstrap 5 with custom CSS
- **Loading States**: Spinner and loading indicators
- **Error Handling**: User-friendly error messages
- **Interactive Elements**: Hover effects and animations

## 🔧 Configuration

### Environment Variables

Create a `.env` file in the `react-frontend` directory:

```env
# API Configuration
REACT_APP_API_URL=http://localhost:8080/api

# Development Settings
GENERATE_SOURCEMAP=false
```

### API URL Configuration

The API URL can be configured for different environments:

- **Development**: `http://localhost:8080/api`
- **Production**: `https://yourdomain.com/api`

## 🚀 Deployment

### Development Deployment

1. Start the PHP backend
2. Start the React development server
3. Access via http://localhost:3000

### Production Deployment

1. **Build the React App**
   ```bash
   ./scripts/build-react.sh
   ```

2. **Deploy Files**
   - Copy contents of `react-frontend/build/` to your web server
   - Configure web server to serve the React app
   - Update API URL for production

3. **Configure Backend**
   - Ensure CORS headers are set
   - Update API endpoints for production domain
   - Configure authentication

## 🐛 Troubleshooting

### Common Issues

#### React App Won't Start

```bash
# Clear cache and reinstall
cd react-frontend
rm -rf node_modules package-lock.json
npm install
npm start
```

#### API Connection Issues

```bash
# Test API endpoints
curl http://localhost:8080/api/analytics

# Check PHP backend logs
docker-compose logs web
```

#### Port Already in Use

```bash
# Kill process on port 3000
lsof -ti:3000 | xargs kill -9

# Or use different port
PORT=3001 npm start
```

#### CORS Issues

Ensure the PHP API endpoints include proper CORS headers:

```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
```

### Debug Mode

Enable debug mode by setting:

```env
REACT_APP_DEBUG=true
```

## 📊 Performance Optimization

### Code Splitting

The app uses React's built-in code splitting:

```javascript
const LazyComponent = React.lazy(() => import('./LazyComponent'));
```

### Bundle Analysis

Analyze bundle size:

```bash
npm install --save-dev webpack-bundle-analyzer
npm run build
npx webpack-bundle-analyzer build/static/js/*.js
```

### Production Optimizations

- Minified JavaScript and CSS
- Optimized images
- Gzip compression
- CDN for static assets

## 🔄 Development Workflow

### Making Changes

1. **Backend Changes**: Modify PHP controllers and models
2. **API Updates**: Update API endpoints as needed
3. **Frontend Changes**: Modify React components
4. **Testing**: Test both backend and frontend integration
5. **Deployment**: Build and deploy both applications

### Git Workflow

```bash
# Create feature branch
git checkout -b feature/react-analytics

# Make changes
# ... modify files ...

# Commit changes
git add .
git commit -m "Add React analytics dashboard"

# Push to remote
git push origin feature/react-analytics
```

## 📚 Learning Resources

- [React Documentation](https://reactjs.org/docs)
- [Create React App](https://create-react-app.dev/)
- [Chart.js Documentation](https://www.chartjs.org/docs/)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.0/)
- [Axios Documentation](https://axios-http.com/docs/intro)

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📞 Support

For issues and questions:
- Check the troubleshooting section
- Review the main README
- Open an issue on GitHub
