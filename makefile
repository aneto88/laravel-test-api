# Makefile para Laravel + Docker Compose

COMPOSE_FILE=compose.dev.yaml
EXEC=docker compose -f $(COMPOSE_FILE) exec workspace
RUN=docker compose -f $(COMPOSE_FILE) run --rm workspace

up:
	docker compose -f $(COMPOSE_FILE) up -d

down:
	docker compose -f $(COMPOSE_FILE) down

build:
	docker compose -f $(COMPOSE_FILE) build

bash:
	$(EXEC) bash

composer-install:
	$(EXEC) composer install

composer-update:
	$(EXEC) composer update

artisan:
	$(EXEC) php artisan

migrate:
	$(EXEC) php artisan migrate

migrate-fresh:
	$(EXEC) php artisan migrate:fresh

seed:
	$(EXEC) php artisan db:seed

test:
	$(EXEC) php artisan test

npm-install:
	$(EXEC) npm install

npm-dev:
	$(EXEC) npm run dev

npm-build:
	$(EXEC) npm run build

logs:
	docker compose -f $(COMPOSE_FILE) logs -f

restart:
	docker compose -f $(COMPOSE_FILE) restart

# Atalhos para comandos comuns do Laravel
key-generate:
	$(EXEC) php artisan key:generate

cache-clear:
	$(EXEC) php artisan cache:clear

config-clear:
	$(EXEC) php artisan config:clear

route-clear:
	$(EXEC) php artisan route:clear

view-clear:
	$(EXEC) php artisan view:clear
