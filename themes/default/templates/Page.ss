<!DOCTYPE html>
<!--[if !IE]><!-->
<html lang="$ContentLocale">
<!--<![endif]-->
<!--[if IE 6 ]><html lang="$ContentLocale" class="ie ie6"><![endif]-->
<!--[if IE 7 ]><html lang="$ContentLocale" class="ie ie7"><![endif]-->
<!--[if IE 8 ]><html lang="$ContentLocale" class="ie ie8"><![endif]-->
<head>
    $SiteConfig.GoogleSiteVerificationCode.RAW
    <% base_tag %>
    <title><% if $MetaTitle %>$MetaTitle<% else %>$Title<% end_if %></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    $MetaTags(false)
    <% include OG %>
    <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <% if $ThemedCSS %>
        <link rel="stylesheet" type="text/css" href="$ThemedCSS" />
    <% end_if %>

    <%-- <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#d7221c">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff"> --%>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script src="//res.wx.qq.com/connect/zh_CN/htmledition/js/wxLogin.js"></script>
    $SiteConfig.GoogleAnalyticsCode.RAW
    $SiteConfig.GTMHead.RAW
</head>
<body class="$ClassName.ShortName.LowerCase" <% if $i18nScriptDirection %>dir="$i18nScriptDirection"<% end_if %>>
$SiteConfig.GTMBody.RAW
<% include Header %>
<div id="login_container"></div>
<main id="main" class="main">
    $Layout
</main>
<% include Footer %>
<script src="$ThemeDir/dist/main.min.js" defer></script>
<script>
var obj = new WxLogin({
    // self_redirect:true,
    id:"login_container",
    appid: "wxb19a69fd97bc9fa9",
    scope: "snsapi_login",
    redirect_uri: "http://ningdelen.cn/signup"
});
</script>
</body>
</html>
