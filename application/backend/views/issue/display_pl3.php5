<div class="data-table-list mt10">
    <table>
      <colgroup>
        <col width="20%">
        <col width="40%">
        <col width="40%">
      </colgroup>
      <thead>
        <tr>
          <th>奖级</th>
          <th>注数</th>
          <th>奖金（元）</th>
        </tr>
      </thead>
      <tbody>
        <tr>
         <td>直选</td>
         <td><?php echo $bonusDetail['zx']['zs'];?></td>
         <td><?php echo $bonusDetail['zx']['dzjj'];?></td>
        </tr>
        <tr>
         <td>组三</td>
         <td><?php echo $bonusDetail['z3']['zs'];?></td>
         <td><?php echo $bonusDetail['z3']['dzjj'];?></td>
        </tr>
        <tr>
         <td>组六</td>
         <td><?php echo $bonusDetail['z6']['zs'];?></td>
         <td><?php echo $bonusDetail['z6']['dzjj'];?></td>
        </tr>
    </tbody>
    </table>
</div>

