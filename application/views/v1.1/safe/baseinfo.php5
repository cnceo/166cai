<?php $this->load->view('v1.1/elements/user/menu');?>
<div class="l-frame-cnt">
<div class="uc-main">
	<div class="tit-b">
		<h2>基本资料</h2>
        <p class="tip cOrange">完善个人资料有助于我们给您提供更好的服务</p>
	</div>
	<div class="safe-item-box edit-form">
		<form action="" class="form uc-form-list pl154">
			<div class="form-item">
				<label class="form-item-label">166彩账号</label>
                <div class="form-item-con">
                	<span class="form-item-txt name"><?php echo $uname; ?></span>
                </div>
			</div>
			<div class="form-item">
				<label class="form-item-label">性别</label>
				<div class="form-item-con">
                    <label for="boy" class="form-item-txt"><input type="radio" value="1" class="radio" name="gender" <?php echo ($gender == '1')?'checked="checked"':''?> id="boy">男</label>
                    <label for="gril" class="form-item-txt"><input type="radio" value="2" class="radio" <?php echo ($gender == '2')?'checked="checked"':''?> name="gender" id="gril">女</label>
                    <label for="unknow" class="form-item-txt"><input type="radio" value="0" class="radio" <?php echo ($gender == '0')?'checked="checked"':''?> name="gender" id="unknow">保密</label>
                </div>
			</div>
			<div class="form-item">
				<label class="form-item-label">QQ号</label>
				<div class="form-item-con">
					<input type="text" id='qq' class="form-item-ipt vcontent" value="<?php echo $qq; ?>" name="qq" data-rule="qq">
					<div class="form_tips" style="display: none;">
	                    <i class="tips_icon"></i>
						<span class="tips_con qq tip"></span>
						<s></s>
					</div>
				</div>
			</div>
			<div class="form-item form-add">
				<label class="form-item-label">居住地</label>
				<div class="form-item-con">
					<dl class="simu-select-med" data-target='city_list'>
			            <dt>
			            	<span class='_scontent' id='province' data-value='<?php echo $province?>'><?php echo $province?></span>
			            	<i class="arrow"></i>
                            <input type="hidden" class="vcontent" name='province' value='<?php echo $province?>'>
                        </dt>
			            <dd class="select-opt">
			            	<div class="select-opt-in" data-name='province'>
			            		<?php foreach($provinceList as $row): ?>
				            	<a href="javascript:;" data-value='<?php echo $row['province']?>'><?php echo $row['province']?></a>
				            	<?php endforeach; ?>
				            </div>
			            </dd>
			        </dl>
			        <dl class="simu-select-med city_list">
			            <dt>
			            	<span class='_scontent' id='city' data-value='<?php echo $city?>'><?php echo $city?></span>
			            	<i class="arrow"></i>
                            <input type="hidden" class="vcontent" name='city' value='<?php echo $city?>'>
                        </dt>
			            <dd class="select-opt">
			            	<div class="select-opt-in" id='city-container' data-name='city'><?php echo $cityList; ?></div>
			            </dd>
			        </dl>
				</div>
			</div>
			<div class="form-item btn-group">
				<div class="form-item-con">
					<a href="javascript:;" class="btn btn-main submit">保存</a>
				</div>
			</li>
		</form>
		<div class="warm-tip">
		</div>
	</div>
</div>
</div> 
<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.1/js/vform.min.js');?>'></script>
<script type="text/javascript">
	$(function(){
		new cx.vform('.edit-form', {
            renderTip: 'renderTips',
	        submit: function(data) {
               var self = this;
               var data = data || {};
               data['gender'] = $('input:checked').val() || 0;

                var flag = true;
                $('.simu-select-med').each(function(){
                    var dt = $(this).find('dt');
                    if( dt.find('._scontent').html() != dt.find('.vcontent').val() || 
                        dt.find('._scontent').html() == '请选择') {
                        flag = false;
                    }
                });
                if(!flag) {
                    new cx.Alert({content: '请选择居住地省市'});
                    return 	false;
                }
                
	            $.ajax({
	                type: 'post',
	                url:  '/safe/baseinfo',
	                data: data,
	                success: function(response) {
                        if(response){
                            new cx.Alert({content: '保存成功'});
                        }
	                }
	            })
	        }
	    });
	});
</script>
<?php $this->load->view('v1.1/elements/user/menu_tail');?>