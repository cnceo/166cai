<p class="form-tips-bar">提示：借记卡和信用卡均可充值，无须开通网银！</p>
<div class="form-item" id="inputCard" <?php if($cardList):?>style="display:none;"<?php endif;?>>
    <label class="form-item-label">银行卡号</label>
    <div class="form-item-con">
	<input type="text" class="form-item-ipt j-bank-id" value="" name="quickCard">
	<div class="form-tip">
	    <i class="icon-tip"></i>
	    <span class="form-tip-con quickCard">请输入银行卡号</span>
	    <s></s>
	</div>
	<div class="mod-tips">
	    <span class="bubble-tip" tiptext="<p><strong>借记卡：</strong>支持中国银行、招行、农行、光大银行、华夏、平安、建行、邮政、兴业、中信、浦发、广发等38家银行</p>
		<p><strong>信用卡：</strong>支持中国银行、工行、农行等57家银行</p>">支持银行列表<i class="icon-font">&#xe613;</i></span>
	</div>
	<?php if($cardList):?>
	<div class="bank-selected-lnk">
	    <a href="javascript:;" id="backCardList">返回历史银行卡</a>
	</div>
	<?php endif;?>
    </div>
</div>
<div class="form-item" id="selectCard" <?php if(empty($cardList)):?>style="display:none;"<?php endif;?>>
	<?php if($cardList[0]):?>
    <label class="form-item-label">银行卡号</label>
    <div class="form-item-con">
    <?php 
    	$dataVal = $cardList[0]['no_agree'] . '|' . $cardList[0]['card_type'];
    	$types = array('2' => '储蓄卡', '3' => '信用卡');
    ?>
	<div class="bank-selected" data-val="<?php echo $dataVal;?>" id="selectedBank"><?php echo $cardList[0]['bank_name'];?> <?php echo $types[$cardList[0]['card_type']];?> 尾号<?php echo $cardList[0]['card_no'];?></div>
	<div class="bank-selected-lnk">
	    <a href="javascript:;" id="backInputCard">使用其它银行卡</a>
	    <s class="split-line">|</s>
	    <a href="javascript:;" id="bankManage">管理快捷银行卡</a>
	</div>
    </div>
    <?php endif;?>
</div>

<script>
	$(function(){
		//有默认卡操作
		if($('#selectCard').find('.bank-selected').length > 0){
			var bankData = $('#selectCard').find('.bank-selected').attr('data-val').split("|");
			$('input[name="pd_FrpId"]').val('');
			$('input[name="no_agree"]').val(bankData[0]);
			$('input[name="pay_type"]').val(bankData[1]);
		}
		//返回银行卡历史
		$('#backCardList').click(function(){
			$('input[name="quickCard"]').val('');
			var val = $('#selectCard').find('.bank-selected').data('val').split("|");
			$('input[name="pd_FrpId"]').val('');
			$('input[name="cardNo"]').val('');
			$('input[name="no_agree"]').val(val[0]);
			$('input[name="pay_type"]').val(val[1]);
        	$('.quickCard').closest('.form-tip').removeClass('form-tip-error form-tip-true');
        	$('#selectCard').show();
        	$('#inputCard').hide();
        });
		//使用其它银行卡
        $('#backInputCard').click(function(){
        	$('input[name="quickCard"]').val('');
        	$('input[name="pd_FrpId"]').val('');
			$('input[name="no_agree"]').val('');
			$('input[name="pay_type"]').val('');
			$('.quickCard').html('请输入银行卡号');
        	$('.quickCard').closest('.form-tip').removeClass('form-tip-error form-tip-true');
        	$('#selectCard').hide();
        	$('#inputCard').show();
        });
        $('input[name="quickCard"]').focus(function(){
        	$('input[name="cardNo"]').val('');
			$('input[name="no_agree"]').val('');
			$('input[name="pay_type"]').val('');
        	$('.quickCard').html('请输入银行卡号');
        	$('.quickCard').closest('.form-tip').removeClass('form-tip-error form-tip-true');
        });
        //查询卡bin信息
        $('input[name="quickCard"]').blur(function(e){
        	$('.quickCard').closest('.form-tip').removeClass('form-tip-error form-tip-true');
			var val = $(this).val().replace(/\s+/g, "");
			if( /^\d{15,19}$/.test(val) ) {
				$.ajax({
	                type: 'post',
	                url: '/wallet/getCardBin',
	                data: {cardNo:val},
	                dataType: 'json',
	                success: function (response) {
	                   if(response.ret_code == '0000'){
	                	   $('input[name="pd_FrpId"]').val(response.bank_code);
	                	   $('input[name="pay_type"]').val(response.card_type);
	                	   $('input[name="cardNo"]').val(val);
	                	   $('.quickCard').html(response.bank_name);
	                	   $('.quickCard').closest('.form-tip').addClass('form_tips_ok');
		               }else{
		            	   $('.quickCard').html(response.ret_msg);
			               $('.quickCard').closest('.form-tip').addClass('form-tip-error');
			           }
	                }
	            });
            } else {
            	$('.quickCard').html('请输入正确的银行卡号');
            	$('.quickCard').closest('.form-tip').addClass('form-tip-error');
            }

			var e = e || window.event;

	        if (e && e.stopPropagation){
	            e.stopPropagation();    
	        }
	        else{
	            e.cancelBubble=true;
	        }
		});

        $('#bankManage').click(function(){
        	var no_agree = $('input[name="no_agree"]').val();
        	$.ajax({
	            type: 'post',
	            url: '/wallet/getLlBankPop',
	            data: {no_agree:no_agree},
	            success: function (response) {
	                $('body').append(response);
	                cx.PopCom.show('.bankList');
	                cx.PopCom.close('.bankList');
	                cx.PopCom.cancel('.bankList');
	            }
	        });
        });

        // 银行卡输入卡号，逢4个数字中间插入2个空格
        $('.j-bank-id').on('keyup mouseout input',function(){
            var value=$(this).val().replace(/\s/g,'').replace(/(\d{4})(?=\d)/g,"$1  ");    
            $(this).val(value);
        });

        $('#inputCard').on('mouseenter', '.bubble-tip', function(){
            $.bubble({
                target:this,
                position: 'b',
                align: 'l',
                content: $(this).attr('tiptext'),
                width:'320'
            })
        }).on('mouseleave', '.bubble-tip', function(){
            $('.bubble').hide();
        });
	});
</script>