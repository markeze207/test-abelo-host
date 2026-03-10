{extends file='layouts/main.tpl'}

{block name='content'}
    <article class="post-full">
        <header class="post-header">
            <h1>{$post->title|escape}</h1>

            {if $post->categories}
                <div class="post-categories">
                    Категории:
                    {foreach $post->categories as $category}
                        <a href="{$base_url}/category/{$category->slug}" class="category-tag">
                            {$category->name|escape}
                        </a>
                        {if !$category@last}, {/if}
                    {/foreach}
                </div>
            {/if}

            <div class="post-meta">
                <span>Дата: {$post->getFormattedDate('d.m.Y')}</span>
                <span>Просмотров: {$post->views}</span>
            </div>
        </header>

        {if $post->image}
            <div class="post-full-image">
                <img src="{$post->getImageUrl()}"
                     alt="{$post->title|escape}">
            </div>
        {/if}

        {if $post->description}
            <div class="post-description">
                <p class="lead">{$post->description|escape}</p>
            </div>
        {/if}

        <div class="post-content">
            {$post->content|nl2br}
        </div>
    </article>

    {if $similar_posts}
        <section class="similar-posts">
            <h2>Похожие статьи</h2>
            <div class="posts-grid">
                {foreach $similar_posts as $similar}
                    <article class="post-preview">
                        {if $similar->image}
                            <img src="{$similar->getImageUrl()}"
                                 alt="{$similar->title|escape}">
                        {/if}

                        <h3>
                            <a href="{$similar->getUrl()}">
                                {$similar->title|escape}
                            </a>
                        </h3>

                        <p>{$similar->getExcerpt(100)|escape}</p>

                        <div class="post-meta">
                            <span>{$similar->getFormattedDate('d.m.Y')}</span>
                            <span>•</span>
                            <span>Просмотров: {$similar->views}</span>
                        </div>
                    </article>
                {/foreach}
            </div>
        </section>
    {/if}
{/block}