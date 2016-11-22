<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Title</title>
    <script src="/xj/public/js/jq12.min.js"></script>
    <script>
        $(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        })
    </script>
</head>
<body>
    <div align="center">
        <h3>管理员列表</h3>

        <table border="1px">
            <tr>
                <th>序号</th>
                <th>用户名</th>
                <th>角色</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            @foreach($user as $k=>$item)
                <tr>
                    <td>{{$k + 1}}</td>
                    <td>{{$item->name}}</td>
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
                    <td>管理员</td>
                    <td>
                        @if($item->role == 10)
                            <button disabled>撤销管理员</button>
                        @else
                            <button onclick="unsetAdmin({{$item->id}})">撤销管理员</button>
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4">
                    {!! $user->links() !!}
                </td>
                <td>
                    用户数：{{$user->total()}}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
<script>
    function unsetAdmin(id){
        if(!confirm('确定撤销此管理员身份？')) return false;
        var data = {'id':id};
        $.post('/xj/manage/unset/admin',data,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('管理员撤销成功！');
            location.reload();
        })
    }
</script>