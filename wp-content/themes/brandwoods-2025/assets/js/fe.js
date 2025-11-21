// ===== SMOOTH HEADER ON SCROLL (RAF + LERP) =====
document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('.jr-header');
    if (!header) return;

    // Nội suy mượt
    const lerp = (a, b, t) => a + (b - a) * t;
    const clamp = (n, min, max) => Math.min(max, Math.max(min, n));

    // Trạng thái
    let lastY = window.scrollY;
    let targetCompact = 0;     // 0..1: mức “thu nhỏ” khi cuộn
    let currentCompact = 0;
    let targetHide = 0;        // 0..1: ẩn header khi cuộn xuống
    let currentHide = 0;
    let ticking = false;

    // Ngưỡng & tham số
    const COMPACT_START = 0;    // bắt đầu compact ngay khi lăn
    const COMPACT_RANGE = 140;  // cuộn ~140px đạt compact = 1
    const HIDE_THRESHOLD = 80;  // chỉ ẩn khi đã cuộn quá ngưỡng
    const SHOW_DELTA = 6;       // biên độ để nhận biết đang cuộn lên
    const EASE = 0.16;          // hệ số lerp (0.1..0.2 là mượt)

    function updateTargets() {
        const y = window.scrollY;

        // Mức compact 0..1
        targetCompact = clamp((y - COMPACT_START) / COMPACT_RANGE, 0, 1);

        // Ẩn/hiện khi cuộn
        const goingDown = y > lastY;
        if (y > HIDE_THRESHOLD && goingDown) {
            targetHide = 1; // ẩn
        } else if (!goingDown && (lastY - y > SHOW_DELTA)) {
            targetHide = 0; // hiện
        } else if (y <= HIDE_THRESHOLD) {
            targetHide = 0;
        }

        lastY = y;
    }

    function render() {
        // Nội suy mượt
        currentCompact = lerp(currentCompact, targetCompact, EASE);
        currentHide = lerp(currentHide, targetHide, EASE);

        // Map compact -> các biến CSS
        // compact=0: trong suốt, không blur, pad lớn, logo 1
        // compact=1: nền rõ, blur 8px, pad nhỏ, logo 0.92
        const blur = lerp(0, 6, currentCompact);     // nhẹ hơn để không đục màu vàng
        const elev = currentCompact;
        const pad = lerp(1.25, 0.94, currentCompact); // rem → 80 px → 70 px
        const scale = lerp(1, 0.92, currentCompact);
        const light = currentCompact; // dùng cho brightness

        // Hide translateY theo currentHide (0..1) -> 0..-100%
        const yHide = -100 * currentHide;

        header.style.setProperty('--hdr-blur', blur.toFixed(2) + 'px');
        header.style.setProperty('--hdr-elev', elev.toFixed(3));
        header.style.setProperty('--hdr-pad', pad.toFixed(2) + 'rem');
        header.style.setProperty('--logo-scale', scale.toFixed(3));
        header.style.setProperty('--hdr-bg-lightness', light.toFixed(3));
        header.style.setProperty('--hdr-y', yHide.toFixed(2) + '%');

        // Lặp đến khi gần tiệm cận mục tiêu
        const stillAnimating = (Math.abs(currentCompact - targetCompact) > 0.001) ||
            (Math.abs(currentHide - targetHide) > 0.001);
        if (stillAnimating) {
            requestAnimationFrame(render);
        } else {
            ticking = false;
        }
    }

    function onScroll() {
        updateTargets();
        if (!ticking) {
            ticking = true;
            requestAnimationFrame(render);
        }
    }

    // Khởi tạo theo vị trí hiện tại
    updateTargets();
    currentCompact = targetCompact;
    currentHide = targetHide;
    render();

    window.addEventListener('scroll', onScroll, { passive: true });

    // === Burger/Collapse aria (nếu còn dùng) ===
    const burger = document.querySelector('.jr-burger');
    const collapse = document.querySelector('#jrNav');
    if (burger && collapse) {
        collapse.addEventListener('shown.bs.collapse', () => burger.setAttribute('aria-expanded', 'true'));
        collapse.addEventListener('hidden.bs.collapse', () => burger.setAttribute('aria-expanded', 'false'));
        collapse.addEventListener('show.bs.collapse', () => { targetHide = 0; }); // mở menu thì hiện header
    }
});

// BE COMMENT : MANUAL ACTIVE CLASS REMOVAL 
// ===== ACTIVE MENU BY URL =====
// document.addEventListener('DOMContentLoaded', () => {
//     const navLinks = document.querySelectorAll('.jr-nav .nav-link');
//     if (!navLinks.length) return;

//     const currentPath = window.location.pathname.split('/').pop();

//     navLinks.forEach(link => {
//         const linkPath = link.getAttribute('href').split('/').pop();
//         if (linkPath === currentPath || (linkPath === 'index.php' && currentPath === '')) {
//             navLinks.forEach(l => l.classList.remove('active'));
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
// document.addEventListener('DOMContentLoaded', () => {
//     const form = document.querySelector('#jr-search .jr-searchbar');
//     if (!form) return;
//     form.addEventListener('submit', (e) => {
//         e.preventDefault();
//         const q = form.querySelector('input[type="search"]')?.value?.trim() || '';
//         if (q) {
//             // Điều hướng tới trang list (ví dụ):
//             window.location.href = `/search.html?q=${encodeURIComponent(q)}`;
//         }
//     });
// });

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

    // if (formKeyword) {
    //     formKeyword.addEventListener('submit', (e) => {
    //         e.preventDefault();
    //         const q = formKeyword.q.value.trim();
    //         if (!q) return;
    //         // Điều hướng đến trang kết quả tìm theo từ khóa
    //         window.location.href = `/search.html?q=${encodeURIComponent(q)}`;
    //     });
    // }

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
    // document.querySelectorAll('#jr-search-dual .jr-tagcloud a').forEach(a => {
    //     a.addEventListener('click', (e) => {
    //         e.preventDefault();
    //         const kw = a.textContent.replace(/^#\s*/, '').trim();
    //         window.location.href = `/search.html?q=${encodeURIComponent(kw)}`;
    //     });
    // });
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


// ===== SEARCH OVERLAY (CSS TRANSITION VERSION) =====
document.addEventListener('DOMContentLoaded', () => {
    const burger = document.querySelector('.jr-burger[data-action="toggle-search"]');
    const overlay = document.getElementById('jrSearch');          // .jr-search-box
    if (!burger || !overlay) return;

    const content = overlay.querySelector('.jr-search') || overlay;
    const input = overlay.querySelector('input[type="search"], input[name="q"]');

    // backdrop: lấy nếu có, không có thì tạo
    let backdrop = overlay.querySelector('.jr-search__backdrop');
    if (!backdrop) {
        backdrop = document.createElement('div');
        backdrop.className = 'jr-search__backdrop';
        overlay.prepend(backdrop);
    }

    let lastFocused = null;

    function openSearch() {
        if (overlay.classList.contains('is-open')) return;
        lastFocused = document.activeElement;

        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.classList.add('search-open');
        burger.setAttribute('aria-expanded', 'true');

        // focus nhẹ sau 1 chút cho an toàn
        setTimeout(() => input?.focus(), 80);
    }

    function closeSearch() {
        if (!overlay.classList.contains('is-open')) return;

        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('search-open');
        burger.setAttribute('aria-expanded', 'false');

        lastFocused?.focus?.();
    }

    // Toggle bằng burger
    burger.addEventListener('click', (e) => {
        e.preventDefault();
        overlay.classList.contains('is-open') ? closeSearch() : openSearch();
    });

    // Click backdrop để đóng
    backdrop.addEventListener('click', closeSearch);

    // ESC để đóng
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && overlay.classList.contains('is-open')) {
            closeSearch();
        }
    });

    // (tuỳ chọn) Submit search
    // const form = overlay.querySelector('form.jr-searchbar');
    // form?.addEventListener('submit', (e) => {
    //     e.preventDefault();
    //     const fd = new FormData(form);
    //     const q = (fd.get('q') || '').toString().trim();
    //     if (!q) return;
    //     window.location.href = `/search.html?q=${encodeURIComponent(q)}`;
    // });
});







// ===== Align about hero image like desktop on all breakpoints =====
document.addEventListener('DOMContentLoaded', () => {
    const section = document.getElementById('jr-about');
    if (!section) return;

    const figure = section.querySelector('.about-hero__figure');
    if (!figure) return;

    // Bootstrap 5.3 defaults:
    // container-xxl max-width = 1320px (>=1400px)
    // container horizontal padding = .75rem (~12px) mỗi bên
    const DESKTOP_CONTAINER_MAX = 1320; // px
    const DESKTOP_CONTAINER_PADDING_L = 12; // px (nếu bạn chỉnh gutter, đổi số này)

    const applyBleedFixed = () => {
        const vw = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        // mép trái = (vw - maxWidth)/2 + padding trái của container trên desktop
        const left = Math.max((vw - DESKTOP_CONTAINER_MAX) / 2 + DESKTOP_CONTAINER_PADDING_L, 0);
        figure.style.setProperty('--bleed-left', left + 'px');
        figure.style.setProperty('--bleed-width', `calc(100vw - ${left}px)`);
    };

    applyBleedFixed();
    // Giữ đồng bộ khi xoay máy / đổi kích thước
    window.addEventListener('resize', applyBleedFixed);
    window.addEventListener('orientationchange', applyBleedFixed);
});

