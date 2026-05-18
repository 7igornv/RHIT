// Функция валидации телефона
function validatePhone(phone) {
    const cleanPhone = phone.replace(/[^0-9+]/g, '');
    return cleanPhone.length >= 10;
}

// Функция валидации email
function validateEmail(email) {
    const re = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
    return re.test(email);
}

// Очистка ошибок
function clearErrors() {
    document.querySelectorAll('.error-message').forEach(el => el.innerHTML = '');
}

// Показ ошибки
function showError(fieldId, message) {
    const errorElement = document.getElementById(fieldId + 'Error');
    if (errorElement) {
        errorElement.innerHTML = message;
    }
}

// Обработчик отправки формы
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('feedbackForm');
    if (!form) return;
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        clearErrors();
        
        const name = document.getElementById('name').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const email = document.getElementById('email').value.trim();
        const message = document.getElementById('message').value.trim();
        const agreement = document.getElementById('agreement').checked;
        
        let hasError = false;
        
        // Валидация имени
        if (name === '') {
            showError('name', 'Введите ваше имя');
            hasError = true;
        } else if (name.length < 2) {
            showError('name', 'Имя должно содержать минимум 2 символа');
            hasError = true;
        } else if (name.length > 100) {
            showError('name', 'Имя слишком длинное');
            hasError = true;
        }
        
        // Валидация телефона
        if (phone === '') {
            showError('phone', 'Введите номер телефона');
            hasError = true;
        } else if (!validatePhone(phone)) {
            showError('phone', 'Введите корректный номер телефона (минимум 10 цифр)');
            hasError = true;
        }
        
        // Валидация email
        if (email === '') {
            showError('email', 'Введите email');
            hasError = true;
        } else if (!validateEmail(email)) {
            showError('email', 'Введите корректный email (например: name@domain.com)');
            hasError = true;
        }
        
        // Валидация сообщения
        if (message === '') {
            showError('message', 'Введите сообщение');
            hasError = true;
        } else if (message.length < 10) {
            showError('message', 'Сообщение должно содержать минимум 10 символов');
            hasError = true;
        } else if (message.length > 5000) {
            showError('message', 'Сообщение слишком длинное');
            hasError = true;
        }
        
        // Валидация согласия
        if (!agreement) {
            showError('agreement', 'Необходимо согласие на обработку персональных данных');
            hasError = true;
        }
        
        if (hasError) {
            return;
        }
        
        const formData = { name, phone, email, message, agreement };
        
        try {
            const response = await fetch('/send_feedback.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('✅ ' + result.message);
                form.reset();
            } else {
                if (result.errors) {
                    for (const [field, error] of Object.entries(result.errors)) {
                        showError(field, error);
                    }
                    alert('❌ Пожалуйста, исправьте ошибки в форме');
                } else {
                    alert('❌ ' + (result.error || 'Ошибка при отправке'));
                }
            }
        } catch (error) {
            console.error('Ошибка:', error);
            alert('❌ Произошла ошибка при отправке. Попробуйте позже.');
        }
    });
    
    // Маска для телефона (только цифры и +)
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9+]/g, '');
        });
    }
});