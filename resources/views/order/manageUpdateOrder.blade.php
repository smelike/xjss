<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>更新订单</title>
    <script src="/xj/public/js/jq12.min.js"></script>
    <script src="/xj/public/nicEditor/nicEdit.js"></script>
    <script type="text/javascript">
        $(function(){
            bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });

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
        <h3>更新订单信息</h3>

        <p>订单号：{{$order->order_id}}</p>

        <label for="state">订单状态：</label>
        <select id="state">
            @if($order->status == 50)
                <option value="50" selected>付款中</option>
                <option value="60">支付成功</option>
            @elseif($order->status == 60)
                <option value="50">付款中</option>
                <option value="60" selected>支付成功</option>
            @else
                <option value="{{$order->status}}" selected>
                    @if($order->status == 20)
                        订单无效，待修改
                    @elseif($order->status == 30)
                        订单已取消
                    @elseif($order->status == 40)
                        审核通过，待付款
                    @elseif($order->status == 50)
                        付款中
                    @elseif($order->status == 60)
                        支付成功
                    @else
                        异常状态
                    @endif
                </option>
                <option value="50">付款中</option>
                <option value="60">支付成功</option>
            @endif
        </select>
        <br>
        <label for="payment">支付备注：</label>
        <textarea name="payment" id="payment" style="width: 600px; height: 200px;">
	        {{$order->pay_comment}}
        </textarea>

        <label for="shipping">物流备注：</label>
        <textarea name="shipping" id="shipping" style="width: 600px; height: 200px;">
	        {{$order->shipping}}
        </textarea>

        <button onclick="update('{{$order->order_id}}')">更新订单</button>
    </div>
</body>
</html>
<script>
    function update(id){
        var state = $('#state').val();

        var payment = nicEditors.findEditor('payment').getContent();
        var shipping = nicEditors.findEditor('shipping').getContent();

        console.log(payment);
        let data = {
            'id'    : id,
            'state' : state,
            'payment':payment,
            'shipping':shipping
        };
        $.post('/xj/order/update',data,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('订单更新成功！');
            location.href = '/xj/order/manage/list';
        })
    }
</script>