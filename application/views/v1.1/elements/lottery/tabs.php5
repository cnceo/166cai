<ul class="bet-type-link">
    <?php $szclidArr = array(SSQ => 'ssq', DLT => 'dlt', FCSD => 'fcsd', PLS => 'pls', QLC => 'qlc', QXC => 'qxc', PLW => 'plw');
    if (array_key_exists($lotteryId, $szclidArr)): ?>
    <li class="selected"><a href="javascript:;">选号投注</a></li>
	<li><a target="_blank" href="/hemai/<?php echo $lotteryId == PLW ? 'pls' : $szclidArr[$lotteryId]?>">参与合买</a><div class="mod-sup"><i class="mod-sup-bg"></i><u>新</u></div></li>
	<li><a target="_blank" href="/gendan/<?php echo $lotteryId == PLW ? 'pls' : $szclidArr[$lotteryId]?>">合买红人</a><div class="mod-sup"><i class="mod-sup-bg"></i><u>新</u></div></li>        
    <?php elseif (in_array($lotteryId, array(JCZQ, GJ, GYJ))): ?>
        <?php foreach ($typeMAP as $key => $value): ?>
            <li <?php echo ($jczqType == $key) ? 'class="selected"' : '' ?>>
                <a href="<?php echo ($jczqType == $key) ? 'javascript:;' : ($baseUrl . 'jczq/' . $key) ?>"><?php echo $value['cnName']; ?></a>
                <?php if ($key == 'dg'): ?>
                    <div class="mod-sup"><i class="mod-sup-bg"></i><u>新</u></div>
                    <div class="mod-tips-b ptips-bd">猜对一场就中奖 返奖率87%<i class="ptips-bd-close">×</i><b></b><s></s></div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        <?php if ($lotteryId == JCZQ) {?>
        <li><a target="_blank" href="/hemai/jczq">参与合买</a><div class="mod-sup"><i class="mod-sup-bg"></i><u>新</u></div></li>
        <li><a target="_blank" href="/gendan/jczq">合买红人</a><div class="mod-sup"><i class="mod-sup-bg"></i><u>新</u></div></li>
        <?php }?>
    <?php elseif ($lotteryId == JCLQ): ?>
        <?php foreach ($typeMAP as $key => $value): ?>
            <li <?php echo ($jclqType == $key) ? 'class="selected"' : '' ?>>
                <a href="<?php echo ($jclqType == $key) ? 'javascript:;' : ($baseUrl . 'jclq/' . $key) ?>"><?php echo $value['cnName']; ?></a>
            </li>
        <?php endforeach; ?>
        <li><a target="_blank" href="/hemai/jclq">参与合买</a><div class="mod-sup"><i class="mod-sup-bg"></i><u>新</u></div></li>
        <li><a target="_blank" href="/gendan/jclq">合买红人</a><div class="mod-sup"><i class="mod-sup-bg"></i><u>新</u></div></li>
    <?php else :
    	$lidArr = array(SFC => array('sfc', '14场胜负彩'), RJ => array('rj', '任选九'),DSSC => array('sfc/dssc', '单式上传'));
    	foreach ($lidArr as $lid => $name) {?>
    		<li  <?php echo  $name[0] == $this->con ? ' class="selected"><a href="javascript:;"' : '><a href="'.$baseUrl.$name[0]?>"><?php echo $name[1]?></a></li>
    	<?php }
    	if (in_array($this->con, array('mylottery', 'kaijiang'))): ?>
            <li <?php echo ($this->con == 'kaijiang') ? 'class="selected"' : '' ?>><a href="<?php echo $baseUrl; ?>kaijiang/<?php echo $lidArr[$lotteryId]?>">赛果开奖</a></li>
        <?php endif; ?>
        <li><a target="_blank" href="/hemai/sfc">参与合买</a><div class="mod-sup"><i class="mod-sup-bg"></i><u>新</u></div></li>
<!--        <li><a target="_blank" href="/gendan/sfc">合买红人</a><div class="mod-sup"><i class="mod-sup-bg"></i><u>新</u></div></li>-->
    <?php endif; ?>
</ul>
