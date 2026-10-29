FROM php:8.5-cli

# Phar and zlib are built in; bz2 is added so bzip2-compressed archives can be extracted too.
RUN apt-get update \
    && apt-get install -y --no-install-recommends libbz2-dev \
    && docker-php-ext-install bz2 \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY extract.php build.php ./

# The PHAR path is passed as the argument, e.g. `docker compose run --rm extract other.phar`.
ENTRYPOINT ["php", "extract.php"]
CMD ["app.phar"]
