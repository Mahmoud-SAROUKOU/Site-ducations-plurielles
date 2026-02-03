// ============================================
// OPTIMISATIONS MOBILE & CROSS-BROWSER
// Fonctionnalités JavaScript pour mobile/iOS
// ============================================

(function() {
    'use strict';

    // ===== DÉTECTION ENVIRONNEMENT =====
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
    const isStandalone = window.navigator.standalone === true || window.matchMedia('(display-mode: standalone)').matches;

    // ===== FIX HAUTEUR VIEWPORT iOS (100VH) =====
    function setVhProperty() {
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    }

    // Mise à jour sur resize et orientation change
    if (isMobile) {
        setVhProperty();
        window.addEventListener('resize', setVhProperty);
        window.addEventListener('orientationchange', () => {
            setTimeout(setVhProperty, 100);
        });
    }

    // ===== PRÉVENTION ZOOM DOUBLE-TAP iOS =====
    let lastTouchEnd = 0;
    document.addEventListener('touchend', function(event) {
        const now = Date.now();
        if (now - lastTouchEnd <= 300) {
            event.preventDefault();
        }
        lastTouchEnd = now;
    }, { passive: false });

    // ===== LAZY LOADING IMAGES =====
    function lazyLoadImages() {
        const images = document.querySelectorAll('img[data-src]');
        
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        imageObserver.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px'
            });

            images.forEach(img => imageObserver.observe(img));
        } else {
            // Fallback pour navigateurs anciens
            images.forEach(img => {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
            });
        }
    }

    // ===== GESTION MENU MOBILE =====
    function initMobileMenu() {
        const menuToggle = document.querySelector('.menu-toggle');
        const mobileMenu = document.querySelector('.mobile-menu');
        const body = document.body;

        if (menuToggle && mobileMenu) {
            menuToggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const isOpen = mobileMenu.classList.contains('open');
                
                if (isOpen) {
                    mobileMenu.classList.remove('open');
                    body.classList.remove('menu-open');
                    menuToggle.setAttribute('aria-expanded', 'false');
                } else {
                    mobileMenu.classList.add('open');
                    body.classList.add('menu-open');
                    menuToggle.setAttribute('aria-expanded', 'true');
                }
            });

            // Fermer le menu au clic sur un lien
            const menuLinks = mobileMenu.querySelectorAll('a');
            menuLinks.forEach(link => {
                link.addEventListener('click', () => {
                    mobileMenu.classList.remove('open');
                    body.classList.remove('menu-open');
                    menuToggle.setAttribute('aria-expanded', 'false');
                });
            });

            // Fermer le menu au clic en dehors
            document.addEventListener('click', (e) => {
                if (mobileMenu.classList.contains('open') && 
                    !mobileMenu.contains(e.target) && 
                    !menuToggle.contains(e.target)) {
                    mobileMenu.classList.remove('open');
                    body.classList.remove('menu-open');
                    menuToggle.setAttribute('aria-expanded', 'false');
                }
            });
        }
    }

    // ===== SMOOTH SCROLL OPTIMISÉ =====
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;
                
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    
                    const headerOffset = document.querySelector('.site-header')?.offsetHeight || 0;
                    const targetPosition = target.offsetTop - headerOffset - 20;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    // ===== OPTIMISATION PERFORMANCE SCROLL =====
    let ticking = false;
    let lastScrollPos = 0;

    function optimizedScrollHandler(callback) {
        return function() {
            const scrollPos = window.pageYOffset;
            
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    callback(scrollPos);
                    ticking = false;
                });
                ticking = true;
            }
            
            lastScrollPos = scrollPos;
        };
    }

    // Header sticky avec animation
    function handleHeaderScroll(scrollPos) {
        const header = document.querySelector('.site-header');
        if (!header) return;
        
        if (scrollPos > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }

    window.addEventListener('scroll', optimizedScrollHandler(handleHeaderScroll), { passive: true });

    // ===== GESTION ORIENTATION =====
    if (window.screen && window.screen.orientation) {
        window.screen.orientation.addEventListener('change', () => {
            // Recalculer les hauteurs
            setVhProperty();
            
            // Message optionnel pour utilisateur
            if (window.screen.orientation.type.includes('landscape')) {
                console.log('Mode paysage activé');
            }
        });
    }

    // ===== INSTALL PWA PROMPT =====
    let deferredPrompt;
    
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        
        // Afficher le bouton d'installation (si vous en avez un)
        const installBtn = document.querySelector('#install-app-btn');
        if (installBtn) {
            installBtn.style.display = 'block';
            
            installBtn.addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    console.log(`Installation: ${outcome}`);
                    deferredPrompt = null;
                    installBtn.style.display = 'none';
                }
            });
        }
    });

    // ===== DÉTECTION CONNEXION =====
    function updateOnlineStatus() {
        const status = navigator.onLine ? 'online' : 'offline';
        document.body.classList.toggle('is-offline', !navigator.onLine);
        
        if (!navigator.onLine) {
            console.warn('Mode hors ligne - Fonctionnalités limitées');
        }
    }

    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);

    // ===== FIX INPUT FOCUS iOS (éviter zoom) =====
    if (isIOS) {
        const inputs = document.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            // S'assurer que la taille de police est >= 16px
            const computedStyle = window.getComputedStyle(input);
            const fontSize = parseFloat(computedStyle.fontSize);
            
            if (fontSize < 16) {
                input.style.fontSize = '16px';
            }
        });
    }

    // ===== GESTION TOUCH EVENTS =====
    function initTouchHandlers() {
        // Swipe pour fermer menu mobile
        let touchStartX = 0;
        let touchEndX = 0;
        
        const mobileMenu = document.querySelector('.mobile-menu');
        if (mobileMenu) {
            mobileMenu.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });
            
            mobileMenu.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
            }, { passive: true });
            
            function handleSwipe() {
                if (touchEndX < touchStartX - 50) {
                    // Swipe left - fermer menu
                    mobileMenu.classList.remove('open');
                    document.body.classList.remove('menu-open');
                }
            }
        }
    }

    // ===== AMÉLIORATION ACCESSIBILITÉ =====
    function enhanceAccessibility() {
        // Skip link
        const skipLink = document.createElement('a');
        skipLink.href = '#main-content';
        skipLink.className = 'skip-link';
        skipLink.textContent = 'Aller au contenu principal';
        document.body.insertBefore(skipLink, document.body.firstChild);
        
        // ARIA labels pour boutons sans texte
        document.querySelectorAll('button:not([aria-label])').forEach(btn => {
            if (!btn.textContent.trim()) {
                const icon = btn.querySelector('i');
                if (icon) {
                    const label = icon.className.includes('menu') ? 'Menu' :
                                  icon.className.includes('search') ? 'Rechercher' :
                                  icon.className.includes('close') ? 'Fermer' : 'Bouton';
                    btn.setAttribute('aria-label', label);
                }
            }
        });
    }

    // ===== PRÉCHARGEMENT STRATÉGIQUE =====
    function preloadCriticalAssets() {
        // Précharger les images au-dessus de la ligne de flottaison
        const heroImages = document.querySelectorAll('.hero-section img, .featured-article img');
        heroImages.forEach(img => {
            if (img.dataset.src) {
                const link = document.createElement('link');
                link.rel = 'preload';
                link.as = 'image';
                link.href = img.dataset.src;
                document.head.appendChild(link);
            }
        });
    }

    // ===== GESTION ERREURS IMAGES =====
    function handleImageErrors() {
        document.addEventListener('error', (e) => {
            if (e.target.tagName === 'IMG') {
                e.target.style.display = 'none';
                console.warn(`Image non chargée: ${e.target.src}`);
            }
        }, true);
    }

    // ===== ANALYTICS MOBILE (optionnel) =====
    function trackMobileUsage() {
        if (window.gtag) {
            gtag('event', 'device_type', {
                'event_category': 'User Device',
                'event_label': isMobile ? 'Mobile' : 'Desktop',
                'is_ios': isIOS,
                'is_pwa': isStandalone
            });
        }
    }

    // ===== INITIALISATION =====
    function init() {
        console.log('🚀 Optimisations mobile initialisées');
        
        // Lancer toutes les initialisations
        lazyLoadImages();
        initMobileMenu();
        initSmoothScroll();
        initTouchHandlers();
        enhanceAccessibility();
        preloadCriticalAssets();
        handleImageErrors();
        updateOnlineStatus();
        
        if (isMobile) {
            trackMobileUsage();
        }
        
        // Ajouter classe au body pour CSS spécifiques
        document.body.classList.add(isMobile ? 'is-mobile' : 'is-desktop');
        if (isIOS) document.body.classList.add('is-ios');
        if (isSafari) document.body.classList.add('is-safari');
        if (isStandalone) document.body.classList.add('is-pwa');
    }

    // Lancer au chargement du DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // ===== EXPORT POUR DEBUG =====
    window.MOBILE_UTILS = {
        isMobile,
        isIOS,
        isSafari,
        isStandalone,
        setVhProperty,
        lazyLoadImages
    };

})();
