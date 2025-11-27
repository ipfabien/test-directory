DOCKER_COMPOSE ?= docker compose

.PHONY: up down build install update

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


