<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/help.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/kaijiang.css');?>" rel="stylesheet" type="text/css" />
<div class="wrap_in help-container lottery-detail">
    <div class="help-section clearfix">
        <?php $this->load->view('kaijiang/aside');?>
        <div class="article">
            <div class="help-content lottery-detail-pls">
                <div class="lottery-detail-img">
                    <i class="icon-lottery"></i>
                </div>
                <div class="lottery-detail-txt">
                    <div class="lottery-detail-title">
                        <h1 class="lottery-detail-name">排列三开奖结果</h1>
                        <div class="date-select">
                            <span>开奖期次：</span>
                            <dl class="simu-select">
                                <dt><?php echo $issue?><i class="arrow"></i></dt>
                                <dd class="select-opt">
                                    <div class="select-opt-in">
                                        <?php foreach ($issueList as $value){?>
                                    		<a href="<?php echo $baseUrl?>kaijiang/pl3/<?php echo $value['issue']?>"><?php echo $value['issue']?></a>
                                    	<?php }?>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                        <a href="<?php echo $baseUrl?>pls" target="_blank" class="btn btn-bet-small">立即投注</a>
                    </div>

                    <dl class="lottery-detail-dl">
                        <dt>开奖时间：</dt>
                        <dd><?php $arr=array("日","一","二","三","四","五","六");
                        echo date('Y年m月d日 H:i ', strtotime($data['award_time']))."周".$arr[date("w",strtotime($data['award_time']))]?></dd>
                        <dt>开奖号码：</dt>
                        <dd>
                            <div class="award-nums">
                            	<?php foreach (explode(',', $data['awardNum']) as $red){?><span class="ball ball-red"><?php echo $red?></span><?php }?>
                            </div>
                        </dd> 
                    </dl>
                    <p class="trend-chart"><a target="_blank" href="http://caipiao2345.cjcp.com.cn/cjwpl3/view/pl3_danxuan.html">走势图</a><i>&raquo;</i></p>
                    <div class="mod-box">
                        <div class="mod-box-hd">
                            <h2 class="mod-box-title">开奖详情</h2>
                            <span class="mod-box-subtxt">直选奖金单注1040元</span>
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
                                		<tr><td>直选</td><td>---</td><td>---</td><td><span class="spec">与开奖号顺序一致</span></td></tr>
                                		<tr><td>组选三</td><td>---</td><td>---</td><td><span class="spec">与开奖号一致但不定位</span></td></tr>
                                		<tr><td>组选六</td><td>---</td><td>---</td><td><span class="spec">与开奖号一致但不定位</span></td></tr>
									<?php }else {
										foreach (json_decode($data['bonusDetail']) as $key => $bonusDetail){
                                			switch ($key){
	                                        		case 'zx':
	                                        			$name = '直选';
	                                        			$condition = '与开奖号顺序一致';
	                                        			break;
	                                        		case 'z3':
	                                        			$name = '组选三';
	                                        			$condition = '与开奖号一致但不定位';
	                                        			break;
	                                        		case 'z6':
	                                        			$name = '组选六';
	                                        			$condition = '与开奖号一致但不定位';
	                                        			break;
												}?>
                                    <tr>
                                        <td><?php echo $name?></td>
                                        <td><?php echo isset($bonusDetail->zs) ? $bonusDetail->zs : '---'?></td>
                                        <td><?php echo isset($bonusDetail->dzjj) ? $bonusDetail->dzjj : '---'?></td>
                                        <td><span class="spec"><?php echo $condition?></span></td>
                                    </tr>
                                    <?php } 
                                    }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>