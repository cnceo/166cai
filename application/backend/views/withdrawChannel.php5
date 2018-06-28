<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：审核管理&nbsp;>&nbsp;<a href="">提款通道配置</a></div>
<div class="data-table-filter mt10" style="width:1080px">
    <form action="/backend/Transactions/withdrawChannel" method="get" id="search_form">
  <table>
    <colgroup>
      <col width="62" />
      <col width="150" />
      <col width="62" />
      <col width="420" />
      <col width="62" />
      <col width="320" />
    </colgroup>
    <tbody>
    <tr>
      <th>当前提款通道：</th>
      <td>
          <?php if($channel=='tonglian'): echo "通联代付"; endif; ?>
          <?php if($channel=='lianlian'): echo "连连代付"; endif; ?>
          <?php if($channel=='xianfeng'): echo "连连代付"; endif; ?>
      </td>
    </tr>
    <tr>
      <th>提款通道修改：</th>
      <td>
          <label for="chanel1" class="mr10"><input type="radio" class="radio" name="channel" value="tonglian" <?php if($channel=='tonglian'): echo "checked"; endif; ?>>通联代付</label>
          <label for="chanel2" class="mr10"><input type="radio" class="radio" name="channel" value="lianlian" <?php if($channel=='lianlian'): echo "checked"; endif; ?>>连连代付</label>
          <label for="chanel3" class="mr10"><input type="radio" class="radio" name="channel" value="xianfeng" <?php if($channel=='xianfeng'): echo "checked"; endif; ?>>先锋代付</label>
      </td>
    </tr>
    <tr>
      <th>当前提现：</th>
      <td>
          <?php if($audit=='1'): echo "需要人工审核"; endif; ?>
          <?php if($audit=='0'): echo "不需要人工审核"; endif; ?>
      </td>
    </tr>
    <tr>
      <th>当前提现：</th>
      <td>
          <label for="audit1" class="mr10"><input type="radio" class="radio" name="audit" value="1" <?php if($audit=='1'): echo "checked"; endif; ?>>需要人工审核</label>
          <label for="audit2" class="mr10"><input type="radio" class="radio" name="audit" value="0" <?php if($audit=='0'): echo "checked"; endif; ?>>不需要人工审核</label>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <a href="javascript:void(0);" class="btn-blue " onclick="$('#search_form').submit();">保存</a>
      </td>
    </tr>
    </tbody>
  </table>
    </form>
</div>
</body>
</html>