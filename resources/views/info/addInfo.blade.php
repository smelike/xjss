<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>添加信息</title>
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
        <h3>添加通知消息</h3>

        <label for="title">标题：</label>
        <input type="text" id="title">

        <br>

        <label for="info">消息正文：</label>
        <textarea id="info" style="width: 600px; height: 200px;">

        </textarea>

        <button onclick="manage('save')">保存</button>
        <button onclick="manage('publish')">保存并发布</button>
    </div>
</body>
</html>
<script>
    function manage(action){
        var title = $('#title').val();
        var info = nicEditors.findEditor('info').getContent();
        if(title == "" || info == ""){
            alert('消息标题或正文不能为空！');
            return false;
        }
        var data = {
            'title':title,
            'info':info,
            'action':action
        };
        $.post('/xj/info/add',data,function(msg){
            if(msg.err_code != '0000'){
                alert(msg.err_msg);
                return false;
            }
            alert('信息创建成功！');
            location.href = '/xj/info/list';
        })
    }
</script>