SHELL := /bin/bash
#
# Makefile
#

.PHONY: help
.DEFAULT_GOAL := help

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

install: ## Installs all production dependencies
	@composer validate
	@composer install --no-dev
	cd ./src/Resources/app/administration/ && npm install --production

dev: ## Installs all dev dependencies
	@composer validate
	@composer install
	cd ./src/Resources/app/administration/ && npm install

core: ## Installs all core dependencies
	cd vendor/shopware/administration/Resources/app/administration && npm install
	cd vendor/shopware/storefront/Resources/app/storefront && npm install

clean: ## Cleans all dependencies
	rm -rf vendor
	rm -rf ./src/Resources/app/administration/node_modules
	rm -rf ./src/Resources/app/storefront/node_modules
	rm -rf ./src/Resources/app/administration/package-lock.json

admin: ## Installs all admin dependencies
	cd vendor/shopware/administration/Resources/app/administration && npm install

# ------------------------------------------------------------------------------------------------------------

build: ## Installs the plugin, and builds
	cd /var/www/html && php bin/console plugin:refresh
	cd /var/www/html && php bin/console plugin:install SasBlogModule --activate | true
	cd /var/www/html && php bin/console plugin:refresh
	cd /var/www/html && php bin/console theme:dump
	cd /var/www/html && PUPPETEER_SKIP_DOWNLOAD=1 ./bin/build-js.sh
	cd /var/www/html && php bin/console theme:refresh
	cd /var/www/html && php bin/console theme:compile
	cd /var/www/html && php bin/console theme:refresh

phpunit: ## Starts all PHPUnit Tests
	@XDEBUG_MODE=coverage php vendor/bin/phpunit --configuration=phpunit.xml --coverage-html ../../../public/.reports/blogmodule/coverage


infection: ## Starts all Infection/Mutation tests
	@XDEBUG_MODE=coverage php vendor/bin/infection --configuration=./.infection.json

eslint: ## Starts the ESLinter
	cd ./src/Resources/app/administration && ./node_modules/.bin/eslint --config ./.eslintrc.json ./src

stan: ## Starts the PHPStan Analyser
	php ./vendor/bin/phpstan --memory-limit=1G analyse -c ./.phpstan.neon

ecs: ## Starts the ESC checker
	php ./vendor/bin/ecs check . --config easy-coding-standard.php

phpcheck: ## Starts the PHP syntax checks
	@find . -name '*.php' -not -path "./vendor/*" -not -path "./tests/*" | xargs -n 1 -P4 php -l

csfix: ## Starts the PHP CS Fixer, set [mode=fix] to auto fix
	php ./vendor/bin/ecs check src --config easy-coding-standard.php --fix

review: ## Review
	@make phpcheck -B
	@make phpunit -B
	@make infection -B
	@make stan -B
	@make ecs -B
	@make eslint -B
