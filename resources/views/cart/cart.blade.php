<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>购物车</title>
    <script src="/xj/public/js/jq12.min.js"></script>
</head>
<body>


    @if($role == 30)
        <label for="type">客户类型：</label>
        <select id="type">
            <option value="50" selected>客户</option>
            <option value="40">经销商</option>
        </select>
        <br>
        <label for="customer">选择客户：</label>
        <select id="customer">
            <option value="-1">请选择</option>
            @foreach($customer as $item)
                <option value="{{$item->telephone}}">{{$item->name}}</option>
            @endforeach
        </select>
        <br>
    @endif
    <button onclick="getAddress()">添加收货地址</button>
<br>
</body>
</html>
<script>
    $('#type').change(function(){
        var type = $(this).val();
        $.get('/xj/product/customer/'+type,null,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            console.log(msg.customer);
            $('#customer').empty();
            $('#customer').append('<option value="-1">请选择</option>');

            $.each(msg.customer,function(i,item){
                var option = '<option value='+item.telephone+'>'+item.name+'</option>';
                $('#customer').append(option);
            })
        })
    });


    function getAddress(){
        var customer = $('#customer').find('option:selected').val();
        console.log(customer)
        if(customer == '-1'){
            alert('请选择目标客户！');
            return false
        }
    }
</script>