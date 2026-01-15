// ============================================
// GESTIONNAIRE DE NAVIGATION UNIVERSEL
// ============================================

class NavigationManager {
    constructor() {
        this.currentPage = 'accueil';
        this.isInitialized = false;
        this.isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        
        console.log('NavigationManager initialisé - iOS:', this.isIOS);
    }
    
    init() {
        if (this.isInitialized) return;
        
        this.setupPages();
        this.setupNavigationLinks();
        this.setupHashChange();
        this.setupInitialPage();
        this.setupContentLinks();
        
        this.isInitialized = true;
        console.log('NavigationManager prêt');
    }
    
    setupPages() {
        // S'assurer que toutes les pages existent
        this.pages = ['accueil', 'articles', 'videos', 'ressources', 'apropos'];
        
        // Cacher toutes les pages sauf l'accueil
        this.pages.forEach(pageId => {
            const page = document.getElementById(pageId);
            if (page) {
                page.style.display = 'none';
                page.classList.remove('active');
            }
        });
    }
    
    setupNavigationLinks() {
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            // Supprimer les anciens écouteurs
            link.removeEventListener('click', this.handleNavClick);
            link.removeEventListener('touchstart', this.handleNavTouch);
            
            // Nouveaux écouteurs
            link.addEventListener('click', (e) => this.handleNavClick(e));
            
            if (this.isIOS) {
                link.addEventListener('touchstart', (e) => this.handleNavTouch(e), { passive: false });
            }
        });
    }
    
    handleNavClick(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const link = e.currentTarget;
        const href = link.getAttribute('href');
        
        if (href && href.startsWith('#')) {
            const pageId = href.substring(1);
            this.navigateTo(pageId);
        }
    }
    
    handleNavTouch(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const link = e.currentTarget;
        const href = link.getAttribute('href');
        
        if (href && href.startsWith('#')) {
            const pageId = href.substring(1);
            this.navigateTo(pageId);
        }
    }
    
    navigateTo(pageId) {
        console.log('Navigation vers:', pageId);
        
        // Vérifier si la page existe
        const targetPage = document.getElementById(pageId);
        if (!targetPage) {
            console.error('Page non trouvée:', pageId);
            return;
        }
        
        // Mettre à jour l'état
        this.currentPage = pageId;
        
        // 1. Cacher toutes les pages
        this.pages.forEach(id => {
            const page = document.getElementById(id);
            if (page) {
                page.classList.remove('active');
                page.style.display = 'none';
                
                // Réinitialiser les animations
                page.style.opacity = '0';
                page.style.transform = 'translateY(20px)';
            }
        });
        
        // 2. Afficher la page cible
        targetPage.classList.add('active');
        targetPage.style.display = 'block';
        
        // 3. Animation d'entrée
        setTimeout(() => {
            targetPage.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            targetPage.style.opacity = '1';
            targetPage.style.transform = 'translateY(0)';
        }, 10);
        
        // 4. Mettre à jour les liens actifs
        this.updateActiveLink(pageId);
        
        // 5. Scroll vers le haut
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
        
        // 6. Mettre à jour l'URL
        history.pushState({ page: pageId }, '', `#${pageId}`);
        
        // 7. Fermer le menu mobile si ouvert
        this.closeMobileMenu();
        
        console.log('Navigation terminée vers:', pageId);
    }
    
    updateActiveLink(pageId) {
        // Retirer active de tous les liens
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        
        // Ajouter active au lien correspondant
        const activeLink = document.querySelector(`.nav-link[href="#${pageId}"]`);
        if (activeLink) {
            activeLink.classList.add('active');
        }
    }
    
    closeMobileMenu() {
        const menuToggle = document.getElementById('menuToggle');
        const mainNav = document.getElementById('mainNavigation');
        const menuOverlay = document.getElementById('menuOverlay');
        
        if (menuToggle && mainNav && mainNav.classList.contains('is-open')) {
            menuToggle.classList.remove('active');
            mainNav.classList.remove('is-open');
            
            if (menuOverlay) {
                menuOverlay.classList.remove('active');
            }
            
            document.body.classList.remove('menu-open');
            menuToggle.setAttribute('aria-expanded', 'false');
            
            // Restaurer le scroll
            document.body.style.overflow = '';
            document.body.style.position = '';
            document.body.style.width = '';
            document.body.style.height = '';
        }
    }
    
    setupHashChange() {
        window.addEventListener('popstate', (e) => {
            const hash = window.location.hash;
            if (hash && hash.startsWith('#')) {
                const pageId = hash.substring(1);
                if (this.pages.includes(pageId)) {
                    this.navigateTo(pageId);
                }
            } else {
                this.navigateTo('accueil');
            }
        });
        
        window.addEventListener('hashchange', () => {
            const hash = window.location.hash;
            if (hash && hash.startsWith('#')) {
                const pageId = hash.substring(1);
                if (this.pages.includes(pageId)) {
                    this.navigateTo(pageId);
                }
            }
        });
    }
    
    setupInitialPage() {
        setTimeout(() => {
            const hash = window.location.hash;
            
            if (hash && hash.startsWith('#')) {
                const pageId = hash.substring(1);
                if (this.pages.includes(pageId)) {
                    this.navigateTo(pageId);
                    return;
                }
            }
            
            // Par défaut : accueil
            this.navigateTo('accueil');
        }, 100);
    }
    
    setupContentLinks() {
        // Liens dans le contenu (boutons, etc.)
        const contentLinks = document.querySelectorAll('[data-page]');
        
        contentLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const pageId = link.getAttribute('data-page');
                if (pageId) {
                    this.navigateTo(pageId);
                }
            });
        });
    }
}

// ============================================
// MENU BURGER
// ============================================

function initMenuBurger() {
    const menuToggle = document.getElementById('menuToggle');
    const mainNav = document.getElementById('mainNavigation');
    const menuOverlay = document.getElementById('menuOverlay');
    const body = document.body;
    
    if (!menuToggle || !mainNav) return;
    
    function toggleMenu() {
        const isOpening = !mainNav.classList.contains('is-open');
        
        menuToggle.classList.toggle('active');
        mainNav.classList.toggle('is-open');
        
        if (menuOverlay) {
            menuOverlay.classList.toggle('active');
        }
        
        body.classList.toggle('menu-open');
        menuToggle.setAttribute('aria-expanded', isOpening);
        
        if (isOpening) {
            document.body.style.overflow = 'hidden';
            document.body.style.position = 'fixed';
            document.body.style.width = '100%';
            document.body.style.height = '100%';
        } else {
            document.body.style.overflow = '';
            document.body.style.position = '';
            document.body.style.width = '';
            document.body.style.height = '';
        }
    }
    
    function closeMenu() {
        menuToggle.classList.remove('active');
        mainNav.classList.remove('is-open');
        if (menuOverlay) menuOverlay.classList.remove('active');
        body.classList.remove('menu-open');
        menuToggle.setAttribute('aria-expanded', 'false');
        
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.width = '';
        document.body.style.height = '';
    }
    
    // Événements
    menuToggle.addEventListener('click', toggleMenu);
    
    if (menuOverlay) {
        menuOverlay.addEventListener('click', closeMenu);
    }
    
    // Les liens de navigation gèrent eux-mêmes la fermeture via NavigationManager
}

// ============================================
// SLIDER ACCUEIL
// ============================================

function initSlider() {
    const sliderTrack = document.querySelector('.slider-track');
    const slides = document.querySelectorAll('.slider-slide');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.querySelector('.slider-prev');
    const nextBtn = document.querySelector('.slider-next');
    
    if (!sliderTrack || slides.length === 0) return;
    
    let currentSlide = 0;
    const totalSlides = slides.length;
    
    function goToSlide(n) {
        currentSlide = (n + totalSlides) % totalSlides;
        
        sliderTrack.style.transform = `translateX(-${currentSlide * 100}%)`;
        
        slides.forEach((slide, index) => {
            slide.classList.toggle('active', index === currentSlide);
        });
        
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
        });
    }
    
    if (prevBtn) prevBtn.addEventListener('click', () => goToSlide(currentSlide - 1));
    if (nextBtn) nextBtn.addEventListener('click', () => goToSlide(currentSlide + 1));
    
    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            const slideIndex = parseInt(dot.getAttribute('data-slide'));
            goToSlide(slideIndex);
        });
    });
    
    // Auto-play
    let slideInterval = setInterval(() => goToSlide(currentSlide + 1), 5000);
    
    if (sliderTrack) {
        sliderTrack.addEventListener('mouseenter', () => clearInterval(slideInterval));
        sliderTrack.addEventListener('mouseleave', () => {
            slideInterval = setInterval(() => goToSlide(currentSlide + 1), 5000);
        });
    }
    
    goToSlide(0);
}

// ============================================
// ARTICLES
// ============================================

function initArticles() {
    const grid = document.getElementById('articlesGrid');
    const loadMoreBtn = document.getElementById('loadMore');
    
    if (!grid || !window.appData?.articlesData) return;
    
    let visibleCount = 2;
    let currentCategory = 'all';
    
    function renderArticles() {
        grid.innerHTML = '';
        
        const filtered = currentCategory === 'all' 
            ? window.appData.articlesData
            : window.appData.articlesData.filter(article => article.category === currentCategory);
        
        filtered.slice(0, visibleCount).forEach(article => {
            const articleElement = document.createElement('article');
            articleElement.className = 'article-card fade-up';
            articleElement.innerHTML = `
                <div class="article-image">
                    <img src="${article.image}" alt="${article.title}">
                    <div class="article-category">${getCategoryLabel(article.category)}</div>
                </div>
                <div class="article-content">
                    <div class="article-meta">
                        <span><i class="far fa-calendar"></i> ${article.date}</span>
                        <span><i class="far fa-clock"></i> ${article.readTime}</span>
                    </div>
                    <h3>${article.title}</h3>
                    <p>${article.excerpt}</p>
                    <div class="article-tags">
                        ${article.tags.map(tag => `<span class="tag">${tag}</span>`).join('')}
                    </div>
                    <a href="#" class="read-more">Lire l'article <i class="fas fa-arrow-right"></i></a>
                </div>
            `;
            grid.appendChild(articleElement);
        });
        
        if (loadMoreBtn) {
            loadMoreBtn.style.display = visibleCount >= filtered.length ? 'none' : 'block';
        }
    }
    
    function getCategoryLabel(category) {
        const labels = {
            'parentalite': 'Parentalité',
            'droits': 'Droits',
            'protection': 'Protection',
            'education': 'Éducation',
            'sante': 'Santé',
            'developpement': 'Développement'
        };
        return labels[category] || category;
    }
    
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            visibleCount += 2;
            renderArticles();
        });
    }
    
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentCategory = this.getAttribute('data-category');
            visibleCount = 2;
            renderArticles();
        });
    });
    
    renderArticles();
}

// ============================================
// BACK TO TOP
// ============================================

function initBackToTop() {
    const backToTopBtn = document.querySelector('.back-to-top');
    
    if (!backToTopBtn) return;
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('visible');
        } else {
            backToTopBtn.classList.remove('visible');
        }
    });
    
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// ============================================
// NEWSLETTER
// ============================================

function initNewsletter() {
    const newsletterForms = document.querySelectorAll('.newsletter-form');
    
    newsletterForms.forEach(form => {
        const input = form.querySelector('input[type="email"]');
        const button = form.querySelector('button');
        
        if (!input || !button) return;
        
        button.addEventListener('click', function() {
            const email = input.value.trim();
            
            if (email && email.includes('@')) {
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                button.disabled = true;
                
                setTimeout(() => {
                    alert(`Merci ! Vous êtes inscrit avec : ${email}`);
                    input.value = '';
                    button.innerHTML = originalText;
                    button.disabled = false;
                }, 1000);
            } else {
                alert('Veuillez entrer une adresse email valide.');
                input.focus();
            }
        });
        
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                button.click();
            }
        });
    });
}

// ============================================
// ANIMATIONS
// ============================================

function initAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    document.querySelectorAll('.fade-up').forEach(el => {
        observer.observe(el);
    });
}

// ============================================
// CORRECTIONS iOS
// ============================================

function initIOSCorrections() {
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    
    if (!isIOS) return;
    
    console.log('iOS détecté - Application des corrections');
    
    function setRealViewportHeight() {
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    }
    
    setRealViewportHeight();
    window.addEventListener('resize', setRealViewportHeight);
    window.addEventListener('orientationchange', setRealViewportHeight);
    
    document.addEventListener('touchstart', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            e.target.style.fontSize = '16px';
        }
    }, { passive: true });
    
    const buttons = document.querySelectorAll('button, a');
    buttons.forEach(btn => {
        btn.style.cursor = 'pointer';
    });
}

// ============================================
// INITIALISATION
// ============================================

// Instance globale de NavigationManager
window.navigationManager = new NavigationManager();

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== INITIALISATION DU SITE ===');
    
    // 1. Corrections iOS
    initIOSCorrections();
    
    // 2. Navigation (LE PLUS IMPORTANT)
    window.navigationManager.init();
    
    // 3. Menu burger
    initMenuBurger();
    
    // 4. Autres fonctionnalités
    initSlider();
    initArticles();
    initBackToTop();
    initNewsletter();
    initAnimations();
    
    console.log('=== SITE INITIALISÉ AVEC SUCCÈS ===');
    
    // Backup: Vérifier que la navigation fonctionne
    setTimeout(() => {
        const activePage = document.querySelector('.page-content.active');
        if (!activePage) {
            console.warn('Aucune page active - Correction automatique');
            window.navigationManager.navigateTo('accueil');
        }
    }, 500);
});

// ============================================
// SCRIPT D'URGENCE
// ============================================

// Si rien ne fonctionne, ce script corrige tout
(function() {
    setTimeout(function() {
        const activePages = document.querySelectorAll('.page-content.active');
        
        if (activePages.length === 0) {
            console.error('ERREUR CRITIQUE: Aucune page active - Application script d\'urgence');
            
            // Forcer l'affichage de l'accueil
            const accueil = document.getElementById('accueil');
            if (accueil) {
                accueil.style.display = 'block';
                accueil.classList.add('active');
                
                // Cacher les autres
                document.querySelectorAll('.page-content').forEach(page => {
                    if (page.id !== 'accueil') {
                        page.style.display = 'none';
                        page.classList.remove('active');
                    }
                });
            }
            
            // Navigation manuelle simple
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const href = this.getAttribute('href');
                    if (href && href.startsWith('#')) {
                        const pageId = href.substring(1);
                        
                        // Cacher tout
                        document.querySelectorAll('.page-content').forEach(page => {
                            page.style.display = 'none';
                            page.classList.remove('active');
                        });
                        
                        // Afficher la page
                        const page = document.getElementById(pageId);
                        if (page) {
                            page.style.display = 'block';
                            page.classList.add('active');
                            window.scrollTo(0, 0);
                            
                            // Lien actif
                            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                            this.classList.add('active');
                        }
                    }
                });
            });
            
            console.log('Script d\'urgence appliqué avec succès');
        }
    }, 2000);
})();