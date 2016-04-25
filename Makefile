SHELL = /bin/sh

DOCKER ?= $(shell which docker)
DOCKER_REPOSITORY := graze/array-filter
VOLUME := /opt/graze/array-filter
VOLUME_MAP := -v $$(pwd):${VOLUME}
DOCKER_RUN := ${DOCKER} run --rm -t ${VOLUME_MAP} ${DOCKER_REPOSITORY}:latest

.SILENT: help

.PHONY: default
default: help


# Building

.PHONY: install
install: ## Download the dependencies then build the image :rocket:.
	make 'composer-install --optimize-autoloader --ignore-platform-reqs'
	$(DOCKER) build --tag ${DOCKER_REPOSITORY}:latest .

.PHONY: composer-%
composer-%: ## Run a composer command, `make "composer-<command> [...]"`.
	${DOCKER} run -t --rm \
        -v $$(pwd):/usr/src/app \
        -v ~/.composer:/root/composer \
        -v ~/.ssh:/root/.ssh:ro \
        graze/composer --ansi --no-interaction $* $(filter-out $@,$(MAKECMDGOALS))

.PHONY: clean
clean: ## Clean up any images.
	$(DOCKER) rmi ${DOCKER_REPOSITORY}:latest

.PHONY: run
run: ## Run a command on the docker image
	$(DOCKER_RUN) $(filter-out $@,$(MAKECMDGOALS))


# Testing

.PHONY: test
test: ## Run the unit and integration testsuites.
test: lint test-unit test-integration

.PHONY: lint
lint: ## Run phpcs against the code.
	$(DOCKER_RUN) vendor/bin/phpcs -p --warning-severity=0 src/ tests/

.PHONY: lint-fix
lint-fix: ## Run phpcsf and fix possible lint errors.
	$(DOCKER_RUN) vendor/bin/phpcbf -p src/ tests/

.PHONY: test-unit
test-unit: ## Run the unit testsuite.
	$(DOCKER_RUN) vendor/bin/phpunit --colors=always --testsuite unit

.PHONY: test-matrix
test-matrix: ## Run the unit tests against multiple targets.
	${DOCKER} run --rm -t ${VOLUME_MAP} -w ${VOLUME} php:5.6-cli \
    vendor/bin/phpunit --testsuite unit
	${DOCKER} run --rm -t ${VOLUME_MAP} -w ${VOLUME} php:7.0-cli \
    vendor/bin/phpunit --testsuite unit
	${DOCKER} run --rm -t ${VOLUME_MAP} -w ${VOLUME} diegomarangoni/hhvm:cli \
    vendor/bin/phpunit --testsuite unit

.PHONY: test-integration
test-integration: ## Run the integration testsuite.
	$(DOCKER_RUN) vendor/bin/phpunit --colors=always --testsuite integration

.PHONY: test-coverage
test-coverage: ## Run all tests and output coverage to the console.
	$(DOCKER_RUN) vendor/bin/phpunit --coverage-text

.PHONY: test-coverage-html
test-coverage-html: ## Run all tests and output coverage to html.
	$(DOCKER_RUN) vendor/bin/phpunit --coverage-html

.PHONY: test-coverage-clover
test-coverage-clover: ## Run all tests and output clover coverage to file.
	$(DOCKER_RUN) vendor/bin/phpunit --coverage-clover=./tests/report/coverage.clover


# Help

.PHONY: help
help: ## Show this help message.
	echo "usage: make [target] ..."
	echo ""
	echo "targets:"
	egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'
