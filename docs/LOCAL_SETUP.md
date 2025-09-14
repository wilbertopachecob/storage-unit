# Local Development Setup (DEPRECATED)

> **⚠️ WARNING: This setup method is deprecated and no longer supported.**
> 
> The Storage Unit Management System now uses **Docker-only** deployment for consistency and reliability.

## 🐳 Use Docker Instead

Please use the Docker setup instead:

1. **Quick Start**: See [QUICKSTART.md](QUICKSTART.md)
2. **Docker Setup**: See [DOCKER_SETUP_COMPLETE.md](DOCKER_SETUP_COMPLETE.md)
3. **Main README**: See [README.md](../README.md)

## Why Docker-Only?

- **Consistency**: Same environment for all developers
- **Reliability**: No local configuration issues
- **Simplicity**: One-command setup
- **Isolation**: No conflicts with local PHP/MySQL versions
- **Production-like**: Matches production environment

## Migration from Local Setup

If you were using local setup:

1. **Stop local services** (Apache, MySQL, PHP)
2. **Install Docker Desktop**
3. **Follow Docker setup** in [QUICKSTART.md](QUICKSTART.md)
4. **Your data will be preserved** in Docker volumes

## Need Help?

- Check [QUICKSTART.md](QUICKSTART.md) for quick setup
- Review [DOCKER_SETUP_COMPLETE.md](DOCKER_SETUP_COMPLETE.md) for detailed configuration
- See [DEBUG.md](DEBUG.md) for troubleshooting