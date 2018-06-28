<?php 
	$platforms = array(
		'0' => '网页',
		'1' => 'Android',
		'2' => 'IOS',
	);
?>
<div class="data-table-list wbase mt20">
     <table>
       <colgroup>
         <col width="150" />
         <col width="150" />
         <col width="118" />
         <col width="333" />
         <col width="110" />
         <col width="110" />
         <col width="110" />
         <col width="110" />
         <col width="110" />
         <col width="110" />
         <col width="110" />
       </colgroup>
       <tbody>
       <tr>
         <th>登录时间</th>
         <th>登录IP</th>
         <th>所在地区</th>
         <th>登录来源</th>
         <th>登录方式</th>
         <th>登录渠道</th>
         <th>访问时长</th>
         <th>手机型号</th>
         <th>系统版本</th>
         <th>软件版本</th>
         <th>设备号</th>
       </tr>
       <?php foreach($login_infos as $key => $login_info): ?>
       <tr>
         <td><?php echo $login_info['login_time']; ?></td>
         <td><?php echo $login_info['ip'] ?></td>
         <td><?php echo $login_info['area'] ?></td>
         <td><?php if($login_info['platform'] === '0' || $login_info['platform'] === '3'){ echo $login_info['reffer'];}else{ echo $platforms[$login_info['platform']];} ?></td>
         <td><?php echo $login_info['login_type'] ? (($login_info['login_type'] == 1) ? '微信登录' : '短信验证码') : '账号密码'; ?></td>
         <td><?php echo $login_info['name'] ?></td>
         <td><?php echo $login_info['duration'] ?></td>
         <td><?php echo $login_info['model'] ?></td>
         <td><?php echo $login_info['system'] ?></td>
         <td><?php echo $login_info['version'] ?></td>
         <td><?php echo $login_info['idfa'] ?></td>
       </tr>
   </tbody>
      <?php endforeach; ?>
      <tfoot>
      <tr>
          <td colspan="5">
              <div class="stat">
                  <span>本页&nbsp;<?php echo $pages[2] ?>&nbsp;条</span>
                  <span class="ml20">共&nbsp;<?php echo $pages[1] ?>&nbsp;页</span>
                  <span class="ml20">总计&nbsp;<?php echo $pages[3] ?>&nbsp;</span>
              </div>
          </td>
      </tr>
  </tfoot>
     </table>
   </div>
   <div class="page mt10 login_info">
       <?php echo $pages[0] ?>
   </div>
   <script>
   $('.login_info a').click(function(){
       var _this = $(this);
       $("#login_info").load(_this.attr("href"));
       return false;
    });
   </script>