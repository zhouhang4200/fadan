var orderHub = {
    count:0,
    timer:[],
    addData:function(data){ //新的订单
        var _this=this;
        _this.count++;
        var temp=$('<li class="fr prom-list relative animated fadeInRight" data-id='+data.orderId+'>'+
            '<div class="text_center prom-list-header relative">集市新订单 <span class="absolute close-prom">关闭通知</span></div>'+
            '<div class="prom-list-body">'+
            '<p>订单号：'+ data.orderId +'<br>'+
            '游戏:'+ data.gameName +
            '</p>'+
            '<div class="overflow prom-list-contents" style="margin-top:15px">'+
            '<span class="fl">商品：'+ data.goods+ '</span>'+
            '<span class="fr">价格：<span style="color:red">'+ data.price +'</span>元</span>'+
            '</div>'+
            '</div>'+
            '<div class="prom-list-footer overflow text_center">'+
            '<div class="prom-list-footer-tab get get-order fl"  data-count="'+_this.count+'" data-remarks="'+data.remarks+'">'+
            '接单(<span style="color:red" class="time" id="count'+_this.count+'">5</span>)'+
            '</div>'+
            '<div class="prom-list-footer-tab ignore fl" data-count="'+_this.count+'">'+
            '忽略'+
            '</div>'+
            '</div>'+
            '</li>');
        temp.prependTo('.prom-inner');
        if($('.prom-list').length>4){
            $('.prom-list').eq(0).remove();
        }
        _this.setCount(_this.count);
    },
    setCount:function(count){
        var _this=this;
        var time=$('#count'+count).text();
        _this.timer[count]=setInterval(function(){
            time--;
            $('#count'+count).text(time);
            if(time==0){
                $('#count'+count).parents('.prom-list').remove();
                clearInterval(_this.timer[count]);
            }
        },1000)
    },
    ignoreOrder:function(self,count){
        var _this=this;
        clearInterval(_this.timer[count]);
        $(self).parents('.prom-list').remove();
    },
    getOrder:function(self,count){
        var _this=this;
        var orderId = $(self).parents('.prom-list').attr('data-id');

        clearInterval(_this.timer[count]);
        $(self).parents('.prom-list').remove();
    }
};

$('.prom-wrap').on('click','.ignore',function(){ //忽略订单
    var _this = $(this);
    var count = _this.attr('data-count');
    orderHub.ignoreOrder(_this,count);
});

// 关闭通知
// $('.prom-wrap').on('click','.close-prom',function(){ //忽略订单
//     $.post(marketNoticeSetUrl, {status:0}, function (result) {
//         layer.msg(result.message);
//         setTimeout(function(){
//             window.location.reload();
//         }, 1000);
//     }, 'json');
// });