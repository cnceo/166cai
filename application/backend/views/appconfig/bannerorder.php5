<?php $this->load->view("templates/head") ?>
<style>
.isorder {
	display:none;
	background-color: #f1f1f1;
}
.isorder span {
	color:#aaa;
}
</style>
<div class="path">您的位置：客户端管理&nbsp;&gt;&nbsp;<?php echo $platform;?>配置&nbsp;&gt;&nbsp;<a href="javascript:;">启动页预约</a></div>
<div class="mod-tab mt20">
    <div class="data-table-list mt10">
		<table>
			<colgroup><col width="5%"><col width="15%"><col width="15%"><col width="18%"><col width="4%"><col width="10%"><col width="17%"></colgroup>
    		<thead><tr><th>尺寸</th><th>标题（长度建议在<span class="cRed">10</span>个字以内）</th><th>图片</th><th>链接</th><th>投注彩种</th><th>渠道选择</th><th>预约上线期限</th></tr></thead>
			<tbody id="pic-table">
			<?php if ($platform == 'android') {?>
			     <tr>
					<td><?php echo $chicun;?></td>
  					<td><?php echo $online['title']; ?></td>
  					<td><div id="imgdiv0" class="imgDiv"><img id="preImgShow<?php echo $i?>" src="<?php echo $online['imgUrl']?>" width="100" height="50" /></div></td>
  					<td><?php echo $online['url']?></td>
  					<td>
  						<?php 
  						$lid = $online['lid'];
  						if(!empty($online['extra'])) {
  							$extra = json_decode($online['extra'], true);
  							if($extra['playType']) $lid .= '-' . $extra['playType'];
  						}
  						echo $lid; ?>
  					</td>
  					<td>已选择<span><?php echo !(empty($online['channels'])) ? count(explode(',', $online['channels'])) : '0'; ?></span>个</td>
  					<td>
  					上线：<input type="text" value="<?php echo $online['start_time'] ?>" class="Wdate1" readonly><br>
  					下线：<input type="text" value="<?php echo $online['end_time'] ?>" class="Wdate1" readonly>
                    </td>
				</tr>
			<?php }else {
			    if (!empty($onlineArr)) {
			        foreach ($onlineArr as $k => $online) {?>
        			 <tr>
        					<td><?php echo $chicun[$k];?></td>
          					<td><?php echo $online['title']; ?></td>
          					<td><div id="imgdiv0" class="imgDiv"><img id="preImgShow<?php echo $i?>" src="<?php echo $online['imgUrl']?>" width="100" height="50" /></div></td>
          					<?php if ($k == 0) {?>
          					<td rowspan="5"><?php echo $online['url']?></td>
          					<td rowspan="5">
          						<?php 
          						$lid = $online['lid'];
          						if(!empty($online['extra'])) {
          							$extra = json_decode($online['extra'], true);
          							if($extra['playType']) $lid .= '-' . $extra['playType'];
          						}
          						echo $lid; ?>
          					</td>
          					<td rowspan="5">已选择<span><?php echo !(empty($online['channels'])) ? count(explode(',', $online['channels'])) : '0'; ?></span>个</td>
          					<td rowspan="5">
          					上线：<input type="text" value="<?php echo $online['start_time'] ?>" class="Wdate1" readonly><br>
          					下线：<input type="text" value="<?php echo $online['end_time'] ?>" class="Wdate1" readonly>
                            </td>
                            <?php }?>
        				</tr>
        			<?php }
			    }
			}?>
				
			</tbody>
		</table><br>
        <form action="/backend/Appconfig/bannerorder/<?php echo $platform?>/<?php echo $cid?>" method="post" id="prelaod_form">
  			<table>
    			<colgroup><col width="5%"><col width="15%"><col width="23%"><col width="18%"><col width="4%"><col width="10%"><col width="17%"><col width="4%"></colgroup>
        		<thead><tr><th>尺寸</th><th>标题（长度建议在<span class="cRed">10</span>个字以内）</th><th>图片</th><th>链接</th><th>投注彩种</th><th>渠道选择</th><th>预约上线期限</th><th>操作</th></tr></thead>
    			<tbody id="pic-table0">
    			<?php if ($platform == 'android') {$count = 1;} else {$count = count($onlineArr);}?>
    			<?php foreach ($data as $k => $val) {?>
    			    <tr data-index="<?php echo floor($k / $count)?>" data-id="<?php echo $val['id']; ?>">
    					<td>
    					<?php echo ($platform == 'android') ? $chicun : $chicun[$k%5]?>
    					<input type="hidden" class="ipt tac w184 freeze" name="prelaod[<?php echo $k?>][id]" value="<?php echo $val['id']; ?>">
    					</td>
      					<td><input readonly name="prelaod[<?php echo $k?>][title]" class="freeze" value="<?php echo $val['title']; ?>"></td>
      					<td>
      						<input class="file" type="file" id="file_<?php echo $k?>" name="file" style="opacity: 0;width: 0px">
      						<label for="file_<?php echo $k?>"><button class="btn-white">选择文件</button></label>
                    		<div class="btn-white" data-index="<?php echo $k?>" onclick="upload('file_<?php echo $k?>')">开始上传</div>
                    		<input type="hidden" name="prelaod[<?php echo $k?>][imgUrl]" value="<?php echo $val['imgUrl']?>" id="prePath_<?php echo $k?>" name="prelaod[<?php echo $k?>][imgUrl]">
                    		<div id="imgdiv0" class="imgDiv"><img id="preImgShow<?php echo $k?>" src="<?php echo $val['imgUrl']?>" width="100" height="50" /></div>
      					</td>
      					<?php if ($k % $count == 0) {?>
          					<td rowspan="<?php echo $count?>"><input type="text" class="ipt tac w222" name="prelaod[<?php echo $k?>][url]" value="<?php echo $val['url']?>"></td>
          					<td rowspan="<?php echo $count?>">
          						<?php 
          						$lid = $val['lid'];
          						if(!empty($val['extra'])) {
          							$extra = json_decode($val['extra'], true);
          							if($extra['playType']) $lid .= '-' . $extra['playType'];
          						}
          						?>
            					<input type="text" class="ipt tac w40" name="prelaod[<?php echo $k?>][lid]" value="<?php echo $lid; ?>">
          					</td>
          					<td rowspan="<?php echo $count?>">
          						已选择<span><?php echo !(empty($val['channels'])) ? count(explode(',', $val['channels'])) : '0'; ?></span>个
            					<input type="hidden" class="ipt tac w40" name="prelaod[<?php echo $k?>][channels]" value="<?php echo $val['channels']?>">
          					</td>
          					<td rowspan="<?php echo $count?>">
          					上线：<span class="ipt ipt-date w184"><input type="text" name='prelaod[<?php echo $k?>][start_time]' value="<?php echo $val['start_time'] ?>" class="Wdate1" <?php if (isset($val) && !$val['isorder']) {?>readonly<?php }?>><i></i></span><br>
          					下线：<span class="ipt ipt-date w184"><input type="text" name='prelaod[<?php echo $k?>][end_time]' value="<?php echo $val['end_time'] ?>" class="Wdate1" /><i></i></span>
                            </td>
          					<td rowspan="<?php echo $count?>">
            					<a href="javascript:;" class="cBlue removeTr">清空</a><br>
            					<a href='javascript:;' class='cBlue deleteTr' data-index="<?php echo $k / $count?>">删除</a>
          					</td>
      					<?php }?>
    				</tr>
    			<?php }?>
    			</tbody>
  			</table>
  			<a href="javascript:;" class="btn-white mt20" id="add">+</a><br>
  			<div class="tac">
  				<a class="btn-blue mt20 submitPrelaod" id="preload_banner">保存并预约</a>
  			</div>
  		</div>
  		<input type="hidden" name="platform" value="<?php echo $platform; ?>">
	</form>
</div>
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
<script src="/source/js/ajaxfileupload.js"></script>
<script  src="/source/date/WdatePicker.js"></script>
<script>
<?php if ($notfull) {?>
alert('请将方案内容填写完整！');
<?php }?>
delId = [];
    $('#add').click(function(){
    	var i = $('#prelaod_form tbody tr').length+1, str = '';
        <?php if ($platform == 'android') {?>
           str += "<tr><td><?php echo $chicun;?></td><td><input readonly name='prelaod["+i+"][title]' class='freeze' value='<?php echo $online['title']; ?>'></td>\
    		<td>\
        		<input class='file' type='file' id='file_"+i+"' name='file' style='opacity: 0;width: 0px'><label for='file_"+i+"'><button class='btn-white'>选择文件</button></label>\
        		<div class='btn-white' data-index='"+i+"' onclick='upload(\"file_"+i+"\")'>开始上传</div>\
        		<input type='hidden' name='prelaod["+i+"][imgUrl]' id='prePath_"+i+"' name='prelaod["+i+"][imgUrl]' value='<?php echo $online['imgUrl']; ?>'>\
        		<div id='imgdiv0' class='imgDiv'><img id='preImgShow"+i+"' src='<?php echo $online['imgUrl']; ?>' width='100' height='50' /></div>\
    		</td>\
    		<td><input type='text' class='ipt tac w222' name='prelaod["+i+"][url]' value='<?php echo $online['url']; ?>'></td>\
    		<td><input type='text' class='ipt tac w40' name='prelaod["+i+"][lid]' value='<?php echo $online['lid']; ?>'></td>\
    		<td>已选择<span><?php echo !(empty($online['channels'])) ? count(explode(',', $online['channels'])) : '0'; ?></span>个\
    	    		<input type='hidden' class='ipt tac w40' name='prelaod["+i+"][channels]' value='<?php echo $online['channels']; ?>'></td>\
    		<td>\
    			上线：<span class='ipt ipt-date w184'><input type='text' name='prelaod["+i+"][start_time]' class='Wdate1'><i></i></span><br>\
    			下线：<span class='ipt ipt-date w184'><input type='text' name='prelaod["+i+"][end_time]' class='Wdate1'><i></i></span>\
    		</td>\
    		<td><a href='javascript:;' class='cBlue removeTr'>清空</a><br><a href='javascript:;' class='cBlue deleteTr'>删除</a></td>\
    	</tr>";
        <?php } else {
        foreach ($onlineArr as $k => $online) {?>
        var index = $('.deleteTr').length;
            str += "<tr data-index="+index+"><td><?php echo $chicun[$k];?></td><td><input readonly name='prelaod["+i+"][title]' class='freeze' value='<?php echo $online['title']; ?>'></td>\
      		<td>\
            <input class='file' type='file' id='file_"+i+"' name='file' style='opacity: 0;width: 0px'><label for='file_"+i+"'><button class='btn-white'>选择文件</button></label>\
          		<div class='btn-white' data-index='"+i+"' onclick='upload(\"file_"+i+"\")'>开始上传</div>\
          		<input type='hidden' name='prelaod["+i+"][imgUrl]' id='prePath_"+i+"' name='prelaod["+i+"][imgUrl]' value='<?php echo $online['imgUrl']; ?>'>\
                      		    <div id='imgdiv0' class='imgDiv'><img id='preImgShow"+i+"' src='<?php echo $online['imgUrl']; ?>' width='100' height='50' /></div>\
                      		        </td>";
                <?php if ($k % 5 == 0) {?>
                    str += "<td rowspan='5'><input type='text' class='ipt tac w222' name='prelaod["+i+"][url]' value='<?php echo $online['url']; ?>'></td>\
                        <td rowspan='5'><input type='text' class='ipt tac w40' name='prelaod["+i+"][lid]' value='<?php echo $online['lid']; ?>'></td>\
                            <td rowspan='5'>已选择<span><?php echo !(empty($online['channels'])) ? count(explode(',', $online['channels'])) : '0'; ?></span>个\
                                    <input type='hidden' class='ipt tac w40' name='prelaod["+i+"][channels]' value='<?php echo $online['channels']; ?>'></td>\
                                <td rowspan='5'>\
                                	上线：<span class='ipt ipt-date w184'><input type='text' name='prelaod["+i+"][start_time]' class='Wdate1'><i></i></span><br>\
          	  					下线：<span class='ipt ipt-date w184'><input type='text' name='prelaod["+i+"][end_time]' class='Wdate1'><i></i></span>\
          	  		</td>\
          	  		<td rowspan='5'><a href='javascript:;' class='cBlue removeTr'>清空</a><br><a href='javascript:;' class='cBlue deleteTr' data-index="+index+">删除</a></td>\
          	  	</tr>"
               <?php }?>
               i++;
               <?php }
            }?>
        $('#prelaod_form tbody').append(str);
    })
	$('#preload_banner').click(function(){
		var notime = false, dataArr = []
		$('.mod-tab tbody').find('tr').each(function(){
    		if($(this).find('td').length > 6) {
    			var index = $(this).index(), 
    			start = $(this).find('td:eq(6) input:first').val(), end = $(this).find('td:eq(6) input:last').val();
    			if (!start || !end) {
    				alert('请设置上下线时间！');
    				notime = true;
    				return false
    			}
    			var startval = (new Date(start)).valueOf(), endval = (new Date(end)).valueOf();
				if (startval >= endval) {
					alert('下线时间不可大于上线时间！');
					notime = true;
					return false
				}
    			dataArr.push([startval, endval, index]);
    		}
		})
		if (notime) return;
		var repeat = false;
		if (dataArr.length > 0) {
			console.log(dataArr);
			$.each(dataArr, function(k0, val0){
				$.each(dataArr, function(k1, val1){
					if (k0 != k1 && ((val0[0] <= val1[0] && val0[1] > val1[0]) || (val0[0] >= val1[0] && val1[1] > val0[0]))) {
						alert('方案上线时间有冲突，请重新设置！');
						repeat = true;
						return false
					}
				})
				if (repeat) return false;
			})
			if (repeat) return false;
		}
		if (repeat.length > 0) return ;
		$('#prelaod_form').append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'><input type='hidden' name='delid' value='"+delId.join(',')+"'>").submit();
	})

	function upload(id) {
    	$.ajaxFileUpload({
            url: '/backend/Appconfig/uploadbanner/<?php echo $platform?>/'+id.replace(/\D+/, '')+'/qdy',  //这里是服务器处理的代码
            type: 'post',
            secureuri: false, //一般设置为false
            fileElementId: id, // 上传文件的id、name属性名
            dataType: 'json', //返回值类型，一般设置为json、application/json
            success: function (data, status) {
            	$('#prelaod_form').find("#preImgShow" + data.index).attr('src', data.path + data.name);
            	$('#prelaod_form').find("#prePath_" + data.index).val(data.path + data.name);
            },
            error: function (data, status, e) {
                alert("错误：上传组件错误，请检察网络!");
            }
        });
    }
	$(function(){

		$('#prelaod_form').on('focus', '.Wdate1', function(){
            dataPicker();
        });
        
        $('#chooseChannel').on('click', '.selectAll', function(){
            $("#chooseChannel").find(':checkbox').each(function(){
                $(this).attr("checked", true);
            });
        })
        
        $('#chooseChannel').on('click', '.selectSource', function(){
            $('#chooseChannel input[name="sourceTag"]').each(function(){
                $(this).attr("checked", true);
            });
        })
        
        $('#chooseChannel').on('click', '.cancelSource', function(){
            $('#chooseChannel input[name="sourceTag"]').each(function(){
                $(this).attr("checked", false);
            });
        })

        $('#chooseChannel').on('click', '.cancelAll', function(){
            $("#chooseChannel").find(':checkbox').each(function(){
                $(this).attr("checked", false);
            });
        })
		
		$('#prelaod_form tbody').on('click', '.removeTr', function(){
			$(this).parents('tr').find('input:not(.freeze)').val('');
			$(this).parents('tr').find('img').attr('src', '');
		})
		
		$('#prelaod_form tbody').on('click', '.deleteTr', function(){
			<?php if ($platform == 'android') {?>
			if ($(this).parents('tr').data('id')) delId.push($(this).parents('tr').data('id'));
			$(this).parents('tr').remove();
			<?php } else {?>
			var index = $(this).data('index');
			if (index !== undefined) {
				$("tr[data-index="+index+"]").each(function(){
					if ($(this).data('id')) delId.push($(this).data('id'));
					$(this).remove();
				})
			}
			<?php }?>
			
		})
		
	})
	
	
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
</script>