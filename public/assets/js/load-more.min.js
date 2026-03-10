window.LoadMore = (function() {
    let pages = {
        latest: 1,
        popular: 1
    };

    function escapeHtml(text) {
        if (!text) return "";
        const div = document.createElement("div");
        div.textContent = text;
        return div.innerHTML;
    }

    function createPostHTML(post) {
        let imageHtml = "";

        if (post.image) {
            if (post.image.startsWith("http") || post.image.startsWith("/")) {
                imageHtml = `<img src="${post.image}" alt="${escapeHtml(post.title)}" loading="lazy">`;
            } else {
                imageHtml = `<img src="${post.image}" alt="${escapeHtml(post.title)}" loading="lazy">`;
            }
        }

        const postUrl = post.slug ? `/post/${post.slug}` : post.url;

        return `
      <article class="post-preview">
        ${imageHtml}
        <h3>
          <a href="${postUrl}">
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

    function setLoadingState(button, isLoading) {
        const spinner = button.querySelector(".loading-spinner");

        if (spinner) {
            spinner.style.display = isLoading ? "inline-block" : "none";
        }

        button.disabled = isLoading;
    }

    async function loadMorePosts(type, page) {
        const container = document.getElementById(`${type}-posts-container`);
        const buttonContainer = document.getElementById(`load-more-${type}`);

        if (!container || !buttonContainer) {
            console.error(`Container or button not found for type: ${type}`);
            return;
        }

        const loadButton = buttonContainer.querySelector(".load-more-btn");
        setLoadingState(loadButton, true);

        try {
            const response = await fetch(`/load-more-${type}?page=${page}`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success && data.posts.length > 0) {
                data.posts.forEach(post => {
                    const html = createPostHTML(post);
                    container.insertAdjacentHTML("beforeend", html);
                });

                pages[type] = page;

                if (data.has_more) {
                    loadButton.setAttribute("onclick", `LoadMore.loadMorePosts('${type}', ${page + 1})`);
                } else {
                    buttonContainer.style.display = "none";
                }
            } else {
                buttonContainer.style.display = "none";
            }
        } catch (error) {
            console.error("Error loading more posts:", error);
            alert("Произошла ошибка при загрузке статей. Пожалуйста, попробуйте позже.");
        } finally {
            setLoadingState(loadButton, false);
        }
    }

    return {
        init: function() {
            console.log("LoadMore module initialized with slug support");
        },

        loadMorePosts: loadMorePosts
    };
})();

document.addEventListener("DOMContentLoaded", function() {
    window.LoadMore.init();
});