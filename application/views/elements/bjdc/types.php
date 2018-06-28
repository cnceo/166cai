<div class="userLboxItems">
    <ul>
      <?php foreach ($typeMAP as $key => $value): ?>
      <li <?php if ($key == $bjdcType): ?>class="selected"<?php endif; ?> >
        <a href="<?php echo $baseUrl; ?>bjdc/<?php echo $key; ?>"><?php echo $value['cnName']; ?></a>
      </li>
      <?php endforeach; ?>
    </ul>
</div>
