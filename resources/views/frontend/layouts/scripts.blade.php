<script src="/vendor/layui/layui.js"></script>
<script src="/js/jquery-1.11.0.min.js"></script>
<script>
$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')}});
$('#main-title').text($('title').text());

function logout() {
    layui.use(['form', 'layedit', 'laydate',], function(){
        var form = layui.form
        ,layer = layui.layer;
        layer.confirm('确定退出吗?', {icon: 3, title:'提示'}, function(index){
            $.post('/logout', {}, function(str){
                window.location.href='/login';
            });
            layer.close(index);
        });
    });
}
</script>