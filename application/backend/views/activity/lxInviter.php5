<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">运营活动</a>&nbsp;&gt;&nbsp;<a href="">拉新活动</a></div>
<?php 
    $from_channel = array(
        0 => '不限',
        1 => 'PC活动页',
        2 => '红包记录页',
        3 => '投注记录页',
        4 => '浮层弹窗',
        5 => 'Android',
        6 => '投注页Banner',
    );
?>
<div class="mod-tab mt20 mb20">
	<div class="mod-tab-hd">
    	<ul>
      		<li class="current"><a href="/backend/Activity/lxInviter">邀请人</a></li>
            <li><a href="/backend/Activity/lxInvitee">受邀人</a></li>
            <li><a href="/backend/Activity/lxPrize">抽奖配置</a></li>
    	</ul>
  	</div>
  	<div class="mod-tab-bd">
    	<ul>
      		<li style="display: block;">
        		<div class="data-table-filter mt10">
        			<form action="/backend/Activity/lxInviter" method="get" id="search_form">
	          			<table>
                            <colgroup>
                                <col width="200">
                                <col width="200">
                                <col width="340">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td>
                                        参与渠道：
                                        <select class="selectList w108" id="" name="from_channel_id">
                                            <?php foreach ($from_channel as $key => $items):?>
                                                <option value="<?php echo $key; ?>" <?php if($search['from_channel_id'] == $key){echo 'selected';} ?> ><?php echo $items; ?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </td>
                                    <td>
                                        手机号：
                                        <input type="text" class="ipt w108" name="phone"  value="<?php echo $search['phone'] ?>">
                                    </td>  
                                    <td>
                                        <a href="javascript:;" class="btn-blue" onclick="$('#search_form').submit();" target="_self">查询</a>
                                    </td>                  
                                </tr>
                            </tbody>
                        </table>
          			</form>
        		</div>
        		<div class="mt20">
                    有效邀请总人数：<span><?php echo $totalNum; ?></span>
                </div>
        		<div class="data-table-list mt10">
                    <table>
                        <colgroup>
                            <col width="200">
                            <col width="200">
                            <col width="200">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>手机号</th>
                                <th>参与渠道</th>
                                <th>邀请人数</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($result)):?>
                            <?php foreach ($result as $items):?>
                            <tr>
                                <td><?php echo $items['phone'];?></td>
                                <td><?php echo $from_channel[$items['from_channel_id']];?></td>
                                <td><?php echo $items['joinNum'];?></td>
                            </tr>
                            <?php endforeach;?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="11">
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
      		</li>
    	</ul>
  	</div>
  	<div class="page mt10 order_info">
      <?php echo $pages[0] ?>
    </div>
</div>