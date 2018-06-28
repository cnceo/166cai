<?php
$this->load->view("templates/head");
$platforms = array(
    '1' => '网页',
    '2' => 'Android',
    '3' => 'IOS',
    '4' => 'M版'
);
?>
<div class="path">您的位置：报表管理&nbsp;>&nbsp;<a href="">用户管理</a></div>
<div class="mod-tab-hd mt20">
	<ul>
    	<li class="current"><a href="javascript:;">用户管理</a></li>
    	<li><a href="/backend/User/ip">IP冻结</a></li>
	</ul>
</div>
<div class="data-table-filter mt10" style="width:1100px">
   <form action="/backend/User/" method="get"  id="search_form">
    <table style = "width : 1160px">
        <colgroup>
            <col width="62" />
            <col width="230" />
            <col width="62" />
            <col width="390" />
            <col width="62" />
            <col width="100" />
            <col width="62" />
            <col width="140" />
        </colgroup>
        <tbody>
            <tr>
                <th>用户信息：</th>
                <td>
                    <input type="text" class="ipt w150" name='name' value="<?php echo $search['name'] ?>" placeholder="用户名/真实姓名/手机/邮箱" />
                    <label for="fuzzyQuery"><input id="fuzzyQuery"  type="checkbox" class="ckbox"  name='islike'   value="1" <?php if($search['islike'] == 1): echo "checked"; endif;    ?>/>模糊查询</label>
                </td>
                <th>注册时间：</th>
                <td>
                    <span class="ipt ipt-date w184"><input type="text" name='start_r_t' value="<?php echo $search['start_r_t'] ?>" class="Wdate1" /><i></i></span>
                    <span class="ml8 mr8">至</span>
                    <span class="ipt ipt-date w184"><input type="text" name='end_r_t' value="<?php echo $search['end_r_t'] ?>" class="Wdate1"/><i></i></span>
                </td>
                <th class = "tar">注册平台：</th>
                <td>
                    <select class="selectList w98" id="platformId" name="platform">
                        <option value="">不限</option>
                        <?php foreach ($platforms as $key => $val):?>
                            <option value="<?php echo $key;?>"
                                <?php if($key == ($search['platform'] + 1) ): echo "selected"; endif;?>><?php echo $val;?></option>
                        <?php endforeach;?>
                    </select>
                </td>
                <th class="tar">注册渠道：</th>
		      	<td>
			        <select class="selectList w130" name="registerChannel">
			      		<option value="">不限</option>
			      		<?php foreach ($channels as $val):?>
			      		<option value="<?php echo $val['id'];?>" <?php if($search['registerChannel'] == $val['id']): echo "selected"; endif;?>><?php echo $val['name'];?></option>
			      		<?php endforeach;?>
			      	</select>
		      	</td>
            </tr>
            <tr>
            <th>访问次数：</th>
            <td>
                <input type="text" class="ipt w98" value='<?php echo $search['start_v_t'] ?>' name="start_v_t"/>
                <span class="ml8 mr8">至</span>
                <input type="text" class="ipt w98" value='<?php echo $search['end_v_t'] ?>' name="end_v_t"/>
            </td>
                <th>登录时间：</th>
                <td>
                    <span class="ipt ipt-date w184"><input type="text" name='start_l_t' value="<?php echo $search['start_l_t'] ?>" class="Wdate1" /><i></i></span>
                    <span class="ml8 mr8">至</span>
                    <span class="ipt ipt-date w184"><input type="text" name='end_l_t' value="<?php echo $search['end_l_t'] ?>"  class="Wdate1" /><i></i></span>
                </td>
                <td colspan="4">
                    <label for="identityCard"><input id="identityCard" name="is_id_bind" value='1' type="checkbox" class="ckbox"  <?php if($search['is_id_bind'] == 1): echo "checked"; endif;    ?> />身份证绑定</label>
                    <label for="bankCard" class="ml20"><input id="bankCard" name="is_bankcard_bind" type="checkbox" class="ckbox" value='1' <?php if($search['is_bankcard_bind'] == 1): echo "checked"; endif;    ?> />银行卡绑定</label><br>
                    <label for="phone" ><input id="phone" name="is_phone_bind" type="checkbox" class="ckbox" value='1' <?php if($search['is_phone_bind'] == 1): echo "checked"; endif;    ?> />手机号绑定</label>
                    <label for="email" class="ml20"><input id="email" name="is_email_bind" type="checkbox" class="ckbox" value='1' <?php if($search['is_email_bind'] == 1): echo "checked"; endif;    ?> />邮箱绑定</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="javascript:void(0);" class="btn-blue ml35" onclick="$('#search_form').submit();">查询</a>
                </td>
            </tr>
            <tr>
                <th class="tar">用户状态：</th>
                <td>
                    <select class="selectList w130" name="userLockStatus">
                        <option value="0" <?php if($search['userLockStatus'] == '0'){ echo "selected"; }?>>不限</option>
                        <option value="1" <?php if($search['userLockStatus'] == '1'){ echo "selected"; }?>>正常</option>
                        <option value="2" <?php if($search['userLockStatus'] == '2'){ echo "selected"; }?>>注销</option>
                        <option value="3" <?php if($search['userLockStatus'] == '3'){ echo "selected"; }?>>冻结</option>
                    </select>
                </td>
                <th class="tar">注册方式：</th>
                <td colspan="4">
                    <select class="selectList w130" name="reg_type">
                        <option value="0" <?php if(empty($search['reg_type']) || $search['reg_type'] == '0'){ echo "selected"; }?>>不限</option>
                        <option value="1" <?php if($search['reg_type'] == '1'){ echo "selected"; }?>>账号密码</option>
                        <option value="3" <?php if($search['reg_type'] == '3'){ echo "selected"; }?>>微信</option>
                        <option value="4" <?php if($search['reg_type'] == '4'){ echo "selected"; }?>>短信验证码</option>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>
   </form>
</div>

<div class="data-table-list mt20">
    <table>
        <colgroup>
            <col width="98" />
            <col width="98" />
            <col width="98" />
            <col width="130" />
            <col width="70" />
            <col width="70" />
            <col width="120" />
            <col width="96" />
            <col width="120" />
            <col width="80" />
            <col width="80" />
        </colgroup>
        <tbody>
            <tr>
                <th>用户名</th>
                <th>真实姓名</th>
                <th>手机号码</th>
                <th>邮箱地址</th>
                <th>身份证绑定</th>
                <th>银行卡绑定</th>
                <th>注册时间</th>
                <th>注册方式</th>
                <th>最后登录时间</th>
                <th>访问次数</th>
                <th>注册平台</th>
                <th>注册渠道</th>
            </tr>
            <?php foreach ($users as $key => $user): ?>
            <tr>
                <td><a href="/backend/User/user_manage/?uid=<?php echo $user['uid'] ?>" class="cBlue" target="_blank"><?php echo $user['uname']; ?></a></td>
                <td><?php echo $user['real_name']; ?></td>
                <td><?php echo $user['phone']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td><span class="<?php echo $user['id_card'] != '' ? "cBlue" : "cRed"?>"><?php echo $user['id_card'] != '' ? '已绑定' : '未绑定' ?></span></td>
                <td><span class="<?php echo $user['bank_id'] != '' ? "cBlue" : "cRed"?>"><?php echo $user['bank_id'] != '' ? '已绑定' : '未绑定' ?></span></td>
                <td><?php echo $user['created']; ?></td>
                <td><?php echo ($user['reg_type'] <= 2) ? '账号密码' : ($user['reg_type'] == 3 ? '微信' : '短信验证码'); ?></td>
                <td><?php echo $user['last_login_time']; ?></td>
                <td><?php echo $user['visit_times']; ?></td>
                <td><?php echo ($user['platform'] == 0) ? "网页" : ($user['platform'] == 1 ? "Android" : ($user['platform'] == 2 ? "IOS" : "M版")); ?></td>
                <td><?php echo $channels[$user['channel']]['name'];?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="11">
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
<div class="page mt10">
   <?php echo $pages[0] ?>
</div>
<script  src="/source/date/WdatePicker.js"></script>
<script>
    $(function(){
        $(".Wdate1").focus(function(){
            dataPicker();
        });

    });

</script>
</body>
</html>
