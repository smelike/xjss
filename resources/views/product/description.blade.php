<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>产品详情</title>
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
        <h3>{{$product->product}}</h3>
        <p>单价：{{$product->price}}¥</p>
        <p>供应商：{{$product->supplier}}</p>
        <p>产品类型：{{$product->category}}</p>
        <div>
            <span>有效成分：</span>
            <?php echo htmlspecialchars_decode($product->component);?>
        </div>
        <div>
            <span>功能主治：</span>
            <?php echo htmlspecialchars_decode($product->major_function);?>
        </div>
        <div>
            <span>用法用量：</span>
            <?php echo htmlspecialchars_decode($product->usage);?>
        </div>
        <div>
            <span>包装规格：</span>
            <?php echo htmlspecialchars_decode($product->specification);?>
        </div>

        <label for="qty">数量：</label>
        <input id="qty" type="text" value="1">
        <br>

        <button onclick="addCart({{$product->id}})">加入购物车</button>
    </div>
</body>
</html>
<script>
    function addCart(id){
        var qty = $('#qty').val();
        var reg = /^\d+$/g;
        if(!reg.test(qty)){
            alert('产品数量错误！');
            return false;
        }
        var data ={
            'id'    : id,
            'qty'   : qty
        };
        $.post('/xj/cart/add',data,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('产品添加购物车成功！');
            location.href = '/xj/product';
        })
    }
</script>