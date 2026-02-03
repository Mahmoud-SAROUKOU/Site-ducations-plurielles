const API_BASE = 'admin/api/index.php';
let currentUser = null;
let canInstall = false;

// Initialiser au chargement
window.addEventListener('load', () => {
  updateAuthUI(false);
  checkAuth();
});

// Vérifier l'authentification
async function checkAuth() {
  try {
    const res = await fetch(API_BASE + '?action=check', {
      credentials: 'include'
    });
    const data = await res.json();
    canInstall = !!data.needs_install;
    updateAuthUI(canInstall);
    if (data.authenticated) {
      currentUser = data.user;
      showPage('dashboard');
      document.getElementById('userInfo').style.display = 'flex';
      document.getElementById('userName').textContent = currentUser.name;
      document.getElementById('sidebar').style.display = 'block';
      loadStats();
    } else {
      if (canInstall) {
        showPage('install');
      } else {
        showPage('login');
      }
    }
  } catch (e) {
    console.error(e);
    updateAuthUI(false);
    showPage('login');
  }
}

function updateAuthUI(canInstallMode) {
  const signupTab = document.getElementById('loginSignupTab');
  const signupNote = document.getElementById('loginSignupNote');
  if (!signupTab || !signupNote) return;

  if (canInstallMode) {
    signupTab.style.display = 'inline-flex';
    signupNote.style.display = 'block';
    signupTab.classList.remove('disabled');
    signupTab.setAttribute('aria-disabled', 'false');
    signupTab.title = 'Créer le premier administrateur';
    signupTab.onclick = (e) => {
      e.preventDefault();
      showPage('install');
    };
  } else {
    signupTab.style.display = 'none';
    signupNote.style.display = 'none';
    signupTab.classList.add('disabled');
    signupTab.setAttribute('aria-disabled', 'true');
    signupTab.title = 'Disponible uniquement lors de la première installation';
    signupTab.onclick = (e) => e.preventDefault();
  }
}

// Afficher une page
function showPage(page) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.getElementById(page).classList.add('active');
  document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
  const link = Array.from(document.querySelectorAll('.nav-link')).find(l => 
    l.getAttribute('onclick')?.includes(`showPage('${page}')`)
  );
  if (link) link.classList.add('active');

  if (page === 'dashboard') {
    loadStats();
  } else if (page === 'articles') {
    loadArticles();
  } else if (page === 'ads') {
    loadAds();
  } else if (page === 'admins') {
    loadAdmins();
  }
}

// LOGIN
async function handleLogin(e) {
  e.preventDefault();
  const email = document.getElementById('loginEmail').value;
  const password = document.getElementById('loginPassword').value;

  try {
    const res = await fetch('admin/login.php', {
      method: 'POST',
      credentials: 'include',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
    });
    const data = await res.json();
    if (data.success) {
      checkAuth();
    } else {
      showFlash('loginFlash', data.error || 'Identifiants invalides', 'error');
    }
  } catch (e) {
    showFlash('loginFlash', 'Erreur de connexion', 'error');
  }
}

// INSTALL
async function handleInstall(e) {
  e.preventDefault();
  const name = document.getElementById('installName').value;
  const email = document.getElementById('installEmail').value;
  const password = document.getElementById('installPassword').value;

  try {
    const res = await fetch('admin/install.php', {
      method: 'POST',
      credentials: 'include',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
    });
    const data = await res.json();
    if (data.success) {
      showFlash('installFlash', 'Admin créé. Connexion en cours...', 'success');
      setTimeout(() => checkAuth(), 1500);
    } else {
      showFlash('installFlash', data.error || 'Erreur', 'error');
    }
  } catch (e) {
    showFlash('installFlash', 'Erreur', 'error');
  }
}

// LOGOUT
function logout() {
  fetch('admin/logout.php', { credentials: 'include' }).then(() => checkAuth());
}

// RESET REQUEST
async function handleResetRequest(e) {
  e.preventDefault();
  const email = document.getElementById('resetEmail').value;

  try {
    const res = await fetch('admin/reset-request.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `email=${encodeURIComponent(email)}`
    });
    const data = await res.json();
    showFlash('resetFlash', 'Si l\'email existe, un lien a été envoyé.', 'success');
    setTimeout(() => {
      document.getElementById('resetEmail').value = '';
      showPage('login');
    }, 2000);
  } catch (e) {
    showFlash('resetFlash', 'Erreur', 'error');
  }
}

// LOAD STATS
async function loadStats() {
  try {
    const articlesRes = await fetch(API_BASE + '?action=articles_count', { credentials: 'include' });
    const adsRes = await fetch(API_BASE + '?action=ads_count', { credentials: 'include' });
    const adminsRes = await fetch(API_BASE + '?action=admins_count', { credentials: 'include' });
    
    const articles = await articlesRes.json();
    const ads = await adsRes.json();
    const admins = await adminsRes.json();
    
    document.getElementById('statsArticles').textContent = articles.count || 0;
    document.getElementById('statsAds').textContent = ads.count || 0;
    document.getElementById('statsAdmins').textContent = admins.count || 0;
  } catch (e) {
    console.error(e);
  }
}

// ARTICLES
async function loadArticles() {
  try {
    const res = await fetch(API_BASE + '?action=articles_list', { credentials: 'include' });
    const data = await res.json();
    const tbody = document.getElementById('articlesTableBody');
    if (!data.articles || data.articles.length === 0) {
      tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: #999;">Aucun article</td></tr>';
    } else {
      tbody.innerHTML = data.articles.map(a => `
        <tr>
          <td>${a.title}</td>
          <td>${a.category}</td>
          <td><span class="tag ${a.status === 'published' ? 'tag-green' : 'tag-gray'}">${a.status === 'published' ? 'Publié' : 'Brouillon'}</span></td>
          <td class="actions">
            <button class="btn-edit" onclick="editArticle(${a.id})">Modifier</button>
            <button class="btn-delete" onclick="deleteArticle(${a.id})">Supprimer</button>
          </td>
        </tr>
      `).join('');
    }
  } catch (e) {
    console.error(e);
  }
}

async function handleArticleSave(e) {
  e.preventDefault();
  const id = document.getElementById('articleForm').dataset.id || null;
  
  const formData = new FormData();
  if (id) formData.append('id', id);
  formData.append('title', document.getElementById('articleTitle').value);
  formData.append('category', document.getElementById('articleCategory').value);
  formData.append('excerpt', document.getElementById('articleExcerpt').value);
  formData.append('content', document.getElementById('articleContent').value);
  formData.append('image_url', document.getElementById('articleImage').value);
  formData.append('tags', document.getElementById('articleTags').value);
  formData.append('read_time', document.getElementById('articleReadTime').value);
  formData.append('status', document.getElementById('articleStatus').value);

  try {
    const res = await fetch('admin/articles.php', {
      method: 'POST',
      credentials: 'include',
      body: formData
    });
    const result = await res.json();
    if (result.success) {
      document.getElementById('articleForm').reset();
      delete document.getElementById('articleForm').dataset.id;
      loadArticles();
    }
  } catch (e) {
    console.error(e);
  }
}

async function editArticle(id) {
  try {
    const res = await fetch(API_BASE + `?action=articles_detail&id=${id}`, { credentials: 'include' });
    const data = await res.json();
    if (data.article) {
      const a = data.article;
      document.getElementById('articleForm').dataset.id = a.id;
      document.getElementById('articleTitle').value = a.title;
      document.getElementById('articleCategory').value = a.category;
      document.getElementById('articleExcerpt').value = a.excerpt || '';
      document.getElementById('articleContent').value = a.content;
      document.getElementById('articleImage').value = a.image_url || '';
      document.getElementById('articleTags').value = a.tags || '';
      document.getElementById('articleReadTime').value = a.read_time || '';
      document.getElementById('articleStatus').value = a.status;
      showPage('articles');
      document.querySelector('.admin-content').scrollTop = 0;
    }
  } catch (e) {
    console.error(e);
  }
}

async function deleteArticle(id) {
  if (!confirm('Supprimer cet article ?')) return;
  try {
    const res = await fetch('admin/articles.php?delete=' + id, { credentials: 'include' });
    const data = await res.json();
    if (data.success) {
      loadArticles();
    }
  } catch (e) {
    console.error(e);
  }
}

// ADS
async function loadAds() {
  try {
    const res = await fetch(API_BASE + '?action=ads_list', { credentials: 'include' });
    const data = await res.json();
    const tbody = document.getElementById('adsTableBody');
    if (!data.ads || data.ads.length === 0) {
      tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: #999;">Aucune pub</td></tr>';
    } else {
      tbody.innerHTML = data.ads.map(a => `
        <tr>
          <td>${a.name}</td>
          <td>${a.position}</td>
          <td><span class="tag ${a.status === 'active' ? 'tag-green' : 'tag-gray'}">${a.status === 'active' ? 'Active' : 'Pause'}</span></td>
          <td class="actions">
            <button class="btn-edit" onclick="editAd(${a.id})">Modifier</button>
            <button class="btn-delete" onclick="deleteAd(${a.id})">Supprimer</button>
          </td>
        </tr>
      `).join('');
    }
  } catch (e) {
    console.error(e);
  }
}

async function handleAdSave(e) {
  e.preventDefault();
  const id = document.getElementById('adForm').dataset.id || null;
  
  const formData = new FormData();
  if (id) formData.append('id', id);
  formData.append('name', document.getElementById('adName').value);
  formData.append('message', document.getElementById('adMessage').value);
  formData.append('icon', document.getElementById('adIcon').value);
  formData.append('position', document.getElementById('adPosition').value);
  formData.append('image_url', document.getElementById('adImage').value);
  formData.append('target_url', document.getElementById('adTarget').value);
  formData.append('status', document.getElementById('adStatus').value);
  formData.append('display_order', document.getElementById('adOrder').value);

  try {
    const res = await fetch('admin/ads.php', {
      method: 'POST',
      credentials: 'include',
      body: formData
    });
    const result = await res.json();
    if (result.success) {
      document.getElementById('adForm').reset();
      delete document.getElementById('adForm').dataset.id;
      loadAds();
    }
  } catch (e) {
    console.error(e);
  }
}

async function editAd(id) {
  try {
    const res = await fetch(API_BASE + `?action=ads_detail&id=${id}`, { credentials: 'include' });
    const data = await res.json();
    if (data.ad) {
      const a = data.ad;
      document.getElementById('adForm').dataset.id = a.id;
      document.getElementById('adName').value = a.name;
      document.getElementById('adMessage').value = a.message || '';
      document.getElementById('adIcon').value = a.icon || '📢';
      document.getElementById('adPosition').value = a.position;
      document.getElementById('adImage').value = a.image_url;
      document.getElementById('adTarget').value = a.target_url;
      document.getElementById('adStatus').value = a.status;
      document.getElementById('adOrder').value = a.display_order || 0;
      showPage('ads');
      document.querySelector('.admin-content').scrollTop = 0;
    }
  } catch (e) {
    console.error(e);
  }
}

async function deleteAd(id) {
  if (!confirm('Supprimer cette pub ?')) return;
  try {
    const res = await fetch('admin/ads.php?delete=' + id, { credentials: 'include' });
    const data = await res.json();
    if (data.success) {
      loadAds();
    }
  } catch (e) {
    console.error(e);
  }
}

// ADMINS
async function loadAdmins() {
  try {
    const res = await fetch(API_BASE + '?action=admins_list', { credentials: 'include' });
    const data = await res.json();
    const tbody = document.getElementById('adminsTableBody');
    if (!data.admins || data.admins.length === 0) {
      tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: #999;">Aucun administrateur</td></tr>';
    } else {
      tbody.innerHTML = data.admins.map(a => `
        <tr>
          <td>${a.name}</td>
          <td>${a.email}</td>
          <td>${new Date(a.created_at).toLocaleDateString('fr-FR')}</td>
          <td class="actions">
            ${a.id !== currentUser.id ? `<button class="btn-delete" onclick="deleteAdmin(${a.id})">Supprimer</button>` : '<span style="color: #999;">Vous</span>'}
          </td>
        </tr>
      `).join('');
    }
  } catch (e) {
    console.error(e);
  }
}

async function handleAdminSave(e) {
  e.preventDefault();
  const data = {
    name: document.getElementById('adminName').value,
    email: document.getElementById('adminEmail').value,
    password: document.getElementById('adminPassword').value
  };

  try {
    const res = await fetch(API_BASE + '?action=admin_create', {
      method: 'POST',
      credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    const result = await res.json();
    if (result.success) {
      document.getElementById('adminForm').reset();
      loadAdmins();
      showFlash('adminFlash', 'Admin créé avec succès', 'success');
    } else {
      showFlash('adminFlash', result.error || 'Erreur', 'error');
    }
  } catch (e) {
    console.error(e);
    showFlash('adminFlash', 'Erreur', 'error');
  }
}

async function deleteAdmin(id) {
  if (!confirm('Supprimer cet administrateur ?')) return;
  try {
    const res = await fetch(API_BASE + `?action=admin_delete&id=` + id, { credentials: 'include' });
    const data = await res.json();
    if (data.success) {
      loadAdmins();
    }
  } catch (e) {
    console.error(e);
  }
}

// UTILS
function showFlash(elementId, message, type) {
  const el = document.getElementById(elementId);
  el.innerHTML = `<div class="flash flash-${type}">${message}</div>`;
}

// RÉINITIALISATION BASE DE DONNÉES
function showResetDialog() {
  document.getElementById('resetModal').classList.add('active');
}

function closeResetDialog() {
  document.getElementById('resetModal').classList.remove('active');
}

async function confirmResetDatabase() {
  try {
    const res = await fetch('admin/reset-db-action.php', {
      method: 'POST',
      credentials: 'include'
    });
    const data = await res.json();
    
    if (data.success) {
      closeResetDialog();
      alert('✅ Base réinitialisée avec succès!\n\nLa page va se recharger...');
      setTimeout(() => {
        location.reload();
      }, 1000);
    } else {
      alert('❌ Erreur: ' + (data.error || 'Impossible de réinitialiser'));
    }
  } catch (e) {
    console.error(e);
    alert('❌ Erreur de connexion');
  }
}

// Fermer la modale en cliquant en dehors
document.addEventListener('click', (e) => {
  const modal = document.getElementById('resetModal');
  if (e.target === modal) {
    closeResetDialog();
  }
});
