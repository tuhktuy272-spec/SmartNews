const BASE_URL = (window.SMARTNEWS_BASE_URL || '').replace(/\/$/, '');
const API_BASE = `${BASE_URL}/api.php?action=`;

const state = {
  categories: [],
  featuredArticles: [],
  newArticles: [],
  recommendations: [],
  selectedCategoryId: null
};

async function fetchJSON(endpoint, options = {}) {
  try {
    const response = await fetch(`${API_BASE}${endpoint}`, {
      headers: { 'Content-Type': 'application/json' },
      ...options
    });

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }

    return await response.json();
  } catch (error) {
    console.warn(`API request failed: ${endpoint}`, error);
    return null;
  }
}

function normalizeArrayResponse(response, key) {
  if (Array.isArray(response)) return response;

  if (response && typeof response === 'object') {
    if (Array.isArray(response[key])) return response[key];
    if (Array.isArray(response.data)) return response.data;
    if (Array.isArray(response.items)) return response.items;
  }

  return [];
}

function getCategoryIdFromURL() {
  const params = new URLSearchParams(window.location.search);
  const categoryId = params.get('category');

  return categoryId && /^\d+$/.test(categoryId) ? categoryId : null;
}

function getSelectedCategory() {
  if (!state.selectedCategoryId) return null;

  return state.categories.find(
    category => String(category.id) === String(state.selectedCategoryId)
  ) || null;
}

async function getCategories() {
  const response = await fetchJSON('categories');

  return normalizeArrayResponse(response, 'categories');
}

async function getArticles(section) {
  let action = `articles&section=${encodeURIComponent(section)}`;

  if (state.selectedCategoryId) {
    action += `&category_id=${encodeURIComponent(state.selectedCategoryId)}`;
  }

  const response = await fetchJSON(action);

  return normalizeArrayResponse(response, 'articles');
}

async function getRecommendations() {
  const response = await fetchJSON('recommendations');

  return normalizeArrayResponse(response, 'recommendations');
}

function showSearchResults(query, items) {
  const resultsSection = document.getElementById('search-results-section');
  const queryText = document.getElementById('search-query-text');
  const resultsList = document.getElementById('search-results-list');

  if (!resultsSection || !resultsList || !queryText) return;

  queryText.textContent = query;
  resultsSection.hidden = false;
  resultsList.innerHTML = '';

  if (!Array.isArray(items) || items.length === 0) {
    resultsList.innerHTML = '<p class="empty-state">Không tìm thấy bài viết phù hợp.</p>';
    resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    return;
  }

  items.forEach((item, index) => {
    resultsList.appendChild(createArticleCard(item, index, 'Xem bài viết'));
  });

  initReveal();
  resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function hideSearchResults() {
  const resultsSection = document.getElementById('search-results-section');

  if (resultsSection) {
    resultsSection.hidden = true;
  }
}

function createArticleCard(item, index, buttonText = 'Đọc tiếp') {
  const card = document.createElement('article');

  card.className = 'article-card reveal';
  card.style.transitionDelay = `${index * 60}ms`;

  const link = item.link || '';
  const category = item.category_name ? `<span class="article-card-category">${escapeHTML(item.category_name)}</span>` : '';
  const author = item.author ? `<span>${escapeHTML(item.author)}</span>` : '';

  card.innerHTML = `
    ${category}
    <h3>${escapeHTML(item.title || 'Không có tiêu đề')}</h3>
    <p>${escapeHTML(item.description || item.summary || '')}</p>
    <div class="article-card-meta">${author}</div>
    ${link ? `<a class="text-link" href="${escapeHTML(link)}">${buttonText} <span>→</span></a>` : ''}
  `;

  if (link) {
    card.style.cursor = 'pointer';

    card.addEventListener('click', (event) => {
      if (event.target.closest('a')) return;
      window.location.href = link;
    });
  }

  return card;
}

function createCategoryCard(item, index) {
  const card = document.createElement('a');

  card.className = 'category-card reveal';
  card.style.transitionDelay = `${index * 60}ms`;
  card.href = item.link || '#new-articles';

  const countText = Number(item.published_count || 0) > 0
    ? `${item.published_count} bài viết`
    : 'Xem bài viết';

  card.innerHTML = `
    <h3>${escapeHTML(item.title || 'Chuyên mục')}</h3>
    <p>${escapeHTML(item.description || '')}</p>
    <span class="text-link">Xem chuyên mục · ${countText} <span>→</span></span>
  `;

  return card;
}

function renderCards(containerId, items) {
  const container = document.getElementById(containerId);

  if (!container) return;

  container.innerHTML = '';

  if (!Array.isArray(items) || items.length === 0) {
    container.innerHTML = '<p class="empty-state">Chưa có nội dung hiển thị ở đây.</p>';
    return;
  }

  items.forEach((item, index) => {
    if (containerId === 'categories-list') {
      container.appendChild(createCategoryCard(item, index));
      return;
    }

    container.appendChild(createArticleCard(item, index, 'Đọc tiếp'));
  });

  initReveal();
}

function updateCategoryHeading() {
  const selectedCategory = getSelectedCategory();

  if (!selectedCategory) return;

  const featuredTitle = document.querySelector('#featured h2');
  const newTitle = document.querySelector('#new-articles h2');
  const recommendationTitle = document.querySelector('.recommendations h2');
  const categorySection = document.getElementById('categories');

  if (featuredTitle) {
    featuredTitle.textContent = `Bài nổi bật trong chuyên mục ${selectedCategory.title}`;
  }

  if (newTitle) {
    newTitle.textContent = `Bài viết trong chuyên mục ${selectedCategory.title}`;
  }

  if (recommendationTitle) {
    recommendationTitle.textContent = 'Những bài viết mới nhất khác';
  }

  if (categorySection) {
    const info = document.createElement('div');
    info.className = 'container reveal';
    info.innerHTML = `
      <div class="category-selected-notice">
        Đang xem chuyên mục <strong>${escapeHTML(selectedCategory.title)}</strong>.
        <a class="text-link" href="${BASE_URL}/index.php#categories">Xem tất cả chuyên mục</a>
      </div>
    `;
    categorySection.insertAdjacentElement('beforebegin', info);
  }
}

async function loadHomePage() {
  state.selectedCategoryId = getCategoryIdFromURL();

  const categories = await getCategories();
  state.categories = categories;

  const [featuredArticles, newArticles, recommendations] = await Promise.all([
    getArticles('featured'),
    getArticles('new'),
    getRecommendations()
  ]);

  state.featuredArticles = featuredArticles;
  state.newArticles = newArticles;
  state.recommendations = recommendations;

  updateCategoryHeading();

  renderCards('categories-list', state.categories);
  renderCards('featured-list', state.featuredArticles);
  renderCards('new-list', state.newArticles);
  renderCards('recommendation-list', state.recommendations);

  hideSearchResults();
}

async function searchArticles(query) {
  const response = await fetchJSON(`search&q=${encodeURIComponent(query)}`);

  return normalizeArrayResponse(response, 'articles');
}

function filterArticles(query) {
  const cleanQuery = String(query || '').trim();

  if (!cleanQuery) {
    hideSearchResults();
    return;
  }

  const resultsList = document.getElementById('search-results-list');
  const resultsSection = document.getElementById('search-results-section');
  const queryText = document.getElementById('search-query-text');

  if (resultsSection && queryText && resultsList) {
    queryText.textContent = cleanQuery;
    resultsSection.hidden = false;
    resultsList.innerHTML = '<p class="empty-state">Đang tìm kiếm...</p>';
  }

  searchArticles(cleanQuery).then(results => {
    showSearchResults(cleanQuery, results);
  });
}

function initTheme() {
  const storedTheme = localStorage.getItem('tintuc-theme');
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const theme = storedTheme || (prefersDark ? 'dark' : 'light');

  document.body.setAttribute('data-theme', theme);

  const icon = document.querySelector('.theme-toggle-icon');

  if (icon) {
    icon.textContent = theme === 'dark' ? '🌙' : '☀️';
  }
}

function initReveal() {
  const elements = document.querySelectorAll('.reveal:not(.is-visible)');

  if (!('IntersectionObserver' in window)) {
    elements.forEach(element => element.classList.add('is-visible'));
    return;
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  elements.forEach(element => observer.observe(element));
}

function initScrollEffects() {
  const ambient = document.querySelector('.hero-ambient');
  const backToTop = document.querySelector('.back-to-top');

  window.addEventListener('scroll', () => {
    const scrollY = window.scrollY;

    if (ambient) {
      ambient.style.transform = `translate3d(0, ${scrollY * 0.08}px, 0)`;
    }

    if (backToTop) {
      backToTop.classList.toggle('is-visible', scrollY > 500);
    }
  });

  backToTop?.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

function initNavigation() {
  const navToggle = document.querySelector('.nav-toggle');
  const navLinks = document.querySelector('.nav-links');

  navToggle?.addEventListener('click', () => {
    const expanded = navToggle.getAttribute('aria-expanded') === 'true';

    navToggle.setAttribute('aria-expanded', String(!expanded));
    navLinks?.classList.toggle('is-open');
  });
}

function initThemeToggle() {
  const toggle = document.querySelector('.theme-toggle');

  toggle?.addEventListener('click', () => {
    const current = document.body.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';

    document.body.setAttribute('data-theme', next);
    localStorage.setItem('tintuc-theme', next);

    const icon = document.querySelector('.theme-toggle-icon');

    if (icon) {
      icon.textContent = next === 'dark' ? '🌙' : '☀️';
    }
  });
}

function initLoadingState() {
  const loader = document.getElementById('page-loading');

  if (!loader) return;

  window.addEventListener('load', () => {
    loader.classList.add('is-hidden');
    window.setTimeout(() => loader.remove(), 350);
  });
}

function initSearchForm() {
  const searchForm = document.getElementById('search-form');
  const searchInput = document.getElementById('search-input');

  searchForm?.addEventListener('submit', (event) => {
    event.preventDefault();
    filterArticles(searchInput?.value || '');
  });

  searchInput?.addEventListener('input', (event) => {
    const value = event.target.value || '';

    if (!value.trim()) {
      hideSearchResults();
    }
  });
}

async function initPage() {
  const categoriesList = document.getElementById('categories-list');

  if (categoriesList) {
    await loadHomePage();
    initSearchForm();
  }
}

function escapeHTML(value) {
  return String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

window.addEventListener('DOMContentLoaded', async () => {
  initTheme();
  initThemeToggle();
  initNavigation();
  initScrollEffects();
  initLoadingState();

  await initPage();

  initReveal();
});
