# Testing

## Testing Philosophy

This project follows a **pragmatic testing approach** balancing coverage with development speed:

- **Functional tests** for API contracts and integration
- **Unit tests** for complex business logic
- **Schema validation** to prevent migration drift
- **Static analysis** (PHPStan) to catch type errors

## Test Execution

| Command | Purpose | When to Use |
|---------|---------|-------------|
| `make test` | Run all tests (no coverage) | Quick feedback during development |
| `make test-coverage` | Run tests with coverage report | Before committing, reviewing coverage |
| `make coverage-report` | Open HTML coverage report | Analyzing test coverage gaps |
| `make db-test-setup` | Create test database | Initial test setup |

## Test Structure

```
tests/
├── Functional/              # Integration tests (HTTP requests)
│   └── ApiHealthCheckTest.php
├── Unit/                    # Unit tests (isolated logic)
└── bootstrap.php            # Test environment setup
```

## Running Tests

**Run all tests:**
```bash
make test
```

**Run with coverage:**
```bash
make test-coverage
make coverage-report  # Opens HTML report in browser
```

**Run specific test:**
```bash
php bin/phpunit tests/Functional/ApiHealthCheckTest.php
php bin/phpunit --filter testHealthCheck
```

## Testing Features

### **DAMA Doctrine Test Bundle**
- Wraps each test in a database transaction
- Automatic rollback after each test
- No need for manual database cleanup
- Faster test execution

### **Isolated Test Database**
- Dedicated `APP_ENV=test` environment
- Separate database schema
- No pollution of development data

### **Test Coverage with PCOV**
- Fast coverage collection (2-5x faster than Xdebug)
- HTML and Clover reports
- Coverage metrics per directory

## Code Quality Checks

| Tool | Purpose | Configuration | Level |
|------|---------|---------------|-------|
| **PHPStan** | Static analysis | `phpstan.neon` | Level 8 (strictest) |
| **PHP CS Fixer** | Code style | `.php-cs-fixer.dist.php` | Symfony standards |
| **PHPUnit** | Unit/Functional tests | `phpunit.dist.xml` | Coverage enabled |
| **Composer Audit** | Security vulnerabilities | N/A | Built-in |

**Run quality checks:**
```bash
make quality          # Run PHPStan + PHPUnit
make phpstan          # Static analysis only
make cs-check         # Check code style
make cs-fix           # Auto-fix code style
make security-check   # Check for vulnerabilities
make full-check       # Complete project validation
```
