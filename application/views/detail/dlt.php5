<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/help.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/kaijiang.css');?>" rel="stylesheet" type="text/css" />
<div class="wrap_in help-container lottery-detail">
    <div class="help-section clearfix">
        <?php $this->load->view('detail/aside');?>
        <div class="article">
            <div class="help-content lottery-detail-dlt">
                <div class="lottery-detail-img">
                    <i class="icon-lottery"></i>
                    大乐透
                </div>
                <div class="lottery-detail-txt">
                    <div class="lottery-detail-title">
                        <h2>开奖信息</h2>
                        <div class="date-select">
                            <span>开奖期次：</span>
                            <dl class="simu-select">
                                <dt><?php echo $issue?><i class="arrow"></i></dt>
                                <dd class="select-opt">
                                    <div class="select-opt-in">
                                        <?php foreach ($issueList as $value){?>
                                    		<a href="<?php echo $baseUrl?>detail/dlt/<?php echo $value['issue']?>"><?php echo $value['issue']?></a>
                                    	<?php }?>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                        <a href="<?php echo $baseUrl?>dlt" target="_blank" class="btn btn-bet-small">立即投注</a> 
                    </div>

                    <dl class="lottery-detail-dl">
                        <dt>开奖时间：</dt>
                        <dd><?php $arr=array("天","一","二","三","四","五","六");
                        echo date('Y年m月d日 H:i ', strtotime($data['award_time']))."周".$arr[date("w",strtotime($data['award_time']))]?></dd>
                        <dt>开奖号码：</dt>
                        <dd>
                            <div class="award-nums">
                            	<?php foreach ($data['award']['red'] as $red){?>
	                            	<span class="ball ball-red"><?php echo $red?></span>
	                            <?php }?>
	                            <?php foreach ($data['award']['blue'] as $blue){?>
	                            	<span class="ball ball-blue"><?php echo $blue?></span>
	                            <?php }?>
	                            
                            </div>
                        </dd> 
                        <dt>全国销量：</dt>
                        <dd><?php echo $data['sale']?>元</dd>
                        <dt>奖池奖金：</dt>
                        <dd><span class="spec"><?php echo $data['pool']?>元</span></dd>
                    </dl>
                    <p class="trend-chart"><a href="###">走势图</a><i>&raquo;</i></p>
                    <div class="mod-box">
                        <div class="mod-box-hd">
                            <h3 class="mod-box-title">开奖详情</h3>
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
                                	<?php foreach (json_decode($data['bonusDetail']) as $key => $bonusDetail){
                                			switch ($key){
	                                        		case '1dj':
	                                        			$name = '一等奖';
	                                        			$condition = '<span class="ball-red">5</span>+<span class="ball-blue">2</span>';
	                                        			break;
	                                        		case '2dj':
	                                        			$name = '二等奖';
	                                        			$condition = '<span class="ball-red">5</span>+<span class="ball-blue">1</span>';
	                                        			break;
	                                        		case '3dj':
	                                        			$name = '三等奖';
	                                        			$condition = '<span class="ball-red">5</span>+<span class="ball-blue">0</span> / <span class="ball-red">4</span>+<span class="ball-blue">2</span>';
	                                        			break;
                                        			case '4dj':
                                        				$name = '四等奖';
                                        				$condition = '<span class="ball-red">3</span>+<span class="ball-blue">2</span> / <span class="ball-red">4</span>+<span class="ball-blue">1</span>';
                                        				break;
                                        			case '5dj':
                                        				$name = '五等奖';
                                        				$condition = '<span class="ball-red">4</span>+<span class="ball-blue">0</span> / <span class="ball-red">3</span>+<span class="ball-blue">1</span> / <span class="ball-red">2</span>+<span class="ball-blue">2</span>';
                                        				break;
                                        			case '6dj':
                                        				$name = '六等奖';
                                        				$condition = '<span class="ball-red">3</span>+<span class="ball-blue">0</span> / <span class="ball-red">2</span>+<span class="ball-blue">1</span> / <span class="ball-blue">1</span>+<span class="ball-blue">2</span> / <span class="ball-blue">0</span>+<span class="ball-blue">2</span>';
                                        				break;
												}?>
                                    <tr>
                                        <td rowspan="2"><?php echo $name?></td>
                                        <td><span class="tag-txt spec">基本</span><?php echo $bonusDetail->jb->zs?></td>
                                        <td><?php echo $bonusDetail->jb->dzjj?></td>
                                        <td rowspan="2"><?php echo $condition?></td>
                                    </tr>
                                    <tr>
                                        <td><span class="tag-txt">追加</span><?php echo $bonusDetail->zj->zs?></td>
                                        <td><?php echo $bonusDetail->zj->dzjj?></td>
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