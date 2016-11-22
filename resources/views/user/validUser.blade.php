<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>正式用户列表</title>
    <script src="/xj/public/js/jq12.min.js"></script>
</head>
<body>
    <div align="center">
        <h3>正式用户列表</h3>
        <table border="1px">
            <tr>
                <th>序号</th>
                <th>用户名</th>
                <th>联系电话</th>
                <th>角色</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>

            @foreach($user as $k=>$item)
                <tr>
                    <td>{{$k + 1}}</td>
                    <td>{{$item->name}}</td>
                    <td>{{$item->telephone}}</td>
                    @if($item->role == 10)
                        <td>经理</td>
                    @elseif($item->role == 20)
                        <td>开票员</td>
                    @elseif($item->role == 30)
                        <td>业务员</td>
                    @elseif($item->role == 40)
                        <td>经销商</td>
                    @elseif($item->role == 50)
                        <td>客户</td>
                    @endif
                    <td>{{$item->created_at}}</td>
                    <td align="center">
                        @if($item->role == 10)
                            <button disabled>删除</button>
                        @else
                            <button class="del" value="{{$item->telephone}}" onclick="del(this.value)">删除</button>
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5">
                    {{--{{$user->links()}}--}}
                    {!! $user->render() !!}
                </td>
                <td>
                    正式用户数：{{$user->total()}}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
<script>
    function del(user){
        if(!confirm('确定将用户更改为删除状态？')) return false;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var data ={
            'user' : user
        };
        $.post('/xj/user/del',data,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('删除成功！');
            location.reload();
        });
    }
</script>