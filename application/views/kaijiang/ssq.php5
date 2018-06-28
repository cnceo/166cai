<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/help.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/kaijiang.css');?>" rel="stylesheet" type="text/css" />
<div class="wrap_in help-container lottery-detail">
    <div class="help-section clearfix">
    	<?php $this->load->view('kaijiang/aside');?>
        <div class="article">
            <div class="help-content lottery-detail-ssq">
                <div class="lottery-detail-img">
                    <i class="icon-lottery"></i>
                </div>
                <div class="lottery-detail-txt">
                    <div class="lottery-detail-title">
                        <h1 class="lottery-detail-name">双色球开奖结果</h1>
                        <div class="date-select">
                            <span>开奖期次：</span>
                            <dl class="simu-select">
                                <dt><?php echo $issue?><i class="arrow"></i></dt>
                                <dd class="select-opt">
                                    <div class="select-opt-in">
                                    	<?php foreach ($issueList as $value){?><a href="<?php echo $baseUrl?>kaijiang/ssq/<?php echo $value['issue']?>"><?php echo $value['issue']?></a><?php }?>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                        <a href="<?php echo $baseUrl?>ssq" target="_blank" class="btn btn-bet-small">立即投注</a> 
                    </div>

                    <dl class="lottery-detail-dl">
                        <dt>开奖时间：</dt>
                        
                        <dd><?php $arr=array("日","一","二","三","四","五","六");
                        echo date('Y年m月d日 H:i ', strtotime($data['award_time']))."周".$arr[date("w",strtotime($data['award_time']))]?></dd>
                        <dt>开奖号码：</dt>
                        <dd>
                            <div class="award-nums">
                            <?php foreach ($data['award']['red'] as $red){?><span class="ball ball-red"><?php echo $red?></span><?php }?><span class="ball ball-blue"><?php echo preg_replace('/\((.+)\)/', '', $data['award']['blue'])?></span></div>
                        </dd> 
                        <dt>全国销量：</dt>
                        <dd><?php echo $data['sale']?>元</dd>
                        <dt>奖池奖金：</dt>
                        <dd><span class="spec"><?php echo $data['pool']?>元</span></dd>
                    </dl>
                    <p class="trend-chart"><a target="_blank" href="http://caipiao2345.cjcp.com.cn/cjwssq/view/ssqzonghe.html">走势图</a><i>&raquo;</i></p>
                    <div class="mod-box">
                        <div class="mod-box-hd">
                            <h2 class="mod-box-title">开奖详情</h2>
                            <span class="mod-box-subtxt">2元赢取1000万,每周二、四、日开奖</span>
                        </div>
                        <div class="mod-box-bd">
                            <table>
                                <thead>
                                    <tr>
                                        <th>奖项</th>
                                        <th>中奖注数</th>
                                        <th>单注中奖</th>
                                        <th>中奖条件</th>
                                    </tr>
                                </thead>
                                <tbody>
                                	<?php if (empty($data['bonusDetail'])){?>
                                		<tr><td>一等奖</td><td>---</td><td>---</td><td><span class="ball-red">6</span>+<span class="ball-blue">1</span></td></tr>
                                		<tr><td>二等奖</td><td>---</td><td>---</td><td><span class="ball-red">6</span>+<span class="ball-blue">0</span></td></tr>
                                		<tr><td>三等奖</td><td>---</td><td>---</td><td><span class="ball-red">5</span>+<span class="ball-blue">1</span></td></tr>
                                		<tr><td>四等奖</td><td>---</td><td>---</td><td><span class="ball-red">5</span>+<span class="ball-blue">0</span> / <span class="ball-red">4</span>+<span class="ball-blue">1</span></td></tr>
                                		<tr><td>五等奖</td><td>---</td><td>---</td><td><span class="ball-red">4</span>+<span class="ball-blue">0</span> / <span class="ball-red">3</span>+<span class="ball-blue">1</span></td></tr>
                                		<tr><td>六等奖</td><td>---</td><td>---</td><td><span class="ball-red">2</span>+<span class="ball-blue">1</span> / <span class="ball-red">1</span>+<span class="ball-blue">1</span> / <span class="ball-red">0</span>+<span class="ball-blue">1</span></td></tr>
									<?php }else {
                                	 foreach (json_decode($data['bonusDetail']) as $key => $bonusDetail){
                                			switch ($key){
	                                        		case '1dj':
	                                        			$name = '一等奖';
	                                        			$condition = '<span class="ball-red">6</span>+<span class="ball-blue">1</span>';
	                                        			break;
	                                        		case '2dj':
	                                        			$name = '二等奖';
	                                        			$condition = '<span class="ball-red">6</span>+<span class="ball-blue">0</span>';
	                                        			break;
	                                        		case '3dj':
	                                        			$name = '三等奖';
	                                        			$condition = '<span class="ball-red">5</span>+<span class="ball-blue">1</span>';
	                                        			break;
                                        			case '4dj':
                                        				$name = '四等奖';
                                        				$condition = '<span class="ball-red">5</span>+<span class="ball-blue">0</span> / <span class="ball-red">4</span>+<span class="ball-blue">1</span>';
                                        				break;
                                        			case '5dj':
                                        				$name = '五等奖';
                                        				$condition = '<span class="ball-red">4</span>+<span class="ball-blue">0</span> / <span class="ball-red">3</span>+<span class="ball-blue">1</span>';
                                        				break;
                                        			case '6dj':
                                        				$name = '六等奖';
                                        				$condition = '<span class="ball-red">2</span>+<span class="ball-blue">1</span> / <span class="ball-red">1</span>+<span class="ball-blue">1</span> / <span class="ball-red">0</span>+<span class="ball-blue">1</span>';
                                        				break;
												}?>
                                		<tr>
                                        	<td><?php echo $name ?></td>
                                        	<td><?php echo isset($bonusDetail->zs) ? $bonusDetail->zs : '---'?></td>
                                        	<td><?php echo isset($bonusDetail->dzjj) ? $bonusDetail->dzjj : '---'?></td>    
                                        	<td><?php echo $condition?></td>
                                        </tr>
                                       <?php }
                                       } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>