<!--容器-->
<?php 
$this->load->config('wenan');
$wenan = $this->config->item('wenan');
?>
<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/lottery-custom.min.css');?>" rel="stylesheet" />
<div class="wrap mod-box jc-sg">
    <div class="mod-box-hd">
        <h1 class="mod-box-title">开奖结果及固定奖金</h1>
        <span><?php echo ($info['m_date'])?$info['m_date']:''; ?> <?php echo ($info['mname'])?$info['mname']:''; ?> <?php echo ($info['home'] && $info['away'])?'(客)' . $info['away'] . ' VS (主)' . $info['home']:''; ?><?php echo ($info['full_score'])?'（' . $info['full_score'] . '）':''; ?></span>
        <span class="mod-box-note">奖金以出票时刻盘口及出票赔率为准</span>
    </div>

    <div class=" mod-box-bd ">
        <div class="table-column">

            <div class="table-box table-column-l">
                <h2>胜负固定奖金</h2>
                <div class="table">
                    <table>
                        <thead>
                            <tr>
                                <th width="190">发布时间</th>
                                <th><?php echo $wenan['jlsf']['0']?></th>
                                <th><?php echo $wenan['jlsf']['3']?></th>
                            </tr>
                        </thead>
                        <?php if(!empty($sf)): ?>
                        <tbody>                     
                            <?php foreach ($sf as $key => $items): ?>
                            <tr>
                                <td><?php echo $items['t']; ?></td>
                                <td class="<?php echo ($info['hscore'] < $info['ascore'])?'td-bingo':''; ?>"><?php echo $items['zf']; ?></td>
                                <td class="<?php echo ($info['hscore'] > $info['ascore'])?'td-bingo':''; ?>"><?php echo $items['zs']; ?></td>
                            </tr>
                            <?php endforeach; ?>         
                        </tbody>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <div class="table-box table-column-r">
                <h2>让分胜负固定奖金</h2>
                <div class="table">
                    <table>
                        <thead>
                            <tr>
                                <th width="190">发布时间</th>
                                <th><?php echo $wenan['jlsf']['0']?></th>
                                <th>让分</th>
                                <th><?php echo $wenan['jlsf']['3']?></th>
                            </tr>
                        </thead>
                        <?php if(!empty($rfsf)): ?>
                        <tbody>
                            <?php foreach ($rfsf as $key => $items): ?>
                            <tr>
                                <td><?php echo $items['t']; ?></td>
                                <td class="<?php echo ($info['hscore'] + $items['rf'] < $info['ascore'])?'td-bingo':''; ?>"><?php echo $items['rfzf']; ?></td>
                                <td><?php echo $items['rf']; ?></td>
                                <td class="<?php echo ($info['hscore'] + $items['rf'] > $info['ascore'])?'td-bingo':''; ?>"><?php echo $items['rfzs']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

        </div>

        <div class="table-box">
            <?php $score = ($info['full_score'])? explode(':', trim($info['full_score'])) : array(); ?>
            <h2>大小分固定奖金（总分<?php echo ($score)?array_sum($score):''; ?>）</h2>
            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th>发布时间</th>
                            <th>大</th>
                            <th>预设总分</th>
                            <th>小</th>
                            <th>彩果</th>
                        </tr>
                    </thead>
                    <?php if(!empty($dxf)): ?>
                    <tbody>
                        <?php foreach ($dxf as $key => $items): ?>
                        <tr>
                            <td><?php echo $items['t']; ?></td>
                            <td class="<?php echo ($info['fscore'] > $items['zf'])?'td-bingo':''; ?>"><?php echo $items['d']; ?></td>
                            <td><?php echo $items['zf']; ?></td>
                            <td class="<?php echo ($info['fscore'] < $items['zf'])?'td-bingo':''; ?>"><?php echo $items['x']; ?></td>
                            <td><?php echo $items['cg']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <div class="table-box">
            <h2>胜分差固定奖金</h2>
            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th width="190" rowspan="2">发布时间</th>
                            <th colspan="6" class="th-colbd"><?php echo $wenan['jlsf']['0']?></th>
                            <th colspan="6" class="th-colbd"><?php echo $wenan['jlsf']['3']?></th>
                        </tr>
                        <tr>
                            <th>1-5</th>
                            <th>6-10</th>
                            <th>11-15</th>
                            <th>16-20</th>
                            <th>21-25</th>
                            <th>26+</th>
                            <th>1-5</th>
                            <th>6-10</th>
                            <th>11-15</th>
                            <th>16-20</th>
                            <th>21-25</th>
                            <th>26+</th>
                        </tr>
                    </thead>
                    <?php if(!empty($sfc)): ?>
                    <tbody>
                        <?php foreach ($sfc as $key => $items): ?>
                        <?php 
                            $r = ($info['ascore'] > $info['hscore']) ? 'ks' : 'zs';
                            $c = abs($info['ascore'] - $info['hscore']);
                        ?>
                        <tr>
                            <td><?php echo $items['t']; ?></td>
                            <td class="<?php echo ($r == 'ks' && $c >= 1 && $c <= 5)?'td-bingo':''; ?>"><?php echo $items['ks_k1']; ?></td>
                            <td class="<?php echo ($r == 'ks' && $c >= 6 && $c <= 10)?'td-bingo':''; ?>"><?php echo $items['ks_k2']; ?></td>
                            <td class="<?php echo ($r == 'ks' && $c >= 11 && $c <= 15)?'td-bingo':''; ?>"><?php echo $items['ks_k3']; ?></td>
                            <td class="<?php echo ($r == 'ks' && $c >= 16 && $c <= 20)?'td-bingo':''; ?>"><?php echo $items['ks_k4']; ?></td>
                            <td class="<?php echo ($r == 'ks' && $c >= 21 && $c <= 25)?'td-bingo':''; ?>"><?php echo $items['ks_k5']; ?></td>
                            <td class="<?php echo ($r == 'ks' && $c >= 26)?'td-bingo':''; ?>"><?php echo $items['ks_k6']; ?></td>
                            <td class="<?php echo ($r == 'zs' && $c >= 1 && $c <= 5)?'td-bingo':''; ?>"><?php echo $items['zs_z1']; ?></td>
                            <td class="<?php echo ($r == 'zs' && $c >= 6 && $c <= 10)?'td-bingo':''; ?>"><?php echo $items['zs_z2']; ?></td>
                            <td class="<?php echo ($r == 'zs' && $c >= 11 && $c <= 15)?'td-bingo':''; ?>"><?php echo $items['zs_z3']; ?></td>
                            <td class="<?php echo ($r == 'zs' && $c >= 16 && $c <= 20)?'td-bingo':''; ?>"><?php echo $items['zs_z4']; ?></td>
                            <td class="<?php echo ($r == 'zs' && $c >= 21 && $c <= 25)?'td-bingo':''; ?>"><?php echo $items['zs_z6']; ?></td>
                            <td class="<?php echo ($r == 'zs' && $c >= 26)?'td-bingo':''; ?>"><?php echo $items['zs_z6']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>
<!--容器end-->
