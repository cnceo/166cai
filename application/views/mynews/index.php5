<?php $this->load->view('elements/user/menu');?>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/lottery.js');?>"></script>
<div class="article">
	<h2 class="tit">我的消息</h2>
	<?php if($listInfo): ?>
		<div class="message-cont">		
			<ul class="message-list">
				<?php foreach ($listInfo as $key => $listInfo): ?>
				<li>
					<div class="pm-out <?php if(($listInfo['if_see'] == 0) && ($listInfo['if_reply'] == 1)): ?>new<?php endif; ?>">
						<i class="dot"></i>
						<?php if($listInfo['abstract']): ?>
						<p class="txt fold_<?php echo $listInfo['id']; ?>" id="" data="<?php echo $listInfo['id']; ?>"><?php echo $listInfo['s_content'].'... '; ?><a href="javascript:void(0);" class="fold">展开</a></p>
						<p class="txt unfold_<?php echo $listInfo['id']; ?>" id="" data="<?php echo $listInfo['id']; ?>" style="display:none;"><?php echo $listInfo['l_content']; ?><a href="javascript:void(0);" class="unfold">收起</a></p>
						<?php else: ?>
						<p class="txt"><?php echo $listInfo['content']; ?></p>
						<?php endif; ?>
						<span class="time"><?php echo $listInfo['created']; ?></span>
					</div>
					<?php if($listInfo['replyInfo']): ?>				
						<div class="pm-reply">
							<div class="arrow-top"> <i>◆</i> <span>◆</span></div>					
							<?php foreach ($listInfo['replyInfo'] as $k => $replyInfo): ?>
							<div class="item">
								<p class="txt"><span>小编回复：</span> <?php echo $replyInfo['content']; ?></p>
								<span class="time"><?php echo $replyInfo['created']; ?></span>
							</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<!-- pagination -->
		<?php if($pageNumber > 1){echo $pagestr;} ?>
		<!-- pagination end -->
	<?php else:?>
		暂无消息，<a class="feedBack" target="_self" href="javascript:void(0);">提个建议</a>
	<?php endif; ?>
</div>
<script>
	$('.fold').click(function(){
		var tag = $(this).parent().attr('data');
		$('.fold_'+tag).hide();
		$('.unfold_'+tag).show();
	});
	$('.unfold').click(function(){
		var tag = $(this).parent().attr('data');
		$('.unfold_'+tag).hide();
		$('.fold_'+tag).show();
	});
</script>
<?php $this->load->view('elements/user/menu_tail');?>