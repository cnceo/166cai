<div class="radio-wrapper">
    <?php foreach($items as $key => $item): ?>
    <span class="cx-radio <?php if ($key == $selected) echo 'selected'; ?>" data-key="<?php echo $key; ?>"><?php echo $item; ?></span>
    <?php endforeach; ?>
</div>
