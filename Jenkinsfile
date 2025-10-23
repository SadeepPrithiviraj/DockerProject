pipeline {
  agent { label 'docker' }  // <-- run on a node that has docker CLI + daemon access

  options {
    skipDefaultCheckout(true) // avoid the implicit checkout
  }

  environment {
    REGISTRY           = 'localhost:5000'        // change if your registry is on another host
    IMAGE_NAME         = 'docker-php-app'
    DOCKER_CREDENTIALS = 'docker-registry'       // Jenkins Username/Password for your registry
    GIT_CRED_ID        = 'github-https-pat'      // Jenkins Username/Password: username or x-access-token, password = PAT
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
        sh '''
          if ! command -v docker >/dev/null 2>&1; then
            echo "ERROR: Docker CLI not installed/accessible on this node."
            exit 2
          fi
          docker build -t ${REGISTRY}/${IMAGE_NAME}:latest .
        '''
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
      // Only prune if docker exists on the node
      sh 'command -v docker >/dev/null 2>&1 && docker system prune -f || true'
    }
  }
}
