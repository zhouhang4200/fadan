// 初始化Web Uploader
var uploader = WebUploader.create({
	// 选完文件后，是否自动上传。
	auto: false,
	// swf文件路径
	swf: '/js/Uploader.swf',
	// 文件接收服务端。
	server: '/admin/goods/game-ico',
	// 选择文件的按钮。可选。
	// 内部根据当前运行是创建，可能是input元素，也可能是flash.
	pick: '#filePicker',
	// 只允许选择图片文件。
	accept: {
		title: 'Images',
		extensions: 'gif,jpg,jpeg,bmp,png',
		mimeTypes: 'image/jpg,image/jpeg,image/png'
	},
	formData: {
		"_token": token
	}
});
// 当有文件添加进来的时候
uploader.on( 'fileQueued', function( file ) {
	// 创建缩略图
	uploader.makeThumb(file, function (error, ret) {
		if (error) {
			alert('预览错误');
		} else {
			$('<div style="width:150px;height:150px;background-image: url(' + ret + ');background-size: 100%"></div>').appendTo('#game-icon');
		}

	}, 1, 1);
});
// 文件上传成功，给item添加成功class, 用样式标记上传成功。
uploader.on( 'uploadSuccess', function( file ) {
	$('#img-box').html('upload-state-done');
});

// 文件上传失败，显示上传出错。
uploader.on( 'uploadError', function( file ) {
	var $li = $( '#'+file.id ),
		$error = $li.find('div.error');

	// 避免重复创建
	if ( !$error.length ) {
		$error = $('<div class="error"></div>').appendTo( $li );
	}

	$error.text('上传失败');
});

// 完成上传完了，成功或者失败，先删除进度条。
uploader.on( 'uploadComplete', function( file ) {
	$( '#'+file.id ).find('.progress').remove();
});

// 多区服
var manyAreaClothingHtml = '<div class="server">';
manyAreaClothingHtml += '<div class="col-lg-5"><div class="form-group"><input type="text" class="form-control" placeholder="区 示例: 电信一区" name="partition"></div></div>';
manyAreaClothingHtml += '<div class="col-lg-5"><div class="form-group"><input type="text" class="form-control" placeholder="服 示例: 1服|2服|3服" name="points_suit"></div></div>';
manyAreaClothingHtml += '<div class="col-lg-2"><div class="form-group"><button type="button" class="btn btn-danger" style="display: block;">删除</button></div></div>';
manyAreaClothingHtml += '</div>';

// 商品
var shopTableHtml = '<tr>';
shopTableHtml += '<td><input type="text"  class="form-control" name="sort"></td>';
shopTableHtml += '<td>';
shopTableHtml += '<input type="hidden" class="form-control" name="goods_id" value="0">';
shopTableHtml += '<input type="hidden" class="form-control" name="price_id">';
shopTableHtml += '<input type="text" readonly class="form-control" name="price_name" placeholder="名称">';
shopTableHtml += '</td>';
shopTableHtml += '<td width="5%"><input type="text" readonly class="form-control" name="price" placeholder="价格面值" style="width: 68px"></td>';
shopTableHtml += '<td><input type="text" class="form-control" name="name" placeholder="商品名称"></td>';
shopTableHtml += '<td width="5%"><input type="text" class="form-control" name="external_id" placeholder="商品ID" style="width: 60px;"></td>';
shopTableHtml += '<td width="6%"><input type="text" class="form-control" name="kamen_goods_id" placeholder="卡门商品ID" style="width: 65px;"></td>';
shopTableHtml += '<td><input type="text" class="form-control" name="product_id" placeholder="商品唯一标识" ></td>';
shopTableHtml += '<td width="5%"><select class="form-control"  name="status" style="width:50px;">';
shopTableHtml += '<option value="0">下架</option>';
shopTableHtml += '<option value="1" selected>上架</option></select></td>';
shopTableHtml += '<td><a href="javascript:void(0)" class="btn pull-left remove-tr"><i class="glyphicon glyphicon-remove"></i></a></td>';
shopTableHtml += '</tr>';

// 新增版本
var version = $('.version').clone();

$('.version-box').on('click', '.change-server-type', function(event) {
	event.preventDefault();
	var thisObj = $(this);
	var parentObj = thisObj.closest('.server');
	var input = parentObj.find('input[name="zone_type"]');
	// 单区服
	var singleZoneSuit = parentObj.find('.single-zone-suit');
	// 多区服
	var manyAreaClothing = parentObj.find('.many-area-clothing');
	// 添加多区服
	var addServer = parentObj.find('.add-server');
	if(input.val() == '2'){
		// 变为单区服
		singleZoneSuit.removeClass('hidden');
		manyAreaClothing.addClass('hidden');
		addServer.addClass('hidden');
		input.val(1);
		thisObj.text('变更为多区服');
	}else if(input.val() == '1'){
		// 变为多区服
		singleZoneSuit.addClass('hidden');
		manyAreaClothing.removeClass('hidden');
		addServer.removeClass('hidden');
		input.val(2);
		thisObj.text('变更为单区服');
	}
});

// 区服添加
$('.version-box').on('click', '.add-server', function(event) {
	event.preventDefault();
	$(this).parents().parents().parents().find('.many-area-clothing').append(manyAreaClothingHtml);
});

// 是否有区服
$('.version-box').on('change', 'input[name="no_zone"]', function(event) {
	event.preventDefault();
	if($(this).is(':checked')){
		$(this).parents().next('.server').addClass('hidden');
		$(this).val(1);
	}else{
		$(this).val("");
		$(this).parents().next('.server').removeClass('hidden');
	}
});

// 添加商品
$('.version-box').on('click', '.add-goods-modal', function(event) {
	event.preventDefault();
	addPriceTbody = $(this).prev('table').find('tbody');
});

// 弹层添加商品
$('#add-goods-modal').on('click', '.add-goods', function(event) {
	event.preventDefault();
	// 商品名称
	var _goods_name = $('input[name="add-goods-name"]').val();

	if(_goods_name == ''){
		layer.alert('商品名称不能为空!', {icon: 2});
		return;
	}

	// 价格 id
	var _selectPrice = $("#add-goods-price").select2("val");
	if(_selectPrice == ''){
		layer.alert('请选择价格', {icon:2});
		return;
	}

	var priceArr = _selectPrice.split("|");

	var _clone = $(shopTableHtml).clone();

	// id
	_clone.find('input[name="price_id"]').val(priceArr[0]);
	// 价格名称
	_clone.find('input[name="price_name"]').val(priceArr[1]);
	// 价格 面值
	_clone.find('input[name="price"]').val(priceArr[2]);
	// 商品名称
	_clone.find('input[name="name"]').val(_goods_name);



	// 是否是皮肤
	if(gameType == '2'){
		// 英雄名称
		var _skin_name = $('input[name="add-skin-name"]').val();
		var _hero_name = $('input[name="add-hero-name"]').val();
		var _hero_ico = $('input[name="add-hero_ico"]').val();
		var _skin_pic = $('input[name="add-skin_pic"]').val();


		console.log(_hero_ico);
		console.log(_skin_pic);
		// console.log();
		if(_skin_name == ''){
			layer.alert('皮肤名称不能为空!', {icon: 2});
			return;
		}
		if(_hero_name == ''){
			layer.alert('英雄名称不能为空!', {icon: 2});
			return;
		}

		if(_hero_ico == ''){
			layer.alert('英雄图标不能为空!', {icon: 2});
			return;
		}
		if(_skin_pic == ''){
			layer.alert('英雄皮肤不能为空!', {icon: 2});
			return;
		}

		_clone.find('input[name="skin_name"]').val(_skin_name);
		_clone.find('input[name="hero_name"]').val(_hero_name);
		_clone.find('input[name="hero_ico"]').val(_hero_ico);
		_clone.find('input[name="skin_pic"]').val(_skin_pic);

		// 英雄类型
		var _skinTypeId = $("#select-need").select2("val");
		if(_skinTypeId == ''){
			layer.alert('请选择英雄类型', {icon:2});
			return;
		}
		var skin_type_option = '';
		$.each(gameTypeArr, function(index, val) {
			skin_type_option += '<option value="'+index+'"';
			if(_skinTypeId == index)
				skin_type_option += ' selected ';
			skin_type_option += '>'+val+'</option>';
		});
		// console.log(skin_type_option);
		_clone.find('select[name="skin_type_id"]').html(skin_type_option);
		// $('.add-goods-price,.select-need').select2();
	}
	// 是否是皮肤 end

	addPriceTbody.append(_clone);
	return false;
});

// 删除行
$('.version-box').on('click', '.remove-tr', function(event) {
	event.preventDefault();
	var _this = $(this);
	layer.confirm('删除后无法恢复!', {icon: 3, title:'确定删除'}, function(index){
		_this.closest('tr').remove();
		layer.close(index);
	});
});

// 新增版本
$('.add-version').on('click', function(event) {
	event.preventDefault();
	var _clone = $(version[0]).clone();
	_clone.find('.version-delete').removeClass('hidden');
	_clone.find('input[name="remark"]').val('');
	_clone.find('input[name="version_id"]').val('');
	_clone.find('.goods-box tbody').html('');
	_clone.find('input[name="single_zone_partition"]').val('');
	_clone.find('.many-area-clothing tbody').html(manyAreaClothingHtml);
	_clone.find('select[name="version_system"]').find('option[value="0"]').attr('selected', true);
	_clone.find('select[name="version_login"]').find('option[value="0"]').attr('selected', true);
	$('.version-box').append(_clone);
});

// 复制新增
$('.version-box').on('click', '.version-copy', function(event) {
	event.preventDefault();
	var _clone = $(this).closest('.version').clone();
	_clone.find('.version-delete').removeClass('hidden');
	_clone.find('.remove-tr').removeClass('hidden');
	_clone.find('input[name="version_id"]').val('');
	$('.version-box').append(_clone);
});

// 删除版本
$('.version-box').on('click', '.version-delete', function(event) {
	event.preventDefault();
	var _ele = $(this).closest('.version');
	layer.confirm('删除后无法恢复!', {icon: 3, title:'确定删除'}, function(index){
		_ele.remove();
		layer.close(index);
	});
});

// 保存
$('.game-save').on('click', function(event) {
	event.preventDefault();
	var url = $(this).attr('data-action') == 'create' ? createUrl : editUrl;
	// 游戏信息
	var game = $('form[name="game-info"]');
	// 区服信息
	var zones = $('form[name="game-zone"]');
	var dataLength = zones.length;
	if(dataLength === 0){
		layer.alert('游戏必须包含一个区服商品信息', {icon: 2});
		return false;
	}
	//获取提交post字符串
	var DATA = getPostData(game, zones);

	if(DATA){
		$.ajax({
			url: url,
			async: false,
			type: 'post',
			cache: false,
			dataType: 'json',
			data: {data : JSON.stringify(DATA)},
			success: function(data, textStatus){
				//console.log(data);
				layer.msg("保存游戏信息成功", {icon: 1, time: 1500, shade:false, offset: '100px'}, function(){
					// window.location.href = showUrl + '/' + data.gid
				});
			},
			error: function(data, textStatus){
				layer.alert('保存游戏信息出错, 请重试', {icon: 2});
			}

		});
	}
});

function getPostData(_game, _zones){
	var _ret = true;
	var _data = {};

	// 游戏信息
	var gameInfo  = _game.serializeJson();
	if(! Validate.gameInfo(gameInfo)){
		layer.alert('游戏信息不完整', {icon: 2});
		_ret = false;
		return false;
	}
	console.log(gameInfo);

	// 区服 && 商品 验证
	var zoneData = [];
	_zones.each(function(index, el) {
		var formRowData = $(el).serializeJson();
		// 是否选择商品
		if(typeof(formRowData.price_id) == 'undefined'){
			// 游戏商品信息不完整
			layer.alert('商品信息不正确', {icon:2});
			_ret = false;
			return;
		}

		// 系统&登录平台
		if(formRowData.version_system == '0' || formRowData.version_login == '0'){
			layer.alert('系统或者登录平台配置错误', {icon: 2});
			_ret = false;
			return;
		}

		// 是否需要区服
		if(formRowData.no_zone != '1'){
			// 区服是否填写
			if(formRowData.zone_type == '1'){ // 单区服
				if(formRowData.single_zone_partition == ''){
					layer.alert('单区服请配置分区', {icon:2});
					_ret = false;
					return;
				}
			}else if(formRowData.zone_type == '2'){ // 多区服
				if(typeof(formRowData.partition) == 'string' && typeof(formRowData.points_suit) == 'string'){
					if(formRowData.partition != '' &&  formRowData.points_suit != ''){
					}else{
						layer.alert('区服信息不能为空1', {icon: 2});
						_ret = false;
						return;
					}
				}
				if(typeof(formRowData.partition) == 'object'){
					if(formRowData.partition.length != formRowData.points_suit.length){
						layer.alert('区服信息不能为空2', {icon: 2});
						_ret = false;
						return;
					}
					for(var key in formRowData.partition){
						if(formRowData.partition[key] != '' && formRowData.points_suit[key] != ''){
						}else{
							layer.alert('区服信息不能为空2', {icon: 2});
							_ret = false;
							return;
						}
					}
				}
			}else{
				// 区服信息异常
				layer.alert('区服信息配置异常, 刷新后重试', {icon:2});
				_ret = false;
				return;
			}
		}else{
			console.log('no zones');
		}
		zoneData.push(formRowData);
	});

	if(_ret){
		_data.gameinfo = gameInfo;
		_data.zones = zoneData;
		return  _data;
	}
	return false;
}

var Validate = {
	gameInfo : function(obj){
		if(obj.game_pic != '' && obj.game_name != '' && obj.game_first_letters != ''){
			return true;
		}
		return false;
	}
};
