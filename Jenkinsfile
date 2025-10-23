pipeline {
  agent { label 'docker' }

  options { skipDefaultCheckout(true) }

  environment {
    REGISTRY           = 'localhost:5000'        // change if registry elsewhere
    IMAGE_NAME         = 'docker-php-app'
    DOCKER_CREDENTIALS = 'docker-registry'       // only needed if your registry requires auth
    GIT_CRED_ID        = 'github-https-pat'
  }

  stages {
    stage('Checkout (HTTPS)') {
      steps {
        git branch: 'main',
            url: 'https://github.com/SadeepPrithiviraj/DockerProject.git',
            credentialsId: "${GIT_CRED_ID}"
      }
    }

    stage('Build') {
      steps {
        echo "Node: ${env.NODE_NAME}, Building ${REGISTRY}/${IMAGE_NAME}:latest"
        sh '''
          if ! command -v docker >/dev/null 2>&1; then
            echo "ERROR: Docker CLI not installed/accessible on this node."
            exit 2
          fi
          docker build --pull -t ${REGISTRY}/${IMAGE_NAME}:latest .
        '''
      }
    }

    stage('Test') {
      steps {
        sh 'docker run --rm ${REGISTRY}/${IMAGE_NAME}:latest php -v'
      }
    }

    stage('Push') {
      when { expression { return "${DOCKER_CREDENTIALS}" != "" } }
      steps {
        withCredentials([usernamePassword(credentialsId: "${DOCKER_CREDENTIALS}",
                                          usernameVariable: 'USER',
                                          passwordVariable: 'PASS')]) {
          sh '''
            echo "${PASS}" | docker login ${REGISTRY} -u "${USER}" --password-stdin || true
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
          docker pull ${REGISTRY}/${IMAGE_NAME}:latest || true
          docker run -d --name php_app -p 8080:80 ${REGISTRY}/${IMAGE_NAME}:latest
        '''
      }
    }
  }

  post {
    always {
      sh 'command -v docker >/dev/null 2>&1 && docker system prune -f || true'
    }
  }
}
