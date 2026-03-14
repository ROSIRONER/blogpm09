# Как создать БД `blog` и применить `schema.sql`

Ниже два удобных способа: через phpMyAdmin и через MySQL CLI.

---

## Способ 1: через phpMyAdmin

1. Запустите MySQL (например, через XAMPP Control Panel).
2. Откройте `http://localhost/phpmyadmin`.
3. Нажмите **Создать БД** (New).
4. Введите имя БД: `blog`.
5. Выберите кодировку: `utf8mb4_general_ci` (или `utf8mb4_unicode_ci`).
6. Нажмите **Создать**.
7. Перейдите в созданную БД `blog`.
8. Откройте вкладку **SQL**.
9. Скопируйте содержимое файла `schema.sql` и вставьте в окно SQL.
10. Нажмите **Вперед / Выполнить**.
11. Проверьте, что созданы таблицы:
    - `users`
    - `posts`
    - `comments`

---

## Способ 2: через командную строку (MySQL CLI)

### 1) Подключитесь к MySQL
```bash
mysql -u root -p
```
(Введите пароль пользователя MySQL.)

### 2) Создайте БД
```sql
CREATE DATABASE blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3) Выйдите из MySQL
```sql
exit;
```

### 4) Импортируйте schema.sql
В каталоге проекта выполните:
```bash
mysql -u root -p blog < schema.sql
```

### 5) Проверка
Снова зайдите в MySQL:
```bash
mysql -u root -p blog
```
И выполните:
```sql
SHOW TABLES;
```

---

## Настройки подключения в проекте
Файл: `config/db.php`

По умолчанию:
- DB_HOST = `127.0.0.1`
- DB_NAME = `blog`
- DB_USER = `root`
- DB_PASS = `` (пустой)

Можно переопределить через переменные окружения:
- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`
