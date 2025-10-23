pipeline {
  agent any

  options {
    // prevent the implicit "Declarative: Checkout SCM"
    skipDefaultCheckout(true)
  }

  environment {
    REGISTRY           = 'localhost:5000'
    IMAGE_NAME         = 'docker-php-app'
    DOCKER_CREDENTIALS = 'docker-registry'
  }

  stages {
    stage('Checkout (SSH)') {
      steps {
        // Load the SSH key properly to avoid the libcrypto error
        sshagent(credentials: ['github-ssh']) {
          git branch: 'main',
              url: 'git@github.com:SadeepPrithiviraj/DockerProject.git'
        }
      }
    }

    stage('Build') {
      steps {
        sh 'docker build -t ${REGISTRY}/${IMAGE_NAME}:latest .'
      }
    }

    stage('Test') {
      steps {
        sh 'docker run --rm ${REGISTRY}/${IMAGE_NAME}:latest php -v'
      }
    }

    stage('Push') {
      steps {
        withCredentials([usernamePassword(
          credentialsId: "${DOCKER_CREDENTIALS}",
          usernameVariable: 'USER',
          passwordVariable: 'PASS'
        )]) {
          sh '''
            echo "${PASS}" | docker login ${REGISTRY} -u "${USER}" --password-stdin
            docker push ${REGISTRY}/${IMAGE_NAME}:latest
          '''
        }
      }
    }

    stage('Deploy') {
      steps {
        sh '''
          docker stop php_app || true
          docker rm php_app || true
          docker pull ${REGISTRY}/${IMAGE_NAME}:latest
          docker run -d --name php_app -p 8080:80 ${REGISTRY}/${IMAGE_NAME}:latest
        '''
      }
    }
  }

  post {
    always {
      sh 'docker system prune -f || true'
    }
  }
}
