<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/lotteryZQDG.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/dialog.css');?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/math.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/jczq_dg.js');?>"></script>
<script type="text/javascript">
var type = '<?php echo $jczqType; ?>';
var typeCnName = '<?php echo $cnName . ", " . $typeMAP[$jczqType]["cnName"]; ?>';
</script>
<!--容器-->
<div class="lottery">
    <div class="wrap clearfix" id="container">
        <?php echo $this->load->view('elements/lottery/info_panel', array('noIssue' => true)); ?>
        <?php //echo $this->load->view('elements/crowd/buy'); ?>
        <div class="userLottery">
        	<?php $this->load->view('elements/lottery/tabs', array('type' => 'cast')); ?>
       		<?php echo $this->load->view('elements/jczq/dg_types'); ?>
        </div>
        <div class="lotteryTableTH">
            <?php $i = 0; ?>
            <?php foreach ($matches as $date => $dateMatches): ?>
            <?php foreach ($dateMatches as $match): ?>
            <?php if (!empty($match['spfFu']) || !empty($match['rqspfFu'])): ?>
            <?php //if (true): ?>
            <?php if ($i % 2 == 0): ?>
            <table class="fl matches"
            <?php else: ?>
            <table class="fr matches"
            <?php endif; ?>
                data-mid="<?php echo $match['mid']; ?>"
                data-home="<?php echo $match['home']; ?>"
                data-away="<?php echo $match['awary']; ?>"
                data-let="<?php echo $match['let']; ?>"
                data-league="<?php echo $leagues[$match['name']]; ?>"
                data-wid="<?php echo $match['weekId']; ?>" >
            <?php $i += 1; ?>
                <tr>
                    <td colspan="5">
                        <strong class="fl ml"><?php echo $match['home']; ?>&nbsp;VS&nbsp;<?php echo $match['awary']; ?></strong>
                        <strong class="fr mr"><?php echo $match['weekId']; ?>&nbsp;<?php echo date('H:i:s', $match['dt'] / 1000); ?></strong>
                    </td>
                </tr>
                <tr>
                    <td rowspan="2" width="25%">让球</td>
                    <td width="15%">0</td>
                    <td width="20%">
                        <?php if (!empty($match['spfFu'])): ?>
                        <div class="spf-option"
                        <?php else: ?>
                        <div
                        <?php endif; ?>
                             data-val="3"
                             data-odd="<?php echo $match['spfSp3']; ?>">
                            <?php if (!empty($match['spfFu'])): ?>
                            <p>胜<?php echo $match['spfSp3']; ?></p>
                            <em class="clear">x</em>
                            <p class="rmb-money">￥&nbsp;<span class="money">0</span></p>
                            <?php else: ?>
                                <p>停售</p>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td width="20%">
                        <?php if (!empty($match['spfFu'])): ?>
                        <div class="spf-option"
                        <?php else: ?>
                        <div
                        <?php endif; ?>
                             data-val="1"
                             data-odd="<?php echo $match['spfSp1']; ?>">
                            <?php if (!empty($match['spfFu'])): ?>
                            <p>平<?php echo $match['spfSp1']; ?></p>
                            <em class="clear">x</em>
                            <p class="rmb-money">￥&nbsp;<span class="money">0</span></p>
                            <?php else: ?>
                                <p>停售</p>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td width="20%">
                        <?php if (!empty($match['spfFu'])): ?>
                        <div class="spf-option"
                        <?php else: ?>
                        <div
                        <?php endif; ?>
                             data-val="0"
                             data-odd="<?php echo $match['spfSp0']; ?>">
                            <?php if (!empty($match['spfFu'])): ?>
                            <p>负<?php echo $match['spfSp0']; ?></p>
                            <em class="clear">x</em>
                            <p class="rmb-money">￥&nbsp;<span class="money">0</span></p>
                            <?php else: ?>
                                <p>停售</p>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $match['let']; ?></td>
                    <td>
                        <?php if (!empty($match['rqspfFu'])): ?>
                        <div class="rqspf-option"
                        <?php else: ?>
                        <div
                        <?php endif; ?>
                             data-val="3"
                             data-odd="<?php echo $match['rqspfSp3']; ?>">
                            <?php if (!empty($match['rqspfFu'])): ?>
                            <p>胜<?php echo $match['rqspfSp3']; ?></p>
                            <em class="clear">x</em>
                            <p class="rmb-money">￥&nbsp;<span class="money">0</span></p>
                            <?php else: ?>
                            <p>停售</p>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <?php if (!empty($match['rqspfFu'])): ?>
                        <div class="rqspf-option"
                        <?php else: ?>
                        <div
                        <?php endif; ?>
                             data-val="1"
                             data-odd="<?php echo $match['rqspfSp1']; ?>">
                            <?php if (!empty($match['rqspfFu'])): ?>
                            <p>平<?php echo $match['rqspfSp1']; ?></p>
                            <em class="clear">x</em>
                            <p class="rmb-money">￥&nbsp;<span class="money">0</span></p>
                            <?php else: ?>
                                <p>停售</p>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <?php if (!empty($match['rqspfFu'])): ?>
                        <div class="rqspf-option"
                        <?php else: ?>
                        <div
                        <?php endif; ?>
                             data-val="0"
                             data-odd="<?php echo $match['rqspfSp0']; ?>">
                            <?php if (!empty($match['rqspfFu'])): ?>
                            <p>负<?php echo $match['rqspfSp0']; ?></p>
                            <em class="clear">x</em>
                            <p class="rmb-money">￥&nbsp;<span class="money">0</span></p>
                            <?php else: ?>
                            <p>停售</p>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="5">
                        <strong class="fl clear-all ml" style="cursor: pointer;">清空</strong>
                        <strong class="fr mr">截止时间：<?php echo date('Y年m月d日 H:i:s', $match['jzdt'] / 1000); ?></strong>
                    </td>
                </tr>
            </table>
            <?php endif; ?>
            <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php $this->load->view('elements/jczq/calc_prize', array('noOptimization' => true)); ?>
<!--容器end-->
