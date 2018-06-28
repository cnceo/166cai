<div class="userLboxItems">
    <ul>
      <?php foreach ($typeMAP as $key => $value): ?>
      <li <?php if ($key == $syxwType): ?>class="selected"<?php endif; ?> style="width:80px;">
        <a href="<?php echo $baseUrl; ?>syxw/<?php echo $key; ?>"><?php echo $value['cnName']; ?></a>
      </li>
      <?php endforeach; ?>
    </ul>
</div>
