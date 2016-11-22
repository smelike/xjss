<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>订单列表</title>
</head>
<body>
    <div align="center">
        <h3>订单列表</h3>
        <ul>
            @foreach($order as $item)
                <li onclick="desc('{{$item->order_id}}')">
                    <div>
                        <span>订单号：{{$item->order_id}}</span>
                        <span>
                            订单状态：@if($item->status == 10)
                                        等待审核
                                    @elseif($item->status == 20)
                                        订单无效，待修改
                                    @elseif($item->status == 30)
                                        订单已取消
                                    @elseif($item->status == 40)
                                        审核通过，待付款
                                    @elseif($item->status == 50)
                                        付款中
                                    @elseif($item->status == 60)
                                        支付成功
                                    @else
                                        异常状态
                                    @endif
                        </span>
                        <span>订单总价（¥）：{{$item->price}}</span>
                        <span>创建日期：{{$item->created_at}}</span>
                    </div>
                </li>
            @endforeach

            <li>
                {!! $order->links() !!}
            </li>
        </ul>
    </div>
</body>
</html>
<script>
    function desc(id){
        location.href = '/xj/order/list/desc/'+id;
    }
</script>