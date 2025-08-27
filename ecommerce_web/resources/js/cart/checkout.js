document.addEventListener("DOMContentLoaded", () => {
    const addressOption = document.getElementById('address_option');
    const existingBlock = document.getElementById('existing_address_block');
    const addressIdSelect = document.getElementById('address_id');
    const newAddressForm = document.getElementById('new_address_form');
    const form = document.getElementById('checkoutForm');

    function setRequiredForNewAddress(on) {
        if (!newAddressForm) return;
        // Chỉ bắt buộc những trường cần thiết; postal_code là không bắt buộc
        const requiredNames = ['full_name','phone_number','details','ward','district','city','country'];
        newAddressForm.querySelectorAll('input').forEach(i => {
            if (requiredNames.includes(i.name)) {
                i.required = !!on;
            } else if (i.name === 'postal_code') {
                i.required = false;
            }
        });
    }

    function applyMode(mode) {
        if (mode === 'existing') {
            if (existingBlock) existingBlock.style.display = 'block';
            if (newAddressForm) newAddressForm.style.display = 'none';
            if (addressIdSelect) addressIdSelect.required = true;
            setRequiredForNewAddress(false);
        } else if (mode === 'new') {
            if (existingBlock) existingBlock.style.display = 'none';
            if (newAddressForm) newAddressForm.style.display = 'block';
            if (addressIdSelect) addressIdSelect.required = false;
            setRequiredForNewAddress(true);
        } else {
            if (existingBlock) existingBlock.style.display = 'none';
            if (newAddressForm) newAddressForm.style.display = 'none';
            if (addressIdSelect) addressIdSelect.required = false;
            setRequiredForNewAddress(false);
        }
    }

    if (addressOption) {
        addressOption.addEventListener('change', function () {
            applyMode(this.value);
        });

        // Khởi tạo trạng thái ngay khi load:
        // - Nếu chưa chọn gì nhưng có địa chỉ: mặc định "existing"
        // - Nếu đã có old('address_option'): option đó đã được selected trong Blade
        if (!addressOption.value && addressOption.dataset.hasAddresses === '1') {
            addressOption.value = 'existing';
        }
        applyMode(addressOption.value);
    }

    // Chuẩn hóa số điện thoại trước khi submit (bỏ space, chấm, gạch)
    function normalizePhone() {
        const phone = document.getElementById('phone_number');
        if (phone && phone.value) {
            phone.value = phone.value.replace(/[\s\.\-]+/g, '');
        }
    }

    function handleSubmit(e) {
        if (!form) return;

        normalizePhone();

        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        let firstInvalid = null;

        inputs.forEach(i => {
            i.style.borderColor = '';
            i.style.boxShadow = '';
        });

        inputs.forEach(i => {
            if (!i.value || !i.value.trim()) {
                isValid = false;
                if (!firstInvalid) firstInvalid = i;
                i.style.borderColor = '#ef4444';
                i.style.boxShadow = '0 0 0 1px #ef4444';
            }
        });

        if (!isValid) {
            e.preventDefault();
            if (firstInvalid) firstInvalid.focus();
            return;
        }

        const btn = form.querySelector('.submit-btn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = 'Đang xử lý đơn hàng...';
        }
        // Không preventDefault -> để form submit bình thường
    }

    if (form) {
        form.addEventListener('submit', handleSubmit);
    }
});
