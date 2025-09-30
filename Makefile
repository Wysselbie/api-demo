# Symfony API Project Makefile
.PHONY: help install test phpstan cs-check cs-fix quality db-create db-migrate serve clean

# Colors for output
BLUE=\033[0;34m
GREEN=\033[0;32m
YELLOW=\033[1;33m
RED=\033[0;31m
NC=\033[0m # No Color

help: ## Show this help message
	@echo "$(BLUE)Symfony API Project Commands$(NC)"
	@echo ""
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "$(GREEN)%-20s$(NC) %s\n", $$1, $$2}' $(MAKEFILE_LIST)

install: ## Install dependencies
	@echo "$(BLUE)Installing dependencies...$(NC)"
	composer install --optimize-autoloader

test: ## Run PHPUnit tests
	@echo "$(BLUE)Running PHPUnit tests...$(NC)"
	php bin/phpunit

test-coverage: ## Run PHPUnit tests with coverage
	@echo "$(BLUE)Running PHPUnit tests with coverage...$(NC)"
	php bin/phpunit --coverage-html var/coverage

coverage-report: ## Open coverage report in browser
	@echo "$(BLUE)Opening coverage report...$(NC)"
	open var/coverage/index.html

phpstan: ## Run PHPStan static analysis
	@echo "$(BLUE)Running PHPStan static analysis...$(NC)"
	vendor/bin/phpstan analyse --memory-limit 1G

phpstan-baseline: ## Generate PHPStan baseline
	@echo "$(BLUE)Generating PHPStan baseline...$(NC)"
	vendor/bin/phpstan analyse --generate-baseline --memory-limit 1G

cs-check: ## Check coding standards with PHP CS Fixer
	@echo "$(BLUE)Checking coding standards...$(NC)"
	@if [ -f vendor/bin/php-cs-fixer ]; then \
		vendor/bin/php-cs-fixer fix --dry-run --diff; \
	else \
		echo "$(YELLOW)PHP CS Fixer not installed. Run: composer require --dev friendsofphp/php-cs-fixer$(NC)"; \
	fi

cs-fix: ## Fix coding standards with PHP CS Fixer
	@echo "$(BLUE)Fixing coding standards...$(NC)"
	@if [ -f vendor/bin/php-cs-fixer ]; then \
		vendor/bin/php-cs-fixer fix; \
	else \
		echo "$(YELLOW)PHP CS Fixer not installed. Run: composer require --dev friendsofphp/php-cs-fixer$(NC)"; \
	fi

quality: phpstan test ## Run all quality checks (PHPStan + PHPUnit)
	@echo "$(GREEN)All quality checks completed!$(NC)"

db-create: ## Create database
	@echo "$(BLUE)Creating database...$(NC)"
	php bin/console doctrine:database:create --if-not-exists

db-migrate: ## Run database migrations
	@echo "$(BLUE)Running database migrations...$(NC)"
	php bin/console doctrine:migrations:migrate --no-interaction

db-setup: db-create db-migrate ## Setup database (create + migrate)
	@echo "$(GREEN)Database setup completed!$(NC)"

db-reset: ## Reset database (drop, create, migrate)
	@echo "$(BLUE)Resetting database...$(NC)"
	php bin/console doctrine:database:drop --force --if-exists
	php bin/console doctrine:database:create
	php bin/console doctrine:migrations:migrate --no-interaction
	@echo "$(GREEN)Database reset completed!$(NC)"

db-test-setup: ## Setup test database
	@echo "$(BLUE)Setting up test database...$(NC)"
	php bin/console doctrine:database:drop --env=test --force --if-exists
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:migrations:migrate --env=test --no-interaction
	@echo "$(GREEN)Test database setup completed!$(NC)"

db-test-reset: ## Reset test database with fresh data
	@echo "$(BLUE)Resetting test database...$(NC)"
	php bin/console doctrine:database:drop --env=test --force --if-exists
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:migrations:migrate --env=test --no-interaction
	@echo "$(GREEN)Test database reset completed!$(NC)"

serve: ## Start development server
	@echo "$(BLUE)Starting Symfony development server...$(NC)"
	php -S localhost:8000 -t public

serve-bg: ## Start development server in background
	@echo "$(BLUE)Starting Symfony development server in background...$(NC)"
	php -S localhost:8000 -t public > /dev/null 2>&1 &
	@echo "$(GREEN)Server started at http://localhost:8000$(NC)"

clean: ## Clean cache and temporary files
	@echo "$(BLUE)Cleaning cache and temporary files...$(NC)"
	rm -rf var/cache/*
	rm -rf var/log/*
	rm -rf .phpunit.cache
	@echo "$(GREEN)Cleanup completed!$(NC)"

cache-clear: ## Clear Symfony cache
	@echo "$(BLUE)Clearing Symfony cache...$(NC)"
	php bin/console cache:clear

cache-warmup: ## Warm up Symfony cache
	@echo "$(BLUE)Warming up Symfony cache...$(NC)"
	php bin/console cache:warmup

requirements-check: ## Check Symfony requirements
	@echo "$(BLUE)Checking Symfony requirements...$(NC)"
	@if [ -f vendor/bin/requirements-checker ]; then \
		vendor/bin/requirements-checker; \
	else \
		echo "$(YELLOW)Requirements checker not available$(NC)"; \
	fi

security-check: ## Run security check
	@echo "$(BLUE)Running security check...$(NC)"
	composer audit

validate-schema: ## Validate Doctrine schema
	@echo "$(BLUE)Validating Doctrine schema...$(NC)"
	php bin/console doctrine:schema:validate

api-docs: ## Show API documentation URL
	@echo "$(GREEN)API Documentation available at:$(NC)"
	@echo "  $(BLUE)http://localhost:8000/api$(NC) (API Platform interface)"
	@echo "  $(BLUE)http://localhost:8000/api/docs.json$(NC) (OpenAPI JSON)"

full-check: install quality security-check ## Run complete project check
	@echo "$(GREEN)Full project check completed!$(NC)"
