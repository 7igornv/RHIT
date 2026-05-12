<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ООО "РХИТ"</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/header.css">
</head>
<body>
    <header class="site-header">
        <div class="logo"><a class="logo-link" href="#hero">ООО "РХИТ"</a></div>
        
        <ul class="nav-list">
            <li><a class="header-link" href="#services">Услуги</a></li>
            <li><a class="header-link" href="#competencies">Компетенции</a></li>
            <li><a class="header-link" href="#clients">Клиенты</a></li>
            <li><a class="header-link" href="https://it.ruschem.ru/licenses">Лицензии</a></li>
            <li><a class="header-link" href="https://it.ruschem.ru/labor_protection">СОУТ</a></li>
            <li><a class="header-link" href="#footer">Контакты</a></li>
        </ul>

        <button class="header-btn">Оставить заявку</button>
        
        <div class="burger-menu">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </header>
    <script src="blocks/Header/header.js"></script>
    <script src="assets/js/anchor-scroll.js"></script>

    <script>
        const burger = document.querySelector('.burger-menu');
        const navList = document.querySelector('.nav-list');
        
        burger.addEventListener('click', function() {
            this.classList.toggle('active');
            navList.classList.toggle('active');
        });
        
        // Закрываем меню при клике на ссылку
        document.querySelectorAll('.header-link').forEach(link => {
            link.addEventListener('click', () => {
                burger.classList.remove('active');
                navList.classList.remove('active');
            });
        });
    </script>
</body>
</html>