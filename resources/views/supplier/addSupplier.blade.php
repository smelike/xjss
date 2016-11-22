<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>添加供应商</title>
    <script src="/xj/public/js/jq12.min.js"></script>
    <script>
        $(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
        })
    </script>
</head>
<body>
    <div align="center">
        <h3>添加供应商</h3>

        <label for="supplier">供应商名称：</label>
        <input type="text" id="supplier">
        <br>
        <label for="address">供应商地址：</label>
        <input type="text" id="address">

        <br>
        <button onclick="create()">添加</button>
    </div>
</body>
</html>
<script>
    function create(){
        var name = $('#supplier').val();
        var addr = $('#address').val();

        if(name == "" || addr == ""){
            alert('供应商名称与地址不能为空！');
            return false;
        }
        var data = {
            'name'  : name,
            'addr'  : addr
        };
        $.post('/xj/supplier/add',data,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('供应商添加成功！');
            location.href = '/xj/supplier';
        })
    }
</script>