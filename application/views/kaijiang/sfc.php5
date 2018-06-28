<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/help.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/kaijiang.css');?>" rel="stylesheet" type="text/css" />
<div class="wrap_in help-container lottery-detail">
    <div class="help-section clearfix">
        <?php $this->load->view('kaijiang/aside');?>
        <div class="article">
            <div class="help-content lottery-detail-sfc">
                <div class="lottery-detail-img">
                    <i class="icon-lottery"></i>
                </div>
                <div class="lottery-detail-txt">
                    <div class="lottery-detail-title">
                        <h1 class="lottery-detail-name">胜负彩开奖结果</h1>
                        <div class="date-select">
                            <span>开奖期次：</span>
                            <dl class="simu-select">
                                <dt><?php echo $issue?><i class="arrow"></i></dt>
                                <dd class="select-opt">
                                    <div class="select-opt-in">
                                        <?php foreach ($issueList as $value){?>
                                    		<a href="<?php echo $baseUrl?>kaijiang/sfc/<?php echo $value['issue']?>"><?php echo $value['issue']?></a>
                                    	<?php }?>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                        <a href="<?php echo $baseUrl?>sfc" target="_blank" class="btn btn-bet-small">立即投注</a> 
                    </div>

                    <dl class="lottery-detail-dl">
                        <dt>本期销量：</dt>
                        <dd><b class="spec"><?php echo ($data['status'] < 50 || $data['rstatus'] < 50) ? '--':$data['sfc_sale']?></b>元</dd> 
                        <dt>奖池奖金：</dt>
                        <dd><b class="spec"><?php echo ($data['status'] < 50 || $data['rstatus'] < 50) ? '--':$data['award']?></b>元</dd>
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
                                <?php $award_detail = json_decode($data['award_detail'], true)?>
                                    <tr>
                                        <th>一等奖</th>
                                        <td><?php echo ($data['status'] < 50 || $data['rstatus'] < 50) ? '统计中':$award_detail['1dj']['zs']?></td>
                                        <td><?php echo ($data['status'] < 50 || $data['rstatus'] < 50) ? '统计中':$award_detail['1dj']['dzjj']?></td>
                                        <td>14场比赛胜平负结果全中</td>
                                    </tr>
                                    <tr>
                                        <th>二等奖</th>
                                        <td><?php echo ($data['status'] < 50 || $data['rstatus'] < 50) ? '统计中':$award_detail['2dj']['zs']?></td>
                                        <td><?php echo ($data['status'] < 50 || $data['rstatus'] < 50) ? '统计中':$award_detail['2dj']['dzjj']?></td>
                                        <td>中任意13场比赛胜平负结果</td>
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