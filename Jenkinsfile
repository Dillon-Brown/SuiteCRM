pipeline {
    agent any

    stages {
        stage('composer_install') {
            steps {
                sh 'composer self-update'
                sh 'composer install --no-interaction'
            }
        }
        stage('php_lint') {
            steps {
                sh 'for file in $(git diff --name-status HEAD~1 HEAD | egrep "^[ACMR].*\.php$" | cut -c 3-); do php -l $file; done'
            }
        }
        stage('phpunit') {
            steps {
                sh 'cd tests && ../vendor/bin/phpunit'
            }
        }
        stage('output') {
            steps {
                sh 'cat ../suitecrm.log'
            }
        }
    }
}
