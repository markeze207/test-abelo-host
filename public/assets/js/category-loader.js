window.CategoryLoader = (function() {
    let cursor = null;
    let isLoading = false;

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function renderCategory(cat) {
        let postsHtml = cat.posts.map(post => `
            <article class="post-preview">
                ${post.image ? `<img src="${post.image}" alt="${escapeHtml(post.title)}">` : ''}
                <h3><a href="${post.url}">${escapeHtml(post.title)}</a></h3>
                <p>${escapeHtml(post.excerpt || '')}</p>
                <div class="post-meta">
                    <span>Просмотров: ${post.views}</span>
                    <span>Дата: ${post.created_at}</span>
                </div>
            </article>
        `).join('');

        return `
            <section class="category" data-category-id="${cat.id}">
                <h2><a href="${cat.url}">${escapeHtml(cat.name)}</a></h2>
                ${cat.description ? `<p class="category-description">${escapeHtml(cat.description)}</p>` : ''}
                <div class="posts-grid">${postsHtml}</div>
                <a href="${cat.url}" class="btn">Все статьи категории</a>
            </section>
        `;
    }

    async function loadMore() {
        if (isLoading || !cursor) return;

        const btn = document.getElementById('btn-load-more-categories');
        const container = document.getElementById('categories-container');
        const spinner = btn?.querySelector('.loading-spinner');

        isLoading = true;
        if (btn) btn.disabled = true;
        if (spinner) spinner.style.display = 'inline-block';

        try {
            const params = new URLSearchParams({
                last_id: cursor.last_id,
                last_name: cursor.last_name
            });

            const response = await fetch(`/load-more-categories?${params}`);
            const data = await response.json();

            if (data.success && data.categories.length > 0) {
                data.categories.forEach(cat => {
                    container.insertAdjacentHTML('beforeend', renderCategory(cat));
                });

                cursor = data.next_cursor;
                if (!data.has_more) {
                    document.getElementById('load-more-categories-wrapper')?.remove();
                }
            }
        } catch (error) {
            console.error('Failed to load categories:', error);
        } finally {
            isLoading = false;
            if (btn) btn.disabled = false;
            if (spinner) spinner.style.display = 'none';
        }
    }

    return {
        init: function(initialCursor) {
            cursor = initialCursor;
            const btn = document.getElementById('btn-load-more-categories');
            if (btn) {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.loadMore();
                });
            }
        },
        loadMore: loadMore
    };
})();

document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('categories-container');
    if (container && container.dataset.nextCursor) {
        window.CategoryLoader.init(JSON.parse(container.dataset.nextCursor));
    }
});