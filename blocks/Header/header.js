(function () {
  'use strict';
  
  const header = document.querySelector('.site-header');
  const burger = document.querySelector('.burger-menu');
  const navList = document.querySelector('.nav-list');
  
  if (!header || !burger || !navList) return;

  // Бургер-меню
  function toggleMenu() {
    const isOpen = navList.classList.toggle('active');
    burger.classList.toggle('active');
    document.body.style.overflow = isOpen ? 'hidden' : '';
  }

  burger.addEventListener('click', (e) => {
    e.stopPropagation();
    toggleMenu();
  });

  // Закрытие при клике на ссылку
  navList.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      navList.classList.remove('active');
      burger.classList.remove('active');
      document.body.style.overflow = '';
    });
  });

  // Закрытие при клике вне меню
  document.addEventListener('click', (e) => {
    if (navList.classList.contains('active') && !navList.contains(e.target) && !burger.contains(e.target)) {
      navList.classList.remove('active');
      burger.classList.remove('active');
      document.body.style.overflow = '';
    }
  });

  // Умный скролл
  let lastScrollY = 0;
  let ticking = false;
  let isHidden = false;

  function handleScroll() {
    const currentScrollY = window.pageYOffset;

    if (currentScrollY <= 10) {
      if (isHidden) {
        header.classList.remove('hide');
        header.classList.add('show');
        isHidden = false;
      }
    } else {
      const isScrollingDown = currentScrollY > lastScrollY;
      
      if (isScrollingDown && currentScrollY > 100 && !isHidden) {
        header.classList.remove('show');
        header.classList.add('hide');
        isHidden = true;
      } else if (!isScrollingDown && isHidden) {
        header.classList.remove('hide');
        header.classList.add('show');
        isHidden = false;
      }
    }

    lastScrollY = currentScrollY;
    ticking = false;
  }

  window.addEventListener('scroll', () => {
    if (!ticking) {
      requestAnimationFrame(handleScroll);
      ticking = true;
    }
  }, { passive: true });

  // Показ при наведении в верхнюю зону
  let hoverTimeout;
  let isNearTop = false;
  
  window.addEventListener('mousemove', (e) => {
    const currentlyNearTop = e.clientY < 50;
    if (currentlyNearTop && !isNearTop && isHidden) {
      clearTimeout(hoverTimeout);
      hoverTimeout = setTimeout(() => {
        header.classList.remove('hide');
        header.classList.add('show');
        isHidden = false;
      }, 150);
      isNearTop = true;
    } else if (!currentlyNearTop && isNearTop) {
      clearTimeout(hoverTimeout);
      isNearTop = false;
    }
  });
})();