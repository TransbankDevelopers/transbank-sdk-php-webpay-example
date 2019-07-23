APP=./src/
TESTS=./tests/

.PHONY: build clean stop kill

build:
	cd $(APP) && docker-compose build
	cd $(TESTS) && docker-compose build
	touch .built

start: .built
	@cd $(APP) && docker-compose up -d
	@echo "============================================\n🌎  Web server: http://localhost:9000/\n============================================"

test: .built
	cd $(APP) && docker-compose up --exit-code-from test

stop:
	cd $(APP) && docker-compose stop
	cd $(TESTS) && docker-compose stop

kill:
	cd $(APP) && docker-compose stop && docker-compose down
	cd $(TESTS) && docker-compose stop && docker-compose down

install: .built
	cd $(app) && docker-compose run --rm -w /var/www web composer install

update: .built
	cd $(app) && docker-compose run --rm -w /var/www web composer update

clean:
	rm .built