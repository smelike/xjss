<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>用户注册</title>
    <script src="/xj/public/js/jq12.min.js"></script>
    <script src="/xj/public/js/jquery.md5.js"></script>
    <script src="/xj/public/js/main/user.js"></script>
</head>
<body>
    <div align="center">
        <h3>用户注册</h3>
        <div class="item">
            <span id="nameEmpty">请输入用户名</span> <br>
            <input type="text" id="name" placeholder="姓名">
        </div>
        <div class="item">
            <span id="errorTel">请输入正确的手机号码</span> <br>
            <input id="tel" type="tel" placeholder="输入手机号">
        </div>
        <div class="item">
            <span id="emptyCaptcha">请输入验证码</span> <br>
            <input type="number" id="captcha" placeholder="验证码">
            <button id="getCaptcha">获取验证码</button>
        </div>
        <div>
            <span id="pwdEmpty">请输入密码</span>
            <br>
            <input type="password" id="pwd" placeholder="密码">
            <br>
            <span id="pwdUnmatched">您两次输入的密码不一致</span>
            <br>
            <input type="password" id="pwd2" placeholder="确认密码">
        </div>
        <div class="item">
            <span id="emptyType">请选择注册类型</span>
            <label class="type" for="type">类型:</label>
            <br/>
            <select name="type" id="type">
                <option value="-1">请选择所在公司</option>
                <option value="30">业务员</option>
                <option value="40">开票员</option>
                <option value="50">经销商</option>
                <option value="60">客户</option>
            </select>
        </div>

        <div>
            <button id="register">注册</button>
            <button id="toLogin" onclick="toLogin()">前往登陆</button>
        </div>
        <input type="hidden" id="flag" value="register">
    </div>
</body>
</html>
<script>
    function toLogin(){
        location.href = '/xj/user/login';
    }
</script>