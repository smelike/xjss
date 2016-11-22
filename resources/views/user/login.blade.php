<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>用户登陆</title>
    <script src="/xj/public/js/jq12.min.js"></script>
    <script src="/xj/public/js/jquery.md5.js"></script>
    <script src="/xj/public/js/main/user.js"></script>
</head>
<body>
    <div align="center">
        <h3>用户登陆</h3>
        <div class="item">
            <span id="errorTel">请输入正确的手机号码</span> <br>
            <input id="tel" type="tel" placeholder="输入手机号">
        </div>
        <div class="item">
            <span id="pwdEmpty">请输入密码</span> <br>
            <input type="password" id="pwd" placeholder="密码">
        </div>

        <div>
            <button id="login">登陆</button>
            <button id="toRegister" onclick="toRegister()">前往注册</button>
        </div>
        <input type="hidden" id="flag" value="login">
    </div>
</body>
</html>
<script>
    function toRegister(){
        location.href = '/xj/user/register';
    }
</script>