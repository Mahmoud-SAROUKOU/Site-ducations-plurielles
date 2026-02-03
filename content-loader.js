// ============================================
// GESTIONNAIRE DE CONTENU DYNAMIQUE
// ============================================

class ContentManager {
    constructor() {
        this.contentData = null;
        this.dataUrl = 'admin/api/index.php?action=articles';
    }

    async loadContent() {
        try {
            const response = await fetch(this.dataUrl);
            if (!response.ok) {
                throw new Error('Erreur de chargement des données');
            }
            this.contentData = await response.json();
            console.log('✅ Contenu chargé:', this.contentData);
            window.dispatchEvent(new CustomEvent('contentLoaded', { detail: this.contentData }));
            return this.contentData;
        } catch (error) {
            console.error('❌ Erreur de chargement:', error);
            // Fallback sur site-content.json puis localStorage
            const fallback = await this.loadFromJson();
            if (fallback) {
                return fallback;
            }
            return this.loadFromLocalStorage();
        }
    }

    async loadFromJson() {
        try {
            const response = await fetch('site-content.json');
            if (!response.ok) {
                return null;
            }
            this.contentData = await response.json();
            window.dispatchEvent(new CustomEvent('contentLoaded', { detail: this.contentData }));
            return this.contentData;
        } catch (e) {
            return null;
        }
    }

    loadFromLocalStorage() {
        // Fallback sur localStorage (données de l'admin)
        const articles = JSON.parse(localStorage.getItem('ep_articles') || '[]');
        const ads = JSON.parse(localStorage.getItem('ep_ads') || '[]');
        
        return {
            articles: articles,
            ads: ads,
            lastUpdate: new Date().toISOString()
        };
    }

    getArticles() {
        return this.contentData?.articles || [];
    }

    getPublishedArticles() {
        return this.getArticles().filter(article => article.published);
    }

    getArticlesByCategory(category) {
        return this.getPublishedArticles().filter(article => article.category === category);
    }

    getArticleById(id) {
        return this.getArticles().find(article => article.id === parseInt(id));
    }

    getAds() {
        return this.contentData?.ads || [];
    }

    getActiveAds() {
        return this.getAds()
            .filter(ad => ad.active)
            .sort((a, b) => a.order - b.order);
    }

    renderNewsTicker() {
        const ticker = document.querySelector('.news-ticker');
        if (!ticker) return;

        const activeAds = this.getActiveAds();
        if (activeAds.length === 0) return;

        ticker.innerHTML = activeAds.map(ad => {
            const text = `${ad.icon || '📢'} ${ad.message || ad.name}`;
            if (ad.target) {
                return `<span class="mx-8"><a href="${ad.target}" target="_blank" rel="noopener noreferrer" class="text-white">${text}</a></span>`;
            }
            return `<span class="mx-8">${text}</span>`;
        }).join('');
    }

    renderArticles(containerId = 'articlesContainer') {
        const container = document.getElementById(containerId);
        if (!container) return;

        const articles = this.getPublishedArticles();
        
        if (articles.length === 0) {
            container.innerHTML = '<p class="text-center text-gray-500">Aucun article disponible pour le moment.</p>';
            return;
        }

        container.innerHTML = articles.map(article => this.createArticleCard(article)).join('');
    }

    createArticleCard(article) {
        const categoryLabels = {
            'parentalite': 'Parentalité',
            'education': 'Éducation',
            'droits': 'Droits de l\'enfant',
            'temoignages': 'Témoignages'
        };

        const categoryColors = {
            'parentalite': 'bg-yellow-100 text-yellow-800',
            'education': 'bg-blue-100 text-blue-800',
            'droits': 'bg-pink-100 text-pink-800',
            'temoignages': 'bg-purple-100 text-purple-800'
        };

        return `
            <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                ${article.image ? `
                    <img src="${article.image}" alt="${article.title}" class="w-full h-48 object-cover" onerror="this.src='images/placeholder.jpg'">
                ` : ''}
                <div class="p-6">
                    <div class="flex items-center mb-3">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold ${categoryColors[article.category] || 'bg-gray-100 text-gray-800'}">
                            ${categoryLabels[article.category] || article.category}
                        </span>
                        <span class="ml-auto text-xs text-gray-500">
                            ${this.formatDate(article.createdAt)}
                        </span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3 hover:text-blue-600 transition">
                        ${article.title}
                    </h3>
                    <p class="text-gray-600 mb-4 line-clamp-3">
                        ${article.excerpt}
                    </p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-user mr-1"></i>${article.author}
                        </span>
                        <button onclick="viewArticle(${article.id})" class="text-blue-600 font-semibold hover:text-blue-800 transition">
                            Lire la suite <i class="fas fa-arrow-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </article>
        `;
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffDays === 0) return "Aujourd'hui";
        if (diffDays === 1) return "Hier";
        if (diffDays < 7) return `Il y a ${diffDays} jours`;

        return date.toLocaleDateString('fr-FR', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    }
}

// Instance globale
const contentManager = new ContentManager();

// Fonction pour voir un article (à implémenter selon vos besoins)
function viewArticle(articleId) {
    const article = contentManager.getArticleById(articleId);
    if (!article) {
        console.error('Article non trouvé:', articleId);
        return;
    }

    // Créer une modal ou naviguer vers une page détail
    showArticleModal(article);
}

function showArticleModal(article) {
    // Créer une modal pour afficher l'article complet
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            ${article.image ? `
                <img src="${article.image}" alt="${article.title}" class="w-full h-64 object-cover">
            ` : ''}
            <div class="p-8">
                <div class="flex justify-between items-start mb-4">
                    <h2 class="text-3xl font-bold text-gray-800">${article.title}</h2>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-500 hover:text-gray-700 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="flex items-center mb-6 text-sm text-gray-600">
                    <span><i class="fas fa-user mr-2"></i>${article.author}</span>
                    <span class="mx-3">•</span>
                    <span><i class="fas fa-calendar mr-2"></i>${contentManager.formatDate(article.createdAt)}</span>
                </div>
                <div class="prose max-w-none text-gray-700 leading-relaxed">
                    ${article.content}
                </div>
                ${article.tags ? `
                    <div class="mt-6 pt-6 border-t">
                        <div class="flex flex-wrap gap-2">
                            ${article.tags.map(tag => `
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                                    #${tag}
                                </span>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    // Fermer la modal en cliquant en dehors
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Initialiser le contenu au chargement de la page
document.addEventListener('DOMContentLoaded', async function() {
    console.log('🚀 Initialisation du gestionnaire de contenu...');
    
    await contentManager.loadContent();
    
    // Rendre le ticker de publicités
    contentManager.renderNewsTicker();
    
    // Si on est sur la page articles, les afficher
    const articlesContainer = document.getElementById('articlesContainer');
    if (articlesContainer) {
        contentManager.renderArticles();
    }
    
    console.log('✅ Contenu initialisé');
});
