# Makefile
#
# Add custom commands to Makefile.custom

# Mute all `make` specific output. Comment this out to get some debug information.
.SILENT:

# make commands be run with `bash` instead of the default `sh`
SHELL='/bin/bash'

# Setup —————————————————————————————————————————————————————————————————————————
PROJECT  = shell-b2b
DOCKER_COMPOSE = docker-compose
EXEC_PHP = $(DOCKER_COMPOSE) run --rm php-fpm php
SYMFONY  = $(EXEC_PHP) bin/console
COMPOSER = $(EXEC_PHP) composer.phar
DB_NAME = shell_b2b
DB_USER = shell_b2b

DOCKER_COMPOSE_TEST = docker-compose -f docker-compose.yml -f docker-compose.test.yml
EXEC_PHP_TEST = $(DOCKER_COMPOSE_TEST) run --rm php-fpm php

.DEFAULT_GOAL := help

## —— 🐝  Project Make file  🐝  —————————————————————————————————————————————

# .DEFAULT: If command does not exist in this makefile
# default:  If no command was specified:
.DEFAULT default:
	if [ "$@" != "" ]; then echo "Command '$@' not found."; fi;
	$(MAKE) help                        # goes to the main Makefile
	$(MAKE) -f Makefile.custom help     # goes to this Makefile
	if [ "$@" != "" ]; then exit 2; fi;

help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
	@echo "Available custom commands:"
	@grep '^[^#[:space:]].*:' Makefile.custom | grep -v '^help' | grep -v '^default' | grep -v '^\.' | grep -v '=' | grep -v '^_' | sed 's/://' | xargs -n 1 echo ' -'

########################################################################################################################

## —— Composer —————————————————————————————————————————————————————————————————
install: ## Install vendors according to the current composer.lock file
	$(COMPOSER) install --no-scripts

## —— Symfony ——————————————————————————————————————————————————————————————————
sf: ## List Symfony commands
	$(SYMFONY)

cc: ## Clear cache
	$(SYMFONY) cache:clear --no-warmup
	$(SYMFONY) cache:warmup

warmup: ## Warmump the cache
	$(SYMFONY) cache:warmup

fix-perms: ## Fix permissions of all var files
	chmod -R 777 var/*

assets: ## Install the assets with symlinks in the public folder (web)
	$(SYMFONY) assets:install public/ --symlink  --relative

purge: ## Purge cache and logs
	rm -rf var/cache/* var/logs/*

## —— Project ——————————————————————————————————————————————————————————————————
project-init: up install migrations load-fixtures cc npm-build-front import-fixtures ## Init local project

commands: ## Display all project specific commands
	$(SYMFONY) list $(PROJECT)

migrations: ## Load Doctrine migrations
	$(SYMFONY) doctrine:migrations:migrate -n

load-fixtures: ## Build the db, control the schema validity, load fixtures and check the migration status
	$(SYMFONY) doctrine:cache:clear-metadata
	$(SYMFONY) doctrine:database:create --if-not-exists
	$(SYMFONY) doctrine:schema:drop --force
	$(SYMFONY) doctrine:schema:create
	$(SYMFONY) doctrine:schema:validate
	$(SYMFONY) doctrine:fixtures:load -n
	$(SYMFONY) doctrine:migration:status
	$(DOCKER_COMPOSE) run --rm php-fpm /bin/sh -c "mkdir -p storage"
	$(DOCKER_COMPOSE) run --rm php-fpm /bin/sh -c "mkdir -p storage/source"
	$(DOCKER_COMPOSE) run --rm php-fpm /bin/sh -c "mkdir -p storage/source/tests"
	$(DOCKER_COMPOSE) run --rm php-fpm /bin/sh -c "cp -r tests/_data/images/* storage/source/tests"

import-fixtures: ##
	$(DOCKER_COMPOSE) run --rm php-fpm /bin/sh -c "\
		mkdir -p storage \
		&& cp -r tests/_data/import/sync/ storage/ \
	"
	$(SYMFONY) import:1c

translate: ## Update project translations
	$(SYMFONY) translation:update --domain=frontend --clean --force --prefix="" --no-debud uk
	$(SYMFONY) translation:update --domain=frontend --clean --force --prefix="" --no-debud en
	$(SYMFONY) translation:parse-json --file=public/locales/uk.json -q
	$(SYMFONY) translation:extract --hide-errors

db-restore: ## pg_restore --clean --if-exists -v -d $(DB_USER) -O -U $(DB_NAME)
	$(DOCKER_COMPOSE) exec -T postgresql pg_restore --clean --if-exists -v -d $(DB_USER) -O -U $(DB_NAME) docker-entrypoint-initdb.d/$(DB_USER).dump

## —— Code analyzes —————————————————————————————————————————————————————————————
analyze: ## Launch static analysis
	$(EXEC_PHP) ./vendor/bin/phpstan analyze -l 4 -c phpstan.neon

insights: ## Launch check code phpinsights https://phpinsights.com
	$(EXEC_PHP) ./vendor/bin/phpinsights

cs: ## Launch check code style
	$(EXEC_PHP) ./vendor/bin/phpcs --standard=phpcs.xml -n -p src/
cs-fix: ## Fix code style by phpcbf
	$(EXEC_PHP) ./vendor/bin/phpcbf --standard=phpcs.xml.dist -p src/*

## —— Encore Yarn ——————————————————————————————————————————————————————————————
yarn-install: ## Install backend npm modules
	$(DOCKER_COMPOSE) run --workdir="/var/www/assets/backend" --rm node yarn install

yarn-build: ## Build production backend scripts
	$(DOCKER_COMPOSE) run --workdir="/var/www/assets/backend" --rm node yarn dev

yarn-watch: ## Watch
	$(DOCKER_COMPOSE) run --workdir="/var/www/assets/backend" --rm node yarn watch

npm-build-front:
	$(DOCKER_COMPOSE) run --workdir="/var/www" --rm node npm install
	$(DOCKER_COMPOSE) run --workdir="/var/www" --rm node npm run build

## —— Generate JWT token ——————————————————————————————————————————————————————————————
generate-jwt: ## Generate the public and private keys used for signing JWT tokens
	$(DOCKER_COMPOSE) run --rm php-fpm /bin/sh -c "set -e \
	&& apk add openssl \
	&& mkdir -p config/jwt \
	&& jwt_passhrase=$(grep ''^JWT_PASSPHRASE='' .env | cut -f 2 -d ''='') \
	&& echo "$jwt_passhrase" | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 \
	&& echo "$jwt_passhrase" | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout \
	"

## —— Tests ——————————————————————————————————————————————————————————————————
test: phpunit.xml.dist ## Launch all unit tests
	$(EXEC_PHP) ./vendor/bin/phpunit -v --stderr --colors=never

codecept: ## Run all codeception test
	$(DOCKER_COMPOSE_TEST) up -d --build
	rm -rf tests/_output/* ^.gitignore
	rm -rf var/logs/*
	$(EXEC_PHP_TEST) bin/console doctrine:schema:drop --force
	$(EXEC_PHP_TEST) bin/console doctrine:schema:update --force
	$(EXEC_PHP_TEST) ./vendor/bin/codecept run api --steps --fail-fast

## —— Pre-Commit ——————————————————————————————————————————————————————————————————
pre-commit: analyze test cs-fix
	$(COMPOSER) validate

## —— Update project ——————————————————————————————————————————————————————————————————————
update: ## Update project
	$(COMPOSER) install --no-scripts
	$(SYMFONY) doctrine:migrations:migrate -n
	$(SYMFONY) cache:clear --no-warmup
	$(SYMFONY) cache:warmup
	$(DOCKER_COMPOSE) run --workdir="/var/www" --rm node npm install
	$(DOCKER_COMPOSE) run --workdir="/var/www" --rm node npm run build

## —— Docker (Local environment) ——————————————————————————————————————————————————————————————————————
up: ## Run docker-compose for local project
	$(DOCKER_COMPOSE) up -d --build
	@echo -e '\nGo to link: http://localhost:8088'

down: ## Stop docker-compose
	$(DOCKER_COMPOSE) down -v --remove-orphans

restart: ## Restart docker-compose
	$(DOCKER_COMPOSE) restart

php-bash: ## Enter to php container bash
	$(DOCKER_COMPOSE) exec php-fpm bash
