<div class="pub-pop pop-w-min bankList">
	<div class="pop-in">
		<div class="pop-head">
			<h2>管理快捷银行卡</h2>
			<span class="pop-close" title="关闭">&times;</span>
		</div>
		<div class="pop-body">
			<div class="pop-manage-bank bank_list">
				<ul>
					<?php 
					$types = array('2' => '储蓄卡', '3' => '信用卡');
					?>
					<?php foreach ($cardList as $key => $val):?>
					<?php 
						$dataVal = $val['no_agree'] . '|' . $val['card_type'];
					?>
					<li <?php if($val['no_agree'] == $no_agree):?> class="selected"<?php endif;?> data-val="<?php echo $dataVal;?>">
						<a href="javascript:;" class="bank-del" data-val="<?php echo $val['no_agree'];?>">&times;</a>
						<span><?php echo $val['bank_name'];?> <?php echo $types[$val['card_type']];?> 尾号<?php echo $val['card_no'];?></span>
						<i class="s_yes"></i>
					</li>
					<?php endforeach;?>
				</ul>
				<p>删除银行卡后，系统将会删除该银行卡在166彩票和银联登记的所有信息。</p>
			</div>
			
		</div>
		<div class="pop-foot">
			<div class="btn-group">
				<a href="javascript:;" class="btn-pop-confirm" id="llbankpop">确定</a>
			</div>
		</div>
	</div>
</div>
<script>
$(function(){
	// 选择银行
    $('.bank_list').on('click', 'li', function(){
        var $this = $(this);
        $this.addClass('selected').siblings().removeClass('selected');
    });
    //管理快捷银行卡
	$('#llbankpop').on('click', function(){
		if($('.bankList').find('.selected').length > 0){
			var bankData = $('.bankList').find('.selected').attr('data-val').split("|");
			$('input[name="pd_FrpId"]').val('');
			$('input[name="cardNo"]').val('');
			$('input[name="no_agree"]').val(bankData[0]);
			$('input[name="pay_type"]').val(bankData[1]);
			var bankInfo = $('.bankList').find('.selected').find('span').html();
			$('#selectedBank').html(bankInfo);
		}else{
			$('input[name="no_agree"]').val('');
			$('input[name="pay_type"]').val('');
			$.ajax({
                type: 'post',
                url: '/wallet/getquickView',
                success: function (response) {
                	$("#platform_quick").html(response);
                }
            });
		}
		cx.PopCom.hide('.bankList');
	});
	
	//删除银行卡
	$('.bank_list').on('click', '.bank-del', function(e){
		var $this = $(this);
		var noAgree = $this.attr('data-val');
		$.ajax({
            type: 'post',
            url: '/wallet/llbankUnbind',
            data: {noAgree:noAgree},
            dataType: 'json',
            success: function (response) {
            	if(response.ret_code == '0000'){
              	   //成功
            		$this.parents('li').remove();
 	            }else{
 	            	new cx.Alert({content: response.ret_msg});
 		        }
            }
        });
		var e = e || window.event;

        if (e && e.stopPropagation){
            e.stopPropagation();    
        }
        else{
            e.cancelBubble=true;
        }
	});

	//弹窗关闭前未选择银行
	$('.bankList').find('.pop-close').click(function () {
		if($(this).find('.selected').length == 0){
			$.ajax({
                type: 'post',
                url: '/wallet/getquickView',
                success: function (response) {
                	$("#platform_quick").html(response);
                }
            });
		}
    });
});
</script>