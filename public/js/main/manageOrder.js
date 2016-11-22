/**
 * Created by HSF on 2016/10/17.
 */
$(function(){

    var orderId;
    var state;
    var pageIndex = 1;  //当前页码
    //var total = 0;      //当前页记录数
    var pageCount=0;    //总页数
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#search').click(function(){
        orderId=$('#orderId').val();
        state = $('#state').val();
        pageIndex = 1;  //初始化
        var data={
            'id'   :orderId,
            'state':state
        };
        search(data);
    });

    function search(data){
        $.post('/xj/order/manage/search',data,function(msg){
            InnerHtml(msg);

            $('#prev').click(function(){
                _pagePrev();
            });
            $('#next').click(function(){
                _pageNext();
            });
        })
    }

    function InnerHtml(msg){
        //查询记录总数
        var searchOrder = msg.searchOrder;
        pageCount = msg.pageCount;
        $('table').html('');

        var str = "<tr><th>序号</th> <th>订单号</th> <th>订单金额（¥）</th> <th>下单用户</th> <th>目标客户</th> <th>订单状态</th> <th>操作</th></tr>";

        $.each(msg.order,function(i,item){
            str += "<tr>"+
                        '<td>'+(i+1)+'</td>'+
                        '<td>'+item.order_id+'</td>'+
                        '<td>'+item.price+'</td>'+
                        '<td>'+item.name+'</td>'+
                        '<td>'+item.target_customer+'</td>';

            if(item.status == 20){
                str += '<td>订单无效，待修改</td>';
            }else if(item.status == 30){
                str += '<td>订单已取消</td>';
            }else if(item.status == 40){
                str += '<td>审核通过，待付款</td>';
            }else if(item.status == 50){
                str += '<td>付款中</td>';
            }else if(item.status == 60){
                str += '<td>支付成功</td>';
            }else {
                str += '<td>异常状态</td>';
            }

            str += '<td>' +
                        '<a href=/xj/order/update/'+item.order_id+'>更新</a>'+
                    '</td>'+
                '</tr>';
        });

        str += '<tr>'+
                '<td colspan="6" class="center">'+
                    '<ul>';

        if (parseInt(pageIndex) <= 1){
            str +='<li class="disabled"><span id="prev">&laquo;</span></li>';
        }else{
            str +='<li><a href="#" id="prev">&laquo;</a></li>';
        }

        str += '<li>第'+pageIndex+'页</li>';

        if (parseInt(pageIndex) >= pageCount){
            str +='<li class="disabled"><span id="next">&raquo;</span></li>';
        }else {
            str +='<li class="abled"><a href="#" id="next">&raquo;</a></li>';
        }
        str +='</ul>'+
                '</td>'+
                '<td class="count">'+
                    '查询订单数：'+searchOrder+
                '</td>'+
            '</tr>';
        if (parseInt(pageIndex) <= 1){
            str +='<li class="disabled"><span id="prev" class="disabled">&laquo;</span></li>';
        }else{
            str +='<li class="abled"><a href="#" id="prev">&laquo;</a></li>';
        }
        $('table').append(str);

    }

    function _pagePrev(){
        if(parseInt(pageIndex) <= 1){ return false;}
        --pageIndex;
        var data={
            'id'  : orderId,
            'state' : state,
            'pageIndex': pageIndex
        };
        search(data);
    }

    function _pageNext(){
        if(parseInt(pageIndex) >= pageCount){ return false;}
        ++pageIndex;
        var data={
            'id'  : orderId,
            'state'  : state,
            'pageIndex': pageIndex
        };
        search(data)
    }

});