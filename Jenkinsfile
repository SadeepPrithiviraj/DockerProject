pipeline {
  agent any
  environment {
    REGISTRY = "registry.example.com:443" // change to YOUR registry (omit :443 if default)
    IMAGE_NAME = "${env.REGISTRY}/your-namespace/php-mysql-demo"
    TAG = "${env.BUILD_NUMBER}"
  }
  stages {
    stage('Checkout') {
      steps {
        checkout scm
      }
    }
    stage('Install & Test') {
      steps {
        dir('app') {
          sh 'composer install --no-interaction'
          sh './vendor/bin/phpunit -v || true' // continue even if tests are failing? Remove || true to fail pipeline on test failure.
        }
      }
    }
    stage('Build Image') {
      steps {
        sh """
          docker build -t ${IMAGE_NAME}:${TAG} .
          docker tag ${IMAGE_NAME}:${TAG} ${IMAGE_NAME}:latest
        """
      }
    }
    stage('Login to Registry') {
      steps {
        withCredentials([usernamePassword(credentialsId: 'docker-registry-credentials', usernameVariable: 'REG_USER', passwordVariable: 'REG_PASS')]) {
          sh 'echo "$REG_PASS" | docker login ${REGISTRY} -u "$REG_USER" --password-stdin'
        }
      }
    }
    stage('Push Image') {
      steps {
        sh """
          docker push ${IMAGE_NAME}:${TAG}
          docker push ${IMAGE_NAME}:latest
        """
      }
    }
    stage('Deploy') {
      steps {
        // Use ssh deploy key credential to SSH into production host and update service
        sshagent (credentials: ['ssh-deploy-key']) {
          // Replace user@host and path to your docker-compose.prod.yml on remote host
          sh """
            ssh -o StrictHostKeyChecking=no deployuser@your.production.host '
              cd /path/to/deployment &&
              docker login ${REGISTRY} -u ${REG_USER} -p ${REG_PASS} &&
              docker compose pull web &&
              docker compose up -d --remove-orphans
            '
          """
        }
      }
    }
  }
  post {
    always {
      sh 'docker logout ${REGISTRY} || true'
    }
  }
}
