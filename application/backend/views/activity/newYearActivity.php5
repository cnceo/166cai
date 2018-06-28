<?php $this->load->view("templates/head") ?>
<?php 
    $platform = array(
        '' => '不限',
        '1' => '安卓',
        '2' => 'IOS',
    );
?>
<div class="path">您的位置：<a href="">运营活动</a>&nbsp;&gt;&nbsp;<a href="">新年活动</a></div>
<div class="mod-tab mt20 mb20">
	<div class="mod-tab-hd">
    	<ul>
      		<li class="current"><a href="/backend/Activity/newYearActivity">分享人</a></li>
            <li><a href="/backend/Activity/newYearInvitee">被分享人</a></li>
            <li><a href="/backend/Activity/manageNewYearInvitee">管理开关</a></li>
            <li><a href="/backend/Activity/newYearPrize">抽奖配置</a></li>
            <li><a href="/backend/Activity/newYearChjList">抽奖记录</a></li>
    	</ul>
  	</div>
  	<div class="mod-tab-bd">
    	<ul>
      		<li style="display: block;">
        		<div class="data-table-filter mt10">
        			<form action="/backend/Activity/newYearActivity" method="get" id="search_form">
	          			<table>
                            <colgroup>
                                <col width="200">
                                <col width="200">
                                <col width="340">
                            </colgroup>
                            <tbody>
                                <tr>
                                	<td>
                                        用户名：
                                        <input type="text" class="ipt w108" name="uname"  value="<?php echo $search['uname'] ?>">
                                    </td>
                                    <td>
                                        手机号：
                                        <input type="text" class="ipt w108" name="phone"  value="<?php echo $search['phone'] ?>">
                                    </td>
                                    <td>
                                        分享平台：
                                       <select class="selectList w108" id="" name="platform">
                                            <?php foreach ($platform as $key => $val):?>
                                                <option value="<?php echo $key; ?>" <?php if($search['platform'] == $key){echo 'selected';} ?> ><?php echo $val; ?></option>
                                            <?php endforeach;?>
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
                    邀请总人数：<span><?php echo $totalNum['total']; ?></span> 注册总人数：<span><?php echo $totalNum['reg_total']; ?></span> 购彩人数：<span><?php echo $totalNum['buy_num']; ?></span>
                </div>
        		<div class="data-table-list mt10">
                    <table>
                        <colgroup>
                            <col width="200">
                            <col width="100">
                            <col width="100">
                            <col width="100">
                            <col width="100">
                            <col width="100">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>用户名</th>
                                <th>邀请人数</th>
                                <th>注册人数</th>
                                <th>购彩人数</th>
                                <th>获得抽奖次数</th>
                                <th>剩余抽奖次数</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($result)):?>
                            <?php foreach ($result as $items):?>
                            <tr>
                                <td><a href="/backend/User/user_manage/?uid=<?php echo $items['uid'] ?>" class="cBlue" target="_blank"><?php echo $items['uname'];?></a></td>
                                <td><?php echo $items['total'];?></td>
                                <td><?php echo $items['reg_total'];?></td>
                                <td><?php echo $items['buy_num'];?></td>
                                <td><?php echo $items['total_num'];?></td>
                                <td><?php echo $items['left_num'];?></td>
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