<div class="userLboxItems">
    <ul>
      <?php foreach ($typeMAP as $key => $value): ?>
      <li <?php if ($key == $plsType): ?>class="selected"<?php endif; ?> >
        <a href="<?php echo $baseUrl; ?>pls/<?php echo $key; ?>"><?php echo $value['cnName']; ?></a>
      </li>
      <?php endforeach; ?>
    </ul>
</div>
