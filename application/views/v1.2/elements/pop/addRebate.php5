<div class="pub-pop pop-id addRebatePop">
	<div class="pop-in">
		<div class="pop-head">
			<h2>添加下线</h2>
			<span class="pop-close" title="关闭">×</span>
		</div>
		<div class="pop-body">
			<form action="" class="form form-one-tips">
				<div class="form-item">
					<label class="form-item-label">用户名：</label>
					<input type="text" class="form-item-ipt vcontent" name="uname">
				</div>
				<div class="form-item">
					<label class="form-item-label">手机号：</label>
					<input type="text" class="form-item-ipt vcontent" name="phone">
				</div>
				<div class="form-tip form-tip-error hide">
	                <i class="icon-tip"></i>
	                <span class="form-tip-con tip">用户名和手机号不符合</span>
	                <s></s>
	            </div>
				
				<div class="form-item btn-group">
	                <a class="btn-pop-confirm submit" target="_self" href="javascript:;">确认</a>
	                <a class="btn-pop-cancel cancel" target="_self" href="javascript:;">取消</a>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
$(function(){
	new cx.vform('.addRebatePop', {
		renderTip: 'renderTips',
        submit: function(data) {
            var self = this;
            if(data.uname == '' || data.phone == ''){
            	self.renderTip('请输入用户名或手机号', $('.form-tip-con'));
                return false;
            }
            $.ajax({
                type: 'post',
                url:  '/ajax/addRebate',
                data: data,
                success: function(response) {
                    if(response.code == 0){
                        //添加成功
                    	$("#subordinate").load("/rebates/subordinate"); //刷新我的下线页面
                    	cx.PopCom.hide('.addRebatePop');
                    	new cx.Confirm({
                            content: '<div class="mod-result result-success"><div class="mod-result-bd"><i class="icon-result"></i><div class="result-txt"><h2 class="result-txt-title">恭喜您，操作成功</h2></div></div></div>',
                            btns: [
                                {
                                    type: 'confirm',
                                    txt: '设置比例',
                                    href: 'javascript:;'
                                }
                            ],
                            confirmCb: function() {
                            	$.ajax({
                    	            type: 'post',
                    	            url: '/pop/setRebateOdd',
                    	            data: {version: version, id: response.id},
                    	            success: function (response) {
                    	                $('body').append(response);
                    	                cx.PopCom.show('.set-rebates');
                    	                cx.PopCom.close('.set-rebates');
                    	                cx.PopCom.cancel('.set-rebates');
                    	            }
                    	        });
                            }
                        });
                    }else{
                        //登录失败
                    	self.renderTip(response.msg, $('.form-tip-con'));
                    }
                }
            });
        }
    });
});
</script>