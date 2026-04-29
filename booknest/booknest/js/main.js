/**
 * BookNest - Main JavaScript
 * Gestion DOM, panier, filtres, événements
 */

console.log('BookNest main.js loaded');

/* ============================================================
   CART (localStorage)
   ============================================================ */
const Cart = {
  get() {
    try {
      return JSON.parse(localStorage.getItem('bn_cart') || '[]');
    } catch (e) {
      console.error('Error reading cart from localStorage:', e);
      return [];
    }
  },
  save(items) {
    try {
      localStorage.setItem('bn_cart', JSON.stringify(items));
      Cart.updateBadge();
    } catch (e) {
      console.error('Error saving cart to localStorage:', e);
    }
  },
  add(product) {
    console.log('Cart.add called with:', product);
    const items = Cart.get();
    console.log('Current cart items:', items);
    const existing = items.find(i => i.id === product.id);
    if (existing) {
      existing.qty += 1;
      console.log('Incremented existing item qty to:', existing.qty);
    } else {
      items.push({ ...product, qty: 1 });
      console.log('Added new item to cart');
    }
    Cart.save(items);
    Cart.showNotification(`"${product.title}" ajouté au panier`);
  },
  addById(id, title, author, price) {
    console.log('Adding to cart:', id, title, author, price);
    Cart.add({ id: id, title: title, author: author, price: price });
  },
  remove(id) {
    const items = Cart.get().filter(i => i.id !== id);
    Cart.save(items);
  },
  updateQty(id, qty) {
    const items = Cart.get();
    const item = items.find(i => i.id === id);
    if (item) {
      item.qty = Math.max(1, qty);
      Cart.save(items);
    }
  },
  count() {
    return Cart.get().reduce((sum, i) => sum + i.qty, 0);
  },
  total() {
    return Cart.get().reduce((sum, i) => sum + i.price * i.qty, 0);
  },
  updateBadge() {
    const badge = document.getElementById('cart-count');
    if (badge) {
      const count = Cart.count();
      badge.textContent = count;
      badge.style.display = count > 0 ? 'inline-flex' : 'none';
    }
  },
  showNotification(msg) {
    let notif = document.getElementById('cart-notification');
    if (!notif) {
      notif = document.createElement('div');
      notif.id = 'cart-notification';
      document.body.appendChild(notif);
    }
    notif.textContent = '🛒 ' + msg;
    notif.classList.add('show');
    clearTimeout(notif._timeout);
    notif._timeout = setTimeout(() => notif.classList.remove('show'), 2800);
  }
};

// Make Cart globally available
window.Cart = Cart;

/* ============================================================
   CART PAGE RENDERING
   ============================================================ */
function renderCartPage() {
  const cartContainer = document.getElementById('cart-items');
  const summaryContainer = document.getElementById('cart-summary');
  if (!cartContainer) return;

  const items = Cart.get();

  if (items.length === 0) {
    cartContainer.innerHTML = `
      <div class="cart-empty">
        <div class="icon">📚</div>
        <h3>Votre panier est vide</h3>
        <p class="text-muted mt-1">Découvrez notre catalogue et ajoutez vos livres préférés.</p>
        <a href="index.php" class="btn btn-primary mt-3">Parcourir le catalogue</a>
      </div>`;
    if (summaryContainer) summaryContainer.style.display = 'none';
    return;
  }

  if (summaryContainer) summaryContainer.style.display = '';

  cartContainer.innerHTML = items.map(item => `
    <div class="cart-item" data-id="${item.id}">
      <div class="cart-item-cover">
        <span>📖</span>
      </div>
      <div class="cart-item-info">
        <h4>${escHtml(item.title)}</h4>
        <p>${escHtml(item.author || '')}</p>
        <div class="qty-controls">
          <button class="qty-btn" onclick="changeQty(${item.id}, -1)">−</button>
          <span class="qty-display" id="qty-${item.id}">${item.qty}</span>
          <button class="qty-btn" onclick="changeQty(${item.id}, 1)">+</button>
        </div>
      </div>
      <div style="text-align:right">
        <div class="price">${(item.price * item.qty).toFixed(2)} DT</div>
        <div class="price-old">${item.price.toFixed(2)} DT / unité</div>
        <button class="remove-btn mt-2" onclick="removeFromCart(${item.id})" title="Supprimer">🗑</button>
      </div>
    </div>
  `).join('');

  updateSummary();
}

function changeQty(id, delta) {
  const items = Cart.get();
  const item = items.find(i => i.id === id);
  if (!item) return;
  item.qty = Math.max(1, item.qty + delta);
  Cart.save(items);
  const qtyEl = document.getElementById(`qty-${id}`);
  if (qtyEl) qtyEl.textContent = item.qty;
  updateSummary();
}

function removeFromCart(id) {
  Cart.remove(id);
  const el = document.querySelector(`.cart-item[data-id="${id}"]`);
  if (el) {
    el.style.opacity = '0';
    el.style.transform = 'translateX(30px)';
    el.style.transition = 'all 0.3s ease';
    setTimeout(() => renderCartPage(), 300);
  }
}

function updateSummary() {
  const items = Cart.get();
  const subtotal = Cart.total();
  const shipping = subtotal > 50 ? 0 : 7;
  const total = subtotal + shipping;

  const el = id => document.getElementById(id);
  if (el('summary-subtotal')) el('summary-subtotal').textContent = subtotal.toFixed(2) + ' DT';
  if (el('summary-shipping')) el('summary-shipping').textContent = shipping === 0 ? 'Gratuit' : shipping.toFixed(2) + ' DT';
  if (el('summary-total'))    el('summary-total').textContent    = total.toFixed(2) + ' DT';
  if (el('summary-count'))    el('summary-count').textContent    = items.reduce((s, i) => s + i.qty, 0);
}

/* ============================================================
   PRODUCT FILTERS (catalogue)
   ============================================================ */
function initFilters() {
  const filterBtns = document.querySelectorAll('.filter-btn');
  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const cat = btn.dataset.category;
      filterProducts(cat);
    });
  });
}

function filterProducts(category) {
  const cards = document.querySelectorAll('.product-card');
  cards.forEach(card => {
    const cardCat = card.dataset.category || '';
    const show = category === 'all' || cardCat === category;
    card.style.transition = 'opacity 0.25s, transform 0.25s';
    if (show) {
      card.style.display = '';
      setTimeout(() => { card.style.opacity = '1'; card.style.transform = ''; }, 10);
    } else {
      card.style.opacity = '0';
      card.style.transform = 'scale(0.95)';
      setTimeout(() => { card.style.display = 'none'; }, 260);
    }
  });
}

/* ============================================================
   AJAX SEARCH
   ============================================================ */
function initSearch() {
  const searchInput = document.getElementById('search-input');
  const searchBtn   = document.getElementById('search-btn');
  const resultsDiv  = document.getElementById('search-results');
  if (!searchInput || !resultsDiv) return;

  let debounceTimer;

  const doSearch = () => {
    const q = searchInput.value.trim();
    if (q.length < 2) {
      resultsDiv.innerHTML = '';
      return;
    }
    resultsDiv.innerHTML = `<div class="loading-placeholder"><span class="spinner"></span> Recherche en cours…</div>`;

    fetch(`back/search.php?q=${encodeURIComponent(q)}`)
      .then(r => r.json())
      .then(data => {
        if (!data.results || data.results.length === 0) {
          resultsDiv.innerHTML = `<p class="text-muted mt-2">Aucun résultat pour "<strong>${escHtml(q)}</strong>".</p>`;
          return;
        }
        resultsDiv.innerHTML = `
          <p class="text-muted mb-2" style="font-size:.85rem">${data.results.length} résultat(s) pour "<strong>${escHtml(q)}</strong>"</p>
          <div class="products-grid">
            ${data.results.map(book => renderProductCard(book)).join('')}
          </div>`;
      })
      .catch(() => {
        resultsDiv.innerHTML = `<p class="text-muted mt-2">Erreur lors de la recherche. Veuillez réessayer.</p>`;
      });
  };

  searchInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(doSearch, 350);
  });

  if (searchBtn) searchBtn.addEventListener('click', doSearch);

  searchInput.addEventListener('keydown', e => {
    if (e.key === 'Enter') doSearch();
  });
}

function renderProductCard(book) {
  const colors = ['#8B5E3C','#1A1208','#C8472B','#5C4033','#4A3728'];
  const color  = colors[book.id % colors.length];
  const inStock = book.stock > 0;
  return `
    <div class="product-card" data-category="${escHtml(book.categorie || '')}">
      <div class="book-cover" style="background: linear-gradient(135deg, ${color} 0%, #1A1208 100%)">
        <div class="book-stripe"></div>
        <div class="book-cover-inner">
          <div class="book-title-cover">${escHtml(book.titre)}</div>
          <div class="book-author-cover">${escHtml(book.auteur || '')}</div>
        </div>
      </div>
      <div class="card-body">
        <div class="card-category">${escHtml(book.categorie || 'Littérature')}</div>
        <div class="card-title">${escHtml(book.titre)}</div>
        <div class="card-author">${escHtml(book.auteur || '')}</div>
        <div class="card-footer">
          <div>
            <div class="price">${parseFloat(book.prix).toFixed(2)} DT</div>
            <div class="stock-badge ${inStock ? '' : 'out'}">${inStock ? '✓ En stock' : '✗ Épuisé'}</div>
          </div>
          ${inStock ? `<button class="btn btn-primary btn-sm add-to-cart"
            data-id="${book.id}"
            data-title="${escHtml(book.titre)}"
            data-author="${escHtml(book.auteur || '')}"
            data-price="${book.prix}">
            Ajouter
          </button>` : `<button class="btn btn-outline btn-sm" disabled>Épuisé</button>`}
        </div>
      </div>
    </div>`;
}

/* ============================================================
   FORM VALIDATION
   ============================================================ */
const Validator = {
  rules: {
    required: (val) => val.trim() !== '' || 'Ce champ est obligatoire.',
    email:    (val) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val) || 'Adresse email invalide.',
    minLen:   (n)  => (val) => val.length >= n || `Minimum ${n} caractères requis.`,
    password: (val) => /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/.test(val) ||
                       'Le mot de passe doit contenir 8 caractères, une majuscule et un chiffre.',
    match:    (otherId) => (val) => val === document.getElementById(otherId)?.value || 'Les mots de passe ne correspondent pas.',
    phone:    (val) => val === '' || /^[0-9+\s\-()]{8,15}$/.test(val) || 'Numéro de téléphone invalide.',
    price:    (val) => /^\d+(\.\d{1,2})?$/.test(val) || 'Prix invalide (ex: 12.99).',
    posInt:   (val) => /^\d+$/.test(val) && parseInt(val) >= 0 || 'Valeur entière positive requise.',
  },

  validate(fieldId, ...ruleNames) {
    const field = document.getElementById(fieldId);
    const errEl = document.getElementById(`${fieldId}-error`);
    if (!field) return true;

    const val = field.value;
    for (const ruleName of ruleNames) {
      let rule, ruleArg;
      if (typeof ruleName === 'string' && ruleName.includes(':')) {
        const [name, arg] = ruleName.split(':');
        rule = Validator.rules[name]?.(isNaN(arg) ? arg : Number(arg));
      } else {
        rule = Validator.rules[ruleName];
      }
      if (!rule) continue;
      const result = rule(val);
      if (result !== true) {
        field.classList.add('error');
        if (errEl) { errEl.textContent = result; errEl.classList.add('visible'); }
        return false;
      }
    }
    field.classList.remove('error');
    if (errEl) errEl.classList.remove('visible');
    return true;
  },

  clearAll(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
    form.querySelectorAll('.field-error').forEach(el => el.classList.remove('visible'));
  }
};

/* ============================================================
   AUTH TABS
   ============================================================ */
function initAuthTabs() {
  const loginTab    = document.getElementById('tab-login');
  const registerTab = document.getElementById('tab-register');
  const loginForm   = document.getElementById('form-login');
  const regForm     = document.getElementById('form-register');
  if (!loginTab) return;

  loginTab.addEventListener('click', () => {
    loginTab.classList.add('active');
    registerTab.classList.remove('active');
    loginForm.style.display = '';
    regForm.style.display = 'none';
  });

  registerTab.addEventListener('click', () => {
    registerTab.classList.add('active');
    loginTab.classList.remove('active');
    regForm.style.display = '';
    loginForm.style.display = 'none';
  });

  // Show register tab if URL has ?tab=register
  if (new URLSearchParams(location.search).get('tab') === 'register') {
    registerTab.click();
  }
}

/* ============================================================
   AJAX EMAIL CHECK (inscription)
   ============================================================ */
function initEmailCheck() {
  const emailInput = document.getElementById('reg-email');
  const statusEl   = document.getElementById('email-status');
  if (!emailInput || !statusEl) return;

  let timer;
  emailInput.addEventListener('input', () => {
    clearTimeout(timer);
    const val = emailInput.value.trim();
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) { statusEl.innerHTML = ''; return; }

    statusEl.className = 'email-status checking';
    statusEl.innerHTML = '<span class="spinner"></span> Vérification…';

    timer = setTimeout(() => {
      fetch(`back/check_email.php?email=${encodeURIComponent(val)}`)
        .then(r => r.json())
        .then(data => {
          if (data.taken) {
            statusEl.className = 'email-status taken';
            statusEl.innerHTML = '✗ Cette adresse est déjà utilisée.';
          } else {
            statusEl.className = 'email-status available';
            statusEl.innerHTML = '✓ Adresse disponible.';
          }
        })
        .catch(() => { statusEl.innerHTML = ''; });
    }, 600);
  });
}

/* ============================================================
   LOGIN FORM VALIDATION
   ============================================================ */
function initLoginForm() {
  const form = document.getElementById('login-form');
  if (!form) return;
  form.addEventListener('submit', e => {
    Validator.clearAll('login-form');
    const ok =
      Validator.validate('login-email', 'required', 'email') &
      Validator.validate('login-password', 'required');
    if (!ok) e.preventDefault();
  });
}

/* ============================================================
   REGISTER FORM VALIDATION
   ============================================================ */
function initRegisterForm() {
  const form = document.getElementById('register-form');
  if (!form) return;
  form.addEventListener('submit', e => {
    Validator.clearAll('register-form');
    const ok =
      Validator.validate('reg-prenom',    'required') &
      Validator.validate('reg-nom',       'required') &
      Validator.validate('reg-email',     'required', 'email') &
      Validator.validate('reg-password',  'required', 'password') &
      Validator.validate('reg-password2', 'required', 'match:reg-password') &
      Validator.validate('reg-phone',     'phone');
    if (!ok) e.preventDefault();
  });
}

/* ============================================================
   ADMIN FORMS VALIDATION
   ============================================================ */
function initAdminForms() {
  const bookForm = document.getElementById('book-form');
  if (!bookForm) return;
  bookForm.addEventListener('submit', e => {
    Validator.clearAll('book-form');
    const ok =
      Validator.validate('book-titre',    'required') &
      Validator.validate('book-auteur',   'required') &
      Validator.validate('book-prix',     'required', 'price') &
      Validator.validate('book-stock',    'required', 'posInt') &
      Validator.validate('book-categorie','required');
    if (!ok) e.preventDefault();
  });
}

/* ============================================================
   DYNAMIC DOM — ACCORDION / TOGGLE
   ============================================================ */
function initAccordions() {
  document.querySelectorAll('[data-accordion]').forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.dataset.accordion;
      const target = document.getElementById(targetId);
      if (!target) return;
      const isOpen = target.style.display !== 'none';
      target.style.display = isOpen ? 'none' : '';
      btn.textContent = isOpen ? btn.dataset.labelOpen || '▶ Voir' : btn.dataset.labelClose || '▼ Masquer';
    });
  });
}

/* ============================================================
   MODAL
   ============================================================ */
function openModal(id) {
  const overlay = document.getElementById(id);
  if (overlay) overlay.classList.add('open');
}
function closeModal(id) {
  const overlay = document.getElementById(id);
  if (overlay) overlay.classList.remove('open');
}
document.addEventListener('click', e => {
  if (e.target.classList.contains('modal-overlay')) closeModal(e.target.id);
  if (e.target.classList.contains('modal-close'))   closeModal(e.target.closest('.modal-overlay')?.id);
});

/* ============================================================
   UTILS
   ============================================================ */
function escHtml(str) {
  const d = document.createElement('div');
  d.appendChild(document.createTextNode(String(str)));
  return d.innerHTML;
}

function showAlert(id, msg, type = 'error') {
  const el = document.getElementById(id);
  if (!el) return;
  el.className = `alert alert-${type} visible`;
  el.textContent = msg;
  el.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function hideAlert(id) {
  const el = document.getElementById(id);
  if (el) el.classList.remove('visible');
}

/* ============================================================
   INIT
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
  console.log('DOMContentLoaded fired');
  Cart.updateBadge();
  renderCartPage();
  initFilters();
  initSearch();
  initAuthTabs();
  initEmailCheck();
  initLoginForm();
  initRegisterForm();
  initAdminForms();
  initAccordions();

  // Event delegation for add to cart buttons
  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('add-to-cart')) {
      e.preventDefault();
      console.log('Add to cart button clicked');
      const btn = e.target;
      const id = parseInt(btn.dataset.id);
      const title = btn.dataset.title;
      const author = btn.dataset.author;
      const price = parseFloat(btn.dataset.price);
      console.log('Extracted data:', {id, title, author, price});
      Cart.addById(id, title, author, price);
    }
  });

  // Highlight active nav link
  const path = location.pathname.split('/').pop();
  document.querySelectorAll('nav a').forEach(a => {
    const href = a.getAttribute('href')?.split('/').pop();
    if (href === path) a.classList.add('active');
  });
});
