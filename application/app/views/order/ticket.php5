<?php $this->load->view('comm/header'); ?>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/bet-history.min.css');?>">
</head>
<body>
    <div class="wrapper draw-detail">
        <ol class="cp-list">
        <!-- 冠军彩 -->
        <?php if(in_array($lid, array('44', '45'))): ?>
        	<?php if(!empty($orderDetail)): ?>
        	<?php foreach ($orderDetail as $k => $order): ?>
        	<li>
        		<span><?php echo $k+1; ?></span>
	        	<div>
	        		<p><b><?php echo $order['mid'] . $order['name']; ?></b><em><i><?php echo !empty($order['sp'])?'('.$order['sp'].')':'';?></i></em></p>
		       		<p><span><?php echo $order['betTnum']; ?>注<?php echo $order['multi']; ?>倍</span><span><?php echo $order['status'];?></span><em><em <?php if($order['bonus'] > 0):?>class="bingo"<?php endif; ?>><?php echo $order['statusMsg'];?></em></em></p>
		       	</div>
	        </li>
        	<?php endforeach; ?>
    		<?php endif; ?>
    	<!-- 上海快三 -->
    	<?php elseif(in_array($lid, array('53', '56', '57'))): ?>
    		<?php if(!empty($orderDetail)): ?>
    		<?php foreach ($orderDetail as $k => $order): ?>
    		<li <?php if($order['status'] == 600):?>class="draw-false"<?php endif; ?>>
	        	<span><?php echo $k+1; ?></span>
	        	<div>
	        		<div class="num-group">
			        	<span class="num-group-tag"><?php echo $order['playTypeName']; ?></span>
			        	<?php echo $order['tpl']; ?>
			        </div>
		        	<p><span><?php echo $order['betTnum']; ?>注<?php echo $order['multi']; ?>倍</span><span><?php echo ($order['status'] == 600)?'出票失败':'出票成功'; ?></span><em><?php echo ($order['status'] == 2000) ? "<em class='bingo'>奖金".number_format(ParseUnit($order['bonus'], 1), 2)."元</em>" : (($order['status'] == 1000) ? '未中奖' : (($order['status'] == 600)?'':'等待开奖'))?></em></p>
		       	</div>
	        </li>
    		<?php endforeach; ?>
    		<?php endif; ?>
    	<!-- 快乐扑克 -->
    	<?php elseif($lid == '54'): ?>
    		<?php if(!empty($orderDetail)): ?>
    		<?php foreach ($orderDetail as $k => $order): ?>
    		<li <?php if($order['status'] == 600):?>class="draw-false"<?php endif; ?>>
	        	<span><?php echo $k+1; ?></span>
	        	<div>
	        		<div class="num-group">
			        	<span class="num-group-tag"><?php echo $order['playTypeName']; ?></span>
			        	<?php echo $order['tpl']; ?>
			        </div>
		        	<p><span><?php echo $order['betNum']; ?>注<?php echo $order['multi']; ?>倍</span><span><?php echo ($order['status'] == 600)?'出票失败':'出票成功'; ?></span><em><?php echo ($order['status'] == 2000) ? "<em class='bingo'>奖金".number_format(ParseUnit($order['bonus'], 1), 2)."元</em>" : (($order['status'] == 1000) ? '未中奖' : (($order['status'] == 600)?'':'等待开奖'))?></em></p>
		       	</div>
	        </li>
    		<?php endforeach; ?>
    		<?php endif; ?>
    	<?php elseif($lid == '55'): ?>
    		<?php if(!empty($orderDetail)): ?>
    		<?php foreach ($orderDetail as $k => $order): ?>
    		<li <?php if($order['status'] == 600):?>class="draw-false"<?php endif; ?>>
	        	<span><?php echo $k+1; ?></span>
	        	<div>
	        		<div class="num-group">
			        	<span class="num-group-tag"><?php echo $order['playTypeName']; ?></span>
			        	<?php echo $order['tpl']; ?>
			        </div>
		        	<p><span><?php echo $order['betTnum']; ?>注<?php echo $order['multi']; ?>倍</span><span><?php echo ($order['status'] == 600)?'出票失败':'出票成功'; ?></span><em><?php echo ($order['status'] == 2000) ? "<em class='bingo'>奖金".number_format(ParseUnit($order['bonus'], 1), 2)."元</em>" : (($order['status'] == 1000) ? '未中奖' : (($order['status'] == 600)?'':'等待开奖'))?></em></p>
		       	</div>
	        </li>
    		<?php endforeach; ?>
    		<?php endif; ?>
    	<?php else:?>
	        <?php foreach ($orderDetail as $k => $order) {?>
	        	<li <?php if ($order['status'] == 600) {?> class="draw-false"<?php }?>>
	        	<span><?php echo $k+1?></span>
	        	<div>
	        	<?php switch ($lidtype) {
	        		case 'num':?>
		        	<div class="num-group">
			        	<span class="num-group-tag"><?php echo $order['type']?></span>
			        	<?php foreach (explode('|', str_replace('*', '|', $order['codes'])) as $ck => $code) {
			        		foreach (explode('#', $code) as $dk => $cd) {
								if ($dk == 0 && count(explode('#', $code)) > 1) {?>
									<span>(</span>
							<?php }
							foreach (explode(',', $cd) as $c) {?>
			        		<span 
			        		<?php if ($lid == '21406' || $lid == '21407' || $lid == '21408' || $lid == '21421') {
			        			if (in_array($order['playType'], array(1, 9, 10))) {
									if (!empty($awardArr[0]) && $c == $awardArr[0][$ck]) {echo "class='bingo'";}
								}elseif (in_array($order['playType'], array(11, 12))) {
									$arr = array(11 => 2, 12 => 3);
									if (!empty($awardArr[0]) && in_array($c, array_slice($awardArr[0], 0, $arr[$order['playType']]))) {echo "class='bingo'";}
								}else {
									if (!empty($awardArr[0]) && in_array($c, $awardArr[0])) {echo "class='bingo'";}
								}
							} elseif ($lid == '23528') {
								if (!empty($awardArr[0]) && in_array($c, $awardArr[0])) {
									echo "class='bingo'";
								}elseif (!empty($awardArr[1]) && in_array($c, $awardArr[1])) {
									echo "class='bingo special-color'";
								}
							} elseif (in_array($lid, array('33', '52')) && in_array($order['playType'], array(2, 3))) {
								foreach ($awardArr as $award) {
									if (in_array($c, $award)) {echo "class='bingo'"; break;}
								}
							} else {
								if ((!empty($awardArr[$ck]) || $awardArr[$ck] === '0') && in_array($c, $awardArr[$ck])) {echo "class='bingo'";}
							}?>
			        		><?php echo $c?></span>
			        	<?php }
			        		if ($dk == 0 && count(explode('#', $code)) > 1) {?>
			        			<span>)</span>
			        	<?php }
						}
			        	if ($ck+1 !== count(explode('|', str_replace('*', '|', $order['codes'])))) {
							if (in_array($lid, array('51', '23529'))) {?>
								<span class="symbol-colon">:</span>
						<?php }elseif (!(in_array($lid, array('33', '52')) && in_array($order['playType'], array(2, 3)))) {?>
								<span class="symbol-line">|</span>
						<?php }
							}
			        	}?>
		        	</div>
		        	<p><span><?php echo $order['betNum']?>注<?php echo $order['multi']?>倍</span><span><?php if ($order['status'] == 600) {echo '出票失败</span>';} else {echo '出票成功</span>';?><em><?php echo ($order['bonus'] > 0) ? "<em class='bingo'>奖金".number_format(ParseUnit($order['bonus'], 1), 2)."元</em>" : ((in_array($order['status'], array(1000, 2000))) ? '未中奖' : '等待开奖')?></em><?php } ?></p>
		       	<?php break;
		       		case 'sfc':?>
		       			<div class="num-group">
		       			<?php foreach (explode('*', $order['codes']) as $k => $code) {?>
		       				<span><?php foreach (explode(',', $code) as $c) {echo $c == 4 ? '-' : ((!empty($awardArr[$k]) || $awardArr[$k] === '0')  && $awardArr[$k] == $c ? "<i class='bingo'>".$c."</i>" :"<i>".$c."</i>"); }?></span>
		       			<?php }?>
	                    </div>
	                    <p><span><?php echo $order['type']?></span><span><?php echo $order['betNum']?>注<?php echo $order['multi']?>倍</span><span><?php if ($order['status'] == 600) {echo '出票失败</span>';} else {echo '出票成功</span>';?><em><?php echo ($order['status'] == 2000) ? "<em class='bingo'>奖金".number_format(ParseUnit($order['bonus'], 1), 2)."元</em>" : (($order['status'] == 1000) ? '未中奖' : '等待开奖')?></em><?php } ?></p>
		       		<?php break;
		       		case 'jc':?>
		       			<p><?php echo $order['matchInfo']?></p>
		       			<p><span><?php echo $order['type']?></span><span><?php echo $order['betNum']?>注<?php echo $order['multi']?>倍</span><span><?php if ($order['status'] == 600) {echo '出票失败</span>';} else {echo '出票成功</span>';?><em><?php echo ($order['status'] == 2000) ? "<em class='bingo'>奖金".number_format(ParseUnit($order['bonus'], 1), 2)."元</em>" : (($order['status'] == 1000) ? '未中奖' : '等待开奖')?></em><?php } ?></p>
		       		<?php break;
	        	}?>
	        	</div>
	        	</li>
	        <?php }?>
	    <?php endif; ?>
        </ol>
    </div>
</body>
</html>