<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">运营活动</a>&nbsp;&gt;&nbsp;<a href="">新年活动</a></div>
<div class="mod-tab mt20 mb20">
	<div class="mod-tab-hd">
    	<ul>
      		<li><a href="/backend/Activity/newYearActivity">分享人</a></li>
            <li><a href="/backend/Activity/newYearInvitee">被分享人</a></li>
            <li><a href="/backend/Activity/manageNewYearInvitee">管理开关</a></li>
            <li><a href="/backend/Activity/newYearPrize">抽奖配置</a></li>
            <li class="current"><a href="/backend/Activity/newYearChjList">抽奖记录</a></li>
    	</ul>
  	</div>
  	<div class="mod-tab-bd">
    	<ul>
      		<li style="display: block;">
        		<div class="data-table-filter mt10">
        			<form action="/backend/Activity/newYearChjList" method="get" id="search_form">
	          			<table>
                            <colgroup>
                                <col width="200">
                                <col width="200">
                                <col width="240">
                            </colgroup>
                            <tbody>
                                <tr>
                                	<td>
                                        用户名：
                                        <input type="text" class="ipt w108" name="uname"  value="<?php echo $search['uname'] ?>">
                                    </td>
                                    <td>
                                        参与时间：
                                        <input type="text" class="ipt w150 ipt-date Wdate1" name="start_r_m" value="<?php echo $search['start_r_m'] ?>">
                                        <span>至</span>
                                        <input type="text" class="ipt w150 ipt-date Wdate1" name="end_r_m" value="<?php echo $search['end_r_m'] ?>">
                                    </td>
                                    <td>
                                        奖品：
                                        <select class="selectList w108" id="" name="award">
                                            <option value="">不限</option>
                                            <?php foreach ($prize as $val):?>
                                            <option value="<?php echo $val['id'];?>" <?php if($search['award'] == $val['id']){echo 'selected';} ?> ><?php echo $val['name'];?></option>
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
                    用户总数：<span><?php echo $totalNum['total_uid']; ?></span> 抽奖总数：<span><?php echo $totalNum['total']; ?></span>
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
                                <th>用户名</th>
                                <th>抽奖时间</th>
                                <th>奖品</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($result)):?>
                            <?php foreach ($result as $items):?>
                            <tr>
                                <td>
                                    <?php if(!empty($items['uid'])): ?>
                                    <a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $items['uid']; ?>" class="cBlue"><?php echo $items['uname'];?></a>
                                    <?php else: ?>
                                    <?php echo $items['uname'];?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $items['created'];?></td>
                                <td><?php echo $items['mark'];?></td>
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