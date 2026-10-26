/**
 * Ahmad Fix — Main JavaScript
 * Menu, Scroll, Animations, Form, Slider
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // Mobile Menu Toggle
    // ============================================
    const menuToggle = document.getElementById('menu-toggle');
    const navMenu = document.getElementById('nav-menu');
    const navClose = document.getElementById('mobile-nav-close');
    const backdrop = document.getElementById('mobile-nav-backdrop');
    
    function closeMenu() {
        if (menuToggle) menuToggle.classList.remove('active');
        if (navMenu) navMenu.classList.remove('active');
        if (backdrop) backdrop.classList.remove('active');
        document.body.style.overflow = '';
        if (menuToggle) menuToggle.setAttribute('aria-expanded', 'false');
    }

    function openMenu() {
        if (menuToggle) menuToggle.classList.add('active');
        if (navMenu) navMenu.classList.add('active');
        if (backdrop) backdrop.classList.add('active');
        document.body.style.overflow = 'hidden';
        if (menuToggle) menuToggle.setAttribute('aria-expanded', 'true');
    }

    if (backdrop) {
        backdrop.addEventListener('click', closeMenu);
    }
    
    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            if (navMenu.classList.contains('active')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        if (navClose) {
            navClose.addEventListener('click', function(e) {
                e.stopPropagation();
                closeMenu();
            });
        }

        // Close when clicking outside menu
        document.addEventListener('click', function(e) {
            if (navMenu.classList.contains('active') && !navMenu.contains(e.target) && !menuToggle.contains(e.target)) {
                closeMenu();
            }
        });
        
        // Close menu on link click
        navMenu.querySelectorAll('.nav-link, a').forEach(link => {
            link.addEventListener('click', function() {
                closeMenu();
            });
        });
    }
    
    // ============================================
    // Sticky Header on Scroll
    // ============================================
    const header = document.getElementById('main-header');
    
    if (header) {
        let lastScroll = 0;
        
        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
            
            lastScroll = currentScroll;
        }, { passive: true });
    }
    
    // ============================================
    // Scroll Reveal Animations
    // ============================================
    const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale, .stagger-children');
    
    if (revealElements.length > 0) {
        const revealObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        revealElements.forEach(el => revealObserver.observe(el));
    }
    
    // ============================================
    // Smooth Scroll for Anchor Links
    // ============================================
    document.querySelectorAll('a[href*="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            const hashIndex = href.indexOf('#');
            
            if (hashIndex === -1) return;
            
            const hash = href.substring(hashIndex);
            
            if (hash === '#') return;
            
            const target = document.querySelector(hash);
            
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                // Update URL hash
                history.pushState(null, null, hash);
            }
        });
    });
    
    // ============================================
    // Testimonials Slider
    // ============================================
    const slider = document.getElementById('testimonials-slider');
    const track = document.getElementById('testimonials-track');
    const dots = document.querySelectorAll('.testimonial-dot');
    
    if (slider && track && dots.length > 0) {
        let currentSlide = 0;
        const totalSlides = dots.length;
        let autoSlideInterval;
        
        function goToSlide(index) {
            currentSlide = index;
            const offset = -(currentSlide * 100);
            track.style.transform = `translateX(${offset}%)`;
            
            // Update dots
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === currentSlide);
            });
        }
        
        // Dot click handlers
        dots.forEach(dot => {
            dot.addEventListener('click', function() {
                const index = parseInt(this.dataset.index);
                goToSlide(index);
                resetAutoSlide();
            });
        });
        
        // Auto-slide
        function startAutoSlide() {
            autoSlideInterval = setInterval(function() {
                const next = (currentSlide + 1) % totalSlides;
                goToSlide(next);
            }, 5000);
        }
        
        function resetAutoSlide() {
            clearInterval(autoSlideInterval);
            startAutoSlide();
        }
        
        startAutoSlide();
        
        // Touch/Swipe support
        let touchStartX = 0;
        let touchEndX = 0;
        
        slider.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        
        slider.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            const diff = touchStartX - touchEndX;
            
            if (Math.abs(diff) > 50) {
                if (diff > 0 && currentSlide < totalSlides - 1) {
                    goToSlide(currentSlide + 1);
                } else if (diff < 0 && currentSlide > 0) {
                    goToSlide(currentSlide - 1);
                }
                resetAutoSlide();
            }
        }, { passive: true });
        
        // RTL support for swipe
        if (document.documentElement.dir === 'rtl') {
            track.style.direction = 'rtl';
        }
    }
    
    // ============================================
    // Contact Form AJAX Submission
    // ============================================
    const contactForm = document.getElementById('contact-form');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formMessage = document.getElementById('form-message');
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const formData = new FormData(contactForm);
            
            // Validation
            const name = (formData.get('name') || '').trim();
            const phone = (formData.get('phone') || '').trim();
            
            if (!name || !phone) {
                showFormMessage(formMessage, contactForm.dataset.msgRequired || 'Please fill in all required fields.', 'error');
                return;
            }
            
            // Show loading state
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            
            fetch(contactForm.getAttribute('action'), {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showFormMessage(formMessage, data.message, 'success');
                    contactForm.reset();
                    trackEvent('generate_lead', { method: 'contact_form', service: formData.get('service') || 'none' });
                } else {
                    showFormMessage(formMessage, data.message, 'error');
                }
            })
            .catch(error => {
                showFormMessage(formMessage, contactForm.dataset.msgNetwork || 'Network error. Please try again.', 'error');
            })
            .finally(function() {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            });
        });
    }
    
    function showFormMessage(el, message, type) {
        if (!el) return;
        el.textContent = message;
        el.className = 'form-message ' + type;
        el.style.display = 'block';
        
        // Auto-hide after 8 seconds
        setTimeout(function() {
            el.style.display = 'none';
        }, 8000);
    }
    
    // ============================================
    // FAQ Accordion
    // ============================================
    document.querySelectorAll('.faq-question').forEach(function(question) {
        question.addEventListener('click', function() {
            const item = this.closest('.faq-item');
            const isActive = item.classList.contains('active');
            
            // Close all FAQ items
            document.querySelectorAll('.faq-item').forEach(function(faq) {
                faq.classList.remove('active');
                const btn = faq.querySelector('.faq-question');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            });
            
            // Toggle the clicked item
            if (!isActive) {
                item.classList.add('active');
                this.setAttribute('aria-expanded', 'true');
            }
        });
    });
    
    // ============================================
    // Lazy Loading Images
    // ============================================
    const lazyImages = document.querySelectorAll('img[loading="lazy"]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(function(img) {
            imageObserver.observe(img);
        });
    }
    
    // ============================================
    // Counter Animation
    // ============================================
    const counters = document.querySelectorAll('.counter[data-target]');
    
    if (counters.length > 0) {
        const counterObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        counters.forEach(function(counter) {
            counterObserver.observe(counter);
        });
    }
    
    function animateCounter(el) {
        const target = parseInt(el.dataset.target);
        const duration = 2000;
        const step = Math.ceil(target / (duration / 16));
        let current = 0;
        
        const timer = setInterval(function() {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            el.textContent = current + '+';
        }, 16);
    }
    
    // ============================================
    // Conversion tracking (Google Analytics 4)
    // Counts Call / WhatsApp clicks so you can see which pages bring customers.
    // Does nothing until GA4_ID is set in config/config.php.
    // ============================================
    function trackEvent(name, params) {
        if (typeof window.gtag === 'function') {
            window.gtag('event', name, params || {});
        }
    }
    window.ahmadFixTrack = trackEvent;

    document.addEventListener('click', function(e) {
        const link = e.target.closest ? e.target.closest('a[href]') : null;
        if (!link) return;
        const href = link.getAttribute('href') || '';
        const where = link.id || link.className.toString().split(' ')[0] || 'link';
        if (href.indexOf('tel:') === 0) {
            trackEvent('phone_call_click', { link_location: where, page_path: location.pathname });
        } else if (href.indexOf('wa.me') !== -1 || href.indexOf('whatsapp') !== -1) {
            trackEvent('whatsapp_click', { link_location: where, page_path: location.pathname });
        }
    }, { passive: true });

});
