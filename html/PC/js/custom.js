$(function(){

    /*
    var form = $('#ajax-appointment');
	// 为表单创建事件监听
	$(form).submit(function(e) {

		//阻止浏览器直接提交表单
		e.preventDefault();

		//手机验证
		var mobile = document.getElementById("mdInputPhone");;
	    mobile.onblur = function(){
	        if(mobile.validity.patternMismatch){ 
	            mobile.setCustomValidity("cell phone number error.");
	        }
	    };

        //手机验证
        var robot = $("#mdCheckRot");
        if(robot.val() == '1'){
            $('.isrobot p i').attr('style','color:red');
            return false;
        }

        // 序列化表单数据 隐藏域无法获到值FUCK
        var formData = $(form).serialize();

        // 使用AJAX提交表单
        $.ajax({
            type: 'POST',
            url: $(form).attr('action'),
            data: formData,
            beforeSend: function ( xhr ) {
                $('.load-wrap').append('<img src="../wp-content/themes/ruxinspa/images/loading.gif" alt="" />');
                $('#submit').hide();
            }
        })
        .done(function(response) {

            $('#modalTip').modal();

            if(response == 200){

                $('#myModal').modal('hide');

                // 清除表单
                $('#mdInputName').val('');
                $('#mdInputPhone').val('');
                $('#mdCheckKnow').val('');
                $('#mdInputOthers').val('');
                $('.know i').attr("class","fa fa-square-o purple-font");
                $('.isrobot').find('i').attr("class","fa fa-square");
              
                $('#myModalLabel').html('<img src="../wp-content/themes/ruxinspa/images/icon_person.png" alt="" style="float:left;width:70px;"/>Thank You For <br/>Using Our Online Booking System.');

                $(formMessages).html('<img src="../wp-content/themes/ruxinspa/images/icon_tel.png" alt="" style="float:left;margin:-15px 10px 0 0;width:50px;"/>Our staff will contact you for confirmation shortly.');

            }else if(response == 500){
                $('#myModalLabel').text('Tip');
                $(formMessages).text('Internal Server Error.');
            }

            $('.load-warp img').remove();

            $('#submit').show();

        })
        .fail(function(data) {

            $('#modalTip').modal();

            $('#myModalLabel').text('Tip');

            // 设置消息文本
            if (data.responseText !== '') {
                $(formMessages).text(data.responseText);
            } else {
                $(formMessages).text('An error occurred and could not send.');
            }

            $('#submit').show();

            $('.load-warp img').remove();

        });
 
	});
    */

    var form = $('#form1');
    // 为表单创建事件监听
    $(form).submit(function(e) {

        //阻止浏览器直接提交表单
        e.preventDefault();

        /*
        var formtype = $('#formtype').val();

        switch(formtype){

            case 'login':

                break;

        }
        */

        /*
        //手机验证
        var robot = $("#mdCheckRot");
        if(robot.val() == '1'){
            $('.isrobot p i').attr('style','color:red');
            return false;
        }

        // 序列化表单数据
        var formData = $(form).serialize();

        // 使用AJAX提交表单
        $.ajax({
            type: 'POST',
            url: $(form).attr('action'),
            data: formData,
            beforeSend: function ( xhr ) {
                $('.load-wrap').append('<img src="../wp-content/themes/ruxinspa/images/loading.gif" alt="" />');
                $('#submit').hide();
            }
        })
        .done(function(response) {

            $('#modalTip').modal();

            if(response == 200){

                $('#myModal').modal('hide');

                // 清除表单
                $('#mdInputName').val('');
                $('#mdInputPhone').val('');
                $('#mdCheckKnow').val('');
                $('#mdInputOthers').val('');
                $('.know i').attr("class","fa fa-square-o purple-font");
                $('.isrobot').find('i').attr("class","fa fa-square");
              
                $('#myModalLabel').html('<img src="../wp-content/themes/ruxinspa/images/icon_person.png" alt="" style="float:left;width:70px;"/>Thank You For <br/>Using Our Online Booking System.');

                $(formMessages).html('<img src="../wp-content/themes/ruxinspa/images/icon_tel.png" alt="" style="float:left;margin:-15px 10px 0 0;width:50px;"/>Our staff will contact you for confirmation shortly.');

            }else if(response == 500){
                $('#myModalLabel').text('Tip');
                $(formMessages).text('Internal Server Error.');
            }

            $('.load-warp img').remove();

            $('#submit').show();

        })
        .fail(function(data) {

            $('#modalTip').modal();

            $('#myModalLabel').text('Tip');

            // 设置消息文本
            if (data.responseText !== '') {
                $(formMessages).text(data.responseText);
            } else {
                $(formMessages).text('An error occurred and could not send.');
            }

            $('#submit').show();

            $('.load-warp img').remove();

        });
        */
    });

    //省市下拉
    $("#prov_city").citySelect({
        nodata: "none",
        required: false
    });

    //安全设置 取消
    $('.info-panel table button.cancel').click(function(){
        var p = $(this).parent();
        p.addClass('hide');
        p.siblings('div').removeClass('hide');
    });

    //安全设置 绑定
    $('.info-panel table button.bind').click(function(){
        var p = $(this).parent().parent();
        p.addClass('hide');
        p.siblings('div').removeClass('hide');
    });

    //安全设置 绑定
    $('.info-panel table button.ok').click(function(){
        var input = $(this).siblings('input');
        //input.val()
        //input.attr('name');
    });

});

$("#form1").validate({
    rules: {
      user: {
        required: true,
        checkphone: true
      },
      pwd: {
        required: true,
        checkpwd: true
      },
      repwd: {
        required: true,
        equalTo: "#pwd"
      },
      verifcode:"required",
      phonecode:"required",
      email: {
        required: true,
        email: true
      },
      agree: "required"
    },
    messages: {
      user: {
        required: "请输入您的手机号码",
        checkphone: "手机号码格式不对"
      },
      pwd: {
        required: "请输入密码",
        checkpwd: "密码需为6-20位字母与数字组合"
      },
      verifcode:"图片验证码必填",
      phonecode:"手机验证码必填",
      repwd: {
        required: "请输入密码",
        equalTo: "两次密码输入不一致"
      },
      email: "请输入一个正确的邮箱",
      agree: "请接受我们的声明"
    }
});

jQuery.validator.addMethod("checkphone", function(value, element) {   
    var str = /^((1[0-9]{1})+\d{9})$/;
    return this.optional(element) || (str.test(value));
}, "用户名为手机号");

jQuery.validator.addMethod("checkpwd", function(value, element) {   
    var str = /^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,20}$/;
    return this.optional(element) || (str.test(value));
}, "密码需为6-20位字母与数字组合");

/*
* switchTab 标签切换
* m - 容器及标题ID
* n - 当前元素
*/
function switchTab(m,n){
    var tli = document.getElementById("tabs_menu"+m).getElementsByTagName("h4");
    var mli = document.getElementById("tabs_main"+m).getElementsByTagName("ul");
    for(i = 0; i < tli.length; i++){
      tli[i].className = i == n ? "active" : "";
      mli[i].style.display = i == n ? "block" : "none";
    }
}