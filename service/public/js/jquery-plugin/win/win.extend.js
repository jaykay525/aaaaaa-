$("<link>").attr({ 
	rel: "stylesheet",
    type: "text/css",
    href: "/public/js/jquery-plugin/win/style.css?1"
}).appendTo("head");
/**
 * 触发HTML标签的Ajax请求事件，并将请求返回内容在新弹出的窗口进行显示
 * 调用方法如：$(tag).win(callback)或$.win(tab,callback)
 * 其中ajax的url默认为href->rel->alt->action->当前url
 */
jQuery.extend({
	//浏览器宽度
	winWidth : 0,
	
	//浏览器高度
	winHeight : 0,
	// 级别
	win_z_index : 10000,
	//是否全屏
	win_is_full_screen : false,
	// 表单验证
	form_valid : '',
	
	//相关初始化：获取窗口宽高
	winInit : function(){
		jQuery.winWidth = jQuery(window).width();
		jQuery.winHeight = jQuery(window).height();
		$('body').css({'overflow':'hidden'});
		$.win_is_full_screen = false;
		$.win_id = 'id-' + new Date().getTime();
		$.form_valid = '';
	},
	
	//添加一个完全透明的DIV层覆盖整个界面
	winPostLoad : function(){
		var win_html =  '<div class="winCoverBackBg" id="win-backdrop-' + $.win_id + '"></div>'+
						'<div class="winFrontContenter" id="win-box-' + $.win_id + '" win-id="' + $.win_id + '">' +
							'<div class="winTitleBar" style="cursor: move;"><span></span><a title="关闭" class="remove-win">×</a></div>' +
							'<div class="win-body-container winBodyContainer"></div>' +
						"</div>";

		$("body").append(win_html);

		var win_bg_width = $(document).width();
		var win_bg_height = $(document).height();
		$('#win-backdrop-' + $.win_id).css({
			'z-index' : $.win_z_index++, 
			'width' : win_bg_width,
			'height' : win_bg_height
		});
		$('#win-box-' + $.win_id).css({'z-index' : $.win_z_index++});

	},
	
	//计算弹出框口的宽度，并将窗口调整为居中
	winCalFront : function(){
		// $wfc = $("#winFrontContenter");
		$wfc = $('#win-box-' + $.win_id);
		// $wfc.removeAttr('style');
		var win_width  = $wfc.width();
		var win_height = $wfc.height();

		var is_width_out_win  = win_width > $(window).width();
		var is_height_out_win = win_height > $(window).height();

		win_width  = is_width_out_win ? $(window).width() : win_width;
		win_height = is_height_out_win ? $(window).height() : win_height;

		//是否全屏
		if($.win_is_full_screen){
			$wfc.width($(window).width());
			win_width = $(window).width();
			$wfc.find('.win-body-container').height($(window).height());
			win_height = $(window).height();
			
		}

		if(is_width_out_win){
			$wfc.find('.win-body-container').css('overflow-x', 'scroll');
		}
		if(is_height_out_win){
			$wfc.find('.win-body-container').css('overflow-y', 'scroll');
			$wfc.find('.win-body-container').height(win_height-30);
		}
		
		$wfc.width(win_width);
		$wfc.css({
			left:(($.winWidth - win_width) / 2) + "px",
			top:(($.winHeight - win_height) / 2) + "px"
		});

		$.win_is_full_screen = false;
		// $wfc.width($wfc.outerWidth()+16);
	},
	
	//将请求返回的内容填充到该窗口中
	winPut : function(html,title,callback){
		// $wbc = $("#winBodyContainer");
		$wbc = $('#win-box-' + $.win_id).find('.win-body-container');
		$wbc.html(html);
		// 满屏不处理
		if(!$.win_is_full_screen){
			$wbc.find("a").each(function(){
				if($(this).hasClass("noneAjax")) return ;
				if($(this).hasClass("nojump")) return ;
				$(this).bind("click",function(){
					return $.winBindEvent(this,callback);
				});
			});
		}
		// 判断是否有设置上下滚动
		if($('#win-box-' + $.win_id).find('[is-scroll="true"]').size()){
			$('#win-box-' + $.win_id).find('.win-body-container').css('overflow-y', 'scroll');
		}
		if($wbc.find("div").height() > $wbc.height()){
			$('#win-box-' + $.win_id).find('.win-body-container').css('overflow-y', 'scroll');
		}
		// 有底部操作按钮，设置win-body的padding为0
		if($('#win-box-' + $.win_id).find('.win-opt-box').size()){
			$('#win-box-' + $.win_id).find('.win-body-container').css({
				'padding' : '0px'
			});
			$('#win-box-' + $.win_id).find('.win-opt-box').prev().css({
				'padding-bottom' : '50px',
				'margin-bottom' : '0px'
			});
		}
		// 表单验证
		$.form_valid = $('#win-box-' + $.win_id).find('form').validate();
		$wbc.find("form:not(.filter)").bind("submit",function(){
			var data = $(this).serialize();
			if($.form_valid != ''){
				if($(this).valid() == false){return false;}
			}
			return $.winBindEvent(this, callback, data);
		});
		
		if(title && typeof(title) != "undefined"){
			$('#win-box-' + $.win_id).find(".winTitleBar span").html(title);
		}
		$wbc.find('form').attr('is-win', 'true');
		$.winCalFront();
		// 鼠标在标题栏上，启用拖动功能
		$(".winTitleBar").on('mousemove', function () {
			$('.winFrontContenter').draggable();
		});
		// 鼠标离开标题栏上，取消拖动功能
		$(".winTitleBar").on('mouseout', function () {
			$('.winFrontContenter').draggable("destroy");
		});

		// 关闭窗口事件
		$('#win-box-' + $.win_id).find('.remove-win,.removeWin').attr('win-id', $.win_id);
		$('#win-box-' + $.win_id).find('.set-win-id,.removeWin').attr('win-id', $.win_id);
		$('.remove-win,.removeWin').bind("click", function(){
			var win_id = $(this).attr('win-id');
			$('#win-backdrop-' + win_id).remove();
			$('#win-box-' + win_id).remove();
			$('body').css({'overflow':'auto'});
		});
	},
	
	//将新加载进来的内容中的“a”、“form”绑定事件，以便新请求内容在该窗口进行显示
	winBindEvent : function(tag,callback,data){
		data = data ? data : '';
		var url        = $(tag).attr("href") ? $(tag).attr("href") : ($(tag).attr("rel") ? $(tag).attr("rel") : $(tag).attr("url"));
		var url        = url ? url : $(tag).attr("action");
		var method     = $(tag).attr("method");
		var url        = url ? url : window.location.href;
		var title      = $(tag).attr("title");
		var is_animate = $(tag).attr("is-animate");
		if(!method && !$(tag).parent().parent().hasClass('pagination')){
			title = title ? title : $(tag).text();
		}
		if(method){
			btn_title = $(tag).find('input[type="submit"]').attr('title');
			title = btn_title ? btn_title : title;
		}
		title = title ? title : $('.winTitleBar span').text();

		is_animate = is_animate ? true : false;
		loading();
		$.ajax({
			url : url,
			data : data,
			type : method=='post' ? 'post' : 'get',
			dataType:'html',
			success : function(requestText){
				removeLoading();
				//判断返回值不是 json 格式
				if (!requestText.match("^\{(.+:.+,*){1,}\}$")){
					// $.winRemove();
					if($('#win-box-' + $.win_id).length <= 0) {
						$.winPostLoad();
					}
					$.winPut(requestText, title, callback, is_animate);
					if(callback){
						eval(callback);
					}
				}else{
					json = jQuery.parseJSON(requestText);
					if(json.code == 1){
						msgbox(json.msg);
						if(json.url){
							jumpurl(json.url, json.wait);
						}
						var node_id      = json.data.node_id;
						var node_data    = json.data.node_data;
						var del_node_id  = json.data.del_node_id;
						var no_close_win = json.data.no_close_win;
						if(node_id && node_data){
							$.each(node_data , function(key, value){
								$('.'+node_id).find('.'+key).html(value);
							});
						}
						if(del_node_id){
							$('.'+del_node_id).remove();
						}
						if(no_close_win == true){
							return false;
						}
						//关闭窗口
						$.winRemove();
					}else{
						$('body').css({'overflow':'auto'});
						msgbox(json.msg, 'error');
					}
				}
				//$.winCalFront();
			},
			error : function () {
				$('body').css({'overflow':'auto'});
				msgbox('请求失败，请稍候再试！', 'error');
			}
		});
		return false;
	},
	
	//移除新弹出的窗口
	winRemove : function(win_id){
		if(win_id){
			$('#win-box-' + win_id).find('.remove-win').trigger("click");
			$('#win-box-' + win_id).find('.removeWin').trigger("click");
		}else{
			$(".winFrontContenter .remove-win").trigger("click");
		}
		$.form_valid = '';
		$('body').css({'overflow' : 'auto'});
	},
	
	win : function(tag,callback){
		$.winInit();
		return $.winBindEvent(tag,callback);
	},
	win_info : function(tag,requestText,title, is_full_screen){
		$.winInit();
		var title = title?title:$(tag).attr("title");
		var is_full_screen = is_full_screen ? true : false;
		$.win_is_full_screen = is_full_screen;
		$.winPostLoad();
		$.winPut(requestText,title);
	}
});
jQuery.fn.extend({
	win : function(callback){
		return jQuery.win(this,callback);
	},
	win_info : function(html,title){
		return jQuery.win_info(this,html,title);
	}
})