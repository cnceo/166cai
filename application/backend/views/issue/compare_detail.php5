<!-- 开奖详情 start -->
<div class="pop-dialog" id="idetail">
  <div class="pop-in">
    <div class="pop-head">
      <h2><?php echo $name;?><?php echo $issue;?>期开奖号码</h2>
      <span class="pop-close" title="关闭">关闭</span>
    </div>
    <div class="pop-body">
      <div class="data-table-filter del-percent">
        <table>
          <colgroup>
            <col width="70">
                    <col width="250">
          </colgroup>
          <thead>
            <tr>
              <th>来源</th>
              <th>开奖号码</th>
            </tr>
          </thead>
          <tbody>
            <?php if($detail):?>
              <?php foreach ($detail as $list): ?>
              <tr>
                <td><?php echo $list['lname'];?></td>
                <td><?php echo $list['awardNum'];?></td>
              </tr>
              <?php endforeach;?> 
            <?php endif;?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>