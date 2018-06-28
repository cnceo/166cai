<div class="frame-container">
	<?php $this->load->view('template/breadcrumb');?>
	<div class="data-table-list mt20 table-no-border">
		<form method="post" id="form"
			action="<?php echo $this->config->item('base_url')?>/shop/edit?id=<?php echo $data['id'] ?>">
			<table>
				<colgroup>
					<col width="10%" />
					<col width="90%" />
				</colgroup>
				<tbody>
					<tr>
						<td class="tar"><label for="">*编号：</label></td>
						<td class="tal pl10"><input type="text" name="info[shopNum]"
							id="shopNum" value="<?php echo $data['shopNum']?>"
							class="ipt w184"></td>
					</tr>
					<tr>
						<td class="tar"><label for="">名称：</label></td>
						<td class="tal pl10"><input type="text" name="info[cname]"
							value="<?php echo $data['cname']?>" class="ipt w184"></td>
					</tr>
					<tr>
						<td class="tar"><label for="">彩种类别：</label></td>
						<td class="tal pl10">
							<select name="info[lottery_type]">
							<?php foreach ($lotteryTypes as $key => $lt) {?>
								<option value="<?php echo $key?>" <?php if ($data['lottery_type'] == $key) {?>selected<?php }?>><?php echo $lt?></option>
							<?php }?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="tar"><label for="">*电话：</label></td>
						<td class="tal pl10"><input type="text" name="info[phone]"
							id="phone" value="<?php echo $data['phone']?>" class="ipt w184"></td>
					</tr>
					<tr>
						<td class="tar"><label for="">QQ：</label></td>
						<td class="tal pl10"><input type="text" name="info[qq]"
							value="<?php echo $data['qq']?>" class="ipt w184"></td>
					</tr>
					<tr>
						<td class="tar"><label for="">微信：</label></td>
						<td class="tal pl10"><input type="text" name="info[webchat]"
							value="<?php echo $data['webchat']?>" class="ipt w184"></td>
					</tr>
					<tr>
						<td class="tar"><label for=""></label>其他联系方式：</td>
						<td class="tal pl10"><input type="text" name="info[other_contact]"
							value="<?php echo $data['other_contact']?>" class="ipt w184"></td>
					</tr>
					<tr>
						<td class="tar"><label for="">*地址：</label></td>
						<td class="tal pl10"><input type="text" name="info[address]"
							id="address" value="<?php echo $data['address']?>"
							class="ipt w360"></td>
					</tr>
					<tr>
						<td class="tar vat"><label for="">附件：</label></td>
						<td class="tal pl10"><input type="hidden" name="delfile">
	         	
	           <div id="uploader" class="wu-example">
								<!--用来存放文件信息-->
								<div class="btns">
								<div id="picker" class="btn-white">选择文件</div>
<!-- 									<input id="picker" name="file" type="file"> -->
									<div id="ctlBtn" class="btn-white">开始上传</div>
								</div>
							</div>
							<div id="upload-list">
							<?php
											if (! empty ( $files ))
											{
												foreach ( $files as $file )
												{
													?>
													<div data-id="<?php echo $file['id']?>" data-state="success" class="item"><p class="info"><?php echo $file['filename']?><font>已上传...</font><font class="remove-this">×</font></p></div>
													
			         <?php
												
}
											}
											?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
	
	</div>
	<div class="audit-detail-btns mt20 ml10bf">
		<input value="提交" class="btn-blue" type="submit">
	</div>
	</form>
</div>
<script src="/source/js/webuploader.min.js"></script>
<!-- <script src="/caipiaoimg/dashboard/js/ajaxfileupload.js"></script> -->

<script>
var  $ = jQuery, shopNumCheck = false, str, filedel = [], uploader;
$(function(){
	uploader = WebUploader.create({

	    // swf文件路径
	    swf: '/caipiaoimg/dashboard/js/Uploader.swf',
	    // 文件接收服务端。
	    server: '<?php echo $this->config->item('base_url')?>/shop/upload?id=<?php echo $data['id'] ?>',

	    // 选择文件的按钮。可选。
	    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
	    pick: '#picker',

	    // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
	    resize: false,
	   //文件去重
	   
	   fileNumLimit: 5,

            fileSizeLimit: 1024 * 1024 * 10,
//
            fileSingleSizeLimit: 1024 * 1024 * 10,

	    accept: {
	        title: 'Image,Applications',
	        extensions: 'pdf,jpg,jpeg,bmp,png,doc,docx,zip,xls,xlsx,txt',
	        mimeTypes: 'image/*,  application/*, text/* '
	    }
	});

	uploader.on( 'fileQueued', function( file ) {
        $("#upload-list").append( '<div id="' + file.id + '" class="item"><p class="info">' + file.name + ' <span class="state">等待上传...</span><span class="remove-this">&times;</span></p></div>' );

        // 移除等待上传的文件，并且移除DOM元素
        $( '#'+file.id ).on('click', '.remove-this', function() {
            uploader.removeFile(file, true);
            $(this).parents('.item').remove();
        })

    });


		uploader.on( 'uploadSuccess', function( file , data) {
			if (data.id && $.inArray(data.id.toString(), filedel) === -1) {
				alert('不可上传相同文件');
			} else {
				$( '#'+file.id ).find('.state').text('已上传');
				str = "<input type='hidden' class='filename' name='filename[]' value='"+data.filename+"'>";
				str += "<input type='hidden' name='filepath[]' value='"+data.filepath+"'>";
				$('#'+file.id).append(str);
				$('#'+file.id).attr('data-state', 'success');
			}
            
        });

        uploader.on( 'uploadError', function( file ) {
            $( '#'+file.id ).find('.state').text('上传出错');
        });

        uploader.on( 'uploadComplete', function( file ) {
            console.log
            $( '#'+file.id ).find('.progress').fadeOut();
        });

		uploader.on('error', function(handler) {

	        if(handler == "Q_EXCEED_NUM_LIMIT"){
	            alert("超出上传数量限制,文件一次最多上传5个");
	        }
	        if(handler == "F_DUPLICATE"){
	            alert("不可上传相同文件");
	        }

	        if(handler == "Q_EXCEED_SIZE_LIMIT"){
	            alert("文件大小不能超过10M");
	        }

	        if(handler == "Q_TYPE_DENIED"){
	            alert("仅支持以下格式：PDF,DOC,DOCX,TXT,XLS,XLSX,JPG,PNG,BMP,JPEG,ZIP");
	        }

	    });

		$("#ctlBtn").on( 'click', function() {
			if ($("#upload-list").find(".item").length <= 5) {
				uploader.upload();
			}else {
				alert('最多只有5个附件！');
			}
		});
	
})


$("#form").on('click', '.remove-this', function(){
	var id = $(this).parents('.item').attr('data-id'), self = $(this);
	self.parents('.item').remove();
	if (id) {
		filedel.push(id);
	}
})
$("#shopNum").blur(function(){
	$.get('<?php echo $this->config->item('base_url')?>/shop/checkShopnum?shopnum='+$(this).val()+'&id=<?php echo $data['id']?>',
		function(data){
		if (data == 1) {
			shopNumCheck = true;
			alert('投注站编号已占用');
		}else {
			shopNumCheck = false;
		}
	})
})
$("#form").on('submit', function(){
	var shopNum = $("#shopNum").val(), phone = $("#phone").val(), address = $("#address").val();
	if (shopNum.length == 0) {
		alert('请输入投注站编号');
		return false;
	}else if(shopNumCheck) {
		alert('投注站编号已占用');
		return false;
	}
	if (phone.length == 0) {
		alert('请输入电话');
		return false;
	}
	if (address.length == 0) {
		alert('请输入地址');
		return false;
	}
	$("input[name=delfile]").val(filedel.toString());
})

</script>
