# Deployment

## Deployment Pipeline Overview

```
┌─────────────┐    ┌──────────────┐    ┌────────────────┐    ┌──────────────┐
│  Git Push   │───▶│  CI/CD Jobs  │───▶│  Docker Build  │───▶│ GHCR Registry│
│  to main    │    │  (Tests, QA) │    │  & Push Image  │    │              │
└─────────────┘    └──────────────┘    └────────────────┘    └──────────────┘
                           │                                          │
                           ▼                                          ▼
                    ✅ All Pass?                              Image: latest, main, <sha>
                           │                                          │
                           ▼                                          ▼
                   ┌──────────────┐                          ┌──────────────┐
                   │  Update      │                          │   Render     │
                   │  render.yaml │─────────────────────────▶│   Platform   │
                   └──────────────┘                          └──────────────┘
                         Manual Step                          Auto-deploys
```

## CI/CD Workflow

The deployment process is orchestrated through **GitHub Actions** with three distinct workflows:

### 1. **CI/CD Pipeline** (`.github/workflows/ci.yml`)

**Triggers**: Push or PR to `main` branch

**Jobs**:
- **test**: Runs PHPStan, PHP CS Fixer, PHPUnit, security audit, schema drift check
- **security**: Composer security audit
- **docker-build-push**: *(main branch only)* Builds and pushes Docker image to GHCR

**Image Tags**:
- `ghcr.io/wysselbie/api-demo:latest` (always latest from main)
- `ghcr.io/wysselbie/api-demo:main` (branch name)
- `ghcr.io/wysselbie/api-demo:<commit-sha>` (specific version)

### 2. **Quality Checks** (`.github/workflows/quality.yml`)

**Purpose**: Comprehensive quality analysis with coverage reporting

**Features**:
- Generates HTML and Clover coverage reports
- Uploads to Codecov for tracking
- Runs on push and PR

### 3. **Code Style** (`.github/workflows/code-style.yml`)

**Purpose**: Fast feedback on code formatting

**Features**:
- PHP CS Fixer dry-run
- Quick lint checks
- Parallel execution with main CI

## Deployment to Render

**Platform**: [Render](https://render.com) - Modern cloud platform  
**Region**: Frankfurt (eu-central)  
**Database**: Managed PostgreSQL 17  
**Infrastructure**: Defined in `render.yaml` (Infrastructure as Code)

### Deployment Process

**Step 1: Automated Image Build**
```bash
# Triggered automatically on push to main after all tests pass
# GitHub Actions builds and pushes:
# - ghcr.io/wysselbie/api-demo:latest
# - ghcr.io/wysselbie/api-demo:main
# - ghcr.io/wysselbie/api-demo:<sha>
```

**Step 2: Update IaC Configuration**
```bash
# Edit render.yaml with new image tag
# Update image URL (use specific SHA for rollback capability)
image:
  url: ghcr.io/wysselbie/api-demo:abc123def
```

**Step 3: Commit and Deploy**
```bash
git add render.yaml
git commit -m "deploy [skip ci]: Update to version abc123def"
git push origin main

# Render automatically detects changes and deploys
```

### Render Configuration

See [render.yaml](./render.yaml) for details.
Visit Render docs for more information: https://render.com/docs/infrastructure-as-code.

## Rollback Strategy

**Image-based rollback**:
```yaml
# Revert to previous version by updating render.yaml
image:
  url: ghcr.io/wysselbie/api-demo:<previous-sha>
  
# Commit and push - Render auto-deploys the previous version
```

**Database rollback**:
```bash
# If migration issues occur, run down migration
docker compose exec app php bin/console doctrine:migrations:migrate prev --no-interaction
```

## Production Checklist

When you are not sure, when and if you can deploying to production. Here is a list of things you can check:

- [ ] All tests pass (`make test`)
- [ ] Static analysis clean (`make phpstan`)
- [ ] Code style compliant (`make cs-check`)
- [ ] No security vulnerabilities (`make security-check`)
- [ ] Database migrations applied (`make db-migrate`)
- [ ] No schema drift (`doctrine:migrations:diff` returns empty)
- [ ] Environment variables configured in Render
- [ ] Health check endpoint responding
- [ ] Smoke tests on local environment

In general you can deploy when all GitHub Action Workflows are green from a commit.

