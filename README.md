# Symfony Ratelimited App

This repository is a proof of concept of a rate limiting service written using
Symfony framework and in memory data saving with redis.


Docker configuration is based upon [Symfony's officially endorsed repository](https://github.com/dunglas/symfony-docker).

## Getting Started (directly from original symfony-docker repo)

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/)
2. Run `docker-compose build --pull --no-cache` to build fresh images
3. Run `docker-compose up` (the logs will be displayed in the current shell)
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker-compose down --remove-orphans` to stop the Docker containers.

## Requirements outline

- Use of a PHP framework (symfony) and a storage system (redis)
- Creation of a JSON endpoint that can handle both POST and GET requests with different results
- Implementation from scratch of an IP based rate limiting service
- limits should be configurable (by default 3 POST and 5 GET every 60 seconds)

