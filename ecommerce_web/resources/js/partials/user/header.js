let isLoggedIn = document.querySelector('meta[name="user-logged-in"]').content === '1';

function init() {
    updateAuthUI();
    attachDropdownToggle();
}

// Cập nhật hiển thị auth/guest
function updateAuthUI() {
    const authUser = document.getElementById('authUser');
    const guestUser = document.getElementById('guestUser');

    if (authUser) authUser.style.display = isLoggedIn ? 'block' : 'none';
    if (guestUser) guestUser.style.display = isLoggedIn ? 'none' : 'block';

    // Ẩn dropdown khi chưa đăng nhập
    const dropdown = document.getElementById('userDropdown');
    if (dropdown && !isLoggedIn) {
        dropdown.classList.remove('show');
    }
}

// Gắn sự kiện toggle dropdown an toàn
function attachDropdownToggle() {
    const toggleBtn = document.querySelector('#authUser > a');
    if (!toggleBtn) return;

    toggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const dropdown = document.getElementById('userDropdown');
        if (!dropdown) return;

        dropdown.classList.toggle('show');

        // Click ngoài để đóng
        setTimeout(() => {
            document.addEventListener('click', closeDropdown);
        }, 0);
    });
}

// Đóng dropdown khi click ngoài
function closeDropdown(e) {
    const dropdown = document.getElementById('userDropdown');
    if (!dropdown) return;

    if (!e.target.closest('.user-menu')) {
        dropdown.classList.remove('show');
        document.removeEventListener('click', closeDropdown);
    }
}

// Logout giả lập
function logout() {
    isLoggedIn = false;
    updateAuthUI();
}

// Menu mobile toggle
function toggleMenu() {
    const menu = document.getElementById('navMenu');
    if (!menu) return;
    menu.classList.toggle('open');
}

// Test toggle bằng phím 'l'
document.addEventListener('keydown', function(e) {
    if (e.key === 'l') {
        isLoggedIn = !isLoggedIn;
        updateAuthUI();
    }
});

document.addEventListener('DOMContentLoaded', init);
