{extends file='layouts/main.tpl'}

{block name='content'}
    <h1>Блог</h1>

    <div class="categories" id="categories-container">
        {if $categories}
            {foreach $categories as $category}
                <section class="category" data-category-id="{$category->id}">
                    <h2>
                        <a href="{$base_url}/category/{$category->id}">
                            {$category->name|escape}
                        </a>
                    </h2>

                    {if $category->description}
                        <p class="category-description">{$category->description|escape}</p>
                    {/if}

                    {if $category->posts}
                        <div class="posts">
                            {foreach $category->posts as $post}
                                <article class="post-preview">
                                    {if $post->image}
                                        <img src="{$post->getImageUrl()}"
                                             alt="{$post->title|escape}">
                                    {/if}

                                    <h3>
                                        <a href="/post/{$post->slug}">
                                            {$post->title|escape}
                                        </a>
                                    </h3>

                                    <p>{$post->excerpt|default:''|escape}</p>

                                    <div class="post-meta">
                                        <span>Просмотров: {$post->views}</span>
                                        <span>Дата: {$post->created_at|date_format:'d.m.Y'}</span>
                                    </div>
                                </article>
                            {/foreach}
                        </div>

                        <a href="{$base_url}/category/{$category->slug}" class="btn">
                            Все статьи категории
                        </a>
                    {else}
                        <p>В этой категории пока нет статей</p>
                    {/if}
                </section>
            {/foreach}
        {else}
            <p>Категории не найдены</p>
        {/if}
    </div>

    {if $has_more_categories}
        <div class="load-more-container" id="load-more-categories">
            <button class="load-more-btn" onclick="CategoryLoader.loadMore(2)">
                Показать еще категории
                <span class="loading-spinner" style="display: none;"></span>
            </button>
        </div>
    {/if}

    {if $latest_posts}
        <section class="latest-posts">
            <h2>Последние статьи</h2>
            <div class="posts-grid" id="latest-posts-container">
                {include file='partials/post_cards.tpl' posts=$latest_posts}
            </div>

            {if $latest_total > 6}
                <div class="load-more-container" id="load-more-latest">
                    <button class="load-more-btn" onclick="LoadMore.loadMorePosts('latest', 2)">
                        Показать еще
                        <span class="loading-spinner" style="display: none;"></span>
                    </button>
                </div>
            {/if}
        </section>
    {/if}

    {if $popular_posts}
        <section class="popular-posts">
            <h2>Популярные статьи</h2>
            <div class="posts-grid" id="popular-posts-container">
                {include file='partials/post_cards.tpl' posts=$popular_posts}
            </div>

            {if $popular_total > 6}
                <div class="load-more-container" id="load-more-popular">
                    <button class="load-more-btn" onclick="LoadMore.loadMorePosts('popular', 2)">
                        Показать еще
                        <span class="loading-spinner" style="display: none;"></span>
                    </button>
                </div>
            {/if}
        </section>
    {/if}

    <script src="/assets/js/load-more.js"></script>
    <script src="/assets/js/category-loader.js"></script>
{/block}