<!-- 修改默认银行卡弹层 start -->
<div class="pub-pop pop-bank-choose" style="display: none;">
    <div class="pop-in">
        <div class="pop-head">
            <h2>默认提现银行卡</h2>
            <span class="pop-close" title="关闭">&times;</span>
        </div>
        <div class="pop-body" id="setDefault">
            <p class="pop-txt">确认将尾号为<span id='setbankid'></span>的<span id='setbankname'></span>卡设为默认提现银行吗?</p>
        </div>
        <div class="pop-foot">
        	<div class="btn-group">
                <input type='hidden' class='vcontent' name='action' value='_3'>
                <input type='hidden' class='vcontent' id="bankId" name='id' value=''>
                <a class="btn btn-pop-confirm submit mr10" target="_self" href="javascript:;">提交</a>
                <a class="btn btn-pop-cancel cancel" id="pop-close" target="_self" href="javascript:;">取消</a>
            </div>
        </div>
    </div>
</div>
<!-- 修改默认银行卡弹层 end -->
<!-- 删除银行卡弹层 start -->
<div class="pub-pop pop-bank-del" style="display: none;">
    <div class="pop-in">
        <div class="pop-head">
            <h2>删除提现银行卡</h2>
            <span class="pop-close" title="关闭">&times;</span>
        </div>
        <div class="pop-body">
            <p class="pop-txt">确认删除尾号为<span id='setbankid_del'></span>的<span id='setbankname_del'></span>账户吗?</p>
        </div>
        <div class="pop-foot">
        	<div class="btn-group">
                <input type='hidden' class='vcontent' name='action' value='_4'>
                <input type='hidden' class='vcontent' id="bankId_del" name='id' value=''>
                <a class="btn btn-pop-confirm submit mr10" target="_self" href="javascript:;">提交</a>
                <a class="btn btn-pop-cancel cancel" id="del-pop-close" target="_self" href="javascript:;">取消</a>
            </div>
        </div>
    </div>
</div>
<!-- 删除银行卡弹层 start -->

