<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">运营活动</a>&nbsp;&gt;&nbsp;<a href="">拉新活动</a></div>
<?php 
    $to_channel = array(
        0 => '不限',
        1 => 'PC端H5',
        2 => '移动端H5',
    );
?>
<div class="mod-tab mt20 mb20">
	<div class="mod-tab-hd">
    	<ul>
      		<li><a href="/backend/Activity/lxInviter">邀请人</a></li>
            <li class="current"><a href="/backend/Activity/lxInvitee">受邀人</a></li>
            <li><a href="/backend/Activity/lxPrize">抽奖配置</a></li>
    	</ul>
  	</div>
  	<div class="mod-tab-bd">
    	<ul>
      		<li style="display: block;">
        		<div class="data-table-filter mt10">
        			<form action="/backend/Activity/lxInvitee" method="get" id="search_form">
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
                                        <select class="selectList w108" id="" name="to_channel_id">
                                            <?php foreach ($to_channel as $key => $items):?>
                                                <option value="<?php echo $key; ?>" <?php if($search['to_channel_id'] == $key){echo 'selected';} ?> ><?php echo $items; ?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </td>
                                    <td>
                                        参与时间：
                                        <input type="text" class="ipt w150 ipt-date Wdate1" name="start_r_m" value="<?php echo $search['start_r_m'] ?>">
                                        <span>至</span>
                                        <input type="text" class="ipt w150 ipt-date Wdate1" name="end_r_m" value="<?php echo $search['end_r_m'] ?>">
                                    </td>                 
                                </tr>
                                <tr>
                                    <td>
                                        手机号：
                                        <input type="text" class="ipt w108" name="phone"  value="<?php echo $search['phone'] ?>">
                                    </td>
                                    <td>
                                        用户名：
                                        <input type="text" class="ipt w108" name="uname"  value="<?php echo $search['uname'] ?>">
                                    </td>  
                                    <td>
                                        邀请人：
                                        <input type="text" class="ipt w108" name="puname"  value="<?php echo $search['puname'] ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        是否注册：
                                        <select class="selectList w108" id="" name="isReg">
                                            <option value="0" <?php if($search['isReg'] == '0'){echo 'selected';} ?> >不限</option>
                                            <option value="1" <?php if($search['isReg'] == '1'){echo 'selected';} ?> >未注册</option>
                                            <option value="2" <?php if($search['isReg'] == '2'){echo 'selected';} ?> >已注册</option>
                                        </select>
                                    </td>
                                    <td>
                                        是否有效邀请：
                                        <select class="selectList w108" id="" name="isBind">
                                            <option value="0" <?php if($search['isBind'] == '0'){echo 'selected';} ?> >不限</option>
                                            <option value="1" <?php if($search['isBind'] == '1'){echo 'selected';} ?> >未有效邀请</option>
                                            <option value="2" <?php if($search['isBind'] == '2'){echo 'selected';} ?> >已有效邀请</option>
                                        </select>
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
                    总人数：<span><?php echo $totalNum; ?></span>
                </div>
        		<div class="data-table-list mt10">
                    <table>
                        <colgroup>
                            <col width="200">
                            <col width="200">
                            <col width="200">
                            <col width="200">
                            <col width="100">
                            <col width="100">
                            <col width="200">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>手机号</th>
                                <th>用户名</th>
                                <th>参与活动时间</th>
                                <th>参与渠道</th>
                                <th>是否注册</th>
                                <th>是否有效邀请</th>
                                <th>邀请人</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($result)):?>
                            <?php foreach ($result as $items):?>
                            <tr>
                                <td><?php echo $items['phone'];?></td>
                                <td>
                                    <?php if(!empty($items['uid'])): ?>
                                    <a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $items['uid']; ?>" class="cBlue"><?php echo $items['uname'];?></a>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $items['created'];?></td>
                                <td><?php echo $to_channel[$items['to_channel_id']];?></td>
                                <td><?php echo ($items['status'] > 0)?'是':'否';?></td>
                                <td><?php echo ($items['status'] == 2)?'是':'否';?></td>
                                <td>
                                    <?php if(!empty($items['puid'])): ?>
                                    <a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $items['puid']; ?>" class="cBlue"><?php echo $items['puname'];?></a>
                                    <?php endif; ?>
                                </td>
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
<script  src="/source/date/WdatePicker.js"></script>
<script>
    $(function(){
        // 时间控件
        $(".Wdate1").focus(function(){
            dataPicker();
        });
    });
</script>