// Подтверждение выхода
document.querySelector('.logout-btn')?.addEventListener('click', function(e) {
    if (!confirm('Вы уверены, что хотите выйти?')) {
        e.preventDefault();
    }
});

// Автоматическое скрытие уведомлений через 5 секунд
setTimeout(() => {
    document.querySelectorAll('.success').forEach(el => {
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 500);
    });
}, 5000);