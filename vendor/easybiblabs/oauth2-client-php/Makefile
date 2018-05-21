ci: composer-validate phpcs phpmd phpunit

composer-validate:
	./composer.phar validate

phpcs:
	./vendor/bin/phpcs --standard=psr2 ./src
	./vendor/bin/phpcs --standard=psr2 ./tests

phpmd:
	./vendor/bin/phpmd src/ text codesize,controversial,design,naming,unusedcode

phpunit:
	./vendor/bin/phpunit
