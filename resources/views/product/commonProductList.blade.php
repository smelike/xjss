<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>产品列表</title>
    <script src="/xj/public/js/jq12.min.js"></script>
</head>
<body>
    <div align="center">
        <h3>产品列表</h3>

        <ul>
            @foreach($product as $item)
                <li value="{{$item->id}}" onclick="location.href = '/xj/product/desc/'+this.value">
                    产品名称：{{$item->product}} <br>
                    单价：{{$item->price}}
                </li>
            @endforeach
            <li>
                {!! $product->links() !!}
            </li>
        </ul>
    </div>
</body>
</html>