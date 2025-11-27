DOCKER_COMPOSE ?= docker compose

.PHONY: up down build install update migrate

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

