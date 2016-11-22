<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>订单详情</title>
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
        <h3>订单详情</h3>

        <p>订单号：<span id="orderId">{{$order->order_id}}</span></p>
        <p>订单金额：¥{{$order->price}}</p>
        @if($role == 30)
            <p>
                <span>{{$order->target_customer}}</span>
            </p>
        @endif

        <span>
            订单状态：@if($order->status == 10)
                        等待审核
                    @elseif($order->status == 20)
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
        </span>

        <div>
            <span>订单产品：</span>
            @foreach($product as $item)
                <div>
                    <span>{{$item->product}}:</span>
                    <input type="hidden" class="pId" VALUE="{{$item->pId}}">
                    <input class="qty" type="number" value="{{$item->qty}}">
                    <span>×</span>
                    <span>¥{{$item->price}}</span>
                </div>
            @endforeach
        </div>

        <p>物流地址：{{$order->address}}</p>

        <label for="payment">支付备注：</label>
            <textarea id="payment" style="width: 600px; height: 200px;">
                {{$order->pay_comment}}
            </textarea>

        <label for="shipping" class=" fds ">物流备注：</label>
            <textarea id="shipping" style="width: 600px; height: 200px;">
                {{$order->shipping}}
            </textarea>

        <div>
            @if($role == 30 && $order->status == 20)
                <button onclick="edit('{{$order->order_id}}')">修改订单</button>
            @endif

            <button onclick="cancel('{{$order->order_id}}')">取消订单</button>

            @if($role != 30 && $order->status == 40)
                <button onclick="toPay('{{$order->order_id}}')">前往支付</button>
            @endif
        </div>
    </div>
</body>
</html>
<script>
    function edit(id){
        var len = $('.pId').length;
        var product = [];
        for (var i = 0; i<len ; i++){

            var pid = $('.pId').eq(i).val();
            var qty = $('.qty').eq(i).val();

            if(parseInt(qty) < 0){
                alert('商品数量不能小于0件！');
                return false;
            }
            var json = {};
            json.pid = pid;
            json.qty = qty;

            product[i] = json
        }
        var data = {
            'id':id,
            'product':product
        };
        $.post('/xj/order/edit',data,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('订单修改成功！');
            location.href = '/xj/order/list';
        });
    }

    function cancel(id){
        var data = {'id':id};
        $.post('/xj/order/cancel',data,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('订单取消成功！');
            location.href = '/xj/order/list';
        })
    }

    function toPay(id){}
</script>