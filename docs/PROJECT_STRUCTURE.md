# Полная структура проекта

```text
blogpm09/
├── .gitignore
├── README.md
├── schema.sql
├── index.php
├── post.php
├── register.php
├── login.php
├── logout.php
├── add_comment.php
├── config/
│   ├── bootstrap.php
│   ├── db.php
│   ├── db.credentials.example.php
│   └── helpers.php
├── templates/
│   ├── header.php
│   └── footer.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── main.js
├── admin/
│   ├── posts.php
│   ├── post_form.php
│   ├── delete_post.php
│   ├── comments.php
│   └── delete_comment.php
├── uploads/
│   └── .gitkeep
└── docs/
    ├── PROJECT_STRUCTURE.md
    ├── DB_SETUP.md
    ├── XAMPP_SETUP.md
    └── BEGET_DEPLOY.md
```

## Кратко по файлам
- `schema.sql` — SQL-структура БД (`users`, `posts`, `comments`).
- `index.php` — главная лента постов карточками с пагинацией.
- `post.php` — страница полного поста и комментариев.
- `register.php`, `login.php`, `logout.php` — модуль аутентификации.
- `add_comment.php` — AJAX endpoint для добавления комментария.
- `config/db.php` — подключение PDO к MySQL.
- `config/db.credentials.example.php` — шаблон локальных кредов для Beget/хостинга.
- `config/helpers.php` — хелперы (`e`, `csrfToken`, `isAdmin`, ...).
- `templates/header.php`, `templates/footer.php` — общий каркас страниц.
- `admin/*` — админ-модуль (CRUD постов + модерация комментариев).
- `assets/css/style.css` — современный адаптивный CSS.
- `assets/js/main.js` — JS: mobile-menu, лайки, AJAX-комментарии.
- `docs/*` — инструкции запуска и деплоя.
