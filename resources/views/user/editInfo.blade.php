<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>个人信息修改</title>
    <script src="/xj/public/js/jq12.min.js"></script>
</head>
<body>
    <div align="center">
        <h3>编辑个人信息</h3>
        用户名：<input type="text" id="name" value="{{$user->name}}">    <br>
        电话：<input id="tel" type="tel" value="{{$user->telephone}}"> <br>
        <br>
        <button onclick="edit()">确定修改</button>
    </div>
</body>
</html>
<script>
    function edit(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var name = $('#name').val();
        var tel = $('#tel').val();

        if(!name){return false;}
        var reg = /^1\d{10}$/;
        if(!reg.test(tel)){
            alert('请填写正确的手机号码！');
            return false;
        }
        var data = {
            'name'  : name,
            'tel'   : tel
        };
        $.post('/xj/user/info/edit',data,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('信息修改成功！')
            location.reload();
        })
    }
</script>