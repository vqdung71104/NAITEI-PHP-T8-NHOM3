document.addEventListener("DOMContentLoaded", () => {
    const addressOption = document.getElementById('address_option');
    const existingBlock = document.getElementById('existing_address_block');
    const addressIdSelect = document.getElementById('address_id');
    const newAddressForm = document.getElementById('new_address_form');
    const confirmBtn = document.getElementById('confirmAddressBtn');
    const form = document.getElementById('checkoutForm');

    const shippingFeeEl = document.getElementById('shipping_fee');
    const totalAmountEl = document.getElementById('total_price');
    const subtotalEl = document.getElementById('subtotal');

    let currentShipping = parseInt(shippingFeeEl.dataset.value) || 30000;
    let subtotal = parseInt(subtotalEl.dataset.value) || 0;

    function updateTotals(newShipping) {
        shippingFeeEl.textContent = newShipping.toLocaleString() + '₫';
        totalAmountEl.textContent = (subtotal + newShipping).toLocaleString() + '₫';
        currentShipping = newShipping;
    }

    // xử lý chọn địa chỉ đã lưu
    if (addressIdSelect) {
        addressIdSelect.addEventListener('change', function () {
            const addressId = this.value;
            if (!addressId) return;

            fetch("/shipping/calculate", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ address_id: addressId, order_amount: subtotal })
            })
            .then(res => res.json())
            .then(data => {
                if (data.shipping !== undefined) {
                    if (data.shipping !== currentShipping) {
                        alert("Phí vận chuyển thay đổi: " + data.shipping.toLocaleString() + "₫");
                    }
                    updateTotals(data.shipping);
                }
            }).catch(console.error);
        });
    }

    // xử lý nhập địa chỉ mới
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function () {
            const country = document.getElementById('country').value.trim();
            const city = document.getElementById('city').value.trim();
            if (!country || !city) {
                alert("Vui lòng nhập đủ Tỉnh/Thành phố và Quốc gia.");
                return;
            }

            fetch("/shipping/calculate", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ address: { country, city }, order_amount: subtotal })
            })
            .then(res => res.json())
            .then(data => {
                if (data.shipping !== undefined) {
                    if (data.shipping !== currentShipping) {
                        alert("Phí vận chuyển thay đổi: " + data.shipping.toLocaleString() + "₫");
                    }
                    updateTotals(data.shipping);
                }
            }).catch(console.error);
        });
    }

    // xử lý hiển thị form
    function setRequiredForNewAddress(on) {
        if (!newAddressForm) return;
        const requiredNames = ['full_name','phone_number','details','ward','district','city','country'];
        newAddressForm.querySelectorAll('input').forEach(i => {
            if (requiredNames.includes(i.name)) i.required = on;
            else if (i.name === 'postal_code') i.required = false;
        });
    }

    function applyMode(mode) {
        if (mode === 'existing') {
            existingBlock.style.display = 'block';
            newAddressForm.style.display = 'none';
            addressIdSelect.required = true;
            setRequiredForNewAddress(false);
        } else if (mode === 'new') {
            existingBlock.style.display = 'none';
            newAddressForm.style.display = 'block';
            addressIdSelect.required = false;
            setRequiredForNewAddress(true);
        } else {
            existingBlock.style.display = 'none';
            newAddressForm.style.display = 'none';
            addressIdSelect.required = false;
            setRequiredForNewAddress(false);
        }
    }

    if (addressOption) {
        addressOption.addEventListener('change', () => applyMode(addressOption.value));
        if (!addressOption.value && addressOption.dataset.hasAddresses === '1') {
            addressOption.value = 'existing';
        }
        applyMode(addressOption.value);
    }

    // chuẩn hóa số điện thoại
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
        inputs.forEach(i => { i.style.borderColor=''; i.style.boxShadow=''; });
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
        if (btn) { btn.disabled=true; btn.innerHTML='Đang xử lý đơn hàng...'; }
    }

    if (form) form.addEventListener('submit', handleSubmit);
});
