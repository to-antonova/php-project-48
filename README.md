#Вычислитель отличий
<hr>

### Hexlet tests and linter status:
[![Actions Status](https://github.com/to-antonova/php-project-48/workflows/hexlet-check/badge.svg)](https://github.com/to-antonova/php-project-48/actions)
[![PHP Composer](https://github.com/to-antonova/php-project-48/actions/workflows/my-check.yml/badge.svg)](https://github.com/to-antonova/php-project-48/actions/workflows/my-check.yml)
[![Maintainability](https://api.codeclimate.com/v1/badges/c92eedd6be1214d3964a/maintainability)](https://codeclimate.com/github/to-antonova/php-project-48/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/c92eedd6be1214d3964a/test_coverage)](https://codeclimate.com/github/to-antonova/php-project-48/test_coverage)

### Требования:

* PHP версии 7 и выше (https://www.php.net/downloads.php)
* Composer (https://getcomposer.org/download/)


### Установка:

$ git clone https://github.com/to-antonova/php-project-48.git

$ cd php-project-48

$ make install

### Описание:
Вычислитель отличий – программа, определяющая разницу между двумя структурами данных. Подобный механизм используется при выводе тестов или при автоматическом отслеживании изменении в конфигурационных файлах.

Возможности утилиты:
Поддержка разных входных форматов: yaml и json
Генерация отчета в виде plain text, stylish и json

##Примеры использования:

###Вывод справки и опции
[![asciicast](https://asciinema.org/a/606638.svg)](https://asciinema.org/a/606638)

###Сравнение плоских файлов JSON
<em>make flat-json-example</em>
[![asciicast](https://asciinema.org/a/606644.svg)](https://asciinema.org/a/606644)

###Сравнение плоских файлов YAML
<em>make flat-yml-example</em>
[![asciicast](https://asciinema.org/a/606643.svg)](https://asciinema.org/a/606643)

###Рекурсивное сравнение файлов JSON + вывод в формате по умолчанию stylish
<em>make stylish-example</em>
[![asciicast](https://asciinema.org/a/606645.svg)](https://asciinema.org/a/606645)

###Вывод в формате plain
<em>make plain-example</em>
[![asciicast](https://asciinema.org/a/606646.svg)](https://asciinema.org/a/606646)

###Вывод в формате json
<em>make json-format-example</em>
[![asciicast](https://asciinema.org/a/606647.svg)](https://asciinema.org/a/606647)
