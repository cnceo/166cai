<?php if(!empty($redpackData)):?>
<!-- 充值红包 start -->
<div class="form-item form-chooseRp">
    <label class="form-item-label">选择红包：</label>
    <div class="form-item-con">
        <div class="hongbao-s" id="redpackInfo">
            <ul>
            <?php foreach( $redpackData as $key => $items ): ?>
                <li  redpack-data="<?php $params = json_decode($items['use_params'], true); echo $items['id'] . '#' . ParseUnit($params['money_bar'], 1);?>" class="redpack<?php echo ParseUnit($params['money_bar'], 1);?> <?php echo ParseUnit($params['money_bar'], 1) ==20 ? 'selected':'' ;?>" id="redpackId-<?php echo $items['id']; ?>">
                    <?php echo ParseDesc($items['use_desc']);?><span><?php echo ParseEnd($items['valid_end']);?></span>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<!-- 充值红包 end -->
<?php endif; ?>
<div class="form-item">
    <a class="btn btn-main submit<?php echo $showBind ? ' not-bind': '';?>" href="javascript:;">确认预约</a>
</div>