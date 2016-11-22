<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>编辑产品</title>
    <script src="/xj/public/js/jq12.min.js"></script>
    <script src="/xj/public/nicEditor/nicEdit.js"></script>
    <script>
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
        <h3>产品编辑</h3>

        <label for="name">产品名称：</label>
        <input type="text" id="name" value="{{$product->product}}">
        <br>

        <label for="supply">供应商：</label>
        <select id="supply">
            @foreach($supply as $item)
                @if($product->supplier == $item->id)
                    <option value="{{$item->id}}" selected="selected">{{$item->supplier}}</option>
                @else
                    <option value="{{$item->id}}" >{{$item->supplier}}</option>
                @endif
            @endforeach
        </select>
        <br>

        <label for="cate">类别：</label>
        <select id="cate">
            @foreach($category as $item)
                @if($product->category == $item->id)
                    <option value="{{$item->id}}" selected="selected">{{$item->category}}</option>
                @else
                    <option value="{{$item->id}}" >{{$item->category}}</option>
                @endif
            @endforeach
        </select>
        <br>

        <label for="price">价格：</label>
        <input type="text" id="price" value="{{$product->price}}">
        <br>

        <label for="makeup">有效成份：</label>
        <textarea id="makeup" style="width: 600px; height: 200px;">
            {{$product->component}}
        </textarea>
        <br>

        <label for="function">功能主治：</label>
        <textarea id="function" style="width: 600px; height: 200px;">
            {{$product->major_function}}
        </textarea>
        <br>

        <label for="usage">用法用量：</label>
        <textarea id="usage" style="width: 600px; height: 200px;">
            {{$product->usage}}
        </textarea>
        <br>

        <label for="spec">包装规格：</label>
        <textarea id="spec" style="width: 600px; height: 200px;">
            {{$product->specification}}
        </textarea>
        <br>

        <button onclick="edit({{$product->id}})">编辑修改</button>
    </div>
</body>
</html>
<script>
    function edit(id){
        var name = $('#name').val();
        if(name == ""){
            alert('产品名称不能为空！');
            return false;
        }

        var supply = $('#supply').find('option:selected').val();
        if(parseInt(supply) == -1){
            alert('请选择供应商！');
            return false;
        }

        var cate = $('#cate').find('option:selected').val();
        if(parseInt(cate) == -1){
            alert('请选择产品类别！');
            return false;
        }

        var price = $('#price').val();
        if(parseInt(price) < 0){
            alert('请输入正确价格！');
            return false;
        }

        var _makeup = nicEditors.findEditor('makeup').getContent();
        if(_makeup == ""){
            alert('有效成份不能为空！');
            return false;
        }
        var _function = nicEditors.findEditor('function').getContent();
        if(_function == ""){
            alert('功能主治不能为空！');
            return false;
        }
        var _usage = nicEditors.findEditor('usage').getContent();
        if(_usage == ""){
            alert('用法用量不能为空！');
            return false;
        }
        var _spec = nicEditors.findEditor('spec').getContent();
        if(_spec == ""){
            alert('包装规格不能为空！');
            return false;
        }
        var data = {
            'id'        :   id,
            'name'      :   name,
            'supply'    :   supply,
            'cate'      :   cate,
            'price'     :   price,
            'makeup'    :   _makeup,
            'function'  :   _function,
            'usage'     :   _usage,
            'spec'      :   _spec
        };
        $.post('/xj/product/edit',data,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false
            }
            alert('产品编辑修改成功！');
            location.href = '/xj/product';
        })
    }
</script>