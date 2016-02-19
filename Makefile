SHELL = /bin/sh

.PHONY: install composer clean help
.PHONY: test test-unit test-integration test-matrix

.SILENT: help

install: ## Download the dependencies then build the image :rocket:.
	make 'composer-install --optimize-autoloader --ignore-platform-reqs'
	docker build --tag graze/array-filter:latest .

composer-%: ## Run a composer command, `make "composer-<command> [...]"`.
	docker run -t --rm \
    -v $$(pwd):/app \
    -v ~/.composer:/root/composer \
    -v ~/.ssh:/root/.ssh:ro \
    composer/composer --ansi --no-interaction $* $(filter-out $@,$(MAKECMDGOALS))

test: ## Run the unit and integration testsuites.
test: lint test-matrix

lint: ## Run phpcs against the code.
	docker run --rm -t -v $$(pwd):/opt/graze/arrayFilter graze/array-filter \
	composer lint --ansi

test-coverage:
	docker run --rm -t -v $$(pwd):/opt/graze/arrayFilter graze/array-filter:latest \
	composer test:coverage --ansi

test-coverage-clover:
	docker run --rm -t -v $$(pwd):/opt/graze/arrayFilter graze/array-filter:latest \
	composer test:coverage-clover --ansi

test-unit: ## Run the unit testsuite.
	docker run --rm -t -v $$(pwd):/opt/graze/arrayFilter graze/array-filter \
	composer test:unit --ansi

test-matrix:
	docker run --rm -t -v $$(pwd):/opt/graze/arrayFilter graze/array-filter:latest \
	vendor/bin/phpunit --testsuite unit

clean: ## Clean up any images.
	docker rmi graze/array-filter:latest

run: ## Run a command on the docker image
	docker run --rm -t -v $$(pwd):/opt/graze/arrayFilter graze/array-filter:latest \
	$(filter-out $@,$(MAKECMDGOALS))

help: ## Show this help message.
	echo "usage: make [target] ..."
	echo ""
	echo "targets:"
	fgrep --no-filename "##" $(MAKEFILE_LIST) | fgrep --invert-match $$'\t' | sed -e 's/: ## / - /'
