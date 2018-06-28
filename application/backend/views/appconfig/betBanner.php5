<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：客户端管理&nbsp;&gt;&nbsp;<a href="/backend/Appconfig/betBanner/<?php echo $platform;?>"><?php echo $platform;?>投注页素材配置</a></div>
<div class="mod-tab mt20">
	<div class="mod-tab-hd remind-hd">
		<ul>
			<?php $this->load->view("appconfig/tag", array('platform' => $platform, 'tag' => 'betBanner')) ?>
    	</ul>
    </div>
    <div class="mod-tab-bd">
	    <ul>
	      	<li style="display: block">
	    		<form action="" method="post" id="banner_form">
	    			<div class="data-table-list mt10">
	          			<table>
	            			<colgroup>
	              				<col width="20%" />
	              				<col width="35%" />
	              				<col width="30%" />
	              				<col width="15%" />
	            			</colgroup>
		            		<thead>
			              		<tr>
			                		<th>彩种</th>
			                		<th>图片</th>
			                		<th>链接</th>
			                		<th>操作</th>
			              		</tr>
		            		</thead>
	            			<tbody id="pic-table">
	            				<?php if(!empty($info)): ?>
								<?php foreach ($info as $key => $items):?>
	            				<tr>
	            					<td>
	                					<?php echo $items['lname']; ?>
	                					<input type="hidden" class="freeze" name="banner[<?php echo $key?>][id]" value="<?php echo $items['id']?>">
	                					<input type="hidden" class="freeze" name="banner[<?php echo $key?>][lid]" value="<?php echo $items['lid']?>">
	              					</td>
	              					<td>
	                					<div class="btn-white file">选择文件</div>
	                					<div class="btn-white upload" data-index="<?php echo $key?>">开始上传</div>
	                					<input type="hidden" name="banner[<?php echo $key?>][imgUrl]" id="path_<?php echo $key?>" value="<?php echo $items['imgUrl']?>">
	                					<div id="imgdiv0" class="imgDiv"><img id="imgShow<?php echo $key?>" src="<?php echo $items['imgUrl'] ? $items['imgUrl'] : ''; ?>" width="100" height="50" /></div>
	              					</td>
	              					<td>
	                					<input type="text" class="ipt tac w184" name="banner[<?php echo $key?>][url]" value="<?php echo $items['url']; ?>">
	              					</td>
	              					<td>
	              						<a href="javascript:;" class="cBlue removeTr">清空</a>
	              					</td>
	            				</tr>
	            				<?php endforeach; ?>
								<?php endif; ?>
	            			</tbody>
	          			</table>
	          			<div class="tac">
	          				<a class="btn-blue mt20 submitBanner">保存并上线</a>
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
		$(".submitBanner").click(function(){
			$("#banner_form").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").submit();
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

        // 轮播图、启动页 - 上传成功
        uploader.on( 'uploadSuccess', function( file, data) {
        	if(data.type == 'qdy'){
        		$("#preImgShow" + data.index).attr('src', data.path + data.name);
            	$("#prePath_" + data.index).val(data.path + data.name);
        	}else{
        		$("#imgShow" + data.index).attr('src', data.path + data.name);
            	$("#path_" + data.index).val(data.path + data.name);
        	}
        });

		// 清空
		$(".removeTr").click(function(){
			$(this).parents('tr').find('input:not(.freeze)').val('');
			$(this).parents('tr').find('img').attr('src', '');
		})
	});
</script>
