 <!-- 修改用户名 start -->
    <div class="pub-pop J-popmodifyname">
        <div class="pop-in">
            <div class="pop-head">
                <h2>修改用户名（仅可修改一次）</h2>
                <span class="pop-close" title="关闭">×</span>
            </div>
            <div class="pop-body">
                <div class="form">
                    <div class="form-item">
                        <label for="oldName" class="form-item-label">旧用户名：</label>
                        <div class="form-item-con"><span class="form-item-txt"><?php echo $oldUname;?></span></div>
                    </div>
                    <div class="form-item" style="height: 64px;">
                        <label for="newName" class="form-item-label">新用户名：</label>
                        <div class="form-item-con">
                            <input class="form-item-ipt vcontent" name="username" type="text" data-rule="username" data-ajaxcheck='1' style="width: 210px;">
                            <div class="form-tip hide">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con tip username" style="width: 210px;"></span>
                                <s></s>
                            </div>
                        </div>  
                    </div>
                    <!--<div class="form-tip form-tip-error">
                        <i class="icon-tip"></i>
                        <span class="form-tip-con tip">用户名和手机号不符合</span>
                        <s></s>
                    </div>-->

                    <div class="form-item btn-group">
                        <a class="btn-pop-confirm submit" target="_self" href="javascript:;">确认</a>
                        <a class="btn-pop-cancel cancel" target="_self" href="javascript:;">取消</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- 修改用户名 end -->
<script>
	new cx.vform('.J-popmodifyname', {
	renderTip: 'renderTips',
	sValidate: 'submitValidate',
    submit: function(data) {
    	var self = this;
    	$.ajax({
            type: 'post',
            url:  '/mainajax/modifyName',
            data: data,
            success: function(response) {
            	if(response.status == '1'){
            		$('.J-popmodifyname').remove();
                	cx.Mask.hide();
                	location.reload();
                }else{
                	self.renderTip(response.msg, $('.username'));
                }
            }
        });
    }
});
</script>