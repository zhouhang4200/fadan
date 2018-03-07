<script src="/vendor/layui/layui.js"></script>
<script src="/js/jquery-1.11.0.min.js"></script>
<script src="/frontend/js/helper.js"></script>
<script src="//cdn.bootcss.com/socket.io/1.3.7/socket.io.min.js"></script>
<script>

$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')}});

var socket = io('{{ env('SOCKET_SERVER') }}');

layui.use(['form', 'layedit', 'laydate', 'element'], function(){
    var form = layui.form, layer = layui.layer, element = layui.element;

    //监听导航点击
    element.on('nav(demo)', function(elem){
        var current = elem.text();

        if (current == '注销登录') {
            layer.confirm('确定退出吗?', {icon: 3, title:'提示'}, function(index){
                $.post('/logout', {}, function(str){
                    window.location.href='/login';
                });
                layer.close(index);
            });
        }
        if (current == '在线') {
            setStatus(1);
            $('.current-status').html('在线<span class="layui-nav-more"></span>');
        }
        if (current == '挂起') {
            setStatus(2);
            $('.current-status').html('挂起<span class="layui-nav-more"></span>');
        }
        // 设置账号状态
        function setStatus(status) {
            $.post('{{ route('frontend.workbench.set-status') }}', {status:status}, function () {

            }, 'json');
        }
    });
});

</script>
