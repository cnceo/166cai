<div class="userLotteryTab">
    <?php if ($lotteryId != JCZQ && $lotteryId != JCLQ): ?>
    <a class="first <?php if ($type == 'cast') echo 'selected'; ?>" href="<?php echo $baseUrl . $enName; ?>" rel="nofollow">普通投注</a>
    <?php endif; ?>

    <?php if ($lotteryId == JCZQ): ?>

      <?php foreach ($typeMAP as $key => $value): ?>
        <a href="<?php echo $baseUrl; ?>jczq/<?php echo $key; ?>"
          <?php if (($key == 'hh') and ($key == $jczqType)): ?> class="first selected"
          <?php elseif ($key == 'hh'): ?> class= "first"
          <?php elseif ($key == $jczqType): ?> class= "selected"
          <?php endif; ?>><?php echo $value['cnName']; ?></a>
      <?php endforeach; ?>

      <a class="<?php if ($type == 'award') echo 'selected'; ?>" href="<?php echo $baseUrl; ?>awards/jczq">赛果开奖</a>

    <?php elseif ($lotteryId == JCLQ): ?>

      <?php foreach ($typeMAP as $key => $value): ?>
        <a href="<?php echo $baseUrl; ?>jclq/<?php echo $key; ?>"
          <?php if (($key == 'hh') and ($key == $jclqType)): ?> class="first selected"
          <?php elseif ($key == 'hh'): ?> class= "first"
          <?php elseif ($key == $jclqType): ?> class= "selected"
          <?php endif; ?>><?php echo $value['cnName']; ?></a>
      <?php endforeach; ?>

      <a class="<?php if ($type == 'award') echo 'selected'; ?>" href="<?php echo $baseUrl; ?>awards/jclq">赛果开奖</a>
    <?php elseif ($lotteryId == SFC || $lotteryId == RJ) : ?>
      <!-- <a class="<?php if ($type == 'award') echo 'selected'; ?>" href="<?php echo $baseUrl; ?>awards/<?php echo $enName; ?>">历史开奖</a> -->
    <?php else: ?>
      <!-- <a class="<?php if ($type == 'award') echo 'selected'; ?>" href="<?php echo $baseUrl; ?>awards/number/<?php echo $lotteryId; ?>/1">历史开奖</a> -->
    <?php endif; ?>
</div>
