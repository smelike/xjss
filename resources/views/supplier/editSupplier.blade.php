<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>供应商编辑</title>
    <script src="/xj/public/js/jq12.min.js"></script>
    <script>
        $(function(){
            $.ajaxSetup({
                headers:{
                    'X-CSRF-TOKEN' : $('meta[name=csrf-token]').attr('content')
                }
            })
        })
    </script>
</head>
<body>
    <div align="center">
        <h3>供应商编辑</h3>

        <label for="supplier">供应商名称：</label>
        <input type="text" id="supplier" value="{{$supplier->supplier}}">

        <br>

        <label for="address">供应商地址：</label>
        <input type="text" id="address" value="{{$supplier->address}}">

        <br>

        <button onclick="update({{$supplier->id}})">更新</button>
    </div>
</body>
</html>
<script>
    function update(id){
        var name = $('#supplier').val();
        var addr = $('#address').val();

        if(name == "" || addr == ""){
            alert('供应商名称与地址不能为空！');
            return false;
        }
        var data = {
            'id'    : id,
            'name'  : name,
            'addr'  : addr
        };
        $.post('/xj/supplier/edit',data,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('供应商更新成功！');
            location.href = '/xj/supplier';
        })
    }
</script>