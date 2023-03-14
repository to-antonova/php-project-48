# Makefile
install: # установить зависимости
	composer install
gendiff: # запуск php-файла
	./bin/gendiff
validate: # проверка файла composer.json
	composer validate
