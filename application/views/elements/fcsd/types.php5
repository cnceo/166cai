<div class="userLboxItems">
    <ul>
      <?php foreach ($typeMAP as $key => $value): ?>
      <li <?php if ($key == $fcsdType): ?>class="selected"<?php endif; ?> >
        <a href="<?php echo $baseUrl; ?>fcsd/<?php echo $key; ?>"><?php echo $value['cnName']; ?></a>
      </li>
      <?php endforeach; ?>
    </ul>
</div>
