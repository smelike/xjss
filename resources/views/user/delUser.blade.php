<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>删除状态用户</title>
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
        <h3>删除状态用户列表</h3>
        <table border="1px">
            <tr>
                <th>序号</th>
                <th>用户名</th>
                <th>联系电话</th>
                <th>角色</th>
                <th>删除时间</th>
                <th>操作</th>
            </tr>

            @foreach($user as $k=>$item)
                <tr>
                    <td>{{$k + 1}}</td>
                    <td>{{$item->name}}</td>
                    <td>{{$item->telephone}}</td>
                    @if($item->role == 20)
                        <td>开票员</td>
                    @elseif($item->role == 30)
                        <td>业务员</td>
                    @elseif($item->role == 40)
                        <td>经销商</td>
                    @elseif($item->role == 50)
                        <td>客户</td>
                    @else
                        <td>未知</td>
                    @endif
                    <td>{{$item->updated_at}}</td>
                    <td>
                        <button class="recovery" value="{{$item->telephone}}" onclick="recover(this.value)">恢复用户</button>
                        <button class="delete" value="{{$item->telephone}}" onclick="del(this.value)">彻底删除</button>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5">
                    {{$user->links()}}
                </td>
                <td>
                    删除状态用户数：{{$user->total()}}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
<script>
    function recover(user){
        var data ={
            'user' : user
        };
        $.post('/xj/user/del/recover',data,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('恢复成功！');
            location.reload();
        });
    }

    function del(user){
        if(!confirm('确定删除？')) return false;
        var data ={
            'user' : user
        };
        $.post('/xj/user/del/delete',data,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('删除成功！');
            location.reload();
        });
    }
</script>