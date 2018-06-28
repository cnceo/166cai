<div class="pub-pop set-rebates">
	<div class="pop-in">
		<div class="pop-head">
			<h2>设置返点比例</h2>
			<span class="pop-close" title="关闭">×</span>
		</div>
		<?php if(empty($uid) || empty($rebate_odds)):?>
		<div>参数错误</div>
		<?php else :?>
		<div class="pop-body">
			<div class="pop-table">
				<table>
					<colgroup>
						<col width="96">
						<col width="150">
					</colgroup>
					<thead>
						<tr>
							<th>彩种</th>
							<th>返点比例</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>竞彩足球</td>
							<td>
								<dl class="simu-select select-small">
						            <dt>
						            <span class='_scontent' id="odd_42"><?php echo $rebate_odds['42'];?>%</span><i class="arrow"></i>
						            <input type='hidden' name='42' class="vcontent" value="<?php echo $rebate_odds['42'];?>" >
						            </dt>
						            <dd class="select-opt">
						            	<div class="select-opt-in" data-name='42'>
					                        <?php foreach( $oddType as $key => $val ): ?>
					                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?>%</a>
					                        <?php endforeach; ?>
							            </div>
						            </dd>
						        </dl>
							</td>
						</tr>
						<tr>
							<td>竞彩篮球</td>
							<td>
								<dl class="simu-select select-small">
						            <dt>
						            <span class='_scontent' id="odd_43"><?php echo $rebate_odds['43'];?>%</span><i class="arrow"></i>
						            <input type='hidden' name='43' class="vcontent" value="<?php echo $rebate_odds['43'];?>" >
						            </dt>
						            <dd class="select-opt">
						            	<div class="select-opt-in" data-name='43'>
					                        <?php foreach( $oddType as $key => $val ): ?>
					                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?>%</a>
					                        <?php endforeach; ?>
							            </div>
						            </dd>
						        </dl>
							</td>
						</tr>
						<tr>
							<td>双色球</td>
							<td>
								<dl class="simu-select select-small">
						            <dt>
						            <span class='_scontent' id="odd_51"><?php echo $rebate_odds['51'];?>%</span><i class="arrow"></i>
						            <input type='hidden' name='51' class="vcontent" value="<?php echo $rebate_odds['51'];?>" >
						            </dt>
						            <dd class="select-opt">
						            	<div class="select-opt-in" data-name='51'>
					                        <?php foreach( $oddType as $key => $val ): ?>
					                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?>%</a>
					                        <?php endforeach; ?>
							            </div>
						            </dd>
						        </dl>
							</td>
						</tr>
						<tr>
							<td>大乐透</td>
							<td>
							<dl class="simu-select select-small">
						            <dt>
						            <span class='_scontent' id="odd_23529"><?php echo $rebate_odds['23529'];?>%</span><i class="arrow"></i>
						            <input type='hidden' name='23529' class="vcontent" value="<?php echo $rebate_odds['23529'];?>" >
						            </dt>
						            <dd class="select-opt">
						            	<div class="select-opt-in" data-name='23529'>
					                        <?php foreach( $oddType as $key => $val ): ?>
					                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?>%</a>
					                        <?php endforeach; ?>
							            </div>
						            </dd>
						        </dl>
							</td>
						</tr>
						<tr>
							<td>福彩3D</td>
							<td>
							<dl class="simu-select select-small">
						            <dt>
						            <span class='_scontent' id="odd_52"><?php echo $rebate_odds['52'];?>%</span><i class="arrow"></i>
						            <input type='hidden' name='52' class="vcontent" value="<?php echo $rebate_odds['52'];?>" >
						            </dt>
						            <dd class="select-opt">
						            	<div class="select-opt-in" data-name='52'>
					                        <?php foreach( $oddType as $key => $val ): ?>
					                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?>%</a>
					                        <?php endforeach; ?>
							            </div>
						            </dd>
						        </dl>
							</td>
						</tr>
						<tr>
							<td>七乐彩</td>
							<td>
								<dl class="simu-select select-small">
						            <dt>
						            <span class='_scontent' id="odd_23528"><?php echo $rebate_odds['23528'];?>%</span><i class="arrow"></i>
						            <input type='hidden' name='23528' class="vcontent" value="<?php echo $rebate_odds['23528'];?>" >
						            </dt>
						            <dd class="select-opt">
						            	<div class="select-opt-in" data-name='23528'>
					                        <?php foreach( $oddType as $key => $val ): ?>
					                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?>%</a>
					                        <?php endforeach; ?>
							            </div>
						            </dd>
						        </dl>
							</td>
						</tr>
						<tr>
							<td>新11选5</td>
							<td>
								<dl class="simu-select select-small">
						            <dt>
						            <span class='_scontent' id="odd_21407"><?php if($rebate_odds['21407'] > 0){ echo $rebate_odds['21407'];}else{ echo '0.0';}?>%</span><i class="arrow"></i>
						            <input type='hidden' name='21407' class="vcontent" value="<?php if($rebate_odds['21407'] > 0){ echo $rebate_odds['21407'];}else{ echo '0.0';}?>" >
						            </dt>
						            <dd class="select-opt">
						            	<div class="select-opt-in" data-name='21407'>
					                        <?php foreach( $oddType as $key => $val ): ?>
					                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?>%</a>
					                        <?php endforeach; ?>
							            </div>
						            </dd>
						        </dl>
							</td>
						</tr>
						<tr>
							<td>惊喜11选5</td>
							<td>
								<dl class="simu-select select-small">
						            <dt>
						            <span class='_scontent' id="odd_21408"><?php if($rebate_odds['21408'] > 0){ echo $rebate_odds['21408'];}else{ echo '0.0';}?>%</span><i class="arrow"></i>
						            <input type='hidden' name='21408' class="vcontent" value="<?php if($rebate_odds['21408'] > 0){ echo $rebate_odds['21408'];}else{ echo '0.0';}?>" >
						            </dt>
						            <dd class="select-opt">
						            	<div class="select-opt-in" data-name='21408'>
					                        <?php foreach( $oddType as $key => $val ): ?>
					                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?>%</a>
					                        <?php endforeach; ?>
							            </div>
						            </dd>
						        </dl>
							</td>
						</tr>
						<tr>
							<td>老时时彩</td>
							<td>
								<dl class="simu-select select-small">
						            <dt>
						            <span class='_scontent' id="odd_55"><?php if($rebate_odds['55'] > 0){ echo $rebate_odds['55'];}else{ echo '0.0';}?>%</span><i class="arrow"></i>
						            <input type='hidden' name='55' class="vcontent" value="<?php if($rebate_odds['55'] > 0){ echo $rebate_odds['55'];}else{ echo '0.0';}?>" >
						            </dt>
						            <dd class="select-opt">
						            	<div class="select-opt-in" data-name='55'>
					                        <?php foreach( $oddType as $key => $val ): ?>
					                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?>%</a>
					                        <?php endforeach; ?>
							            </div>
						            </dd>
						        </dl>
							</td>
						</tr>
						<tr>
							<td>江西快三</td>
							<td>
								<dl class="simu-select select-small">
						            <dt>
						            <span class='_scontent' id="odd_57"><?php if($rebate_odds['57'] > 0){ echo $rebate_odds['57'];}else{ echo '0.0';}?>%</span><i class="arrow"></i>
						            <input type='hidden' name='57' class="vcontent" value="<?php if($rebate_odds['57'] > 0){ echo $rebate_odds['57'];}else{ echo '0.0';}?>" >
						            </dt>
						            <dd class="select-opt">
						            	<div class="select-opt-in" data-name='57'>
					                        <?php foreach( $oddType as $key => $val ): ?>
					                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?>%</a>
					                        <?php endforeach; ?>
							            </div>
						            </dd>
						        </dl>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="pop-table">
				<table>
					<colgroup>
						<col width="96">
						<col width="150">
					</colgroup>
					<thead>
						<tr>
							<th>彩种</th>
							<th>返点比例</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>老11选5</td>
							<td>
								<dl class="simu-select select-small">
						            <dt>
						            <span class='_scontent' id="odd_21406"><?php echo $rebate_odds['21406'];?>%</span><i class="arrow"></i>
						            <input type='hidden' name='21406' class="vcontent" value="<?php echo $rebate_odds['21406'];?>" >
						            </dt>
						            <dd class="select-opt">
						            	<div class="select-opt-in" data-name='21406'>
					                        <?php foreach( $oddType as $key => $val ): ?>
					                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?>%</a>
					                        <?php endforeach; ?>
							            </div>
						            </dd>
						        </dl>
							</td>
						</tr>
						<tr>
							<td>七星彩</td>
							<td>
								<dl class="simu-select select-small">
						            <dt>
						            <span class='_scontent' id="odd_10022"><?php echo $rebate_odds['10022'];?>%</span><i class="arrow"></i>
						            <input type='hidden' name='10022' class="vcontent" value="<?php echo $rebate_odds['10022'];?>" >
						            </dt>
						            <dd class="select-opt">
						            	<div class="select-opt-in" data-name='10022'>
					                        <?php foreach( $oddType as $key => $val ): ?>
					                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?>%</a>
					                        <?php endforeach; ?>
							            </div>
						            </dd>
						        </dl>
							</td>
						</tr>
						<tr>
							<td>排列三</td>
							<td>
								<dl class="simu-select select-small">
						            <dt>
						            <span class='_scontent' id="odd_33"><?php echo $rebate_odds['33'];?>%</span><i class="arrow"></i>
						            <input type='hidden' name='33' class="vcontent" value="<?php echo $rebate_odds['33'];?>" >
						            </dt>
						            <dd class="select-opt">
						            	<div class="select-opt-in" data-name='33'>
					                        <?php foreach( $oddType as $key => $val ): ?>
					                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?>%</a>
					                        <?php endforeach; ?>
							            </div>
						            </dd>
						        </dl>
							</td>
						</tr>
						<tr>
							<td>排列五</td>
							<td>
								<dl class="simu-select select-small">
						            <dt>
						            <span class='_scontent' id="odd_35"><?php echo $rebate_odds['35'];?>%</span><i class="arrow"></i>
						            <input type='hidden' name='35' class="vcontent" value="<?php echo $rebate_odds['35'];?>" >
						            </dt>
						            <dd class="select-opt">
						            	<div class="select-opt-in" data-name='35'>
					                        <?php foreach( $oddType as $key => $val ): ?>
					                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?>%</a>
					                        <?php endforeach; ?>
							            </div>
						            </dd>
						        </dl>
							</td>
						</tr>
						<tr>
							<td>胜负彩</td>
							<td>
								<dl class="simu-select select-small">
						            <dt>
						            <span class='_scontent' id="odd_11"><?php echo $rebate_odds['11'];?>%</span><i class="arrow"></i>
						            <input type='hidden' name='11' class="vcontent" value="<?php echo $rebate_odds['11'];?>" >
						            </dt>
						            <dd class="select-opt">
						            	<div class="select-opt-in" data-name='11'>
					                        <?php foreach( $oddType as $key => $val ): ?>
					                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?>%</a>
					                        <?php endforeach; ?>
							            </div>
						            </dd>
						        </dl>
							</td>
						</tr>
						<tr>
							<td>任选九</td>
							<td>
								<dl class="simu-select select-small">
						            <dt>
						            <span class='_scontent' id="odd_19"><?php echo $rebate_odds['19'];?>%</span><i class="arrow"></i>
						            <input type='hidden' name='19' class="vcontent" value="<?php echo $rebate_odds['19'];?>" >
						            </dt>
						            <dd class="select-opt">
						            	<div class="select-opt-in" data-name='19'>
					                        <?php foreach( $oddType as $key => $val ): ?>
					                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?>%</a>
					                        <?php endforeach; ?>
							            </div>
						            </dd>
						        </dl>
							</td>
						</tr>
						<tr>
							<td>上海快三</td>
							<td>
								<dl class="simu-select select-small">
						            <dt>
						            <span class='_scontent' id="odd_53"><?php if($rebate_odds['53'] > 0){ echo $rebate_odds['53'];}else{ echo '0.0';}?>%</span><i class="arrow"></i>
						            <input type='hidden' name='53' class="vcontent" value="<?php if($rebate_odds['53'] > 0){ echo $rebate_odds['53'];}else{ echo '0.0';}?>" >
						            </dt>
						            <dd class="select-opt">
						            	<div class="select-opt-in" data-name='53'>
					                        <?php foreach( $oddType as $key => $val ): ?>
					                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?>%</a>
					                        <?php endforeach; ?>
							            </div>
						            </dd>
						        </dl>
							</td>
						</tr>	
						<tr>
							<td>快乐扑克</td>
							<td>
								<dl class="simu-select select-small">
						            <dt>
						            <span class='_scontent' id="odd_54"><?php if($rebate_odds['54'] > 0){ echo $rebate_odds['54'];}else{ echo '0.0';}?>%</span><i class="arrow"></i>
						            <input type='hidden' name='54' class="vcontent" value="<?php if($rebate_odds['54'] > 0){ echo $rebate_odds['54'];}else{ echo '0.0';}?>" >
						            </dt>
						            <dd class="select-opt">
						            	<div class="select-opt-in" data-name='54'>
					                        <?php foreach( $oddType as $key => $val ): ?>
					                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?>%</a>
					                        <?php endforeach; ?>
							            </div>
						            </dd>
						        </dl>
							</td>
						</tr>
						<tr>
							<td>吉林快三</td>
							<td>
								<dl class="simu-select select-small">
						            <dt>
						            <span class='_scontent' id="odd_56"><?php if($rebate_odds['56'] > 0){ echo $rebate_odds['56'];}else{ echo '0.0';}?>%</span><i class="arrow"></i>
						            <input type='hidden' name='56' class="vcontent" value="<?php if($rebate_odds['56'] > 0){ echo $rebate_odds['54'];}else{ echo '0.0';}?>" >
						            </dt>
						            <dd class="select-opt">
						            	<div class="select-opt-in" data-name='56'>
					                        <?php foreach( $oddType as $key => $val ): ?>
					                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?>%</a>
					                        <?php endforeach; ?>
							            </div>
						            </dd>
						        </dl>
							</td>
						</tr>
						<tr>
							<td>乐11选5</td>
							<td>
								<dl class="simu-select select-small">
						            <dt>
						            <span class='_scontent' id="odd_21421"><?php if($rebate_odds['21421'] > 0){ echo $rebate_odds['21421'];}else{ echo '0.0';}?>%</span><i class="arrow"></i>
						            <input type='hidden' name='21421' class="vcontent" value="<?php if($rebate_odds['21421'] > 0){ echo $rebate_odds['21421'];}else{ echo '0.0';}?>" >
						            </dt>
						            <dd class="select-opt">
						            	<div class="select-opt-in" data-name='21421'>
					                        <?php foreach( $oddType as $key => $val ): ?>
					                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?>%</a>
					                        <?php endforeach; ?>
							            </div>
						            </dd>
						        </dl>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="main-color" id="showMsg" class="display:none;">
		</div>
		<div class="pop-foot">
			<div class="btn-group">
				<input type="hidden" class="vcontent" name="id" value="<?php echo $uid;?>" />
                <a class="btn-pop-confirm submit" target="_self" href="javascript:;">提交</a>
                <a class="btn-pop-cancel cancel" target="_self" href="javascript:;">取消</a>
			</div>
		</div>
		<?php endif;?>
	</div>
</div>
<script>
$(function(){
	new cx.vform('.set-rebates', {
		renderTip: 'renderTips',
        submit: function(data) {
            var self = this;
            //验证  TODO
            $.ajax({
                type: 'post',
                url:  '/ajax/setRebateOdd',
                data: data,
                success: function(response) {
                    if(response.code == 0){
                        //成功
                    	cx.PopCom.hide('.set-rebates');
                    	$(".setRebate[data-val='"+response.data.id+"']").parents('tr').find('.bubble-tip').html(response.data.oddStr);
                    	$(".setRebate[data-val='"+response.data.id+"']").parents('tr').find('.bubble-tip').attr("tiptext", response.data.oddStr);
                    	new cx.Confirm({
                            content: '<div class="mod-result result-success"><div class="mod-result-bd"><i class="icon-result"></i><div class="result-txt"><h2 class="result-txt-title">恭喜您，操作成功</h2></div></div></div>',
                            btns: [
                                {
                                    type: 'confirm',
                                    txt: '确认',
                                    href: 'javascript:;'
                                }
                            ]
                        });
                    }else{
                        $('._scontent').removeClass('main-color');
                        var err = response.data || [];
                        for(var i=0; i < err.length; i++){
                            $('#odd_' + err[i]).addClass('main-color');
                        }
                        $('#showMsg').html(response.msg).show();
                    }
                }
            });
        }
    });
	$('.simu-select', '.pop-in').on('click', function(){
		var _this = $(this);
        var selectTop = $('.pop-in').height() - ($(this).offset().top - $('.pop-in').offset().top);
        var selectOpt =  $(this).find('.select-opt');
        if(selectTop < 180){
            selectOpt.css({'top': 'auto', 'bottom': '22px', 'z-index': 10});
        }
    });
});
</script>