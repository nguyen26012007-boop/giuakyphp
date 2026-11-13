document.querySelectorAll('[data-user-list]').forEach((list) => {
    const search = list.querySelector('[data-user-search]');
    const rows = [...list.querySelectorAll('[data-filter-item]')];
    const empty = list.querySelector('[data-no-results]');
    const status = list.querySelector('#result-count');
    if (!search) return;

    const filterUsers = () => {
        const query = search.value.trim().toLocaleLowerCase('vi');
        let visible = 0;
        rows.forEach((row) => {
            row.hidden = !row.dataset.search.toLocaleLowerCase('vi').includes(query);
            if (!row.hidden) visible++;
        });
        empty.hidden = visible > 0 || !query;
        status.textContent = query
            ? `${visible} / ${rows.length} người dùng phù hợp`
            : `${rows.length} người dùng trong danh sách`;
    };
    search.addEventListener('input', filterUsers);
    list.querySelector('[data-clear-search]')?.addEventListener('click', () => {
        search.value = '';
        filterUsers();
        search.focus();
    });
});

const passwordToggle = document.querySelector('[data-password-toggle]');
passwordToggle?.addEventListener('click', () => {
    const password = document.getElementById('admin_password');
    const visible = password.type === 'password';
    password.type = visible ? 'text' : 'password';
    passwordToggle.setAttribute('aria-pressed', String(visible));
    passwordToggle.setAttribute('aria-label', visible ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
});

const imageInput = document.getElementById('images');
const preview = document.getElementById('image-preview');
if (imageInput && preview) {
    const initialSource = preview.src;
    const fileName = document.getElementById('file-name');
    const initialName = fileName.textContent;
    const error = document.getElementById('image-error');
    let previewUrl;
    imageInput.addEventListener('change', () => {
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        const file = imageInput.files[0];
        let message = '';
        if (file && file.size > 2 * 1024 * 1024) {
            message = 'Vui lòng chọn ảnh không vượt quá 2 MB.';
        } else if (file && !['image/jpeg', 'image/png', 'image/webp', 'image/gif'].includes(file.type)) {
            message = 'Vui lòng chọn ảnh JPG, PNG, WebP hoặc GIF.';
        }
        imageInput.setCustomValidity(message);
        imageInput.setAttribute('aria-invalid', String(Boolean(message)));
        error.hidden = !message;
        error.textContent = message;
        fileName.textContent = file ? file.name : initialName;
        preview.src = file && !message ? (previewUrl = URL.createObjectURL(file)) : initialSource;
    });
}

const formError = document.getElementById('form-error');
if (formError) formError.focus();
