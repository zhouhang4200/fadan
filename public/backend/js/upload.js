function upload(obj,url,fn){
    var opts = {
        url: url,
        type: "POST",
        beforeSend: function () {

        },
        success: function (result, status, xhr) {

            if (result.status == "0") {
                alert(result.info);
                return false;
            }
            fn(result.info);

        },
        error: function (result, status, errorThrown) {
            alert('文件上传失败');
        }
    }
    obj.fileUpload(opts);
}

upload($('#thumb_upload'),"/admin/goods/hero-icon",function(info){
    $("input[name='add-hero_ico']").val(info);
    $("#img_show").attr('src', info);
});

upload($('#app_one_upload'),"/admin/goods/hero-skin",function(info){
    $("input[name='add-skin_pic']").val(info);
    $("#skin_img_show").attr('src',info);
});

upload($('#edit_thumb_upload'),"/admin/goods/hero-icon",function(info){
    $("input[name='edit-hero_ico']").val(info);
    $("#edit_img_show").attr('src',info);
});

upload($('#edit_app_one_upload'),"/admin/goods/hero-skin",function(info){
    $("input[name='edit-skin_pic']").val(info);
    $("#edit_skin_img_show").attr('src',info);
});


//文件上传
// var opts = {
//     url: "/upload",
//     type: "POST",
//     beforeSend: function () {
//
//     },
//     success: function (result, status, xhr) {
//
//         if (result.status == "0") {
//             alert(result.info);
//             return false;
//         }
//
//         $("input[name='add-hero_ico']").val(result.info);
//         $("#img_show").attr('src', result.info);
//
//     },
//     error: function (result, status, errorThrown) {
//
//         alert('文件上传失败');
//     }
// }
//
// //文件上传
// var opts_logo = {
//     url: "/app_upload",
//     type: "POST",
//     beforeSend: function () {
//
//     },
//     success: function (result, status, xhr) {
//         if (result.status == "0") {
//             alert(result.info);
//             return false;
//         }
//
//         $("input[name='add-skin_pic']").val(result.info);
//         $("#skin_img_show").attr('src', result.info);
//     },
//     error: function (result, status, errorThrown) {
//         alert('文件上传失败');
//     }
// }
//
//
// $('#thumb_upload').fileUpload(opts);
//
//
// $('#app_one_upload').fileUpload(opts_logo);

/*
$('#app_two_upload').fileUpload(opts_logo_two);

$('#app_three_upload').fileUpload(opts_logo_three);*/
