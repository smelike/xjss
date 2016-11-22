/**
 * Created by HSF on 2016/10/12.
 */
$(function(){

    var product;
    var supplier;
    var cate;
    var pageIndex = 1;  //当前页码
    //var total = 0;      //当前页记录数
    var pageCount=0;    //总页数
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#search').click(function(){
        product=$('#product').val();
        supplier = $('#supplier').val();
        cate=$('#cate').val();
        pageIndex = 1;  //初始化
        var data={
            'product'   :product,
            'supplier'  :supplier,
            'cate'      :cate
        };
        search(data);
    });

    function search(data){
        $.post('/xj/product/search',data,function(msg){
            InnerHtml(msg);

            $('#prev').click(function(){
                _pagePrev();
            });
            $('#next').click(function(){
                _pageNext();
            });

            $('.del').click(function(){
                if(!confirm('确定删除此产品？')) return false;
                var data ={
                    'id':$(this).val()
                };
                $.post('/xj/product/del',data,function(msg){
                    if(msg.err_code != '0000'){
                        alert(msg.err_msg);
                        return false;
                    }
                    alert('删除成功！');
                    location.reload()
                })
            })
        })
    }

    function InnerHtml(msg){
        //查询记录总数
        var countProduct = msg.countProduct;
        pageCount = msg.pageCount;
        $('table').html('');

        var str = "<tr><th>序号</th><th>产品名称</th> <th>供应商</th> <th>分类</th> <th>添加日期</th> <th>操作</th></tr>";

        $.each(msg.product,function(i,item){
            str += "<tr>"+
                        '<td>'+(i+1)+'</td>'+
                        '<td>'+item.product+'</td>'+
                        '<td>'+item.supplier+'</td>'+
                        '<td>'+item.category+'</td>'+
                        '<td>'+item.created_at+'</td>'+
                        '<td>' +
                            '<a href='+'/xj/product/edit/'+item.id+'>编辑</a>'+
                            '<button value='+item.id+' class=del>删除</button>'+
                        '</td>'+
                    '</tr>';
        });

        str += '<tr>'+
                '<td colspan="5" class="center">'+
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
                        '查询产品数：'+countProduct+
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
            'product'   :product,
            'supplier'  :supplier,
            'cate'      :cate,
            'pageIndex':pageIndex
        };
        search(data);
    }

    function _pageNext(){
        if(parseInt(pageIndex) >= pageCount){ return false;}
        ++pageIndex;
        var data={
            'product'   :product,
            'supplier'  :supplier,
            'cate'      :cate,
            'pageIndex':pageIndex
        };
        search(data)
    }

});