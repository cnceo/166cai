<div class="tit-b">
    <h2>验证真实身份</h2>
    <p class="tip cOrange">绑定真实身份才能顺利领奖</p>
</div>
<ul class="steps-bar clearfix">
    <li class="cur"><i>1</i><span class="des">填写身份信息</span></li>
    <li><i>2</i><span class="des">核对信息</span></li>
    <li class="last"><i>3</i><span class="des">验证完成</span></li>
</ul>
<div class="safe-item-box safe-auth-box">
    <input type='hidden' class='vcontent' name='action' value='_1'/>
    <form class="form uc-form-list pl154">
        <div class="form-item">
            <label class="form-item-label">真实姓名</label>
            <div class="form-item-con">
                <input type="text" class="form-item-ipt vcontent" data-rule='chinese' value="<?php echo $real_name; ?>" name="real_name">
                <div class="form-tip hide">
                    <i class="icon-tip"></i>
                    <span class="form-tip-con real_name tip"></span>
                    <s></s>
                </div>
            </div>
        </div>
        <div class="form-item">
            <label class="form-item-label">身份证号码</label>
            <div class="form-item-con">
                <input type="text" class="form-item-ipt vcontent" data-rule='identification' value="<?php echo $id_card; ?>" name="id_card">
                <div class="form-tip hide">
                    <i class="icon-form"></i>
                    <span class="form-tip-con id_card tip">身份证号码应为18位</span>
                    <s></s>
                </div>
            </div>
        </div>
        <div class="form-item btn-group">
            <div class="form-item-con">
                <a href="javascript:;" class="btn btn-confirm submit">提交</a>
            </div>
        </div>
    </form>
</div>
<div class="warm-tip">
    <h3>温馨提示：</h3>
    <p>1.真实姓名是您提款时的重要依据，填写后不可更改（请保证身份证姓名与银行卡姓名保持一致，否则无法提款）。</p>
    <p>2.网站不向未满18周岁的青少年出售彩票。</p>
    <p>3.您的个人信息将被严格保密，不会用于任何第三方用途。</p>
</div>
<script type="text/javascript">
    <!--
    $(function(){
        new cx.vform('.safe-item-box', {
            renderTip: 'renderTips',
            submit: function(data) {
                var self = this;
                $.ajax({
                    type: 'post',
                    url:  '/safe/idcard',
                    data: data,
                    success: function(response) {
                        if(response == 2){
                            self.renderTip('登录密码错误', $('.pword'));
                        }
                        else if(response == 3){
                            self.renderTip('真实姓名为空', $('.real_name'));
                        }
                        else if(response == 4){
                        }
                        else if(response == 5){
                            self.renderTip('身份证格式错误', $('.id_card'));
                        }
                        else if(response == 6){
                            self.renderTip('年龄未满18周岁', $('.id_card'));
                        }
                        else if (response == 7 ) {
                            self.renderTip('身份证已绑定', $('.id_card'));
                        }
                        else{
                            $('.article').html(response);
                        }
                    }
                });
            }
        });
    })
    -->
</script>