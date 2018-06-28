<?php $this->load->view("templates/head") ?>
<style type="text/css">
	._red{color:#f00;font-style: normal;}
	._normal{font-style: normal;}
	.ml2c{margin-left: 2em}
	.data-table-list tr:hover td{background:#efefef}
</style>
<div id="app">
    <div class="path">您的位置：<a href="javascript:;">渠道分析</a>&nbsp;&gt;&nbsp;<a href="/backend/ChannelAnalysis/manage">渠道管理</a></div>
    <div class="mod-tab mt20">
        <div class="mod-tab-hd">
            <ul>
              <li class="current"><a href="/backend/ChannelAnalysis/manage">渠道管理</a></li>
              <li><a href="/backend/ChannelAnalysis/countData">渠道数据</a></li>
              <li><a href="/backend/ChannelAnalysis/scoreAndRet">渠道评分及扣减</a></li>
            </ul>
        </div>
        <div class="mod-tab-bd"><ul><li style="display: list-item;">
        <div class="data-table-filter mt10">
            <form action="/backend/ChannelAnalysis/manage" method="post" id="search_form" name="search_form">
	            <table>
	            	<colgroup>
	            		<col width="190">
	                    <col width="190">
	                    <col width="190">
	                    <col width="190">
	                </colgroup>
	                <tbody>
	                <tr>
	                    <td>
							<label for=""><span class="ml2c">平台：</span>
							    <select class="selectList w98" id="platform" name="platform">
							        <?php foreach ($platform as $key => $val):?>
							            <option value="<?php echo $key;?>" <?php if($search['platform'] === "{$key}"): echo "selected"; endif;?>><?php echo $val;?></option>
							        <?php endforeach;?>
							    </select>
							</label>
	                    </td>
	                    <td>渠道名称：<input class="ipt w98" name="name" value="<?php echo $search['name']?>"></td>
	                    <td>渠道号：<input class="ipt w98" name="id" value="<?php echo $search['id']?>"></td>
	                    <?php if(in_array($search['platform'], array(2, 3))){ ?>
	                        <td>
							    <label for="">应用名称：
							       	<select class="selectList w110" id="package" name="package">
							       	    <option value="0">全部</option>
							             <?php foreach ($packages as $val):?>
							                <option value="<?php echo $val['id'];?>" 
							                  	<?php if($search['package'] == "{$val['id']}"){
							                  		echo "selected";
							                  	}?>>
							                  	<?php echo $val['name'];?>
							                </option>
							            <?php endforeach;?>
							        </select>
							    </label>
	                        </td>
	                    <?php } ?>
	                </tr>
	                <tr>
	                	<td>
							<label for="">结算方式：
							    <select class="selectList w98" id="settlemode" name="settlemode">
							       	<option value="0">全部</option>
							        <?php foreach ($settleModeArr as $key => $val):?>
							            <option value="<?php echo $key;?>" <?php if($search['settlemode'] === "{$key}"): echo "selected"; endif;?>><?php echo $val;?></option>
							        <?php endforeach;?>
							    </select>
							</label>
	                    </td>
	                	<td>
							<label for="">推广状态：
							    <select class="selectList w98" id="status" name="status">
							        <?php foreach ($statusArr as $key => $val):?>
							            <option value="<?php echo $key;?>" <?php if($search['status'] === "{$key}"): echo "selected"; endif;?>><?php echo $val;?></option>
							        <?php endforeach;?>
							    </select>
							</label>
	                    </td>
	                    <?php if(in_array($search['platform'], array(2, 3))){ ?>
	                        <td></td>
	                    <?php } ?>
	                    <td>
	                        <a id="search" href="javascript:;" class="btn-blue">查询</a>
	                        <a href="javascript:;" class="btn-blue ml25" id="update">新增渠道</a>
	                        <a href="javascript:;" class="btn-blue btn-table-modify ml25">保存</a>
	                        <a href="javascript:;" class="btn-blue ml25" id="addPackage">新增应用名称</a>
	                    </td>
	                </tr>
	                </tbody>
	            </table>
            </form>
        </div>
		<!--数据循环-->
		<div class="data-table-list mt10">
					<table id="manage">
					    <colgroup>
					      	<col width="65" />
					      	<col width="45" />
					      	<col width="70" />
					      	<!-- <col width="85"> -->
					      	<col width="70" />
					      	<col width="80" />
					      	<col width="100" />
					      	<?php if($search['platform']=='2'){ ?>
					      	<col width="100" />
					      	<col width="70" />
					      	<col width="70" />
					      	<col width="100" />
					      	<?php } ?>
					      	<?php if($search['platform']=='4'){ ?>
					        <col width="100" />
					      	<?php } ?>
					      	<!-- 新增马甲包 -->
					      	<?php if(in_array($search['platform'], array(2, 3))): ?>
					      		<col width="60" />	
					      	<?php endif; ?>
					      	<col width="45" />
					      	<col width="45" />
					      	<col width="45" />
					    </colgroup>
					    <thead>
					      <tr>
					        <th>渠道名称</th>
					        <th>结算方式</th>
					        <th>单价(CPA,元)</th>
					        <!--<th>核减系数(CPA)</th>-->
					        <th>分成比例(CPS)</th>
					        <!-- <th>月费(CPT)</th> -->
					        <th style="color:#f00;">注册时限(CPS,天)</th>
					        <th>链接</th>
					        <?php if($search['platform']=='2'){ ?>
					        <th>包名</th>
					        <th>包上传</th>
					        <th>开关</th>
					        <th>对应IOS下载链接</th>
					        <?php } ?>
					        <?php if($search['platform']=='4'){ ?>
					        <th>对应渠道包链接</th>
					        <?php } ?>
					        <?php if(in_array($search['platform'], array(2, 3))): ?>
					      		<th>应用名称</th>
					      	<?php endif; ?>
					        <th>渠道别名</th>
					        <th>推广状态</th>
					         <th>操作</th>
					      </tr>
					    </thead>
					    <tbody>
					    <?php foreach ($list as $val):?>
					      <tr id="<?php echo $val['id'];?>">
					        <td>
						        <div class="table-modify">
						            <p class="table-modify-txt"><?php echo $val['name']; ?><i></i></p>
						            <p class="table-modify-ipt"><input type="text" id="alt_channelname" name="alt_channelname" class="ipt" value="<?php echo $val['name']; ?>"><i></i></p>
						         </div>
					        </td>
					        <td>
					        	 <div class="table-modify">
						            <p class="table-modify-txt"><?php echo $settleModeArr[$val['settle_mode']]; ?><i></i></p>
						            <p class="table-modify-ipt"><input type="text" id="alt_settlemode" name="alt_settlemode" class="ipt" value="<?php echo $settleModeArr[$val['settle_mode']]; ?>"><i></i></p>
						         </div>
					        </td>
					        <td>
								<?php if($val['settle_mode'] == 1){ ?>
					        	<div class="table-modify">
						            <p class="table-modify-txt"><?php echo number_format($val['unit_price']/100,2); ?><i></i></p>
						            <p class="table-modify-ipt"><input type="text" id="alt_cpa" name="alt_cpa" class="ipt" value="<?php echo number_format($val['unit_price']/100,2); ?>"><i></i></p>
						         </div>
								<?php } else{ ?>
								<div>N/A</div>
								<?php } ?>
					        </td>
					        <td>
								<?php if($val['settle_mode'] == 2){ ?>
					        	<div class="table-modify">
						            <p class="table-modify-txt"><?php echo number_format($val['share_ratio'],2); ?>%<i></i></p>
						            <p class="table-modify-ipt"><input type="text" class="ipt" id="alt_cps" name="alt_cps" value="<?php echo number_format($val['share_ratio'],2); ?>">%<i></i></p>
						         </div>
								<?php } else{ ?>
								<div>N/A</div>
								<?php } ?>
						    </td>
						    <td>
								<?php if($val['settle_mode'] == 2){ ?>
					        	<div class="table-modify">
						            <p class="table-modify-txt"><?php echo $val['reg_time'] ? '≤'.$val['reg_time'] : '0'; ?><i></i></p>
						            <p class="table-modify-ipt"><input type="text" class="ipt" id="alt_regTime" name="alt_regTime" value="<?php echo $val['reg_time']; ?>"><i></i></p>
						         </div>
								<?php } else{ ?>
								<div>N/A</div>
								<?php } ?>
						    </td>
					        <td>
					            <?php if($search['platform']=='2'){ $url = $this->url_prefix."://".$this->config->item('base_url')."/app/download/?c=".$val['id'];?>
					            <a href="<?php echo $url; ?>" target="_blank"><?php echo $url; ?></a>
					            <?php }elseif($search['platform']=='4'){ $url = $this->config->item('m_pages_url')."?cpk=".$val['id'];?>
					            <a href="<?php echo $url; ?>" target="_blank"><?php echo $url; ?></a>
					            <?php }else{ $url = $this->url_prefix."://".$this->config->item('base_url')."?cpk=".$val['id']; ?>
					            <a href="<?php echo $url; ?>" target="_blank"><?php echo $url; ?></a>
					            <?php } ?>
					            </td>
					        <?php if($search['platform']=='2'){ ?>
					        <td>
					            <div><?php echo str_replace('/uploads/apk/','',$val['app_path']);?></div>      
					        </td>
					        <td>
					         <div class="btn-white file">选择文件</div>
					         <div class="btn-white ycfcupload" data-id="<?php echo $val['id'];?>">开始上传</div>
					        </td>
					        <td>
            					<a class="isShow <?php echo ($val['cstate'] & 1) ? 'btn-red' : 'btn-blue'; ?>" data-val="<?php echo $val['id']; ?>" data-name="<?php echo $val['name']; ?>" data-index="<?php echo ($val['cstate'] & 1) ? '1' : '0'; ?>"><?php echo ($val['cstate'] & 1) ? '已停售' : '已开启'; ?></a>
          					</td>
					        <td>
					        	<div class="table-modify">
						            <p class="table-modify-txt"><?php echo $val['ios_download'] ? '≤'.$val['ios_download'] : 'App Store'; ?><i></i></p>
						            <p class="table-modify-ipt"><input type="text" class="ipt" id="alt_download" name="alt_download" value="<?php echo $val['ios_download']; ?>"><i></i></p>
						        </div>
					        </td>
					        <?php } ?>
					        <?php if($search['platform']=='4'){ ?>
					        <td>
					          <div class="table-modify">
					            <p class="table-modify-txt"><?php echo $val['app_path']?$val['app_path']:'无'; ?><i></i></p>
					            <p class="table-modify-ipt"><input type="text" id="alt_app_path" name="alt_app_path" class="ipt" value="<?php echo $val['app_path']?$val['app_path']:'无'; ?>"><i></i></p>
					          </div>  
					        </td>
					        <?php } ?>
					        <!-- 新增马甲包 -->
					      	<?php if(in_array($search['platform'], array(2, 3))): ?>
					      	<td>
					      		<a href="javascript:;" data-package="<?php echo $val['package']; ?>" class="alterPackage"><?php echo $val['pname'] ? $val['pname'] : ''; ?></a>
						    </td>	
					      	<?php endif; ?>
					        <td>
					        	<div class="table-modify">
						            <p class="table-modify-txt"><?php echo $val['nick_name']?$val['nick_name']:'无'; ?><i></i></p>
						            <p class="table-modify-ipt"><input type="text" id="alt_nick_name" name="alt_nick_name" class="ipt" value="<?php echo $val['nick_name']?$val['nick_name']:'无'; ?>"><i></i></p>
						        </div>
						    </td>
						    <td>
						    	<a href="javascript:;" data-status="<?php echo $val['status']; ?>" class="alterStatus"><?php echo $statusArr[$val['status']] ? $statusArr[$val['status']] : ''; ?></a>	
						    </td>
					        <td>
					        	<a href="<?php echo "/backend/ChannelAnalysis/loglist?channel_id={$val['id']}";?>" class="cBlue" id="loglist" name="loglist">变更日志</a><!-- &nbsp;&nbsp;<a href="javascript:;" class="cBlue _modify_pwd" data-id='<?php echo $val['id']?>' data-name='<?php echo $val['name']; ?>'>生成默认密码</a> -->
					        </td>
					      </tr>
					      <?php endforeach;?>
					    </tbody>
					</table>
		</div>
	    </li></ul></div>
    </div>
</div>
<div class="pop-mask" style="display:none"></div>
<!-- 新增渠道 -->
<form id='updateForm' name='updateForm' method='post' action=''>
<div class="pop-dialog" id="updatePop" style="display: none;">
	<div class="pop-in">
		<div class="pop-head">
			<h2>新增渠道</h2>
			<span class="pop-close _cancle" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<div class="data-table-list table-no-border">
				<table>
					<colgroup>
						<col width="100" />
		                <col width="220" />
					</colgroup>
					<tbody id='_tbody'>
						<tr>
							<td class="tar">
								<label for="">平台：</label>
							</td>
							 <td class="tal">
								<select class="selectList w202 _val addChannel" id="pplatform" name="pplatform">
						              <?php foreach ($platform as $key => $val):?>
						             	 <option value="<?php echo $key;?>" <?php if($search['platform'] === "{$key}"): echo "selected"; endif;?>><?php echo $val;?></option>
						              <?php endforeach;?>
						        </select>
						        <span class='hide_span'></span>
		            		</td>
						</tr>
						<tr>
							<td class="tar">
								<label for="">渠道名称：</label>
							</td>
							<td class="tal">
								<input type="text" class="ipt w202 _val" id="pchannelname" name="pchannelname" placeholder="请输入渠道名称">
								<span class='hide_span'></span>
							</td>
						</tr>
						<tr>
							<td class="tar">
								<label for="">结算方式：</label>
							</td>
							<td class="tal">
								<select class="selectList w202 _val" id="psettlemode" name="psettlemode">
					            	  <?php foreach ($settleModeArr as $key => $val):?>
						             	 <option value="<?php echo $key;?>" <?php if($key==1):echo "selected"; endif;?>><?php echo $val;?></option>
						              <?php endforeach;?>
					            </select>
					            <span class='hide_span'></span>
							</td>
						</tr>
						<tr id="unit">
							<td class="tar">
								<label for="">单价(CPA,元)：</label>
							</td>
							<td class="tal">
								<input type="text" class="ipt w202 _val" id="punit" name="punit" placeholder="举例：1.50">
								<span class='hide_span _red'></span>
							</td>
						</tr>
                        <?php if($search['platform']=='4'){ ?>
                                                <tr id="cpa">
							<td class="tar">
								<label for="">对应渠道包链接：</label>
							</td>
							<td class="tal">
								<input type="text" class="ipt w202 _val" id="path" name="path" placeholder="">
								<span class='hide_span'></span>
							</td>
						</tr>
                        <?php } ?>
						<tr id="cps" style="display: none;">
							<td class="tar">
								<label for="">分成比例(CPS)：</label>
							</td>
							<td class="tal">
								<input type="text" class="ipt w202 _val" id="pcps" name="pcps" placeholder="举例：6.00% 则输入6.00"><span class='hide_span _red'></span><i class='_normal'>%</i>
								
							</td>
						</tr>
						<tr id="reg_times" style="display: none;">
							<td class="tar">
								<label for="" class='_reg _red'>注册小于等于：</label>
							</td>
							<td class="tal">
								<input type="text" class="ipt w202 _val" id="reg_time" name="reg_time" placeholder="举例：注册近3个月购彩则输入90"/><span class='hide_span _red'></span><i class='_red'>天</i>
							</td>
						</tr>
						<tr id="reg_times1" style="display: none;">
							<td class="tar">
								<label for="">&nbsp;</label>
							</td>
							<td class="tal">
								注：无注册时限输入*
							</td>
						</tr>
						<tr>
							<td class="tar">
								<label for="">渠道别名：</label>
							</td>
							<td class="tal">
								<input type="text" class="ipt w202 _val" id="pnickname" name="pnickname" placeholder="请输入渠道别名">
								<span class='hide_span'></span>
							</td>
						</tr>
						<?php if(in_array($search['platform'], array(2, 3))): ?>
						<tr id="showPackages">
							<td class="tar">
								<label for="">所属应用名称：</label>
							</td>
							<td class="tal" id="packageDetail">
								<select class="selectList w202 _val" id="package" name="package">
									<?php foreach ($packages as $key => $val):?>
					             		<option value="<?php echo $val['id'];?>" <?php if($key==0):echo "selected"; endif;?>><?php echo $val['name'];?></option>
					              	<?php endforeach;?>
								</select>
								<span class="hide_span"></span>
							</td>
						</tr>
						<?php else: ?>
						<tr id="showPackages" style="display: none;">
							<td class="tar">
								<label for="">所属应用名称：</label>
							</td>
							<td class="tal" id="packageDetail">
								<input type="hidden" class="ipt w202 _val" id="package" name="package" value="0">
							</td>
						</tr>
						<?php endif; ?>
						<tr>
							<td class="tac" colspan="2">
								<a href="javascript:;" class="btn-blue-h32 mr10"  id="sureSubmit" name="sureSubmit">确定</a>
								<a href="javascript:;" class="btn-blue-h32 mr10"  id="updateSubmit" name="updateSubmit" style="display: none;">确定</a>
								<a href="javascript:;" class="btn-b-white mlr15 pop-cancel _cancle">关闭</a>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
</form>
<div class="pop-dialog" id="alertPop">
	<div class="pop-in">
		<div class="pop-head">
			<h2>提示</h2>
			<span class="pop-close _cancle" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<div class="data-table-filter del-percent" id="alertBody">
			</div>
		</div>
		<div class="pop-foot tac">
			<a href="javascript:;" class="btn-b-white mlr15 pop-cancel">确认</a>
		</div>
	</div>
</div>
<!-- 新增应用名称 -->
<form id='updatePackage' name='updatePackage' method='post' action=''>
<div class="pop-dialog" id="packagePop" style="display: none;">
	<div class="pop-in">
		<div class="pop-head">
			<h2>新增应用名称</h2>
			<span class="pop-close _cancle" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<div class="data-table-list table-no-border">
				<table>
					<colgroup>
						<col width="100" />
		                <col width="220" />
					</colgroup>
					<tbody id='_tbody'>
						<tr>
							<td class="tar">
								<label for="">平台：</label>
							</td>
							 <td class="tal">
								<select class="selectList w202 _val" id="pplatform" name="pplatform">
									<?php foreach ($platform as $key => $val):?>
										<?php if(in_array($key, array(2, 3))): ?>
										<option value="<?php echo $key;?>" <?php if($search['platform'] === "{$key}"): echo "selected"; endif;?>><?php echo $val;?></option>
										<?php endif; ?>
						            <?php endforeach;?>
						        </select>
		            		</td>
						</tr>
						<tr>
							<td class="tar">
								<label for="">应用名称：</label>
							</td>
							<td class="tal">
								<input type="text" class="ipt w202 _val" id="packagename" name="packagename" placeholder="例如166彩票">
							</td>
						</tr>
						<tr>
							<td class="tac" colspan="2">
								<a href="javascript:;" class="btn-blue-h32 mr10" id="submitPackage">确定</a>
								<a href="javascript:closePop();" class="btn-b-white mlr15">关闭</a>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
</form>
<!-- 修改应用名称 -->
<form id='alterPackage' name='alterPackage' method='post' action=''>
<div class="pop-dialog" id="alterPop" style="display: none;">
	<div class="pop-in">
		<div class="pop-head">
			<h2>修改应用主包</h2>
			<span class="pop-close _cancle" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<div class="data-table-list table-no-border">
				<table>
					<colgroup>
						<col width="100" />
		                <col width="220" />
					</colgroup>
					<tbody id='_tbody'>
						<tr>
							<td class="tar">
								<label for="">渠道名称：</label>
							</td>
							<td class="tal" id="channelName"></td>
						</tr>
						<tr>
							<td class="tar">
								<label for="">应用名称：</label>
							</td>
							<td class="tal">
								<select class="selectList w202" id="packageData" name="packageData">
								<?php foreach ($packages as $val):?>
									<option value="<?php echo $val['id'];?>"><?php echo $val['name'];?></option>
					            <?php endforeach;?>
					            </select>
								<input type="hidden" class="ipt w202" id="channelId" name="channelId" value="">
								<input type="hidden" class="ipt w202" id="defaultPackage" name="defaultPackage" value="">
							</td>
						</tr>
						<tr>
							<td class="tac" colspan="2">
								<a href="javascript:;" class="btn-blue-h32 mr10" id="submitAlterPackage">确定</a>
								<a href="javascript:closePop();" class="btn-b-white mlr15">关闭</a>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
</form>

<script src="/source/js/webuploader.min.js"></script>
<!--引入第三方插件 layer.js-->
<script src="/caipiaoimg/src/layer/layer.js"></script>
<script type="text/javascript">
	var flash_url = "/caipiaoimg/v1.1/js/ZeroClipboard.swf";
</script>
<script language="javascript" src="/caipiaoimg/v1.1/js/jquery.zclip.js"></script>
<script language="javascript" src="/caipiaoimg/v1.1/js/jquery.snippet.min.js"></script>
<script>
$(function(){
	// 获取马甲包主包信息
	var selectTag = true;
	$(".addChannel").on("change", function() {
		var platform = $(this).children('option:selected').val();
		var tpl = '';
		if(platform == 2 || platform == 3){
			if(selectTag)
			{
				selectTag = false;
				$.ajax({
	                type: 'post',
	                url: '/backend/ChannelAnalysis/getPackages',
	                data: {platform:platform},

	                success: function (response) {
	                    var response = $.parseJSON(response);
	                    if(response.status == 1)
	                    {
	                    	tpl += '<select class="selectList w202 _val" id="package" name="package">';
	                    	$.each(response.data,function(index,items){
	                    		tpl += '<option value="' + items['id'] +'"';
	                    		if(index == 0){
	                    			tpl += ' "selected"';
	                    		}
	                    		tpl += '>' + items['name'] + '</option>';
	                    	});
	                    	tpl += '</select><span class="hide_span"></span>';
	                    	$('#packageDetail').html(tpl);
	        				$('#showPackages').show();	        				
	                    }
	                    selectTag = true;
	                },
	                error: function () {
	                    selectTag = true;
	                    alert('网络异常，请稍后再试');
	                }
	            });
	        }       
		}else{
			tpl = '<input type="hidden" class="ipt w202 _val" id="package" name="package" value="0">';
			$('#packageDetail').html(tpl);
			$('#showPackages').hide();
			selectTag = true;
		}
	});
    //重写提示框
    function alertPop(content){
        $("#alertBody").html(content);
        popdialog("alertPop");
    }
//提取字符串中的数字
    function getNum(str)
    {
        var value = str.replace(/[^0-9]/ig,"");
        return value;
    }

    function validateAlphaNum(str)
    {
        var returnArr = [true,""];
        if(/^[a-zA-Z0-9]+$/.test(str) == false)
        {
            message = "用户名只能为字母数字";
            returnArr[0] = false;
            returnArr[1] = message;
        }
        return returnArr;
    }

    function validateNum(str,type)
    {
        var returnArr = [false,"请输入大于等于0的数字"];
        if(str != "" &&str != null)
        {
            if(/^([0-9]*\.|\.?)[0-9]*$/.test(str) == true)
            {
                returnArr[0] = true;
                returnArr[1] = "";
            }
            // if(type == 'alt_cpa')
            // {
            //     if(str > 1)
            //     {
            //         returnArr[0] = false;
            //         returnArr[1] = "cpa必须小于等于1";
            //     }
            // }
        }
        return returnArr;
    }
	$("#search").click(function(){
		$('#search_form').submit();
	});
    $('.table-modify-txt').on('click', function(){
        $(this).hide();
        $(this).parents('.table-modify').find('.table-modify-ipt').show();
        var ipt = $(this).parents('.table-modify').find('.table-modify-ipt');
        var flage = ipt.find('input').attr('flage' ,'1') ;
    });
    $('.btn-table-modify').on('click', function(){
        var tableModify= $(this).parents('.wrapper').find('.table-modify');
        tableModify.find('.table-modify-ipt').hide();
        tableModify.find('.table-modify-txt').show();
        var table = document.getElementById("manage");
        var tbody = table.tBodies[0];
        var jsonArray = [];
        for(var k =0, rowb; rowb= tbody.rows[k]; k++){
            console.log(rowb.cells[6]);
            if(getText(rowb.cells[0])||getText(rowb.cells[1])||getText(rowb.cells[2])||getText(rowb.cells[3])||getText(rowb.cells[4])||getText(rowb.cells[5])||getText(rowb.cells[6])||getText(rowb.cells[7])||getText(rowb.cells[9])||getText(rowb.cells[11])) 
            {
                var arr = {
                    id: rowb.id,
                    name: getText(rowb.cells[0]),
                    settle_mode: getText(rowb.cells[1]),
                    unit_price: getText(rowb.cells[2]),
                    share_ratio: getText(rowb.cells[3]),
                    reg_time: getText(rowb.cells[4]),
                    month_fee: getText(rowb.cells[5])
                };
                <?php if($search['platform']=='2'){ ?>
                arr.ios_download = getText(rowb.cells[9]);
                arr.nick_name=getText(rowb.cells[11]);
                <?php }elseif($search['platform']=='4'){ ?>
                arr.app_path=getText(rowb.cells[6]);
                arr.nick_name=getText(rowb.cells[7]);
                <?php }elseif($search['platform']=='3'){ ?>
                arr.nick_name = getText(rowb.cells[7])
                <?php }else {?>
                arr.nick_name = getText(rowb.cells[6])
                <?php }?>
                jsonArray.push(arr);
            }
        }

        var data = JSON.stringify(jsonArray);
        $.ajax({
            type: "post",
            url: '/backend/ChannelAnalysis/alter',
            data: {data:data},
            dataType: "json",
            success: function (returnData) {
                if(returnData.status =='y')
                {
                    layer.alert('修改成功', {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){
                        location.reload();
                        } });
                }else
                {
                    layer.alert(returnData.message, {icon: 2,btn:'',title:'温馨提示',time:0});
                }
            }
        });

    })
    //获取dom文本
    var getText = function( el ){
        if($(el).find("input").attr('flage') == 1)
        {
            return $(el).find("input").val();
        }
    };
	 //新增按钮
	$("#update").click(function(){
		popdialog("updatePop");
	});

	// 新增包名称
	$("#addPackage").click(function(){
		popdialog("packagePop");
	});

	// 提交新包
	$("#submitPackage").click(function(){
		$.ajax({
            type: "post",
            url: '/backend/ChannelAnalysis/addPackage',
            data: $("#updatePackage").serialize(),
			dataType:"json",
            success: function (returnData) {
               	if(returnData.status =='y'){
					layer.alert('新增成功', {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){location.reload();} });
					
				}else{
					layer.alert(returnData.message, {icon: 2,btn:'',title:'温馨提示',time:0});
				}
            }
        });
	});

	function validateNewChannle(pre)
	{  
		var channelname = $.trim($("#"+pre+"channelname").val());
		var jiesuan;
		var jiesuan1 = '-1';
		var type = '';
		if($("#"+pre+"settlemode option:selected").val()==1)
		{
			jiesuan = 1;
		    type = 'alt_cpa';
			var unitprice = $.trim($("#" +pre + "unit").val());
		}else if($("#"+pre+"settlemode option:selected").val()==2)
		{
			jiesuan = $("#pcps").val();
			jiesuan1 = $("#reg_time").val();
		}else
		{
			jiesuan = $("#pcpt").val();
		}
		var returnArr = [true,""];
		var validateResult = channelname;
		if(channelname.length==0)
		{
			return [flase,"渠道名称不能为空"];
		}else
		{
				validateResult = validateNum(jiesuan,type);
				if(validateResult[0] == false)
				{
					return validateResult;
				}
				if(jiesuan1!=-1)
				{
					if(jiesuan1!='*')
					{
						validateResult = validateNum(jiesuan1,type);
					}
				}
				if(type == "alt_cpa")
				{
					validateResult = validateNum(unitprice);
				}
				if(validateResult[0] == false)
				{
					return validateResult;
				}
		}
		return returnArr;
	}
	
	var selectTag = true;
	$("#updateSubmit").click(function(){
		if(selectTag){
    		selectTag = false;
			var validateArr  = validateNewChannle("p");
			if(validateArr[0] == false)
			{
				layer.alert(validateArr[1], {icon: 2,btn:'',title:'温馨提示',time:0});
				return;
			}
			$.ajax({
	            type: "post",
	            url: '/backend/ChannelAnalysis/update',
	            data: $("#updateForm").serialize(),
				dataType:"json",
	            success: function (returnData) {
	               if(returnData.status =='y')
						{
							layer.alert('新增成功', {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){location.reload();} });
							
						}else
						{
							layer.alert(returnData.message, {icon: 2,btn:'',title:'温馨提示',time:0});
						}
					selectTag = true;
	            },
	            error: function () {
                    selectTag = true;
                    alert('网络异常，请稍后再试');
                }
	        });
	    }
	});

	//弹出层结算方式改变后
	$('#psettlemode').change(function(){
		$('#unit').hide();
		$('#cpa').hide();
		$('#cps').hide();
		$('#cpt').hide();
		$('#reg_times').hide();
		$('#reg_times1').hide();
		
		if($('#psettlemode option:selected').val()==1)
		{
			$('#unit').show();
			$('#cpa').show();
			$('#cps').val("");
			$('#cpt').val("");
		}else if($('#psettlemode option:selected').val()==2)
		{
			$('#cps').show();
			$('#reg_times').show();
			$('#reg_times1').show();
			$('#cpa').val("");
			$('#cpt').val("");
			$('#unit').val("");
		}else
		{
			$('#cpt').show();	
			$('#cpa').val("");
			$('#cps').val("");
			$('#unit').val("");
		}
	});
    var uploader = WebUploader.create({
        swf: '/caipiaoimg/v1.1/js/jUploader.swf',
        server: '',
        pick: '.file'
    });
    
    $(".ycfcupload").click(function(){
        uploader.options.server = "/backend/ChannelAnalysis/upload/id/"+$(this).attr("data-id");
        uploader.upload();
    });
    
    uploader.on( 'uploadSuccess', function( file, data) {
        alert("上传成功");
        location.reload();
    });
    //确认
    $('#sureSubmit').click(function(){
    	var validateArr  = validateNewChannle("p");
    	if($('input[name=pchannelname]').val()=='')
    	{
    		layer.alert('渠道名称不能为空', {icon: 2,btn:'',title:'温馨提示',time:0});
    		return;
    	}
    	if($('select[name=psettlemode]').val() == 2 && $('input[name=reg_time]').val() =='')
    	{
    		layer.alert('该渠道无注册时限', {icon: 2,btn:'',title:'温馨提示',time:0});
    		return; 		
    	}
    	if($('#psettlemode').val()==2)
    	{
    		var pcps = parseFloat($('#pcps').val());
    		if(pcps >7 || pcps <0)
    		{
	    		layer.alert('分成比例0.00-7.00', {icon: 2,btn:'',title:'温馨提示',time:0});
	    		$('#pcps').val('');
	    		return; 
    		}
    	}
		if(validateArr[0] == false)
		{
			layer.alert(validateArr[1], {icon: 2,btn:'',title:'温馨提示',time:0});
			return;
		}
    	$('#updateSubmit').show();
    	$('#sureSubmit').hide();
    	$('#reg_times1').hide();
    	$('._reg').removeClass('_red');
    	$('._normal').addClass('_red');
    	$('#_tbody tr:visible .tal').each(function(index){
    		 var obj = $(this).find('._val');
    		 if(obj.attr('name')=='pplatform' || obj.attr('name')=='psettlemode' || obj.attr('name')=='package'){
    		 	$(this).find('span').html(obj.find('option:selected').text());
    		 }else{
    		 	$(this).find('span').html(obj.val());
    		 }
    		 $(this).find('._val').hide();
    	});
    });
    //点击取消
   $('._cancle').click(function(){
   	  $('._reg').addClass('_red');
   	  $('._normal').removeClass('_red');
   	  $('#updateSubmit').hide();
      $('#sureSubmit').show();
      $('#reg_times1').show();
   	  $('.hide_span').html('');
   	  $('#_tbody tr:visible .tal').each(function(){
   	  	$(this).find('._val').show();
   	  });
   });
   /**
    * [修改密码]
    * @author Likangjian  2017-04-30
    * @param  {[type]} ){                } [description]
    * @return {[type]}     [description]
    */
   $('._modify_pwd').click(function(){
   		var _Jname = $(this).attr('data-name');
   		 
		$.ajax({
		    type: "post",
		    url: "/backend/ChannelAnalysis/updateChannelPwd",
		    data: {channel_id:$(this).attr('data-id')},
		    success: function(data)
		    {
		    	var json = jQuery.parseJSON(data);
		    	layer.closeAll();
		    	if(json.status == 'SUCCESSS' || json.status == 'y')
		    	{
		    		//layer.alert(json.message, {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){$('.pop-cancel').trigger("click");}});
				    layer.open({
				      'title':'生成默认密码',
				      'type': 1,
				      'area': '300px;',
				      'closeBtn': 1, //不显示关闭按钮
				      'btn': ['复制密码'],
				      'shadeClose': true, //开启遮罩关闭
				      'content': '<div style="margin-left:15px;margin-top:15px;margin-right:15px;">渠道名称：<span style="color:blue;">'+_Jname+'</span></div><div style="margin-left:15px;margin-top:15px;margin-right:15px;">默认密码：<span style="color:red;">'+json.message+'</span></div>', 
				      'btnAlign': 'c',
				      'success':function(){
				      	  $('.layui-layer-btn').css('position','relative');
						  $('.layui-layer-btn a').zclip({
								path:flash_url,
								copy:function(){return json.message;}
						  });
						  $('.snippet-clipboard').css({'position': 'absolute','left': '110px','top':'18px','width':'80px','height':'30px','z-index':'999999'});	
				      }
				    }); 

		    	}else{
		    		layer.alert(json.message, {icon: 2,btn:'',title:'温馨提示',time:0});
		    	}
		    }
		});
   });
   //提交密码修改
   $('#surePwdSubmit').click(function(){
   	 //验证
   	 var surepwd = $('input[name=surepwd]').val();
   	 var newpwd = $('input[name=newpwd]').val();
   	 var oldpwd = $('input[name=oldpwd]').val();
   	 var  _reg = /^\S{6,16}$/;
   	 if(!_reg.test($.trim(oldpwd)) || !_reg.test($.trim(newpwd)) || !_reg.test($.trim(surepwd)))
   	 {
   	 	layer.alert('密码应在6~16位之间', {icon: 2,btn:'',title:'温馨提示',time:0});
   	 }else if(surepwd != newpwd)
   	 {
   	 	layer.alert('两次输入密码不一致', {icon: 2,btn:'',title:'温馨提示',time:0});
   	 }else{
		$.ajax({
		    type: "post",
		    url: "/backend/ChannelAnalysis/updateChannelPwd",
		    data: $('form[name=updatePwdForm]').serialize(),
		    success: function(data)
		    {
		    	var json = jQuery.parseJSON(data);
		    	layer.closeAll();
		    	if(json.status == 'SUCCESSS' || json.status == 'y')
		    	{
		    		layer.alert(json.message, {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){$('.pop-cancel').trigger("click");}});
		    	}else{
		    		layer.alert(json.message, {icon: 2,btn:'',title:'温馨提示',time:0});
		    	}
		    }
		})
   	 }
   });
   $('#reg_time').keyup(function(){
   		var val = $(this).val();
   	  if(val != '*' && !/^\d+$/.test(val) )
   	  {
   	  	$(this).val('');
   	  	layer.alert('只能填入*或者正整数', {icon: 2,btn:'',title:'温馨提示',time:0});
   	  }
   });

   	var selectTag = true;
    $('.isShow').click(function(){
    	if(selectTag){
    		selectTag = false;
    		var id = $(this).data('val');
	    	var cstate = $(this).data('index');
	    	var name = $(this).data('name');
	    	var _this = $(this);

	    	$.ajax({
			    type: "post",
			    url: "/backend/ChannelAnalysis/updateChnnelSale",
			    data: {id:id, cstate:cstate, name:name},
			    success: function(data)
			    {
			    	var json = jQuery.parseJSON(data);
			    	layer.closeAll();
			    	if(json.status == 'y')
			    	{
			    		changeSale(_this);
			    		layer.alert(json.message, {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){$('.pop-cancel').trigger("click");}});
			    	}else{
			    		layer.alert(json.message, {icon: 2,btn:'',title:'温馨提示',time:0});
			    	}
			    	selectTag = true;
			    }
			})
    	}   	
    });

    // 停开售展示切换
    function changeSale(obj){
    	var state = obj.data('index');
    	if(state > 0){
    		// 停售转开售
    		obj.data('index', '0');
    		obj.removeClass('btn-red');
    		obj.addClass('btn-blue');
    		obj.text('已开启');
    	}else{
    		obj.data('index', '1');
    		obj.removeClass('btn-blue');
    		obj.addClass('btn-red');
    		obj.text('已停售');
    	}
    }

    // 修改主包名
    $(".alterPackage").click(function(){
    	var channelId = $(this).parents('tr').attr('id');
    	var channelName = $(this).parents('tr').find('td').eq(0).find('input[name="alt_channelname"]').val();
    	var defaultPackage = $(this).attr('data-package');
    	$("#alterPop input[name='channelId']").val(channelId);
    	$("#channelName").html(channelName);
    	// 选中默认包
    	$("#packageData option").each(function(){
    		if($(this).val() == defaultPackage){
    			$(this).prop('selected', true);
    		}else{
    			$(this).prop('selected', false);
    		}
    	})
    	popdialog("alterPop");
    });

	$("#submitAlterPackage").click(function(){
		$.ajax({
            type: "post",
            url: '/backend/ChannelAnalysis/alterPackage',
            data: $("#alterPackage").serialize(),
			dataType:"json",
            success: function (returnData) {
               	if(returnData.status =='y'){
					layer.alert('修改成功', {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){location.reload();} });
					
				}else{
					layer.alert(returnData.message, {icon: 2,btn:'',title:'温馨提示',time:0});
				}
            }
        });
	});

	//修改推广状态
	$(".alterStatus").click(function(){
		var res = 1;
		var defaultStatus = $(this).attr('data-status');
		var newStatus = defaultStatus == 0 ? 1 : 0;
		var text = defaultStatus == 0 ? '合作终止' : '合作中';
		var channelId = $(this).parents('tr').attr('id');
		$.ajax({
            type: "post",
            async: false,
            url: '/backend/ChannelAnalysis/alterStatus',
            data: {'channelId':channelId, 'newStatus':newStatus},
			dataType:"json",
            success: function (json) {
               	if(json.status == 'y')
		    	{
		    		res = 2;
		    		layer.alert('恭喜您，操作成功', {icon: 1, closeBtn:0, title:'温馨提示', time:0}, function(){
		    			location.reload();
		    		});
		    	} else {
		    		layer.alert(json.message, {icon: 2, btn:'', title:'温馨提示', time:0});
		    	}
            }
        });
        if (res === 2) {
        	$(this).html(text);
        }
	});
});
</script>
<script>
  new Vue({
        el: '#app',
        data: function () {
          return {
            qdpf: {
              radio: 1
            }
          }
        }
  })
</script>
</body>
</html>