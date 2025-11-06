// ========== HEADER SCROLL SHADOW ==========
document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('.jr-header');
    if (header) {
        const toggleShadow = () => {
            if (window.scrollY > 2) header.classList.add('scrolled');
            else header.classList.remove('scrolled');
        };

        toggleShadow();
        window.addEventListener('scroll', toggleShadow, { passive: true });
    }

    // ========== HAMBURGER ANIMATION ==========
    const burger = document.querySelector('.jr-burger');
    const collapse = document.querySelector('#jrNav');

    if (burger && collapse) {
        collapse.addEventListener('shown.bs.collapse', () => {
            burger.setAttribute('aria-expanded', 'true');
        });
        collapse.addEventListener('hidden.bs.collapse', () => {
            burger.setAttribute('aria-expanded', 'false');
        });
    }
});

// BE COMMENT : MANUAL ACTIVE CLASS REMOVAL 
// ===== ACTIVE MENU BY URL =====
// document.addEventListener('DOMContentLoaded', () => {
//     const navLinks = document.querySelectorAll('.jr-nav .nav-link');
//     if (!navLinks.length) return;

//     const currentPath = window.location.pathname.split('/').pop(); // ví dụ: 'news.php'

//     navLinks.forEach(link => {
//         const linkPath = link.getAttribute('href').split('/').pop();
//         if (linkPath === currentPath || (linkPath === 'index.php' && currentPath === '')) {
//             // bỏ active cũ
//             navLinks.forEach(l => l.classList.remove('active'));
//             // set active mới
//             link.classList.add('active');
//             link.setAttribute('aria-current', 'page');
//         }
//     });
// });


// ========== INTRO ANIMATION ==========
document.addEventListener('DOMContentLoaded', () => {
    const blocks = document.querySelectorAll('#jr-intro .jr-intro__title, #jr-intro .jr-intro__lead, #jr-intro .jr-intro__text');

    // Set trạng thái ban đầu
    blocks.forEach(el => {
        el.style.opacity = 0;
        el.style.transform = 'translateY(16px)';
        el.style.transition = 'opacity .6s ease, transform .6s ease';
    });

    const io = new IntersectionObserver(entries => {
        entries.forEach((en, i) => {
            if (en.isIntersecting) {
                // trễ dần từng khối cho mượt
                setTimeout(() => {
                    en.target.style.opacity = 1;
                    en.target.style.transform = 'none';
                }, 120 * i);
                io.unobserve(en.target);
            }
        });
    }, { threshold: 0.2 });

    blocks.forEach(el => io.observe(el));
});

// ========== SEARCH FORM HANDLER ==========
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('#jr-search .jr-searchbar');
    if (!form) return;
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const q = form.querySelector('input[type="search"]')?.value?.trim() || '';
        if (q) {
            // Điều hướng tới trang list (ví dụ):
            window.location.href = `/search.html?q=${encodeURIComponent(q)}`;
        }
    });
});

// ========== BACK TO TOP BUTTON ==========
document.addEventListener('DOMContentLoaded', () => {
    const topBtn = document.getElementById('btn-top');
    if (!topBtn) return;

    const toggleTopBtn = () => {
        if (window.scrollY > 400) topBtn.classList.add('show');
        else topBtn.classList.remove('show');
    };
    toggleTopBtn();

    window.addEventListener('scroll', toggleTopBtn, { passive: true });

    topBtn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});


// ========== ARTICLES PAGINATION (client-side helper) ==========
document.addEventListener('DOMContentLoaded', () => {
    const pager = document.querySelector('.jr-pagination [data-pager]');
    if (!pager) return;

    // Đọc config
    const totalPages = parseInt(pager.dataset.total || '1', 10);
    const base = pager.dataset.base || window.location.pathname;
    const url = new URL(window.location.href);
    const currentQS = parseInt(url.searchParams.get('page') || pager.dataset.current || '1', 10);
    const current = Math.min(Math.max(1, currentQS), totalPages);

    // Render nút: 1 … current-1 current current+1 … total
    pager.innerHTML = '';
    const ul = pager;

    const addItem = (label, page, opts = {}) => {
        const li = document.createElement('li');
        li.className = 'page-item';
        if (opts.disabled) li.classList.add('disabled');
        if (opts.active) li.classList.add('active');

        const a = document.createElement('a');
        a.className = 'page-link';
        a.href = `${base}?page=${page}`;
        a.textContent = label;
        a.setAttribute('data-page', page);
        li.appendChild(a);
        ul.appendChild(li);
    };

    const addEllipsis = () => {
        const li = document.createElement('li');
        li.className = 'page-item disabled';
        const span = document.createElement('span');
        span.className = 'page-link';
        span.textContent = '…';
        li.appendChild(span);
        ul.appendChild(li);
    };

    // Prev
    addItem('‹', Math.max(1, current - 1), { disabled: current === 1 });

    // First
    addItem('1', 1, { active: current === 1 });

    // Left ellipsis
    if (current > 3) addEllipsis();

    // Middle neighbors
    for (let p = Math.max(2, current - 1); p <= Math.min(totalPages - 1, current + 1); p++) {
        addItem(String(p), p, { active: p === current });
    }

    // Right ellipsis
    if (current < totalPages - 2) addEllipsis();

    // Last (nếu total >= 2)
    if (totalPages >= 2) addItem(String(totalPages), totalPages, { active: current === totalPages });

    // Next
    addItem('›', Math.min(totalPages, current + 1), { disabled: current === totalPages });

    // Scroll top khi click (giữ UX tốt)
    ul.addEventListener('click', (e) => {
        const a = e.target.closest('a.page-link');
        if (!a) return;
        // Nếu bạn dùng server render, bỏ đoạn preventDefault dưới
        // và để trình duyệt điều hướng bình thường.
        // --- CLIENT-SIDE DEMO (không có backend) ---
        e.preventDefault();
        const page = parseInt(a.dataset.page || '1', 10);
        // TODO: nếu có API, bạn fetch dữ liệu trang "page" ở đây rồi render grid.
        // Demo đơn giản: đổi query param và reload để SSR/SSG xử lý
        const nu = new URL(window.location.href);
        nu.searchParams.set('page', page);
        window.location.href = nu.toString();
    });
});


// ========== Dual Search handlers ==========
document.addEventListener('DOMContentLoaded', () => {
    const formKeyword = document.getElementById('form-keyword');
    const formIssue = document.getElementById('form-issue');

    if (formKeyword) {
        formKeyword.addEventListener('submit', (e) => {
            e.preventDefault();
            const q = formKeyword.q.value.trim();
            if (!q) return;
            // Điều hướng đến trang kết quả tìm theo từ khóa
            window.location.href = `/search.html?q=${encodeURIComponent(q)}`;
        });
    }

    if (formIssue) {
        formIssue.addEventListener('submit', (e) => {
            e.preventDefault();
            let issue = formIssue.issue.value.trim();
            // chuẩn hóa: lấy số (nếu chỉ dùng số), vẫn để nguyên nếu bạn dùng mã khác
            const pure = issue.replace(/[^\d]/g, '');
            if (!pure) return;
            // Điều hướng đến trang số tạp chí
            window.location.href = `/issues.html?issue=${encodeURIComponent(pure)}`;
        });
    }

    // Optional: click vào tag cloud -> tìm kiếm theo keyword
    document.querySelectorAll('#jr-search-dual .jr-tagcloud a').forEach(a => {
        a.addEventListener('click', (e) => {
            e.preventDefault();
            const kw = a.textContent.replace(/^#\s*/, '').trim();
            window.location.href = `/search.html?q=${encodeURIComponent(kw)}`;
        });
    });
});
// BE COMMENT : MANUAL ACTIVE CLASS REMOVAL 
// Back to index & đánh dấu sidebar theo query
// document.addEventListener('DOMContentLoaded', () => {
//     // back
//     const back = document.getElementById('btn-news-back');
//     if (back) {
//         back.addEventListener('click', (e) => {
//             e.preventDefault();
//             if (history.length > 1) history.back();
//             else window.location.href = back.dataset.back || '/news.html';
//         });
//     }

//     // active theo ?category=&tag=
//     const url = new URL(location.href);
//     const cat = url.searchParams.get('category') || '';
//     const tag = url.searchParams.get('tag') || '';
//     const setActive = (sel, attr, val) => {
//         document.querySelectorAll(`${sel} a`).forEach(a => {
//             a.classList.toggle('active', (a.dataset[attr] ?? '') === val);
//         });
//     };
//     setActive('#news-cats', 'cat', cat);
//     setActive('#news-tags', 'tag', tag);
// });
