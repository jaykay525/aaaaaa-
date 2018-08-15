jQuery.extend({
	/**
	 * 全选或反选复选框
	 */
	checkbox : function(o, container, v){
		if($(o).is(':checked') || v)
			$(container + ' input[type="checkbox"]').prop('checked', 'checked');
		else{
			$(container + ' input[type="checkbox"]').removeProp('checked');
		}
	},
});

//跳转URL
function jumpurl (url, wait_time) {
	var wait_time = wait_time ? wait_time * 1000 : 0;
	loading();
	jumpurl_handle = setTimeout(function(){
		clearTimeout(jumpurl_handle);
		if(url){
			location.href = url;
		}else{
			removeLoading();
			history.go(-1);
		}
	}, wait_time);
}

function loading(){
	removeLoading();
	$("body").append("<div class='loading-box' id='loading'><div class='icon-logo'></div><div class='loading'></div></div>");
	var left = ($(document).outerWidth() - $('#loading').outerWidth()) / 2;
	var top  = ($(window).outerHeight() - $('#loading').outerHeight()) / 2;
		left = left - 10;
		top  = top - 30;
	$("#loading").css({left:left + 'px'});
	$("#loading").css({top:top + 'px'});
}

function money_format(money) {
    if(isNaN(money)){
        return money;
    }
    money = (Math.round((money - 0) * 100)) / 100;
    money = (money == Math.floor(money)) ? money + ".00" : ((money * 10 == Math.floor(money * 10)) ? money + "0" : money);
    money = String(money);
    var ps = money.split('.');
    var whole = ps[0];
    var sub = ps[1] ? '.' + ps[1] : '.00';
    var r = /(\d+)(\d{3})/;
    while (r.test(whole)) {
        whole = whole.replace(r, '$1' + ',' + '$2');
    }
    money = whole + sub;

    return money;
}

/**
 * JS消息盒子
 * @param msg 信息
 * @param type 类型，有warn、success、error默认为success
 * @param redirect 重定向地址
 * @param timeout 缓冲时间，默认3秒
 * @return
 */
function msgbox(msg, type, redirect, timeout){
	removeLoading();
	$("#msgbox-div").remove();
	type = type ? type : 'success';
	msg = msg ? msg : '数据操作成功！';
	var left = ($(document).width()-240)/2;
	$("body").append("<div id='msgbox-div' class='msgbox-" + type +"'><i class='icon-" + type + "'></i>" + msg +"</div>");
	loading_w = $("#msgbox-div").outerWidth();
	var left = ($(window).width() - loading_w) / 2;
	$("#msgbox-div").css({left:left + 'px'});

	timeout = parseInt(timeout);
	timeout = timeout>0 ? timeout : 3;
	var handle = setTimeout(function(){
		$("#msgbox-div").remove();
		clearTimeout(handle);
		if(redirect) document.location.href = redirect;
	},timeout * 1000);
}


function removeLoading(){
	$("#loading").remove();
}

//生成随机密码
function suggest_password(id, len) {
	var len       = len ? len : 16
	var pwchars   = "abcdefhjmnpqrstuvwxyz123456789ABCDEFGHJKLMNPQRSTUVWYXZ";
	var passwd    = $('#' + id);
	var passwdstr = '';
	for ( i = 0; i < len; i++ ) {
		passwdstr += pwchars.charAt( Math.floor( Math.random() * pwchars.length ) )
	}
	passwd.val(passwdstr);
}

$(document).ready(function(){
	// 密码MD5加密
	$('form').on('submit', function () {
		$('input[type="password"]').each(function () {
			if(!$(this).hasClass('not-md5') && $(this).val()){
				var password     = $(this).val();
				var password_md5 = $.md5(password);
				$(this).val(password_md5);
			}
		});
	});
	// 图片验证码更换
	$(document).on('click', '.img-verify', function () {
		var url = $(this).attr('href') + '?' + Math.random();
		$(this).attr('src', url);
		return false;
	});
	//获取验证码
	$(document).on('click', '.get-smscode', function () {
		var mobile   = $('input[name="mobile"]').val();
		var imgcode  = $('input[name="imgcode"]').val();
		var url      = $(this).attr('url');
		var sms_type = $(this).attr('sms-type');
			sms_type = sms_type ? sms_type : 'register';
		if(!url){
			msgbox('URL未配置！', 'error');
			return false;
		}
		if(!mobile){
			msgbox('手机号不能为空！', 'error');
			return false;
		}
		if(!imgcode){
			msgbox('图片验证码不能为空！', 'error');
			return false;
		}
		loading('获取验证码中...');
		$.ajax({
			type: "get",
			url: url,
			data: 'type=' + sms_type + '&mobile=' + mobile + '&imgcode=' + imgcode,
			dataType: "json",
			success: function(data){
				removeLoading();
				if(data.code == 1){
					$('.get-smscode').hide();
					$('.smscode-time').css('display', 'inline-block').find('span').text(60);
					msgbox(data.msg);
					var wait = $('.smscode-time span');
					var smscode_interval = setInterval(function(){
						var time = wait.text();
							--time;
							wait.text(time);
						if(time == 0) {
							$('.smscode-time').hide();
							$('.get-smscode').show();
							clearInterval(smscode_interval);
						};
					}, 1000);
				}else{
					msgbox(data.msg, 'error');
				}
			},
			error : function () {
				removeLoading();
				msgbox('请求失败！', 'error');
			}
		});
	});

	//弹窗JS
	$.getScript("/public/js/jquery-plugin/win/win.extend.js", function () {
		// 弹出窗口
		$(document).on('click', ".win", function(){
			$(this).win();
			return false;
		});
	});
	// 加载表单验证
	$.getScript("/public/js/jquery-plugin/validate/init.js");
	//ajax表单提交
	$(document).on('submit', 'form.ajax-form', function () {
		var url    = $(this).attr('action');
			url    = url ? url : window.location.href;
		var method = $(this).attr('method');
		var method = method ? method : 'post';
		var data   = $(this).serialize();

		loading('正在提交，请稍候...');
		$.ajax({
			url : url,
			data : data,
			type : method == 'post' ? 'post' : 'get',
			dataType:'json',
			success : function(json){
				if(json.status == 1){
					msgbox(json.info);
				}else{
					msgbox(json.info,'error');
				}
				//有返回链接，则跳转
				if(json.url){
					jumpurl(json.url);
				}
			},
			error : function (data) {
				msgbox('请求失败，请稍候再试！', 'error');
			}
		});
		return false;
	});
	//表单提交
	$(document).on('click', '.form-submit', function () {
		var action    = $(this).attr('action');
			action    = action ? action : ($(this).attr('href') ? $(this).attr('href') : window.location.href);
		var method    = $(this).attr('method');
		var container = $(this).attr('form');

		form_html = container ? $('.' + container).html() : $('form').html();
		if(form_html){
			var html = "<form class='tmp-form' novalidate='true' action='" + (action ? action : '') + "' method='" + (method ? method : 'post') + "'>" + form_html + "</form>";
			$(html).submit();
		}
		return false;
	});
	
	//打印处理
	if($(".btn-print").size()){
		$.getScript("/Public/Js/jquery-plugin/jquery.jqprint-0.3.js", function () {
			$(".btn-print").click(function(){
				var container = $(this).attr('for');
					container = container ? '.' + container : 'body';
				$(container).jqprint(); 
			});
		});
	}

	//跳转链接
	$(document).on('click', '.jump', function () {
		var url = $(this).attr('href');
		var url = url ? url : $(this).attr('url');
		jumpurl(url);
		return false;
	});

	// AJAX 复选框选择
	$(document).on('click', '.ajax_checkbox', function () {
		var url = $(this).attr('url');
		loading('正在修改，请稍候...');
		$.ajax({
			url : url,
			type : 'get',
			dataType:'json',
			success : function(json){
				if(json.code == 1){
					msgbox(json.msg);
				}else{
					msgbox(json.msg, 'error');
				}
			},
			error : function (data) {
				msgbox('请求失败，请稍候再试！', 'error');
			}
		});
	});

	// 通用添加选择
	$(document).on('click', '.add-select-common', function () {
		var field        = $(this).attr('field');
		var is_mulit     = $(this).attr('is-mulit');
		var select_value = $(this).attr('select-value');
		var show_name    = $(this).attr('show-name');
		var win_id       = $(this).attr('win-id');
		var max_num      = $(this).attr('max-num');
		var is_del       = $(this).attr('no-del') != 'true';

		count = $("." + field + "-common-select-box input[value='" + select_value + "']").size();
		if(count > 0){
			msgbox('您选择的已存在！', 'error');
			return;
		}
		var cur_num = $("." + field + "-common-select-box span").size() - 1;
		if(max_num && cur_num >= max_num){
			msgbox('对不起，选择不能超过' + max_num + '个', 'error');
			return;
		}
		
		var html  = '<span class="btn btn-info">' + show_name;
		    html += '	<input type="hidden" name="' + field + '[]" value="' + select_value + '"> ';
		    html += '	<i class="delete-select-common fa fa-times" field="' + field + '" is-mulit="' + is_mulit + '"></i>';
		    html +=	'</span> ';

		$('.' + field + '-common-select-box .common-select-plugin').before(html);
		// 删除选中行
		if(is_del){
			if($('.data-select-' + select_value).size()){
				$('.data-select-' + select_value).remove();
			}else{
				$(this).parent().parent().remove();
			}
		}else{
			$('.select-icon-' + select_value).removeClass('fa-square-o').addClass('fa-check-square-o');
		}
		if(is_mulit == 0){
			$.winRemove(win_id);
			$('.' + field + '-common-select-box .common-select-plugin').hide();
		}

		$('.' + field + '-common-select-box').find('input[value="0"]').remove();
	});
	$(document).on('click', '.delete-select-common', function () {
		var is_mulit = $(this).attr('is-mulit');
		var field    = $(this).attr('field');
		if(is_mulit == 0){
			$('.' + field + '-common-select-box .common-select-plugin').show();
		}
		$(this).parent().remove();

		var count = $('input[name="' + field + '[]"]').size();
		if(count == 0){
			var html = '<input type="hidden" name="' + field + '[]" value="0">';
			$('.' + field + '-common-select-box .common-select-plugin').before(html);
		}
	});

	// 处理上传后文件选择是否选中
	$(document).on('click', '.win-upload-box .file-item', function() {
		var is_mulit = $(this).attr('is-mulit');
			is_mulit = is_mulit == 1 ? 1 : 0;
		if(is_mulit == 0){
			$('.win-upload-box .file-item').removeClass('on');
		}
		if($(this).hasClass('on')){
			$(this).removeClass('on')
		}else{
			$(this).addClass('on');
		}
	});
	// 确定选中文件处理
	$(document).on('click', '.confirm-selected-file', function () {
		var el           = $(this).attr('to');
		var is_mulit     = $(this).attr('is-mulit');
			is_mulit     = is_mulit == 1 ? 1 : 0;
		var upload_field = $(this).attr('field');
		var win_id       = $(this).attr('win-id');
			win_id       = win_id ? win_id : '';
		$('.win-upload-box li.on').each(function(){
			var file_id  = $(this).find('input').val();
			var file_url = $(this).attr('file-url');
			var is_pic   = $(this).attr('is-pic');
			var html     = '<li><a class="nojump ' + (is_pic ? 'view_big_pic' : '') + '" href="' + file_url + '">' + $(this).html() + '</a><span class="cancel delete-select-upload-file" is-mulit="' + is_mulit + '" field="' + upload_field + '">X</span></li>';
			if(!$('.' + el).find("input[value='" + file_id + "']").val()){
				if(is_mulit){
					$('.' + el).append(html).find('.icon-success').remove();
					$('.' + upload_field + '-upload-btn').show();
				}else{
					$('.' + el).html(html).find('.icon-success').remove();
					$('.' + upload_field + '-upload-btn').hide();
				}
			}
			// 删除默认值
			$('.' + upload_field + '-upload-list-box input[value="0"]').remove();
			$.winRemove(win_id);
		});
	});
	$(document).on('click', '.delete-select-upload-file', function () {
		var is_mulit = $(this).attr('is-mulit');
		var field    = $(this).attr('field');
		if(is_mulit == 0){
			$('.' + field + '-upload-btn').show();
		}
		$(this).parent().remove();
		// 如果删除掉所有的，则添加默认
		if(!$('.upload-list-' + field + ' li').size()){
			var html = '<input type="hidden" name="' + field + '[]" value="0">';
			$('.' + field + '-upload-list-box').append(html);
		}
	});

	//查看大图
	if($('.view_big_pic').size() || $('.upload-common-list').size()){
		$.getScript("/public/js/jquery-plugin/fancyBox/jquery.fancybox.pack.js?v=2.1.5",function () {
			$("<link>").attr({ 
				rel: "stylesheet",
				type: "text/css",
				href: "/public/js/jquery-plugin/fancyBox/jquery.fancybox.css?v=2.1.5"
			}).appendTo("head");
			//点击图片，查看大图
			$('.view_big_pic').fancybox();
			$('.view_big_pic').click(function () {
				// return false;
			})
		});
	}

	//操作提示
	$(document).on('click', '.opt-tip', function () {
		var url = $(this).attr('tip-url');
			url = url ? url : $(this).attr('href');
			url = url ? url : $(this).attr('url');
		var tourl       = $(this).attr('jumpurl');
		var del_mod     = $(this).attr('del-class');
		var label_mod   = $(this).attr('label-class');
		var loading_tip = $(this).attr('tip-loading');
			loading_tip = loading_tip ? loading_tip : '正在操作，请稍候...';
		var here = $(this);

		if (!url) {
			return false;
		}
		loading(loading_tip);
		$.ajax({
			type: "get",
			url: url,
			dataType: "json",
			success : function(data){
				if(data.code == 1){
					msgbox(data.msg);
					if(tourl){
						jumpurl(tourl);
					}
					if(data.url){
						jumpurl(data.url);
					}
					if(del_mod){
						$('.' + del_mod).fadeOut('slow', function () {
							$('.' + del_mod).remove();
						});
					}
					if(data.data.label){
						if(label_mod){
							$('.' + label_mod).text(data.data.label);
						}else{
							here.text(data.data.label);
						}
					}
				}else{
					msgbox(data.msg, 'error');
				}
			},
			error :function (data) {
				msgbox('请求失败，请稍候再试！', 'error');
			}
		});
		return false;
	});
	//确认操作
	$(document).on('click', ".confirm-opt", function () {
		var checkbox  = $(this);
		var url       = $(this).attr('url');
		var del_mod   = $(this).attr('del-class');
		var msg       = $(this).attr('msg');
		var method    = $(this).attr('method');
		var label_mod = $(this).attr('label-class');
		var here      = $(this);

		url = url ? url : $(this).attr('href');
		msg = msg ? msg : '您确认要进行此操作吗？';

		html  = '<div style="width:300px;" class="confirm-opt-box">';
		html += '<div>' + msg + '</div>';
		html += '<div style="text-align:center;margin-top:8px"><span class="btn btn-confirm"><i class="icon-success"></i>确定</span> <span class="btn btn-default" onclick="$.winRemove()"><span class="icon-error"></span>取消</span></div>';
		html += '</div>';
		$.win_info('', html, '提示信息');
		$('.confirm-opt-box .btn-confirm').bind('click', function () {
			loading('正在处理，请稍候...');
			$.ajax({
				type: method ? method : 'get',
				url: url,
				dataType: 'json',
				success: function(data){
					$.winRemove();
					if(data.code == 1){
						msgbox(data.msg);
						$('.' + del_mod).fadeOut('slow', function () {
							$('.' + del_mod).remove();
						});
						if(checkbox.attr('checked')){
							checkbox.removeAttr('checked');
						}else{
							checkbox.attr('checked', 'checked');
						}
						if(data.url){
							jumpurl(data.url);
						}
						if(data.data.label){
							if(label_mod){
								$('.' + label_mod).text(data.data.label);
							}else{
								here.text(data.data.label);
							}
						}
					}else{
						msgbox(data.msg,'error');
					}
				},
				error : function () {
					msgbox('请求失败，请稍候再试！', 'error');
				}
			});
		})
		return false;
	});

	// 批量修改
	$('.batch-edit').on('click', function () {
		var action = $(this).attr('url');
		var action = action ? action : location.href;
		var method = $(this).attr('method');
			method = method ? method : 'post'
		var container = $(this).attr('from');
			container = container ? container : 'data-list';
		$('.' + container).wrap("<form action='" + action + "' method='" + method + "'></form>");
		$('.' + container).parent("form").submit();
	});
	// 选中删除,批量操作
	$('.batch-opt').on('click', function () {
		var url = $(this).attr('url');
			url = url ? url : $(this).attr('href');
			url = url ? url : '';
		var msg = $(this).attr('msg');
		var is_restore = $(this).hasClass('restore-opt');
		var is_delete  = $(this).hasClass('delete-opt');
		if(!url){return false }
		if($('input[name="ids[]"]:checked').size() == 0){
			msgbox('对不起，您尚未选择!', 'error');
			return false;
		}
		var btn_text = '';
		var content  = '';
		if(is_restore){
			btn_text = '还原';
			content  = '您确定要还原选中的吗？'
		}
		if(is_delete){
			btn_text = '删除';
			content  = '您确定要删除选中的吗？删除后将无法恢复'
		}
		msg = msg ? msg : (content ? content : '您确定要删除选中的吗？');

		var btn_confirm_text = $(this).attr('btn-confirm-text');
			btn_confirm_text = btn_confirm_text ? btn_confirm_text : (btn_text ? btn_text : '确定');
		html  = '<style> .winBodyContainer{padding:0px;padding-top:10px;overflow-y:scroll;} </style><div class="confirm-opt-box">'; html += '<div class="win-confirm-content">' + msg + '</div>';
		html += '<div class="win-opt-box"><span class="btn btn-confirm"><i class="icon-success"></i>' + btn_confirm_text + '</span> <span class="btn btn-cancel" onclick="$.winRemove()"><span class="icon-error"></span>取消</span></div>';
		html += '</div>';
		$.win_info('', html, '<i class="if if-tips"></i>提示信息');
		$('.confirm-opt-box .btn-confirm').bind('click', function () {
			loading('正在请求中...');
			$('table').wrap("<form class='ajax-batch-form' action='" + url + "' method='post'></form>");
			var data = $('.ajax-batch-form').serialize();
			$.ajax({
				type: 'post',
				url: url,
				data: data,
				dataType: 'json',
				success: function(data){
					removeLoading();
					$.winRemove();
					if(data.code == 1){
						msgbox(data.msg);
						location.reload();
					}else{
						msgbox(data.msg, 'error');
					}
				},
				error : function () {
					removeLoading();
					msgbox('请求失败，请稍候再试！', 'error');
				}
			});
		})
		return false;
	});

	if($('body').has('.ueditor').size()){
		$('.ueditor').hide().after('<p class="ueditor-loading">加载中，请稍候...</p>');
		window.UEDITOR_HOME_URL = "/public/js/ueditor/";
		$.getScript("/public/js/ueditor/ueditor.config.js",function () {
			$.getScript("/public/js/ueditor/ueditor.all.min.js",function () {
				var toolbar = [[
					"fullscreen","source","undo","redo","|",
					"drafts","pasteplain","|",
					"simpleupload","insertimage","|",
					"bold","italic","underline","fontborder","forecolor","backcolor","spechars","|",
					"link","justifyleft","justifycenter","justifyright","justifyjustify","indent","lineheight","|",
					"removeformat","fontfamily","fontsize"]];
				$('.ueditor').each(function(){
					var ueditor_id = $(this).attr('id');
					var ueditor_id_type = typeof(ueditor_id);
					if($(this).hasClass('ueditor') && ueditor_id_type == 'string'){
						UE.getEditor(ueditor_id, {
							toolbars: toolbar,
							removeFormatAttributes : '',
							allowDivTransToP : false,
							'initialFrameWidth' : '100%'
							// autoHeightEnabled : true
						});
						UE.getEditor(ueditor_id).addListener( 'ready', function() {
							$('#' + ueditor_id).show();
							$('#' + ueditor_id).parent().find('.ueditor-loading').remove();
						});
					}
				});
			});
		});
	}
	//星级评分
	$(document).on('mousemove', '.rating', function (e) {
		var offset    = $(this).offset().left;
		var curoffset = e.pageX - offset;
		var value     = (curoffset / $(this).width()) * 100;
		if(value <= 20){
			val = 1;
		}
		else if(value > 20 && value <= 40){
			val = 2;
		}
		else if(value > 40 && value <= 60){
			val = 3;
		}
		else if(value > 60 && value <= 80){
			val = 4;
		}else{
			val = 5;
		}
		$(this).find('.star').removeClass('on');
		$(this).find('.star:lt(' + val + ')').addClass('on');
		$(this).find('input').val(val);
	});

	// AJAX 修改input内容更新保存
	$('.ajax-update-field').on('focusout', function() {
		var request_url = $(this).attr('url') ? $(this).attr('url') : $(this).attr('href');
		var content     = $(this).val();
		if(!request_url){
			msgbox('请求URL未配置！', 'error');
			return false;
		}
		loading();
		$.ajax({
			url : request_url,
			data : 'content=' + content,
			type : 'post',
			dataType:'json',
			success : function(json){
				msgbox(json.info);
			},
			error : function () {
				msgbox('请求失败，请稍候再试！', 'error');
			}
		});
	});

	$(document).on('click', '.panel-header .title', function () {
		if($(this).find('i').hasClass('icon-arrow-down-solid')){
			$(this).find('i').removeClass('icon-arrow-down-solid').addClass('icon-arrow-right-solid');
		}else{
			$(this).find('i').removeClass('icon-arrow-right-solid').addClass('icon-arrow-down-solid');
		}
		$(this).parent().parent().find('.panel-body').slideToggle();
	});
	// 选择省份处理
	$(document).on('change', '.ajax-province-change', function () {
		var request_url  = $(this).attr('ajax-url');
		var uniqid       = $(this).attr('uniqid');
		var province_id  = $(this).val();
		var city_option  = '<option value="">-城市-</option>';
		var area_option  = '<option value="">-地区-</option>';
		var is_have_city = $(this).next('.ajax-city-change').size();
		$('.ajax-city-change[uniqid="' + uniqid + '"]').html(city_option).hide();
		$('.ajax-area-change[uniqid="' + uniqid + '"]').html(area_option).hide();
		if(!province_id || !request_url || !is_have_city){
			return false;
		}
		loading();
		$.ajax({
			url : request_url,
			data : 'parent_id=' + province_id,
			type : 'get',
			dataType:'json',
			success : function(json){
				removeLoading();
				if(json.code == 1){
					var list = json.data.list;
					$.each(list, function(i){
						area_id = list[i]['area_id'];
						name    = list[i]['name'];
						city_option += '<option value="' + area_id + '">' + name + '</option>';
					});
					$('.ajax-city-change[uniqid="' + uniqid + '"]').html(city_option).show();
				}
			},
			error : function () {
				removeLoading();
				msgbox('请求失败，请稍候再试！', 'error');
			}
		});
	});
	// 选择城市处理
	$(document).on('change', '.ajax-city-change', function () {
		var request_url  = $(this).attr('ajax-url');
		var uniqid       = $(this).attr('uniqid');
		var city_id      = $(this).val();
		var area_option  = '<option value="">-地区-</option>';
		var is_have_area = $(this).next('.ajax-area-change').size();
		$('.ajax-area-change[uniqid="' + uniqid + '"]').html(area_option).hide();
		if(!city_id || !request_url || !is_have_area){
			return false;
		}
		loading();
		$.ajax({
			url : request_url,
			data : 'parent_id=' + city_id,
			type : 'get',
			dataType:'json',
			success : function(json){
				removeLoading();
				if(json.code == 1){
					var list = json.data.list;
					$.each(list, function(i){
						area_id = list[i]['area_id'];
						name    = list[i]['name'];
						area_option += '<option value="' + area_id + '">' + name + '</option>';
					});
					$('.ajax-area-change[uniqid="' + uniqid + '"]').html(area_option).show();
				}
			},
			error : function () {
				removeLoading();
				msgbox('请求失败，请稍候再试！', 'error');
			}
		});
	});
	// 复制
	if($('.clipboard').size()){
		$.getScript("/public/js/ZeroClipboard/ZeroClipboard.min.js",function () {
			ZeroClipboard.config( { swfPath: "/public/js/ZeroClipboard/ZeroClipboard.swf" } );
			$('.clipboard').each(function () {
				var here = $(this);
				var elem = $(this).attr('for');

				var client = new ZeroClipboard( $(this) );
				client.on( 'ready', function(event) {
					client.on( 'copy', function(event) {
						var content = $('.' + elem).text();
							content = content ? content : here.attr('copy-text');
							alert(content);
						event.clipboardData.setData('text/plain', content);
					});

					client.on( 'aftercopy', function(event) {
						msgbox('复制成功！');
						// console.log('Copied text to clipboard: ' + event.data['text/plain']);
					});
				});

			});
		});
	}
	// tip提示
	$( ".tooltip" ).tooltip({
		show: {
			delay: 10
		}
    });
});





