<!DOCTYPE html>
<html lang="en">
<HEAD>
	<meta charset="UTF-8">
	<TITLE>404-您访问的页面去火星了！！！</TITLE>
	{{--<META http-equiv=X-UA-Compatible content=IE=EmulateIE7>--}}
	<!--<META http-equiv=refresh content=3;URL=http://icbc.com/icbc/homepage>-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="/xj/public/css/404.css" type="text/css">
	<script src="/xj/public/js/jq12.min.js"></script>
	<script>
		$(function(){
			var time=5;
			$('#timer').html(time);
			function remainTime(){
				time--;
				if(time < 0){
					history.go(-1);
					return false;
				}
				$('#timer').html(time);
				setTimeout(remainTime,1000);
			}
			setTimeout(remainTime,1000);
		})
	</script>
</HEAD>
<BODY>
	<DIV class=bg>
		<DIV class=cont>
			<DIV class=c1>
				<IMG class=img1 src="/xj/public/images/404/01.png">
			</DIV>
			<H2>Sorry...您访问的页面已删除或不存在！<span id="timer"></span>s后自动返回</H2>
			<DIV class=c3>
				<b>【温馨提示】</b>
				您可能输入了错误的网址，或者该网页已删除或移动！
			</DIV>
		</DIV>
	</DIV>
</BODY>
</HTML>

