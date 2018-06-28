<div class="wrapper bank-list bind-list">
	<ul>
	<?php foreach ($data as $val) {?>
		<li>
			<a href="javascript:;" class="bankcard-box bankcard-<?php echo $val['bank_type']?>" pay_agreement_id="<?php $pay_agreement = json_decode($val['pay_agreement'], true);echo $pay_agreement['umpay']?>"
			bank_id = "<?php echo $val['bank_id']?>">
				<h2 class="bankcard-hd"><b><?php echo $bankcode[$val['bank_type']]?></b></h2>
				<p class="bankcard-bd"><?php echo preg_replace('/(\d{4})\d+(\d{4})/', '${1}**** ****${2}', $val['bank_id'])?></p>
				<div class="bankcard-edit">
					<input type="radio" <?php if ($val['is_default']) {?>checked<?php }?> id="bankcard_<?php echo $val['id']?>" name="bank_choose"><label for="bankcard_<?php echo $val['id']?>"></label>
					<div class="unbind" bankid="<?php echo $val['bank_id']?>">解除绑定</div>
				</div>
			</a>
		</li>
	<?php }?>
	</ul>
	