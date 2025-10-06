// Form Validation
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            showError(input, 'این فیلد الزامی است');
            isValid = false;
        } else {
            clearError(input);
        }
    });
    
    // Email validation
    form.querySelectorAll('input[type="email"]').forEach(input => {
        if (input.value && !isValidEmail(input.value)) {
            showError(input, 'ایمیل نامعتبر است');
            isValid = false;
        }
    });
    
    // Phone validation
    form.querySelectorAll('input[type="tel"]').forEach(input => {
        if (input.value && !isValidPhone(input.value)) {
            showError(input, 'شماره تلفن نامعتبر است');
            isValid = false;
        }
    });
    
    // Number validation
    form.querySelectorAll('input[type="number"]').forEach(input => {
        const min = input.getAttribute('min');
        const max = input.getAttribute('max');
        if (min && parseFloat(input.value) < parseFloat(min)) {
            showError(input, `حداقل مقدار ${min} است`);
            isValid = false;
        }
        if (max && parseFloat(input.value) > parseFloat(max)) {
            showError(input, `حداکثر مقدار ${max} است`);
            isValid = false;
        }
    });
    
    return isValid;
}

function showError(input, message) {
    clearError(input);
    input.classList.add('border-red-500');
    const error = document.createElement('p');
    error.className = 'text-red-500 text-xs mt-1 error-message';
    error.textContent = message;
    input.parentElement.appendChild(error);
}

function clearError(input) {
    input.classList.remove('border-red-500');
    const error = input.parentElement.querySelector('.error-message');
    if (error) error.remove();
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function isValidPhone(phone) {
    return /^[0-9+\-\s()]{7,}$/.test(phone);
}

// Real-time validation
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form').forEach(form => {
        form.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('blur', () => {
                if (input.hasAttribute('required') && !input.value.trim()) {
                    showError(input, 'این فیلد الزامی است');
                } else if (input.type === 'email' && input.value && !isValidEmail(input.value)) {
                    showError(input, 'ایمیل نامعتبر است');
                } else if (input.type === 'tel' && input.value && !isValidPhone(input.value)) {
                    showError(input, 'شماره تلفن نامعتبر است');
                } else {
                    clearError(input);
                }
            });
            
            input.addEventListener('input', () => {
                if (input.classList.contains('border-red-500')) {
                    clearError(input);
                }
            });
        });
        
        form.addEventListener('submit', (e) => {
            if (!validateForm(form)) {
                e.preventDefault();
                showToast('لطفا فیلدهای الزامی را پر کنید', 'error');
            }
        });
    });
});
