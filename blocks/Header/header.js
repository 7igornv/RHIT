// Оптимизированная умная шапка
(function() {
    'use strict';
    
    // Элементы
    const header = document.querySelector('.site-header');
    if (!header) return;
    
    // Переменные
    let lastScrollY = 0;
    let ticking = false;
    let isHidden = false;
    
    // Функция обработки скролла
    function handleScroll() {
        const currentScrollY = window.pageYOffset;
        
        // В самом верху - всегда показываем
        if (currentScrollY <= 10) {
            if (isHidden) {
                header.classList.remove('hide');
                header.classList.add('show');
                isHidden = false;
            }
            lastScrollY = currentScrollY;
            ticking = false;
            return;
        }
        
        // Определяем направление
        const isScrollingDown = currentScrollY > lastScrollY;
        const isScrolledEnough = currentScrollY > 100;
        
        // Логика показа/скрытия
        if (isScrollingDown && isScrolledEnough && !isHidden) {
            // Скроллим вниз - скрываем
            header.classList.remove('show');
            header.classList.add('hide');
            isHidden = true;
        } 
        else if (!isScrollingDown && isHidden) {
            // Скроллим вверх - показываем
            header.classList.remove('hide');
            header.classList.add('show');
            isHidden = false;
        }
        
        lastScrollY = currentScrollY;
        ticking = false;
    }
    
    // Оптимизированный слушатель скролла
    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(handleScroll);
            ticking = true;
        }
    }, { passive: true }); // Пассивный режим для производительности
    
    // Показываем шапку при наведении в верхнюю часть экрана (опционально)
    let hoverTimeout;
    let isNearTop = false;
    window.addEventListener('mousemove', (e) => {
        const currentlyNearTop = e.clientY < 50;
        if (e.clientY < 50 && !isNearTop && isHidden) {
            clearTimeout(hoverTimeout);
            hoverTimeout = setTimeout(() => {
                header.classList.remove('hide');
                header.classList.add('show');
                isHidden = false;
            }, 150);
            isNearTop = true;
        }
        else if (!currentlyNearTop && isNearTop) {
            clearTimeout(hoverTimeout);
            isNearTop = false;
            isNearTop = false;
        }   
    });
    
})();