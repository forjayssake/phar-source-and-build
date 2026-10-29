# phar-source-and-build

Extract the source from a PHP `.phar` archive so you can modify it, then rebuild it into a working `.phar`.

- `extract.php` unpacks a PHAR into `source/` and saves the archive's original executable stub to `stub.php`.
- `build.php` packs `source/` back into a PHAR with that stub, writing it to `build/`.

The `source/` and `build/` directories are created automatically the first time either script runs.

## Requirements

Either:

- **Docker** with Docker Compose (recommended; no local PHP needed), or
- **PHP 8+** with the `phar` extension (enabled by default). Archives compressed with gzip or bzip2 also need the `zlib` or `bz2` extension.

## Quick start (Docker)

1. Put the PHAR you want to work on in the project root as `app.phar`.
2. Extract it:

   ```sh
   docker compose run --rm extract
   ```

   This creates `source/` (the archive contents) and `stub.php` (the original stub).
3. Edit the files in `source/` as needed.
4. Rebuild:

   ```sh
   docker compose run --rm build
   ```

   The new archive is written to `build/app.phar`.

The project directory is mounted into the container, so `source/`, `stub.php` and `build/` appear on your machine.

### Using a different file name

Pass the input PHAR path (relative to the project root) to `extract`, and the output path to `build`:

```sh
docker compose run --rm extract other.phar
docker compose run --rm build build/other.phar
```

## Without Docker

Run the same scripts with a local PHP:

```sh
php extract.php                  # reads app.phar
php extract.php path/to/other.phar

php -d phar.readonly=0 build.php                    # writes build/app.phar
php -d phar.readonly=0 build.php build/other.phar
```

`phar.readonly=0` is required because PHP refuses to write PHAR archives by default.

## How the rebuild works

`build.php` creates an uncompressed archive, signed with SHA-256, whose alias is the output file name. It adds every file under `source/` and uses `stub.php` as the stub, so the rebuilt PHAR starts up the same way as the original.

`stub.php` can be edited too; for example, to change the entry point the stub loads.

Running `extract.php` again overwrites files in `source/` and replaces `stub.php`, so commit or back up your changes first. Files you added to `source/` that are not in the archive are left in place.

## Files not tracked in git

`source/`, `build/`, `stub.php` and `*.phar` are listed in `.gitignore`, because they are the third-party code being worked on rather than part of this tool. Check the license of any PHAR before you redistribute its contents or a modified build.

## License

Licensed under the [Apache License, Version 2.0](LICENSE).
