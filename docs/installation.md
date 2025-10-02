# Installation

I recommend using Docker for setting up the services (database and app) and installing binaries on your machine to use `make` outside of the container for convenience.

## Quick Start

### Prerequisites

| Requirement | Docker Setup | Local Development |
|------------|--------------|-------------------|
| **Docker** | ✅ Required | ❌ Not needed |
| **PHP 8.3+** | ✅ Included | ✅ Required |
| **Composer** | ✅ Included | ✅ Required |
| **PostgreSQL 17** | ✅ Included | ✅ Required |

### Installation

#### Docker Setup (Recommended)

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd api-demo
   ```

2. **Start with Docker Compose:**
   ```bash
   docker compose up --build
   ```

3. **Create and migrate database:**
   ```bash
   docker compose exec app make db-setup
   ```

4. **Access the application:**
   - API Interface: http://localhost:8080/api
   - OpenAPI Docs: http://localhost:8080/api/docs.json
   - Health Check: http://localhost:8080/api/health
   - Database: PostgreSQL 17 on localhost:5432

#### Local Development Setup

1. **Clone and install dependencies:**
   ```bash
   git clone <repository-url>
   cd api-demo
   make install
   ```

2. **Configure environment:**
   ```bash
   cp .env .env.local
   # Edit .env.local with your database configuration
   # Add APP_SECRET to .env.local - generate one with:
   # php -r "echo 'APP_SECRET=' . bin2hex(random_bytes(20)) . PHP_EOL;"
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
   - API Interface: http://localhost:8080/api
   - OpenAPI Docs: http://localhost:8080/api/docs.json

---

## Running the Application

### Development Commands

| Command | Description | Use Case |
|---------|-------------|----------|
| `make serve` | Start PHP development server | Local development |
| `docker compose up` | Start full stack with database | Full environment testing |
| `make db-setup` | Create and migrate database | Initial setup |
| `make db-reset` | Drop, recreate, and migrate database | Fresh start |
| `make cache-clear` | Clear Symfony cache | After config changes |

### Docker Development Workflow

```bash
# Start application stack
docker compose up --build    # Build and start (with logs)
docker compose up -d --build # Build and start (background)

# Manage services
docker compose down          # Stop all services
docker compose restart app  # Restart app service
docker compose logs app     # View app logs
docker compose logs -f      # Follow all logs

# Execute commands in container
docker compose exec app php bin/console cache:clear
docker compose exec app composer install
docker compose exec database psql -U app app  # Connect to database
```

### Environment Configuration

#### Required Environment Variables

| Variable | Description | Example |
|----------|-------------|---------|
| `DATABASE_URL` | PostgreSQL connection string | `postgresql://user:pass@host:5432/db` |
| `APP_SECRET` | Symfony application secret | Generate with `openssl rand -hex 20` |
| `APP_ENV` | Application environment | `dev`, `test`, `prod` |
| `CORS_ALLOW_ORIGIN` | CORS allowed origins (regex) | `^https?://(localhost\|127\.0\.0\.1)(:[0-9]+)?$` |

#### Development vs Production

**Development** (`.env.local`):
```bash
APP_ENV=dev
APP_DEBUG=1
DATABASE_URL="postgresql://app:password@127.0.0.1:5432/app?serverVersion=17"
```

**Production** (Render environment):
```bash
APP_ENV=prod
APP_DEBUG=0
DATABASE_URL="<injected-by-render>"
APP_SECRET="<secure-random-value>"
```
