<table class="table-kj">
          <thead>
            <tr>
              <th>期次</th>
              <th>开奖号码</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($data as $kj)
          	{
          	$awardArr = explode("|", $kj['awardNum'])?>
          	<tr>
              <td><?php echo $kj['issue']?>期</td>
              <td>
              <div class="num-group">
              	<?php foreach (explode(',', $awardArr[0]) as $award){?><span><?php echo $award?></span><?php }?><?php foreach (explode(',', $awardArr[1]) as $award){?><span class="num-blue"><?php echo $award?></span><?php }?>
              </div>
              </td>
            </tr>
          <?php }?>
          </tbody>
        </table>