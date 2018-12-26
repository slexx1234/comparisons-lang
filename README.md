# Comparisons language

Это мини язык для задания условий сравнения к уже имеющимся данным. К идее меня
сподвигло постоянное написание схожих между собой фильтров для `GET` запросов
минимальное число, максимальное, интервал даты или конкретная дата использовать я
это буду при создании фильтров для `GET` запросов `?example=>5&<=10`.

Язык очень прост, я взял обычный синтаксис `WHERE SQL` и выкинул из него всё не нужное, к примеру
название колонок (она указывается только один раз), длинные слова `AND` был заменён на `&`, а
`OR` на `|`.

## Типы данных

В языке присуцтвуют несколько типов данных:

| Название типа | Пример              | Регулярное выражение               |
|---------------|---------------------|------------------------------------|
| **DATE_TIME** | 2018-12-26 23:00:00 | `\d{4}-\d{2}-\d{2} \d\d:\d\d:\d\d` |
| **DATE**      | 2018-12-26          | `\d{4}-\d{2}-\d{2}`                |
| **FLOAT**     | 0.1                 | `-?\d\.\d+`                        |
| **INT**       | 777                 | `-?\d+`                            |

Во всём выражении может использоваться только один тип данных, он зарание должен
быть известен, наглухо зашит в код. При попытке сравнения числа и даты будет
вызвано исключение.

## Операторы сравнения

Доступны следующие операторы сравнения:

| Оператор | Описание                                               |
|----------|--------------------------------------------------------|
| `=`      | Проверяет равны ли входные данные сравниваемым         |
| `!=`     | Проверяет не равны ли входные данные сравниваемым      |
| `>`      | Проверяет входные данные больше сравниваемых           |
| `>=`     | Проверяет входные данные больше или равны сравниваемым |
| `<`      | Проверяет входные данные являются меньше сравниваемых  |
| `<=`     | Проверяет входные данные меньше или равны сравниваемым |

Каждый оператор сравнения всегда пишется перед входными параметрами: `>5`, `=5`...

## Логические операторы

Для задания нескольких отдельных условий есть логические операторы.

| Оператор | Описание                                          |
|----------|---------------------------------------------------|
| `&`      | Левый и правый операнд должны возвращать правд    |
| `|`      | Левый или правый операнд должны возвращать правду |

## Группировка

Группировка условий осуществляется с помощью круглых скобок `()` как в матемытике или
в любом языке программирования. Здесь кроется небольшой подвох, возможности группировки
сильно ограничены, группы не могут быть вложенными друг в друга `(>6&(<4&>-44))` - это
вызовет исключение.

## Установка

Установите пакет [composer](http://composer.org/):
```bash
composer require slexx/comparisons-language
```

Используйте в вашем `php`.
```php
<?php

require 'vendor/autoload,php';

use Slexx\CL\CL;
use Slexx\CL\Tokenizer;

$parser = new CL('>=18&<40', Tokenizer::T_INT);

var_dump($parser->compileToPHP('$age')); // "$age >= 18 && $age < 40"
var_dump($parser->compileToSQL('users', 'age')); // "`users`.`age` >= 18 AND `users`.`age` < 40"
```

Я думаю что это неплохая оптимизация учитывая что входная строка 8 символов, а `SQL` вариант аж 42!

## Laravel

Для использования вместе с фремворком [laravel](https://laravel.com/) просто добывте в ваш
файл `config/app.php` одну строку:

```php
'providers' => [
    // ...
    Slexx\CL\LaravelServiceProvider::class,
];
```

Провайдер добавить однин очень полезный миксин `CLFilter` в Query Builder, он позволит использовать
*Comparison Language* прям при генерации запросов:

```php
Users::CLFilter('age', '>=18&<40', 'int')->get();
```

А вот пример как это будет выглядеть по моеё задумке с `GET` параметрами:

```php
$query = Users::query();

if (Request::has('birthday')) {
    $query->CLFilter('birthday', Request::get('birthday'), 'date');
}

if (Request::has('created_at')) {
    $query->CLFilter('created_at', Request::get('created_at'), 'date_time');
}

if (Request::has('rating')) {
    $query->CLFilter('rating', Request::get('rating'), 'int');
}

$users = $query->get();
```

Вся остальныя волокита с фильтрами вроде минимальный возраст, максимальный переходит к
фронтендеру, ему остаётся лишь передать нужный оператор через `API`.

В миксин принимаются следующие аргументы:

| Имя      | Тип            | Описание                                                                                                              |
|----------|----------------|-----------------------------------------------------------------------------------------------------------------------|
| `$field` | `string|array` | Имя колонки к фильтрации, можно отделить имя таблицы точкой `table.column` или передать массив `['table', 'column']`. |
| `$input` | `string`       | Строка Comparisons Language                                                                                           |
| `$type`  | `string`       | Тип данных к сравнению `int`, `integer`, `float`, `double`, `date`, `datetime`, `date_time` или `date-time`           |