# GitHub Actions CI/CD Setup

This project uses GitHub Actions for continuous integration and deployment.

## 🚀 Workflows

### 1. **CI/CD Pipeline** (`.github/workflows/ci.yml`)
Runs on every push and pull request to `main` branch.

**Testing & Quality Checks:**
- ✅ **PHPUnit Tests** - Full test suite execution
- 🔍 **PHPStan Analysis** - Static analysis at level 8
- 🎨 **PHP CS Fixer** - Code style checking
- 🔒 **Security Audit** - Composer security check
- 🗄️ **Schema Drift Detection** - Automatically detects missing migrations
- 🐘 **PostgreSQL 16 Setup** - Test database configuration

**Docker Build & Deploy** (only on `main` branch pushes):
- 🐳 **Builds Docker image** after all tests pass
- 🏷️ **Tags with git commit short SHA** (7 chars) - e.g., `b86647b`
- 📦 **Pushes to GitHub Container Registry** - `ghcr.io/wysselbie/api-demo`
- 🚀 **Creates `latest` tag** for main branch
- 💾 **Layer caching** for faster builds

**Image tags created:**
```
ghcr.io/wysselbie/api-demo:b86647b  # Git commit short SHA
ghcr.io/wysselbie/api-demo:latest   # Latest from main branch
```

### 2. **Quality Checks** (`.github/workflows/quality.yml`)
Comprehensive quality analysis with extended validation.

**Triggers:** Push and PR to `main` branch

**Features:**
- 🔄 **Full Project Check** - Runs `make full-check` (install + quality + security-check)
- 📊 **Coverage Reports** - Generates HTML and Clover XML coverage
- 📈 **Codecov Integration** - Uploads coverage to Codecov for tracking
- 🗄️ **Database Setup** - Creates test database for complete validation

### 3. **Code Style** (`.github/workflows/code-style.yml`)
Smart code style enforcement with different behavior for main branch vs PRs:

**Main Branch (Strict Mode):**
- ❌ **Fails the build** if code style issues are found
- Requires developers to run `make cs-fix` locally and commit fixes

**Pull Requests (Auto-fix Mode):**
- 🤖 **Automatically fixes** code style issues
- Commits fixes back to the PR branch with detailed messages
- Includes guidance for future prevention

Uses your project's `.php-cs-fixer.dist.php` configuration

## 🛠️ Local Development

All CI checks can be run locally using the project's Makefile:

```bash
# Run all quality checks
make full-check

# Individual checks
make test          # PHPUnit tests
make phpstan       # Static analysis
make cs-check      # Code style check
make cs-fix        # Fix code style issues
make security-check # Security audit
```

**Build Docker image locally:**
```bash
# Get current git SHA
GIT_SHA=$(git rev-parse --short=7 HEAD)

# Build and tag
docker build -t ghcr.io/wysselbie/api-demo:$GIT_SHA .

# Test locally
docker run -p 8080:80 ghcr.io/wysselbie/api-demo:$GIT_SHA
```

## 🔧 Configuration

### Environment Variables
The workflows use these environment configurations:
- `DATABASE_URL`: PostgreSQL connection for tests
- PHP 8.3 with extensions: `mbstring, xml, ctype, iconv, intl, pdo_pgsql, dom, filter, gd, json, pgsql`

### Caching
- **Composer dependencies** are cached to speed up builds
- **Docker layers** are cached using GitHub Actions cache
- Cache keys based on `composer.lock` hash

### Database
- Uses PostgreSQL 16 for testing
- Automatically sets up test database and runs migrations

## 📊 Coverage Reports

Coverage reports are generated in multiple formats:
- **HTML**: `var/coverage/index.html` (local development)
- **Clover XML**: `var/coverage/clover.xml` (CI integration)

## 🚫 Skip CI

To skip CI on specific commits, use:
```bash
git commit -m "Your commit message [skip ci]"
```

## 🎯 Quality Standards

This project maintains high quality standards with:
- **PHPStan Level 8** - Maximum static analysis
- **Symfony Coding Standards** - via PHP CS Fixer
- **100% Test Coverage Goal** - Comprehensive testing
- **Security First** - Regular dependency audits
