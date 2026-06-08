// assets/js/anchor-scroll.js
document.addEventListener('DOMContentLoaded', function() {
    
    // Функция плавной прокрутки с контролем скорости
    function smoothScrollToElement(targetElement, duration = 1000, offset = 0) {
        if (!targetElement) return;
        
        const startPosition = window.pageYOffset;
        const targetPosition = targetElement.getBoundingClientRect().top + startPosition - offset;
        const distance = targetPosition - startPosition;
        let startTime = null;
        
        function easeInOutCubic(t) {
            return t < 0.5 
                ? 4 * t * t * t 
                : 1 - Math.pow(-2 * t + 2, 3) / 2;
        }
        
        function animation(currentTime) {
            if (startTime === null) startTime = currentTime;
            const timeElapsed = currentTime - startTime;
            const progress = Math.min(timeElapsed / duration, 1);
            const easeProgress = easeInOutCubic(progress);
            
            window.scrollTo(0, startPosition + (distance * easeProgress));
            
            if (timeElapsed < duration) {
                requestAnimationFrame(animation);
            }
        }
        
        requestAnimationFrame(animation);
    }
    
    // Очистка URL от хэша без перезагрузки страницы
    function removeHashFromURL() {
        if (window.location.hash) {
            history.pushState(null, null, window.location.pathname + window.location.search);
        }
    }
    
    // Обработка кликов по якорным ссылкам
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#' || targetId === '#/') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                // Удаляем хэш из URL
                removeHashFromURL();
                
                const headerHeight = document.querySelector('.site-header')?.offsetHeight || document.querySelector('header')?.offsetHeight || 0;
                const extraOffset = 20;
                const totalOffset = headerHeight + extraOffset;
                
                // duration - время прокрутки в миллисекундах (1000 = 1 секунда)
                smoothScrollToElement(targetElement, 1500, totalOffset);
            }
        });
    });
    
    // Прокрутка при загрузке с якорем в URL
    if (window.location.hash) {
        setTimeout(() => {
            const targetElement = document.querySelector(window.location.hash);
            if (targetElement) {
                const headerHeight = document.querySelector('.site-header')?.offsetHeight || document.querySelector('header')?.offsetHeight || 0;
                const extraOffset = 20;
                const totalOffset = headerHeight + extraOffset;
                smoothScrollToElement(targetElement, 1200, totalOffset);
                
                // После прокрутки удаляем хэш из URL
                setTimeout(() => {
                    removeHashFromURL();
                }, 1300);
            }
        }, 200);
    }
    
    // Дополнительно: при любом изменении хэша (если кто-то вручную его вставил)
    window.addEventListener('hashchange', function() {
        removeHashFromURL();
        const targetElement = window.location.hash ? document.querySelector(window.location.hash) : null;
        if (targetElement) {
            const headerHeight = document.querySelector('.site-header')?.offsetHeight || document.querySelector('header')?.offsetHeight || 0;
            const extraOffset = 20;
            const totalOffset = headerHeight + extraOffset;
            smoothScrollToElement(targetElement, 1200, totalOffset);
        }
    });
});