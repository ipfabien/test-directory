DOCKER_COMPOSE ?= docker compose
API_BASE_URL ?= http://localhost:8000

FIRSTNAME ?= John
LASTNAME ?= Doe
EMAIL ?= john.doe@example.com
PHONE ?= +33123456789
NOTE ?=

# Try to read API_TOKEN from .env so that curl commands don't require manual export.
AUTH_TOKEN ?= $(shell sed -n 's/^API_TOKEN=\(.*\)/\1/p' .env)

CONTACT_ID ?= 00000000-0000-0000-0000-000000000001
MANAGER_ID ?= 11111111-1111-1111-1111-111111111111
PAGE ?= 1
PER_PAGE ?= 20

.PHONY: up down build install update migrate serve call-create-contact call-get-contact call-get-contact-list call-get-contact-manager call-get-manager-list call-get-manager cs-fix phpstan phpunit mailhog-ui

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
		-H "Authorization: Bearer $(AUTH_TOKEN)" \
		-d "{\"firstname\":\"$(FIRSTNAME)\",\"lastname\":\"$(LASTNAME)\",\"email\":\"$(EMAIL)\",\"phone\":\"$(PHONE)\",\"note\":\"$(NOTE)\",\"managerId\":\"$(MANAGER_ID)\"}"

call-get-contact:
	curl -i -H "Authorization: Bearer $(AUTH_TOKEN)" -X GET "$(API_BASE_URL)/api/contact/$(CONTACT_ID)"

call-get-contact-list:
	curl -i -H "Authorization: Bearer $(AUTH_TOKEN)" -X GET "$(API_BASE_URL)/api/contacts?firstname=$(FIRSTNAME)&lastname=$(LASTNAME)&email=$(EMAIL)&phone=$(PHONE)&page=$(PAGE)&perPage=$(PER_PAGE)"

call-get-contact-manager:
	curl -i -H "Authorization: Bearer $(AUTH_TOKEN)" -X GET "$(API_BASE_URL)/api/contact/$(CONTACT_ID)/manager"

call-get-manager-list:
	curl -i -H "Authorization: Bearer $(AUTH_TOKEN)" -X GET "$(API_BASE_URL)/api/managers"

call-get-manager:
	curl -i -H "Authorization: Bearer $(AUTH_TOKEN)" -X GET "$(API_BASE_URL)/api/manager/$(MANAGER_ID)"

cs-fix:
	$(DOCKER_COMPOSE) run --rm app php vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php

phpstan:
	$(DOCKER_COMPOSE) run --rm app php vendor/bin/phpstan analyse -c phpstan.neon

phpunit:
	$(DOCKER_COMPOSE) run --rm app php vendor/bin/phpunit

jwt:
	$(DOCKER_COMPOSE) run --rm app php bin/console app:jwt:generate-test-token

mailhog-ui:
	xdg-open http://localhost:18025 || sensible-browser http://localhost:18025 || x-www-browser http://localhost:18025 || echo "Ouvre http://localhost:18025 dans ton navigateur"
