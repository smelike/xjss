<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>待审核订单</title>
    <script src="/xj/public/js/jq12.min.js"></script>
</head>
<body>
    <div align="center">
        <h3>待审核订单</h3>

        <p>订单号：{{$order->order_id}}</p>
        <p>订单金额：¥{{$order->price}}</p>
        <p>下单用户：{{$order->name}}</p>
        <p>
            目标客户：@if($order->target_customer == "")
                        <span>无</span>
                     @else
                        <span>{{$order->target_customer}}</span>
                     @endif
        </p>

        <label for="status">订单状态</label>
        <select id="status">
            <option value="10" selected>等待审核</option>
            @if($role == 30)
                <option value="20">订单无效，待修改</option>
            @else
                <option value="30">取消订单</option>
            @endif
                <option value="40">审核通过，待付款</option>
        </select>

        <div>
            <span>订单产品：</span>
            @foreach($product as $item)
                <div>
                    <span>{{$item->product}}</span>
                    <span>{{$item->qty}}</span>
                    <span>×</span>
                    <span>¥{{$item->price}}</span>
                </div>
            @endforeach
        </div>

        <p>物流地址：{{$order->address}}</p>

        <button onclick="check('{{$order->order_id}}')">确定</button>
    </div>
</body>
</html>
<script>
    function check(id){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var state = $('#status').val();
        if(parseInt(state) == 10){
            alert('请选择订单审核后的状态！');
            return false
        }
        var data = {
            'id'    : id,
            'state' : state
        };
        $.post('/xj/order/check', data , function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('订单审核成功！');
            location.href = '/xj/order/check';
        })
    }
</script>