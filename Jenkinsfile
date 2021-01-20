#!/bin/env groovy

def getDeployCongFileId(branch) {
    def result

    switch (branch) {
        case 'master':
            result = 'deploy-conf-prod'
            break
        case 'stage':
            result = 'deploy-conf-stage'
            break
        default:
            result = 'deploy-conf-dev'
            break
    }
    return result
}

pipeline {
    agent {
        node {
            label 'php72 && pgsql && selenium'
        }
    }

    environment {
        DEPLOY_CONF_FILE_ID = getDeployCongFileId(env.BRANCH_NAME)
    }

    tools {nodejs "node"}

    stages {
        stage('Prepare environments') {
            environment {
                DEPLOY_APP_ENV = 'test'
                DEPLOY_APP_DEBUG = 'true'
                DEPLOY_APP_SECRET = 't8sts$cretf0rt3st'
                DEPLOY_ROOT_SECRET = '$argon2id$v=19$m=65536,t=4,p=1$j6mHiXd8jNMwAKPNuNb0oA$gei3ZhxmdyxDSMijBCohh7kbeKIpHpruyDIVWOxssao'

                DEPLOY_DB_HOSTNAME = "localhost"
                DEPLOY_DB_PORT = "5432"
                DEPLOY_DB_NAME = "db"
                DEPLOY_DB_USERNAME = "jenkins"
                DEPLOY_SQL_PASSWORD = ""

                DEPLOY_WEB_DRIVER_URL = "http://localhost"
                DEPLOY_WEB_DRIVER_HOST = "localhost"

                DEPLOY_DATABASE_URL = "pgsql://${DEPLOY_DB_USERNAME}:${DEPLOY_SQL_PASSWORD}@${DEPLOY_DB_HOSTNAME}:${DEPLOY_DB_PORT}/${DEPLOY_DB_NAME}"

                DEPLOY_MARKETING_API_NEWS_HOST = "https://shellcards.com.ua"
                DEPLOY_MARKETING_NEWS_PAGE = "https://shellcards.com.ua/news"
                DEPLOY_MARKETING_NEWS_PAGE_EN = "https://shellcards.com.ua/en/news"
                DEPLOY_SHELL_LINK_GET_BONUSES = "http://shellsmart.com.ua"
                DEPLOY_MARKETING_BECOME_CLIENT = "https://shellcards.com.ua/become-client"
                DEPLOY_MARKETING_BECOME_CLIENT_EN= "https://shellcards.com.ua/en/become-client"

                DEPLOY_B2B_PORTAL= "https://portal.shellcards.com.ua"
                DEPLOY_PUBLIC_SOTA_SERVER_URL= "https://shell.sota-buh.com.ua/"
                DEPLOY_PUBLIC_SOTA_MERCHANT_ID= "34430873"

                DEPLOY_IMPORT_1S_DIRECTORY = "%kernel.project_dir%/storage/sync/1S_files"
                DEPLOY_EXPORT_1S_DIRECTORY = "%kernel.project_dir%/storage/sync/WWW_files"
            }

            steps {
                sh '''
                    cp --force .env .env.local
                    sed -r -i '
                        /^APP_ENV=/       s|=.*$|='"$DEPLOY_APP_ENV"'|;
                        /^APP_DEBUG=/     s|=.*$|='"$DEPLOY_APP_DEBUG"'|;
                        /^APP_SECRET=/    s|=.*$|='"$DEPLOY_APP_SECRET"'|;
                        /^DATABASE_URL=/  s|=.*$|='"$DEPLOY_DATABASE_URL"'|;

                        /^MARKETING_API_NEWS_HOST=/    s|=.*$|='"$DEPLOY_MARKETING_API_NEWS_HOST"'|;
                        /^MARKETING_NEWS_PAGE=/        s|=.*$|='"$DEPLOY_MARKETING_NEWS_PAGE"'|;
                        /^MARKETING_NEWS_PAGE_EN=/     s|=.*$|='"$DEPLOY_MARKETING_NEWS_PAGE_EN"'|;
                        /^SHELL_LINK_GET_BONUSES=/     s|=.*$|='"$DEPLOY_SHELL_LINK_GET_BONUSES"'|;
                        /^MARKETING_BECOME_CLIENT=/    s|=.*$|='"$DEPLOY_MARKETING_BECOME_CLIENT"'|;
                        /^MARKETING_BECOME_CLIENT_EN=/ s|=.*$|='"$DEPLOY_MARKETING_BECOME_CLIENT_EN"'|;

                        /^B2B_PORTAL=/                 s|=.*$|='"$DEPLOY_B2B_PORTAL"'|;
                        /^PUBLIC_SOTA_SERVER_URL=/     s|=.*$|='"$DEPLOY_PUBLIC_SOTA_SERVER_URL"'|;
                        /^PUBLIC_SOTA_MERCHANT_ID=/    s|=.*$|='"$DEPLOY_PUBLIC_SOTA_MERCHANT_ID"'|;

                        /^IMPORT_1S_DIRECTORY=/    s|=.*$|='"$DEPLOY_IMPORT_1S_DIRECTORY"'|;
                        /^EXPORT_1S_DIRECTORY=/    s|=.*$|='"$DEPLOY_EXPORT_1S_DIRECTORY"'|;
                    ' .env.local
                    sed -r -i "/^ROOT_SECRET=/ s|=.*$|='"$DEPLOY_ROOT_SECRET"'|" .env.local

                    sed -r -i '
                        /^APP_ENV=/         s|=.*$|='"$DEPLOY_APP_ENV"'|;
                        /^DB_HOSTNAME=/     s|=.*$|='"$DEPLOY_DB_HOSTNAME"'|;
                        /^DB_PORT=/         s|=.*$|='"$DEPLOY_DB_PORT"'|;
                        /^DB_NAME=/         s|=.*$|='"$DEPLOY_DB_NAME"'|;
                        /^DB_USERNAME=/     s|=.*$|='"$DEPLOY_DB_USERNAME"'|;
                        /^DB_PASSWORD=/     s|=.*$|='"$DEPLOY_SQL_PASSWORD"'|;
                        /^WEB_DRIVER_URL=/  s|=.*$|='"$DEPLOY_WEB_DRIVER_URL"'|;
                        /^WEB_DRIVER_HOST=/ s|=.*$|='"$DEPLOY_WEB_DRIVER_HOST"'|;
                    ' .env.test
                    sed -r -i "/^ROOT_SECRET=/ s|=.*$|='"$DEPLOY_ROOT_SECRET"'|" .env.test
                '''
            }
        }

        stage('Prepare') {
            parallel {
                stage('Prepare frontend') {
                    steps {
                        sh '''
                            # Prebuild >
                            echo 'Install frontend dependencies'
                            npm install --prefer-offline --no-audit --progress=false
                        '''
                    }
                }

                stage('Prepare backend') {
                    steps {
                        sh '''
                            echo 'Generate the public and private keys used for signing JWT tokens'
                            set -e
                            mkdir -p config/jwt
                            jwt_passhrase=$(grep ''^JWT_PASSPHRASE='' .env | cut -f 2 -d ''='')
                            echo "$jwt_passhrase" | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
                            echo "$jwt_passhrase" | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout

                            touch .symfony

                            echo 'Install backend dependencies'
                            php composer.phar global require hirak/prestissimo
                            php composer.phar install --no-progress --no-suggest --prefer-dist --optimize-autoloader --no-scripts

                            echo 'Update database'
                            psql -c 'CREATE EXTENSION IF NOT EXISTS "uuid-ossp"'
                            php bin/console doctrine:migrations:migrate -n

                            echo 'Clear Cache'
                            php bin/console cache:clear --no-warmup
                            php bin/console cache:warmup --env=test
                        '''
                    }
                }
            }
        }

        stage('Static Code analyse') {
            steps {
                sh '''
                    ./vendor/bin/phpstan analyse -l 4 -c phpstan.neon --no-progress
                '''
            }
        }

        stage('Tests') {
            parallel {
                stage('Frontend Tests') {
                    steps {
                        sh '''
                            npm run test
                        '''
                    }
                }
                stage('Unit Tests') {
                    steps {
                        sh '''
                            ./vendor/bin/phpunit tests/unit -v --colors=never --stderr
                        '''
                    }
                }
            }
        }

//         stage('Codeception API Test') {
//             steps {
//                 sh '''
//                     ./vendor/bin/codecept run api --steps --xml --html
//                 '''
//             }
//         }

//         stage('Codeception Import Test') {
//             steps {
//                 sh '''
//                     ./vendor/bin/codecept run tests/acceptance/command --steps -vv
//                 '''
//             }
//         }

        stage('Prepare for deploy') {
            when {
                expression { BRANCH_NAME ==~ /(test|stage|master)/ }
            }
            steps {
                sh '''
                    npm run build
                    npm run translation

                    echo 'Update project translations'
                    php bin/console translation:update --domain=frontend --clean --force --prefix="" uk
                    php bin/console translation:update --domain=frontend --clean --force --prefix="" en
                    php bin/console translation:parse-json --file=public/locales/uk.json
                    php bin/console translation:extract --hide-errors

                    php composer.phar install --no-progress --no-suggest --prefer-dist --optimize-autoloader --no-scripts --no-dev

                    rm .symfony
                    rm .env.local
                    rm .env.test
                    rm -rf config/jwt
                    rm -rf docker
                    rm docker-compose.test.yml
                    rm docker-compose.yml
                    rm -f .php_cs.dist
                    rm -f .phpunit.result.cache
                    rm -f .phpcs-cache
                    rm -f phpcs.xml
                    rm -rf node_modules
                    rm -rf var/cache
                    rm -rf var/log
                    rm -rf storage
                    rm -rf translations/frontend.*.xlf
                    rm -rf translations/jsonfile.*.xlf
                '''
            }
        }

        stage('Deploy') {
            when {
                expression { BRANCH_NAME ==~ /(test|stage|master)/ }
            }

            steps {
                configFileProvider([configFile(fileId: DEPLOY_CONF_FILE_ID, targetLocation: 'deploy.conf', variable: 'conf_path')]){
                    configFileProvider([configFile(fileId: 'deploy-script-common', targetLocation: 'deploy.sh', variable: 'script_path')]){
                        sshagent(['jenkins-bitbucket-ssh']){
                            sh 'chmod a+x "$script_path" && "$script_path" "$conf_path"'
                        }
                    }
                }
            }
        }
    }
    post {
        failure {
            archiveArtifacts artifacts: 'tests/_output/record_**/*, tests/_output/*, var/log/*', fingerprint: true
            junit 'tests/_output/report.xml'
        }
    }
}
