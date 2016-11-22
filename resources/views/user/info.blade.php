<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>个人中心</title>
</head>
<body>
    <div align="center">
        <h2>个人中心</h2>

        用户名：<span>{{$user->name}}</span>    <br>
        电话：<span>{{$user->telephone}}</span> <br>
        角色：
            @if($user->role == 10)
            <span>经理</span>
            @elseif($user->role == 20)
                <span>开票员</span>
            @elseif($user->role == 30)
                <span>业务员</span>
            @elseif($user->role == 40)
                <span>经销商</span>
            @elseif($user->role == 50)
                <span>客户</span>
            @else
                <span>未知</span>
            @endif

            @if($user->is_admin == 1)
                <span>管理员</span>
            @endif
        <br>
        <a href="/xj/user/info/edit">修改信息</a>
    </div>
</body>
</html>