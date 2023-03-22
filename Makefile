# Makefile
install: # установить зависимости
	composer install
validate: # проверка файла composer.json
	composer validate
gendiff: # запуск php-файла
	./bin/gendiff
diff-1-2: # сравнить file1.json и file2.json
	./bin/gendiff tests/fixtures/file1.json tests/fixtures/file2.json
diff-3-4: # сравнить file3.json и file4.json
	./bin/gendiff tests/fixtures/file3.json tests/fixtures/file4.json
lint: # запуск phpcs
	composer exec --verbose phpcs -- --standard=PSR12 src bin
test: # запуск тестов
	composer exec --verbose phpunit tests
test-coverage: # покрытие тестами
# 	./vendor/bin/phpunit --coverage-text
	composer exec --verbose phpunit tests -- --coverage-text
