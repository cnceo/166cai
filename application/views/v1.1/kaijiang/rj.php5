<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/help.min.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/kaijiang.min.css');?>" rel="stylesheet" type="text/css" />
<div class="wrap lottery-detail">
    <div class="l-frame">
        <?php $this->load->view('v1.1/kaijiang/aside');?>
        <div class="l-frame-cnt">
            <div class="lottery-detail-main lottery-rj">
                <div class="lottery-detail-img">
                    <div class="lottery-img">
                        <svg width="320" height="320">
								<image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
                        </svg>
                    </div>
                </div>
                <div class="lottery-detail-txt">
                    <div class="lottery-detail-title">
                        <h1 class="lottery-detail-name">任选九开奖结果</h1>
                        <div class="date-select">
                            <span>开奖期次：</span>
                            <dl class="simu-select">
                                <dt><?php echo $issue?><i class="arrow"></i></dt>
                                <dd class="select-opt">
                                    <div class="select-opt-in">
                                        <?php foreach ($issueList as $value){?>
                                    		<a href="<?php echo $baseUrl?>kaijiang/rj/<?php echo $value['issue']?>"><?php echo $value['issue']?></a>
                                    	<?php }?>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                        <a href="<?php echo $baseUrl?>rj" target="_blank" class="btn-ss btn-ss-bet">立即预约</a> 
                    </div>

                    <dl class="lottery-detail-dl">
                        <dt>本期销量：</dt>
                        <dd><b class="spec"><?php echo ($data['rjstatus'] < 50 || $data['rjrstatus'] < 50) ? '--':$data['rj_sale']?></b>元</dd> 
                        <dt>奖池奖金：</dt>
                        <dd><b class="spec"><?php echo ($data['rjstatus'] < 50 || $data['rjrstatus'] < 50) ? '--':$data['award']?></b>元</dd>
                    </dl>
                    <div class="lottery-detail-table">
                        <h2>开奖详情：</h2>
                        <div>
                            <table class="jc-inTable">
                                <tbody>
                                    <tr class="th-bg-fix">
                                        <th width="7%">场次</th>
                                        <td width="6%">1</td>
                                        <td width="6%">2</td>
                                        <td width="6%">3</td>
                                        <td width="6%">4</td>
                                        <td width="6%">5</td>
                                        <td width="6%">6</td>
                                        <td width="6%">7</td>
                                        <td width="6%">8</td>
                                        <td width="6%">9</td>
                                        <td width="6%">10</td>
                                        <td width="6%">11</td>
                                        <td width="6%">12</td>
                                        <td width="6%">13</td>
                                        <td width="6%" class="last">14</td>
                                    </tr>
                                    <tr class="text-vertical">
                                        <th><span>主队</span></th>
                                        <?php foreach ($team as $tm)
                                        {?>
                                        <td><span><?php echo $tm['home']?></span></td>
                                        <?php }?>
                                    </tr>
                                    <tr class="text-vertical">
                                        <th><span>客队</span></th>
                                        <?php foreach ($team as $tm)
                                        {?>
                                        <td><span><?php echo $tm['away']?></span></td>
                                        <?php }?>
                                    </tr>
                                    <tr>
                                        <th>彩果</th>
                                        <?php foreach (explode(',', $data['result']) as $value)
                                        {?>
                                        <td class="spec"><?php echo $value?></td>
                                        <?php }?>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="jc-inTable">
                                <thead>
                                    <tr>
                                        <th width="20%">奖项</th>
                                        <th width="20%">中奖注数</th>
                                        <th width="20%">单注奖金（元）</th>
                                        <th width="40%">中奖条件</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    <?php $award_detail = json_decode($data['award_detail'])?>
                                        <th>一等奖</th>
                                        <td><?php echo ($data['rjstatus'] < 50 || $data['rjrstatus'] < 50) ? '统计中':$award_detail->rj->zs?></td>
                                        <td><?php echo ($data['rjstatus'] < 50 || $data['rjrstatus'] < 50) ? '统计中':$award_detail->rj->dzjj?></td>
                                        <td>一等奖：9场比赛胜平负结果全中</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>