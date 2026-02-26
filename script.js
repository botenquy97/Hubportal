// API Base URL (Change this to your actual domain later if needed, e.g., 'https://yourdomain.com/api')
const API_URL = './api';

// App State
let links = [];
const THEME_KEY = 'webHubTheme';
const LIGHT_MODE_CLASS = 'light-mode';

// DOM References
const greetingEl = document.getElementById('greeting');
const clockEl = document.getElementById('clock');
const linksGrid = document.getElementById('links-grid');
const addBtn = document.getElementById('add-btn');
const themeToggleBtn = document.getElementById('theme-toggle');
const searchInput = document.getElementById('search-input');

// Modal References
const modal = document.getElementById('link-modal');
const closeModalBtn = document.getElementById('close-modal-btn');
const cancelBtn = document.getElementById('cancel-btn');
const linkForm = document.getElementById('link-form');
const linkIdInput = document.getElementById('link-id');
const linkNameInput = document.getElementById('link-name');
const linkUrlInput = document.getElementById('link-url');
const linkIconInput = document.getElementById('link-icon');
const modalTitle = document.getElementById('modal-title');

// Initialize the app
async function init() {
    loadTheme();
    await loadLinks();
    updateClockAndGreeting();
    setInterval(updateClockAndGreeting, 1000); // Ticking clock every 1s
    setupEventListeners();
}

// ---- THEME ----
function loadTheme() {
    const savedTheme = localStorage.getItem(THEME_KEY);
    if (savedTheme === 'light') {
        document.documentElement.classList.add(LIGHT_MODE_CLASS);
    }
    updateThemeIcon();
}

function toggleTheme() {
    const isLightMode = document.documentElement.classList.toggle(LIGHT_MODE_CLASS);
    localStorage.setItem(THEME_KEY, isLightMode ? 'light' : 'dark');
    updateThemeIcon();
}

function updateThemeIcon() {
    if (!themeToggleBtn) return;
    const isLightMode = document.documentElement.classList.contains(LIGHT_MODE_CLASS);
    themeToggleBtn.innerHTML = isLightMode
        ? '<i class="ph ph-moon"></i>'
        : '<i class="ph ph-sun"></i>';
}

// ---- TIME & GREETING ----
function updateClockAndGreeting() {
    const now = new Date();
    const hours = now.getHours();
    const minutes = now.getMinutes().toString().padStart(2, '0');

    clockEl.textContent = `${hours}:${minutes}`;

    let greeting = '';
    if (hours >= 0 && hours < 5) {
        greeting = 'Chúc ngủ ngon';
    } else if (hours >= 5 && hours < 11) {
        greeting = 'Chào buổi sáng';
    } else if (hours >= 11 && hours < 14) {
        greeting = 'Chào buổi trưa';
    } else if (hours >= 14 && hours < 18) {
        greeting = 'Chào buổi chiều';
    } else {
        greeting = 'Chào buổi tối';
    }

    greetingEl.textContent = greeting;
}

// ---- DATA (API) ----
async function loadLinks() {
    try {
        const response = await fetch(`${API_URL}/get_links.php`);
        const result = await response.json();

        if (result.success) {
            links = result.data;
            renderLinks();
        } else {
            console.error('Lỗi lấy dữ liệu:', result.message);
            // Fallback empty or default on error
            links = [];
            renderLinks();
        }
    } catch (error) {
        console.error('Không thể kết nối đến server:', error);
        links = [];
        renderLinks();
    }
}

async function apiCall(endpoint, data) {
    try {
        const response = await fetch(`${API_URL}/${endpoint}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        return await response.json();
    } catch (error) {
        console.error(`Lỗi API ${endpoint}:`, error);
        return { success: false, message: 'Lỗi kết nối mạng' };
    }
}

// ---- HELPER FUNCTIONS ---- 
function autoConvertUrl(urlStr) {
    if (!urlStr.trim()) return '';
    // Thêm phương thức http nếu không có protocol
    if (!/^https?:\/\//i.test(urlStr)) {
        return 'https://' + urlStr;
    }
    return urlStr;
}

// Lấy favicon của một tên miền thông qua dịch vụ của Google
function getFaviconFromUrl(urlStr) {
    try {
        const domain = new URL(urlStr).hostname;
        return `https://www.google.com/s2/favicons?domain=${domain}&sz=64`;
    } catch {
        return '';
    }
}

// ---- RENDER ----
function renderLinks(filterText = '') {
    linksGrid.innerHTML = '';

    const lowerFilter = filterText.toLowerCase().trim();
    const filteredLinks = links.filter(link => {
        if (!lowerFilter) return true;
        return link.name.toLowerCase().includes(lowerFilter) ||
            link.url.toLowerCase().includes(lowerFilter);
    });

    if (filteredLinks.length === 0) {
        linksGrid.innerHTML = '<p style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 2rem 0;">Không tìm thấy lối tắt nào phù hợp.</p>';
        return;
    }

    filteredLinks.forEach(link => {
        // Build URL that the card navigates to
        const anchor = document.createElement('a');
        anchor.className = 'link-card';
        anchor.href = link.url;
        anchor.target = "_self"; // Mở tab hiện tại, hoặc thay bằng "_blank" để mở tab mới

        // Build icon HTML
        let iconHtml = '';
        const finalIcon = link.icon || getFaviconFromUrl(link.url);

        if (finalIcon) {
            // Dùng img tag có event onerror để fallback qua chữ cái đầu nếu không load được icon
            iconHtml = `<img src="${finalIcon}" alt="${link.name}" onerror="this.onerror=null; this.parentNode.innerHTML='<span style=\\'font-size: 2rem; font-weight: 500\\'>${link.name.charAt(0).toUpperCase()}</span>';">`;
        } else {
            iconHtml = `<span style="font-size: 2rem; font-weight: 500">${link.name.charAt(0).toUpperCase()}</span>`;
        }

        anchor.innerHTML = `
            <div class="link-actions">
                <button class="action-btn edit" onclick="editLink(event, '${link.id}')" title="Sửa">
                    <i class="ph ph-pencil-simple"></i>
                </button>
                <button class="action-btn delete" onclick="deleteLink(event, '${link.id}')" title="Xóa">
                    <i class="ph ph-trash"></i>
                </button>
            </div>
            <div class="link-icon-wrapper">
                ${iconHtml}
            </div>
            <span class="link-name" title="${link.name}">${link.name}</span>
        `;

        linksGrid.appendChild(anchor);
    });
}

// ---- ACTIONS ----
window.editLink = function (e, id) {
    e.preventDefault(); // Ngăn mở link
    e.stopPropagation(); // Ngăn sự kiện nhấp lan ra ngoài

    const targetLink = links.find(l => l.id === id);
    if (!targetLink) return;

    linkIdInput.value = targetLink.id;
    linkNameInput.value = targetLink.name;
    linkUrlInput.value = targetLink.url;
    linkIconInput.value = targetLink.icon || '';

    modalTitle.textContent = 'Sửa lối tắt';
    openModal();
};

window.deleteLink = async function (e, id) {
    e.preventDefault();
    e.stopPropagation();

    if (confirm('Bạn có chắc chắn muốn xóa lối tắt này không?')) {
        const result = await apiCall('delete_link.php', { id });
        if (result.success) {
            links = links.filter(l => l.id !== id);
            renderLinks(searchInput ? searchInput.value : '');
        } else {
            alert('Lỗi khi xóa: ' + result.message);
        }
    }
};

// ---- MODAL / EVENTS ----
function openModal() {
    modal.classList.remove('hidden');
    // Đợi transition chạy rồi focus vào ô tên
    setTimeout(() => linkNameInput.focus(), 300);
}

function closeModal() {
    modal.classList.add('hidden');
    linkForm.reset();
}

function setupEventListeners() {
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', toggleTheme);
    }

    addBtn.addEventListener('click', () => {
        linkIdInput.value = ''; // Xóa ID để tạo mới
        modalTitle.textContent = 'Thêm lối tắt mới';
        openModal();
    });

    closeModalBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    // Nhấp ra ngoài vùng form để đóng modal
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    linkForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const id = linkIdInput.value;
        const name = linkNameInput.value.trim();
        const url = autoConvertUrl(linkUrlInput.value);
        const icon = linkIconInput.value.trim();

        const submitBtn = document.querySelector('#link-form button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Đang lưu...';
        submitBtn.disabled = true;

        if (id) {
            // Update
            const result = await apiCall('update_link.php', { id, name, url, icon });
            if (result.success) {
                const index = links.findIndex(l => l.id === id);
                if (index !== -1) {
                    links[index] = { ...links[index], name, url, icon };
                }
            } else {
                alert('Lỗi cập nhật: ' + result.message);
            }
        } else {
            // Create
            const newId = Date.now().toString();
            const result = await apiCall('add_link.php', { id: newId, name, url, icon });

            if (result.success) {
                const newLink = { id: newId, name, url, icon };
                links.push(newLink);
            } else {
                alert('Lỗi thêm mới: ' + result.message);
            }
        }

        submitBtn.textContent = originalText;
        submitBtn.disabled = false;

        renderLinks(searchInput ? searchInput.value : '');
        closeModal();
    });

    // Cập nhật kết quả khi gõ tìm kiếm
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            renderLinks(e.target.value);
        });
    }
}

// Bắt đầu
document.addEventListener('DOMContentLoaded', init);
