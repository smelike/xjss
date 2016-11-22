<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>添加产品分类</title>
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
        <h3>添加分类</h3>

        <p>分类名称：<input type="text" id="cate"></p>

        <button id="ok">提交</button>
    </div>
</body>
</html>
<script>
    $('#ok').on('click',function(){
        var data = {'name':$('#cate').val()};
        $.post('/xj/category/add',data,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('添加分类成功！');
            location.href = '/xj/category';
        })
    })
</script>