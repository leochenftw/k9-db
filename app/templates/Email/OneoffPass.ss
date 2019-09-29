<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        登录通行证
    </title>
</head>
<body>
    <p>您好,</p>
    <p>以下为您此次的一次性登录通行证. 通行证将在使用后过期.</p>
    <p><a href="{$baseURL}one-off-pass?id={$Member.ID}&token={$Member.OneoffToken}">{$baseURL}one-off-pass?id={$Member.ID}&token={$Member.OneoffToken}</a><br /><small>如果链接无法点击, 请手动复制到浏览器地址栏打开.</small></p>
    <p><small>若通行证在24小时内未被使用将自动过期.</small></p>
    <p>我爱工作犬平台</p>
</body>
</html>
