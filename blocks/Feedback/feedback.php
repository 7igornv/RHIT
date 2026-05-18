<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма обратной связи</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/feedback.css">
</head>
<body>
    <div class="feedback-section">
        <div class="feedback-container">
            <div class="feedback-right-section">
                <h2>Возникли вопросы?</h2>
                <form id="feedbackForm" class="feedback-form" method="POST" action="#">
                    <div class="feedback-form-group">
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="feedback-form-input" 
                            placeholder="Ваше имя"
                        >
                        <div class="error-message" id="nameError" style="color: #ff6b6b; font-size: 12px; margin-top: 5px;"></div>
                    </div>

                    <div class="feedback-form-group">
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            class="feedback-form-input" 
                            placeholder="Номер телефона"
                        >
                        <div class="error-message" id="phoneError" style="color: #ff6b6b; font-size: 12px; margin-top: 5px;"></div>
                    </div>

                    <div class="feedback-form-group">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="feedback-form-input" 
                            placeholder="Электронная почта"
                        >
                        <div class="error-message" id="emailError" style="color: #ff6b6b; font-size: 12px; margin-top: 5px;"></div>
                    </div>

                    <div class="feedback-form-group">
                        <textarea 
                            id="message" 
                            name="message" 
                            class="feedback-form-input feedback-form-textarea" 
                            placeholder="Ваше сообщение" 
                            rows="4"
                        ></textarea>
                        <div class="error-message" id="messageError" style="color: #ff6b6b; font-size: 12px; margin-top: 5px;"></div>
                    </div>

                    <div class="feedback-form-group feedback-checkbox-group">
                        <label class="feedback-checkbox-label">
                            <input 
                                type="checkbox" 
                                id="agreement" 
                                name="agreement"
                            >
                            <span class="feedback-checkmark"></span>
                            <span class="feedback-checkbox-text">я соглашаюсь на обработку моих персональных данных согласно условиям, определенным Политикой в отношении обработки ПД ООО "РХИТ"</span>
                        </label>
                        <div class="error-message" id="agreementError" style="color: #ff6b6b; font-size: 12px; margin-top: 5px;"></div>
                    </div>

                    <button type="submit" class="feedback-btn">Оставить заявку</button>
                </form>
            </div>
        </div>
    </div>

    <script src="blocks/Feedback/feedback.js"></script>
</body>
</html>