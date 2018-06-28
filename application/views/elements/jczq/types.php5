<div class="userLboxItems">
    <ul>
      <?php foreach ($typeMAP as $key => $value): ?>
      <li <?php if ($key == $jczqType): ?>class="selected"<?php endif; ?> >
        <a href="<?php echo $baseUrl; ?>jczq/<?php echo $key; ?>">
            <p><?php echo $value['cnName']; ?></p>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
</div>