{extends file='layouts/main.tpl'}

{block name='content'}
    <div class="error-page">
        <h1>404 - Страница не найдена</h1>
        <p>Извините, запрошенная страница не существует.</p>
        <a href="{$base_url}/" class="btn">Вернуться на главную</a>
    </div>
{/block}