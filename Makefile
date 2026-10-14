.DEFAULT_GOAL := help
.PHONY: help build up down restart rebuild ps logs logs-app logs-web bash bash-web spark routes cache-clear composer-install composer-update test lint dev-serve build-old up-old down-old

ENV_FILE := .env.docker
COMPOSE  := docker compose --env-file $(ENV_FILE)
# Default fallback variables untuk docker-compose 1.8.0
UID ?= $(shell id -u 2>/dev/null || echo 1000)
GID ?= $(shell id -g 2>/dev/null || echo 1000)
APP_PORT ?= 84
FOTOWM_PATH ?= ./fotowm

# docker-compose 1.8.0 tidak mendukung flag CLI --env-file dan sintaks ${VAR:-default}.
# Injeksi default fallbacks dan variabel dari ENV_FILE via 'env' agar selalu terdefinisi lengkap.
ENV_DEFAULTS_OLD := UID=$(UID) GID=$(GID) APP_PORT=$(APP_PORT) FOTOWM_PATH=$(FOTOWM_PATH)
ENV_VARS_OLD     := $(shell [ -f $(ENV_FILE) ] && grep -E '^[A-Za-z_][A-Za-z0-9_]*=' $(ENV_FILE) | sed -e 's/[[:space:]]*\#.*//' 2>/dev/null)
COMPOSE_OLD      := env $(ENV_DEFAULTS_OLD) $(ENV_VARS_OLD) docker-compose -f docker-compose-old.yml


# Colors for terminal output
COLOR_RESET   := \033[0m
COLOR_TITLE   := \033[1;36m
COLOR_CMD     := \033[1;32m
COLOR_COMMENT := \033[0;33m

help: ## Tampilkan daftar perintah Makefile yang tersedia
	@echo ""
	@echo "$(COLOR_TITLE)========================================================$(COLOR_RESET)"
	@echo "$(COLOR_TITLE)  CodeIgniter 4 (cater_web_ci4) - Docker Management Tool$(COLOR_RESET)"
	@echo "$(COLOR_TITLE)========================================================$(COLOR_RESET)"
	@echo ""
	@echo "$(COLOR_COMMENT)Usage:$(COLOR_RESET) make $(COLOR_CMD)<target>$(COLOR_RESET)"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(COLOR_CMD)%-18s$(COLOR_RESET) %s\n", $$1, $$2}'
	@echo ""

## --- DOCKER LIFECYCLE ---
build: ## Build image container Docker
	$(COMPOSE) build

up: ## Jalankan container di background (port 84)
	$(COMPOSE) up -d

down: ## Hentikan dan hapus container
	$(COMPOSE) down

build-old: ## Build image container Docker menggunakan docker-compose-old.yml (Compose 1.8.0)
	$(COMPOSE_OLD) build

up-old: ## Jalankan container di background menggunakan docker-compose-old.yml (Compose 1.8.0)
	$(COMPOSE_OLD) up -d

down-old: ## Hentikan dan hapus container menggunakan docker-compose-old.yml (Compose 1.8.0)
	$(COMPOSE_OLD) down

restart: ## Restart container
	$(COMPOSE) restart

rebuild: ## Hentikan, refresh volume app_code, dan build ulang container dari awal
	$(COMPOSE) down
	docker volume rm cater_web_ci4_app_code 2>/dev/null || true
	$(COMPOSE) up -d --build

ps: ## Lihat status container yang sedang berjalan
	$(COMPOSE) ps

logs: ## Pantau live logs dari semua service (Nginx & PHP-FPM)
	$(COMPOSE) logs -f

logs-app: ## Pantau live logs dari service PHP-FPM (app)
	$(COMPOSE) logs -f app

logs-web: ## Pantau live logs dari service Nginx (web)
	$(COMPOSE) logs -f web

## --- SHELL & ACCESS ---
bash: ## Masuk ke shell container PHP-FPM (app)
	$(COMPOSE) exec app bash

bash-web: ## Masuk ke shell container Nginx (web)
	$(COMPOSE) exec web sh

## --- CODEIGNITER 4 SPARK ---
routes: ## Tampilkan daftar route CodeIgniter 4
	$(COMPOSE) exec app php spark routes

cache-clear: ## Bersihkan cache CodeIgniter 4
	$(COMPOSE) exec app php spark cache:clear

spark: ## Jalankan custom perintah spark (contoh: make spark CMD="routes")
	$(COMPOSE) exec app php spark $(CMD)

## --- COMPOSER ---
composer-install: ## Jalankan composer install di dalam container
	$(COMPOSE) exec app composer install

composer-update: ## Jalankan composer update di dalam container
	$(COMPOSE) exec app composer update

## --- TESTING & QUALITY ---
test: ## Jalankan unit testing PHPUnit di dalam container
	$(COMPOSE) exec app ./vendor/bin/phpunit

lint: ## Jalankan linting PHP CS Fixer (dry-run) di dalam container
	$(COMPOSE) exec app ./vendor/bin/php-cs-fixer fix --dry-run --diff

## --- LOCAL DEVELOPMENT (WITHOUT DOCKER) ---
dev-serve: ## Jalankan development server lokal di host (http://localhost:8080)
	php spark serve
