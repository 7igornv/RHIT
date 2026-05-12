document.addEventListener('DOMContentLoaded', function() {
    // ===== DOM ELEMENTS =====
    const mainTrack = document.getElementById('mainTrack');
    const mainCells = document.querySelectorAll('.services-cell');
    const carouselStage = document.getElementById('carouselStage');
    
    const thumbTrack = document.getElementById('thumbTrack');
    const thumbPrev = document.getElementById('thumbPrev');
    const thumbNext = document.getElementById('thumbNext');
    
    // ===== CONFIGURATION =====
    let currentIndex = 0;
    let visibleCount = 4;
    let cellWidth = 291;
    let gap = 20;
    let step = cellWidth + gap;
    let thumbItemFullWidth = 53;
    let maxIndex = 0;
    let isAnimating = false;
    let animationTimeout;
    
    // ===== DRAG & DROP STATE =====
    let isDragging = false;
    let startX;
    let startIndex;
    
    // ===== TOUCH STATE =====
    let touchStartX = 0;
    let touchEndX = 0;
    
    // ===== UPDATE LAYOUT METRICS =====
    function updateMetrics() {
        const stageWidth = carouselStage.offsetWidth;
        
        if (stageWidth < 600) {
            visibleCount = 1;
            cellWidth = stageWidth - 40;
        } else if (stageWidth < 900) {
            visibleCount = 2;
            cellWidth = (stageWidth - 60) / 2;
        } else if (stageWidth < 1200) {
            visibleCount = 3;
            cellWidth = (stageWidth - 80) / 3;
        } else {
            visibleCount = 4;
            cellWidth = 291;
        }
        
        step = cellWidth + gap;
        
        mainCells.forEach(cell => {
            cell.style.flex = `0 0 ${cellWidth}px`;
            cell.style.width = `${cellWidth}px`;
        });
        
        updateMaxIndex();
    }
    
    // ===== РАСЧЁТ МАКСИМАЛЬНОГО ИНДЕКСА =====
    function updateMaxIndex() {
        const totalWidth = mainCells.length * step;
        const stageWidth = carouselStage.offsetWidth;
        
        const maxScroll = totalWidth - stageWidth;
        maxIndex = Math.ceil(maxScroll / step);
        maxIndex = Math.min(maxIndex, mainCells.length - 1);
        maxIndex = Math.max(0, maxIndex);
    }
    
    // ===== CREATE THUMBNAILS =====
    function createThumbnails() {
        thumbTrack.innerHTML = '';
        
        mainCells.forEach((cell, index) => {
            const thumb = document.createElement('div');
            thumb.className = 'thumb-item inactive';
            thumb.dataset.index = index;
            
            const img = cell.querySelector('.services-cell-img').cloneNode(true);
            thumb.appendChild(img);
            
            thumbTrack.appendChild(thumb);
        });
    }
    
    // ===== СИНХРОНИЗАЦИЯ МИНИАТЮР =====
    function syncThumbnails() {
        let thumbOffset = -(currentIndex * thumbItemFullWidth);
        
        const trackWidth = thumbTrack.scrollWidth;
        const viewportWidth = 200;
        const minOffset = viewportWidth - trackWidth;
        const maxOffset = 0;
        
        let finalOffset = thumbOffset;
        if (finalOffset > maxOffset) finalOffset = maxOffset;
        if (finalOffset < minOffset) finalOffset = minOffset;
        
        thumbTrack.style.transform = `translateX(${finalOffset}px)`;
    }
    
    // ===== UPDATE ACTIVE STATE =====
    function updateThumbnailsActiveState() {
        const thumbItems = document.querySelectorAll('.thumb-item');
        
        thumbItems.forEach((item, index) => {
            const isActive = (index >= currentIndex && index < currentIndex + visibleCount);
            if (isActive) {
                item.classList.remove('inactive');
                item.classList.add('active');
            } else {
                item.classList.remove('active');
                item.classList.add('inactive');
            }
        });
    }
    
    // ===== UPDATE BUTTONS STATE =====
    function updateButtonsState() {
        if (thumbPrev) thumbPrev.disabled = (currentIndex === 0);
        if (thumbNext) thumbNext.disabled = (currentIndex >= maxIndex);
    }
    
    // ===== UPDATE CAROUSEL =====
    function updateCarousel() {
        updateMaxIndex();
        currentIndex = Math.max(0, Math.min(currentIndex, maxIndex));
        
        const mainOffset = -(currentIndex * step);
        
        mainTrack.style.transform = `translateX(${mainOffset}px)`;
        
        syncThumbnails();
        updateButtonsState();
        updateThumbnailsActiveState();
        
        if (animationTimeout) clearTimeout(animationTimeout);
        isAnimating = true;
        animationTimeout = setTimeout(() => {
            isAnimating = false;
        }, 600);
    }
    
    // ===== NAVIGATION =====
    function nextSlide() {
        if (isAnimating) return;
        if (currentIndex < maxIndex) {
            currentIndex++;
            updateCarousel();
        }
    }
    
    function prevSlide() {
        if (isAnimating) return;
        if (currentIndex > 0) {
            currentIndex--;
            updateCarousel();
        }
    }
    
    // ===== DRAG & DROP =====
    function handleDragStart(e) {
        e.preventDefault();
        isDragging = true;
        startX = e.pageX - carouselStage.offsetLeft;
        startIndex = currentIndex;
        carouselStage.style.cursor = 'grabbing';
    }
    
    function handleDragMove(e) {
        if (!isDragging) return;
        e.preventDefault();
        
        const x = e.pageX - carouselStage.offsetLeft;
        const walk = (x - startX) * 1.5;
        const deltaIndex = Math.round(walk / step);
        
        let newIndex = startIndex - deltaIndex;
        newIndex = Math.min(maxIndex, Math.max(0, newIndex));
        
        if (newIndex !== currentIndex) {
            currentIndex = newIndex;
            updateCarousel();
        }
    }
    
    function handleDragEnd() {
        isDragging = false;
        carouselStage.style.cursor = 'grab';
    }
    
    // ===== MOUSE WHEEL & TOUCHPAD =====
    let wheelTimeout;
    function handleWheelScroll(e) {
        e.preventDefault();
        if (isAnimating) return;
        if (wheelTimeout) clearTimeout(wheelTimeout);
        
        const delta = e.deltaY !== 0 ? e.deltaY : e.deltaX;
        
        if (delta > 0) {
            nextSlide();
        } else if (delta < 0) {
            prevSlide();
        }
        
        wheelTimeout = setTimeout(() => {}, 200);
    }
    
    // ===== TOUCH EVENTS =====
    function handleTouchStart(e) {
        touchStartX = e.changedTouches[0].screenX;
    }
    
    function handleTouchEnd(e) {
        touchEndX = e.changedTouches[0].screenX;
        const deltaX = touchEndX - touchStartX;
        
        if (Math.abs(deltaX) > 50) {
            if (deltaX > 0) {
                prevSlide();
            } else {
                nextSlide();
            }
        }
    }
    
    // ===== DISABLE SELECTION =====
    function disableSelection() {
        const servicesBlock = document.querySelector('.services-container');
        if (servicesBlock) {
            servicesBlock.addEventListener('selectstart', (e) => e.preventDefault());
            servicesBlock.addEventListener('dragstart', (e) => e.preventDefault());
        }
    }
    
    // ===== RESIZE =====
    let resizeTimer;
    function handleResize() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            updateMetrics();
            updateCarousel();
        }, 150);
    }
    
    // ===== INIT =====
    function init() {
        updateMetrics();
        createThumbnails();
        updateCarousel();
        disableSelection();
        carouselStage.style.cursor = 'grab';
    }
    
    // ===== EVENT LISTENERS =====
    if (thumbNext) thumbNext.addEventListener('click', nextSlide);
    if (thumbPrev) thumbPrev.addEventListener('click', prevSlide);
    
    carouselStage.addEventListener('mousedown', handleDragStart);
    window.addEventListener('mousemove', handleDragMove);
    window.addEventListener('mouseup', handleDragEnd);
    carouselStage.addEventListener('wheel', handleWheelScroll, { passive: false });
    carouselStage.addEventListener('touchstart', handleTouchStart);
    carouselStage.addEventListener('touchend', handleTouchEnd);
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight') nextSlide();
        if (e.key === 'ArrowLeft') prevSlide();
    });
    
    window.addEventListener('resize', handleResize);
    
    init();
});