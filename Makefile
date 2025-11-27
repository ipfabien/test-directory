DOCKER_COMPOSE ?= docker compose

.PHONY: up down build

up:
	$(DOCKER_COMPOSE) up -d

down:
	$(DOCKER_COMPOSE) down

build:
	$(DOCKER_COMPOSE) build


