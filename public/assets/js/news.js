const API_BASE = 'api.php?action=';

const state = {
  categories: [],
  featuredArticles: [],
  newArticles: [],
  recommendations: [],
  userState: {
    isLoggedIn: false,
    userId: null,
    bookmarkIds: [],
    likedArticleIds: []
  }
};

const fallbackData = {
  categories: [
    { title: 'Công nghệ', description: 'Tin tức công nghệ, sản phẩm mới, xu hướng.' },
    { title: 'Giải trí', description: 'Phim ảnh, âm nhạc, sao và sự kiện.' },
    { title: 'Kinh doanh', description: 'Thị trường, doanh nghiệp và đầu tư.' },
    { title: 'Thể thao', description: 'Trực tiếp trận đấu, bảng xếp hạng, bình luận.' },
    { title: 'Sức khỏe', description: 'Lối sống, dinh dưỡng và chăm sóc sức khỏe.' },
    { title: 'Giáo dục', description: 'Học tập, tuyển sinh, hướng nghiệp.' }
  ],
  featuredArticles: [
    { title: 'AI và tương lai báo chí số', summary: 'Cách AI thay đổi sản xuất nội dung và trải nghiệm đọc tin.', link: 'article.php?id=ai-va-tuong-lai-bao-chi-so' },
    { title: 'Thiết kế giao diện tin tức chuyên nghiệp', summary: 'Mẹo bố cục, màu sắc và trải nghiệm người đọc cho trang tin.', link: 'article.php?id=thiet-ke-giao-dien-tin-tuc-chuyen-nghiep' },
    { title: 'Tối ưu tốc độ trang tin tức', summary: 'Các kỹ thuật front-end giúp trang tin tải nhanh và ổn định.', link: 'article.php?id=toi-uu-toc-do-trang-tin-tuc' }
  ],
  newArticles: [
    { title: 'Xu hướng tìm kiếm 2026', summary: 'Những chủ đề và từ khoá được độc giả tìm kiếm nhiều nhất.', link: 'article.php?id=xu-huong-tim-kiem-2026' },
    { title: 'Cách viết tiêu đề thu hút', summary: 'Kỹ thuật tạo tiêu đề khiến người đọc muốn click ngay.', link: 'article.php?id=cach-viet-tieu-de-thu-hut' },
    { title: 'Phân tích hành vi người đọc', summary: 'Hiểu thói quen đọc để tối ưu độ tương tác và retention.', link: 'article.php?id=phan-tich-hanh-vi-nguoi-doc' },
    { title: 'Làm nội dung cho mobile', summary: 'Thiết kế và định dạng nội dung phù hợp với người dùng di động.', link: 'article.php?id=lam-noi-dung-cho-mobile' },
    { title: 'Bảo mật trang tin tức', summary: 'Các biện pháp bảo vệ dữ liệu người dùng an toàn.', link: 'article.php?id=bao-mat-trang-tin-tuc' },
    { title: 'Xây dựng hệ thống đề xuất', summary: 'Giới thiệu logic gợi ý bài viết dựa trên hành vi đọc.', link: 'article.php?id=xay-dung-he-thong-de-xuat' }
  ],
  recommendations: [
    { title: 'Cách viết tiêu đề thu hút', summary: 'Kỹ thuật tạo tiêu đề khiến người đọc muốn click ngay.', link: 'article.php?id=cach-viet-tieu-de-thu-hut' },
    { title: 'Làm nội dung cho mobile', summary: 'Định dạng và bố cục bài viết trên điện thoại.', link: 'article.php?id=lam-noi-dung-cho-mobile' },
    { title: 'Xây dựng hệ thống đề xuất', summary: 'Gợi ý bài viết cá nhân hóa giúp người đọc quay lại.', link: 'article.php?id=xay-dung-he-thong-de-xuat' }
  ]
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

function normalizeArrayResponse(response, fallback, fallbackKey) {
  if (Array.isArray(response)) return response;
  if (response && typeof response === 'object') {
    if (Array.isArray(response[fallbackKey])) return response[fallbackKey];
    if (Array.isArray(response.data)) return response.data;
    if (Array.isArray(response.items)) return response.items;
  }
  return fallback;
}

async function getCategories() {
  const response = await fetchJSON('categories');
  return normalizeArrayResponse(response, fallbackData.categories, 'categories');
}

async function getArticles(section) {
  const response = await fetchJSON(`articles&section=${section}`);
  const fallback = section === 'featured' ? fallbackData.featuredArticles : fallbackData.newArticles;
  return normalizeArrayResponse(response, fallback, 'articles');
}

async function getRecommendations(userId) {
  const response = await fetchJSON('recommendations');
  return normalizeArrayResponse(response, fallbackData.recommendations, 'recommendations');
}

function showSearchResults(query, items) {
  const resultsSection = document.getElementById('search-results-section');
  const queryText = document.getElementById('search-query-text');
  const resultsList = document.getElementById('search-results-list');

  if (!resultsSection || !resultsList || !queryText) return;

  queryText.textContent = query;
  resultsSection.hidden = false;

  if (!Array.isArray(items) || items.length === 0) {
    resultsList.innerHTML = '<p class="empty-state">Không tìm thấy bài viết phù hợp.</p>';
    return;
  }

  resultsList.innerHTML = '';
  items.forEach((item, index) => {
    const card = document.createElement('article');
    card.className = 'article-card reveal';
    card.style.transitionDelay = `${index * 60}ms`;
    card.innerHTML = `
      <h3>${item.title}</h3>
      <p>${item.summary || ''}</p>
      ${item.link ? `<a class="text-link" href="${item.link}">Xem bài viết <span>→</span></a>` : ''}
    `;
    resultsList.appendChild(card);
  });
}

function hideSearchResults() {
  const resultsSection = document.getElementById('search-results-section');
  if (resultsSection) {
    resultsSection.hidden = true;
  }
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
    const card = document.createElement('article');
    card.className = containerId === 'categories-list' ? 'category-card reveal' : 'article-card reveal';
    card.style.transitionDelay = `${index * 60}ms`;
    card.innerHTML = `
      <h3>${item.title}</h3>
      <p>${item.description || item.summary || ''}</p>
      ${item.link ? `<a class="text-link" href="${item.link}">Đọc tiếp <span>→</span></a>` : ''}
    `;
    container.appendChild(card);
  });
}

function renderSearchResults(items) {
  if (!Array.isArray(items)) return;
  const mapped = items.map(item => ({
    title: item.title,
    summary: item.summary,
    link: item.link || `article.php?id=${item.slug || item.id}`
  }));
  return mapped;
}

async function loadHomePage() {
  const [categories, featuredArticles, newArticles, recommendations] = await Promise.all([
    getCategories(),
    getArticles('featured'),
    getArticles('new'),
    getRecommendations()
  ]);

  state.categories = categories;
  state.featuredArticles = featuredArticles;
  state.newArticles = newArticles;
  state.recommendations = recommendations;

  renderCards('categories-list', state.categories);
  renderCards('featured-list', state.featuredArticles);
  renderCards('new-list', state.newArticles);
  renderCards('recommendation-list', state.recommendations);
  hideSearchResults();
}

async function searchArticles(query) {
  const response = await fetchJSON(`search&q=${encodeURIComponent(query)}`);
  if (Array.isArray(response)) return response;

  const normalized = query.trim().toLowerCase();
  return [...state.featuredArticles, ...state.newArticles].filter(item => {
    return item.title.toLowerCase().includes(normalized) || item.summary.toLowerCase().includes(normalized);
  });
}

function filterArticles(query) {
  if (!query.trim()) {
    hideSearchResults();
    return;
  }

  searchArticles(query).then(results => {
    const mapped = renderSearchResults(results);
    showSearchResults(query, mapped);
    initReveal();
  });
}

function initTheme() {
  const storedTheme = localStorage.getItem('tintuc-theme');
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const theme = storedTheme || (prefersDark ? 'dark' : 'light');
  document.body.setAttribute('data-theme', theme);
  const icon = document.querySelector('.theme-toggle-icon');
  if (icon) icon.textContent = theme === 'dark' ? '🌙' : '☀️';
}

function initReveal() {
  const elements = document.querySelectorAll('.reveal');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15 });

  elements.forEach(element => observer.observe(element));
}

function initScrollEffects() {
  const ambient = document.querySelector('.hero-ambient');
  const backToTop = document.querySelector('.back-to-top');

  window.addEventListener('scroll', () => {
    const scrollY = window.scrollY;
    if (ambient) ambient.style.transform = `translate3d(0, ${scrollY * 0.08}px, 0)`;
    if (backToTop) backToTop.classList.toggle('is-visible', scrollY > 500);
  });

  backToTop?.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

function initNavigation() {
  const toggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.nav-links');
  toggle?.addEventListener('click', () => {
    const isOpen = nav.classList.toggle('is-open');
    toggle.setAttribute('aria-expanded', String(isOpen));
  });
}

function initThemeToggle() {
  const button = document.querySelector('.theme-toggle');
  button?.addEventListener('click', () => {
    const currentTheme = document.body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    document.body.setAttribute('data-theme', currentTheme);
    localStorage.setItem('tintuc-theme', currentTheme);
    const icon = document.querySelector('.theme-toggle-icon');
    if (icon) icon.textContent = currentTheme === 'dark' ? '🌙' : '☀️';
  });
}

function initLoadingState() {
  document.body.classList.add('is-loading');
  window.setTimeout(() => {
    document.body.classList.remove('is-loading');
  }, 700);
}

function getCurrentPage() {
  const path = window.location.pathname;
  return path.endsWith('article.php') ? 'article' : 'home';
}

async function initPage() {
  const isHomePage = document.getElementById('categories-list')
    || document.getElementById('featured-list')
    || document.getElementById('new-list')
    || document.getElementById('recommendation-list');

  if (isHomePage) {
    await loadHomePage();
  }

  window.scrollTo({ top: 0, behavior: 'auto' });
}

const searchForm = document.getElementById('search-form');
searchForm?.addEventListener('submit', event => {
  event.preventDefault();
  const input = document.getElementById('search-input');
  if (input) filterArticles(input.value);
});

window.addEventListener('DOMContentLoaded', () => {
  initPage();
  initTheme();
  initThemeToggle();
  initNavigation();
  initScrollEffects();
  initReveal();
  initLoadingState();
});