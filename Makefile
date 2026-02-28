.PHONY: docker-build docker-up docker-down composer-install test-run docker-shell composer-validate composer-show composer-dump-autoload phpstan cs-check cs-fix

docker-build:
	docker compose build app

docker-up:
	docker compose up -d

docker-down:
	docker compose down

composer-install:
	docker compose exec app composer install

test-run:
	docker compose exec app composer test

test-debug-run:
	docker compose exec app composer test:debug

test-coverage-run:
	docker compose exec app composer test:coverage

docker-shell:
	docker compose exec app bash

composer-validate:
	docker compose exec app composer validate

composer-show:
	docker compose exec app composer show

composer-dump-autoload:
	docker compose exec app composer dump-autoload

phpstan:
	docker compose exec app composer phpstan

cs-check:
	docker compose exec app composer cs-check

cs-fix:
	docker compose exec app composer cs-fix
