# Educational Office Task

## Project Goal

This project is a simplified higher education admission score calculator.

- The input contains applicant data (selected program, graduation exam results, extra points).
- The system validates required and selectable subject requirements, as well as minimum result thresholds.
- After successful validation, it calculates base points, bonus points, and the total score.
- For invalid or incomplete input, it returns a clear validation error message.

## Prerequisites

- Docker Engine + Docker Compose plugin
- GNU Make

## Composer Package Information

- Name: `green-zen-monk/educational_office_task`
- Type: `project`
- License: `MIT`
- Description: `Educational Office - Home Assignment`
- PSR-4 namespace: `GreenZenMonk\\SimplifiedScoreCalculator\\` (`src/`)
- Requirement: `php: ^8.2`
- Dev dependency: `phpunit/phpunit: ^10.0`

## Useful Composer Commands

```bash
make composer-validate
make composer-show
make composer-dump-autoload
```

## Run With Docker (via Makefile)

### 1) Start the container in the background

```bash
make docker-up
```

### 2) Install dependencies (vendor)

```bash
make composer-install
```

### 3) Run tests

```bash
make test-run
```

### 4) Open an interactive shell in the running container

```bash
make docker-shell
```

### 5) Stop containers

```bash
make docker-down
```

## Same Commands Without `make`

```bash
docker compose up -d
docker compose exec app composer install
docker compose exec app vendor/bin/phpunit
docker compose exec app bash
docker compose down
```

## Useful Notes

- `docker compose exec ...` only works with a running container, so run `docker compose up -d` first.
- `exec` enters the same running container, it does not create a new temporary one.
