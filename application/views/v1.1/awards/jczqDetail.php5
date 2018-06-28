<!--容器-->
<?php 
$this->load->config('wenan');
$wenan = $this->config->item('wenan');
?>
<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/lottery-custom.min.css');?>" rel="stylesheet" />
<div class="wrap mod-box jc-sg">
    <div class="mod-box-hd">
        <h1 class="mod-box-title">开奖结果及固定奖金</h1>
        <span><?php echo ($info['m_date'])?$info['m_date']:''; ?> <?php echo ($info['mname'])?$info['mname']:''; ?> <?php echo ($info['home'] && $info['away'])?'(主)' . $info['home'] . ' VS (客)' . $info['away']:''; ?><?php echo ($info['full_score'])?'（' . $info['full_score'] . '）':''; ?></span>
        <span class="mod-box-note">奖金以出票时刻盘口及出票赔率为准</span>
    </div>

    <div class=" mod-box-bd ">
        <div class="table-box">
            <h2>单场开奖结果</h2>
            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th>玩法</th>
                            <th>胜平负</th>
                            <th>让球胜平负</th>
                            <th>比分</th>
                            <th>总进球</th>
                            <th>半全场胜负</th>
                        </tr>
                    </thead>
                    <?php if(!empty($sg)): ?>
                    <tbody>
                        <tr>
                            <td>开奖结果</td>
                            <td><?php echo str_replace(array('胜', '平', '负'), array($wenan['jzspf']['3'], $wenan['jzspf']['1'], $wenan['jzspf']['0']), $sg['spf']); ?></td>
                            <td><?php echo str_replace(array('胜', '平', '负'), array($wenan['jzspf']['r3'], $wenan['jzspf']['r1'], $wenan['jzspf']['r0']), $sg['rqspf']); ?></td>
                            <td><?php echo $sg['cbf']; ?></td>
                            <td><?php echo $sg['jqs']; ?></td>
                            <td><?php echo $sg['bqc']; ?></td>
                        </tr>
                    </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <div class="table-box">
            <h2>让球胜平负过关固定奖金（所有玩法固定奖金以购买时票面显示奖金为准）</h2>
            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th>发布时间</th>
                            <th>让球</th>
                            <th><?php echo $wenan['jzspf']['r3']?></th>
                            <th><?php echo $wenan['jzspf']['r1']?></th>
                            <th><?php echo $wenan['jzspf']['r0']?></th>
                        </tr>
                    </thead>
                    <?php if(!empty($rqspf)): ?>
                    <tbody>
                        <?php foreach ($rqspf as $key => $items): ?>
                        <tr>
                            <td><?php echo $items['t']; ?></td>
                            <td><?php echo $items['rq']; ?></td>
                            <td class="<?php echo ($info['rqspf'] == '胜')?'td-bingo':''; ?>"><?php echo $items['s']; ?></td>
                            <td class="<?php echo ($info['rqspf'] == '平')?'td-bingo':''; ?>"><?php echo $items['p']; ?></td>
                            <td class="<?php echo ($info['rqspf'] == '负')?'td-bingo':''; ?>"><?php echo $items['f']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <div class="table-box">
            <h2>胜平负过关固定奖金（所有玩法固定奖金以购买时票面显示奖金为准）</h2>
            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th>发布时间</th>
                            <th><?php echo $wenan['jzspf']['3']?></th>
                            <th><?php echo $wenan['jzspf']['1']?></th>
                            <th><?php echo $wenan['jzspf']['0']?></th>
                        </tr>
                    </thead>
                    <?php if(!empty($spf)): ?>
                    <tbody>
                        <?php foreach ($spf as $key => $items): ?>
                        <tr>
                            <td><?php echo $items['t']; ?></td>
                            <td class="<?php echo ($info['spf'] == '胜')?'td-bingo':''; ?>"><?php echo $items['s']; ?></td>
                            <td class="<?php echo ($info['spf'] == '平')?'td-bingo':''; ?>"><?php echo $items['p']; ?></td>
                            <td class="<?php echo ($info['spf'] == '负')?'td-bingo':''; ?>"><?php echo $items['f']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <div class="table-box">
            <h2>总进球数过关固定奖金</h2>
            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th width="190">发布时间</th>
                            <th>0</th>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
                            <th>7+</th>
                        </tr>
                    </thead>
                    <?php if(!empty($jqs)): ?>
                    <tbody>
                        <?php foreach ($jqs as $key => $items): ?>
                        <tr>
                            <td><?php echo $items['t']; ?></td>
                            <td class="<?php echo ($info['jqs'] == 0)?'td-bingo':''; ?>"><?php echo $items[0]; ?></td>
                            <td class="<?php echo ($info['jqs'] == 1)?'td-bingo':''; ?>"><?php echo $items[1]; ?></td>
                            <td class="<?php echo ($info['jqs'] == 2)?'td-bingo':''; ?>"><?php echo $items[2]; ?></td>
                            <td class="<?php echo ($info['jqs'] == 3)?'td-bingo':''; ?>"><?php echo $items[3]; ?></td>
                            <td class="<?php echo ($info['jqs'] == 4)?'td-bingo':''; ?>"><?php echo $items[4]; ?></td>
                            <td class="<?php echo ($info['jqs'] == 5)?'td-bingo':''; ?>"><?php echo $items[5]; ?></td>
                            <td class="<?php echo ($info['jqs'] == 6)?'td-bingo':''; ?>"><?php echo $items[6]; ?></td>
                            <td class="<?php echo ($info['jqs'] >= 7)?'td-bingo':''; ?>"><?php echo $items[7]; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <div class="table-box">
            <h2>半全场胜平负过关固定奖金</h2>
            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th width="190">发布时间</th>
                            <th>胜胜</th>
                            <th>胜平</th>
                            <th>胜负</th>
                            <th>平胜</th>
                            <th>平平</th>
                            <th>平负</th>
                            <th>负胜</th>
                            <th>负平</th>
                            <th>负负</th>
                        </tr>
                    </thead>
                    <?php if(!empty($bqc)): ?>
                    <tbody>
                        <?php foreach ($bqc as $key => $items): ?>
                        <tr>
                            <td><?php echo $items['t']; ?></td>
                            <td class="<?php echo ($info['bqc'] == 'ss')?'td-bingo':''; ?>"><?php echo $items['ss']; ?></td>
                            <td class="<?php echo ($info['bqc'] == 'sp')?'td-bingo':''; ?>"><?php echo $items['sp']; ?></td>
                            <td class="<?php echo ($info['bqc'] == 'sf')?'td-bingo':''; ?>"><?php echo $items['sf']; ?></td>
                            <td class="<?php echo ($info['bqc'] == 'ps')?'td-bingo':''; ?>"><?php echo $items['ps']; ?></td>
                            <td class="<?php echo ($info['bqc'] == 'pp')?'td-bingo':''; ?>"><?php echo $items['pp']; ?></td>
                            <td class="<?php echo ($info['bqc'] == 'pf')?'td-bingo':''; ?>"><?php echo $items['pf']; ?></td>
                            <td class="<?php echo ($info['bqc'] == 'fs')?'td-bingo':''; ?>"><?php echo $items['fs']; ?></td>
                            <td class="<?php echo ($info['bqc'] == 'fp')?'td-bingo':$info['bqc']; ?>"><?php echo $items['fp']; ?></td>
                            <td class="<?php echo ($info['bqc'] == 'ff')?'td-bingo':''; ?>"><?php echo $items['ff']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>


        <div class="table-box">
            <h2>比分固定奖金</h2>
            <?php if(!empty($cbf)): ?>
            <?php foreach ($cbf as $key => $items): ?>
            <div class="table">
                <div class="caption">发布时间 <?php echo $items['t']; ?></div>
                <table>
                    <thead>
                        <tr>
                            <th>1:0</th>
                            <th>2:0</th>
                            <th>2:1</th>
                            <th>3:0</th>
                            <th>3:1</th>
                            <th>3:2</th>
                            <th>4:0</th>
                            <th>4:1</th>
                            <th>4:2</th>
                            <th>5:0</th>
                            <th>5:1</th>
                            <th>5:2</th>
                            <th>胜其他</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="<?php echo ($info['cbf'] == 's1')?'td-bingo':''; ?>"><?php echo $items['s1']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's2')?'td-bingo':''; ?>"><?php echo $items['s2']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's3')?'td-bingo':''; ?>"><?php echo $items['s3']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's4')?'td-bingo':''; ?>"><?php echo $items['s4']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's5')?'td-bingo':''; ?>"><?php echo $items['s5']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's6')?'td-bingo':''; ?>"><?php echo $items['s6']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's7')?'td-bingo':''; ?>"><?php echo $items['s7']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's8')?'td-bingo':''; ?>"><?php echo $items['s8']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's9')?'td-bingo':''; ?>"><?php echo $items['s9']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's10')?'td-bingo':''; ?>"><?php echo $items['s10']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's11')?'td-bingo':''; ?>"><?php echo $items['s11']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's12')?'td-bingo':''; ?>"><?php echo $items['s12']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's13')?'td-bingo':''; ?>"><?php echo $items['s13']; ?></td>
                        </tr>
                    </tbody>
                </table>
                <div class="fix-table">
                    <table>
                        <thead>
                            <tr>
                                <th>0:0</th>
                                <th>1:1</th>
                                <th>2:2</th>
                                <th>3:3</th>
                                <th>平其他</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="<?php echo ($info['cbf'] == 's14')?'td-bingo':''; ?>"><?php echo $items['s14']; ?></td>
                                <td class="<?php echo ($info['cbf'] == 's15')?'td-bingo':''; ?>"><?php echo $items['s15']; ?></td>
                                <td class="<?php echo ($info['cbf'] == 's16')?'td-bingo':''; ?>"><?php echo $items['s16']; ?></td>
                                <td class="<?php echo ($info['cbf'] == 's17')?'td-bingo':''; ?>"><?php echo $items['s17']; ?></td>
                                <td class="<?php echo ($info['cbf'] == 's18')?'td-bingo':''; ?>"><?php echo $items['s18']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>0:1</th>
                            <th>0:2</th>
                            <th>1:2</th>
                            <th>0:3</th>
                            <th>1:3</th>
                            <th>2:3</th>
                            <th>0:4</th>
                            <th>1:4</th>
                            <th>2:4</th>
                            <th>0:5</th>
                            <th>1:5</th>
                            <th>2:5</th>
                            <th>负其他</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="<?php echo ($info['cbf'] == 's19')?'td-bingo':''; ?>"><?php echo $items['s19']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's20')?'td-bingo':''; ?>"><?php echo $items['s20']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's21')?'td-bingo':''; ?>"><?php echo $items['s21']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's22')?'td-bingo':''; ?>"><?php echo $items['s22']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's23')?'td-bingo':''; ?>"><?php echo $items['s23']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's24')?'td-bingo':''; ?>"><?php echo $items['s24']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's25')?'td-bingo':''; ?>"><?php echo $items['s25']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's26')?'td-bingo':''; ?>"><?php echo $items['s26']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's27')?'td-bingo':''; ?>"><?php echo $items['s27']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's28')?'td-bingo':''; ?>"><?php echo $items['s28']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's29')?'td-bingo':''; ?>"><?php echo $items['s29']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's30')?'td-bingo':''; ?>"><?php echo $items['s30']; ?></td>
                            <td class="<?php echo ($info['cbf'] == 's31')?'td-bingo':''; ?>"><?php echo $items['s31']; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>
<!--容器end-->
