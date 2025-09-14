#!/bin/bash

# Storage Unit Management System Setup Script
# ⚠️  DEPRECATED: This script is deprecated. Use Docker instead.

echo "⚠️  WARNING: This setup script is deprecated!"
echo ""
echo "The Storage Unit Management System now uses Docker-only deployment."
echo "Please use the Docker setup instead:"
echo ""
echo "🐳 Docker Setup (Recommended):"
echo "1. Install Docker Desktop: https://www.docker.com/products/docker-desktop"
echo "2. Start the application:"
echo "   docker-compose up -d"
echo "3. Run migrations:"
echo "   ./scripts/docker-migrate.sh"
echo "4. Access the application:"
echo "   - Web App: http://localhost:8080"
echo "   - phpMyAdmin: http://localhost:8081"
echo ""
echo "📚 For detailed setup instructions, see:"
echo "   - docs/QUICKSTART.md"
echo "   - docs/DOCKER_ONLY_SETUP.md"
echo ""
echo "🛑 This script will not perform any local setup."
echo "   All development should be done using Docker containers."
echo ""
exit 1