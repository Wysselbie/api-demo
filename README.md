# Symfony API Backend

A professional Symfony-based REST API application built with modern development practices and comprehensive software quality tools.

## Features

- **Symfony 7.3** with **Symfony Flex** for streamlined package management
- **API Platform** for automatic REST API generation with OpenAPI documentation
- **Doctrine ORM** for database management
- **PHPStan** (Level 8) for static code analysis
- **PHPUnit** for comprehensive testing
- **PHP CS Fixer** for code style consistency
- **Sample Book API** demonstrating CRUD operations
- **Makefile** for easy development workflow

## 🚀 Quick Start

### Prerequisites

- PHP 8.4+
- Composer
- Database (SQLite, MySQL, PostgreSQL)

### Installation

1. **Clone and install dependencies:**
   ```bash
   git clone <repository-url>
   cd koro
   make install
   ```

2. **Configure environment:**
   ```bash
   cp .env .env.local
   # Edit .env.local with your database configuration
   ```

3. **Setup database:**
   ```bash
   make db-setup
   ```

4. **Start development server:**
   ```bash
   make serve
   ```

5. **Access API documentation:**
   - API Interface: http://localhost:8000/api
   - OpenAPI Docs: http://localhost:8000/api/docs.json

## 📁 Project Structure

```
├── config/             # Symfony configuration files
├── public/             # Web server document root
├── src/
│   ├── ApiResource/    # API Platform resources
│   ├── Controller/     # Symfony controllers
│   ├── Entity/         # Doctrine entities
│   └── Repository/     # Doctrine repositories
├── tests/
│   ├── Functional/     # Integration tests
│   └── Unit/          # Unit tests
├── var/               # Cache and logs
└── vendor/            # Composer dependencies
```

## 🛠️ Development Workflow

### Quality Assurance Commands

```bash
# Run all quality checks
make quality

# Individual checks
make test                # Run PHPUnit tests
make phpstan            # Run PHPStan static analysis
make cs-check           # Check coding standards
make cs-fix             # Fix coding standards

# Database operations
make db-create          # Create database
make db-migrate         # Run migrations
make db-reset           # Reset database

# Development server
make serve              # Start server (foreground)
make serve-bg           # Start server (background)
```

### Testing

The project includes comprehensive tests:

- **Unit Tests**: Test individual classes and methods
- **Functional Tests**: Test API endpoints and integration

```bash
# Run all tests
make test

# Run tests with coverage
make test-coverage
```

### Code Quality

**PHPStan Configuration:**
- Level 8 (strictest)
- Symfony-specific rules
- Custom ignore patterns for Doctrine

**PHP CS Fixer:**
- Symfony coding standards
- Automatic formatting

```bash
# Static analysis
make phpstan

# Code style
make cs-check
make cs-fix
```

## 📚 API Documentation

### Book API Endpoints

The sample Book API provides full CRUD operations:

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/books` | List all books |
| GET | `/api/books/{id}` | Get specific book |
| POST | `/api/books` | Create new book |
| PUT | `/api/books/{id}` | Update book |
| DELETE | `/api/books/{id}` | Delete book |

### Example Usage

**Create a Book:**
```bash
curl -X POST http://localhost:8000/api/books \
  -H "Content-Type: application/ld+json" \
  -d '{
    "title": "Clean Code",
    "author": "Robert C. Martin",
    "description": "A handbook of agile software craftsmanship",
    "isbn": "978-0132350884"
  }'
```

**Get All Books:**
```bash
curl http://localhost:8000/api/books
```

## 🏗️ Architecture

### API Platform Integration

- Automatic REST API generation
- JSON-LD and Hydra support
- OpenAPI (Swagger) documentation
- Built-in validation
- Flexible serialization

### Database Layer

- Doctrine ORM with annotations
- Entity validation with Symfony Validator
- Custom repository methods
- Automatic timestamps

### Testing Strategy

- **Unit Tests**: Entity behavior, business logic
- **Functional Tests**: HTTP endpoints, database integration
- **Database Isolation**: DAMA Doctrine Test Bundle automatically wraps each test in a transaction
- **Test Database**: Isolated test environment with automatic rollback
- **Symfony Test Client**: HTTP request simulation

## 🔧 Configuration

### Environment Variables

```bash
# Database
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"

# Application
APP_ENV=dev
APP_SECRET=your-secret-key
```

### PHPStan Configuration

Located in `phpstan.neon`:
- Strictest level (8)
- Symfony extension
- Custom bootstrap
- Doctrine compatibility

### PHPUnit Configuration

Located in `phpunit.dist.xml`:
- Separate test environment
- Code coverage support
- Symfony test extensions

## 🔐 Security

- Input validation with Symfony Validator
- CORS support via NelmioCorsBundle
- Security best practices
- Regular dependency updates with `composer audit`

## 📈 Performance

- Symfony caching system
- Doctrine query optimization
- Production-ready configuration
- OpCache support

## 🤝 Contributing

1. **Code Style**: Follow PSR-12 and Symfony standards
2. **Testing**: Maintain test coverage
3. **Static Analysis**: Ensure PHPStan level 8 compliance
4. **Documentation**: Update README for significant changes

## 📝 Available Make Commands

```bash
make help              # Show all available commands
make install           # Install dependencies
make test             # Run tests
make quality          # Run all quality checks
make db-setup         # Setup database
make serve            # Start development server
make clean            # Clean cache and temporary files
make full-check       # Complete project validation
```

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection**: Check `DATABASE_URL` in `.env.local`
2. **Cache Issues**: Run `make cache-clear`
3. **Permission Issues**: Ensure `var/` directory is writable
4. **Missing Dependencies**: Run `make install`

### Debug Mode

Set `APP_ENV=dev` in `.env.local` for detailed error messages.

## 📄 License

This project is open source. Please check the LICENSE file for details.

## 🙏 Acknowledgments

- **Symfony** - The PHP framework for web artisans
- **API Platform** - REST and GraphQL API framework
- **PHPStan** - PHP static analysis tool
- **PHPUnit** - PHP testing framework
- **Doctrine** - PHP Object Relational Mapper
