# Vkontakte Wall Stat

Приложение, показывающее статистику записей, опубликованных подписчиками в сообществе на сайте vk.com.

## Возможности

- Отображение подписчиков в порядке убывания лайков к записям.
- Просмотр публикаций каждого участника.
- Фильтр по дате публикаций и по тексту, содержащемуся в посте.
- Форматирование результатов в текстовом виде для вставки в пост со ссылками на участников.

## Установка

`composer create-project silverslice/wall-stat`

## Настройка

- В `app/config/main.php` внести данные о сообществе.
- В `app/config/db.php` внести реквизиты доступа к mysql.

## Использование

- запустить `app/console wall:install` для установки таблиц в БД;
- запустить `app/console wall:update` для обновления базы постов через API vk.com;
- установить директорию `web` как webroot;
- при локальной разработке создать в корне проекта файл local для отображения ошибок.

## Разработка

- Фреймворк: silex.
- Шаблонизатор: twig.
- Консоль: symfony/console.
- DAO: silverslice/easydb.