<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：客户端管理&nbsp;&gt;&nbsp;<a href="/backend/Appconfig/banner?platform=<?php echo $platform;?>"><?php echo $platform;?>配置</a></div>
<div class="mod-tab mt20">
	<div class="mod-tab-hd remind-hd">
		<ul>
            <?php $this->load->view("appconfig/tag", array('platform' => $platform, 'tag' => 'gift')) ?>
    	</ul>
    </div>
    <div class="mod-tab-bd">
	    <ul>
	      	<li style="display: block">
                <form action="" method="post" id="gift_form">
        			<div class="data-table-list mt10">
              			<table>
                			<colgroup>
                  				<col width="9%" />
                  				<col width="15%" />
                  				<col width="25%" />
                  				<col width="10%" />
                  				<col width="14%" />
                  				<?php if(in_array($platform, array('android', 'ios'))):?>
                                <col width="7%" />
                                <col width="10%" />
                                <?php endif; ?>
                                <col width="10%" />
                			</colgroup>
    	            		<thead>
    		              		<tr>
    		                		<th>页面标题</th>
    		                		<th>图片下方文案（长度建议在<span class="cRed">6-15</span>个字以内）</th>
    		                		<th>图片</th>
                                    <th>按钮文案</th>
    		                		<th>按钮链接</th>
    		                		<?php if(in_array($platform, array('android', 'ios'))):?>
                                    <th>投注彩种</th>
                                    <th>跳转原生页</th>
                                    <th>渠道选择</th>
                                    <?php else:?>
                                    <th>是否开启</th>
                                    <?php endif; ?>
    		              		</tr>
    	            		</thead>
                			<tbody id="pic-table" class="eventList">
                				<?php $length = (in_array($platform, array('android', 'ios'))) ? 3 : 1;  for ($i = 0; $i < $length; $i++) {?>
                				<tr>
                  					<td>
                                        <input type="hidden" class="ipt w84 tac" name="banner[<?php echo $i?>][id]" value="<?php echo $info[$i]['id']; ?>">
                                        <input type="text" class="ipt w84 tac" name="banner[<?php echo $i?>][title]" value="<?php echo $info[$i]['title']; ?>">
                                    </td>
                  					<td>
                                        <input type="text" class="ipt w130 tac" name="banner[<?php echo $i?>][mark]" value="<?php echo $info[$i]['mark']; ?>">
                                    </td>
                  					<td>
                    					<div class="btn-white file">选择文件</div>
                                        <div class="btn-white upload" data-index="<?php echo $i?>">开始上传</div>
                                        <input type="hidden" name="banner[<?php echo $i?>][imgUrl]" id="bannerImgVal_<?php echo $i?>" value="<?php echo $info[$i]['imgUrl']?>">
                                        <div id="imgdiv" class="imgDiv"><img id="imgShow_<?php echo $i?>" src="<?php echo $info[$i]['imgUrl']; ?>" width="100" height="50" /></div>
                  					</td>
                  					<td>
                                        <input type="text" class="ipt w84 tac" name="banner[<?php echo $i?>][btnMsg]" value="<?php echo $info[$i]['btnMsg']; ?>">
                                    </td>
                  					<td>
                                        <input type="text" class="ipt w130 tac" name="banner[<?php echo $i?>][url]" value="<?php echo $info[$i]['url']; ?>">
                                    </td>
                                    <?php if(in_array($platform, array('android', 'ios'))):?>
                  					<td>
                                        <input type="text" class="ipt tac w40" name="banner[<?php echo $i?>][lid]" value="<?php echo $info[$i]['lid'] ?>">
                                    </td>
                                    <td>
                                        <select class="selectList w98" id="" name="banner[<?php echo $i?>][appAction]">
                                            <option value="" <?php if($info[$i]['appAction'] === ''){echo 'selected';} ?> >不使用</option>
                                            <option value="bet" <?php if($info[$i]['appAction'] === 'bet'){echo 'selected';} ?>>投注页</option>
                                            <option value="email" <?php if($info[$i]['appAction'] === 'email'){echo 'selected';} ?>>绑定邮箱</option>
                                            <option value="redpack" <?php if($info[$i]['appAction'] === 'redpack'){echo 'selected';} ?>>红包页</option>
                                        </select>
                                    </td>
                                    <td>
                                        已选择<span><?php echo !(empty($info[$i]['channels'])) ? count(explode(',', $info[$i]['channels'])) : '0'; ?></span>个
                                        <a href="javascript:;" class="cBlue selectChannels">编辑选择</a>
                                        <input type="hidden" class="ipt tac w40" name="banner[<?php echo $i?>][channels]" value="<?php echo $info[$i]['channels']?>">
                                    </td>
                                    <?php else: ?>
                                    <td>
                                        <a class="btn-blue changeStatus"><?php echo ($info[$i]['status']) ? '已开启' : '已关闭'; ?></a>
                                        <input type="hidden" name="banner[<?php echo $i?>][status]" value="<?php echo ($info[$i]['status']) ? '1' : '0'; ?>">
                                    </td>
                                    <?php endif; ?>
                                    
                				</tr>
                				<?php }?>
                			</tbody>
              			</table>
                        <div class="tac">
                            <a class="btn-blue mt20 submitGift">保存并上线</a>
                        </div>
              		</div>
              		<input type="hidden" name="platform" value="<?php echo $platform; ?>">
                </form>
	       	</li>
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
</div>
<script  src="/source/date/WdatePicker.js"></script>
<script src="/source/js/webuploader.min.js"></script>
<script>
	$(function() {
        var channelList = '<?php echo json_encode($channels['detail']); ?>';
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
    		$("#imgShow_" + data.index).attr('src', data.path + data.name);
        	$("#bannerImgVal_" + data.index).val(data.path + data.name);
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

        // 浮层开关切换
		$('.changeStatus').click(function(){
			var tag = $(this).parent().find('input');
			if(tag.val() == '0'){
				tag.val('1');
				$(this).html('已开启');
			}else{
				tag.val('0');
				$(this).html('已关闭');
			}
		});	

        // 保存上线
        $(".submitGift").click(function(){  
            var platform = $('input[name="platform"]').val(); 
            // 渠道信息检查
            if(platform != 'm'){
                var text = checkActivityChannel();
                if(text){
                    var msg = '渠道' + text + '请不要同时启用2个以上';
                    alert(msg);
                    return false;
                } 
            }   
            $("#gift_form").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").submit();
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
            title = _this.closest('tr').find('td').eq(1).find('input').val();
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
            $("#gift_form .selectChannels").each(function(){
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
</script>
