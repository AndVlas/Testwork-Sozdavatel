// Правила валидации
const rules = {
    text: (value, required) => !required || value.trim().length > 0,
    phone: (value, required) => {
        if (!required && !value.trim()) return true;
        const phoneRegex = /^(\+7|8)[\s\-]?\(?\d{3}\)?[\s\-]?\d{3}[\s\-]?\d{2}[\s\-]?\d{2}$|^\d{10,11}$/;
        return phoneRegex.test(value.trim());
    },
    email: (value, required) => {
        if (!required && !value.trim()) return true;
        const emailRegex = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
        return emailRegex.test(value.trim());
    }
};

const errorMessages = {
    text: 'Поле не может быть пустым',
    phone: 'Некорректный телефон (пример: +7 999 123-45-67)',
    email: 'Некорректный email (пример: name@mail.ru)'
};

const fields = ['name', 'phone', 'email', 'comment'];

// Проверка поля
function validateField(id) {
    const input = document.getElementById(id);
    const isValid = rules[input.dataset.type](input.value, input.dataset.required === 'true');
    const errorDiv = document.getElementById(`${id}Error`);

    input.classList.remove('error-input', 'success-input');
    
    if (!isValid) {
        input.classList.add('error-input');
        errorDiv.textContent = errorMessages[input.dataset.type];
        errorDiv.classList.remove('hidden');
    } else {
        input.classList.add('success-input');
        errorDiv.classList.add('hidden');
        errorDiv.textContent = '';
    }
    
    return isValid;
}

// Валидация формы и показ результата
const form = document.getElementById('validatorForm');
const messageDiv = document.getElementById('form-message');

form?.addEventListener('submit', (e) => {
    e.preventDefault();
    let isFormValid = true;
    fields.forEach(id => {
        const isValid = validateField(id);
        if (!isValid) isFormValid = false
    })
    
    messageDiv.className = isFormValid ? 'success' : 'error';
    messageDiv.textContent = isFormValid ? 'Форма прошла валидацию' : 'Заполните все поля корректно';
    if (isFormValid) {
        const formData = fields.map(id => {
            const input = document.getElementById(id);
            return {
                field: id,
                value: input ? input.value : ''
            };
        });

        console.log('Данные формы:', formData);
    };
});

// Live-валидация
fields.forEach(id => {
    const input = document.getElementById(id);
    if (input) {
        const handler = () => validateField(id);
        input.addEventListener('input', handler);
    }
});