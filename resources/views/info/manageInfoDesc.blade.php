<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>信息管理</title>
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
    {{-- 普通用户查看 --}}
    @if($privilege > 20)
        <div align="center">
            <h3>{{$info->title}}</h3>

            <div>
                <?php echo $info->info;?>
            </div>
        </div>

    @else
        <div align="center">
            <h3>信息管理</h3>

            <label for="title">标题：</label>
            <input id="title" type="text" value="{{$info->title}}">
            <br>
            <label for="info">消息正文：</label>
        <textarea id="info" style="width: 600px; height: 200px;">
            {{$info->info}}
        </textarea>
            <br>

            @if($info->status == 0)
                <button onclick="manage('{{$info->id}}','update')">更新消息</button>
            @else
                <button onclick="cancel('{{$info->id}}')">取消发布</button>
            @endif

            <button onclick="manage('{{$info->id}}','publish')">更新并发布</button>
        </div>
    @endif
</body>
</html>
<script>
    function manage(id,action){
        var title = $('#title').val();
        var info  = nicEditors.findEditor('info').getContent();

        if(title == "" || info == ""){
            alert('消息标题或正文不能为空！');
            return false;
        }
        var data = {
            'id':id,
            'title':title,
            'info':info,
            'action':action
        };
        $.post('/xj/info/manage',data,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('操作成功！');
            location.href = '/xj/info/list';
        })
    }

    function cancel(id){
        $.get('/xj/info/cancel/'+id,null,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('此条信息已取消发布！');
            location.href = '/xj/info/list';
        })
    }
</script>