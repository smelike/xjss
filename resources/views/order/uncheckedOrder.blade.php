<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>待审核订单</title>
</head>
<body>
    <div align="center">
        <h3>等待审核订单列表</h3>

        <table border="1px">
            <tr>
                <th>序号</th>
                <th>订单号</th>
                <th>下单用户</th>
                <th>目标客户</th>
                <th>订单状态</th>
                <th>操作</th>
            </tr>

            @foreach($order as $k=>$item)
                <tr>
                    <td>{{$k + 1}}</td>
                    <td>{{$item->order_id}}</td>
                    <td>{{$item->name}}</td>
                    @if($item->target == "")
                        <td>无</td>
                    @else
                        <td>{{$item->target}}</td>
                    @endif
                    <td>等待审核</td>
                    <td>
                        <a href="/xj/order/check/{{$item->order_id}}">审核</a>
                    </td>
                </tr>
            @endforeach

            <tr>
                <td colspan="5">
                    {!! $order->links() !!}
                </td>
                <td>
                    待审核订单：{{$count}}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>