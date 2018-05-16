/*
* @Author: tomato
* @Date:   2018-4-24 22:56:00
* @Last Modified by:   tomato
* @Last Modified time: 2018-5-7 23:02:07
*/
//无限级下拉框
layui.define(['jquery', 'form'], function(exports){
		var MOD_NAME = 'selectN';
		var $ = layui.jquery;
		var form = layui.form;
    var obj = function(config){
		//当前选中数据
		this.selected =[];
		//选中的值
		this.values = '';
		//选中的文字
		this.names = '';
		//最后值
		this.lastValue = '';
		//是否已选
		this.isSelected = false;
		//初始化配置
		this.config = {
			//选择器id或class
			elem: '',
			//空值项提示
			tips: '请选择',
			//默认选中值
			selected: [],
			//无限级分类数据
			data: [],
			//事件过滤器，lay-filter名
			filter: '',
			//取值类型 all last
			last: false,
			//input的name
			name: '',
			//数据分隔符
			delimiter: ',',
			//数据的键名
			field:{idName:'id',titleName:'name',childName:'children'},
			//表单区分 form.render(type, filter); 为class="layui-form" 所在元素的 lay-filter="" 的值 
			formFilter: null
		}
		
		//实例化配置
		this.config = $.extend(this.config,config);
		
		//创建一个Select
		this.createSelect=function(optionData){
			var c = this.config,f=c.field;
			var html = '';
			html+= '<div class="layui-input-inline">';
			html+= ' <select lay-filter="'+c.filter+'">';
			html+= '  <option value="">'+c.tips+'</option>';
			for(var i=0;i<optionData.length;i++){
				html+= '  <option value="'+optionData[i][f.idName]+'">'+optionData[i][f.titleName]+'</option>';
			}
			html+= ' </select>';
			html+= '</div>';
			return html;
		};

		//获取当前option的数据
		this.getOptionData=function(catData,optionIndex){
			var f = this.config.field;
			var item = catData;
			for(var i=0;i<optionIndex.length;i++){
				if('undefined' == typeof item[optionIndex[i]]){
					item = null;
					break;      
				}
				else if('undefined' == typeof item[optionIndex[i]][f.childName]){
					item = null;
					break;
				}
				else{
					item = item[optionIndex[i]][f.childName];
				}
			}
			return item;
		};
		
		//下拉事件
		this.change = function(elem){
			var o = this,c = o.config;
			var $thisItem = elem.parent();
			//移除后面的select
			$thisItem.nextAll('div.layui-input-inline').remove();
			var index=[];
			//获取所有select，取出选中项的值和索引
			$thisItem.parent().find('select').each(function(){
				index.push($(this).get(0).selectedIndex-1);
			});
			
			var childItem = o.getOptionData(c.data,index);
			if(childItem){
				var html = o.createSelect(childItem);
				$thisItem.after(html);
				form.render('select',c.formFilter);
			}
			this.getSelected();			
		};

		//获取所有值-数组 每次选择后执行
		this.getSelected=function(){
			var c = this.config;
			var values =[];
			var names =[];
			var selected =[];
			$selectWrap = $(c.elem);
			$selectWrap.find('select').each(function(){
				var item = {};
				var v = $(this).val()
				var n = $(this).find('option:selected').text();
				item.value = v;
				item.name = n;
				values.push(v);
				names.push(n);
				selected.push(item);
			});
			this.selected =selected;			
			this.values = values.join(c.delimiter);
			this.names = names.join(c.delimiter);
			this.lastValue = $selectWrap.find('select:last').val();
			this.isSelected = this.lastValue=='' ? false : true;
			var inputVal = c.last===true ? this.lastValue : this.values;
			$selectWrap.find('input[name='+c.name+']').val(inputVal);
		};
	};

	//渲染一个实例
  obj.prototype.render = function(){

		var o=this,c=o.config;
		$selectWrap = $(c.elem);
		if($selectWrap.length==0){
			console.error(MOD_NAME+' hint：找不到容器 '+c.elem);
			return false;
		}
			
		if(Object.prototype.toString.call(c.data)!='[object Array]'){
			console.error(MOD_NAME+' hint：缺少分类数据');
			return false;
		}
		
		c.filter = c.filter=='' ? c.elem.replace('#','').replace('.','') : c.filter;
		c.name = c.name=='' ? c.elem.replace('#','').replace('.','') : c.name;
	
		this.config = c;
		
		//创建顶级select
		$selectWrap.html('<input name="'+c.name+'" type="hidden">');
		var html = o.createSelect(c.data);
		$selectWrap.append(html);
		var index=[];
		for(var i=0;i<c.selected.length;i++){
			//设置最后一个select的选中值
			$selectWrap.find('select:last').val(c.selected[i]);
			//获取该选中值的索引
			var lastIndex = $selectWrap.find('select:last').get(0).selectedIndex-1; 
			index.push(lastIndex);
			//取出下级的选项值
			var childItem = o.getOptionData(c.data,index);
			//下级选项值存在则创建select
			if(childItem){
				var html = o.createSelect(childItem);
				$selectWrap.append(html);
			}
			this.getSelected();
		}
		form.render('select',c.formFilter);
		
		//监听下拉事件
		form.on('select('+c.filter+')',function(data){
			o.change($(data.elem));	
		});
	}
	
	//输出模块
	exports(MOD_NAME, function (config) {
		var _this = new obj(config);
		_this.render();
		return _this;
  });
});