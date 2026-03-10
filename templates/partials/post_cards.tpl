{foreach $posts as $post}
    <article class="post-preview">
        {if $post->image}
            <img src="{$post->getImageUrl()}"
                 alt="{$post->title|escape}"
                 loading="lazy">
        {/if}

        <h3>
            <a href="{$post->getUrl()}">
                {$post->title|escape}
            </a>
        </h3>

        <p>{$post->getExcerpt(100)|escape}</p>

        <div class="post-meta">
            <span>Просмотров: {$post->views}</span>
            <span>Дата: {$post->getFormattedDate('d.m.Y')}</span>
        </div>
    </article>
{/foreach}