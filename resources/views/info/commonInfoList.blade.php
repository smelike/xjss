<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>消息通知</title>
</head>
<body>
    <div align="center">
        <h3>通知消息列表</h3>

        <ul>
            @foreach($infos as $info)
            <li onclick="location.href = '/xj/info/desc/{{$info->id}}'">
                <span>*<i>{{$info->title}}</i></span> &nbsp;
                <span>{{$info->created_at}}</span>
            </li>
            @endforeach

            @if(count($infos) > 10)
                <li>{!! $infos->links() !!}</li>
            @endif
        </ul>
    </div>
</body>
</html>