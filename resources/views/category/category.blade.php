<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>分类管理</title>
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
        <h3>产品分类列表</h3>
    </div>
    <div align="center">
        <a href="/xj/category/add">添加分类</a>
    </div>
    <div align="center">
        <table border="1px">
            <tr>
                <th>序号</th>
                <th>分类名称</th>
                <th>操作</th>
            </tr>

            @foreach($cate  as $k=>$item)
                <tr>
                    <td>{{$k + 1}}</td>
                    <td>{{$item->category}}</td>
                    <td>
                        <a href="/xj/category/edit/{{$item->id}}">编辑</a>
                        <button onclick="del({{$item->id}})">删除</button>
                    </td>
                </tr>
            @endforeach

            <tr>
                <td colspan="2">
                    {!! $cate->links() !!}
                </td>
                <td>
                    分类条目数：{{$count}}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
<script>
    function del(id){
        if(!confirm('确定要删除此分类？')) return false;
        var data ={'id':id};
        $.post('/xj/category/del',data,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('删除成功！');
            location.reload();
        })
    }
</script>