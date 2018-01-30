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
            if (result.status == "1") {
                layer.msg('上传成功请导入')
            }
            fn(result.info);

        },
        error: function (result, status, errorThrown) {
            alert('文件上传失败');
        }
    };
    obj.fileUpload(opts);
}

upload($('#file'),"/file/qrCode",function(info){
    $("#pho").attr('src', info);
});

upload($('#file-excel'),"/file/fileExcel",function(info){
    $('input[name="fileExcel"]').val(info);
});