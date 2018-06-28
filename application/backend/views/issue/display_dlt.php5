<div class="data-table-list mt10">
    <table>
      <colgroup>
        <col width="10%">
        <col width="10%">
        <col width="40%">
        <col width="40%">
      </colgroup>
      <thead>
        <tr>
          <th colspan="2">奖级</th>
          <th>注数</th>
          <th>奖金</th>
        </tr>
      </thead>
      <tbody>
        <tr>
         <td rowspan="2">一等奖</td>
         <td>基本</td>
         <td><?php echo $bonusDetail['1dj']['jb']['zs'];?></td>
         <td><?php echo $bonusDetail['1dj']['jb']['dzjj'];?></td>
        </tr>
        <tr>
         <td>追加</td>
         <td><?php echo $bonusDetail['1dj']['zj']['zs'];?></td>
         <td><?php echo $bonusDetail['1dj']['zj']['dzjj'];?></td>
        </tr>
        <tr>
         <td rowspan="2">二等奖</td>
         <td>基本</td>
         <td><?php echo $bonusDetail['2dj']['jb']['zs'];?></td>
         <td><?php echo $bonusDetail['2dj']['jb']['dzjj'];?></td>
        </tr>
        <tr>
         <td>追加</td>
         <td><?php echo $bonusDetail['2dj']['zj']['zs'];?></td>
         <td><?php echo $bonusDetail['2dj']['zj']['dzjj'];?></td>
        </tr>
        <tr>
         <td rowspan="2">三等奖</td>
         <td>基本</td>
         <td><?php echo $bonusDetail['3dj']['jb']['zs'];?></td>
         <td><?php echo $bonusDetail['3dj']['jb']['dzjj'];?></td>
        </tr>
        <tr>
         <td>追加</td>
         <td><?php echo $bonusDetail['3dj']['zj']['zs'];?></td>
         <td><?php echo $bonusDetail['3dj']['zj']['dzjj'];?></td>
        </tr>
        <tr>
         <td rowspan="2">四等奖</td>
         <td>基本</td>
         <td><?php echo $bonusDetail['4dj']['jb']['zs'];?></td>
         <td><?php echo $bonusDetail['4dj']['jb']['dzjj'];?></td>
        </tr>
        <tr>
         <td>追加</td>
         <td><?php echo $bonusDetail['4dj']['zj']['zs'];?></td>
         <td><?php echo $bonusDetail['4dj']['zj']['dzjj'];?></td>
        </tr>
        <tr>
         <td rowspan="2">五等奖</td>
         <td>基本</td>
         <td><?php echo $bonusDetail['5dj']['jb']['zs'];?></td>
         <td><?php echo $bonusDetail['5dj']['jb']['dzjj'];?></td>
        </tr>
        <tr>
         <td>追加</td>
         <td><?php echo $bonusDetail['5dj']['zj']['zs'];?></td>
         <td><?php echo $bonusDetail['5dj']['zj']['dzjj'];?></td>
        </tr>
        <?php  if($bonusDetail['6dj']['zj']['zs'] || $bonusDetail['6dj']['zj']['dzjj']):?>
        <tr>
         <td rowspan="2">六等奖</td>
         <td>基本</td>
         <td><?php echo $bonusDetail['6dj']['jb']['zs'];?></td>
         <td><?php echo $bonusDetail['6dj']['jb']['dzjj'];?></td>
        </tr>
        <tr>
         <td>追加</td>
         <td><?php echo $bonusDetail['6dj']['zj']['zs'];?></td>
         <td><?php echo $bonusDetail['6dj']['zj']['dzjj'];?></td>
        </tr>
        <?php  else :?>
        <tr>
         <td colspan="2">六等奖</td>
         <td><?php  echo $bonusDetail['6dj']['jb']['zs'];?></td>
         <td><?php  echo $bonusDetail['6dj']['jb']['dzjj'];?></td>
        </tr>
        <?php endif;?>
      </tbody>
    </table>
</div>

