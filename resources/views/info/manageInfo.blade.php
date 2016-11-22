<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>信息列表</title>
    <script src="/xj/public/js/jq12.min.js"></script>
</head>
<body>
    <div align="center">
        <h3>通知信息列表</h3>

        <table border="1px">
            <tr>
                <th>序号</th>
                <th>标题</th>
                <th>创建者</th>
                <th>状态</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
            @if(count($infos) == 0)
                <tr>
                    <td colspan="6">暂无信息通知</td>
                </tr>
            @else
                @foreach($infos as $k=> $info)
                    <tr>
                        <td>{{$k + 1}}</td>
                        <td>{{$info->title}}</td>
                        <td>{{$info->name}}</td>
                        <td>
                            @if($info->status == 0)
                                未发布
                            @else
                                已发布
                            @endif
                        </td>
                        <td>{{$info->created_at}}</td>
                        <td>
                            <button onclick="location.href='/xj/info/desc/{{$info->id}}'">编辑</button>
                            <button onclick="del('{{$info->id}}')">删除</button>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="5">
                        {!! $infos -> links() !!}
                    </td>
                    <td>
                        信息条目：{{$count}}
                    </td>
                </tr>
            @endif
        </table>
    </div>
</body>
</html>
<script>
    function del(id){
        if(!confirm('确定要删除此条信息？')) return false;

        $.get('/xj/info/del/'+id,null,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('消息删除成功！');
            location.href = '/xj/info/list';
        })
    }
</script>