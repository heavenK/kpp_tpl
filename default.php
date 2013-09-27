<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

    <script type="text/javascript">
        var Sys = {}; var ua = navigator.userAgent.toLowerCase(); var s; (s = ua.match(/msie ([\d.]+)/)) ? Sys.ie = s[1] : (s = ua.match(/firefox\/([\d.]+)/)) ? Sys.firefox = s[1] : (s = ua.match(/chrome\/([\d.]+)/)) ? Sys.chrome = s[1] : (s = ua.match(/opera.([\d.]+)/)) ? Sys.opera = s[1] : (s = ua.match(/version\/([\d.]+).*safari/)) ? Sys.safari = s[1] : 0;

        var url = 'zh-cn/user/';

        if (CheckVersion(Sys.ie, '8') || CheckVersion(Sys.ie, '9')) {
            window.location = url;
        }

        if (CheckVersion(Sys.firefox, '3') || CheckVersion(Sys.firefox, '4')) {
            window.location = url;
        }
        if (CheckVersion(Sys.chrome, '8') || CheckVersion(Sys.safari, '5') || CheckVersion(Sys.opera, '9')) {
            window.location = url;
        }

        function CheckVersion(ver, compare) {
            try {
                if (ver.substring(0, 1) >= compare) {
                    return true;

                }
                else {
                    return false;
                }
            }
            catch (e) {
                return false;
            }
        }

    </script>

    <title>浏览器升级提示 - Update Your Browser - Mettez à jour votre navigateur
    </title>
    <link href="tpl/default/d8/css/login.css" rel="stylesheet" rev="stylesheet" type="text/css" />
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
</head>
<body>
    <div class="login">
        <div class="msg">
            <h5>
                浏览器升级提示</h5>
            <p>
                软件兼容IE8、IE9、Firefox 4+、Safari 5+、Chorme 8+、Opera 11+主流浏览器，您的浏览器可能存在兼容的问题，请更新您的浏览器，
                
            </p>
        </div>
    </div>
    <div class="login">
        <div class="msg">
            <h5>
                Update your browser</h5>
            <p>
                This software compatible with IE8, IE9,Firefox 4+, Safari 5+, Chorme
                8+, Opera 11+ major browsers, your browser may have compatibility issues, please update
                your browser.
            </p>
        </div>
    </div>
    <div class="login">
        <div class="msg">
            <h5>
                Mettez à jour votre navigateur</h5>
            <p>
         De logiciels compatibles avec IE8, IE9,Firefox 4+, Safari 5+, Chorme
                8+, Opera 11+  principaux navigateurs, votre navigateur peut avoir des problèmes de compatibilité, s'il vous plaît mettre à jour
                 votre navigateur.
            </p>
        </div>
    </div>
    
    <div class="bottom">
        &copy; 2004 - 2013 <a><i>SigilSoft</i></a>
        Inc.
    </div>
</body>
</html>
