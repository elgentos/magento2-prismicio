
GH_REPO = https://github.com/elgentos/magento2-prismicio

PHP_VERSION = php81

PHP = php
COMPOSER = composer2

COMPOSER_ARGUMENTS = --prefer-dist --optimize-autoloader

PHPUNIT_ARGUMENTS = 

IDE = phpstorm

MKDOCS_DOCKER_IMAGE = mkdocs
MKDOCS_DOCKER_RUN = docker run --rm -v $(PWD):/mnt --workdir=/mnt
MKDOCS_COMMAND = $(MKDOCS_DOCKER_RUN) $(MKDOCS_DOCKER_IMAGE)
MKDOCS_ARGUMENTS = 

all: vendor/autoload.php .$(PHP_VERSION)

.$(PHP_VERSION):
	@echo 'This stopfile works for https://github.com/jeroenboersma/docker-compose-development'
	touch .php81

help:
	@echo 'Available make commands'
	@echo '$(MAKE) # get you up and running'
	@echo '$(MAKE) vendor/autoload.php # install packages'
	@echo '$(MAKE) update # update packages'
	@echo '$(MAKE) test # run tests'
	@echo '$(MAKE) clean # remove generated files'
	@echo '$(MAKE) build # build the docs'
	@echo '$(MAKE) serve # serve the docs'
	@echo '$(MAKE) web # open Github'

.PHONY: clean
clean:
	rm -rf vendor
	rm composer.lock
	rm .$(PHP_VERSION)
	docker rmi $(MKDOCS_DOCKER_IMAGE)

vendor/autoload.php:
	$(COMPOSER) install $(COMPOSER_ARGUMENTS)

.PHONY: update
update:
	$(COMPOSER) update $(COMPOSER_ARGUMENTS)

.PHONY: test
test: vendor/autoload.php
	$(PHP) vendor/bin/phpunit $(PHPUNIT_ARGUMENTS)

.PHONY: mkdocs_image
mkdocs_image:
ifeq ($(shell docker images -q $(MKDOCS_DOCKER_IMAGE) 2>/dev/null),)
	docker build --tag=$(MKDOCS_DOCKER_IMAGE) .
endif

.PHONY: docs
docs: mkdocs_image
	$(MKDOCS_COMMAND) $(MKDOCS_ARGUMENTS)

.PHONY: build
build:
	@$(MAKE) docs MKDOCS_ARGUMENTS='gh-deploy $(MKDOCS_ARGUMENTS)' MKDOCS_DOCKER_RUN='$(MKDOCS_DOCKER_RUN) -e SSH_AUTH_SOCK=/ssh-agent -v $(SSH_AUTH_SOCK):/ssh-agent -it'

.PHONY: serve
serve:
	@$(MAKE) docs MKDOCS_DOCKER_RUN='$(MKDOCS_DOCKER_RUN) -it -p 127.0.0.1:8000:8000' MKDOCS_ARGUMENTS='serve -a 0.0.0.0:8000 --livereload $(MKDOCS_ARGUMENTS)'

.PHONY: ide
ide:
	$(IDE) . &

.PHONY: web
web:
	open  $(GH_REPO)
