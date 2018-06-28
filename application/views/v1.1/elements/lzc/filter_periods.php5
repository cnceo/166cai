<div class="filter-periods">
    <ul>
        <?php foreach ($issues as $issue): ?>
            <?php if ($issue['seExpect'] >= $prevIssue['seExpect']): ?>
                <li>
                    <input type="radio" id="filterPeriods<?php echo $issue['seExpect'] ?>" name="filterPeriods"
                           class="filterPeriods" data-issue="<?php echo $issue['seExpect'] ?>"
                        <?php echo $issueId == $issue['seExpect'] ? 'checked="checked"' : ''; ?>
                    >
                    <label for="filterPeriods<?php echo $issue['seExpect'] ?>">
                        <?php if ($issue['seFsendtime'] < time() * 1000): ?>
                            上一期赛果
                        <?php elseif ($issue['sale_time'] <= time() * 1000 && $issue['seFsendtime'] >= time() * 1000): ?>
                            <?php if ($issue['seExpect'] == $minCurrentId): ?>
                                <?php echo $issue['seExpect'] ?>期（当前期）
                            <?php else: ?>
                                <?php echo $issue['seExpect'] ?>期（预售期）
                            <?php endif; ?>
                        <?php else: ?>
                            <?php echo $issue['seExpect'] ?>期<s>（预售期）</s>
                        <?php endif; ?>
                    </label>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>