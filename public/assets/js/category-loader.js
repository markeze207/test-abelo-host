window.CategoryLoader = (function() {
    let currentPage = 1;

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function renderPost(post) {
        let imageHtml = '';
        if (post.image) {
            imageHtml = `<img src="${post.image}" alt="${escapeHtml(post.title)}" loading="lazy">`;
        }

        return `
            <article class="post-preview">
                ${imageHtml}
                <h3>
                    <a href="${post.url}">
                        ${escapeHtml(post.title)}
                    </a>
                </h3>
                <p>${escapeHtml(post.excerpt)}</p>
                <div class="post-meta">
                    <span>Просмотров: ${post.views}</span>
                    <span>Дата: ${post.created_at}</span>
                </div>
            </article>
        `;
    }

    function renderCategory(category) {
        let descriptionHtml = '';
        if (category.description) {
            descriptionHtml = `<p class="category-description">${escapeHtml(category.description)}</p>`;
        }

        let postsHtml = '';
        if (category.posts && category.posts.length > 0) {
            const postsList = category.posts.map(post => renderPost(post)).join('');
            postsHtml = `
                <div class="posts">
                    ${postsList}
                </div>
                <a href="${category.url}" class="btn">
                    Все статьи категории
                </a>
            `;
        } else {
            postsHtml = '<p>В этой категории пока нет статей</p>';
        }

        return `
            <section class="category" data-category-id="${category.id}">
                <h2>
                    <a href="${category.url}">
                        ${escapeHtml(category.name)}
                    </a>
                </h2>
                ${descriptionHtml}
                ${postsHtml}
            </section>
        `;
    }

    function setLoading(loading) {
        const button = document.querySelector('#load-more-categories .load-more-btn');
        if (!button) return;

        const spinner = button.querySelector('.loading-spinner');
        if (spinner) {
            spinner.style.display = loading ? 'inline-block' : 'none';
        }
        button.disabled = loading;
    }

    async function loadMore(page) {
        const container = document.getElementById('categories-container');
        const loadMoreDiv = document.getElementById('load-more-categories');

        if (!container || !loadMoreDiv) {
            console.error('Categories container or button not found');
            return;
        }

        setLoading(true);

        try {
            const response = await fetch(`/load-more-categories?page=${page}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success && data.categories && data.categories.length > 0) {
                // Render each category and append to container
                data.categories.forEach(category => {
                    const categoryHtml = renderCategory(category);
                    container.insertAdjacentHTML('beforeend', categoryHtml);
                });

                // Update current page
                currentPage = page;

                // Update button for next page or hide if no more categories
                if (data.has_more) {
                    const button = loadMoreDiv.querySelector('.load-more-btn');
                    button.setAttribute('onclick', `CategoryLoader.loadMore(${page + 1})`);
                } else {
                    loadMoreDiv.style.display = 'none';
                }

                console.log(`Loaded ${data.categories.length} categories. Total: ${data.total}`);
            } else {
                loadMoreDiv.style.display = 'none';
            }

        } catch (error) {
            console.error('Error loading more categories:', error);
            alert('Произошла ошибка при загрузке категорий. Пожалуйста, попробуйте позже.');
        } finally {
            setLoading(false);
        }
    }

    return {
        loadMore: loadMore,
        getCurrentPage: () => currentPage
    };
})();

// Auto-initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('CategoryLoader initialized');
});