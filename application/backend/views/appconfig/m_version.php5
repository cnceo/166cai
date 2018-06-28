<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：客户端管理&nbsp;&gt;&nbsp;<a href="/backend/Appconfig/banner?platform=<?php echo $platform;?>"><?php echo $platform;?>配置</a></div>
<div class="mod-tab mt20">
	<div class="mod-tab-hd">
        <ul>
        	<?php $this->load->view("appconfig/tag", array('platform' => $platform, 'tag' => 'mindex')) ?>
        </ul>
    </div>
    <div class="mod-tab-bd">
	    <ul>
	      	<li style="display: block">
	    		<form action="" method="post" id="lottery_form">
	    			<div class="data-table-list mt10">
	          			<table>
	            			<colgroup>
	              				<col width="5%" />
	              				<col width="8%" />
	              				<col width="5%" />
	              				<col width="7%" />
	              				<col width="8%" />
	              				<col width="17%" />
	              				<col width="10%" />
	              				<col width="10%" />
	            			</colgroup>
		            		<thead>
			              		<tr>
			                		<th>序号</th>
			                		<th>权重</th>
			                		<th>彩种ID</th>
			                		<th>名称</th>
			                		<th>图片</th>
			                		<th>副标题</th>
			                		<th>附加标识位</th>
			                		<th>是否显示</th>
			              		</tr>
		            		</thead>
	            			<tbody id="pic-table" class="lotteryConfigTab">
								<?php if(!empty($lotteryInfo['baseInfo'])):?>
                            	<?php foreach ($lotteryInfo['baseInfo'] as $key => $items):?>
	            				<tr <?php if($items['plid'] > 0): ?> style="display: none" class="more_<?php echo $items['plid']; ?>" id="<?php echo $items['lid']; ?>" <?php endif;?> >
	            					<td>
	                					<?php echo $key + 1;?>
	                					<input type="hidden" class="ipt w40 tac" name="lottery[<?php echo $key?>][lid]" value="<?php echo $items['lid']; ?>" readonly>
	              					</td>
	              					<td>
	                					<input type="text" class="ipt w40 tac" name="lottery[<?php echo $key?>][weight]" value="<?php echo $items['weight']; ?>">
	              					</td>
	              					<td>
	                					<?php echo $items['lid']; ?>
	              					</td>
	              					<td><?php echo $items['lname'];?></td>
	              					<td>
	                					<div><img src="<?php echo $items['logUrl']; ?>" width="50" height="50" /></div>
	              					</td>
	              					<td>
	                					<input type="text" class="ipt tac w222" name="lottery[<?php echo $key?>][memo]" value="<?php echo $items['memo']; ?>">
	              					</td>
	              					<td>
	              					<?php foreach ($attachFlags as $attchId => $val):?>
	              						<input type="checkbox" <?php if ($items['attachFlag'] & $attchId) {?>checked<?php }?> value="<?php echo $attchId;?>" name="lottery[<?php echo $key;?>][attachFlag][]"><?php echo $val;?>
	              					<?php endforeach;?>
	              					</td>
	              					<td>
	                					<a class="btn-<?php echo $items['delect_flag']?'red':'blue'; ?> showLottery" id="showLottery" data-index="<?php echo $key?>"><?php echo $items['delect_flag']?'不显示':'显示'; ?></a><input type="hidden" id="flag<?php echo $key?>" name="lottery[<?php echo $key?>][delect_flag]" value="<?php echo $items['delect_flag']; ?>">

	              					</td>
	            				</tr>
	            				<?php endforeach;?>
                            	<?php endif; ?>
	            			</tbody>
	          			</table>
	          			<div class="tac">
	          				<a class="btn-blue mt20 mr20 review-lottery">保存并预览</a>
	          				<a class="btn-blue mt20 submit-lottery">上线</a>
	          			</div>
	          		</div>
	          		<input type="hidden" name="subType" value="1">
	          		<input type="hidden" name="platform" value="<?php echo $platform; ?>">
	          		<input type="hidden" name="reviewCheck" value="0">
	       		</form>
	       	</li>
	    </ul>
    </div>
    <div class="pop-mask" style="display:none;width:200%"></div>
</div>
<script>
	$(function() {
		// 显示切换
		$('.showLottery').click(function(){
			var flagVal = $("#flag" + $(this).data('index')).val();
			if(flagVal == '0'){
				$("#flag" + $(this).data('index')).val('1');
				$(this).html('不显示');
			}else{
				$("#flag" + $(this).data('index')).val('0');
				$(this).html('显示');
			}
		});

		// 修改彩种
		$(".submit-lottery").click(function(){
			// 预览检查
			var checked = $('input[name="reviewCheck"]').val();
			if(checked == 0){
				alert('请先预览确认后再上线');
				return false;
			}
			$("#lottery_form").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").submit();
		})

		$('.showAlert').click(function(){
			var flagVal = $('input[name="showAlert"]').val();
			change2(flagVal, '0');
		});

		// 版本配置 - 预览
		$('.review-lottery').click(function(){
			var platform = $('input[name="platform"]').val();
			var list = [];
			var qz = [];
			$('.lotteryConfigTab').find('tr').each(function () {
				var tdArr = $(this).children();
				// 加奖标识
				var val = [];
				tdArr.eq(6).find('input').each(function (item, idx) {
					if ($(this).prop('checked')) {
						val.push(1)
					} else {
						val.push(0)
					}
				});

				// 显示
				var isShow = tdArr.eq(7).find('input').val();
				if(isShow > 0){
					isShow = false;
				}else{
					isShow = true;
				}

				// 父类
				var lid = tdArr.eq(0).find('input').val();
				if(lid == 2 || lid == 3){
					qz[lid] = tdArr.eq(1).find('input').val();
					var isSeries = true;
				}else{
					var isSeries = false;
				}

				// 子类
				var plid = tdArr.eq(8).find('input').val();
				if(plid > 0){
					var child = true;
					var parentLid = plid;
				}else{
					var child = false;
					var parentLid = '';
				}

				var data = {
					qz: tdArr.eq(1).find('input').val(),
		            name: tdArr.eq(3).text(),
		            des: tdArr.eq(5).find('input').val(),
		            imgsrc: tdArr.eq(4).find('img').attr("src"),
		            jj: val[0],
		            desMark: val[1],
		            isShow: isShow,
		            isSeries: isSeries,
		            isOpen: true,
		            lid: lid,
		            parentLid: parentLid,
				}

				if(plid > 0 && qz[plid] >= 0){
					data.parentQz = qz[plid];
				}

				list.push(data);
			})
			// 更新检查
			$('input[name="reviewCheck"]').val('1');
			// 保存localStorage
			window.localStorage.removeItem(platform)
			window.localStorage.setItem(platform, JSON.stringify(list))
			// 跳转预览页
			window.open('/backend/Appconfig/review/' + platform);
		});
	});
</script>
