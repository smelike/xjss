<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>产品分类编辑</title>
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
        <h3>分类编辑</h3>

        <p>分类名称：<input type="text" id="cate" value="{{$cate->category}}"></p>
        <p>创建日期：<input type="text" value="{{$cate->created_at}}" disabled></p>

        <button onclick="edit({{$cate->id}})">确认修改</button>
    </div>
</body>
</html>
<script>
    function edit(id){
        var data ={'id':id,'cate':$('#cate').val()};
        $.post('/xj/category/edit',data,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('修改成功！');
            location.href = '/xj/category';
        })
    }
</script>