<?php $this->load->view("templates/head") ?>
<?php 
	$mark = array(
		'android' => array(
			'通用尺寸<br>720X1060', 
			'通用尺寸<br>720X1060', 
			'通用尺寸<br>720X1060'
		),
		'ios' => array(
			'3.5-640X796', 
			'4-640X926', 
			'4.7-750X1088', 
			'5.5-1242X1800', 
			'5.8-1125X1962',
			'3.5-640X796', 
			'4-640X926', 
			'4.7-750X1088', 
			'5.5-1242X1800', 
			'5.8-1125X1962',
			'3.5-640X796', 
			'4-640X926', 
			'4.7-750X1088', 
			'5.5-1242X1800', 
			'5.8-1125X1962',
		)
	);
?>
<style>
.isorder {
	display:none;
	background-color: #f1f1f1;
}
.isorder span {
	color:#aaa;
}
.editing {
	display:none;
}
</style>
<div class="path">您的位置：客户端管理&nbsp;&gt;&nbsp;<a href="/backend/Appconfig/banner?platform=<?php echo $platform;?>"><?php echo $platform;?>配置</a></div>
<div class="mod-tab mt20">
	<div class="mod-tab-hd remind-hd">
		<ul>
			<?php $this->load->view("appconfig/tag", array('platform' => $platform, 'tag' => 'banner')) ?>
    	</ul>
    </div>
    <div class="mod-tab-bd">
	    <ul>
	      	<li style="display: block">
	    		<form action="/backend/Appconfig/banner/<?php echo $platform;?>" method="post" id="banner_form">
	    		     <div class="data-table-filter mt10">
                        <input type="checkbox" id="showorder"><label for="showorder">显示预约banner</label>
                    </div>
	    			<div class="data-table-list mt10">
	          			<table>
	            			<colgroup>
	              				<col width="3%" />
	              				<col width="4%" />
	              				<col width="16%" />
	              				<col width="24%" />
	              				<col width="19%" />
	              				<col width="5%" />
	              				<?php if(in_array($platform, array('android', 'ios'))): ?>
	              				<col width="5%" />
	              				<?php endif; ?>
	              				<col width="17%" />
	              				<col width="3%" />
	            			</colgroup>
		            		<thead>
			              		<tr>
			                		<th>序号</th>
			                		<th>权重</th>
			                		<th>标题（长度建议在<span class="cRed">10</span>个字以内）</th>
			                		<th>图片</th>
			                		<th>链接</th>
			                		<th>投注彩种</th>
			                		<?php if(in_array($platform, array('android', 'ios'))): ?>
		              				<th>渠道选择</th>
		              				<?php endif; ?>
		              				<th>上线期限</th>
			                		<th>操作</th>
			              		</tr>
		            		</thead>
	            			<tbody id="pic-table">
	            			    <?php foreach ($addInfo as $i => $ai) {?>
	            			    <tr <?php if ($ai['isorder']) {?>class="isorder"<?php }?>>
	            					<td><?php echo $i + 1;?></td>
	              					<td><input type="text" class="ipt w40 tac" name="banner[<?php echo $i?>][weight]" value="<?php echo $ai['weight']; ?>"><br> <?php if ($ai['isorder']) {echo '<span>预约</span>';}?></td>
	              					<td><input type="text" class="ipt tac w184" name="banner[<?php echo $i?>][imgTitle]" value="<?php echo $ai['imgTitle']; ?>"></td>
	              					<td>
	              						<div class="btn-white file">选择文件</div>
	                					<div class="btn-white upload1" data-index="<?php echo $i?>">开始上传</div>
	                					<input type="hidden" name="banner[<?php echo $i?>][imgUrl]" id="path_<?php echo $i?>" value="<?php echo $ai['imgUrl']?>">
	                					<div id="imgdiv0" class="imgDiv"><img id="imgShow<?php echo $i?>" src="<?php echo $ai['imgUrl']?>" width="100" height="50" /></div>
	              					</td>
	              					<td><input type="text" class="ipt tac w222" name="banner[<?php echo $i?>][hrefUrl]" value="<?php echo $ai['hrefUrl']?>"></td>
	              					<td>
	              						<?php 
	              						$lid = $ai['lid'];
	              						if(!empty($ai['extra'])) {
	              							$extra = json_decode($ai['extra'], true);
	              							if($extra['playType']) $lid .= '-' . $extra['playType'];
	              						}
	              						?>
	                					<input type="text" class="ipt tac w40" name="banner[<?php echo $i?>][lid]" value="<?php echo $lid; ?>">
	              					</td>
	              					<?php if(in_array($platform, array('android', 'ios'))): ?>
	              					<td>
	              						已选择<span><?php echo !(empty($ai['channels'])) ? count(explode(',', $ai['channels'])) : '0'; ?></span>个
	              						<br><a href="javascript:;" class="cBlue selectChannels">编辑选择</a>
	                					<input type="hidden" class="ipt tac w40" name="banner[<?php echo $i?>][channels]" value="<?php echo $ai['channels']?>">
	              					</td>
		              				<?php endif; ?>
		              				<td>
		              				<?php if (isset($ai) && !$ai['isorder']) {?>
		              				<input type="hidden" name='banner[<?php echo $i?>][start_time]' value="<?php echo $ai['start_time'] ?>">
		              				上线：<span class="ipt ipt-date w184"><input type="text" value="<?php echo $ai['start_time'] ?>" class="Wdate1" disabled><i></i></span><br>
                                    <?php } else {?>
                                                                                                                                上线：<span class="ipt ipt-date w184"><input type="text" name='banner[<?php echo $i?>][start_time]' value="<?php echo $ai['start_time'] ?>" class="Wdate1"><i></i></span><br>
                                    <?php }?>
                                                                                                                                下线：<span class="ipt ipt-date w184"><input type="text" name='banner[<?php echo $i?>][end_time]' value="<?php echo $ai['end_time'] ?>" class="Wdate1" /><i></i></span>
                                    </td>
	              					<td><a href="javascript:;" class="cBlue removeTr">清空</a><br><a href="javascript:;" class="cBlue copyTr">复制</a></td>
	            				</tr>
	            			    <?php }
	            			    if (empty($addInfo)) $i = -1;
	            			    for ($j = $i + 1; $j < $i + 6; $j++) {?>
	            				<tr class="editing">
	            					<td><?php echo $j + 1;?></td>
	              					<td><input type="text" class="ipt w40 tac" name="banner[<?php echo $j?>][weight]" ></td>
	              					<td><input type="text" class="ipt tac w184" name="banner[<?php echo $j?>][imgTitle]"></td>
	              					<td>
	              						<div class="btn-white file">选择文件</div>
	                					<div class="btn-white upload1" data-index="<?php echo $j?>">开始上传</div>
	                					<input type="hidden" name="banner[<?php echo $j?>][imgUrl]" id="path_<?php echo $j?>">
	                					<div id="imgdiv0" class="imgDiv"><img id="imgShow<?php echo $j?>" width="100" height="50" /></div>
	              					</td>
	              					<td><input type="text" class="ipt tac w222" name="banner[<?php echo $j?>][hrefUrl]"></td>
	              					<td>
	              						<?php 
	              						$lid = $ai['lid'];
	              						if(!empty($ai['extra'])) {
	              							$extra = json_decode($ai['extra'], true);
	              							if($extra['playType']) $lid .= '-' . $extra['playType'];
	              						}
	              						?>
	                					<input type="text" class="ipt tac w40" name="banner[<?php echo $j?>][lid]">
	              					</td>
	              					<?php if(in_array($platform, array('android', 'ios'))): ?>
	              					<td>已选择<span>0</span>个<br><a href="javascript:;" class="cBlue selectChannels">编辑选择</a><input type="hidden" class="ipt tac w40" name="banner[<?php echo $j?>][channels]"></td>
		              				<?php endif; ?>
		              				<td>
                                                                                                                                上线：<span class="ipt ipt-date w184"><input type="text" name='banner[<?php echo $j?>][start_time]' class="Wdate1"><i></i></span><br>
                                                                                                                                下线：<span class="ipt ipt-date w184"><input type="text" name='banner[<?php echo $j?>][end_time]' class="Wdate1" /><i></i></span>
                                    </td>
	              					<td><a href="javascript:;" class="cBlue removeTr">清空</a><br><a href="javascript:;" class="cBlue pasteTr">粘贴</a></td>
	            				</tr>
	            				<?php }?>
	            			</tbody>
	          			</table>
	          			<div class="tac">
	          				<a class="btn-blue mt20 submitBanner">保存并上线</a>
	          			</div>
	          		</div>
	          		<input type="hidden" name="platform" value="<?php echo $platform; ?>">
	       		</form>
	       	</li>
	       	<!-- M版不显示启动页配置 -->
	       	<?php if(in_array($platform, array('android'))):?>
	       	<li style="display: block">
	    		<form action="/backend/Appconfig/banner/<?php echo $platform;?>" method="post" id="prelaod_form">
	    			<div class="data-table-list mt10">
	          			<table>
	            			<colgroup><col width="5%"><col width="15%"><col width="23%"><col width="18%"><col width="4%"><col width="5%"><col width="17%"><col width="5%"></colgroup>
		            		<thead>
			              		<tr>
			                		<th>尺寸</th>
			                		<th>标题（长度建议在<span class="cRed">10</span>个字以内）</th>
			                		<th>图片</th>
			                		<th>链接</th>
			                		<th>投注彩种</th>
			                		<th>渠道选择</th>
			                		<th>预约上线期限</th>
			                		<th>操作</th>
			              		</tr>
		            		</thead>
	            			<tbody id="pic-table">
	            				<?php for ($cid = 1; $cid <= 3; $cid++){
	            				    $row = $preloadInfo[$cid][0]?>
	            				<tr>
	            					<td><?php echo $mark[$platform][$cid - 1];?></td>
	              					<td><input name="prelaod[<?php echo $cid?>][title]" class="freeze" value="<?php echo !empty($row) ? $row['title'] : "开屏广告图 - ".$cid; ?>"></td>
	              					<td>
	              						<div class="btn-white file">选择文件</div>
	                					<div class="btn-white upload2" data-index="<?php echo $cid?>">开始上传</div>
	                					<input type="hidden" name="prelaod[<?php echo $cid?>][imgUrl]" id="prePath_<?php echo $cid?>" value="<?php echo $row['imgUrl']?>">
	                					<div id="imgdiv0" class="imgDiv"><img id="preImgShow<?php echo $cid?>" src="<?php echo $row['imgUrl']?>" width="100" height="50" /></div>
	              					</td>
	              					<td><input type="text" class="ipt tac w222" name="prelaod[<?php echo $cid?>][url]" value="<?php echo $row['url']?>"></td>
	              					<td>
	              						<?php 
	              						$lid = $row['lid'];
	              						if(!empty($row['extra'])) {
	              							$extra = json_decode($row['extra'], true);
	              							if($extra['playType']) $lid .= '-' . $extra['playType'];
	              						}
	              						?>
	                					<input type="text" class="ipt tac w40" name="prelaod[<?php echo $cid?>][lid]" value="<?php echo $lid; ?>">
	              					</td>
	              					<td>
	              						已选择<span><?php echo !(empty($row['channels'])) ? count(explode(',', $row['channels'])) : '0'; ?></span>个<br>
	              						<a href="javascript:;" class="cBlue selectChannels">编辑选择</a>
	                					<input type="hidden" class="ipt tac w40" name="prelaod[<?php echo $cid?>][channels]" value="<?php echo $row['channels']?>">
	              					</td>
	              					<td>
	              					<?php if (!empty($row)) {?>
	              					<input type="hidden" name='prelaod[<?php echo $cid?>][start_time]' value="<?php echo $row['start_time'] ?>">
		              				    上线：<span class="ipt ipt-date w184"><input type="text" name='prelaod[<?php echo $cid?>][start_time]' value="<?php echo $row['start_time'] ?>" class="Wdate1" disabled><i></i></span><br>
	            				    <?php } else {?>
		              				    上线：<span class="ipt ipt-date w184"><input type="text" name='prelaod[<?php echo $cid?>][start_time]' class="Wdate1"><i></i></span><br>
	              					<?php }?>
                                                                                                                                下线：<span class="ipt ipt-date w184"><input type="text" name='prelaod[<?php echo $cid?>][end_time]' value="<?php echo $row['end_time'] ?>" class="Wdate1" /><i></i></span>
                                    </td>
	              					<td>
	              					    <input type="hidden" class="ipt tac w184 freeze" name="prelaod[<?php echo $cid?>][id]" value="<?php echo $row['id']; ?>">
	              						<input type="hidden" class="ipt tac w184 freeze" name="prelaod[<?php echo $cid?>][cid]" value="<?php echo $cid; ?>">
	                					<a href="javascript:;" class="cBlue removeTrAndroid" data-index="<?php echo $cid?>">清空</a><br>
	                					<a href="/backend/Appconfig/bannerorder/android/<?php echo $cid?>" target="_blank" class="cBlue">查看预约(<?php echo (int)$preloadInfo[$cid]['ordercount']?>)</a>
	              					</td>
	            				</tr>
	            				<?php }?>
	            			</tbody>
	          			</table>
	          			<div class="tac">
	          				<a class="btn-blue mt20 submitPrelaod" id="preload_banner">保存并上线</a>
	          			</div>
	          		</div>
	          		<input type="hidden" name="platform" value="<?php echo $platform; ?>">
	       		</form>
	       	</li>
	       <?php elseif(in_array($platform, array('ios'))):?>
	       	<li style="display: block">
	    		<form action="/backend/Appconfig/banner/ios" method="post" id="prelaod_form">
	    			<div class="data-table-list mt10">
	          			<table>
	            			<colgroup><col width="7%"><col width="15%"><col width="24%"><col width="19%"><col width="5%"><col width="5%"><col width="17%"><col width="5%"></colgroup>
		            		<thead>
			              		<tr>
			                		<th>尺寸</th>
			                		<th>标题（长度建议在<span class="cRed">10</span>个字以内）</th>
			                		<th>图片</th>
			                		<th>链接</th>
			                		<th>投注彩种</th>
			                		<th>渠道选择</th>
			                		<th>预约上线期限</th>
			                		<th>操作</th>
			              		</tr>
		            		</thead>
	            			<tbody id="pic-table">
	            			<?php for ($cid = 1; $cid <= 3; $cid++) {
	            			    for ($i = 0; $i < 5; $i++) {
	            			    $j = ($cid - 1) * 5 + $i?>
	            			    <tr class="cid_<?php echo $cid?>">
	            					<td>
	                					<?php echo $mark[$platform][$j];?>
	                					<input type="hidden" class="ipt tac w184 freeze" name="prelaod[<?php echo $j?>][cid]" value="<?php echo $cid?>">
	                					<input type="hidden" class="ipt tac w184 freeze" name="prelaod[<?php echo $j?>][id]" value="<?php echo $preloadInfo[$cid][$i]['id']; ?>">
	              					</td>
	              					<td>
	                					<input name="prelaod[<?php echo $j?>][title]" style="width:170px" class="freeze" value="<?php echo isset($preloadInfo[$cid][$i]) ? $preloadInfo[$cid][$i]['title'] : "开屏广告图 ".$mark[$platform][$j]."- ".$cid; ?>">
	              					</td>
	              					<td>
	              						<div class="btn-white file">选择文件</div>
	                					<div class="btn-white upload2" data-index="<?php echo $j?>">开始上传</div>
	                					<input type="hidden" name="prelaod[<?php echo $j?>][imgUrl]" id="prePath_<?php echo $j?>" value="<?php echo $preloadInfo[$cid][$i]['imgUrl']?>">
	                					<div id="imgdiv0" class="imgDiv"><img id="preImgShow<?php echo $j?>" src="<?php echo $preloadInfo[$cid][$i]['imgUrl']?>" width="100" height="50" /></div>
	              					</td>
	              					<?php if($i == 0): ?>
	              					<td rowspan="5"><input type="text" class="ipt tac w222" name="prelaod[<?php echo $j?>][url]" value="<?php echo $preloadInfo[$cid][$i]['url']?>"></td>
	              					<td rowspan="5">
	              						<?php 
	              						$lid = $preloadInfo[$cid][$i]['lid'];
	              						if(!empty($preloadInfo[$cid][$i]['extra']))
	              						{
	              							$extra = json_decode($preloadInfo[$cid][$i]['extra'], true);
	              							if($extra['playType']) $lid .= '-' . $extra['playType'];
	              						}
	              						?>
	                					<input type="text" class="ipt tac w40" name="prelaod[<?php echo $j?>][lid]" value="<?php echo $lid; ?>">
	              					</td>
	              					<td rowspan="5">
	              						已选择<span><?php echo !(empty($preloadInfo[$cid][$i]['channels'])) ? count(explode(',', $preloadInfo[$cid][$i]['channels'])) : '0'; ?></span>个
	              						<a href="javascript:;" class="cBlue selectChannels">编辑选择</a>
	                					<input type="hidden" class="ipt tac w40" name="prelaod[<?php echo $j?>][channels]" value="<?php echo $preloadInfo[$cid][$i]['channels']?>">
	              					</td>
	              					<td rowspan="5">
	              					<?php if (isset($preloadInfo[$cid][$i])) {?>
	              					<input type="hidden" name='prelaod[<?php echo $i?>][start_time]' value="<?php echo $preloadInfo[$cid][$i]['start_time'] ?>">
		              				    上线：<span class="ipt ipt-date w184"><input type="text" name='prelaod[<?php echo $i?>][start_time]' value="<?php echo $preloadInfo[$cid][$i]['start_time'] ?>" class="Wdate1" disabled><i></i></span><br>
	            			        <?php } else {?>
	            			                                 上线：<span class="ipt ipt-date w184"><input type="text" name='prelaod[<?php echo $i?>][start_time]' class="Wdate1"><i></i></span><br>
	              					<?php }?>
                                                                                                                                下线：<span class="ipt ipt-date w184"><input type="text" name='prelaod[<?php echo $j?>][end_time]' value="<?php echo $preloadInfo[$cid][$i]['end_time'] ?>" class="Wdate1" /><i></i></span>
                                    </td>
	              					<td rowspan="5">
	                					<a href="javascript:;" class="cBlue removeTrIos" data-index="<?php echo $cid?>">清空</a><br>
	                					<a href="/backend/Appconfig/bannerorder/ios/<?php echo $cid?>" target="_blank" class="cBlue">查看预约(<?php echo (int)$preloadInfo[$cid]['ordercount'] / 5?>)</a>
	              					</td>
	              					<?php endif; ?>
	            				</tr>
	            			    <?php }?>
	            			<?php }?>

	            			</tbody>
	          			</table>
	          			<div class="tac">
	          				<a class="btn-blue mt20 submitPrelaod" id="preload_banner">保存并上线</a>
	          			</div>
	          		</div>
	          		<input type="hidden" name="platform" value="<?php echo $platform; ?>">
	       		</form>
	       	</li>
	       <?php endif; ?>
	    </ul>
    </div>
    <!-- 渠道模块 start -->
  	<div class="pop-dialog chooseSource" id="chooseChannel" style="display:none;">
		<div class="pop-in">
			<div class="pop-head">
				<h2>涉及渠道选择</h2>
				<span class="pop-close" title="关闭">关闭</span>
			</div>
			<div class="pop-body">
				<div class="del-percent padding overflow-y">
					<table>
						<colgroup>
							<col width="80">
							<col>
						</colgroup>
						<tbody>
							<tr>
								<th class="tar" style="vertical-align: top;">标题：</th>
								<td id="channelTitle"></td>
							</tr>
							<tr>
								<th class="tar" style="vertical-align: top;">按应用选择：</th>
								<td>
									<?php if(!empty($channels)): ?>
									<?php foreach ($channels['package'] as $items):?>
									<label for="package"><input type="checkbox" name="packageTag" data-type="tag" data-index="<?php echo $items['package']; ?>" class="ckbox"><?php echo $items['pname']; ?></label>
									<?php endforeach;?>
									<?php endif;?>
									<a href="javascript:" class="mr10 selectAll">全选</a>
									<a href="javascript:" class="cancelAll">清空</a>
								</td>
							</tr>
							<tr>
								<th class="tar" style="vertical-align: top;">按渠道选择：</th>
								<td>
									<?php if(!empty($channels)): ?>
									<?php foreach ($channels['detail'] as $items):?>
									<label for="source"><input type="checkbox" name="sourceTag" class="ckbox package_<?php echo $items['package']; ?>" data-type="source" data-index="<?php echo $items['id']; ?>"><?php echo $items['name']; ?></label>
									<?php endforeach;?>
									<?php endif;?>
									<a href="javascript:" class="mr10 selectSource">全选</a>
									<a href="javascript:" class="cancelSource">清空</a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="pop-foot tac">
				<a href="javascript:;" class="btn-blue-h32 mlr15 confirmChannel" data-index="">确认</a>
				<a href="javascript:closePop();" class="btn-b-white">关闭</a>
			</div>
		</div>
	</div>
	<!-- 渠道模块 end -->
</div>
<script src="/source/js/webuploader.min.js"></script>
<script  src="/source/date/WdatePicker.js"></script>
<script>
    var delcid = [];
	<?php if ($timeerror) {?>
	alert('上线时间冲突！');
	<?php }?>
	<?php if ($notfull) {?>
	alert('请将方案内容填写完整！');
	<?php }?>
	$(function() {
		var channelList = '<?php echo json_encode($channels['detail']); ?>';
		// 表单提交
		$(".submitBanner").click(function(){
			var notime = false, dataArr = {}
			$('#banner_form tbody tr').each(function(){
				var index = $(this).find('td:eq(0)').html(), priority = $(this).find('td:eq(1) input').val(), 
				<?php if (in_array($platform, array('android', 'ios'))) {?>
				start = $(this).find('td:eq(7) input:first').val(), end = $(this).find('td:eq(7) input:last').val();
				<?php }else {?>
				start = $(this).find('td:eq(6) input:first').val(), end = $(this).find('td:eq(6) input:last').val();
				<?php }?>
				if (priority && (!start || !end)) {
					alert('请设置上下线时间！');
					notime = true;
					return false
				}
				if (priority !== '') {
					if (!(priority in dataArr)) dataArr[priority] = [];
					var startval = (new Date(start)).valueOf(), endval = (new Date(end)).valueOf();
					if (startval >= endval) {
						alert('下线时间不可大于上线时间！');
						notime = true;
						return false
					}
					dataArr[priority].push([startval, endval, index]);
				}
			})
			if (notime) return;
			var repeat = [];
			$.each (dataArr, function(k, data) {
				if (data.length > 0) {
					$.each(data, function(k0, val0){
						$.each(data, function(k1, val1){
							if (k0 != k1 && ((val0[0] < val1[0] && val0[1] > val1[0]) || (val0[0] >= val1[0] && val1[1] > val0[0]) || (val0[0] == val1[0] && val1[1] == val0[0]))) {
								if (repeat.indexOf(val0[2]) == -1) repeat.push(val0[2])
								if (repeat.indexOf(val1[2]) == -1) repeat.push(val1[2])
							}
						})
					})
					if (repeat.length > 0) {
	        			alert('方案'+repeat.join('、')+'上线时间冲突')
	        			return false;
	        		}
				}
			})
			if (repeat.length > 0) return ;
			$("#banner_form").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").submit();
		})
				
		$(".Wdate1").focus(function(){
            dataPicker();
        });

		$(".submitPrelaod").click(function(){
			// 渠道信息检查
			var text = checkActivityChannel();
            if(text){
                var msg = '渠道' + text + '请不要同时启用2个以上';
                alert(msg);
                return false;
            }
            error = false;
            var imgUrl, start, end;
            $('#prelaod_form tbody tr').each(function(k, e){
                imgUrl = $(this).find('td:eq(2) input:last').val();
                if ($(this).find('td:eq(6)').length) {
                	start = $(this).find('td:eq(6) input:eq(-2)').val();
                	end = $(this).find('td:eq(6) input:last').val();
                }
                if ((imgUrl || start || end) && (!imgUrl || !start || !end)) {
                	alert('请将方案内容填写完整！');
                	error = true;
                	return false;
                }
				if ((new Date(start)).valueOf() >= (new Date(end)).valueOf()) {
					alert('下线时间不可大于上线时间！');
					error = true;
					return false
				}
				if (imgUrl && start && end) {
					var c = $(this).find('td:last .removeTrAndroid').data('index') || $(this).find('td:last .removeTrIos').data('index');
					if (c) {
						var j = $.inArray(c, delcid);
						if (j > -1) delete delcid[j];
					}
				}
            })
            if (error) return false;
			$("#prelaod_form").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'><input type='hidden' name='delcid' value='"+delcid.join(',')+"'>").submit();
		})

		// 轮播图、启动页 - 初始化
        var uploader = WebUploader.create({
            swf: '/caipiaoimg/v1.1/js/jUploader.swf',
            pick: '.file',
        }), files;

        // 轮播图 - 上传
        $(".upload1").click(function(){
            var platform = $('input[name="platform"]').val();
            uploader.options.server = "/backend/Appconfig/uploadbanner/" + platform + "/" + $(this).data('index');
            files = uploader.getFiles();
            var index = files.length - 1;
            // 分割文件名
            if(!(/^\w+\.\w+$/.test(files[index].name))){
                alert('文件名只能包含字母和数字！');
                uploader.removeFile(files);
                return false;
            }
            uploader.upload();
        })

        // 启动页 - 上传
        $(".upload2").click(function(){
            var platform = $('input[name="platform"]').val();
            uploader.options.server = "/backend/Appconfig/uploadbanner/" + platform + "/" + $(this).data('index') + "/qdy";
            files = uploader.getFiles();
            var index = files.length - 1;
            // 分割文件名
            if(!(/^\w+\.\w+$/.test(files[index].name))){
                alert('文件名只能包含字母和数字！');
                uploader.removeFile(files);
                return false;
            }
            uploader.upload();
        })

        // 轮播图、启动页 - 上传成功
        uploader.on( 'uploadSuccess', function( file, data) {
        	if(data.type == 'qdy'){
        		$("#preImgShow" + data.index).attr('src', data.path + data.name);
            	$("#prePath_" + data.index).val(data.path + data.name);
        	}else{
        		$("#imgShow" + data.index).attr('src', data.path + data.name);
            	$("#path_" + data.index).val(data.path + data.name);
        	}
        	uploader.removeFile(files);
        });

		// 清空
		$(".removeTr").click(function(){
			$(this).parents('tr').find('input:not(.freeze)').val('').filter('[disabled=disabled]').removeAttr('disabled');
			$(this).parents('tr').find('img').attr('src', '');
		})
		
		$(".removeTrAndroid").click(function(){
			var i = $(this).data('index');
			if ($.inArray(i, delcid) === -1) delcid.push(i);
			$(this).parents('tr').find('input:not(.freeze)').val('').filter('[disabled=disabled]').removeAttr('disabled');
			$(this).parents('tr').find('img').attr('src', '');
		})

		// IOS启动图清空
		$(".removeTrIos").click(function(){
			var cid = $(this).data('index');
			if ($.inArray(cid, delcid) === -1) delcid.push(i); delcid.push(cid);
			$('.cid_' + cid + '').find('input:not(.freeze)').val('').filter('[disabled=disabled]').removeAttr('disabled');
			$('.cid_' + cid + '').find('img').attr('src', '');
		})

		// 显示切换
		$(".isShow").click(function(){
			var flagVal = $("#flag" + $(this).data('index')).val();
			if(flagVal == '0'){
				$(".showFlag").val('1');
				$(".isShow").html('显示');
			}else{
				$(".showFlag").val('0');
				$(".isShow").html('不显示');
			}
		})

		// 渠道选择
		$(".selectChannels").click(function(){
			var _this = $(this);
			channelPopInit(_this);
			popdialog("chooseChannel");
		})

		// 渠道包交互
		$(".ckbox").click(function(){
			var type = $(this).data('type');
			var checked = $(this).prop('checked');
			if(type == 'tag'){
				var packageId = $(this).data('index');
				if(checked){
					// 勾选所有关联
					$(".package_" + packageId).prop('checked', true);
				}else{
					// 取消所有关联
					$(".package_" + packageId).prop('checked', false);
				}
			}
		})

		// 初始化
		function channelPopInit(_this){
			// 清空
			$("#chooseChannel").find(':checkbox').each(function(){
				$(this).attr("checked", false);
			});
			$(".confirmChannel").data('index', '');

			var channels = _this.closest('td').find('input').val();
			if(channels){
				var channelArr = channels.split(",");
				$('#chooseChannel input[name="sourceTag"]').each(function(){
					var id = $(this).data('index').toString();
					if($.inArray(id,channelArr) >= 0){
						$(this).prop('checked', true);
					}
				})
			}
			var tag = _this.siblings('input').attr('name');
			$(".confirmChannel").data('index', tag);
			// 设置标题
			var title = '';
			if(tag.indexOf('banner') >= 0){
				title = _this.closest('tr').find('td').eq(2).find('input').val();
			}else{
				title = _this.closest('tr').find('td').eq(1).find('input').val();
			}
			$("#channelTitle").html(title);
		}

		// 确认
		$(".confirmChannel").click(function(){
			var tag = $(this).data('index');
			var channelArr = [];
			$('#chooseChannel input[name="sourceTag"]:checked').each(function(){
				channelArr.push($(this).data('index'));
			})
			var tag = $(".confirmChannel").data('index');
			if(tag){
				// 数据填入
				$('input[name="' + tag + '"]').val(channelArr.join(','));
				$('input[name="' + tag + '"]').siblings('span').html(channelArr.length);
			}
			closePop();
		})

		$(".selectAll").click(function(){
            $("#chooseChannel").find(':checkbox').each(function(){
                $(this).attr("checked", true);
            });
        })

        $(".cancelAll").click(function(){
            $("#chooseChannel").find(':checkbox').each(function(){
                $(this).attr("checked", false);
            });
        })

        $(".selectSource").click(function(){
            $('#chooseChannel input[name="sourceTag"]').each(function(){
                $(this).attr("checked", true);
            });
        })

        $(".cancelSource").click(function(){
            $('#chooseChannel input[name="sourceTag"]').each(function(){
                $(this).attr("checked", false);
            });
        })

        function checkActivityChannel(){
        	var text = [];
        	var diff = [];
			var list = [];
			$("#prelaod_form .selectChannels").each(function(){
				var channelArr = $(this).parents('td').find('input').val().split(',');
				if(channelArr.length > 0){
					for (var i = 0; i < channelArr.length; i++) {
						var val = channelArr[i];
						if(list[val] == undefined){
	                        list[val] = 1;
	                    }else{
	                        list[val] += 1;
	                    }
					}
				}
			});

			if(list.length > 0){
				$.each(list, function(x, v){
                    if(v != undefined && v > 1){
                        diff.push(x);
                    }
                });
			}

			// 获取渠道映射
            var channelMaps = [];
            $.each($.parseJSON(channelList), function(index, items){
                channelMaps[items['id']] = items['name'];
            });
            
            // 去重
            diff = $.unique(diff);

            if(diff.length > 0){
                for (var index = 0; index < diff.length; index++) {
                    text.push(channelMaps[diff[index]]);
                }
            }

            return text.join(',');
        }
	});

	$(".copyTr").click(function(){
		var tr = $(this).parents('tr'), priority = tr.find('td:eq(1) input').val(), title = tr.find('td:eq(2) input').val(),
		path = tr.find('td:eq(3) input[name^=banner]').val(), url = tr.find('td:eq(4) input').val(), lid = tr.find('td:eq(5) input').val(), 
		chlnum = tr.find('td:eq(6) span:first').html(), channels = tr.find('td:eq(6) input').val();
		<?php if (in_array($platform, array('android', 'ios'))) {?>
		window.localStorage['banner'] = priority+'|'+title+'|'+path+'|'+url+'|'+lid+'|'+chlnum+'|'+channels;
		<?php } else {?>
		window.localStorage['banner'] = priority+'|'+title+'|'+path+'|'+url+'|'+lid;
		<?php }?>
		
	})

	$(".pasteTr").click(function(){
		var arr = window.localStorage['banner'].split('|'), tr = $(this).parents('tr');
		tr.find('td:eq(1) input').val(arr[0]);
		tr.find('td:eq(2) input').val(arr[1]);
		tr.find('td:eq(3) input[name^=banner]').val(arr[2]);
		tr.find('td:eq(3) img').attr('src', arr[2]);
		tr.find('td:eq(4) input').val(arr[3]);
		tr.find('td:eq(5) input').val(arr[4]);
		<?php if (in_array($platform, array('android', 'ios'))) {?>
		tr.find('td:eq(6) span:first').html(arr[5]);
		tr.find('td:eq(6) input').val(arr[6]);
		<?php }?>
	})

	$("#showorder").click(function(){
		$('#banner_form tbody tr').show();
		if(!$(this).attr('checked')) $('.isorder, .editing').hide();	
	})
</script>
