<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>供应商列表</title>
    <script src="/xj/public/js/jq12.min.js"></script>
</head>
<body>
    <div align="center">
        <h3>供应商管理</h3>
        <button onclick="location.href = '/xj/supplier/add'">添加供应商</button>
        <table border="1px">

            <tr>
                <th>序号</th>
                <th>供应商名称</th>
                <th>供应商地址</th>
                <th>操作</th>
            </tr>

            @foreach($supplier as $k => $v)
                <tr>
                    <td>{{$k + 1}}</td>
                    <td>{{$v->supplier}}</td>
                    <td>{{$v->address}}</td>
                    <td>
                        <button onclick="location.href = '/xj/supplier/edit/{{$v->id}}'">编辑</button>
                        <button onclick="del({{$v->id}})">删除</button>
                    </td>
                </tr>
            @endforeach

            <tr>
                <td colspan="3">
                    {!! $supplier->links() !!}
                </td>
                <td>
                    供应商数量：{{$count}}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
<script>
    function del(id){
        if(!confirm('确定要删除此供应商？')) return false;

        $.get('/xj/supplier/del/'+id,null,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false
            }
            alert('供应商删除成功！');
            location.reload()
        })
    }
</script>