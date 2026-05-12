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
                            required
                        >
                    </div>

                    <div class="feedback-form-group">
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            class="feedback-form-input" 
                            placeholder="Номер телефона"
                            required
                        >
                    </div>

                    <div class="feedback-form-group">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="feedback-form-input" 
                            placeholder="Электронная почта"
                            required
                        >
                    </div>

                    <div class="feedback-form-group">
                        <textarea 
                            id="message" 
                            name="message" 
                            class="feedback-form-input feedback-form-textarea" 
                            placeholder="Ваше сообщение"
                            rows="4"
                            required
                        ></textarea>
                    </div>

                    <div class="feedback-form-group feedback-checkbox-group">
                        <label class="feedback-checkbox-label">
                            <input 
                                type="checkbox" 
                                id="agreement" 
                                name="agreement"
                                required
                            >
                            <span class="feedback-checkmark"></span>
                            <span class="feedback-checkbox-text">я соглашаюсь на обработку моих персональных данных согласно условиям, определенным Политикой в отношении обработки ПД ООО "РХИТ"</span>
                        </label>
                    </div>

                    <button type="submit" class="feedback-btn">Оставить заявку</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('feedbackForm').addEventListener('submit', async function(e) {
            e.preventDefault(); 
            
            const formData = {
                name: document.getElementById('name').value,
                phone: document.getElementById('phone').value,
                email: document.getElementById('email').value,
                message: document.getElementById('message').value,
                agreement: document.getElementById('agreement').checked
            };
            
            if (!formData.agreement) {
                alert('Пожалуйста, согласитесь на обработку персональных данных');
                return;
            }
            
           
            console.log('Отправка данных:', formData);
            
            try {
                const response = await fetch('send_feedback.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });
                
                if (response.ok) {
                    alert('Сообщение отправлено успешно!');
                    this.reset();
                } else {
                    alert('Ошибка при отправке');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при отправке');
            }
            
            alert('Форма отправлена! (демо-режим)');
            this.reset();
        });
    </script>
</body>
</html>