/**
 * Created by HSF on 2016/8/26.
 */
$(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#errorTel').hide();
    $("#emptyCaptcha").hide();
    $('#nameEmpty').hide();
    $('#pwdEmpty').hide();
    $('#pwdUnmatched').hide();
    $('#emptyType').hide();

    // get telephone captcha
    $('#getCaptcha').click(function(){
        var tel = $('#tel').val();
        if (!regTel(tel)) return false;
        var flag = $('#flag').val();
        var data ={
            'tel':tel,
            'flag':flag
        };
        $(this).prop('disabled','disabled');
        countTime();
        $.post('/xj/user/captcha',data,function(msg){
            if(msg.err_code != '0000'){
                second = 0;
                alert(msg.err_msg);
                return false;
            }
            second =60;
        })
    });

    function regTel(tel){
        var reg = /^1\d{10}$/;
        if(!reg.test(tel)){
            $('#errorTel').show();
            return false;
        }else {
            $('#errorTel').hide();
        }
        return true;
    }

    var second=60;
    function countTime(){
        if(second > 0){
            $('#getCaptcha').html(second+'s');
            --second;
            setTimeout(countTime,1000);
        }else {
            second = 60;
            $('#getCaptcha').prop('disabled',false);
            $('#getCaptcha').html('重新获取');
        }
    }

    $("#name").on("focusout", function () {
        if ($(this).val() == "") {
            $("#nameEmpty").show();
            return false;
        } else {
            $("#nameEmpty").hide();
        }
    });

    //check password if empty
    $("#pwd").on("focusout", function () {
        if ($(this).val() == "") {
            $("#pwdEmpty").show();
            return false;
        } else {
            $("#pwdEmpty").hide();
        }
    });

    //check password if empty
    $("#captcha").on("focusout", function () {
        if ($(this).val() == "") {
            $("#emptyCaptcha").show();
            return false;
        } else {
            $("#emptyCaptcha").hide();
        }
    });

    $('#register').click(function(){
        var name = $('#name').val();
        if(name == ""){
            $('#nameEmpty').show();
            return false;
        }else {
            $('#nameEmpty').hide();
        }

        var tel = $('#tel').val();
        if (!regTel(tel)) return false;

        var captcha = $('#captcha').val();
        if(captcha == ""){
            $("#emptyCaptcha").show();
            return false;
        }else {
            $("#emptyCaptcha").hide();
        }

        var pwd = $('#pwd').val();
        if(pwd == ""){
            $("#pwdEmpty").show();
            return false;
        }else {
            $("#pwdEmpty").hide();
        }
        var pwd2= $('#pwd2').val();
        if(pwd !== pwd2){
            $('#pwdUnmatched').show();
            return false;
        }else {
            $('#pwdUnmatched').hide();
        }

        var type = $('#type').val();
        if(parseInt(type) == -1){
            $('#emptyType').show();
            //alert('请选择注册类型！');
            return false;
        }else {
            $('#emptyType').hide();
        }

        var data = {
            'name'  : name,
            'tel'   : tel,
            'pwd'   : $.md5(pwd),
            'captcha':captcha,
            'role'  : type
        };

        $.post('/xj/user/register',data,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            location.href = '/xj/user/register/notice';
        })
    });

    $('#login').click(function(){
        var tel = $('#tel').val();
        var pwd = $('#pwd').val();
        if (!init(tel,pwd)) return false;

        var data = {
            'tel'   : tel,
            'pwd'   : $.md5(pwd)
        };
        $.post('/xj/user/login',data,function(msg){
            if(msg.err_code != '0000'){
                if(msg.err_code == '2006'){
                    location.href = '/xj/user/register/notice';
                    return false;
                }
                alert(msg.err_msg);
                return false;
            }
            //进入用户主界面
            location.href = '/xj/home';
        });
    });

    //判断刚进入页面直接登陆或注册情况
    function init(tel,pwd){
        if(!regTel(tel)){
            return false;
        }
        if(pwd==''){
            $("#pwdEmpty").show();
            return false;
        }
        return true;
    }
});
