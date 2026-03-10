<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{$page_title|default:$app_name} | {$app_name}</title>
    <link rel="stylesheet" href="{$base_url}/assets/css/style.css">
</head>
<body>
{include file='partials/header.tpl'}

<main class="container">
    {block name='content'}{/block}
</main>

{include file='partials/footer.tpl'}

</body>
</html>