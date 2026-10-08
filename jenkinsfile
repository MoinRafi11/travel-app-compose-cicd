pipeline {
    agent any

    environment {
        DOCKER_USER = "moinrafi11"
        VM_USER = "verjenkins"
        STAGING_IP = "192.168.1.99"
        PROD_IP = "192.168.1.100"
        DOCKER_CREDS_ID = "My-PAT-Travel-APP"
        SSH_CREDS_ID = "vm-ssh-key"
        TAG = "${env.BUILD_NUMBER}"
        APP_NAME = "meen-travel-app"
    }

    stages {

        stage('Build & Push') {
            steps {
                script {
                    withCredentials([
                        usernamePassword(
                            credentialsId: DOCKER_CREDS_ID,
                            passwordVariable: 'PASS',
                            usernameVariable: 'USER'
                        )
                    ]) {
                        sh "echo \$PASS | docker login -u \$USER --password-stdin"

                        // Build & Push single image artifact for both environments
                        sh "docker build -t ${DOCKER_USER}/${APP_NAME}:v${TAG} ."
                        sh "docker push ${DOCKER_USER}/${APP_NAME}:v${TAG}"
                    }
                }
            }
        }

        stage('Create Environment File') {
            steps {
                withCredentials([
                    string(
                        credentialsId: 'travel-M-app-PG_PASS',
                        variable: 'DB_PASS'
                    )
                ]) {
                    sh """
                        cat << EOF > .env
POSTGRES_DB=travel_db
POSTGRES_USER=postgres
POSTGRES_PASSWORD=${DB_PASS}
DB_HOST=travel_db
DB_PORT=5432
EOF
                    """
                }
            }
        }

        stage('Deploy to Staging') {
            steps {
                sshagent([SSH_CREDS_ID]) {

                    // Remove old database directory
                    sh "ssh -o StrictHostKeyChecking=no ${VM_USER}@${STAGING_IP} 'mkdir -p ~/${APP_NAME}'"

                    // Transfer Compose, environment file and database initialization
                    sh "scp -r -o StrictHostKeyChecking=no docker-compose.yml .env db ${VM_USER}@${STAGING_IP}:~/${APP_NAME}/"

                    sh """
                        ssh -o StrictHostKeyChecking=no ${VM_USER}@${STAGING_IP} '
                            cd ~/${APP_NAME}
                            docker rm -f travel-db travel-web 2>/dev/null || true

                            export TAG=${TAG}
                            export DOCKER_USER=${DOCKER_USER}

                            docker compose -p ${APP_NAME} pull
                            docker compose -p ${APP_NAME} up -d --force-recreate --remove-orphans
                        '
                    """
                }
            }
        }

        stage('Approval Gate') {
            steps {
                input message: "Verify Staging environment at http://${STAGING_IP}:8085. Promote to Production?",
                      ok: "Deploy!"
            }
        }

        stage('Deploy to Production') {
            steps {
                sshagent([SSH_CREDS_ID]) {

                    sh "ssh -o StrictHostKeyChecking=no ${VM_USER}@${PROD_IP} 'mkdir -p ~/${APP_NAME}/'"

                    sh "scp -r -o StrictHostKeyChecking=no docker-compose.yml .env db ${VM_USER}@${PROD_IP}:~/${APP_NAME}/"

                    sh """
                        ssh -o StrictHostKeyChecking=no ${VM_USER}@${PROD_IP} '
                            cd ~/${APP_NAME}
                            docker rm -f travel-db travel-web 2>/dev/null || true

                            export TAG=${TAG}
                            export DOCKER_USER=${DOCKER_USER}

                            docker compose -p ${APP_NAME} pull
                            docker compose -p ${APP_NAME} up -d --force-recreate --remove-orphans
                        '
                    """
                }
            }
        }
    }
}
