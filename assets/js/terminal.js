/**
 * terminal.js — PHP Security Lab
 * Retro Terminal Effects: Typing, Blink Cursor, Console-style Animations
 */

(function () {
    'use strict';

    /* ── Typing Effect ─────────────────────────────────────── */

    const SUBTITLE_MESSAGES = [
        'Initializing security lab environment...',
        'Loading SQL Injection modules... OK',
        'Loading XSS protection modules... OK',
        'Lab ready. For educational use only.',
        'root@security-lab:~$ _',
    ];

    function typeText(element, text, speed, callback) {
        let i = 0;
        element.textContent = '';
        const cursor = document.createElement('span');
        cursor.className = 'blink';
        cursor.textContent = '█';
        element.appendChild(cursor);

        const interval = setInterval(() => {
            if (i < text.length) {
                cursor.before(text.charAt(i));
                i++;
            } else {
                clearInterval(interval);
                if (callback) {
                    setTimeout(callback, 400);
                }
            }
        }, speed);
    }

    function runSequence(element, messages, index) {
        if (index >= messages.length) return;

        const msg   = messages[index];
        const speed = index === messages.length - 1 ? 60 : 30;

        typeText(element, msg, speed, () => {
            if (index < messages.length - 1) {
                setTimeout(() => {
                    element.textContent = '';
                    runSequence(element, messages, index + 1);
                }, 300);
            }
        });
    }

    /* ── Console Boot Log ──────────────────────────────────── */

    function bootLog() {
        const LOG_LINES = [
            '[  0.001] BOOT: php-security-lab kernel loaded',
            '[  0.012] MEM: 512M available',
            '[  0.034] SQL: MySQL connection pool initialized',
            '[  0.051] SEC: WAF module loaded (demo mode)',
            '[  0.072] WEB: HTTP server listening on :80',
            '[  0.090] LAB: All modules ready',
        ];

        const container = document.getElementById('boot-log');
        if (!container) return;

        LOG_LINES.forEach((line, i) => {
            setTimeout(() => {
                const div = document.createElement('div');
                div.textContent = line;
                div.style.opacity = '0';
                div.style.transition = 'opacity 0.3s';
                container.appendChild(div);
                requestAnimationFrame(() => { div.style.opacity = '1'; });
            }, i * 120);
        });
    }

    /* ── Glitch Effect on Hover ────────────────────────────── */

    function initGlitch() {
        const titles = document.querySelectorAll('.card-title, .page-title');
        titles.forEach(el => {
            el.addEventListener('mouseenter', () => {
                el.dataset.original = el.textContent;
                let count = 0;
                const glitchChars = '!@#$%^&*01█▓▒░';

                const interval = setInterval(() => {
                    if (count > 6) {
                        clearInterval(interval);
                        el.textContent = el.dataset.original;
                        return;
                    }
                    el.textContent = el.dataset.original
                        .split('')
                        .map((c, i) => {
                            if (c === ' ') return c;
                            return Math.random() < 0.15
                                ? glitchChars[Math.floor(Math.random() * glitchChars.length)]
                                : c;
                        })
                        .join('');
                    count++;
                }, 50);
            });
        });
    }

    /* ── Card Hover Terminal Sound (visual feedback) ────────── */

    function initCardFeedback() {
        const cards = document.querySelectorAll('.demo-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transition = 'all 0.15s ease';
            });
        });
    }

    /* ── Fade-in Sections ──────────────────────────────────── */

    function initFadeIn() {
        const elements = document.querySelectorAll(
            '.terminal-card, .warning-box, .secure-box, .edu-section, .demo-card, .cheatsheet'
        );

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity    = '1';
                    entry.target.style.transform  = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08 });

        elements.forEach(el => {
            el.style.opacity    = '0';
            el.style.transform  = 'translateY(12px)';
            el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            observer.observe(el);
        });
    }

    /* ── Query Highlight Animation ─────────────────────────── */

    function initQueryHighlight() {
        const injected = document.querySelectorAll('.query-injected');
        injected.forEach(el => {
            el.style.animation = 'dangerPulse 2s ease-in-out infinite';
        });

        const style = document.createElement('style');
        style.textContent = `
            @keyframes dangerPulse {
                0%, 100% { box-shadow: 0 0 8px rgba(255, 32, 32, 0.2); }
                50%       { box-shadow: 0 0 20px rgba(255, 32, 32, 0.5); }
            }
            @keyframes securePulse {
                0%, 100% { box-shadow: 0 0 6px rgba(0, 255, 65, 0.1); }
                50%       { box-shadow: 0 0 16px rgba(0, 255, 65, 0.3); }
            }
        `;
        document.head.appendChild(style);

        const secureQueries = document.querySelectorAll('.secure-query');
        secureQueries.forEach(el => {
            el.style.animation = 'securePulse 3s ease-in-out infinite';
        });
    }

    /* ── Cursor Blink for Form Inputs ──────────────────────── */

    function initInputEffects() {
        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.style.caretColor = '#00ff41';
            });
        });
    }

    /* ── Copy Payload to Input (click on attack table) ─────── */

    function initPayloadClicker() {
        const attackRows = document.querySelectorAll('.attack-row:not(.header-row)');
        attackRows.forEach(row => {
            const codeEl = row.querySelector('code');
            if (!codeEl) return;

            row.style.cursor = 'pointer';
            row.title = 'Klik untuk copy payload ke input';

            row.addEventListener('click', () => {
                const payload = codeEl.textContent.trim();
                const usernameInput = document.querySelector('input[name="username"]');
                const commentInput  = document.querySelector('textarea[name="comment"]');

                if (usernameInput) {
                    usernameInput.value = payload;
                    usernameInput.focus();
                    flashElement(usernameInput);
                } else if (commentInput) {
                    commentInput.value = payload;
                    commentInput.focus();
                    flashElement(commentInput);
                }

                // Visual flash on clicked row
                row.style.background = 'rgba(0, 255, 65, 0.1)';
                setTimeout(() => { row.style.background = ''; }, 300);
            });
        });
    }

    function flashElement(el) {
        const original = el.style.borderColor;
        el.style.borderColor = '#00ff41';
        el.style.boxShadow   = '0 0 12px rgba(0, 255, 65, 0.5)';
        setTimeout(() => {
            el.style.borderColor = original;
            el.style.boxShadow   = '';
        }, 600);
    }

    /* ── Matrix Rain Cursor Trail ──────────────────────────── */

    function initCursorTrail() {
        const chars = '01アイウエオカキクケコ#$%@!';
        let last = 0;

        document.addEventListener('mousemove', (e) => {
            const now = Date.now();
            if (now - last < 80) return;
            last = now;

            const span = document.createElement('span');
            span.textContent = chars[Math.floor(Math.random() * chars.length)];
            span.style.cssText = `
                position: fixed;
                left: ${e.clientX + 4}px;
                top:  ${e.clientY - 8}px;
                color: #00ff41;
                font-family: 'Share Tech Mono', monospace;
                font-size: 11px;
                pointer-events: none;
                z-index: 10000;
                opacity: 0.7;
                transition: opacity 0.5s, transform 0.5s;
                text-shadow: 0 0 6px #00ff41;
            `;
            document.body.appendChild(span);

            requestAnimationFrame(() => {
                span.style.opacity   = '0';
                span.style.transform = 'translateY(-20px)';
            });

            setTimeout(() => { span.remove(); }, 500);
        });
    }

    /* ── Init All ──────────────────────────────────────────── */

    document.addEventListener('DOMContentLoaded', () => {

        // Typing subtitle on index page
        const subtitleEl = document.getElementById('subtitle');
        if (subtitleEl) {
            setTimeout(() => {
                runSequence(subtitleEl, SUBTITLE_MESSAGES, 0);
            }, 400);
        }

        bootLog();
        initGlitch();
        initCardFeedback();
        initFadeIn();
        initQueryHighlight();
        initInputEffects();
        initPayloadClicker();
        initCursorTrail();

        // Console-style welcome message
        console.log('%c PHP SECURITY LAB ', 'background:#00ff41;color:#000;font-family:monospace;font-size:16px;font-weight:bold;padding:4px 8px;');
        console.log('%c[EDUCATIONAL USE ONLY] Do NOT use these techniques on real websites without permission.', 'color:#00ff41;font-family:monospace;');
        console.log('%cLoaded: SQL Injection Demo + XSS Demo', 'color:#00b32d;font-family:monospace;');
    });

})();
