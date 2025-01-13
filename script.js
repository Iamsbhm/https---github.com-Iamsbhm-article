document.addEventListener('DOMContentLoaded', () => {
    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navLinks = document.querySelector('.nav-links');

    mobileMenuBtn?.addEventListener('click', () => {
        navLinks?.classList.toggle('active');
    });

    // Category tabs
    const tabs = document.querySelectorAll('.tab');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Remove active class from all tabs
            tabs.forEach(t => t.classList.remove('active'));
            // Add active class to clicked tab
            tab.classList.add('active');
            
            // Fetch articles for selected category
            fetchArticles(tab.textContent.toLowerCase());
        });
    });

    // Search functionality
    const searchInput = document.querySelector('.search-box input');
    const searchBtn = document.querySelector('.search-box button');

    let searchTimeout;
    searchInput?.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performSearch(e.target.value);
        }, 500);
    });

    searchBtn?.addEventListener('click', () => {
        performSearch(searchInput.value);
    });

    // Article lazy loading
    const observerOptions = {
        root: null,
        rootMargin: '50px',
        threshold: 0.1
    };

    const articleObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target.querySelector('img');
                if (img && img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                }
                articleObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.article-card').forEach(card => {
        articleObserver.observe(card);
    });
});

// Fetch articles from the server
async function fetchArticles(category) {
    try {
        const response = await fetch(`/api/articles?category=${category}`);
        if (!response.ok) throw new Error('Failed to fetch articles');
        
        const articles = await response.json();
        updateArticlesGrid(articles);
    } catch (error) {
        console.error('Error fetching articles:', error);
        // Show error message to user
        showNotification('Failed to load articles. Please try again later.', 'error');
    }
}

// Update articles grid with new content
function updateArticlesGrid(articles) {
    const grid = document.querySelector('.articles-grid');
    if (!grid) return;

    grid.innerHTML = articles.map(article => `
        <article class="article-card">
            <div class="article-image">
                <img data-src="${article.image_url}" 
                     alt="${article.title}"
                     loading="lazy">
                ${article.category ? 
                    `<span class="category-tag">${article.category}</span>` 
                    : ''}
            </div>
            <div class="article-content">
                <h2>${article.title}</h2>
                <div class="article-meta">
                    <span class="author">By ${article.author}</span>
                    <span class="date">${new Date(article.publish_date).toLocaleDateString()}</span>
                </div>
                <p class="article-excerpt">${article.excerpt}</p>
            </div>
        </article>
    `).join('');
}

// Search functionality
async function performSearch(query) {
    if (!query) return;
    
    try {
        const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
        if (!response.ok) throw new Error('Search failed');
        
        const results = await response.json();
        updateArticlesGrid(results);
    } catch (error) {
        console.error('Error performing search:', error);
        showNotification('Search failed. Please try again later.', 'error');
    }
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}