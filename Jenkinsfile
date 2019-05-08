pipeline {
    agent any

    stages {
        stage('composer_install') {
            steps {
                sh 'composer install --no-interaction'
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

