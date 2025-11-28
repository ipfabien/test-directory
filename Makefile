DOCKER_COMPOSE ?= docker compose
API_BASE_URL ?= http://localhost:8000

FIRSTNAME ?= John
LASTNAME ?= Doe
EMAIL ?= john.doe@example.com
PHONE ?= +33123456789

CONTACT_ID ?= 00000000-0000-0000-0000-000000000001
PAGE ?= 1
PER_PAGE ?= 20

.PHONY: up down build install update migrate serve call-create-contact call-get-contact call-get-contact-list cs-fix

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

call-get-contact:
	curl -i -X GET "$(API_BASE_URL)/api/contact/$(CONTACT_ID)"

call-get-contact-list:
	curl -i -X GET "$(API_BASE_URL)/api/contacts?firstname=$(FIRSTNAME)&lastname=$(LASTNAME)&email=$(EMAIL)&phone=$(PHONE)&page=$(PAGE)&perPage=$(PER_PAGE)"

cs-fix:
	$(DOCKER_COMPOSE) run --rm app php vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php
