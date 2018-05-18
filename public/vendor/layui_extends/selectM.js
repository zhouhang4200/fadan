/*
* @Author: tomato
* @Date:   2018-5-5 11:29:57
* @Last Modified by:   tomato
* @Last Modified time: 2018-5-16 07:48:00
*/
//多选下拉框
layui.define(['jquery', 'layer'], function(exports){
	var MOD_NAME = 'selectM';
	var $ = layui.jquery,layer=layui.layer;
	var obj = function(config){
		this.selectedData = [];
		this.values =[];
		this.names =[];
		this.config = {
			//选择器id或class
			elem: '',
			
			//默认选中值
			selected: [],
			
			//最多选中个数
			max : 5,
			
			//选项数据
			data: [],
			
			//input的name
			name: '',
			
			//数据分隔符
			delimiter: ',',
			
			//数据的键名
			field: {idName:'id',titleName:'name'}
		}

		this.config = $.extend(this.config,config);
		
		//创建选项元素
		this.createOption = function(){
				var o=this,c=o.config,f=c.field,d = c.data;
				var s = c.selected;
				$selectWrap = $(c.elem);
				var inputName = c.name=='' ? '' : 'name="'+c.name+'"';
				var html = '';
				html +=	'<div class="layui-unselect layui-form-select">';
				html +=			'<div class="layui-select-title">';
				html +=				'<input '+inputName+' type="text" placeholder="请选择" value="" readonly="" class="layui-input layui-unselect">';
				html +=			'</div>';
				html +=			'<div class="layui-input multiple">';
				html +=			'</div>';
				html +=			'<dl class="layui-anim layui-anim-upbit">';
				html +=				'<dd lay-value="" class="layui-select-tips">请选择 最多'+c.max+'个</dd>';
				for(var i=0;i<d.length;i++){
					html +='<dd lay-value="'+d[i][f.idName]+'" class="">';
					html +=		'<div class="layui-unselect layui-form-checkbox" lay-skin="primary">';
					html +=			'<span>'+d[i][f.titleName]+'</span><i class="layui-icon">&#xe605;</i>';
					html +=		'</div>';
					html +='</dd>';
					//html +=			'<dd lay-value="'+d[i][f.idName]+'" class="">'+d[i][f.titleName]+'</dd>';					
				}
				html +=			'</dl>';
				html +=		'</div>';				
				$selectWrap.html(html);
				//为默认选中值添加类名
				for(var i=0;i<s.length;i++){
					if(s[i]){
						$selectWrap.find('dd[lay-value='+s[i]+']').addClass('layui-this');	
					}
				}
				$selectWrap.find('dd.layui-this').each(function(){
					$(this).find('div').addClass('layui-form-checked');
				});
		}
		
		
		//设置选中值 每次点击操作后执行
		this.setSelected = function(){
			var o=this,c=o.config,f=c.field;
			$selectWrap = $(c.elem);
			var values=[],names=[],selectedData = [],spans = [];
			var items = $selectWrap.find('dd.layui-this');
			if(items.length==0){
				$selectWrap.find('.multiple').html('<span style="pointer-events: none;position: absolute;left: 10px;top: 10px;color:#757575;">请选择 最多'+c.max+'个</span><i class="layui-edge" style="pointer-events: none;"></i>');
			}
			else{
				items.each(function(){
					$this = $(this);
					var item ={};
					var v = $this.attr('lay-value');
					var n = $this.find('span').text();
					item[f.idName] = v;
					item[f.titleName] = n;
					values.push(v);
					names.push(n);
					spans.push('<a href="javascript:;"><span lay-value="'+v+'">'+n+'</span><i class="layui-icon">&#x1006;</i></a>');
					selectedData.push(item);
				});
				$selectWrap.find('.layui-select-title').find('input').val(values.join(c.delimiter));
				$selectWrap.find('.multiple').html(spans.join('')+'<i class="layui-edge" style="pointer-events: none;"></i>');				
			}
			var h = $selectWrap.find('.multiple').height()+14;
			$selectWrap.find('.layui-form-select dl').css('top',h+'px');
			o.values=values,o.names=names,o.selectedData = selectedData;
		}
	};

	//渲染一个实例
  obj.prototype.render = function(){
		var o=this,c=o.config,f=c.field;
		$selectWrap = $(c.elem);
		if($selectWrap.length==0){
			console.error(MOD_NAME+' hint：找不到容器 ' +c.elem);
			return false;
		}
		if(Object.prototype.toString.call(c.data)!='[object Array]'){
			console.error(MOD_NAME+' hint：缺少分类数据');
			return false;
		}
		
		
		//给容器添加一个类名
		$selectWrap.addClass('lay-ext-mulitsel');
		//添加专属的style
		if($('#lay-ext-select-style').length==0){
			var style = '.lay-ext-mulitsel .layui-form-select dl dd div{margin-top:0px!important;}.lay-ext-mulitsel .layui-form-select dl dd.layui-this{background-color:#fff}.lay-ext-mulitsel .layui-input.multiple{line-height:auto;height:auto;padding:4px 4px 4px 10px;overflow:hidden;min-height:38px;margin-top:-38px;left:0;z-index:99;position:relative;background:#fff;}.lay-ext-mulitsel .layui-input.multiple a{padding:2px 5px;background:#5FB878;border-radius:2px;color:#fff;display:block;line-height:20px;height:20px;margin:2px 5px 2px 0;float:left;}.lay-ext-mulitsel .layui-input.multiple a i{margin-left:4px;font-size:14px;} .lay-ext-mulitsel .layui-input.multiple a i:hover{background-color:#009E94;border-radius:2px;}';
			$('<style id="lay-ext-select-style"></style>').text(style).appendTo($('head'));
		};
		
		//创建选项
		o.createOption();
		//设置选中值
		o.setSelected();
		
		//展开/收起选项
		$selectWrap.on('click','.layui-select-title,.multiple,.multiple.layui-edge',function(e){
			//隐藏其他实例显示的弹层
			$('.lay-ext-mulitsel').not(c.elem).removeClass('layui-form-selected');
			if($(c.elem).is('.layui-form-selected')){
				$(c.elem).removeClass('layui-form-selected');
				
				$(document).off('click',mEvent);
			}
			else{
				$(c.elem).addClass('layui-form-selected');
				
				$(document).on('click',mEvent=function(e){
					if(e.target.id!==c.elem && e.target.className!=='layui-input multiple'){
						$(c.elem).removeClass('layui-form-selected');			
						$(document).off('click',mEvent);
					}
				});	
			}
		});
		

		
		//点击选项
		$selectWrap.on('click','dd',function(e){
			var _dd = $(this);
					  
			//点 请选择
			if(_dd.is('.layui-select-tips')){
				_dd.siblings().removeClass('layui-this');
				$(c.elem).find('.layui-form-checkbox').removeClass('layui-form-checked');
				
				$(c.elem).find('.layui-select-title').find('input').val('');
				$(c.elem).find('.multiple').html('<span style="position: absolute;left: 10px;top: 10px;color:#757575;">请选择</span><i class="layui-edge" style="pointer-events: none;"></i>');
			}
			//取消选中
			else if(_dd.is('.layui-this')){
				_dd.removeClass('layui-this');
				_dd.find('.layui-form-checkbox').removeClass('layui-form-checked');
				e.stopPropagation();
			}
			//选中
			else{
				if(o.selectedData.length >= c.max){
					layer.msg('最多只能选择'+c.max+'个');
					return false;
				}
				else{
					_dd.addClass('layui-this');
					_dd.find('.layui-form-checkbox').addClass('layui-form-checked');
					e.stopPropagation();					
				}
			}
			
			o.setSelected();
		});
		
		//删除选项
		$selectWrap.on('click','a i',function(e){
			var _this = $(this).prev('span');
			var v = _this.attr('lay-value');
			if(v){
				var _dd = $(c.elem).find('dd[lay-value='+v+']');
				_dd.removeClass('layui-this');
				_dd.find('.layui-form-checkbox').removeClass('layui-form-checked');
			}
			o.setSelected();
			_this.parent().remove();
			e.stopPropagation();
			
		})
		
	}
	
	//输出模块
	exports(MOD_NAME, function (config) {
		var _this = new obj(config);
		_this.render();
		return _this;
  });
});