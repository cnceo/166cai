<!-- 修改默认银行卡弹层 start -->
<div class="pub-pop pop-bank-choose" style="display: none;">
    <div class="pop-in">
        <div class="pop-head">
            <h2>默认提款银行卡</h2>
            <span class="pop-close" title="关闭">关闭</span>
        </div>
        <div class="pop-body" id="setDefault">
            <p class="pop-txt">确认将尾号为<span id='setbankid'></span>的<span id='setbankname'></span>卡设为默认提现银行吗?</p>
            <div class="btn-group">
                <input type='hidden' class='vcontent' name='action' value='_3'>
                <input type='hidden' class='vcontent' id="bankId" name='id' value=''>
                <a class="btn btn-blue-med submit mr10" target="_self" href="javascript:;">提交</a>
                <a class="btn btn-gray-med cancel" id="pop-close" target="_self" href="javascript:;">取消</a>
            </div>
        </div>
    </div>
</div>
<!-- 修改默认银行卡弹层 end -->
<!-- 删除银行卡弹层 start -->
<div class="pub-pop pop-bank-del" style="display: none;">
    <div class="pop-in">
        <div class="pop-head">
            <h2>删除提款银行卡</h2>
            <span class="pop-close" title="关闭">关闭</span>
        </div>
        <div class="pop-body">
            <p class="pop-txt">确认删除尾号为<span id='setbankid_del'></span>的<span id='setbankname_del'></span>账户吗?</p>
            <div class="btn-group">
                <input type='hidden' class='vcontent' name='action' value='_4'>
                <input type='hidden' class='vcontent' id="bankId_del" name='id' value=''>
                <a class="btn btn-blue-med submit mr10" target="_self" href="javascript:;">提交</a>
                <a class="btn btn-gray-med cancel" id="del-pop-close" target="_self" href="javascript:;">取消</a>
            </div>
        </div>
    </div>
</div>
<!-- 删除银行卡弹层 start -->
<!-- 查看身份信息弹层 start -->
<div class="pub-pop pop-bank-id" style="display: none;">
    <div class="pop-in" id="checkIdBank">
        <div class="pop-head">
            <h2>查看身份信息</h2>
            <span class="pop-close" title="关闭">关闭</span>
        </div>
        <div class="pop-body">
            <!-- 表单 -->
            <form action="" class="form form-check">
                <div class="form-item">
                    <label for="" class="form-item-label">绑定证件号</label>
                    <div class="form-item-con">
                        <span class="form-item-txt"><?php echo cutstr($this->uinfo['id_card'], 0, 12);?></span>
                    </div>
                </div>
                <div class="form-item">
                    <label for="checkIdBank" class="form-item-label">检验证件号</label>
                    <div class="form-item-con">
                        <input type="text" class="form-item-ipt vcontent" value="" data-rule="sameidcard" data-ajaxcheck='1' name="validate_idcard" >
                        <div class="form-tip hide">
                            <i class="icon-tip"></i>
                            <span class="form-tip-con validate_idcard tip"></span>
                            <s></s>
                        </div>
                    </div>
                </div>
                <div class="form-item btn-group">
                    <div class="form-item-con">
                        <a class="btn btn-confirm submit" target="_self" href="javascript:;">提交</a>
                        <a class="btn btn-default cancel" id='bid-pop-close' target="_self" href="javascript:;">取消</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- 查看身份信息弹层 end -->
<script>
//提交验证身份
    new cx.vform('#checkIdBank', {
        renderTip: 'renderTips',
        submit: function (data) {
            cx.viewUinfo.hide();
            $('#realName').hide();
            $('#realNameShow').show();
            $('#idCardHide').hide();
            $('#idCardShow').show();
        }
    });
</script>

