(function () {
    'use strict';

    const isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ── Apply reveal delay from data attribute ── */
    document.querySelectorAll('[data-reveal-delay]').forEach(el => {
        const delay = parseFloat(el.getAttribute('data-reveal-delay')) || 0;
        el.style.setProperty('--reveal-delay', delay);
    });

    /* ── Mobile menu ── */
    const menuToggle = document.getElementById('menuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
            document.body.classList.toggle('menu-open');
            menuToggle.innerHTML = mobileMenu.classList.contains('active')
                ? '<i class="fa-solid fa-xmark"></i>'
                : '<i class="fa-solid fa-bars"></i>';
        });
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('active');
                document.body.classList.remove('menu-open');
                menuToggle.innerHTML = '<i class="fa-solid fa-bars"></i>';
            });
        });
    }

    /* ── Header scroll effect ── */
    const header = document.querySelector('.site-header');
    if (header) {
        const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 40);
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    /* ── Dropdown nav (desktop) ── */
    document.querySelectorAll('.nav-dropdown').forEach(dropdown => {
        const trigger = dropdown.querySelector('.nav-dropdown-trigger');
        if (!trigger) return;
        trigger.addEventListener('click', e => {
            e.preventDefault();
            dropdown.classList.toggle('open');
            document.querySelectorAll('.nav-dropdown').forEach(d => {
                if (d !== dropdown) d.classList.remove('open');
            });
        });
    });
    document.addEventListener('click', e => {
        if (!e.target.closest('.nav-dropdown')) {
            document.querySelectorAll('.nav-dropdown').forEach(d => d.classList.remove('open'));
        }
    });

    /* ── Scroll reveal animations ── */
    const revealEls = document.querySelectorAll('[data-reveal]');
    if (revealEls.length && 'IntersectionObserver' in window && !prefersReducedMotion) {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -30px 0px' });
        revealEls.forEach(el => observer.observe(el));
    } else {
        revealEls.forEach(el => el.classList.add('revealed'));
    }

    /* ── Testimonial carousel ── */
    const carousel = document.querySelector('.testimonial-carousel');
    if (carousel) {
        const slides = carousel.querySelectorAll('.testimonial-slide');
        const dots = carousel.querySelectorAll('.carousel-dot');
        const counter = carousel.querySelector('.carousel-counter');
        let current = 0;
        let timer;

        function goTo(index) {
            if (!slides.length) return;
            current = (index + slides.length) % slides.length;
            slides.forEach((s, i) => s.classList.toggle('active', i === current));
            dots.forEach((d, i) => d.classList.toggle('active', i === current));
            if (counter) counter.textContent = `${current + 1} of ${slides.length}`;
        }

        dots.forEach((dot, i) => dot.addEventListener('click', () => { goTo(i); resetTimer(); }));

        const prev = carousel.querySelector('.carousel-prev');
        const next = carousel.querySelector('.carousel-next');
        if (prev) prev.addEventListener('click', () => { goTo(current - 1); resetTimer(); });
        if (next) next.addEventListener('click', () => { goTo(current + 1); resetTimer(); });

        function resetTimer() {
            clearInterval(timer);
            if (slides.length > 1) timer = setInterval(() => goTo(current + 1), 6000);
        }

        goTo(0);
        resetTimer();
    }

    /* ── FAQ accordion ── */
    document.querySelectorAll('.faq-item').forEach(item => {
        const btn = item.querySelector('.faq-question');
        if (!btn) return;
        btn.addEventListener('click', () => {
            const isOpen = item.classList.contains('open');
            document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
            if (!isOpen) item.classList.add('open');
        });
    });

    /* ── Gallery marquee auto-scroll ── */
    const marquee = document.querySelector('.gallery-marquee');
    if (marquee && !prefersReducedMotion) {
        const track = marquee.querySelector('.gallery-track');
        if (track && track.children.length > 0) {
            track.innerHTML += track.innerHTML;
            let pos = 0;
            const speed = 0.6;
            function animateGallery() {
                pos += speed;
                const half = track.scrollWidth / 2;
                if (pos >= half) pos = 0;
                track.style.transform = `translateX(-${pos}px)`;
                requestAnimationFrame(animateGallery);
            }
            requestAnimationFrame(animateGallery);
        }
    }

    /* ── Counter animation for stats ── */
    document.querySelectorAll('[data-count]').forEach(el => {
        const target = el.getAttribute('data-count');
        const num = parseInt(target.replace(/[^0-9]/g, ''), 10);
        if (!num || !('IntersectionObserver' in window) || prefersReducedMotion) return;

        const suffix = target.replace(/[0-9,]/g, '');
        const counterObs = new IntersectionObserver(entries => {
            if (!entries[0].isIntersecting) return;
            counterObs.disconnect();
            const duration = 1500;
            const start = performance.now();
            function tick(now) {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                const val = Math.floor(num * eased);
                el.textContent = val.toLocaleString() + suffix;
                if (progress < 1) requestAnimationFrame(tick);
                else el.textContent = target;
            }
            requestAnimationFrame(tick);
        }, { threshold: 0.5 });
        counterObs.observe(el);
    });

    /* ── Smooth scroll for anchor links ── */
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const id = a.getAttribute('href').slice(1);
            const el = document.getElementById(id);
            if (el) {
                e.preventDefault();
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    /* ── Parallax on hero visual ── */
    const heroVisual = document.querySelector('.hero-visual');
    if (heroVisual && !isTouch && !prefersReducedMotion) {
        window.addEventListener('scroll', () => {
            const scroll = window.scrollY;
            if (scroll < window.innerHeight) {
                heroVisual.style.transform = `translateY(${scroll * 0.06}px)`;
            }
        }, { passive: true });
    }
})();
