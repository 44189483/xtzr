var t;

$(function(){

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
        },
        //这是关键的语句，配置这个参数后表单不会自动提交，验证通过之后会去调用的方法 //速度有点慢
        submitHandler:function() {
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

    var form = $('#form1');
    // 为表单创建事件监听
    $(form).submit(function(e) {

        if($("#verifcode").val() == ''){
            return false;
        }

        var url;

        //阻止浏览器直接提交表单
        e.preventDefault();

        var formtype = $('#formtype').val();

        if(formtype == 'mysafe'){
            if($("#pwd").val() == '' || ($("#pwd").val() != $("#repwd").val())){
                return false;
            }
        }

        // 序列化表单数据
        var formData = $(form).serialize();

        // 使用AJAX提交表单
        $.ajax({
            type: 'POST',
            url: $(form).attr('action'),
            data: formData,
            beforeSend: function ( xhr ) {
                $('#submit').attr('disabled',true);
            }
        })
        .done(function(response) {

            $('#submit').removeAttr('disabled',true);

            if(response == 500){
                layer.msg('服务器错误.');
                return false;
            }

            if(response == -1){
                layer.msg('图片验证码错误.');
                return false;
            }

            //登陆
            if(formtype == 'login'){
                if(response == -2){
                    layer.msg('用户不存在.');
                    return false;
                }else if(response == -3){
                    layer.msg('密码错误请重新填写.');
                    return false;
                }
                url = '/user';
            }

            //注册
            if(formtype == 'register'){
                if(response == -2){
                    layer.msg('用户已存在.');
                    return false;
                }else if(response == -3){
                    layer.msg('短信验证码错误.');
                    //发送验证码复位
                    return false;
                }else if(response == -4){
                    layer.msg('渠道推荐码错误.');
                    return false;
                }  
                url = '/user';
            }

            //查找密码
            if(formtype == 'findpwd'){
                if(response == -2){
                    layer.msg('用户不存在.');
                    return false;
                }else if(response == -3){
                    layer.msg('短信验证码错误.');
                    //发送验证码复位
                    return false;
                }
                url = null;
                window.location.href='/member/setpwd';
            }

            //设置密码
            if(formtype == 'setpwd'){
                url = '/member/login';
            }

            if(formtype == 'mysafe'){
                url = '/user/mysafe';
            }

            if(url != null){
                //弹出框提示+跳转
                layer.confirm(response, {
                  btn: ['确定'] //按钮
                }, function(){
                  window.location.href = url;
                });
            }

        })
        .fail(function(data) {
            var txt = '';
            // 设置消息文本
            if (data.responseText !== '') {
                txt = data.responseText;
            } else {
                txt = '出错了,请重新再试.';
            }
            //提示层
            layer.msg(txt);
            $('#submit').removeAttr('disabled',true);
        });

    });

    //安全设置 取消
    $('.info-panel table').delegate('button.cancel','click',function(){
        var p = $(this).parent();
        p.addClass('hide');
        p.siblings('div').removeClass('hide');
    });

    //安全设置 绑定
    $('.info-panel table').delegate('button.bind','click',function(){
        var p = $(this).parent().parent();
        p.addClass('hide');
        p.siblings('div').removeClass('hide');
    });

    //安全设置 绑定
    $('.info-panel table').delegate(' button.ok','click',function(){

        var input = $(this).siblings('input');

        var name = input.attr('name');

        if(name == 'mobile'){

            if (!input.val().match(/^((1[0-9]{1})+\d{9})$/)) { 
                layer.msg('手机号码格式不正确.');
                return false; 
            } 

        }

        if(name == 'email'){

            if(!input.val().match(/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/)){
                layer.msg('邮箱格式不正确.');
                return false;
            }

        }

        if(name == 'bankcard'){
            if(input.val() == '' || input.val() == null){
                layer.msg('银行卡+卡号不能为空.');
                return false;                
            }
        }

        $.ajax({
           type: "POST",
           url: "/user/ajax_safe",
           data: "filed="+name+"&value="+input.val(),
           cache: false,
           success: function(msg){
             if(msg == '-1'){
                layer.msg('信息已存在.');
             }else{
                input.parent().addClass('hide');
                input.parent().siblings('div').removeClass('hide');
                input.parent().siblings('div').html(input.val()+'<span class="fr"><button type="button" class="btn-small btn-s-g bind">已绑定</button></span>');
             }
           }
        });
    });

    //登陆 记住并切换选中
    $("#rem").click(function(){
        var r = $("#rember");
        if(r.val() == 1){
            r.val(0);
            $(this).attr('src','/public/home/pc/images/checkbox_f.png');
        }else{
            r.val(1);
            $(this).attr('src','/public/home/pc/images/checkbox_t.png');
        }
    });

    getlend();

    //触发输入手机验证码时
    $("#phonecode").keyup(function(){
        clearTimeout(t);
        //验证码按钮激活
        $("#getphonecode").attr("value","重新发送验证码");
        $("#getphonecode").css("background","#fff"); 
        $("#getphonecode").attr("disabled", false);
        //提交按钮变成点击
        $("#submit").attr("disabled", false);
        $("#submit").css("background","#fe6724"); 
    });

    //发送手机验证码
    $("#getphonecode").click(function(){
        var phone = $("#user");
        if(phone.val() == '' || phone.val() == null){
            layer.msg('手机号码不能为空.');
            return false;
        }
        if(!phone.val().match(/^((1[0-9]{1})+\d{9})$/)){
            layer.msg('手机号码格式不正确.');
            return false;
        }
        
        $.ajax({
           type: "POST",
           url: "/member/sendverifycode",
           data: "phone="+phone.val()+"&formtype="+$("#formtype").val(),
           cache: false,
           success: function(msg){
            if(msg == -2){
                layer.msg('用户已存在.');
                return false;
            }else if(msg != 'ok' && msg != null && msg != ''){ 
                alert(msg);
            }
           }
        });

        $("#submit").css("background","#eee");  
        $("#submit").attr("disabled", true);//禁用提交按钮
        time(this);
        
    });

});

//出借无刷新查询 
function getlend(ids,n){
    var getids = ids == null ? '0-0-0-0' : ids;
    var p = n == null ? '' : '?per_page='+n;
    var url = "/lend/getlist/"+getids+"/"+Math.random()+p;
    $("#lends").load(url);    
}

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

/*发送手机验证码*/
var wait=180;  
function time(o) {  
    var btn = document.getElementById('submit');
    if (wait == 0) {  
        o.removeAttribute("disabled");            
        o.value="重新发送验证码";
        o.style.background="#fff"; 
        btn.style.background="#fe6724";
        btn.setAttribute("disabled", false);//禁用提交按钮 
        wait = 180;  
    } else {  
        o.setAttribute("disabled", true);  
        o.value="发送验证码(" + wait + ")...";
        o.style.background="#9d9d9d";
        wait--;  
        t = setTimeout(function() {  
            time(o)  
        },  
        1000)  
    }  
}  