<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>添加产品</title>
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
        <h3>添加产品</h3>

        <label for="name">产品名称：</label>
        <input type="text" id="name">
        <br>

        <label for="supply">供应商：</label>
        <select id="supply">
            <option value="-1">选择供应商</option>

            @foreach($supply as $item)
                <option value="{{$item->id}}">{{$item->supplier}}</option>
            @endforeach
        </select>
        <br>

        <label for="cate">类别：</label>
        <select id="cate">
            <option value="-1">选择类别</option>

            @foreach($category as $cate)
                <option value="{{$cate->id}}">{{$cate->category}}</option>
            @endforeach
        </select>
        <br>

        <label for="price">价格（¥）：</label>
        <input type="number" id="price">
        <br>

        <label for="makeup">有效成份：</label>
        <textarea  id="makeup" style="width: 600px; height: 200px;"></textarea>
        <br>
        <label for="function">功能主治：</label>
        <textarea id="function" style="width: 600px; height: 200px;"></textarea>
        <br>
        <label for="usage">用法用量：</label>
        <textarea id="usage" style="width: 600px; height: 200px;"></textarea>
        <br>
        <label for="spec">包装规格：</label>
        <textarea id="spec" style="width: 600px; height: 200px;"></textarea>
        <br>

        <button onclick="submit()">提交</button>
    </div>
</body>
</html>
<script>
    function submit(){
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
        if(_makeup == "" || _makeup == '<br>'){
            alert('有效成份不能为空！');
            return false;
        }
        var _function = nicEditors.findEditor('function').getContent();
        if(_function == "" || _function == '<br>'){
            alert('功能主治不能为空！');
            return false;
        }
        var _usage = nicEditors.findEditor('usage').getContent();
        if(_usage == "" || _usage == '<br>'){
            alert('用法用量不能为空！');
            return false;
        }
        var _spec = nicEditors.findEditor('spec').getContent();
        if(_spec == "" || _spec == '<br>'){
            alert('包装规格不能为空！');
            return false;
        }

        var data = {
            'name'      :   name,
            'supply'    :   supply,
            'cate'      :   cate,
            'price'     :   price,
            'makeup'    :   _makeup,
            'function'  :   _function,
            'usage'     :   _usage,
            'spec'      :   _spec
        };
        $.post('/xj/product/add',data,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('产品添加成功！');
            location.href = '/xj/product';
        })
    }
</script>