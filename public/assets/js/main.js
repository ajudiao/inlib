/* ================================
   INLIB - MAIN UTILITIES
   ================================ */

// Mobile menu toggle
function toggleMenu() {
  const nav = document.querySelector('nav');
  if (nav) {
    nav.classList.toggle('active');
  }
}

// Close mobile menu when link is clicked
document.addEventListener('DOMContentLoaded', function() {
  const menuToggle = document.querySelector('.menu-toggle');
  const navLinks = document.querySelectorAll('nav a');

  if (menuToggle) {
    menuToggle.addEventListener('click', toggleMenu);
  }

  navLinks.forEach(link => {
    link.addEventListener('click', function() {
      const nav = document.querySelector('nav');
      if (nav && nav.classList.contains('active')) {
        nav.classList.remove('active');
      }
    });
  });

  // Search functionality
  const searchBox = document.querySelector('.search-box');
  const searchIcon = document.querySelector('.search-icon');
  const searchInput = document.querySelector('.search-box input');

  if (searchIcon) {
    searchIcon.addEventListener('click', function() {
      const query = searchInput ? searchInput.value : '';
      if (query.trim()) {
        window.location.href = `/pesquisa?q=${encodeURIComponent(query)}`;
      }
    });
  }

  if (searchInput) {
    searchInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        const query = searchInput.value;
        if (query.trim()) {
          window.location.href = `/pesquisa?q=${encodeURIComponent(query)}`;
        }
      }
    });
  }

  function initHeroBackgroundRotation() {
    const hero = document.querySelector('.hero');
    if (!hero) return;

    const panels = hero.querySelectorAll('.hero-bg-panel');
    if (panels.length !== 2) return;

    const heroImages = [
      'https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=1600&q=80',
      'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=1600&q=80',
      'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=1600&q=80'
    ];

    let currentIndex = 0;
    let activePanel = 0;

    panels[0].style.backgroundImage = `url('${heroImages[0]}')`;
    panels[0].classList.add('active');
    panels[1].style.backgroundImage = `url('${heroImages[1]}')`;
    panels[1].style.transform = 'translateX(100%)';

    setInterval(() => {
      const nextIndex = (currentIndex + 1) % heroImages.length;
      const nextPanel = panels[1 - activePanel];
      const currentPanel = panels[activePanel];

      nextPanel.style.transition = 'none';
      nextPanel.style.transform = 'translateX(100%)';
      nextPanel.style.opacity = '1';
      nextPanel.style.backgroundImage = `url('${heroImages[nextIndex]}')`;

      void nextPanel.offsetWidth;

      nextPanel.style.transition = '';
      currentPanel.style.transform = 'translateX(-100%)';
      currentPanel.style.opacity = '0';
      nextPanel.style.transform = 'translateX(0)';
      nextPanel.classList.add('active');
      currentPanel.classList.remove('active');

      activePanel = 1 - activePanel;
      currentIndex = nextIndex;
    }, 8000);
  }

  initHeroBackgroundRotation();
});

// Format date
function formatDate(date) {
  return new Date(date).toLocaleDateString('pt-BR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
}

// Format currency
function formatCurrency(value) {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value);
}

// Show notification
function showNotification(message, type = 'info') {
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.textContent = message;
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 1rem 1.5rem;
    background-color: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#b58b33'};
    color: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 10000;
    animation: slideInRight 0.3s ease;
  `;

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.style.animation = 'slideOutRight 0.3s ease';
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}

// Confirm dialog
function confirmAction(message, callback) {
  if (confirm(message)) {
    callback();
  }
}

// Local storage helpers
function setStorage(key, value) {
  localStorage.setItem(key, JSON.stringify(value));
}

function getStorage(key) {
  const value = localStorage.getItem(key);
  return value ? JSON.parse(value) : null;
}

function removeStorage(key) {
  localStorage.removeItem(key);
}

// Pagination
function paginate(items, page = 1, itemsPerPage = 9) {
  const totalPages = Math.ceil(items.length / itemsPerPage);
  const start = (page - 1) * itemsPerPage;
  const end = start + itemsPerPage;

  return {
    items: items.slice(start, end),
    totalPages: totalPages,
    currentPage: page,
    hasNext: page < totalPages,
    hasPrev: page > 1
  };
}

// Render book cards
function renderBookCards(books, containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;

  if (books.length === 0) {
    container.innerHTML = '<p style="text-align: center; padding: 2rem;">Nenhum livro encontrado.</p>';
    return;
  }

  container.innerHTML = books.map(book => `
    <div class="book-card">
      <img src="${book.cover}" alt="${book.title}" class="book-cover">
      <div class="book-info">
        <h3 class="book-title">${book.title}</h3>
        <p class="book-author">por ${book.author}</p>
        <div class="book-meta">
          <span>${book.category}</span>
          <span class="book-year">${book.year}</span>
        </div>
        <p class="book-description">${book.description}</p>
        <button class="btn-view-details" onclick="viewBook(${book.id})">Ver Detalhes</button>
      </div>
    </div>
  `).join('');
}

// Render category cards
function renderCategoryCards(categories, containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;

  container.innerHTML = categories.map(category => `
    <div class="category-card" onclick="filterByCategory('${category.name}')">
      <div class="category-icon">${category.icon}</div>
      <div class="category-name">${category.name}</div>
      <div class="category-count">${category.count} livros</div>
    </div>
  `).join('');
}

// View book details
function viewBook(bookId) {
  window.location.href = `/ver_livro/${bookId}`;
}

// Filter by category
function filterByCategory(categoryName) {
  window.location.href = `/livros?category=${encodeURIComponent(categoryName)}`;
}

// Validate email
function validateEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}

// Validate form
function validateForm(formId) {
  const form = document.getElementById(formId);
  if (!form) return false;

  const inputs = form.querySelectorAll('input, textarea, select');
  let isValid = true;

  inputs.forEach(input => {
    if (!input.value.trim()) {
      input.style.borderColor = '#ef4444';
      isValid = false;
    } else {
      input.style.borderColor = '';
    }

    if (input.type === 'email' && !validateEmail(input.value)) {
      input.style.borderColor = '#ef4444';
      isValid = false;
    }
  });

  return isValid;
}

// Reset form
function resetForm(formId) {
  const form = document.getElementById(formId);
  if (form) {
    form.reset();
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
      input.style.borderColor = '';
    });
  }
}

// Get URL parameters
function getUrlParameter(name) {
  const url = new URL(window.location);
  return url.searchParams.get(name);
}

// Set page title
function setPageTitle(title) {
  document.title = `${title} - INLIB`;
  const pageTitle = document.querySelector('h1');
  if (pageTitle) {
    pageTitle.textContent = title;
  }
}

// Add smooth scroll behavior
function addSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href !== '#') {
        e.preventDefault();
        const element = document.querySelector(href);
        if (element) {
          element.scrollIntoView({ behavior: 'smooth' });
        }
      }
    });
  });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
  addSmoothScroll();
});

// Print function
function printPage() {
  window.print();
}

// Download PDF
function downloadPDF(bookId) {
  const book = getBook(bookId);
  if (book) {
    showNotification(`Abrindo PDF: ${book.title}...`, 'info');
    // In production, this would open the actual PDF
    window.open(book.pdf, '_blank');
  }
}

// Animation utilities
function fadeInElement(element, delay = 0) {
  element.style.opacity = '0';
  element.style.transform = 'translateY(10px)';
  
  setTimeout(() => {
    element.style.transition = 'all 0.3s ease';
    element.style.opacity = '1';
    element.style.transform = 'translateY(0)';
  }, delay);
}

function observeElements() {
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('fade-in');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.card, .category-card, .book-card').forEach(el => {
      observer.observe(el);
    });
  }
}

document.addEventListener('DOMContentLoaded', observeElements);
