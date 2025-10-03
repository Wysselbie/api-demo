# Deployment

This document describes the deployment process for this application.

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

## 🚀 Deployment Workflow

When you push to `main`:
1. **Tests run** - PHPUnit, PHPStan, CS Fixer, Security checks
2. **Docker image builds** - Tagged with git commit SHA
3. **Image pushed to GHCR** - `ghcr.io/wysselbie/api-demo:<git-sha>`
4. **Update Render** - Manually update `render.yaml` with the new image tag:
   ```yaml
   image:
     url: ghcr.io/wysselbie/api-demo:b86647b  # Use the git SHA from CI
   ```
5. **Deploy** - Commit the updated `render.yaml` to trigger Render deployment

**Get the latest image tag:**
```bash
# From your local commit
git rev-parse --short=7 HEAD

# Or check GitHub Actions output after CI completes
```

**Commit and Deploy**
```bash
git add render.yaml
git commit -m "deploy [skip ci]: Update to version $(git rev-parse --short=7 HEAD)"
git push origin main

# Render automatically detects changes and deploys
```

## Deployment to Render

**Platform**: [Render](https://render.com) - Modern cloud platform  
**Region**: Frankfurt (eu-central)  
**Database**: Managed PostgreSQL 17  
**Infrastructure**: Defined in `render.yaml` (Infrastructure as Code)

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

Currently not supported.

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

