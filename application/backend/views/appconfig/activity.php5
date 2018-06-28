<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：客户端管理&nbsp;&gt;&nbsp;<a href="/backend/Appconfig/banner?platform=<?php echo $platform;?>"><?php echo $platform;?>配置</a></div>
<div class="mod-tab mt20">
	<div class="mod-tab-hd remind-hd">
		<ul>
			<?php $this->load->view("appconfig/tag", array('platform' => $platform, 'tag' => 'activity')) ?>
    	</ul>
    </div>
    <div class="mod0-tab-hd mt20">
		活动位
	</div>
    <div class="mod-tab-bd">
	    <ul>
	      	<li style="display: block">
	    		<form action="" method="post" id="activity_form">
	    			<div class="data-table-list mt10">
	          			<table>
	            			<colgroup>
	              				<col width="25%" />
	              				<col width="25%" />
	              				<col width="20%" />
	              				<col width="10%" />
	              				<col width="20%" />
	            			</colgroup>
		            		<thead>
			              		<tr>
			                		<th>模块名称</th>
			                		<th>内容</th>
			                		<th>渠道选择</th>
			                		<th>优先级</th>
			                		<th>操作</th>
			              		</tr>
		            		</thead>
	            			<tbody id="pic-table" class="top-table">
	            				<?php if(!empty($info)):?>
                            	<?php foreach ($info as $key => $items):?>
                            	<?php if(in_array($items['type'], array(1,2,3,4,7,8))): ?>
                            	<tr>
	              					<td>
	              						<input type="hidden" class="ipt tac w40" name="activity[<?php echo $key?>][type]" value="<?php echo $items['type']?>"><span><?php echo $items['title']; ?></span>
	              					</td>
	              					<td>
	              						<div><?php echo $items['content']; ?></div>
	              						<input type="hidden" class="ipt tac w40" id = "content<?php echo $items['type']; ?>" name="activity[<?php echo $key?>][content]" value="<?php echo $items['content']?>">
	              					</td>
                                    <td>
                                        已选择<span><?php echo !(empty($items['channels'])) ? count(explode(',', $items['channels'])) : '0'; ?></span>个
                                        <a href="javascript:;" class="cBlue selectChannels">编辑选择</a>
                                        <input type="hidden" class="ipt tac w40" name="activity[<?php echo $key?>][channels]" value="<?php echo $items['channels']?>">
                                    </td>
	              					<td>
	                					<input type="text" class="ipt tac w40" name="activity[<?php echo $key?>][weight]" value="<?php echo $info[$key]['weight']?>">
	              					</td>
	              					<td>
	                					<a href="javascript:;" class="cBlue editActivity" data-index="<?php echo $items['type']; ?>">编辑模块</a>
	                					<input type="hidden" id="extra<?php echo $items['type']; ?>" class="ipt tac w40" name="activity[<?php echo $key?>][extra]" value='<?php echo $info[$key]['extra']?>'>
	              					</td>
	            				</tr>
	            				<?php endif; ?>
                            	<?php endforeach;?>
                            	<?php endif; ?>
	            			</tbody>
	          			</table>
	          			<div class="tac">
	          				<a class="btn-blue mt20 submitActivity">保存并上线</a>
	          			</div>
	          		</div>
	          		<input type="hidden" name="subType" value="1">
	          		<input type="hidden" name="platform" value="<?php echo $platform; ?>">
	       		</form>
	       	</li>
	       	<li style="display: block">
	    		<form action="" method="post" id="prelaod_form">
	    			<div class="data-table-list mt10">
	          			<table>
	            			<colgroup>
	              				<col width="5%" />
	              				<col width="15%" />
	              				<col width="25%" />
	              				<col width="20%" />
	              				<col width="8%" />
	              				<col width="12%" />
	              				<col width="10%" />
	            			</colgroup>
		            		<thead>
			              		<tr>
			                		<th>序号</th>
			                		<th>标题（长度建议在<span class="cRed">10</span>个字以内）</th>
			                		<th>图片</th>
			                		<th>链接</th>
			                		<th>投注彩种</th>
			                		<th>点击跳转原生页（高优先级）</th>
			                		<th>渠道选择</th>
			              		</tr>
		            		</thead>
	            			<tbody id="pic-table">
	            				<?php if(!empty($info)):?>
	            				<?php $index = 0; ?>
                            	<?php foreach ($info as $key => $items):?>
                            	<?php if(in_array($items['type'], array(5, 6, 9))): ?>
	            				<tr>
	            					<td>
	            						<input type="hidden" class="ipt tac w40" name="activityBanner[<?php echo $index?>][type]" value="<?php echo $items['type']?>">
	                					通用尺寸
	              					</td>
	              					<td>
	                					<input type="text" class="ipt tac w184" name="activityBanner[<?php echo $index?>][content]" value="<?php echo $items['content']; ?>">
	              					</td>
	              					<td>
	              						<div class="btn-white file">选择文件</div>
	                					<div class="btn-white upload1" data-index="<?php echo $index?>">开始上传</div>
	                					<input type="hidden" name="activityBanner[<?php echo $index?>][imgUrl]" id="bannerImgVal_<?php echo $index; ?>" value="<?php echo $config[$items['type']]['activity']['imgUrl']?>">
	                					<div id="imgdiv" class="imgDiv"><img id="bannerImgShow_<?php echo $index; ?>"  src="<?php echo $config[$items['type']]['activity']['imgUrl'] ? '//' . $this->config->item('base_url') . '/uploads/appconfig/'. $platform . '/banner/' . $config[$items['type']]['activity']['imgUrl'] : ''; ?>" width="100" height="50" /></div>
	              					</td>
	              					<td>
	                					<input type="text" class="ipt tac w222" name="activityBanner[<?php echo $index?>][url]" value="<?php echo $config[$items['type']]['activity']['url'] ?>">
	              					</td>
	              					<td>
	                					<input type="text" class="ipt tac w40" name="activityBanner[<?php echo $index?>][lid]" value="<?php echo $config[$items['type']]['activity']['lid'] ?>">
	              					</td>
	              					<td>
	                					<select class="selectList w98" id="" name="activityBanner[<?php echo $index?>][appAction]">
                                            <option value="" <?php if($config[$items['type']]['activity']['appAction'] === ''){echo 'selected';} ?> >不使用</option>
                                            <option value="bet" <?php if($config[$items['type']]['activity']['appAction'] === 'bet'){echo 'selected';} ?>>投注页</option>
                                            <!-- <option value="email" <?php if($config[$items['type']]['activity']['appAction'] === 'email'){echo 'selected';} ?>>绑定邮箱</option> -->
                                        </select>
	              					</td>
	              					<td>
                                        已选择<span><?php echo !(empty($items['channels'])) ? count(explode(',', $items['channels'])) : '0'; ?></span>个
                                        <a href="javascript:;" class="cBlue selectChannels">编辑选择</a>
                                        <input type="hidden" class="ipt tac w40" name="activityBanner[<?php echo $index?>][channels]" value="<?php echo $items['channels']?>">
                                    </td>
	            				</tr>
	            				<?php $index++; ?>
	            				<?php endif; ?>
                            	<?php endforeach;?>
                            	<?php endif; ?>
	            			</tbody>
	          			</table>
	          			<div class="tac">
	          				<a class="btn-blue mt20 submitPrelaod" id="preload_banner">保存并上线</a>
	          			</div>
	          		</div>
	          		<input type="hidden" name="subType" value="2">
	          		<input type="hidden" name="platform" value="<?php echo $platform; ?>">
	       		</form>
	       	</li>
	    </ul>
    </div>
    <!-- 追号不中包赔 start -->
  	<div class="pop-dialog" id="dialog-1" style="display:none;">
		<div class="pop-in">
            <div class="pop-head">
                <h2>追号不中包赔模块编辑</h2>
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
                                <td>活动彩种：</td>
                                <td>
                                	<?php if(!empty($config[1]['config']['lotterys'])):?>
                            		<?php foreach ($config[1]['config']['lotterys'] as $lid => $lname):?>
                            		<label for="" class="mr20"><input type="radio" name="dialog1_lid" value="<?php echo $lid; ?>" <?php echo ($lid == $config[1]['activity']['lid']) ? 'checked' : ''; ?>><?php echo $lname; ?></label>
                            		<?php endforeach;?>
                            		<?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>活动内容：</td>
                                <td>
                                	<ul>
                                    <?php if(!empty($config[1]['config']['content'])):?>
                            		<?php foreach ($config[1]['config']['content'] as $issue => $content):?>
                            		<li class="mt10">
                            		<label for="" class="mr20"><input type="radio" name="issue" value="<?php echo $issue; ?>" <?php echo ($issue == $config[1]['activity']['issue']) ? 'checked' : ''; ?>><?php echo $content; ?></label>
                            		</li>
                            		<?php endforeach;?>
                            		<?php endif; ?>
                            		</ul>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-blue-h32 mlr15" id="confirm-1">确认</a>
                <a href="javascript:;" class="btn-blue-h32 mlr15 reload-cancel">取消</a>
            </div>
        </div>
	</div>
	<!-- 追号不中包赔 end -->
	<!-- 竞彩模块 start -->
  	<div class="pop-dialog" id="dialog-2" style="display:none;">
		<div class="pop-in">
            <div class="pop-head">
                <h2>竞彩模块编辑</h2>
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
                                <td>选择彩种：</td>
                                <td>
                                	<?php if(!empty($config[2]['config']['lotterys'])):?>
                            		<?php foreach ($config[2]['config']['lotterys'] as $lid => $lname):?>
                            		<label for="" class="mr20"><input type="radio" name="dialog2_lid" value="<?php echo $lid; ?>" <?php echo ($lid == $config[2]['activity']['lid']) ? 'checked' : ''; ?>><?php echo $lname; ?></label>
                            		<?php endforeach;?>
                            		<?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>过关方式：</td>
                                <td class="selectPlaytype">
                                    <?php if(!empty($config[2]['config']['playtype'])):?>
                            		<?php foreach ($config[2]['config']['playtype'] as $playtype => $name):?>
                            		<label for="" class="mr20"><input type="radio" name="playtype" value="<?php echo $playtype; ?>" <?php echo ($playtype == $config[2]['activity']['playtype']) ? 'checked' : ''; ?>><?php echo $name; ?></label>
                            		<?php endforeach;?>
                            		<?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>场次编号：</td>
                                <td>
                                	<ul>
                                    <?php if(!empty($config[2]['activity']['mid'])):?>
                            		<?php foreach (explode(',', $config[2]['activity']['mid']) as $key => $mid):?>
                            		<li class="mt10">
                            		<input type="text" class="ipt tac w130" name="mid" value="<?php echo $mid; ?>">
                            		</li>
                            		<?php endforeach;?>
                            		<?php else: ?>
                            		<li class="mt10">
                            		<input type="text" class="ipt tac w130" name="mid" value="">
                            		</li>
                            		<?php endif; ?>
                            		</ul>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-blue-h32 mlr15" id="confirm-2">确认</a>
                <a href="javascript:;" class="btn-blue-h32 mlr15 reload-cancel">取消</a>
            </div>
        </div>
	</div>
	<!-- 竞彩模块 end -->
	<!-- 数字彩模块 start -->
  	<div class="pop-dialog" id="dialog-3" style="display:none;">
		<div class="pop-in">
            <div class="pop-head">
                <h2>数字彩模块编辑</h2>
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
                                <td>选择彩种：</td>
                                <td>
                                	<ul>
                                	<?php if(!empty($config[3]['config']['lotterys'])):?>
                            		<?php foreach ($config[3]['config']['lotterys'] as $lid => $lname):?>
                            		<li class="mt10">
                            		<label for="" class="mr20"><input type="radio" name="dialog3_lid" value="<?php echo $lid; ?>" <?php echo ($lid == $config[3]['activity']['lid']) ? 'checked' : ''; ?>><?php echo $lname; ?></label>
                            		</li>
                            		<?php endforeach;?>
                            		<?php endif; ?>
                            		</ul>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-blue-h32 mlr15" id="confirm-3">确认</a>
                <a href="javascript:;" class="btn-blue-h32 mlr15 reload-cancel">取消</a>
            </div>
        </div>
	</div>
	<!-- 数字彩模块 end -->
	<!-- 自定义模块 start -->
  	<div class="pop-dialog" id="dialog-4" style="display:none;">
		<div class="pop-in">
            <div class="pop-head">
                <h2>自定义活动模块编辑</h2>
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
                                <td>活动banner：</td>
                                <td>
                                	<div class="btn-white file">选择文件</div>
                					<div class="btn-white upload2" data-index="4">开始上传</div>
                					<input type="hidden" id="path" name="imgUrl" value="<?php echo $config[4]['activity']['imgUrl']?>">
                					<div id="imgdiv" class="imgDiv"><img id="imgShow" src="<?php echo $config[4]['activity']['imgUrl'] ? '//' . $this->config->item('base_url') . '/uploads/appconfig/'. $platform . '/banner/' . $config[4]['activity']['imgUrl'] : ''; ?>" width="100" height="50" /></div>
                                </td>
                            </tr>
                            <tr>
                                <td>链接名称：</td>
                                <td>
                                	<input type="text" class="ipt tac w130" name="url" value="<?php echo $config[4]['activity']['url']; ?>">
                                	<a href="javascript:;" class="cBlue removeTr">清空</a>
                                </td>
                            </tr>
                            <tr>
                                <td>投注彩种：</td>
                                <td>
                                	<input type="text" class="ipt tac w130" name="lid" value="<?php echo $config[4]['activity']['lid']; ?>">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-blue-h32 mlr15" id="confirm-4">确认</a>
                <a href="javascript:;" class="btn-blue-h32 mlr15 reload-cancel">取消</a>
            </div>
        </div>
	</div>
    <div class="pop-dialog" id="dialog-7" style="display:none;">
        <div class="pop-in">
            <div class="pop-head">
                <h2>自定义活动模块编辑</h2>
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
                                <td>活动banner：</td>
                                <td>
                                    <div class="btn-white file">选择文件</div>
                                    <div class="btn-white upload2" data-index="7">开始上传</div>
                                    <input type="hidden" id="path" name="imgUrl" value="<?php echo $config[7]['activity']['imgUrl']?>">
                                    <div id="imgdiv" class="imgDiv"><img id="imgShow" src="<?php echo $config[7]['activity']['imgUrl'] ? '//' . $this->config->item('base_url') . '/uploads/appconfig/'. $platform . '/banner/' . $config[7]['activity']['imgUrl'] : ''; ?>" width="100" height="50" /></div>
                                </td>
                            </tr>
                            <tr>
                                <td>链接名称：</td>
                                <td>
                                    <input type="text" class="ipt tac w130" name="url" value="<?php echo $config[7]['activity']['url']; ?>">
                                    <a href="javascript:;" class="cBlue removeTr">清空</a>
                                </td>
                            </tr>
                            <tr>
                                <td>投注彩种：</td>
                                <td>
                                    <input type="text" class="ipt tac w130" name="lid" value="<?php echo $config[7]['activity']['lid']; ?>">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-blue-h32 mlr15" id="confirm-7">确认</a>
                <a href="javascript:;" class="btn-blue-h32 mlr15 reload-cancel">取消</a>
            </div>
        </div>
    </div>
    <div class="pop-dialog" id="dialog-8" style="display:none;">
        <div class="pop-in">
            <div class="pop-head">
                <h2>自定义活动模块编辑</h2>
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
                                <td>活动banner：</td>
                                <td>
                                    <div class="btn-white file">选择文件</div>
                                    <div class="btn-white upload2" data-index="8">开始上传</div>
                                    <input type="hidden" id="path" name="imgUrl" value="<?php echo $config[8]['activity']['imgUrl']?>">
                                    <div id="imgdiv" class="imgDiv"><img id="imgShow" src="<?php echo $config[8]['activity']['imgUrl'] ? '//' . $this->config->item('base_url') . '/uploads/appconfig/'. $platform . '/banner/' . $config[8]['activity']['imgUrl'] : ''; ?>" width="100" height="50" /></div>
                                </td>
                            </tr>
                            <tr>
                                <td>链接名称：</td>
                                <td>
                                    <input type="text" class="ipt tac w130" name="url" value="<?php echo $config[8]['activity']['url']; ?>">
                                    <a href="javascript:;" class="cBlue removeTr">清空</a>
                                </td>
                            </tr>
                            <tr>
                                <td>投注彩种：</td>
                                <td>
                                    <input type="text" class="ipt tac w130" name="lid" value="<?php echo $config[8]['activity']['lid']; ?>">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-blue-h32 mlr15" id="confirm-8">确认</a>
                <a href="javascript:;" class="btn-blue-h32 mlr15 reload-cancel">取消</a>
            </div>
        </div>
    </div>
	<!-- 自定义模块 end -->
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
<script>
	$(function() {
        var channelList = '<?php echo json_encode($channels['detail']); ?>';
		// 表单提交
		$(".submitActivity").click(function(){
            // 渠道检查活动开启数
            var text = checkActivityChannel();
            if(text){
                alert(text);
                return false;
            }
			$("#activity_form").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").submit();
		})

        function checkActivityChannel(){
            var text = [];
            var diff = [];
            var list = [];
            var zdy = [];
            $('.top-table tr').each(function(){
                var type = $(this).find('td').eq(0).find('input').val();
                var channels = $(this).find('td').eq(2).find('input').val();
                if(channels != ''){
                    var channelArr = channels.split(',');
                    for (var i = 0; i < channelArr.length; i++) {
                        var val = channelArr[i];
                        // 自定义类型汇总
                        if(type == 4 || type == 7 || type == 8){
                            if(zdy[val] == undefined){
                                zdy[val] = 1;
                            }else{
                                zdy[val] += 1;
                            }
                        }
                        if(list[val] == undefined){
                            list[val] = 1;
                        }else{
                            list[val] += 1;
                        }
                    }
                }
            });

            var msg = "";
            // 获取渠道映射
            var channelMaps = [];
            $.each($.parseJSON(channelList), function(index, items){
                channelMaps[items['id']] = items['name'];
            });

            if(zdy.length > 0){
                $.each(zdy, function(x, v){
                    if(v != undefined && v > 1 && $.inArray(v, diff) < 0){
                        diff.push(x);
                    }
                });

                // 去重
                diff = $.unique(diff);

                if(diff.length > 0){
                    for (var index = 0; index < diff.length; index++) {
                        text.push(channelMaps[diff[index]]);
                    }
                }

                if(text.length > 0){
                   msg = '渠道' + text.join(',') + '请不要同时启用1个以上的自定义活动模块';
                   return msg;
                }
            }

            if(list.length > 0){
                $.each(list, function(x, v){
                    if(v != undefined && v > 2 && $.inArray(v, diff) < 0){
                        diff.push(x);
                    }
                });

                // 去重
                diff = $.unique(diff);

                if(diff.length > 0){
                    for (var index = 0; index < diff.length; index++) {
                        text.push(channelMaps[diff[index]]);
                    }
                }

                if(text.length > 0){
                   msg = '渠道' + text.join(',') + '请不要同时启用3个或3个以上活动模块';
                   return msg;
                }
            }
            return msg;
        }

		$(".submitPrelaod").click(function(){
			$("#prelaod_form").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").submit();
		})

        // 底部banner - 初始化
        var uploader = WebUploader.create({
            swf: '/caipiaoimg/v1.1/js/jUploader.swf',
            pick: '.file',
        });

        // 底部banner - 上传
        $(".upload1").click(function(){
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

        // 自定义banner - 上传
        $(".upload2").click(function(){
            var platform = $('input[name="platform"]').val();
            uploader.options.server = "/backend/Appconfig/uploadbanner/" + platform + "/" + $(this).data('index') + "/zdy";
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

        // 底部banner/自定义banner - 上传成功
        uploader.on( 'uploadSuccess', function( file, data) {
            if(data.type == 'zdy'){
                $("#dialog-" + data.index + " #imgShow").attr('src', data.path + data.name);
                $('#dialog-' + data.index + ' input[name="imgUrl"]').val(data.name)
            }else{
                $("#bannerImgShow_" + data.index).attr('src', data.path + data.name);
                $('#bannerImgVal_' + data.index).val(data.name)
            }
        });

        // 编辑模块
        $(".editActivity").click(function(){
        	popdialog("dialog-" + $(this).data('index'));
        })

		// 取消并刷新
		$(".reload-cancel").click(function(){
			closePop();
			window.location.reload();
		})

		// 不中包赔 - 确认编辑
		$("#confirm-1").click(function(){
			var config = {
				'lid' : $('#dialog-1 input[name="dialog1_lid"]:checked').val(),
				'issue' : $('#dialog-1 input[name="issue"]:checked').val(),
				'content' : $('#dialog-1 input[name="issue"]:checked').parent().text()
			};
			// 修改配置
			$("input[id='extra1']").val(JSON.stringify(config));
			// 修改内容展示
			var content = $('#dialog-1 input[name="dialog1_lid"]:checked').parent().text() + ' ' + config['issue'] + '期';
			$('#content1').parent().find('div').text(content);
			$("#content1").val(content);
			closePop();
		})

		// 竞彩模块 - 切换按钮
		$(".selectPlaytype :radio").click(function(){
			var num = $(this).val();
			changeJcInput(num);
  		});

  		function changeJcInput(num){
  			var length = $("#dialog-2 li").length;
  			if(length > num){
  				var k = -1;
  				for (var i = 0; i < length - num; i++) {
  					$("#dialog-2 li").eq(k).remove();
  					k--;
  				}
  			}
  			if(length < num){
  				for (var i = 0; i < num - length; i++) {
  					$("#dialog-2 li").after('<li class="mt10"><input type="text" class="ipt tac w130" name="mid" value=""></li>');
  				}	
  			}
  		}

		// 竞彩模块 - 确认编辑
		$("#confirm-2").click(function(){
			var mid = [];
			var midName = [];
			$('#dialog-2 input[name="mid"]').each(function(){
				mid.push($(this).val());
				midName.push();
			})
			var mids = '';
			var midNames = '';
			if(mid.length > 0){
				mids = mid.join(',');
			}

			var config = {
				'lid' : $('#dialog-2 input[name="dialog2_lid"]:checked').val(),
				'playtype' : $('#dialog-2 input[name="playtype"]:checked').val(),
				'mid' : mids
			};

            // 检查配置
            $.ajax({
                type: 'post',
                url: '/backend/Appconfig/checkJcActivity',
                data: config,
                success: function (response) {
                    var response = $.parseJSON(response);
                    if(response.status == 'y')
                    {
                        // 修改配置
                        $("input[id='extra2']").val(JSON.stringify(config));
                        // 修改内容展示
                        var content = $('#dialog-2 input[name="dialog2_lid"]:checked').parent().text() + ' ' + $('#dialog-2 input[name="playtype"]:checked').parent().text();
                        $('#content2').parent().find('div').text(content);
                        $("#content2").val(content);
                        closePop();
                    }else{
                        alert(response.message);
                    }
                },
                error: function () {
                    alert('网络异常，请稍后再试');
                }
            });		
		})

		// 数字彩 - 确认编辑
		$("#confirm-3").click(function(){
			var config = {
				'lid' : $('#dialog-3 input[name="dialog3_lid"]:checked').val(),
			};
			// 修改配置
			$("input[id='extra3']").val(JSON.stringify(config));
			// 修改内容展示
			var content = $('#dialog-3 input[name="dialog3_lid"]:checked').parent().text();
			$('#content3').parent().find('div').text(content);
			$("#content3").val(content);
			closePop();
		})

		// 自定义 - 确认编辑
		$("#confirm-4").click(function(){
			var config = {
				'imgUrl' : $('#dialog-4 input[name="imgUrl"]').val(),
				'url' : $('#dialog-4 input[name="url"]').val(),
				'lid' : $('#dialog-4 input[name="lid"]').val(),
			};
			// 修改配置
			$("input[id='extra4']").val(JSON.stringify(config));
			closePop();
		})

        // 自定义 - 确认编辑
        $("#confirm-7").click(function(){
            var config = {
                'imgUrl' : $('#dialog-7 input[name="imgUrl"]').val(),
                'url' : $('#dialog-7 input[name="url"]').val(),
                'lid' : $('#dialog-7 input[name="lid"]').val(),
            };
            // 修改配置
            $("input[id='extra7']").val(JSON.stringify(config));
            closePop();
        })

        // 自定义 - 确认编辑
        $("#confirm-8").click(function(){
            var config = {
                'imgUrl' : $('#dialog-8 input[name="imgUrl"]').val(),
                'url' : $('#dialog-8 input[name="url"]').val(),
                'lid' : $('#dialog-8 input[name="lid"]').val(),
            };
            // 修改配置
            $("input[id='extra8']").val(JSON.stringify(config));
            closePop();
        })

		// 清空
		$(".removeTr").click(function(){
			$(this).parents('tr').find('input').val('');
			$(this).parents('tr').find('img').attr('src', '');
		})

		// banner显示切换
		$(".showActBanner").click(function(){
			var status = $(this).parent().find('input').val();
			if(status > 0){
				$(this).parent().find('input').val('0');
				$(this).text('已关闭');
			}else{
				$(this).parent().find('input').val('1');
				$(this).text('已显示');
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
            if(tag.indexOf('activityBanner') >= 0){
                title = _this.closest('tr').find('td').eq(1).find('input').val();
            }else{
                title = _this.closest('tr').find('td').eq(0).find('span').html();
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
	});
</script>
