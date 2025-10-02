# Symfony API Backend

A production-ready Symfony-based REST API application demonstrating modern PHP development practices, comprehensive quality assurance, and automated deployment workflows.

## Table of Contents

- [Overview](#overview)
- [Installation](./docs/installation.md)
- [Testing](./docs/testing.md)
- [Deployment](./docs/deployment.md)
- [Architecture & Design Decisions](#architecture--design-decisions)
- [Project Structure](#project-structure)
- [API Documentation](#api-documentation)
- [Troubleshooting](#troubleshooting)

## Overview

This project showcases a professional Symfony API backend with:

- **Modern PHP Stack**: Symfony 7.3, PHP 8.3, PostgreSQL 17
- **API-First Design**: API Platform with OpenAPI/Swagger documentation
- **Quality Assurance**: PHPStan Level 8, PHPUnit tests, PHP CS Fixer
- **CI/CD Pipeline**: Automated testing, Docker image builds, deployment to Render
- **Production-Ready**: Docker containerization, health checks, structured logging
- **Infrastructure as Code**: Render deployment configuration in `render.yaml`

---

## Installation

See [Installation](./docs/installation.md).

---

## Testing

See [Testing](./docs/testing.md).

---

## Deployment

See [Deployment](./docs/deployment.md).

---

## Architecture

### Technology Stack

#### **Symfony 7.3 with PHP 8.3**

Modern, enterprise-grade PHP framework. Latest versions since this is a fresh start.

#### **API Platform**
**Choice**: Automatic REST API generation with Hypermedia support  
**Rationale**: Dramatically reduces boilerplate code for CRUD operations. Auto-generates OpenAPI documentation, provides built-in validation, pagination, and filtering.  
**Trade-off**: Less control over API response structure (JSON-LD format), but gains in development speed and standardization are significant.  

#### **PostgreSQL 17**
**Choice**: Latest PostgreSQL version
**Rationale**: I learn something new, robust ACID compliance, advanced indexing, JSON support, and excellent performance. PostgreSQL 17 provides improved query optimization and better logical replication.
**Trade-off**: Requires PostgreSQL-specific knowledge compared to MySQL, but superior feature set justifies the choice.

#### **Layered Architecture**
```
┌─────────────────────────────────┐
│   API Platform (Presentation)   │  ← REST endpoints, OpenAPI docs
├─────────────────────────────────┤
│   Controllers (Application)     │  ← Business logic orchestration
├─────────────────────────────────┤
│   Entities & Repositories       │  ← Domain models, data access
├─────────────────────────────────┤
│   Doctrine ORM (Persistence)    │  ← Database abstraction
└─────────────────────────────────┘
```

### Infrastructure

#### **Docker Multi-Process Container**
**Choice**: Single image with nginx + PHP-FPM managed by Supervisor
**Rationale**: Simpler deployment, single image to manage, reduced orchestration complexity. Ready for Container Platform Render.com.
**Trade-off**: Less microservices-oriented, but for a monolithic API, single container deployment is more practical.
**Alternative Considered**: Separate nginx and PHP containers → Rejected due to deployment complexity for this scale. Like this it works out of the box.

#### **Base Image Strategy**
**Choice**: Custom base image (`ghcr.io/wysselbie/apiplatform-base:php8.3-1.0.0`)  
**Rationale**: Faster builds (pre-installed PHP extensions), consistent environment, reduced CI build time.  
**Trade-off**: Additional maintenance overhead for base image, but significant CI speed improvements. And adjustments does not happen that often.

#### **GitHub Container Registry (GHCR)**
**Choice**: GHCR over Docker Hub or private registries  
**Rationale**: Native GitHub integration, free for public repositories, same authentication as source code.  

### Testing Strategy

#### **DAMA Doctrine Test Bundle**
**Choice**: Automatic transaction rollback per test  
**Rationale**: Fast test execution (no database reset), isolated tests, no side effects between tests.  
**Trade-off**: Cannot test transaction-specific behavior, but gains in speed and simplicity are worth it.

#### **PCOV for Coverage**
**Choice**: PCOV instead of Xdebug  
**Rationale**: 2-5x faster coverage collection, purpose-built for coverage analysis.  
**Trade-off**: No debugging capabilities, but CI only needs coverage, not debugging.

### Quality Assurance Approach

#### **PHPStan Level 8**
**Choice**: Strictest static analysis level
**Rationale**: Catches bugs before runtime, enforces type safety, improves code quality.
**Trade-off**: More initial effort to satisfy type requirements, but code quality is important.

#### **Schema Drift Detection**
**Choice**: CI pipeline checks for un-migrated entity changes  
**Rationale**: Prevents production schema mismatches, enforces migration discipline.  
**Implementation**: `doctrine:migrations:diff` in CI fails if differences detected.

#### **Separate Test Database**
**Choice**: Dedicated test environment (`APP_ENV=test`)  
**Rationale**: Best practice. Isolates test data, prevents development data pollution.

### Deployment Architecture

#### **Manual Deployment with IaC (Render.com)**
**Choice**: GitOps-style deployment to Render.com by updating `render.yaml`  
**Rationale**: Explicit deployment control, infrastructure version-controlled, audit trail in git history. Render supports GitOps-style deployments for prebuilt images while others don't. Also Render.com can be used to spin up a database and other services. All with internal network access for security and better performance.
**Trade-off**: Manual step required (not fully automated), but provides human approval gate before production changes. Lock-in to Render.com, but all used services are for free at the moment.

---

## Project Structure

```
api-demo/
├── .github/
│   └── workflows/           # GitHub Actions CI/CD
├── config/
│   ├── packages/            # Symfony bundle configurations
│   ├── routes/              # Routing configuration
│   └── services.yaml        # Service container config
├── docker/                  # Docker configuration
├── src/
│   ├── ApiResource/         # API Platform resources
│   ├── Controller/
│   ├── Entity/              # Doctrine entities
│   ├── Repository/
│   └── Kernel.php           # Application kernel
├── tests/
│   ├── Functional/          # Integration/API tests
│   ├── Unit/                # Unit tests
│   ├── bootstrap.php        # Test environment setup
│   ├── console-application.php  # PHPStan helper
│   └── object-manager.php   # PHPStan Doctrine helper
├── var/
│   ├── cache/               # Symfony cache
│   ├── log/                 # Application logs
│   └── coverage/            # Test coverage reports
├── vendor/                  # Composer dependencies
├── Makefile                 # Development commands
└── render.yaml              # Render.com IaC config
```

---

## API Documentation

**OpenAPI Documentation**:
- Interactive API docs: `http://localhost:8080/api`
- OpenAPI JSON spec: `http://localhost:8080/api/docs.json`

---

## Make Commands Reference

| Command | Category | Description |
|---------|----------|-------------|
| `make help` | General | Show all available commands |
| `make install` | Setup | Install Composer dependencies |
| `make test` | Quality | Run PHPUnit tests (no coverage) |
| `make test-coverage` | Quality | Run tests with HTML coverage |
| `make coverage-report` | Quality | Open coverage report in browser |
| `make phpstan` | Quality | Run PHPStan static analysis |
| `make cs-check` | Quality | Check code style compliance |
| `make cs-fix` | Quality | Auto-fix code style issues |
| `make quality` | Quality | Run PHPStan + PHPUnit |
| `make full-check` | Quality | Complete project validation |
| `make security-check` | Security | Run Composer security audit |
| `make validate-schema` | Database | Validate Doctrine schema |
| `make db-setup` | Database | Create and migrate database |
| `make db-reset` | Database | Drop, recreate, migrate database |
| `make db-test-setup` | Database | Setup isolated test database |
| `make serve` | Development | Start PHP development server |
| `make clean` | Maintenance | Clear cache and temporary files |

---

## Troubleshooting

### Common Issues

**Docker Issues:**
1. **Port Already in Use**: Change ports in `compose.yaml` if 8080/5432 are occupied
2. **Build Failures**: Run `docker compose down` then `docker compose up --build`  
3. **Database Connection**: Ensure database service is healthy: `docker compose logs database`
4. **Permission Issues**: Reset containers with deleting volumes: `docker compose down -v && docker compose up --build`

**Local Development Issues:**
1. **Database Connection**: Check `DATABASE_URL` in `.env.local`
2. **Cache Issues**: Run `make cache-clear`
3. **Permission Issues**: Ensure `var/` directory is writable
4. **Missing Dependencies**: Run `make install`
5. **Code Coverage Error**: Install PCOV (`pecl install pcov`) or run `make test` instead of `make test-coverage`
6. **Install of PCOV not possible (macOS; missing pcre2.h)**: Link missing library `ln -s /opt/homebrew/opt/pcre2/include/pcre2.h /opt/homebrew/opt/php@8.4/include/php/ext/pcre/`

**CI/CD Issues:**
1. **Image Build Failures**: Check Docker build logs in GitHub Actions
3. **Schema Drift**: Run `php bin/console doctrine:migrations:diff` to generate migrations

---

## Contributing

### Development Workflow

1. Clone the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Run quality checks: `make full-check`
5. Commit your changes (`git commit -m 'Add amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

### Code Standards

- **PSR-12**: PHP code style standard
- **Symfony Conventions**: Follow Symfony best practices
- **PHPStan Level 8**: Strict type checking
- **100% Test Coverage**: For critical business logic
- **Meaningful Commits**: Use conventional commit messages

### Pull Request Checklist

- [ ] Code follows PSR-12 and Symfony standards (`make cs-check`)
- [ ] All tests pass (`make test`)
- [ ] PHPStan analysis clean (`make phpstan`)
- [ ] No security vulnerabilities (`make security-check`)
- [ ] Documentation updated (README, docblocks)
- [ ] Migration files included (if schema changes)
- [ ] Changelog updated (if applicable)

---

## License

This project is open source and available under the MIT License.
