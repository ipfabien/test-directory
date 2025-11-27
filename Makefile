DOCKER_COMPOSE ?= docker compose
API_BASE_URL ?= http://localhost:8000

FIRSTNAME ?= John
LASTNAME ?= Doe
EMAIL ?= john.doe@example.com
PHONE ?= +33123456789

.PHONY: up down build install update migrate serve call-create-contact

up:
	$(DOCKER_COMPOSE) up -d

down:
	$(DOCKER_COMPOSE) down

build:
	$(DOCKER_COMPOSE) build

install:
	$(DOCKER_COMPOSE) run --rm app composer install

update:
	$(DOCKER_COMPOSE) run --rm app composer update

migrate:
	$(DOCKER_COMPOSE) run --rm app php bin/console doctrine:migrations:migrate --no-interaction

serve:
	$(DOCKER_COMPOSE) exec app php -S 0.0.0.0:8000 -t public

call-create-contact:
	curl -i -X POST "$(API_BASE_URL)/api/contact" \
		-H "Content-Type: application/json" \
		-d "{\"firstname\":\"$(FIRSTNAME)\",\"lastname\":\"$(LASTNAME)\",\"email\":\"$(EMAIL)\",\"phone\":\"$(PHONE)\"}"
