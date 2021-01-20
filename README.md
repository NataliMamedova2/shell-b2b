## Documentations
1. [Quick start (docker-compose)](docs/quick-start.md)
2. [Frontend](#frontend-up-and-go)
3. [Deployment console commands](#deployment-console-commands)
4. [Tests](docs/tests.md)
5. [Project structure](docs/structure.md)
6. [Git workflow](docs/git-workflow.md)
7. [Import/Export files](#import-export-files)
8. [Cron](#cron-jobs)

### REQUIREMENTS

PHP 7.2+

Postgres 9.6+

Required PHP extensions:
```
- ctype
- iconv
- mcrypt
- intl
- json
- pgsql
- xml
- mbstring
- curl
- dom
- gd
- imagick
- exif
```

### php.ini settings
```
post_max_size = 10M
upload_max_filesize = 10M
```

## INSTALLATION

### Get source code

#### Clone repository manually
```
$ git clone git@bitbucket.org:aurocraft/shell-b2b.git
```

## First installation

### Setup production application

1. Go to the project root folder 
```
$ cd shell-b2b
```

2. If need Download [composer.phar file](https://getcomposer.org/download/)
```
$ curl -sS https://getcomposer.org/installer | php
```

3. Install dependencies packages
```
$ php composer.phar install --no-dev --optimize-autoloader
```
or
```
$ composer install --no-dev --optimize-autoloader
```

4. Copy or create environment file
```
$ cp .env .env.local
```

Adjust settings in `.env.local` file

- Configure debug mode and current environment
``` 
APP_ENV     = prod
APP_DEBUG   = false
```
- Configure DB configuration
```
DATABASE_URL=pgsql://shell_b2b:securepassword@localhost:5432/shell_b2b
```

5. (Optional) Check the database connection settings
```
$ php bin/console doctrine:schema:validate 
```

6. Apply migrations
```
$ php bin/console doctrine:migrations:migrate
```

7. (Optional) Populating Database using Doctrine-Fixtures (only for dev environment)
```
$ php bin/console doctrine:fixtures:load
```

You will get a warning about the database getting purged. You can go ahead and type Y:

```bash
Careful, database will be purged. Do you want to continue y/N ? y
  > purging database
  > loading App\DataFixtures\ORM\Fixtures  
```

8. Build frontend scripts
```
$ npm install && npm run build
```

9. Clearing and Warming Up Cache
```
$ php bin/console cache:clear --no-warmup
$ php bin/console cache:warmup
```

10. Generate password gor "root" user & fill in .env.local ROOT_SECRET
```
$ php bin/console security:encode-password
```

11. Generate the public and private keys used for signing JWT tokens
```shell script
set -e
mkdir -p config/jwt
jwt_passhrase=$(grep ''^JWT_PASSPHRASE='' .env.local | cut -f 2 -d ''='')
echo "$jwt_passhrase" | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
echo "$jwt_passhrase" | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout
setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
```

## Deployment console commands

### Production deployment
```bash
$ php composer.phar install --no-progress --no-suggest --prefer-dist --optimize-autoloader --no-dev --no-scripts
$ php bin/console doctrine:migrations:migrate -n
$ php bin/console translation:update --domain=frontend --clean --force --prefix="" uk
$ php bin/console translation:update --domain=frontend --clean --force --prefix="" en
$ php bin/console translation:parse-json --file=public/locales/uk.json
$ php bin/console translation:extract --hide-errors
$ php bin/console cache:clear --no-warmup
$ php bin/console cache:warmup --env=prod
```

### Local Deployment
```bash
$ composer install --no-scripts
$ php bin/console doctrine:migrations:migrate -n
$ php bin/console cache:clear
$ npm install
$ npm run build
```
#### OR use makefile command
```bash
$ make update
```

## Console commands
### Project commands
```bash
$ php bin/console import:1c                     // import 1C files from "IMPORT_1S_DIRECTORY" directory. Use "--help" for more details
$ php bin/console import:pc                     // import PC files from "PC_SERVER_ROOT_DIRECTORY" directory. Use "--help" for more details
$ php bin/console export                        // export 1C&PC files to "EXPORT_1S_DIRECTORY" and "PC_SERVER_ROOT_DIRECTORY" directories
$ php bin/console request-client-info:pc        // generate and export "SIDCLi_Q.txt" file to PC - "PC_SERVER_ROOT_DIRECTORY" directory
$ php bin/console import:documents              // import & save documments files from "IMPORT_DOCUMENTS_DIRECTORY". Use "--help" for more details
$ php bin/console migrate:balance-history       // migrate clients balance histories from imported "PIDCLi_R.txt" files. Only for first day of each month
```

### Migrations

```bash
$ php bin/console make:migration
$ php bin/console doctrine:migrations:migrate
$ php bin/console doctrine:migrations:execute YYYYMMDDHHMMSS --down
```

### Load fixtures

```bash
$ php bin/console doctrine:fixtures:load --append
```
### Postgres dump/restore

```bash
$ pg_dump -U shell_b2b -h 127.0.0.1 -p 5433 -Fc shell_b2b > shell_b2b.dump
$ pg_restore --clean --if-exists -v -d shell_b2b -O -U shell_b2b shell_b2b.dump
```

### Composer

```bash
$ composer install
```

### Clear Symfony Cache

```bash
$ php bin/console cache:clear
```

### Update translation

#### Update translations and remove old
```bash
$ php bin/console translation:update uk --domain=frontend --clean --force --prefix="" -q
$ php bin/console translation:update en --domain=frontend --clean --force --prefix="" -q
```

#### Parse code and dd new translations and keep it empty
```bash
$ php bin/console translation:extract --hide-errors
```

### Encore
```bash
$ cd assets/backend && yarn watch
```

## Import/Export files

### 1S import/export .env variables
```
IMPORT_1S_DIRECTORY=%kernel.project_dir%/storage/sync/1S_files
EXPORT_1S_DIRECTORY=%kernel.project_dir%/storage/sync/WWW_files
```

### PC server .env variables
```
PC_SERVER_HOST=127.0.0.1
PC_SERVER_PORT=22
PC_SERVER_USERNAME=
PC_SERVER_PASSWORD=
PC_SERVER_ROOT_DIRECTORY=/
```

### 1S import documents .env variable
```
IMPORT_DOCUMENTS_DIRECTORY=%kernel.project_dir%/storage/sync/documents
```

### Console command for import/export files
```shell script
$ php bin/console import:1c                 // import from 1C
$ php bin/console import:pc                 // import from PC
$ php bin/console export                    // export files to 1C & PC
$ php bin/console request-client-info:pc    // send to PC file for get clients info
```

#### For more info about import command use `--help`
```shell script
$ php bin/console import:1c --help
```

## Cron Jobs
```cron
*/5  * * * *     /usr/bin/php bin/console import:1c
*/5  * * * *     /usr/bin/php bin/console import:pc
*/5  * * * *     /usr/bin/php bin/console export 
*/15 * * * *     /usr/bin/php bin/console request-client-info:pc
0 */17 * * *     /usr/bin/php bin/console import:documents
```