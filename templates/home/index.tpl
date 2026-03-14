{extends file='layouts/main.tpl'}

{block name='content'}
    <h1>Блог</h1>

    {* Категории *}
    <div class="categories"
         id="categories-container"
         {if $next_categories_cursor}data-next-cursor='{$next_categories_cursor|json_encode nofilter}'{/if}>
        {if $categories}
            {foreach $categories as $category}
                <section class="category" data-category-id="{$category->id}">
                    <h2><a href="{$category->getUrl()}">{$category->name|escape}</a></h2>
                    {if $category->description}
                        <p class="category-description">{$category->description|escape}</p>
                    {/if}
                    <div class="posts-grid">
                        {include file='partials/post_cards.tpl' posts=$category->posts}
                    </div>
                    <a href="{$category->getUrl()}" class="btn">Все статьи категории</a>
                </section>
            {/foreach}
        {/if}
    </div>

    {if $has_more_categories}
        <div class="load-more-container" id="load-more-categories-wrapper">
            <button class="load-more-btn" id="btn-load-more-categories">
                Показать еще категории
                <span class="loading-spinner" style="display: none;"></span>
            </button>
        </div>
    {/if}

    <section class="popular-posts">
        <h2>Популярные статьи</h2>
        <div class="posts-grid"
             id="popular-posts-container"
             {if $popular_next_cursor}data-next-cursor='{$popular_next_cursor|json_encode nofilter}'{/if}>
            {include file='partials/post_cards.tpl' posts=$popular_posts}
        </div>
        {if $has_more_popular}
            <div class="load-more-container">
                <button class="load-more-btn" data-type="popular">Показать еще</button>
            </div>
        {/if}
    </section>

    <section class="latest-posts">
        <h2>Последние статьи</h2>
        <div class="posts-grid"
             id="latest-posts-container"
             {if $latest_next_cursor}data-next-cursor='{$latest_next_cursor|json_encode nofilter}'{/if}>
            {include file='partials/post_cards.tpl' posts=$latest_posts}
        </div>
        {if $has_more_latest}
            <div class="load-more-container">
                <button class="load-more-btn" data-type="latest">Показать еще</button>
            </div>
        {/if}
    </section>

    <script src="/assets/js/category-loader.js"></script>
    <script src="/assets/js/load-more.js"></script>
{/block}