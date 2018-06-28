<div class="ds-mod">
    <div class="ds-mod-hd clearfix">
        <h3>开奖公告</h3>
    </div>
    <div class="ds-mod-bd">
        <ul class="mod-tab clearfix">
        	<?php foreach ($kj as $k => $val) {?> 
        		<li <?php if ($k == 0) {?>class="current" <?php }?>><?php echo $val['cname']?></li>
        	<?php }?>
        </ul>
        <div class="mod-tab-con">
        <?php foreach ($kj as $k => $val) {?>
        	<div class="mod-tab-item notice-<?php echo $val['ename']?>" <?php if ($k == 0){?> style="display:block" <?php }?>>
           	  <div class="picTxt">
                <div class="lottery-img">
                   <svg width="224" height="224">
			           <image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg');?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png');?>" width="224" height="224"></image>
                   </svg>
                </div>
                <h4><?php echo $val['cname']?></h4>
                <p>第<span class="s-des"><?php echo $val['info']['current']['issue'] ?></span>期<a href="/kaijiang/<?php echo $val['enkj'] ? $val['enkj'] : $val['ename']?>" class="detail">详情></a></p>
              </div>
              <p class="p-num">
              <?php $awardArr = explode(":", $val['info']['current']['awardNum']) ?>
                开奖号码：
                <?php foreach (explode(',', $awardArr[0]) as $award) {?>
                	<span class="ball ball-red"><?php echo $award ?></span>
                <?php } ?>
                <?php foreach (explode(',', $awardArr[1]) as $award) {?>
                    <span class="ball ball-blue"><?php echo $award ?></span>
                <?php } ?>
              </p>   
              <p class="p-time">
                开奖时间：<span class="ball"><?php echo date('Y年m月d日', $val['info']['current']['awardTime'] / 1000) ?></span>
              </p>
              <?php if (!in_array($val['ename'], array('fcsd', 'pls', 'plw'))) {?>
              <p class="p-money">
              	<?php 
              	if ($val['pool']['award']) {
					$award = $val['pool']['award'];
					$billion = floor($award / 100000000);
					$million = floor(($award - $billion * 100000000) / 10000);
					$yuan = $award % 10000;
					$pool = array($billion, $million, $yuan);
				} else {
					$pool = explode('|', $val['info']['current']['pool']);
				}
              	if ((empty($pool[0]) && empty($pool[1]) && empty($pool[2]) && in_array($val['ename'], array('dlt', 'ssq')))
              	|| ($val['ename'] === 'qlc' && $val['info']['current']['rStatus'] < 50)): ?>
                            奖池更新中...
                        <?php else: ?>
                            奖池奖金：
                            <?php if ( ! empty($pool[0])): ?>
                                <span class="ball ball-red"><?php echo $pool[0] ?></span>亿
                            <?php endif; ?>
                            <?php if ( ! empty($pool[1])): ?>
                                <span class="ball ball-red"><?php echo $pool[1] ?></span>万
                            <?php endif; ?>
                            <?php if ( ! empty($pool[2]) && in_array($val['ename'], array('dlt', 'ssq'))): ?>
                                <span class="ball ball-red"><?php echo $pool[2] ?></span>元
                            <?php else:?>
                           		<span class="ball ball-red"><?php echo $pool[2] ?></span>元
                            <?php endif; ?>
                        <?php endif; ?>
              </p>
              <?php }?>
            </div>
        <?php }?>
        </div>
    </div>
</div>
