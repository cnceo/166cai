<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：客户端管理&nbsp;&gt;&nbsp;<a href="/backend/Appconfig/banner?platform=<?php echo $platform;?>"><?php echo $platform;?>配置</a></div>
<div class="mod-tab mt20">
	<div class="mod-tab-hd">
        <ul>
        	<?php $this->load->view("appconfig/tag", array('platform' => $platform, 'tag' => 'version')) ?>
        </ul>
    </div>
    <div class="mod-tab-bd">
	    <ul>
	      	<li style="display: block">
	    		<form action="" method="post" id="lottery_form">
	    			<div class="data-table-list mt10">
	          			<table>
	            			<colgroup>
	              				<col width="5%" />
	              				<col width="8%" />
	              				<col width="5%" />
	              				<col width="7%" />
	              				<col width="8%" />
	              				<col width="17%" />
	              				<col width="10%" />
	              				<col width="10%" />
	              				<col width="5%" />
	            			</colgroup>
		            		<thead>
			              		<tr>
			                		<th>序号</th>
			                		<th>权重</th>
			                		<th>彩种ID</th>
			                		<th>名称</th>
			                		<th>图片</th>
			                		<th>副标题</th>
			                		<th>附加标识位</th>
			                		<th>渠道选择</th>
			                		<td>操作</td>
			              		</tr>
		            		</thead>
	            			<tbody id="pic-table" class="lotteryConfigTab">
								<?php if(!empty($lotteryInfo['baseInfo'])):?>
                            	<?php foreach ($lotteryInfo['baseInfo'] as $key => $items):?>
	            				<tr <?php if($items['plid'] > 0): ?> style="display: none" class="more_<?php echo $items['plid']; ?>" id="<?php echo $items['lid']; ?>" <?php endif;?> >
	            					<td>
	                					<?php echo $key + 1;?>
	                					<input type="hidden" class="ipt w40 tac" name="lottery[<?php echo $key?>][lid]" value="<?php echo $items['lid']; ?>" readonly>
	              					</td>
	              					<td>
	                					<input type="text" class="ipt w40 tac" name="lottery[<?php echo $key?>][weight]" value="<?php echo $items['weight']; ?>">
	              					</td>
	              					<td>
	                					<?php echo $items['lid']; ?>
	              					</td>
	              					<td><?php echo $items['lname'];?></td>
	              					<td>
	                					<div><img src="<?php echo $items['logUrl']; ?>" width="50" height="50" /></div>
	              					</td>
	              					<td>
	                					<input type="text" class="ipt tac w222" name="lottery[<?php echo $key?>][memo]" value="<?php echo $items['memo']; ?>">
	              					</td>
	              					<td>
	              					<?php foreach ($attachFlags as $attchId => $val):?>
	              						<input type="checkbox" <?php if ($items['attachFlag'] & $attchId) {?>checked<?php }?> value="<?php echo $attchId;?>" name="lottery[<?php echo $key;?>][attachFlag][]"><?php echo $val;?>
	              					<?php endforeach;?>
	              					</td>
	              					<td>
                                        已选择<span><?php echo !(empty($items['channels'])) ? count(explode(',', $items['channels'])) : '0'; ?></span>个
                                        <a href="javascript:;" class="cBlue selectChannels">编辑选择</a>
                                        <input type="hidden" class="ipt tac w40" name="lottery[<?php echo $key?>][channels]" value="<?php echo $items['channels']?>">
                                    </td>
	              					<td>
	              						<input type="hidden" class="ipt tac w222" name="lottery[<?php echo $key?>][plid]" value="<?php echo $items['plid']; ?>">
	              						<?php if(in_array($items['lid'], array('2', '3'))):?>
	              						<a href="javascript:;" class="cBlue moreConfig" data-index="<?php echo $items['lid']; ?>" data-val="0">配置</a>
	              						<?php endif; ?>
	              					</td>
	            				</tr>
	            				<?php endforeach;?>
                            	<?php endif; ?>
	            			</tbody>
	          			</table>
	          			<div class="tac">
	          				<a class="btn-blue mt20 mr20 review-lottery">保存并预览</a>
	          				<a class="btn-blue mt20 submit-lottery">上线</a>
	          			</div>
	          		</div>
	          		<input type="hidden" name="subType" value="1">
	          		<input type="hidden" name="platform" value="<?php echo $platform; ?>">
	          		<input type="hidden" name="reviewCheck" value="0">
	       		</form>
	       	</li>
	       	<li style="display: block">
	       		<div class="version clearfix">
                    <div class="mod-tab version-list">
                        <div class="mod-tab-hd">
                            <ul class="clearfix">
                            	<?php if(!empty($versionInfo)):?>
                            	<?php foreach (array_reverse($versionInfo) as $key => $items):?>
                            	<li class="versionTab <?php if(count($versionInfo) == ($key + 1)){ echo 'current'; } ?>">
                                    <a href="javascript:;" class="btn-white choseVersion" data-index="<?php echo $items['versionCode']; ?>"><?php echo $items['versionName']; ?></a>
                                </li>
                            	<?php endforeach;?>
                            	<?php endif; ?>
                            </ul>
                            <span class="version-left"><</span>
                            <span class="version-right">></span>
                        </div>
                        <div class="mod-tab-bd">
                            <ul>
                                <li style="display: block; width: 800px !important;">
                                	<form action="" method="post" id="version_form">
	                                    <div class="data-table-list mt10">
	                                        <table>
	                                            <colgroup>
	                                            	<col width="10%">
	                                                <col width="10%">
	                                                <col width="10%">
	                                                <col width="10%">
	                                                <col width="40%">
	                                                <col width="20%">
	                                            </colgroup>
	                                            <thead>
	                                                <tr>
	                                                	<th>versionCode</th>
	                                                	<?php if($platform == 'ios'): ?>
	                                                	<th>红包入口</th>
	                                                	<th>审核弹窗</th>
	                                                	<th>升级弹窗</th>
	                                                	<th>弹窗文案</th>
	                                                	<th>强制升级版本</th>
	                                                	<?php endif; ?>
	                                            	</tr>
	                                            </thead>
	                                            <tbody>
	                                                <tr>
	                                                	<td>
	                                                		<input type="text" class="ipt w60" name="appVersionCode" value="<?php echo $newVersion['versionCode']; ?>" readonly>
	                                                	</td>
	                                                	<?php if($platform == 'ios'): ?>
	                                                    <td>
	                                                    	<a class="btn-blue showRedpack" id="showRedpack"><?php echo $newVersion['showRedpack']?'显示':'不显示'; ?></a><input type="hidden" id="showRedpackVal" name="showRedpack" value="<?php echo $newVersion['showRedpack']; ?>">
	                                                    </td>
	                                                    <td>
	                                                    	<a class="btn-blue showAlert" id="showAlert"><?php echo $newVersion['showAlert']?'显示':'不显示'; ?></a><input type="hidden" id="showAlertVal" name="showAlert" value="<?php echo $newVersion['showAlert']; ?>">
	                                                    </td>
	                                                    <td>
	                                                    	<a class="btn-blue isCheck" id="isCheck"><?php echo $newVersion['isCheck']?'不显示':'显示'; ?></a><input type="hidden" id="isCheckVal" name="isCheck" value="<?php echo $newVersion['isCheck']; ?>">
	                                                    </td>
	                                                    <td>
	                                                    	<textarea name="mark"><?php echo $newVersion['mark']; ?></textarea>
	                                                    </td>
	                                                    <td>
	                                                        <input type="text" name="upgradeVersion" value="<?php echo $newVersion['upgradeVersion'];?>" class="ipt w108">
	                                                    </td>
	                                                	<?php elseif($platform == 'android'): ?>    
	                                                    <td style="display:none;">
	                                                    	<input type="hidden" id="showRedpackVal" name="showRedpack" value="0">
	                                                    	<input type="hidden" id="showAlertVal" name="showAlert" value="0">
	                                                    	<input type="hidden" id="isCheckVal" name="isCheck" value="0">
	                                                        <input type="text" name="upgradeVersion" value="" class="ipt w108">
	                                                    </td>
	                                                	<?php endif; ?>
	                                                </tr>
	                                            </tbody>
	                                        </table>
	                                        <input type="hidden" name="appVersionName" value="<?php echo $newVersion['versionName']; ?>">
	                                    </div>
	                                    <input type="hidden" name="subType" value="2">
	          							<input type="hidden" name="platform" value="<?php echo $platform; ?>">
	          							<input type="hidden" name="lastVersionCode" value="<?php echo $newVersion['versionCode']; ?>">
                                	</form>
                                </li>
                            </ul>
                        </div>
                        <div class="tac">
                            <a href="javascript:;" class="btn-blue mt20 submit-version">保存并上线</a>
                        </div>
                    </div>
                    <input type="text" class="ipt version-add w40">
                    <a href="javascript:;" class="btn-white version-add-btn mt10">新增版本</a>
                </div>
	       	</li>
	       	<li style="display: block">
	       		<div class="version clearfix">
                    <div class="mod-tab version-list">
                        <div class="mod-tab-hd">
                            <ul class="clearfix">
                            	<?php if(!empty($versionInfo)):?>
                            	<?php foreach (array_reverse($versionInfo) as $key => $items):?>
                            	<li class="versionTab2 <?php if(count($versionInfo) == ($key + 1)){ echo 'current'; } ?>">
                                    <a href="javascript:;" class="btn-white choseLottery" data-index="<?php echo $items['versionCode']; ?>"><?php echo $items['versionName']; ?></a>
                                </li>
                            	<?php endforeach;?>
                            	<?php endif; ?>
                            </ul>
                            <span class="version-left"><</span>
                            <span class="version-right">></span>
                        </div>
                        <div class="mod-tab-bd">
                            <ul>
                                <li style="display: block;">
                                	<form action="" method="post" id="lstatus_form">
	                                    <div class="data-table-list mt10">
	                                        <table>
	                                            <colgroup>
	                                            	<col width="20%">
	                                                <col width="20%">
	                                            </colgroup>
	                                            <thead>
	                                                <tr>
	                                                	<th>彩种名称</th>
	                                                	<th>是否销售</th>
	                                            	</tr>
	                                            </thead>
	                                            <tbody>
	                                            	<?php if(!empty($newLottery)):?>
	                                            	<?php foreach ($newLottery as $key => $items):?>
	                                                <tr id="lotteryConfig<?php echo $key; ?>">
	                                                	<td>
	                                                		<?php echo BetCnName::$BetCnName[$key]; ?>
	                                                		
	                                                		<input type="hidden" class="ipt w60" name="lid" value="<?php echo $key; ?>">
	                                                	</td>
	                                                    <td>
	                                                    	<a class="btn-blue lotteryStatus" data-index="<?php echo $key; ?>" id="lotteryStatusTag<?php echo $key; ?>"><?php echo ($items == '0')?'销售':'不销售'; ?></a><input type="hidden" name="lstatus[<?php echo $key; ?>]" id="lotteryStatus<?php echo $key; ?>" value="<?php echo $items; ?>">
	                                                    </td>
	                                                </tr>
	                                                <?php endforeach;?>
                            						<?php endif; ?>
	                                            </tbody>
	                                        </table>  
	                                    </div>
	                                    <input type="hidden" name="lotteryVersionCode" value="<?php echo $newVersion['versionCode']; ?>">
	                                    <input type="hidden" name="subType" value="3">
	          							<input type="hidden" name="platform" value="<?php echo $platform; ?>">
                                	</form>
                                </li>
                            </ul>
                        </div>
                        <div class="tac">
                            <a href="javascript:;" class="btn-blue mt20 submit-lstatus">保存并上线</a>
                        </div>
                    </div>
                </div>
	       	</li>
	       	<li style="display: block">
	       		<!-- 启动页弹窗 -->
	    		<form action="" method="post" id="pop_form">
	    			<div class="data-table-list mt10">
	          			<table>
	            			<colgroup>
	              				<col width="3%" />
	              				<col width="28%" />
	              				<col width="17%" />
	              				<col width="10%" />
	              				<col width="7%" />
	              				<col width="7%" />
	              				<col width="10%" />
	              				<col width="10%" />
	              				<col width="15%" />
	            			</colgroup>
		            		<thead>
			              		<tr>
			                		<th>序号</th>
			                		<th>图片（建议图片尺寸为604x604）</th>
			                		<th>链接</th>
			                		<th>渠道选择</th>
			                		<th>是否显示</th>
			                		<th>是否需要登录</th>
			                		<th>客户端原生页</th>
			                		<th>投注彩种</th>
			                		<th>更新</th>
			              		</tr>
		            		</thead>
	            			<tbody id="pic-table">
	            				<?php 
	            				$needLoginArr = array(
	            					'0'		=>	'否',
	            					'-1'	=>	'不限',
	            					'1'		=>	'是',
	            				);	
	            				?>
	            				<?php if(!empty($popInfo)):?>
                            	<?php foreach ($popInfo as $i => $items):?>
                            	<tr>
	            					<td>
	                					<?php echo $i+1;?>
	                					<input type="hidden" class="ipt tac w40" name="pop[<?php echo $i?>][id]" value="<?php echo $items['id']?>">
	              					</td>
	              					<td>
	              						<div class="btn-white file">选择文件</div>
	                					<div class="btn-white upload" data-index="<?php echo $i?>">开始上传</div>
	                					<input type="hidden" name="pop[<?php echo $i?>][imgUrl]" id="path_<?php echo $i?>" value="<?php echo $items['imgUrl']?>">
	                					<div id="imgdiv0" class="imgDiv"><img id="imgShow<?php echo $i?>" src="<?php echo $items['imgUrl']?>" width="100" height="50" /></div>
	              					</td>
	              					<td>
	                					<input type="text" class="ipt tac w184" name="pop[<?php echo $i?>][url]" value="<?php echo $items['url']; ?>">
	              					</td>
	              					<?php if($i % 3 == 0): ?>
	              					<td rowspan="3">
                                        已选择<span><?php echo !(empty($items['channels'])) ? count(explode(',', $items['channels'])) : '0'; ?></span>个
                                        <a href="javascript:;" class="cBlue selectChannels">编辑选择</a>
                                        <input type="hidden" class="ipt tac w40" name="pop[<?php echo $i?>][channels]" value="<?php echo $items['channels']?>">
                                    </td>
                                   	<?php endif; ?>
                                   	<td>
	                					<a class="btn-<?php echo $items['isShow']?'blue':'red'; ?> showPop" id="showPop" data-index="<?php echo $i; ?>"><?php echo $items['isShow']?'显示':'不显示'; ?></a><input type="hidden" class="showFlag" id="popflag<?php echo $i; ?>" name="pop[<?php echo $i?>][isShow]" value="<?php echo $items['isShow']?'1':'0'; ?>">
	              					</td>
	              					<td>
	              						<?php echo $needLoginArr[$items['needLogin']]; ?>
	                					<input type="hidden" class="ipt tac w40" name="pop[<?php echo $i?>][needLogin]" value="<?php echo $items['needLogin']?>">
	              					</td>
	              					<td>
	              						<select class="selectList w98" id="" name="pop[<?php echo $i?>][appAction]">
                                            <option value="" <?php if($items['appAction'] === ''){echo 'selected';} ?> >不使用</option>
                                            <option value="register" <?php if($items['appAction'] === 'register'){echo 'selected';} ?>>注册</option>
                                            <option value="login" <?php if($items['appAction'] === 'login'){echo 'selected';} ?>>登录</option>
                                            <option value="bet" <?php if($items['appAction'] === 'bet'){echo 'selected';} ?>>投注页</option>
                                            <option value="email" <?php if($items['appAction'] === 'email'){echo 'selected';} ?>>绑定邮箱</option>
                                        </select>
	              					</td>
	              					<td>
	                					<input type="text" class="ipt tac w40" name="pop[<?php echo $i?>][lid]" value="<?php echo $items['lid'] ? $items['lid'] : '0'; ?>">
	              					</td>
	              					<td>
	              						<span><?php echo $items['time'] ? $items['time'] : '0'; ?></span>
	              						<input type="hidden" class="ipt tac w40" name="pop[<?php echo $i?>][time]" value="<?php echo $items['time'] ? $items['time'] : '0'; ?>">
	                					<a href="javascript:;" class="cBlue updatePopTime">更新</a>
	              					</td>
	            				</tr>
                            	<?php endforeach;?>
                            	<?php endif; ?>
	            			</tbody>
	          			</table>
	          			<div class="tac">
	          				<a class="btn-blue mt20 submit-pop">保存并上线</a>
	          			</div>
	          		</div>
	          		<input type="hidden" name="subType" value="4">
	          		<input type="hidden" name="platform" value="<?php echo $platform; ?>">
	       		</form>
	       	</li>
	       	<!-- 微信登录开关 -->
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
	                                                	<th>启动配置项</th>
	                                                	<th>渠道选择</th>
	                                            	</tr>
	                                            </thead>
	                                            <tbody>
	                                            	<?php if(!empty($loadingInfo)):?>
	                                            	<?php foreach ($loadingInfo as $key => $items):?>
	                                                <tr id="lotteryConfig<?php echo $key; ?>">
	                                                	<td>
	                                                		<?php echo $items['title']; ?>
	                                                		
	                                                		<input type="hidden" class="ipt w60" name="loading[<?php echo $key?>][title]" value="<?php echo $items['title']; ?>">
	                                                		<input type="hidden" class="ipt w60" name="loading[<?php echo $key?>][id]" value="<?php echo $items['id']; ?>">
	                                                	</td>
	                                                	<td>
	                                                		已选择<span><?php echo !(empty($items['channels'])) ? count(explode(',', $items['channels'])) : '0'; ?></span>个
	                                                		<a href="javascript:;" class="cBlue selectChannels">编辑选择</a>
	                                                		<input type="hidden" class="ipt tac w40" name="loading[<?php echo $key?>][channels]" value="<?php echo $items['channels']?>">
	                                                	</td>
	                                                </tr>
	                                                <?php endforeach;?>
                            						<?php endif; ?>
	                                            </tbody>
	                                        </table>  
	                                    </div>
	                                    <input type="hidden" name="subType" value="5">
	          							<input type="hidden" name="platform" value="<?php echo $platform; ?>">
                                	</form>
                                </li>
                            </ul>
                        </div>
                        <div class="tac">
                            <a href="javascript:;" class="btn-blue mt20 submit-loading">保存并上线</a>
                        </div>
                    </div>
                </div>
	       	</li>
	    </ul>
    </div>
    <div class="pop-mask" style="display:none;width:200%"></div>
    <!-- 创建活动 start -->
  	<div class="pop-dialog" id="dialog-updatePop" style="display:none;">
		<div class="pop-in">
            <div class="pop-head">
                <h2>更新时间戳</h2>
                <span class="pop-close" title="关闭">关闭</span>
            </div>
            <div class="pop-body tac">
                <p><b>时间戳更新后，将会对所有符合条件的用户重新弹出新素材！</b></p>
                <p><b>请谨慎操作！</b></p>
                <p>适用于以下场景：</p>
                <p>1.新活动上线推广</p>
                <p>1.活动更换新一轮素材，且有必要通知所有用户</p>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-blue-h32 mlr15" id="confirmUpdate">确认更新</a>
            </div>
        </div>
	</div>
	<!-- 创建活动 end -->
	<!-- 彩种系列配置 start -->
  	<div class="pop-dialog" id="dialog-listPop" style="display:none;">
		<div class="pop-in">
            <div class="pop-head">
                <h2>系列彩种</h2>
                <span class="pop-close" title="关闭">关闭</span>
            </div>
            <div class="pop-body">
				<div class="data-table-list mt10">
	          		<table>
	            		<colgroup>
              				<col width="5%">
              				<col width="8%">
              				<col width="5%">
              				<col width="7%">
              				<col width="8%">
              				<col width="17%">
              				<col width="10%">
              				<col width="10%">
            			</colgroup>
	            		<thead>
		              		<tr>
		                		<th>序号</th>
		                		<th>权重</th>
		                		<th>彩种ID</th>
		                		<th>名称</th>
		                		<th>图片</th>
		                		<th>副标题</th>
		                		<th>附加标识位</th>
		                		<th>是否显示</th>
		              		</tr>
	            		</thead>
            			<tbody id="pic-table" class="popListDetail" data-index="">
            			</table>
	          		</div>
			</div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-blue-h32 mlr15" id="confirmList">确认</a>
            </div>
        </div>
	</div>
	<!-- 彩种系列配置 end -->
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
                <a href="javascript:;" class="btn-blue-h32 mlr15 confirmChannel" data-index="" data-func="">确认</a>
                <a href="javascript:;" class="btn-b-white closeChannel">关闭</a>
            </div>
        </div>
    </div>
    <!-- 渠道模块 end -->
</div>
<script src="/source/js/webuploader.min.js"></script>
<script>
	$(function() {
		var channelList = '<?php echo json_encode($channels['detail']); ?>';
		// 显示切换
		$('.showLottery').click(function(){
			var flagVal = $("#flag" + $(this).data('index')).val();
			if(flagVal == '0'){
				$("#flag" + $(this).data('index')).val('1');
				$(this).html('不显示');
			}else{
				$("#flag" + $(this).data('index')).val('0');
				$(this).html('显示');
			}
		});

		// 修改彩种
		$(".submit-lottery").click(function(){
			// 预览检查
			var checked = $('input[name="reviewCheck"]').val();
			if(checked == 0){
				alert('请先预览确认后再上线');
				return false;
			}
			$("#lottery_form").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").submit();
		})

		// 修改版本信息
		$(".submit-version").click(function(){
			// 输入框检查
			var appVersionCode = $('input[name="appVersionCode"]').val();
			var lastVersionCode = $('input[name="lastVersionCode"]').val();

			if($('.mod-tab-hd ul').find('li').hasClass('newli'))
			{
				if(appVersionCode == ''){
					alert('请输入versionCode');
					return false;
				}
				if(parseInt(appVersionCode) <= parseInt(lastVersionCode)){
					alert('versionCode不能小于' + lastVersionCode);
					return false;
				}
			}	
			$("#version_form").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").submit();
		})

		// 修改版本彩种销售
		$(".submit-lstatus").click(function(){
			$("#lstatus_form").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").submit();
		})

		// 新增版本
		// $(".version-add-btn").click(function(){
		// 	var version = $(this).siblings('.version-add').val();
		// 	if(!version == ""){
		// 		if($('.mod-tab-hd ul').find('li').hasClass('newli')){
		// 			return false;
		// 		}
		// 		$('.versionTab').removeClass('current');
		// 		$(this).siblings('.version-list').find('.mod-tab-hd ul').append('<li class="current newli"><a href="javascript:;" class="btn-white">'+version+'</a></li>');
		// 		// 初始化新版本信息
		// 		$('input[name="appVersionName"]').val(version);
		// 		$('input[name="appVersionCode"]').removeAttr("readonly");
		// 		$('input[name="appVersionCode"]').val('');
		// 		$('.showRedpack').html('不显示');
		// 		$('input[name="showRedpack"]').val('0');
		// 		$('.showAlert').html('显示');
		// 		$('input[name="showAlert"]').val('1');
		// 		$('.isCheck').html('显示');
		// 		$('input[name="isCheck"]').val('1');
		// 		$('input[name="upgradeVersion"]').val('');
		// 		$('textarea[name="mark"]').val('');			
		// 	}else{
		// 		alert('请输入版本号');
		// 		return false;
		// 	}
		// })

		// 版本信息 红包显示切换
		function change1(flagVal, type)
		{
			if(type == '0'){
				if(flagVal == '0'){
					$('input[name="showRedpack"]').val('1');
					$('.showRedpack').html('显示');
				}else{
					$('input[name="showRedpack"]').val('0');
					$('.showRedpack').html('不显示');
				}
			}else{
				if(flagVal == '0'){
					$('input[name="showRedpack"]').val('0');
					$('.showRedpack').html('不显示');
				}else{
					$('input[name="showRedpack"]').val('1');
					$('.showRedpack').html('显示');
				}
			}
			
		}

		$('.showRedpack').click(function(){
			var flagVal = $('input[name="showRedpack"]').val();
			change1(flagVal, '0');
		});

		// 版本信息 升级弹窗显示切换
		function change2(flagVal, type)
		{
			if(type == '0'){
				if(flagVal == '0'){
					$('input[name="showAlert"]').val('1');
					$('.showAlert').html('显示');
				}else{
					$('input[name="showAlert"]').val('0');
					$('.showAlert').html('不显示');
				}
			}else{
				if(flagVal == '0'){
					$('input[name="showAlert"]').val('0');
					$('.showAlert').html('不显示');
				}else{
					$('input[name="showAlert"]').val('1');
					$('.showAlert').html('显示');
				}
			}
			
		}

		$('.showAlert').click(function(){
			var flagVal = $('input[name="showAlert"]').val();
			change2(flagVal, '0');
		});

		// 版本信息 审核弹窗显示切换
		function change3(flagVal, type)
		{
			if(type == '0'){
				if(flagVal == '0'){
					$('input[name="isCheck"]').val('1');
					$('.isCheck').html('不显示');
				}else{
					$('input[name="isCheck"]').val('0');
					$('.isCheck').html('显示');
				}
			}else{
				if(flagVal == '0'){
					$('input[name="isCheck"]').val('0');
					$('.isCheck').html('显示');
				}else{
					$('input[name="isCheck"]').val('1');
					$('.isCheck').html('不显示');
				}
			}	
		}

		$('.isCheck').click(function(){
			var flagVal = $('input[name="isCheck"]').val();
			change3(flagVal, '0');
		});

		function change4(lid, val){
			if(val == '0'){
				$('#lotteryStatus' + lid).val('0');
				$('#lotteryStatusTag' + lid).html('销售');
			}else{
				$('#lotteryStatus' + lid).val('1');
				$('#lotteryStatusTag' + lid).html('不销售');
			}
		}

		// 版本信息切换
		var selectTag = true;
		$('.choseVersion').click(function(){
			var appVersionCode = $(this).data('index');
			var platform = $('input[name="platform"]').val();
			var liobj = $(this).parent();
			if(liobj.hasClass('current'))
			{
				return false;
			}

			if(selectTag)
			{
				selectTag = false;
				$.ajax({
	                type: 'post',
	                url: '/backend/Appconfig/choseVersion',
	                data: {appVersionCode:appVersionCode,platform:platform},

	                success: function (response) {
	                    var response = $.parseJSON(response);
	                    if(response.status == 1)
	                    {
	                        selectTag = true;
	                        $('.newli').remove();
	                      	$('input[name="appVersionCode"]').attr("readonly","readonly");
	                        $('input[name="appVersionCode"]').val(response.data.versionCode);
	                        $('input[name="appVersionName"]').val(response.data.versionName);
	                        $('input[name="upgradeVersion"]').val(response.data.upgradeVersion);
	                        $('textarea[name="mark"]').val(response.data.mark);

	                        change1(response.data.showRedpack, '1');
	                        change2(response.data.showAlert, '1');
	                        change3(response.data.isCheck, '1');
	                        
	                        // 选中
	                        $('.versionTab').removeClass('current');
	                        liobj.addClass('current');
	                    }else{
	                        selectTag = true;
	                        alert(response.msg);
	                    }
	                },
	                error: function () {
	                    selectTag = true;
	                    alert('网络异常，请稍后再试');
	                }
	            });
			}
		});
		
		// 版本彩种销售切换
		$('.lotteryStatus').click(function(){
			var lid = $(this).data('index');
			var flagVal = $('#lotteryStatus' + lid).val();
			if(flagVal == '0'){
				$('#lotteryStatus' + lid).val('1');
				$(this).html('不销售');
			}else{
				$('#lotteryStatus' + lid).val('0');
				$(this).html('销售');
			}
		});

		// 版本信息切换
		$('.choseLottery').click(function(){
			var appVersionCode = $(this).data('index');
			var platform = $('input[name="platform"]').val();
			var liobj = $(this).parent();
			if(liobj.hasClass('current'))
			{
				return false;
			}

			if(selectTag)
			{
				selectTag = false;
				$.ajax({
	                type: 'post',
	                url: '/backend/Appconfig/choseLottery',
	                data: {appVersionCode:appVersionCode,platform:platform},

	                success: function (response) {
	                    var response = $.parseJSON(response);
	                    if(response.status == 1)
	                    {
	                        selectTag = true;
	                        jQuery.each(response.data, function(lid,val) {
	                        	change4(lid, val);
						    });
	                        $('input[name="lotteryVersionCode"]').val(appVersionCode);
						    // 选中
	                        $('.versionTab2').removeClass('current');
	                        liobj.addClass('current');
	                    }else{
	                        selectTag = true;
	                        alert(response.msg);
	                    }
	                },
	                error: function () {
	                    selectTag = true;
	                    alert('网络异常，请稍后再试');
	                }
	            });
			}
		});

		// 首页弹窗上传图片
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

	    // 弹框显示切换
		$('.showPop').click(function(){
			var flagVal = $("#popflag" + $(this).data('index')).val();
			if(flagVal == '0'){
				$("#popflag" + $(this).data('index')).val('1');
				$(this).html('显示');
			}else{
				$("#popflag" + $(this).data('index')).val('0');
				$(this).html('不显示');
			}
		});

		// 保存首页弹框
		$(".submit-pop").click(function(){
			// 渠道信息检查
			var text = checkActivityChannel();
            if(text){
                var msg = '渠道' + text + '请不要同时启用2个以上';
                alert(msg);
                return false;
            }
			$("#pop_form").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").submit();
		})

		var _this;
		// 更新弹框时间
		$('.updatePopTime').click(function(){
			_this = $(this).parents('td');
			popdialog("dialog-updatePop");
			return false;     
		});

		$('#confirmUpdate').click(function(){
			// var _this = $(this).parents('td');
			$.ajax({
                type: 'post',
                url: '/backend/Appconfig/getSeverTime',
                data: {},

                success: function (response) {
                    var response = $.parseJSON(response);
                    if(response.status == 1)
                    {
                        _this.find('input').val(response.data);
                        _this.find('span').text(response.data);
                        closePop();
                    }else{
                        alert(response.msg);
                    }
                },
                error: function () {
                    alert('网络异常，请稍后再试');
                }
            });
		});
		
		// 启动配置项
		$(".submit-loading").click(function(){
			$("#loading_form").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").submit();
		})

		$('.loadingStatus').click(function(){
			var id = $(this).data('index');
			var flagVal = $('#loadingStatus' + id).val();
			if(flagVal == '0'){
				$('#loadingStatus' + id).val('1');
				$(this).html('已开启');
			}else{
				$('#loadingStatus' + id).val('0');
				$(this).html('已关闭');
			}
		});

		!function () {
			var baseLength = 12,
			currentLenth = 0;
			function addArrow (oLi, placeParent) {
				if (oLi.length > baseLength) {
					placeParent.append('<span class="version-left"><i class="ivu-icon ivu-icon-arrow-left-b"></i></span><span class="version-right pointerEvents"><i class="ivu-icon ivu-icon-arrow-right-b"></i></span>');
				}
			}
			$('.version-list').each(function () {
				var placeParent = $(this).find('.mod-tab-hd');
				var oLi = placeParent.find('li');
				addArrow(oLi, placeParent);
				placeParent.find('li:last-child').addClass('.current');
			})
			$('.version-list').on('click', '.version-left', function () {
				var $paretn = $(this).closest('.version-list .mod-tab-hd');
				var totalLength = $paretn.find('li').length;
				$paretn.find('li').hide().slice(baseLength + currentLenth, currentLenth + baseLength * 2).show();
				currentLenth += baseLength;
				if (totalLength - currentLenth <=  baseLength) {
					$paretn.find('li').hide().slice(totalLength - baseLength, totalLength).show();
					currentLenth = totalLength;
					$(this).addClass('pointerEvents')
				}
				$paretn.find('.version-right').removeClass('pointerEvents');
			})
			$('.version-list').on('click', '.version-right', function () {
				var $paretn = $(this).closest('.version-list .mod-tab-hd');
				var totalLength = $paretn.find('li').length;
				$paretn.find('li').hide().slice(currentLenth - baseLength * 2 ,currentLenth - baseLength).show();
				currentLenth -= baseLength;
				if (currentLenth <  baseLength) {
					$paretn.find('li').hide().slice(0, baseLength).show();
					currentLenth = 0;
					$(this).addClass('pointerEvents');
				}
				$paretn.find('.version-left').removeClass('pointerEvents');
			})
			$('.version').on('click', '.version-add-btn', function () {
				var version = $(this).siblings('.version-add').val();
				if(! version==""){
			
					
					if($('.mod-tab-hd ul').find('li').hasClass('newli')){
						return false;
					}
					$('.versionTab').removeClass('current');
					$(this).siblings('.version-list').find('.mod-tab-hd ul').prepend('<li class="current newli"><a href="javascript:;" class="btn-white">'+version+'</a></li>');
					$(this).siblings('.version-list').find('.mod-tab-bd ul').append('<li style="display:none;"><div class="data-table-list mt10"><table><colgroup><col width="10%"><col width="10%"><col width="10%"><col width="20%"></colgroup><thead><tr><th>红包入口</th><th>升级弹窗</th><th>审核弹窗</th><th>强制升级版本</th></tr></thead><tbody><tr><td>显示</td><td>显示</td><td>显示</td><td><input type="text" class="ipt w108"></td></tr></tbody></table></div></li>')
					

					// 初始化新版本信息
					$('input[name="appVersionName"]').val(version);
					$('input[name="appVersionCode"]').removeAttr("readonly");
					$('input[name="appVersionCode"]').val('');
					$('.showRedpack').html('不显示');
					$('input[name="showRedpack"]').val('0');
					$('.showAlert').html('显示');
					$('input[name="showAlert"]').val('1');
					$('.isCheck').html('显示');
					$('input[name="isCheck"]').val('1');
					$('input[name="upgradeVersion"]').val('');
					$('textarea[name="mark"]').val('');	
				}else{
					alert('请输入版本号')
				}
				var placeParent = $(this).closest('.version-list').find('.mod-tab-hd');
				var oLi = placeParent.find('li');
				addArrow(oLi, placeParent);
			})
		}();

		// 版本配置 - 系列更多
		$('.moreConfig').click(function(){
			var pid = $(this).data('index');
			var index = 1;
			var html = "";
			$('.popListDetail').data('index', pid);
			$('.lotteryConfigTab').find('tr').each(function (index) {
				if($(this).hasClass('more_' + pid)){
					var tdArr = $(this).children();
					html += '<tr>';
					html += '<td>' + index + '</td>';
					html += '<td><input type="text" class="ipt w40 tac" name="weight" value="' + tdArr.eq(1).find('input').val() + '"></td>';
              		html += '<td>' + tdArr.eq(2).text() + '</td>';
              		html += '<td>' + tdArr.eq(3).text() + '</td>';
              		html += '<td><div><img src="' + tdArr.eq(4).find('img').attr("src") + '" width="50" height="50"></div></td>';
              		html += '<td><input type="text" class="ipt tac w222" name="memo" value="' + tdArr.eq(5).find('input').val() + '"></td>';
              		html += '<td>';
              		html += '<input type="checkbox" value="1" name="attachFlag"' 
              		if(tdArr.eq(6).find('input').eq(0).prop('checked')){
              			html += ' checked ';
              		}
              		html += '>加奖中';
              		html += '<input type="checkbox" value="2" name="attachFlag"';
              		if(tdArr.eq(6).find('input').eq(1).prop('checked')){
              			html += ' checked ';
              		}
              		html += '>副标题标红';
              		html += '</td>';
              		// 渠道
              		var channels = tdArr.eq(7).find('input').val();
              		var channelnum = 0;
              		if(channels){
              			var channelArr = channels.split(',');
              			channelnum = channelArr.length;
              		}
              		html += '<td>';
              		html += '已选择<span>' + channelnum + '</span>个<a href="javascript:;" class="cBlue selectPopChannels">编辑选择</a>';
              		html += '<input type="hidden" class="ipt tac w40" name="channels' + index + '" value="' + channels + '">';
              		html += '</td>';
              		/**
              		if(tdArr.eq(7).find('input').val() > 0){
              			html += '<td><a class="btn-blue popShowLottery" data-show="1">不显示</a>';
              		}else{
              			html += '<td><a class="btn-blue popShowLottery" data-show="0">显示</a>';
              		}	
              		**/	
              		html += '</tr>';
              		index ++;		
				}
			})
			// 插入弹层
			$('.popListDetail').html(html);
			popdialog("dialog-listPop");
			// 弹层宽度设置
			$('#dialog-listPop').css({
				'margin-left': '-512px',
				'left': '50%',
				'max-width': '100%',
				'width': '1024px'
			})
			return false;


			var pid = $(this).data('index');
			var status = $(this).data('val');
			$('.more').hide();
			if(status){
				// 关闭
				$(this).data('val', 0);
				$('.more_' + pid).hide();
			}else{
				// 显示
				$(this).data('val', 1);
				$('.more_' + pid).show();
			}
		});

		// 弹层显示切换
		$('.popListDetail').on('click', '.popShowLottery', function(){
			if($(this).data('show') > 0){
				$(this).data('show', '0');
				$(this).html('显示');
			}else{
				$(this).data('show', '1');
				$(this).html('不显示');
			}
		});

		// 弹层确认
		$('#confirmList').click(function(){
			// 回插更新表单
			var pid = $('.popListDetail').data('index');
			var list = [];
			$('.popListDetail').find('tr').each(function () {
				var tdArr = $(this).children();
				var lid = parseInt(tdArr.eq(2).text());
				var weight = tdArr.eq(1).find('input').val();
				var memo = tdArr.eq(5).find('input').val();
				// var show = tdArr.eq(7).find('a').data('show');
				var channels = tdArr.eq(7).find('input').val();
				// 回插指定元素
				var _thisTd = $('.lotteryConfigTab #' + lid).find('td');
				_thisTd.eq(1).find('input').val(weight);
				_thisTd.eq(5).find('input').val(memo);
				tdArr.eq(6).find('input').each(function (idx, item) {
					if ($(this).prop('checked')) {
						_thisTd.eq(6).find('input').eq(idx).prop('checked', true)
					} else {
						_thisTd.eq(6).find('input').eq(idx).prop('checked', false)
					}
				});
				_thisTd.eq(7).find('input').val(channels);
				// if(show > 0){
				// 	_thisTd.eq(7).find('a').text('不显示');
				// 	_thisTd.eq(7).find('input').val(1);
				// }else{
				// 	_thisTd.eq(7).find('a').text('显示');
				// 	_thisTd.eq(7).find('input').val(0);

				// }
			});
			closePop();
		});

		// 版本配置 - 预览
		$('.review-lottery').click(function(){
			var platform = $('input[name="platform"]').val();
			var list = [];
			var qz = [];
			$('.lotteryConfigTab').find('tr').each(function () {
				var tdArr = $(this).children();
				// 加奖标识
				var val = [];
				tdArr.eq(6).find('input').each(function (item, idx) {
					if ($(this).prop('checked')) {
						val.push(1)
					} else {
						val.push(0)
					}
				});

				// 显示
				// var isShow = tdArr.eq(7).find('input').val();
				// if(isShow > 0){
				// 	isShow = false;
				// }else{
				// 	isShow = true;
				// }

				isShow = true;

				// 父类
				var lid = tdArr.eq(0).find('input').val();
				if(lid == 2 || lid == 3){
					qz[lid] = tdArr.eq(1).find('input').val();
					var isSeries = true;
				}else{
					var isSeries = false;
				}

				// 子类
				var plid = tdArr.eq(8).find('input').val();
				if(plid > 0){
					var child = true;
					var parentLid = plid;
				}else{
					var child = false;
					var parentLid = '';
				}

				var data = {
					qz: tdArr.eq(1).find('input').val(),
		            name: tdArr.eq(3).text(),
		            des: tdArr.eq(5).find('input').val(),
		            imgsrc: tdArr.eq(4).find('img').attr("src"),
		            jj: val[0],
		            desMark: val[1],
		            isShow: isShow,
		            isSeries: isSeries,
		            isOpen: true,
		            lid: lid,
		            parentLid: parentLid,
				}

				if(plid > 0 && qz[plid] >= 0){
					data.parentQz = qz[plid];
				}

				list.push(data);
			})
			// 更新检查
			$('input[name="reviewCheck"]').val('1');
			// 保存localStorage
			window.localStorage.removeItem(platform)
			window.localStorage.setItem(platform, JSON.stringify(list))
			// 跳转预览页
			window.open('/backend/Appconfig/review/' + platform);
		});

		// 渠道选择
        $(".selectChannels").click(function(){
            var _this = $(this);
            channelPopInit(_this);
            popdialog("chooseChannel");
        })

        $('.popListDetail').on('click', '.selectPopChannels', function(){
        	var _this = $(this);
            channelPopInit(_this, 'pop');
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
        function channelPopInit(_this, func = ''){
            // 清空
            $("#chooseChannel").find(':checkbox').each(function(){
                $(this).attr("checked", false);
            });
            $(".confirmChannel").data('index', '');
            $(".confirmChannel").attr('data-func', '');

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
            $(".confirmChannel").attr('data-func', func);
        }

        // 确认
        $(".confirmChannel").click(function(){
            var tag = $(this).data('index');
            var func = $(this).attr('data-func');
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
            if(func){
            	popdialog("dialog-listPop");
            }
        })

        // 关闭
        $(".closeChannel").click(function(){
        	var func = $(".confirmChannel").attr('data-func');
        	closePop();
        	if(func){
            	popdialog("dialog-listPop");
            }
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
			$("#pop_form .selectChannels").each(function(){
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
