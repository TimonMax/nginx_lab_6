# Лабораторная работа №6: Работа с ClickHouse через PHP и Docker


## 👩‍💻 Автор
**ФИО:** Фаткин Артем Александрович  
**Группа:** 2ПМ-1  

---

## 📌 Описание задания
1. Изучить работу с нереляционной базой данных ClickHouse.
2. Научиться отправлять SQL-запросы через HTTP API с помощью Guzzle.
3. Создать таблицу для аналитических данных.
4. Сохранять и инициализировать тестовые данные в ClickHouse.
5. Выводить агрегированную статистику на странице.
6. Использовать классы PHP для работы с ClickHouse.
7. Работать с Docker-контейнерами: nginx, PHP-FPM и ClickHouse.
http://localhost:8080

---

## ⚙️ Как запустить проект

### 1. Клонировать репозиторий
```bash
git clone https://github.com/TimonMax/nginx_lab_6.git
cd nginx_lab_6
```
### 2. Запустить контейнеры Docker
```bash
docker-compose up -d --build
```
### 3. Открыть в браузере
```bash
http://localhost:8080
```
### 4. Проверка работы ClickHouse
```bash
http://localhost:8123/ping
```
## Содержимое проекта
```docker-compose.yml``` — описание сервиса Nginx

```Dockerfile``` — параметры для запуска

```www/composer.json``` — зависимости проекта и автозагрузка классов

```www/composer.lock``` — зафиксированные версии зависимостей

```www/db.php``` — константы для ДБ

```www/index.php``` — главная страница

```nginx/default.conf``` — файл для обработки PHP

```www/Helpers/ClientFactory.php`` — фабрика

```www/ClickhouseExample.php``` — класс для работы с ClickHouse