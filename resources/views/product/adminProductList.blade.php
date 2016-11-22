<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>产品列表</title>
    <script src="/xj/public/js/jq12.min.js"></script>
    <script src="/xj/public/js/main/adminProduct.js"></script>
</head>
<body>
    <div align="center">
        <h3>产品列表</h3>
    </div>
    <div align="center">
        产品名称：<input type="text" id="product">
        <label for="supplier">供应商：</label>
        <select id="supplier">
            <option value="-1">选择供应商</option>

            @foreach($supply as $item)
                <option value="{{$item->id}}">{{$item->supplier}}</option>
            @endforeach
        </select>

        <label for="cate">类别：</label>
        <select id="cate">
            <option value="-1">选择类别</option>

            @foreach($category as $cate)
                <option value="{{$cate->id}}">{{$cate->category}}</option>
            @endforeach
        </select>

        <button id="search">搜索</button>
    </div>
    <div align="center">
        <table border="1px">
            <tr>
                <th>序号</th>
                <th>产品名称</th>
                <th>供应商</th>
                <th>分类</th>
                <th>添加日期</th>
                <th>操作</th>
            </tr>

            @foreach($product as $k=>$item)
                <tr>
                    <td>{{$k + 1}}</td>
                    <td>{{$item->product}}</td>
                    <td>{{$item->supplier}}</td>
                    <td>{{$item->category}}</td>
                    <td>{{$item->created_at}}</td>
                    <td>
                        <a href="/xj/product/edit/{{$item->id}}">编辑</a>
                        <button onclick="del({{$item->id}})">删除</button>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5">
                    {!! $product->links() !!}
                </td>
                <td>
                    产品总数：{{$product->total()}}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
<script>
    function del(id){
        if(!confirm('确定删除此产品？')) return false;
        var data ={
            'id':id
        };
        $.post('/xj/product/del',data,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('删除成功！');
            location.reload()
        })
    }
</script>