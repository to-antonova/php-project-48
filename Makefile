# Makefile
install: # установить зависимости
	composer install
validate: # проверка файла composer.json
	composer validate
gendiff: # запуск php-файла
	./bin/gendiff

diff-json-example: # сравнить file1.json и file2.json
	./bin/gendiff tests/fixtures/file1.json tests/fixtures/file2.json
diff-yml-example: # сравнить file3.yml и file4.yml
	./bin/gendiff tests/fixtures/file3.yml tests/fixtures/file4.yml
diff-recursive-example: # сравнить file5.json и file6.json
	./bin/gendiff tests/fixtures/file5.json tests/fixtures/file6.json
diff-plain-example: # плоский формат
	./bin/gendiff --format plain tests/fixtures/file5.json tests/fixtures/file6.json

lint: # запуск phpcs
	composer exec --verbose phpcs -- --standard=PSR12 src bin tests
test: # запуск тестов
	composer exec --verbose phpunit tests
test-coverage: # покрытие тестами
# 	./vendor/bin/phpunit --coverage-text
# 	composer exec --verbose phpunit tests -- --coverage-text
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml
