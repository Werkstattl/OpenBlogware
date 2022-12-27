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

clean: ## Cleans all dependencies
	rm -rf vendor
	rm -rf ./src/Resources/app/administration/node_modules/*
	rm -rf ./src/Resources/app/storefront/node_modules/*


build: ## Installs the plugin, and builds
	cd /var/www/html && php bin/console plugin:refresh
	cd /var/www/html && php bin/console plugin:install SasBlogModule --activate | true
	cd /var/www/html && php bin/console plugin:refresh
	cd /var/www/html && php bin/console theme:dump
	cd /var/www/html && PUPPETEER_SKIP_DOWNLOAD=1 ./bin/build-js.sh
	cd /var/www/html && php bin/console theme:refresh
	cd /var/www/html && php bin/console theme:compile
	cd /var/www/html && php bin/console theme:refresh

stan: ## Starts the PHPStan Analyser
	php ./vendor/bin/phpstan --memory-limit=1G analyse -c ./.phpstan.neon

ecs: ## Starts the ESC checker
	php ./vendor/bin/ecs check . --config easy-coding-standard.php

csfix: ## Starts the PHP CS Fixer, set [mode=fix] to auto fix
	php ./vendor/bin/ecs check src --config easy-coding-standard.php --fix

review: ## Review
	make stan -B
	make ecs -B