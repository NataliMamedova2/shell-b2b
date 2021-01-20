## Quick start

### Quick run project use docker-compose & Makefile

First of all you must install and run [docker-compose](https://docs.docker.com/compose/install/).

#### Clone repository
```bash
$ git clone git@bitbucket.org:aurocraft/shell-b2b.git
```

If you don't have composer.phar file - download [composer.phar file](https://getcomposer.org/download/)
```bash
$ curl -sS https://getcomposer.org/installer | php
```

1. Go to the project root directory 
```bash
$ cd shell-b2b
```

2. Copy or create environment file (use environment from docker-compose.yaml)
```bash
$ cp .env .env.local
```
Adjust settings in `.env.local` file

- Configure debug mode and current environment
```
APP_ENV     = dev
APP_DEBUG   = true
```
- Configure DB configuration
```
DATABASE_URL=pgsql://shell_b2b:111@postgresql:5432/shell_b2b
```
- Set password for **root** user (login: ***root***)
```
ROOT_SECRET='$argon2id$v=19$m=65536,t=4,p=1$j6mHiXd8jNMwAKPNuNb0oA$gei3ZhxmdyxDSMijBCohh7kbeKIpHpruyDIVWOxssao' //111
```

3. Up & Init the project
```
$ make project-init
```

4. Go to link [http://localhost:8088](http://localhost:8088)