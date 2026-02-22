.PHONY: docker-build docker-up docker-down composer-install test-run docker-shell composer-validate composer-show composer-dump-autoload

docker-build:
	docker compose build app

docker-up:
	docker compose up -d

docker-down:
	docker compose down

composer-install:
	docker compose exec app composer install

test-run:
	docker compose exec  app vendor/bin/phpunit

docker-shell:
	docker compose exec app bash

composer-validate:
	docker compose exec app composer validate

composer-show:
	docker compose exec app composer show

composer-dump-autoload:
	docker compose exec app composer dump-autoload
