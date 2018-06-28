<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：客户端管理&nbsp;&gt;&nbsp;<a href="/backend/Appconfig/banner?platform=<?php echo $platform;?>"><?php echo $platform;?>配置</a></div>
<div class="mod-tab mt20">
	<div class="mod-tab-hd remind-hd">
		<ul>
            <?php $this->load->view("appconfig/tag", array('platform' => $platform, 'tag' => 'event')) ?>
    	</ul>
    </div>
    <div class="mod-tab-bd">
	    <ul>
	      	<li style="display: block">
	      		<div class="data-table-filter">
          			<table>
            			<tbody>
              				<tr>
                				<td>
                  					<a href="javascript:;" class="btn-blue" id="createEvent">新建活动</a>
                				</td>
              				</tr>
            			</tbody>
          			</table>
        		</div>
    			<div class="data-table-list mt10">
          			<table>
            			<colgroup>
              				<col width="5%" />
              				<col width="17%" />
              				<col width="15%" />
              				<col width="20%" />
              				<col width="8%" />
              				<col width="25%" />
              				<col width="10%" />
            			</colgroup>
	            		<thead>
		              		<tr>
		                		<th>排序</th>
		                		<th>标题（长度建议在<span class="cRed">6-15</span>个字以内）</th>
		                		<th>图片</th>
		                		<th>链接</th>
		                		<th>投注彩种</th>
		                		<th>活动时间</th>
		                		<th>操作</th>
		              		</tr>
	            		</thead>
            			<tbody id="pic-table" class="eventList">
            				<?php if(!empty($info)):?>
            				<?php foreach ($info as $items):?>
            				<tr id="<?php echo 'event' . $items['id']; ?>">
              					<td><?php echo $items['weight']; ?></td>
              					<td><?php echo $items['title']; ?></td>
              					<td>
                					<div><img src="<?php echo $items['path']; ?>" width="100" height="50" /></div>
              					</td>
              					<td><?php echo $items['url']; ?></td>
              					<td><?php echo $items['lid']; ?></td>
              					<td>
                                    <?php echo $items['start_time'] . ' 至 ' . $items['end_time']; ?>
                                    <input type="hidden" name="startTime" value="<?php echo $items['start_time']; ?>">
                                    <input type="hidden" name="endTime" value="<?php echo $items['end_time']; ?>">
                                </td>
              					<td>
                					<a href="javascript:;" class="cBlue modifyTr" data-index="<?php echo $items['id']; ?>">修改</a>
                					<a href="javascript:;" class="cBlue removeTr" data-index="<?php echo $items['id']; ?>">删除</a>
              					</td>
            				</tr>
            				<?php endforeach;?>
                  			<?php endif; ?>
            			</tbody>
            			<tfoot>
		                    <tr>
		                     	<td colspan="14">
		                        	<div class="stat">
		                          		<span>本页&nbsp;<?php echo $pages[2] ?>&nbsp;条</span>
		                          		<span class="ml20">共&nbsp;<?php echo $pages[1] ?>&nbsp;页</span>
		                          		<span class="ml20">总计&nbsp;<?php echo $pages[3] ?>&nbsp;</span>
		                        	</div>
		                      	</td>
		                    </tr>
	                  	</tfoot>
          			</table>
          		</div>
          		<input type="hidden" name="platform" value="<?php echo $platform; ?>">
	       		<div class="page mt10 order_info">
			    	<?php echo $pages[0] ?>
			    </div>
	       	</li>
	       	<li style="display: block">
	       		<div class="version clearfix">
                    <div class="mod-tab version-list">
                        <div class="mod-tab-bd">
                            <ul>
                                <li style="display: block;">
                                	<form action="" method="post" id="loading_form">
	                                    <div class="data-table-list mt10">
	                                        <table>
	                                            <colgroup>
	                                            	<col width="20%">
	                                                <col width="20%">
	                                            </colgroup>
	                                            <thead>
	                                                <tr>
	                                                	<th>活动中心入口</th>
	                                                	<th>是否开启</th>
	                                            	</tr>
	                                            </thead>
	                                            <tbody>
	                                                <tr>
	                                                	<td>
	                                                		浮层
	                                                	</td>
	                                                    <td>
	                                                    	<a class="btn-blue" data-index="<?php echo $eventStatus; ?>" id="eventStatus"><?php echo ($eventStatus == '0') ? '已关闭' : '已开启'; ?></a>
	                                                    </td>
	                                                </tr>
	                                            </tbody>
	                                        </table>  
	                                    </div>
                                	</form>
                                </li>
                            </ul>
                        </div>
                        <div class="tac">
                            <a href="javascript:;" class="btn-blue mt20 submit-event">保存并上线</a>
                        </div>
                    </div>
                </div>
	       	</li>
	    </ul>
    </div>
    <!-- 自定义模块 start -->
  	<div class="pop-dialog" id="dialog-createEvent" style="display:none;">
		<div class="pop-in">
            <div class="pop-head">
                <h2>新建活动</h2>
            </div>
            <div class="pop-body">
                <div class="data-table-filter del-percent">
                    <table>
                        <colgroup>
                            <col width="60">
                            <col width="240">
                        </colgroup>
                        <tbody>
                        	<tr>
                        		<td>排序：</td>
                        		<td>
                                	<input type="text" class="ipt tac w130" name="weight" value="">
                                </td>
                        	</tr>
                        	<tr>
                        		<td>标题：</td>
                        		<td>
                                	<input type="text" class="ipt tac w130" name="title" value="">
                                </td>
                        	</tr>
                            <tr>
                                <td>图片：</td>
                                <td>
                                	<div class="btn-white file">选择文件</div>
                					<div class="btn-white upload" data-index="0">开始上传</div>
                					<input type="hidden" id="path" name="path" value="">
                					<div id="imgdiv" class="imgDiv"><img id="imgShow" src="" width="100" height="50" /></div>
                                </td>
                            </tr>
                            <tr>
                                <td>链接：</td>
                                <td>
                                	<input type="text" class="ipt tac w140" name="url" value="">
                                </td>
                            </tr>
                            <tr>
                                <td>投注彩种：</td>
                                <td>
                                	<input type="text" class="ipt tac w130" name="lid" value="0">
                                </td>
                            </tr>
                            <tr>
                                <td>活动时间：</td>
                                <td>
                                	<input type="text" class="ipt Wdate1 w150 ipt-date" name="start_time" value="">
                                	至
                                	<input type="text" class="ipt Wdate2 w150 ipt-date" name="end_time" value="">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-blue-h32 mlr15" data-index="0" id="submit-event">确认</a>
                <a href="javascript:closePop();" class="btn-blue-h32 mlr15">取消</a>
            </div>
        </div>
	</div>
	<!-- 自定义模块 end -->
    <!-- 删除 start -->
    <div class="pop-dialog" id="dialog-delet" style='display:none;'>
        <div class="pop-in">
            <div class="pop-head">
                <h2>删除活动</h2>
            </div>
            <div class="pop-body">
                <p><b>请确认删除这条活动信息</b></p>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:void(0);" class="btn-blue-h32 mlr15" data-index="0" id="deleteConfirm">确认</a>
                <a href="javascript:closePop();" class="btn-b-white mlr15">取消</a><br/><br/>
            </div>
        </div>
    </div>
    <!-- 删除 end -->
</div>
</div>
<script  src="/source/date/WdatePicker.js"></script>
<script src="/source/js/webuploader.min.js"></script>
<script>
	$(function() {
		// 时间控件
		$(".Wdate1").focus(function(){
            dataPicker({dateFmt:'yyyy-MM-dd 00:00:00'});
        });

        $(".Wdate2").focus(function(){
            dataPicker({dateFmt:'yyyy-MM-dd 23:59:59'});
        });

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
    		$("#imgShow").attr('src', data.path + data.name);
        	$("#path").val(data.path + data.name);
        });

        // 新建活动
        $("#createEvent").click(function(){
        	$('#submit-event').data('index', '0');
            // 清空input
            $('#dialog-createEvent input').val('');
            $('#dialog-createEvent #imgShow').attr('src', '');
        	popdialog("dialog-createEvent");
			return false;
        });

        // 修改
        $(".modifyTr").click(function(){         
            // 展示
            var id = $(this).data('index');
            $('#submit-event').data('index', id);
            var tdArr = $("#event" + id).find('td');
            var weight = tdArr.eq(0).html();
            $('#dialog-createEvent input[name="weight"]').val(weight);
            var title = tdArr.eq(1).html();
            $('#dialog-createEvent input[name="title"]').val(title);
            var path = tdArr.eq(2).find('img').attr('src');
            console.log(path);
            $('#dialog-createEvent input[name="path"]').val(path);
            $('#dialog-createEvent #imgShow').attr('src', path);
            var url = tdArr.eq(3).html();
            $('#dialog-createEvent input[name="url"]').val(url);
            var lid = tdArr.eq(4).html();
            $('#dialog-createEvent input[name="lid"]').val(lid);
            var start_time = tdArr.eq(5).find('input[name="startTime"]').val();
            var end_time = tdArr.eq(5).find('input[name="endTime"]').val();
            $('#dialog-createEvent input[name="start_time"]').val(start_time);
            $('#dialog-createEvent input[name="end_time"]').val(end_time);
            popdialog("dialog-createEvent");
            return false;
        });

        var selectTag = true;

        // 新建 修改
        $('#submit-event').click(function(){
        	var platform = $('input[name="platform"]').val();
			var id = $('#submit-event').data('index');
			var weight = $('#dialog-createEvent input[name="weight"]').val();
			var title = $('#dialog-createEvent input[name="title"]').val();
			var	path = $('#dialog-createEvent input[name="path"]').val();
			var url = $('#dialog-createEvent input[name="url"]').val();
			var lid = $('#dialog-createEvent input[name="lid"]').val();
			var start_time = $('#dialog-createEvent input[name="start_time"]').val();
			var end_time = $('#dialog-createEvent input[name="end_time"]').val();
			if(selectTag)
			{
				selectTag = false;
				$.ajax({
	                type: 'post',
	                url: '/backend/Appconfig/updateEventInfo',
	                data: {platform:platform,id:id,weight:weight,title:title,path:path,url:url,lid:lid,start_time:start_time,end_time:end_time},
	                success: function (response) {
	                    var response = $.parseJSON(response);
	                    if(response.status == 'y'){
	                    	alert(response.message);
	                        selectTag = true;
	                        window.location.reload();
	                    }else{
	                    	alert(response.message);
	                        selectTag = true;
	                    }
	                },
	                error: function () {
	                    selectTag = true;
	                    alert('网络异常，请稍后再试');
	                }
	            });
			}
        });

        // 删除
        $(".removeTr").click(function(){
            var id = $(this).data('index');
            $('#deleteConfirm').data('index', id);
            popdialog("dialog-delet");
            return false;
        });

        // 确认删除
        $("#deleteConfirm").click(function(){
            var platform = $('input[name="platform"]').val();
            var id = $('#deleteConfirm').data('index');
            if(selectTag)
            {
                selectTag = false;
                $.ajax({
                    type: 'post',
                    url: '/backend/Appconfig/delEventInfo',
                    data: {id:id,platform:platform},
                    success: function (response) {
                        var response = $.parseJSON(response);
                        if(response.status == 'y'){
                            alert(response.message);
                            selectTag = true;
                            window.location.reload();
                        }else{
                            alert(response.message);
                            selectTag = true;
                        }
                    },
                    error: function () {
                        selectTag = true;
                        alert('网络异常，请稍后再试');
                    }
                });
            }
        });
        

        // 浮层开关切换
		$('#eventStatus').click(function(){
			var flagVal = $(this).data('index');
			if(flagVal == '0'){
				$('#eventStatus').data('index', '1');
				$(this).html('已开启');
			}else{
				$('#eventStatus').data('index', '0');
				$(this).html('已关闭');
			}
		});

		// 修改浮层展示状态
		$('.submit-event').click(function(){
			var platform = $('input[name="platform"]').val();
			var status = $('#eventStatus').data('index');
			if(selectTag)
			{
				selectTag = false;
				$.ajax({
	                type: 'post',
	                url: '/backend/Appconfig/updateEventStatus',
	                data: {platform:platform,status:status},
	                success: function (response) {
	                    var response = $.parseJSON(response);
	                    if(response.status == 'y'){
	                    	alert(response.message);
	                        selectTag = true;
	                        window.location.reload();
	                    }else{
	                    	alert(response.message);
	                        selectTag = true;
	                    }
	                },
	                error: function () {
	                    selectTag = true;
	                    alert('网络异常，请稍后再试');
	                }
	            });
			}
		})


		// // 表单提交
		// $(".submitEvent").click(function(){
		// 	// 检查提交
		// 	var start = 1;
		// 	var error = 0;
		// 	$('.eventList').find('tr').each(function () {
		// 		var tdArr = $(this).children();
		// 		if(tdArr.eq(0).find('input').val() === '' || tdArr.eq(1).find('input').val() == '' || tdArr.eq(2).find('img').attr('src') == '' || tdArr.eq(5).find('input').eq(0).val() == '' || tdArr.eq(5).find('input').eq(1).val() == '' ){
		// 			alert('第' + start + '行填写不完整');
		// 			error ++;
		// 			return false;
		// 		}
		// 		start ++;
		// 	})
		// 	if(error > 0){
		// 		return false;
		// 	}
		// 	$("#event_form").submit();
		// })

		

  //       // 添加一行
  //       $(".addTr").click(function(){
  //       	var index = $(".eventList").find('tr').last().data('index');
  //       	if(typeof(index) == "undefined"){
  //       		index = 0;
  //       	}else{
  //       		index ++;
  //       	}
  //       	var html = '';
		// 	html += '<tr data-index="' + index + '">';
		// 	html += '<td><input type="text" class="ipt w40 tac" name="event[' + index + '][weight]" value=""></td>';
		// 	html += '<td><input type="text" class="ipt tac w140" name="event[' + index + '][title]" value=""></td>';
		// 	html += '<td><div class="btn-white file">选择文件</div><div class="btn-white upload" data-index="' + index + '">开始上传</div><input type="hidden" name="event[' + index + '][imgUrl]" id="path_' + index + '" value=""><div id="imgdiv0" class="imgDiv"><img id="imgShow' + index + '" src="" width="100" height="50" /></div></td>';
		// 	html += '<td><input type="text" class="ipt tac w140" name="event[' + index + '][hrefUrl]" value=""></td>';
	 //        html += '<td><input type="text" class="ipt tac w40" name="event[' + index + '][lid]" value=""></td>';
	 //        html += '<td><span class="ipt ipt-date w140"><input type="text" name="event[' + index + '][start_time]" value="" class="Wdate1"><i></i></span><span class="ml8 mr8">至</span><span class="ipt ipt-date w140"><input type="text" name="event[' + index + '][end_time]" value="" class="Wdate2"><i></i></span></td>';   					
	 //        html += '<td><a href="javascript:;" class="cBlue removeTr">删除</a></td>';
	 //        html += '</tr>';
  //       	$(".eventList").prepend(html);

  //       	// 初始化
  //       	var uploader = WebUploader.create({
	 //            swf: '/caipiaoimg/v1.1/js/jUploader.swf',
	 //            pick: '.file',
	 //        });

	 //        // 时间控件
		// 	$(".Wdate1").focus(function(){
	 //            dataPicker({dateFmt:'yyyy-MM-dd 00:00:00'});
	 //        });

	 //        $(".Wdate2").focus(function(){
	 //            dataPicker({dateFmt:'yyyy-MM-dd 23:59:59'});
	 //        });
  //       })

		// // 删除
		// $(".eventList").on('click', '.removeTr', function(){
		// 	$(this).parent('td').parent('tr').remove();
		// })

		
	});
</script>
