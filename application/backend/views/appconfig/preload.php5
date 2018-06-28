<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：信息管理&nbsp;&gt;&nbsp;<a href="/backend/Appconfig/banner?platform=android">APP配置</a></div>
<div class="mod-tab mt20">
	<div class="mod-tab-hd remind-hd">
        <ul>
            <li><a href="/backend/Appconfig/banner/android">Android轮播图</a></li>
            <li><a href="/backend/Appconfig/banner/ios">Ios轮播图</a></li>
            <li><a href="/backend/Appconfig/banner/m">M版轮播图</a></li>    
            <li <?php if($platform == 'android'){echo 'class="current"';} ?>><a href="/backend/Appconfig/preload/android">Android启动页</a></li>
            <li <?php if($platform == 'ios'){echo 'class="current"';} ?>><a href="/backend/Appconfig/preload/ios">Ios启动页</a></li>
            <li><a href="/backend/Appconfig/version/android">Android版本配置</a></li>
            <li><a href="/backend/Appconfig/version/ios">Ios版本配置</a></li>
            <li><a href="/backend/Appconfig/webBanner/android">Android支付成功页广告</a></li>
            <li><a href="/backend/Appconfig/webBanner/ios">Ios支付成功页广告</a></li>
        </ul>
        <!-- <div class="remind-infor">注意：banner图文件名不支持中文字符</div> -->
    </div>
    <div class="mod-tab-bd">
	    <ul>
	      	<li style="display: block">
	    		<form action="" method="post" id="banner_form">
	    			<div class="data-table-list mt10">
	          			<table>
	            			<colgroup>
	              				<col width="10%" />
	              				<col width="15%" />
	              				<col width="25%" />
	              				<col width="20%" />
	              				<col width="10%" />
	              				<col width="10%" />
	              				<col width="5%" />
	            			</colgroup>
		            		<thead>
			              		<tr>
			                		<th>序号</th>
			                		<th>标题（长度建议在<span class="cRed">10</span>个字以内）</th>
			                		<th>图片</th>
			                		<th>链接</th>
			                		<th>投注彩种</th>
			                		<th>是否显示</th>
			                		<th>操作</th>
			              		</tr>
		            		</thead>
	            			<tbody id="pic-table">
	            				<?php 
	            					$mark = array(
	            						'android' => array('通用尺寸'),
	            						'ios' => array('3.5-640X796', '4-640X926', '4.7-750X1088', '5.5-1242X1800', '5.8-1125X2436')
	            					);
	            				?>
	            				<?php $j = ($platform == 'android')?1:5;  for ($i = 0; $i < $j; $i++) {?>
	            				<tr>
	            					<td>
	                					<?php echo $mark[$platform][$i];?>
	              					</td>
	              					<td>
	                					<input type="text" class="ipt tac w184" name="banner[<?php echo $i?>][imgTitle]" value="<?php echo $addInfo[$i]['imgTitle']; ?>">
	              					</td>
	              					<td>
	              						<div class="btn-white file">选择文件</div>
	                					<div class="btn-white upload" data-index="<?php echo $i?>">开始上传</div>
	                					<input type="hidden" name="banner[<?php echo $i?>][imgUrl]" id="path_<?php echo $i?>" value="<?php echo $addInfo[$i]['imgUrl']?>">
	                					<div id="imgdiv0" class="imgDiv"><img id="imgShow<?php echo $i?>" src="<?php echo $addInfo[$i]['imgUrl']?>" width="100" height="50" /></div>
	              					</td>
	              					<td>
	                					<input type="text" class="ipt tac w222" name="banner[<?php echo $i?>][hrefUrl]" value="<?php echo $addInfo[$i]['hrefUrl']?>">
	              					</td>
	              					<td>
	                					<input type="text" class="ipt tac w40" name="banner[<?php echo $i?>][lid]" value="<?php echo $addInfo[$i]['lid']?>">
	              					</td>
	              					<td>
	                					<a class="btn-<?php echo $addInfo[$i]['isShow']?'blue':'red'; ?> isShow" id="showPreload" data-index="<?php echo $i; ?>"><?php echo $addInfo[$i]['isShow']?'显示':'不显示'; ?></a><input type="hidden" class="showFlag" id="flag<?php echo $i; ?>" name="banner[<?php echo $i?>][isShow]" value="<?php echo $addInfo[$i]['isShow']?'1':'0'; ?>">
	              					</td>
	              					<td>
	                					<a href="javascript:;" class="cBlue removeTr">清空</a>
	              					</td>
	            				</tr>
	            				<?php }?>
	            			</tbody>
	          			</table>
	          			<div class="tac">
	          				<a class="btn-blue mt20 submit" id="preload_banner">保存并上线</a>
	          			</div>
	          		</div>
	          		<input type="hidden" name="platform" value="<?php echo $platform; ?>">
	       		</form>
	       	</li>
	    </ul>
    </div>
</div>
<script src="/source/js/webuploader.min.js"></script>
<script>
	$(function() {
		// 表单提交
		$("#preload_banner").click(function(){
			$("form").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").submit();
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

		// 初始化
		var uploader = WebUploader.create({
	        swf: '/caipiaoimg/v1.1/js/jUploader.swf',
	        pick: '.file',
	    });

	    // 上传
	    $(".upload").click(function(){
	    	var platform = $('input[name="platform"]').val();
			uploader.options.server = "/backend/Appconfig/uploadbanner/" + platform + "/" + $(this).data('index');
			var files = uploader.getFiles();
			var index = files.length - 1;
			// 分割文件名
			if(!(/^\w+\.\w+$/.test(files[index].name))){
				alert('文件名只能包含字母和数字！');
				uploader.removeFile(files);
				return false;
			}
	    	uploader.upload();
	    })

	    // 上传成功
	    uploader.on( 'uploadSuccess', function( file, data) {
	        $("#imgShow" + data.index).attr('src', data.path + data.name);
	        $("#path_" + data.index).val(data.path + data.name);
		});

		// 清空
		$(".removeTr").click(function(){
			$(this).parents('tr').find('input').val('');
			$(this).parents('tr').find('img').attr('src', '');
		})
	});
</script>
