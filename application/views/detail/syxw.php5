<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/help.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/kaijiang.css');?>" rel="stylesheet" type="text/css" />
<div class="wrap_in help-container lottery-detail">
    <div class="help-section clearfix">
        <?php $this->load->view('detail/aside');?>
        <div class="article">
            <div class="help-content lottery-detail-syxw">
                <div class="lottery-detail-img">
                    <i class="icon-lottery"></i>
                    老11选5
                </div>
                <div class="lottery-detail-txt">
                    <div class="lottery-detail-title">
                        <h2>开奖信息</h2>
                        <div class="date-select">
                            <span>开奖期次：</span>
                            <dl class="simu-select">
                                <dt><?php echo $date?><i class="arrow"></i></dt>
                                <dd class="select-opt">
                                    <div class="select-opt-in">
                                        <?php foreach ($dateList as $value){?>
                                    		<a href="<?php echo $baseUrl?>detail/syxw/<?php echo $value['date']?>"><?php echo $value['date']?></a>
                                    	<?php }?>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>

                    <dl class="lottery-detail-dl">
                        <dt>开奖时间：</dt>
                        <dd><?php 
                        $nowdt = end($data);
                        $arr = array("天","一","二","三","四","五","六");
                        echo date('Y年m月d日 H:i ', strtotime($nowdt['award_time']))."周".$arr[date("w",strtotime($nowdt['award_time']))]?></dd>
                        <dt>开奖号码：</dt>
                        <dd>
                            <div class="award-nums">
                            	<?php foreach (explode(',', $nowdt['awardNum']) as $red){?>
	                            	<span class="ball ball-red"><?php echo $red?></span>
	                            <?php }?>
                            </div>
                        </dd> 
                    </dl>
                    <p class="lottery-detail-qs">今日已开<?php echo count($data)?>期，还剩<b><?php echo 78-count($data);?></b>期</p>
                    <p class="fast-lottery-title">第<b><?php echo $current['issue']?></b>期正在销售&nbsp;&nbsp;&nbsp;&nbsp;距投注截止时间还有：<b class="spec"><?php echo $min?></b>分<b class="spec"><?php echo $second?></b>秒<a href="<?php echo $baseUrl?>syxw" target="_blank" class="btn btn-bet-small">立即投注</a></p>
                    <p class="trend-chart"><a href="###">走势图</a><i>&raquo;</i></p>
                    <div class="mod-box">
                        <div class="mod-box-hd">
                            <h3 class="mod-box-title">开奖详情</h3>
                            <span class="mod-box-subtxt">10分钟一期，返奖率59%，每天09:00-22:00销售</span>
                        </div>
                        <div class="mod-box-bd">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="table-algin-c">期次</th>
                                        <th>开奖号码</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                                $data = array_merge($data, array_fill(count($data), 80-count($data), null));
                                foreach (array_slice($data, 0, 20) as $value)
                                {?>
                                	<tr>
                                        <th><?php echo substr($value['issue'], -2)?></th>
                                        <td>
                                        <?php foreach (explode(',', $value['awardNum']) as $val)
                                        {?>
                                        <span class="ball ball-red"><?php echo $val?></span>
                                		<?php }?>
                                        </td>
                                    </tr>
                                <?php }?>
                                </tbody>
                            </table>
                            <table>
                                <thead>
                                    <tr>
                                        <th class="table-algin-c">期次</th>
                                        <th>开奖号码</th>
                                    </tr>
                                </thead>
                                <tbody>
								<?php foreach (array_slice($data, 20, 20) as $value)
                                {?>
                                	<tr>
                                        <th><?php echo substr($value['issue'], -2)?></th>
                                        <td>
                                        <?php foreach (explode(',', $value['awardNum']) as $val)
                                        {?>
                                        <span class="ball ball-red"><?php echo $val?></span>
                                		<?php }?>
                                        </td>
                                    </tr>
                                <?php }?>
                                </tbody>
                            </table>
                            <table>
                                <thead>
                                    <tr>
                                        <th class="table-algin-c">期次</th>
                                        <th>开奖号码</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($data, 40, 20) as $value)
                                	{?>
                                	<tr>
                                        <th><?php echo substr($value['issue'], -2)?></th>
                                        <td>
                                        <?php foreach (explode(',', $value['awardNum']) as $val)
                                        {?>
                                        <span class="ball ball-red"><?php echo $val?></span>
                                		<?php }?>
                                        </td>
                                    </tr>
                                	<?php }?>
                                </tbody>
                            </table>
                            <table>
                                <thead>
                                    <tr>
                                        <th class="table-algin-c">期次</th>
                                        <th>开奖号码</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($data, 60, 20) as $value)
                                	{?>
                                	<tr>
                                        <th><?php echo substr($value['issue'], -2)?></th>
                                        <td>
                                        <?php foreach (explode(',', $value['awardNum']) as $val)
                                        {?>
                                        <span class="ball ball-red"><?php echo $val?></span>
                                		<?php }?>
                                        </td>
                                    </tr>
                                	<?php }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>