<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：客户端管理&nbsp;&gt;&nbsp;<a href="/backend/Appconfig/banner?platform=<?php echo $platform;?>"><?php echo $platform;?>配置</a></div>
<div class="mod-tab mt20">
	<div class="mod-tab-hd">
        <ul>
        	<?php $this->load->view("appconfig/tag", array('platform' => $platform, 'tag' => 'pop')) ?>
        </ul>
    </div>
    <div class="mod-tab-bd">
	    <ul>
	       	<li style="display: block">
	       		<!-- 启动页弹窗 -->
	    		<form action="" method="post" id="pop_form">
	    			<div class="data-table-list mt10">
	          			<table>
	            			<colgroup>
	              				<col width="10%" />
	              				<col width="30%" />
	              				<col width="30%" />
	              				<col width="20%" />
	              				<col width="10%" />
	            			</colgroup>
		            		<thead>
			              		<tr>
			                		<th>序号</th>
			                		<th>图片</th>
			                		<th>链接</th>
			                		<th>是否显示</th>
			                		<th>清空</th>
			              		</tr>
		            		</thead>
	            			<tbody id="pic-table" class="pop-table">
	            				<?php 
	            				$needLoginArr = array(
	            					0 => array('title' => '否', 'val' => '0'),
	            					1 => array('title' => '不限', 'val' => '-1'),
	            					2 => array('title' => '是', 'val' => '1'),
	            				);
	            				?>
	            				<?php for ($i = 0; $i < 3; $i++) {?>
	            				<tr>
	            					<td>
	                					<?php echo $i+1;?>
	              					</td>
	              					<td>
	              						<div class="btn-white file">选择文件</div>
	                					<div class="btn-white upload" data-index="<?php echo $i?>">开始上传</div>
	                					<input type="hidden" name="pop[<?php echo $i?>][path]" id="path_<?php echo $i?>" value="<?php echo $popInfo[$i]['path']?>">
	                					<div id="imgdiv0" class="imgDiv"><img id="imgShow<?php echo $i?>" src="<?php echo $popInfo[$i]['path']?>" width="100" height="50" /></div>
	              					</td>
	              					<td>
	                					<input type="text" class="ipt tac w184" name="pop[<?php echo $i?>][url]" value="<?php echo $popInfo[$i]['url']; ?>">
	              					</td>
	              					<td>
	                					<a class="btn-<?php echo $popInfo[$i]['isShow']?'blue':'red'; ?> showPop" id="showPop" data-index="<?php echo $i; ?>"><?php echo $popInfo[$i]['isShow']?'显示':'不显示'; ?></a><input type="hidden" class="showFlag" id="popflag<?php echo $i; ?>" name="pop[<?php echo $i?>][isShow]" value="<?php echo $popInfo[$i]['isShow']?'1':'0'; ?>">
	              					</td>
	              					<td>
	              						<a href="javascript:;" class="cBlue removeTr">清空</a>
	              					</td>
	            				</tr>
	            				<?php }?>
	            			</tbody>
	          			</table>
	          			<div class="tac">
	          				<a class="btn-blue mt20 submit-pop">保存并上线</a>
	          			</div>
	          		</div>
	          		<input type="hidden" name="platform" value="<?php echo $platform; ?>">
	       		</form>
	       	</li>
	    </ul>
    </div>
    <div class="pop-mask" style="display:none;width:200%"></div>
</div>
<script src="/source/js/webuploader.min.js"></script>
<script>
	$(function() {
		// 初始化
		var uploader = WebUploader.create({
            swf: '/caipiaoimg/v1.1/js/jUploader.swf',
            pick: '.file',
        });

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

        uploader.on( 'uploadSuccess', function( file, data) {
    		$("#imgShow" + data.index).attr('src', data.path + data.name);
        	$("#path_" + data.index).val(data.path + data.name);
        });

		// 清空
		$(".removeTr").click(function(){
			$(this).parents('tr').find('input').val('');
			$(this).parents('tr').find('img').attr('src', '');
		})

		// 显示切换
		$('.showPop').click(function(){
			var flagVal = $("#popflag" + $(this).data('index')).val();
			if(flagVal == '0'){
				$("#popflag" + $(this).data('index')).val('1');
				$(this).removeClass('btn-red').addClass('btn-blue');
				$(this).html('显示');
			}else{
				$("#popflag" + $(this).data('index')).val('0');
				$(this).removeClass('btn-blue').addClass('btn-red');
				$(this).html('不显示');
			}
		});

		// 修改版本彩种销售
		$(".submit-pop").click(function(){
			var k = 0;
			var index = 0;
			var error = 0;
			$('.pop-table').find('tr').each(function(){
				index ++;
				var tdArr = $(this).children();
				if(tdArr.eq(3).find('input').val() == 1){
					k ++;
					if(tdArr.eq(1).find('img').attr("src") === '' || tdArr.eq(2).find('input').val() === ''){
						error = index;
					}
				}
			});
			// 同时只能显示一个
			if(k > 1){
				alert('同时只能显示一个');
				return false;
			}
			if(error > 0){
				alert('第' + error + '行显示的图片或者链接不能为空');
				return false;
			}
			$("#pop_form").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").submit();
		})
	});



</script>
