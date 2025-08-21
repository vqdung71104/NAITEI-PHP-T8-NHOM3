function handleSubmit(e) {
    e.preventDefault(); // chặn submit mặc định
    const form = document.getElementById('checkoutForm');
    const inputs = form.querySelectorAll('input[required], select[required]');
    let isValid = true;

    // Reset
    inputs.forEach(input => {
        input.style.borderColor = '#d1d5db';
        input.style.boxShadow = 'none';
    });

    // Validate
    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.style.borderColor = '#6b7280';
            input.style.boxShadow = '0 0 0 1px #6b7280';
            if (isValid === false) input.focus();
        }
    });

    if (isValid) {
        // Nếu muốn animation thì giữ
        const btn = form.querySelector('.submit-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Đang xử lý đơn hàng...';
        btn.style.background = '#6b7280';
        btn.disabled = true;

        // Sau 1 giây submit form ra server
        setTimeout(() => {
            form.submit(); // đây mới thực sự gửi POST tới route('checkout.process')
        }, 1000);
    }
}


// Enhanced form interactions
document.querySelectorAll('input, select, textarea').forEach(element => {
    element.addEventListener('focus', function() {
        this.style.borderColor = '#1a1a1a';
        this.style.boxShadow = '0 0 0 1px #1a1a1a';
    });
    
    element.addEventListener('blur', function() {
        if (this.value.trim()) {
            this.style.borderColor = '#6b7280';
            this.style.boxShadow = 'none';
        } else {
            this.style.borderColor = '#d1d5db';
            this.style.boxShadow = 'none';
        }
    });
});

// Smooth page entrance
window.addEventListener('load', function() {
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.5s ease';
    
    setTimeout(() => {
        document.body.style.opacity = '1';
    }, 100);
});

window.handleSubmit = handleSubmit; 