<?php $position = $this->config->item('POSITION')?>
<style>
.captcha_div{
	display: inline-block;
    margin-right: 10px;
    vertical-align: middle;
    *display: inline;
    *zoom: 1;
}
</style>
<div class="pub-pop pop-id pop-w-max" id="seeCard1">
    <div class="pop-in" style="overflow: visible">
        <div class="pop-head">
            <h2>查看完整身份信息</h2>
            <span class="pop-close" title="关闭">&times;</span>
        </div>
        <div class="pop-body modifyUserPhone">
            <div class="form form-modifyUserPhone">
                <div class="form-item">
                    <label class="form-item-label">手机号：</label>
                    <div class="form-item-con">
                        <span class="form-item-txt"><?php echo substr_replace($this->uinfo['phone'],'****',3,4);?></span>
                    </div>
                </div>
                <div class="form-item">
                    <label class="form-item-label">滑块验证码：</label>
                    <div class="form-item-con">
                        <div class="captcha_div"></div>
                        <div class="form-tip hide">
            				<i class="icon-tip"></i>
            				<span class="form-tip-con tip captcha"></span>
            				<s></s>
            			</div>
                    </div>
                </div>
                <div class="form-item form-vcode vcode-captcha">
                    <label class="form-item-label">验证码：</label>
                    <div class="form-item-con">
                        <input type="text" value="" data-rule="checkcode" class="form-item-ipt vyzm vcontent" name="modifyCaptcha">
                        <a href="javascript:;" id="btn-getYzm" data-freeze="phone" class="lnk-getvcode _timer" target="_self">获取验证码</a>
                        <span href="javascript:;" class="lnk-getvcode-disabled hide">重新发送(<em id='_timer'>60</em>秒)</span>
                        <div class="form-tip hide">
                            <i class="icon-tip"></i>
                            <span class="form-tip-con tip modifyCaptcha"></span>
                            <s></s>
                        </div>
                    </div>
                </div>
                <div class="form-item btn-group">
                    <a class="btn-pop-confirm submit" target="_self" href="javascript:;">提交</a>
                    <a class="btn-pop-cancel cancel" target="_self" href="javascript:;">取消</a>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('v1.1/elements/netimer');?>
</div>
<!-- 身份详情 -->
<div class="pub-pop" id="seeCard2" style="display: none;">
    <div class="pop-in">
        <div class="pop-head">
            <h2>查看完整身份信息</h2>
            <span class="pop-close" title="关闭">&times;</span>
        </div>
        <div class="pop-body seeCard">
            <div class="form seeCard">
                <div class="form-item" id="replace_content">
                </div>
                <div class="form-item btn-group">
                    <a class="btn-pop-confirm cancel" target="_self" href="javascript:;">确定</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
var validate = '', verify = function(err, ret){
    if(!err) {
        validate = ret.validate;
        $('#btn-getYzm').removeClass('btn-disabled');
        $('.captcha').parent().removeClass('form-tip-error').addClass('hide');
    }
}, initNeFun = function() {
	validate = '';
	initne({}, verify);
}
$(function() {
	initNeFun()
    new cx.vform('.modifyUserPhone', {
        renderTip: 'renderTips',
        submit: function (data) {
            var self = this;
            var data = data || {};
            $.ajax({
                type: 'post',
                url: '/safe/seeCard',
                data: data,
                success: function (response) {
                    if( response == 1 ){
                        self.renderTip('请先登录', $('.modifyCaptcha'));
                    }else if( response == 2 ){
                        self.renderTip('验证码错误', $('.modifyCaptcha'));
                        $('input[name="modifyCaptcha"]').val('');
                    }else if( response == 3 ){
                        self.renderTip('验证码错误', $('.modifyCaptcha'));
                        $('input[name="modifyCaptcha"]').val('');
                    }else { 
                        cx.PopCom.hide('#seeCard1');       
                        cx.PopCom.show('#seeCard2');
                        cx.PopCom.close('#seeCard2');
                        cx.PopCom.cancel('#seeCard2');
                        $("#replace_content").html(response);
                    }
                }
            });
        }
    });

    // 发送验证码
    $('#btn-getYzm').click(function(){
        var self = $(this);
        if (!validate) {
			$('.captcha').html('请先滑动验证码完成校验').parent().removeClass('hide').addClass('form-tip-error');
			return;
		}
        $.ajax({
            type: 'post',
            url:  '/main/getPhcodeNE/modifyCaptcha',
            data: {'position':'189', 'validate':validate},
            dataType: 'json',
            success: function(response) {
            	netimer(self);
                if(!response.status) closNeTimer(1);
            },
        });
    });
});
</script>