pipeline {
    agent any

    environment {
        REGISTRY = 'your.private.registry:5000'
        IMAGE_NAME = 'docker-php-app'
        GIT_CREDENTIALS = 'github-credentials'
        DOCKER_CREDENTIALS = 'registry-creds'
    }

    stages {
        stage('Checkout') {
            steps {
                git(
                    url: 'https://github.com/SadeepPrithiviraj/DockerProject.git',
                    branch: 'main',
                    credentialsId: 'github-credentials'
                    )
            }
        }

        stage('Build') {
            steps {
                script {
                    sh 'docker build -t $REGISTRY/$IMAGE_NAME:latest .'
                }
            }
        }

        stage('Test') {
            steps {
                script {
                    sh 'docker run --rm $REGISTRY/$IMAGE_NAME:latest php -v'
                }
            }
        }

        stage('Push') {
            steps {
                withCredentials([usernamePassword(credentialsId: "${DOCKER_CREDENTIALS}", usernameVariable: 'USER', passwordVariable: 'PASS')]) {
                    sh '''
                    echo "$PASS" | docker login $REGISTRY -u "$USER" --password-stdin
                    docker push $REGISTRY/$IMAGE_NAME:latest
                    '''
                }
            }
        }

        stage('Deploy') {
            steps {
                script {
                    sh '''
                    docker stop php_app || true
                    docker rm php_app || true
                    docker pull $REGISTRY/$IMAGE_NAME:latest
                    docker run -d --name php_app -p 8080:80 $REGISTRY/$IMAGE_NAME:latest
                    '''
                }
            }
        }
    }
}
