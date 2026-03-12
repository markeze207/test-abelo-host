{extends file='layouts/main.tpl'}

{block name='content'}
    <div class="category-header">
        <h1>{$category->name|escape}</h1>
        {if $category->description}
            <p class="category-description">{$category->description|escape}</p>
        {/if}
    </div>

    <div class="sorting">
        <span>Сортировать:</span>
        <a href="?sort=created_at&order={if $sort_by == 'created_at' && $order == 'DESC'}ASC{else}DESC{/if}&page=1"
           class="{if $sort_by == 'created_at'}active{/if}">
            По дате {if $sort_by == 'created_at'}{if $order == 'DESC'}↓{else}↑{/if}{/if}
        </a>
        <a href="?sort=views&order={if $sort_by == 'views' && $order == 'DESC'}ASC{else}DESC{/if}&page=1"
           class="{if $sort_by == 'views'}active{/if}">
            По просмотрам {if $sort_by == 'views'}{if $order == 'DESC'}↓{else}↑{/if}{/if}
        </a>
    </div>

    {if $posts}
        <div class="posts-list">
            {foreach $posts as $post}
                <article class="post-item">
                    {if $post->getImageUrl()}
                        <div class="post-image">
                            <img src="{$post->getImageUrl()}" alt="{$post->title|escape}">
                        </div>
                    {/if}
                    <div class="post-content">
                        <h2><a href="{$post->getUrl()}">{$post->title|escape}</a></h2>
                        <p class="post-description">{$post->description|escape}</p>
                        <div class="post-meta">
                            <span><i class="far fa-calendar"></i> {$post->getFormattedDate('d.m.Y')}</span>
                            <span><i class="far fa-eye"></i> {$post->views}</span>
                        </div>

                        <a href="{$post->getUrl()}" class="read-more">
                            Читать далее
                        </a>
                    </div>
                </article>
            {/foreach}
        </div>

        {if $pagination.total_pages > 1}
            <div class="pagination">
                {if $pagination.current_page > 1}
                    <a href="?page={$pagination.current_page - 1}&sort={$sort_by|escape:'url'}&order={$order}" class="prev">
                        &laquo; Назад
                    </a>
                {/if}

                {for $i = 1 to $pagination.total_pages}
                    {if $i >= $pagination.current_page - 2 && $i <= $pagination.current_page + 2}
                        {if $i == $pagination.current_page}
                            <span class="current">{$i}</span>
                        {else}
                            <a href="?page={$i}&sort={$sort_by|escape:'url'}&order={$order}">{$i}</a>
                        {/if}
                    {/if}
                {/for}

                {if $pagination.current_page < $pagination.total_pages}
                    <a href="?page={$pagination.current_page + 1}&sort={$sort_by|escape:'url'}&order={$order}" class="next">
                        Вперед &raquo;
                    </a>
                {/if}
            </div>
        {/if}
    {else}
        <div class="empty-category">
            <p>В этой категории пока нет статей.</p>
            <a href="/" class="btn-home">На главную</a>
        </div>
    {/if}
{/block}