<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">运营活动</a>&nbsp;&gt;&nbsp;<a href="">竞彩活动</a></div>
<div class="mod-tab mod-tab-s mt20 mb20">
	<div class="mod-tab-hd">
    	<ul>
      	 	<li><a href="/backend/Activity/newActivityJc">不中包赔</a></li>
         	<li class="current"><a href="/backend/Activity/activityJj">竞彩加奖</a></li>
    	</ul>
  	</div>
</div>
<div class="mod-tab mt20 mb20">
	<div class="mod-tab-hd">
    	<ul>
      		<li class="current"><a href="/backend/Activity/activityJj">活动概览</a></li>
      		<li><a href="/backend/Activity/manageJj">活动管理</a></li>
    	</ul>
  	</div>
  	<div class="mod-tab-bd">
    	<ul>
      		<li style="display: block;">
        		<div class="data-table-filter mb10">
        			<form action="/backend/Activity/activityJj" method="get" id="search_form">
	          			<table>
                            <colgroup>
                                <col width="200">
                                <col width="200">
                                <col width="200">
                                <col width="340">
                                <col width="140">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td>
                                        关 键 字：
                                        <input type="text" class="ipt w108" name="name" placeholder="用户名/订单号" value="<?php echo $search['name'] ?>">
                                    </td>
                                    <td>
                                        参与金额：
                                        <input type="text" class="ipt w108" name="start_r_m" value="<?php echo $search['start_r_m'] ?>">
                                        <span>至</span>
                                        <input type="text" class="ipt w108" name="end_r_m" value="<?php echo $search['end_r_m'] ?>">
                                    </td>
                                    <td>
                                        投注平台：
                                        <select class="selectList w108" id="" name="platform">
                                            <option value="-1" <?php if($search['platform'] === '-1'){echo 'selected';} ?> >不限</option>
                                            <option value="0" <?php if($search['platform'] === '0'){echo 'selected';} ?>>网页</option>
                                            <option value="1" <?php if($search['platform'] === '1'){echo 'selected';} ?>>Android</option>
                                            <option value="2" <?php if($search['platform'] === '2'){echo 'selected';} ?>>IOS</option>
                                            <option value="3" <?php if($search['platform'] === '3'){echo 'selected';} ?>>M版</option>
                                        </select>
                                    </td>   
                                    <td>
                                        投注彩种：
                                        <select class="selectList w108" id="" name="lid">
                                            <option value="" <?php if($search['lid'] === ''){echo 'selected';} ?> >不限</option>
                                            <option value="42" <?php if($search['lid'] === '42'){echo 'selected';} ?>>竞彩足球</option>
                                            <option value="43" <?php if($search['lid'] === '43'){echo 'selected';} ?>>竞彩篮球</option>
                                        </select>
                                    </td>                       
                                    <td>
                                        活动期次：
                                        <input type="text" class="ipt w108" name="jj_id" value="<?php echo $search['jj_id'] ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        订单状态：
                                        <select class="selectList w108" id="" name="status">
                                            <option value="">全部</option>
                                            <?php foreach ($this->caipiao_status_cfg as $key => $status): ?>
                                            <?php 
                                                if(!in_array($key, array('40', '200', '240', '500', '510', '600', '1000', '2000')))
                                            continue;
                                            ?>
                                            <option value="<?php echo $key ?>" <?php if ($search['status'] === "{$key}"): echo "selected"; endif; ?>><?php echo $status[0]; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        下单时间：
                                        <input type="text" class="ipt w150 ipt-date Wdate1" name="start_r_c" value="<?php echo $search['start_r_c'] ?>">
                                        <span>至</span>
                                        <input type="text" class="ipt w150 ipt-date Wdate1" name="end_r_c" value="<?php echo $search['end_r_c'] ?>">
                                    </td>
                                    <td>
                                        <a href="javascript:;" class="btn-blue" onclick="$('#search_form').submit();" target="_blank">查询</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
          			</form>
        		</div>
        		<div class="mt20">
                    订单总金额：<span><?php echo ParseUnit($total['totalMoney'], 1); ?></span>元&nbsp;&nbsp;中奖总额（税后）<?php echo ParseUnit($total['totalMargin'], 1); ?>元&nbsp;&nbsp;加奖总额<?php echo ParseUnit($total['totalAddMoney'], 1); ?>元&nbsp;&nbsp;用户统计：<span><?php echo $total['totalPeople']; ?></span>人
                </div>
        		<div class="data-table-list mt10">
                    <table>
                        <colgroup>
                            <col width="60">
                            <col width="100">
                            <col width="100">
                            <col width="80">
                            <col width="80">
                            <col width="100">
                            <col width="80">
                            <col width="80">
                            <col width="80">
                            <col width="60">
                            <col width="60">
                            <col width="60">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>活动期次</th>
                                <th>订单编号</th>
                                <th>用户名</th>
                                <th>彩种</th>
                                <th>期次</th>
                                <th>创建时间</th>
                                <th>订单金额（元）</th>
                                <th>中奖总额（税后）</th>
                                <th>加奖金额（元）</th>
                                <th>订单状态</th>
                                <th>投注</th>
                                <th>详情</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($result)):?>
                            <?php foreach ($result as $items):?>
                            <tr>
                                <td><?php echo $items['jj_id'];?></td>
                                <td><?php echo $items['orderId'];?></td>
                                <td><?php echo $items['userName'];?></td>
                                <td><?php echo $items['lname'];?></td>
                                <td><?php echo $items['issue'];?></td>
                                <td><?php echo $items['created'];?></td>
                                <td><?php echo ParseUnit($items['money'], 1);?></td>
                                <td><?php echo ParseUnit($items['margin'], 1);?></td>
                                <td><?php echo ParseUnit($items['add_money'], 1);?></td>
                                <td><?php echo $this->caipiao_status_cfg[$items['status']][0];?></td>
                                <td><?php echo $items['buyPlatform'];?></td>
                                <td><a  href="/backend/Management/orderDetail/?id=<?php echo $items['orderId']; ?>" class="cBlue" target="_blank">查看</a></td>
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