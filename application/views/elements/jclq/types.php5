<div class="userLboxItems">
    <ul>
      <?php foreach ($typeMAP as $key => $value): ?>
      <li <?php if ($key == $jclqType): ?>class="selected"<?php endif; ?> >
        <a href="<?php echo $baseUrl; ?>jclq/<?php echo $key; ?>">
            <p><?php echo $value['cnName']; ?></p>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
</div>