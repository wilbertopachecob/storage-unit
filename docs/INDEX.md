# Documentation Index

Welcome to the Storage Unit Management System documentation. This index provides an overview of all available documentation files.

## 📚 Documentation Files

### Setup & Installation
- **[QUICKSTART.md](QUICKSTART.md)** - Quick Docker setup guide for getting started
- **[DOCKER_ONLY_SETUP.md](DOCKER_ONLY_SETUP.md)** - Complete Docker-only setup guide
- **[DOCKER_SETUP_COMPLETE.md](DOCKER_SETUP_COMPLETE.md)** - Docker setup and configuration guide
- **[LOCAL_SETUP.md](LOCAL_SETUP.md)** - ⚠️ DEPRECATED - Local setup (use Docker instead)

### Development
- **[DEVELOPMENT.md](DEVELOPMENT.md)** - Development guidelines and best practices
- **[FILESYSTEM_IMPROVEMENTS.md](FILESYSTEM_IMPROVEMENTS.md)** - File structure and organization improvements
- **[DEBUG.md](DEBUG.md)** - Debugging guide, logging information, and troubleshooting

## 🚀 Quick Navigation

### For New Users
1. Start with [QUICKSTART.md](QUICKSTART.md) for a quick overview
2. Follow [DOCKER_ONLY_SETUP.md](DOCKER_ONLY_SETUP.md) for complete setup
3. Check [DEBUG.md](DEBUG.md) if you encounter issues

### For Developers
1. Read [DEVELOPMENT.md](DEVELOPMENT.md) for coding standards
2. Review [DOCKER_ONLY_SETUP.md](DOCKER_ONLY_SETUP.md) for Docker development
3. Use [DEBUG.md](DEBUG.md) for troubleshooting and logging

### For Docker Users
1. Follow [DOCKER_SETUP_COMPLETE.md](DOCKER_SETUP_COMPLETE.md) for containerized setup
2. Check [SETUP_SUMMARY.md](SETUP_SUMMARY.md) for configuration options

## 📁 File Structure

```
docs/
├── INDEX.md                      # This file - documentation index
├── QUICKSTART.md                 # Quick setup guide
├── LOCAL_SETUP.md               # Local development setup
├── DOCKER_SETUP_COMPLETE.md     # Docker setup guide
├── SETUP_SUMMARY.md             # Setup options summary
├── DEVELOPMENT.md               # Development guidelines
├── FILESYSTEM_IMPROVEMENTS.md   # Project structure guide
└── DEBUG.md                     # Debugging and logging guide
```

## 🔍 Finding Information

### Setup Issues
- Check [LOCAL_SETUP.md](LOCAL_SETUP.md) for step-by-step instructions
- Review [DOCKER_SETUP_COMPLETE.md](DOCKER_SETUP_COMPLETE.md) for Docker-specific issues
- See [DEBUG.md](DEBUG.md) for troubleshooting common problems

### Development Questions
- Read [DEVELOPMENT.md](DEVELOPMENT.md) for coding standards
- Check [FILESYSTEM_IMPROVEMENTS.md](FILESYSTEM_IMPROVEMENTS.md) for project organization
- Use [DEBUG.md](DEBUG.md) for logging and debugging information

### Logging & Debugging
- See [DEBUG.md](DEBUG.md) for comprehensive debugging guide
- Check log files in `storage/logs/` directory
- Review error messages and stack traces

## 📝 Contributing to Documentation

When adding new documentation:

1. Create a new `.md` file in this `docs/` directory
2. Update this `INDEX.md` file to include the new documentation
3. Follow the existing naming conventions (UPPERCASE.md)
4. Include proper headings and structure
5. Add cross-references to related documentation

## 🔗 Related Files

- **README.md** (root directory) - Main project overview and quick start
- **composer.json** - PHP dependencies and autoloading
- **docker-compose.yml** - Docker services configuration
- **phpunit.xml** - Testing configuration

---

**Note:** This documentation is maintained alongside the codebase. If you find outdated information, please update the relevant documentation file or create an issue.
