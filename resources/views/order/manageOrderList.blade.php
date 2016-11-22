<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>订单列表</title>
    <script src="/xj/public/js/jq12.min.js"></script>
    <script src="/xj/public/js/main/manageOrder.js"></script>
</head>
<body>
    <div align="center">
        <h3>订单列表</h3>
        <div>
            <span>订单号：</span><input type="text" id="orderId">
            <label for="state">订单状态：</label>
            <select id="state">
                <option value="-1">请选择</option>

                @foreach($status as $k=>$state)
                    <option value="{{$k}}">{{$state}}</option>
                @endforeach
            </select>

            <button id="search">查询</button>
        </div>

        <div>
            <table border="1px">
                <tr>
                    <th>序号</th>
                    <th>订单号</th>
                    <th>订单金额（¥）</th>
                    <th>下单用户</th>
                    <th>目标客户</th>
                    <th>订单状态</th>
                    {{--<th>支付备注</th>--}}
                    {{--<th>物流备注</th>--}}
                    <th>操作</th>
                </tr>

                @foreach($order as $k=>$item)
                    <tr>
                        <td>{{$k + 1}}</td>
                        <td>{{$item->order_id}}</td>
                        <td>{{$item->price}}</td>
                        <td>{{$item->name}}</td>
                        @if($item->target_customer == "")
                            <td>无</td>
                        @else
                            <td>{{$item->target_customer}}</td>
                        @endif

                        @if($item->status == 20)
                            <td>订单无效，待修改</td>
                        @elseif($item->status == 30)
                            <td>订单已取消</td>
                        @elseif($item->status == 40)
                            <td>审核通过，待付款</td>
                        @elseif($item->status == 50)
                            <td>付款中</td>
                        @elseif($item->status == 60)
                            <td>支付成功</td>
                        @else
                            <td>异常状态</td>
                        @endif

                        {{--@if($item->pay_comment == "")--}}
                            {{--<td>无</td>--}}
                        {{--@else--}}
                            {{--<td>{{substr($item->pay_comment,0,10)}}...</td>--}}
                        {{--@endif--}}

                        {{--@if($item->shipping == "")--}}
                            {{--<td>无</td>--}}
                        {{--@else--}}
                            {{--<td>{{substr($item->shipping,0,10)}}...</td>--}}
                        {{--@endif--}}

                        <td>
                            <a href="/xj/order/update/{{$item->order_id}}">更新</a>
                        </td>
                    </tr>
                @endforeach

                <tr>
                    <td colspan="6">
                        {!! $order->links() !!}
                    </td>
                    <td>
                        订单数：{{$order->total()}}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>