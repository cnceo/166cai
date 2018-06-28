<?php $this->load->view("templates/head") ?>
<div class="frame-container" style="margin-left:0;">
    <div class="path">您的位置：数据中心&nbsp;&gt;&nbsp;<a href="/backend/Issue/management">期次管理</a>&nbsp;&gt;&nbsp;开奖详情修改</div>
    <div class="kj-dtail-fix mt10">
  <h2 class="kj-dtail-fix-title">
    <em><?php echo $name;?></em>第<?php echo $issue;?>期开奖详情
  </h2>
  <div class="data-table-filter" style=" width: 100%;">
    <table>
      <tbody>
      <tr>
        <td>
          开奖号码:<span><?php echo $awardNum;?></span>
        </td>
        <td style="text-align:right;">
          <a href="/backend/Issue/modifyIssueDetail?lid=<?php echo $lid;?>&issue=<?php echo $issue;?>" class="btn-blue-h32">修改</a>
        </td>
      </tr>
      <tr>
        <?php if($lid == 'sfc'):?>
        <td>
          任九销售:<span><?php $rj_sale = $rj_sale?number_format($rj_sale):'--'; echo $rj_sale;?></span>元
        </td>
        <td>
          胜负彩销售:<span><?php $sfc_sale = $sfc_sale?number_format($sfc_sale):'--'; echo $sfc_sale;?></span>元
        </td>
        <?php else:?>
        <td>
          全国销售:<span><?php $sale = $sale?number_format($sale):'--'; echo $sale;?></span>元
        </td>
      <?php endif;?>
        <td>
          奖池滚存:<span><?php $pool = $pool>=0?number_format($pool):'--'; echo $pool;?></span>元
        </td>
      </tr>
      </tbody>
    </table>
  </div>
  <?php $page = '/issue/display_'.$lid; $this->load->view($page); ?>
</div>
</div>
</body>
</html>
