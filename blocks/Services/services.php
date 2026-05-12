<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Наши услуги</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/services.css">
</head>
<body>
    <div id="services" class="services-container">
        <h2>Наши услуги</h2>
        
        <div class="carousel-stage" id="carouselStage">
            <div class="carousel-track" id="mainTrack">
                <div class="services-cell">
                    <div class="services-cell-text" lang="ru">IT-консалтинг</div>
                    <img class="services-cell-img" src="/assets/img/services-img-1.webp" alt="IT-консалтинг">
                </div>
                <div class="services-cell">
                    <div class="services-cell-text" lang="ru">Разработка программного обеспечения</div>
                    <img class="services-cell-img" src="/assets/img/services-img-2.webp" alt="Разработка ПО">
                </div>
                <div class="services-cell">
                    <div class="services-cell-text" lang="ru">Интеграция и поддержка</div>
                    <img class="services-cell-img" src="/assets/img/services-img-3.webp" alt="Интеграция">
                </div>
                <div class="services-cell">
                    <div class="services-cell-text" lang="ru">Кибербезопасность</div>
                    <img class="services-cell-img" src="/assets/img/services-img-4.webp" alt="Кибербезопасность">
                </div>
                <div class="services-cell">
                    <div class="services-cell-text" lang="ru">Big Data и аналитика</div>
                    <img class="services-cell-img" src="/assets/img/services-img-5.webp" alt="Big Data">
                </div>
                <div class="services-cell">
                    <div class="services-cell-text" lang="ru">Облачные решения</div>
                    <img class="services-cell-img" src="/assets/img/services-img-1.webp" alt="Облачные решения">
                </div>
            </div>
        </div>

        <div class="thumb-stage" id="thumbStage">
            <div class="thumb-frame">
                
                <button class="frame-btn prev" id="thumbPrev">‹</button>
                
                <div class="thumb-viewport">
                    <div class="thumb-track" id="thumbTrack"></div>
                </div>
                
                <button class="frame-btn next" id="thumbNext">›</button>
                
            </div>
        </div>
    </div>
    
    <script src="blocks/Services/services.js"></script>
</body>
</html>