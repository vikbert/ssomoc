SHELL := /bin/bash

help:
	# shellcheck disable=SC2046
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$|(^#--)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m %-43s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m #-- /[33m/'

.PHONY: help
.DEFAULT_GOAL := help

#-- project
start: ## start the application
	make docker-clean
	cd ./docker/siam && npm install
	docker-compose up -d
	sleep 5
	docker-compose exec php composer install
	make db-migrate
	open http://localhost

restart: ## restart the application
	docker-compose up -d
	make db-reset
	open http://localhost


#-- db
db-reset: ## reset the db
	make db-clean
	make db-migrate

db-clean: ## clean the db
	docker-compose exec php bin/console doctrine:database:drop --if-exists -n --force
	docker-compose exec php bin/console doctrine:database:create --if-not-exists -n

db-migrate: ## doctrine migrate
	docker-compose exec php bin/console doctrine:migrations:migrate -n

#-- docker
docker-clean: ## clean up all docker resource
	docker-compose stop
	docker container prune -f
	docker image prune -f
	docker volume prune -f
	docker network prune -f

#-- E2E tests
test: ## start the cypress E2E tests
	node_modules/cypress/bin/cypress run --spec 'cypress/integration/login_*.spec.js'
test-open: ## open the cypress E2E runner
	node_modules/cypress/bin/cypress open
