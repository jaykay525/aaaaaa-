$.getScript("/public/js/jquery-plugin/validate/jquery.validate.min.js", function(){
	$.extend($.validator.messages, {
		required: "不能为空",
		remote: "请修正该字段",
		email: "请输入正确E-mail格式",
		url: "请输入合法的网址",
		date: "请输入正确的日期",
		dateISO: "请输入正确的日期 (ISO).",
		number: "请输入合法的数字",
		digits: "只能输入整数",
		creditcard: "请输入正确银行卡号",
		equalTo: "请再次输入相同的值",
		accept: "请输入拥有合法后缀名的字符串",
		maxlength: $.validator.format("请输入一个长度最多是 {0} 的字符串"),
		minlength: $.validator.format("请输入一个长度最少是 {0} 的字符串"),
		rangelength: $.validator.format("请输入一个长度介于 {0} 和 {1} 之间的字符串"),
		range: $.validator.format("请输入一个介于 {0} 和 {1} 之间的值"),
		max: $.validator.format("请输入一个最大为 {0} 的值"),
		min: $.validator.format("请输入一个最小为 {0} 的值")
	});
	// 比大字段
	jQuery.validator.addMethod("big-field", function(value, element) {
		var field = $(element).attr('big-field');
		var big_val = $('input[name="' + field + '"]').val();
		
		if(parseFloat(value) < parseFloat(big_val)){
			return true;
		}
		return false;
	}, function (value, element) {
		var msg = $(element).attr('msg');
		return msg;
	});

	// 手机号验证   
	jQuery.validator.addMethod("mobile", function(value, element) {
		var mobile = /^((\(\d{2,3}\))|(\d{3}\-))?(13|14|15|17|18|19)\d{9}$/;
		return this.optional(element) || (mobile.test(value));
	}, '请输入正确手机号！');

	// 固定电话   
	jQuery.validator.addMethod("phone", function(value, element) {
		var mobile = /^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/;
		return this.optional(element) || (mobile.test(value));
	}, '请输入正确手机号！');

	// 手机号验证   
	jQuery.validator.addMethod("pmobile", function(value, element) {
		var mobile = /^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$|(^(13[0-9]|14[0-9]|15[0|3|6|7|8|9]|17[0-9]|18[0-9]|19[0-9])\d{8}$)/;
		return this.optional(element) || (mobile.test(value));
	}, '请输入正确电话号码！');

	// 金额验证   
	jQuery.validator.addMethod("price", function(value, element) {
		var price = /(^[1-9]([0-9]+)?(\.[0-9]{1,4})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/;
		return this.optional(element) || (price.test(value));
	}, '请输入正确金额！');

	// 身份证号码验证       
	jQuery.validator.addMethod("cardno", function(value, element) { 
		var Wi = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1 ];// 加权因子;
		var ValideCode = [ 1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2 ];// 身份证验证位值，10代表X;

		if (value.length == 15) {   
			return isValidityBrithBy15IdCard(value);   
		}else if (value.length == 18){   
			var a_idCard = value.split("");// 得到身份证数组   
			if (isValidityBrithBy18IdCard(value)&&isTrueValidateCodeBy18IdCard(a_idCard)) {   
				return true;   
			}   
			return false;
		}
		return false;
		
		function isTrueValidateCodeBy18IdCard(a_idCard) {   
			var sum = 0; // 声明加权求和变量   
			if (a_idCard[17].toLowerCase() == 'x') {   
				a_idCard[17] = 10;// 将最后位为x的验证码替换为10方便后续操作   
			}   
			for ( var i = 0; i < 17; i++) {   
				sum += Wi[i] * a_idCard[i];// 加权求和   
			}   
			valCodePosition = sum % 11;// 得到验证码所位置   
			if (a_idCard[17] == ValideCode[valCodePosition]) {   
				return true;   
			}
			return false;   
		}
		
		function isValidityBrithBy18IdCard(idCard18){   
			var year = idCard18.substring(6,10);   
			var month = idCard18.substring(10,12);   
			var day = idCard18.substring(12,14);   
			var temp_date = new Date(year,parseFloat(month)-1,parseFloat(day));   
			// 这里用getFullYear()获取年份，避免千年虫问题   
			if(temp_date.getFullYear()!=parseFloat(year) || temp_date.getMonth()!=parseFloat(month)-1 || temp_date.getDate()!=parseFloat(day)){   
				return false;   
			}
			return true;   
		}
		
		function isValidityBrithBy15IdCard(idCard15){   
			var year =  idCard15.substring(6,8);   
			var month = idCard15.substring(8,10);   
			var day = idCard15.substring(10,12);
			var temp_date = new Date(year,parseFloat(month)-1,parseFloat(day));   
			// 对于老身份证中的你年龄则不需考虑千年虫问题而使用getYear()方法   
			if(temp_date.getYear()!=parseFloat(year) || temp_date.getMonth()!=parseFloat(month)-1 || temp_date.getDate()!=parseFloat(day)){   
				return false;   
			}
			return true;
		}

	}, "请正确输入您的身份证号码");
	
	$("input[creditcard]").keyup(function () {
		value = $(this).val();
		value = value.replace(/\s/g,'').replace(/(\d{4})(?=\d)/g,"$1 ");
		$(this).val(value);
	});

	$.validator.setDefaults({
		errorClass : 'if validate-error',
		errorElement : 'span',
		// success : 'if validate-success',
		submitHandler: function(form) {
			var url    = $(form).attr('action');
				url    = url ? url : window.location.href;
			var method = $(form).attr('method');
			var method = method ? method : 'post';
			var data   = $(form).serialize();
			var is_win = $(form).attr('is-win');
			var is_cache_data = $(form).attr('is-cache-data');
			if(is_win){
				return false;
			}
			loading('正在提交，请稍候...');
			$.ajax({
				url : url,
				data : data,
				type : method,
				dataType:'json',
				success : function(json){
					if(json.code == 1){
						// 清空缓存数据
						if(is_cache_data){
							localStorage.clear();
						}
						msgbox(json.msg);
					}else{
						msgbox(json.msg, 'error');
						// 处理错误，清空密码
						$('input[type="password"]').val('');
						$('input[type="password"]:first').focus();
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
		}
	});

	// 表单验证
	if($('form[novalidate!="true"]').size()){
		$('form[novalidate!="true"]').each(function () {
			$(this).validate({
				ignore: []
			});
		});
	}
});

