<title>我的银行卡-166彩票网</title>
<div class="tit-b">
    <h2>添加提现银行卡</h2>
    <p class="tip cOrange">绑定银行卡可用于快捷充值与提现</p>
</div>
<ul class="steps-bar clearfix">
    <li><i>1</i><span class="des">填写实名信息和卡号</span></li>
    <li class="cur"><i>2</i><span class="des">核对信息</span></li>
    <li class="last"><i>3</i><span class="des">添加成功</span></li>
</ul>
<div class="safe-item-box">
    <input type='hidden' class='vcontent' name='action' value='_2'/>
    <div class="form uc-form-list pl200">
        <div class="form-item">
            <label class="form-item-label">真实姓名</label>
            <div class="form-item-con"><span class="form-item-txt name"><?php echo cutstr($this->uinfo['real_name'], 0, 1); ?></span></div>
        </div>
        <div class="form-item">
            <label class="form-item-label">开户银行</label>
            <input type='hidden' class="vcontent" name='bank_type' value='<?php echo $bank_type; ?>' />
            <div class="form-item-con"><span class="form-item-txt"><?php echo $bankTypeList[$bank_type]; ?></span></div>
        </div>
        <div class="form-item">
            <label class="form-item-label">银行卡号</label>
            <div class="form-item-con">
                <input type='hidden' class='vcontent' name='bank_id' value='<?php echo $bank_id; ?>' />
                <span class="form-item-txt bankCard-num"><?php echo $bank_id; ?></span>
            </div>
        </div>
        <div class="form-item form-add">
            <label class="form-item-label">开户地区</label>
            <input type='hidden' class='vcontent' name='province' value='<?php echo $bank_province; ?>' />
            <input type='hidden' class='vcontent' name='city' value='<?php echo $bank_city; ?>' />
            <div class="form-item-con"><span class="form-item-txt"><?php echo $bank_province; ?></span><span class="form-item-txt"><?php echo $bank_city; ?></span></div>
        </div>
        <div class="form-item btn-group">
            <div class="form-item-con">
                <a href="javascript:;" class="btn btn-main submit<?php echo $showBind ? ' not-bind' : ''; ?>">确认</a>
                <a href="javascript:;" class="reedit">返回修改</a>
            </div>
        </div>
    </div>
</div>
<div class="warm-tip mt30">
    <h3>温馨提示：</h3>
    <p>1.银行卡开户姓名必须与绑定的真实姓名一致，否则将提现失败。</p>
    <p>2.银行卡绑定后不可随意修改，特殊情况需修改，请联系客服。</p>
</div>
<script type="text/javascript">

    $(function () {

        $('.not-bind').on('click', showBind);

        $('.reedit').click(function(){
            $('.safe-item-box').find('input[name="action"]').val('_3');
            $('.safe-item-box').find('.submit').trigger('click');
        });

        new cx.vform('.safe-item-box', {
            renderTip: 'renderTips',
            submit: function (data) {
                var self = this;

                if (self.$submit.hasClass('not-bind')) {
                    return false;
                }

                var data = data || {};
                $.ajax({
                    type: 'post',
                    url: '/safe/bankcard',
                    data: data,
                    success: function (response) {
                        if (response) {
                            $('.l-frame-cnt .uc-main').html(response);
                        } else {
                        }
                    }
                });
            }
        });
    });
</script>