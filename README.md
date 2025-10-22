# PHP MySQL Demo with Docker + Jenkins CI/CD

## Quick start (dev)
1. `docker compose up -d --build`
2. Visit `http://localhost:8080`
3. Run tests: `docker run --rm -v "$PWD/app":/var/www/html -w /var/www/html php:8.2-cli bash -lc "composer install && ./vendor/bin/phpunit -v"`

## CI/CD
- Jenkins pipeline (Jenkinsfile) will run: checkout → composer install → phpunit → docker build → push to registry → ssh deploy.

## Production deploy
1. Ensure private registry is reachable and credentials set.
2. On production host: `docker login registry.example.com`
3. `docker compose pull` then `docker compose up -d`
