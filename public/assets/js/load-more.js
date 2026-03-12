window.LoadMore = (function() {
    let cursors = { latest: null, popular: null };
    let isLoading = false;

    function escapeHtml(text) {
        if (!text) return "";
        const div = document.createElement("div");
        div.textContent = text;
        return div.innerHTML;
    }

    async function loadMorePosts(type) {
        if (isLoading) return;

        const container = document.getElementById(`${type}-posts-container`);
        const btn = document.querySelector(`.load-more-btn[data-type="${type}"]`);
        const cursor = cursors[type];

        if (!container || !cursor) return;

        isLoading = true;
        if (btn) btn.disabled = true;

        try {
            const params = new URLSearchParams({ last_id: cursor.last_id });
            if (type === 'popular') params.append('last_views', cursor.last_views);
            else params.append('last_created_at', cursor.last_created_at);

            const response = await fetch(`/load-more-${type}?${params}`);
            const data = await response.json();

            if (data.success && data.posts.length > 0) {
                data.posts.forEach(post => {
                    const html = `
                        <article class="post-preview">
                            ${post.image ? `<img src="${post.image}" alt="${escapeHtml(post.title)}" loading="lazy">` : ''}
                            <h3><a href="${post.url}">${escapeHtml(post.title)}</a></h3>
                            <p>${escapeHtml(post.excerpt || '')}</p>
                            <div class="post-meta">
                                <span>Просмотров: ${post.views}</span>
                                <span>Дата: ${post.created_at}</span>
                            </div>
                        </article>`;
                    container.insertAdjacentHTML('beforeend', html);
                });

                cursors[type] = data.next_cursor;
                if (!data.has_more) btn.parentElement.remove();
            }
        } catch (e) {
            console.error(e);
        } finally {
            isLoading = false;
            if (btn) btn.disabled = false;
        }
    }

    return {
        init: (initial) => { cursors = { ...cursors, ...initial }; },
        loadMorePosts: loadMorePosts
    };
})();

document.addEventListener('DOMContentLoaded', () => {
    const latest = document.getElementById('latest-posts-container');
    const popular = document.getElementById('popular-posts-container');
    const initData = {};

    if (latest?.dataset.nextCursor) initData.latest = JSON.parse(latest.dataset.nextCursor);
    if (popular?.dataset.nextCursor) initData.popular = JSON.parse(popular.dataset.nextCursor);

    window.LoadMore.init(initData);

    document.querySelectorAll('.load-more-btn[data-type]').forEach(btn => {
        btn.addEventListener('click', () => window.LoadMore.loadMorePosts(btn.dataset.type));
    });
});