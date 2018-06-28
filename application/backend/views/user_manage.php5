<?php 
	$this->load->view("templates/head");
	$userStatus = array(
		'0' => '正常',
		'1' => '注销',
		'2' => '冻结'
	);
?>
<div class="path">您的位置：<a href="">报表系统</a>&nbsp;&gt;&nbsp;<a href="/backend/User/">用户管理</a>&nbsp;&gt;&nbsp;<a href="">详情</a></div>
<div class="data-table-brief mt10">
    <table>
        <colgroup><col width="184" /><col width="206" /><col width="286" /><col width="164" /><col width="144" /></colgroup>
        <tbody>
            <tr>
                <td><strong>注册时间：</strong><span><?php echo $user['created'] ?></span></td>
                <td><strong>最后登录时间：</strong><span><?php echo $user['last_login_time'] ?></span></td>
                <td><strong>注册来源：</strong><span class="ipt w202"><?php echo $user['reg_reffer'] ?></span></td>
                <td><strong>访问次数：</strong><span><?php echo intval($user['visit_times']) ?></span></td>
                <td><strong>投注成功订单数：</strong><span><?php echo intval($orderInfo[0]) ?></span></td>
            </tr>
            <tr>
                <td><strong>累计充值金额：</strong><span><?php echo m_format($transInfo[0]) ?> 元</span></td>
                <td><strong>累计投注金额：</strong><span><?php echo m_format($orderInfo[1]) ?> 元</span></td>
                <td><strong>累计中奖金额：</strong><span><?php echo m_format($orderInfo[2]) ?> 元</span></td>
                <td><strong>累计提款金额：</strong><span><?php echo m_format($transInfo[1]) ?> 元</span></td>
                <td><strong>当前可用余额：</strong><span><?php echo m_format($user['money']) ?> 元</span></td>
            </tr>
            <tr>
                <td colspan="1"><strong>用户状态：</strong><span><?php echo $userStatus[$user['userStatus']]; ?></span></td>
                <td><strong>可提现金额：</strong><span><?php echo m_format($withdraw) ?> 元</span></td>
                <td><span>注：累计投注、中奖未统计合买</span></td>
            </tr>   
        </tbody>
    </table>
</div>

<div class="tab-nav mt20">
    <ul class="clearfix">
        <li id="li_1" class="active load_info"><a onClick="javascript:stab('user_detail','User/user_detail');"><span>基本信息</span></a></li>
        <li id="li_2" class="load_info"><a onClick="javascript:stab('login_info','User/login_info')"><span>登录信息</span></a></li>
        <li id="li_3"><a onClick="javascript:stab('order_info','Management/manageOrder')"><span>订单记录</span></a></li>
        <li id="li_4"><a onClick="javascript:stab('united_order','Management/manageUnited')"><span>合买记录</span></a></li>
        <li id="li_5"><a onClick="javascript:stab('follow_order','Unitedfollow/index')"><span>跟单记录</span></a></li>
        <li id="li_6"><a onClick="javascript:stab('chase_info','Management/chaseManage')"><span>追号记录</span></a></li>
        <li id="li_7"><a onClick="javascript:stab('transactions_info','Transactions/index')"><span>交易明细</span></a></li>
        <li id="li_8"><a onClick="javascript:stab('redpack_info','Activity/ajaxListRedpack')"><span>红包明细</span></a></li>
        <li id="li_9"><a onClick="javascript:stab('jifen_info','Growth/ajaxPointDetail')"><span>积分明细</span></a></li>
        <li id="li_10"><a onClick="javascript:stab('level_info','Activity/ajaxListRedpack')"><span>等级中心</span></a></li>
        <li id="li_11"><a onClick="javascript:stab('united_follow','User/unitedFollow')"><span>合买关注</span></a></li>
    </ul>
</div>
<div class="tab-content">
    <!-- 基本信息 -->
    <div class="item" style="display:block;" id="user_detail" has_load='true'>
        <div class="data-table-log bd-width-item" style="width: 1100px">
            <table>
                <colgroup><col width="92" /><col width="200" /><col width="92" /><col width="199" /><col width="92" /><col width="199" /><col width="92" /><col width="80" /><col /></colgroup>
                <tbody>
                    <?php $level = array(1=>'新手',2=>'青铜',3=>'白银',4=>'黄金',5=>'铂金',6=>'钻石');?>
                    <tr class="title"><th colspan="9">基本资料</th></tr>
                    <tr><th>用户名：</th><td><?php echo $user['uname'] ?>(<?php echo isset($growth['grade']) ? $level[$growth['grade']].'彩民' :$level[1].'彩民'; ?>)<?php if ($user['nick_name_modify_time'] !== '0000-00-00 00:00:00') {?><a href="javascript:;" onClick="resetuname(<?php echo $user['uid']?>)">重置次数</a><?php }?></td><!-- <th>用户昵称：</th><td><?php echo $user['nick_name'] ?></td> --><th>注册方式：</th><td colspan="2"><?php echo $user['reg_type'] ?></td></tr>
                    <tr>
                        <th>性别：</th>
                        <td><?php if($user['gender'] == 1): echo "男"; elseif($user['gender'] == 2): echo "女";else: echo "保密";endif; ?></td>
                        <th>所在地区：</th>
                        <td><?php echo $user['province'] . " " . $user['city'] ?></td>
                        <th>注册平台：</th>
                        <td ><?php echo ($user['platform'] == 0) ? "网页" : ($user['platform'] == 1 ? "Android" : ($user['platform'] == 2 ? "IOS" : "M版")); ?></td>
                    </tr>
                    <tr>
                        <th>帐号绑定：</th>
                        <td colspan="8">
                            <label for="bindPwd"><input disabled="disabled" id="bindPwd" name="bindPwd" type="checkbox" class="ckbox"<?php if($user['pay_pwd'] != ''):  echo "checked";  endif; ?> />已设置支付密码</label>
                            <label for="bindPhone" class="ml25"><input disabled="disabled" id="bindPhone" name="bindPhone" type="checkbox" class="ckbox" <?php if($user['phone'] != ""): echo "checked"; endif;?> />已绑定手机</label>
                            <label for="bindMail" class="ml25"><input disabled="disabled" id="bindMail" name="bindMail" type="checkbox" class="ckbox" <?php if($user['email'] != ''):  echo "checked";  endif; ?>/>已绑定邮箱</label>
                            <label for="bindBank" class="ml25"><input  disabled="disabled" id="bindBank" name="bindBank" type="checkbox" class="ckbox" <?php if(!empty($bankInfo)): echo "checked"; endif;?>/>已绑定银行卡</label>
                        </td>
                    </tr>
                    <?php $phone_send = (isset($user['msg_send']) && ($user['msg_send'] & 1)) ? true : false; $email_send = (isset($user['msg_send']) && ($user['msg_send'] & 2)) ? true : false; $win_prize = (!isset($user['msg_send']) || ($user['msg_send'] & 4) == 0) ? true : false;
                     $chase_prize = (!isset($user['msg_send']) || ($user['msg_send'] & 8) == 0) ? true : false; $gendan_prize = (!isset($user['msg_send']) || ($user['msg_send'] & 16) == 0) ? true : false;?>
                    <tr>
                        <th>出票成功短信：</th>
                        <td>
                        	已<?php echo empty($phone_send) ? '关闭' : '开启'?>
                        	<a href="javascript:;" class="cBlue msg" data-id="phone" data-modify='<?php echo empty($phone_send) ? '1' : '0'?>'><?php echo empty($phone_send) ? '开启' : '关闭'?></a>
                        </td>
                        <th>出票成功邮件：</th>
                        <td>
                        	已<?php echo empty($email_send) ? '关闭' : '开启'?>
                        	<a href="javascript:;" class="cBlue msg" data-id="email" data-modify='<?php echo empty($email_send) ? '1' : '0'?>'><?php echo empty($email_send) ? '开启' : '关闭'?></a>
                        </td>
                        <th>中奖短信：</th>
                        <td>
                            已<?php echo empty($win_prize) ? '关闭' : '开启'?>
                            <a href="javascript:;" class="cBlue msg" data-id="win_prize" data-modify='<?php echo empty($win_prize) ? '1' : '0'?>'><?php echo empty($win_prize) ? '开启' : '关闭'?></a>
                        </td>
                    </tr>
                    <tr>
                        <th>追号短信：</th>
                        <td>
                            已<?php echo empty($chase_prize) ? '关闭' : '开启'?>
                            <a href="javascript:;" class="cBlue msg" data-id="chase_prize" data-modify='<?php echo empty($chase_prize) ? '1' : '0'?>'><?php echo empty($chase_prize) ? '开启' : '关闭'?></a>
                        </td>
                        <th>定制跟单短信：</th>
                        <td>
                            已<?php echo empty($gendan_prize) ? '关闭' : '开启'?>
                            <a href="javascript:;" class="cBlue msg" data-id="gendan_prize" data-modify='<?php echo empty($gendan_prize) ? '1' : '0'?>'><?php echo empty($gendan_prize) ? '开启' : '关闭'?></a>
                        </td>
                    </tr>
                    <tr>
                        <th>当前可用余额：</th><td><?php echo m_format($user['money'])?></td><td><a href="javascript:;" class="cBlue" id="adjust_umoney">手动调款</a></td><th>可用积分：</th><td><?php echo $growth['points'];?></td>
                    </tr>
                    <tr class="hr"><td colspan="9"><div class="hr-dashed"></div></td></tr>
                    <tr class="title"><th colspan="9">联系方式</th></tr>
                    <tr>
                        <th>手机号码：</th>
                        <td><?php echo $user['phone'] ?><a href="javascript:;" class="ml20 cBlue" id="modifyPhone">修改</a></td>
                        <th>邮箱地址：</th>
                        <td><?php echo $user['email'] ?><a href="javascript:;" class="ml20 cBlue" id="modifyEmail">修改</a></td>
                        <th>绑定邮箱时间：</th>
                        <td><?php echo $user['bind_email_time'] ?></td>
                        <th>QQ号：</th>
                        <td><?php echo $user['qq'] ?></td>
                    </tr>
                    <tr class="hr"><td colspan="9"><div class="hr-dashed"></div></td></tr>
                    <tr class="title"><th colspan="9">实名信息</th></tr>
                    <tr>
                        <th>真实姓名：</th><td><?php echo $user['real_name'] ?><?php if(!empty($user['real_name'])): ?><a href="javascript:;" class="ml20 cBlue" id="modifyRealName">修改</a><?php endif; ?></td>
                        <th>身份证号：</th><td><?php if($user['id_card']!=''): echo $user['id_card']; endif;?><?php if(!empty($user['id_card'])): ?><a href="javascript:;" class="ml20 cBlue" id="modifyIdCard">修改</a><?php endif; ?></td>
                        <th>实名时间：</th>
                        <td colspan="2"><?php echo $user['bind_id_card_time'] ?></td>
                    </tr>
                    <tr class="hr"><td colspan="9"><div class="hr-dashed"></div></td></tr>
                    <tr class="title"><th colspan="9">开户信息</th></tr>
                    
                    <tr>
                        <td colspan="9">
                            <table>
                                <colgroup>
                                    <col width="90">
                                    <col width="140">
                                    <col width="90">
                                    <col width="140">
                                    <col width="90">
                                    <col width="140">
                                    <col width="190">
                                </colgroup>
                                <tbody>
                                    <?php foreach ($bankInfo as $bankList):?>
                                    <tr>
                                        <th><?php echo ($bankList['is_default'])?'（默认）':''; ?>开户银行：</th><td><?php echo $this->pay_cfg["chinabank"]['child'][$bankList['bank_type']][0] ?></td>
                                        <th>开户省市：</th><td><?php echo $bankList['bank_province'] . " " .  $bankList['bank_city']?></td>
                                        <th>银行卡号：</th><td><?php if($bankList['bank_id']!=''): echo  $bankList['bank_id']; endif;?></td>
                                        <td><a href="javascript:void(0);" class="btn-blue ml25 delBank" data-index="<?php echo $bankList['id']; ?>" data-bankid="<?php echo $bankList['bank_id']; ?>">删除</a></td>   
                                    </tr>
                                    <?php endforeach; ?> 
                                </tbody>
                            </table>    
                        </td>
                    </tr>

                    <tr class="title"><th colspan="9">充值信息</th></tr>
                    
                    <tr>
                        <td colspan="9">
                            <table>
                                <colgroup>
                                    <col width="90">
                                    <col width="140">
                                    <col width="90">
                                    <col width="140">
                                    <col width="90">
                                    <col width="140">
                                    <col width="190">
                                </colgroup>
                                <tbody>
                                    <?php if(!empty($payBankInfo)): ?>
                                    <?php foreach ($payBankInfo as $bankList):?>
                                    <?php if($bankList['delect_flag'] == 0):?>
                                    <tr>
                                        <th><?php echo ($bankList['is_default'])?'（默认）':''; ?>充值银行：</th><td><?php echo $bankList['bank_type']; ?></td>
                                        <th>银行卡号：</th><td><?php if($bankList['bank_id']!=''): echo  $bankList['bank_id']; endif;?></td>
                                        <th>签约快捷支付：</th>
                                        <td>
                                        <?php 
                                            $agree = '';
                                            $agreement = array();
                                            if(!empty($bankList['pay_agreement']))
                                            {
                                                $agreement = json_decode($bankList['pay_agreement'], true);
                                            }
                                            if($agreement)
                                            {
                                                $type = array(
                                                    'umpay' => array(
                                                        'name' => '联动支付', 
                                                        'cstate' => 1
                                                    ) 
                                                );
                                                foreach ($agreement as $key => $val) 
                                                {
                                                    if(!empty($type[$key]) && ($bankList['cstate'] & $type[$key]['cstate']) == $type[$key]['cstate'])
                                                    {
                                                        $agree .= $type[$key]['name'];
                                                    }
                                                }
                                                if(!$agree)
                                                {
                                                    $agree = '未签约';
                                                }
                                            }
                                            else
                                            {
                                                $agree = '未签约';
                                            }
                                            echo $agree;
                                        ?>
                                        </td>
                                        <td>
                                        <?php if($bankList['cstate'] > 0): ?>
                                        <a href="javascript:void(0);" class="btn-blue ml25 delPayBank" data-index="<?php echo $bankList['id']; ?>" data-bankid="<?php echo $bankList['bank_id']; ?>">解除绑定</a>
                                        <?php endif; ?>
                                        </td>   
                                    </tr>
                                    <?php endif; ?>
                                    <?php endforeach; ?> 
                                    <?php endif; ?>
                                </tbody>
                            </table>    
                        </td>
                    </tr>
                
                     <tr>
                        <td colspan="3"></td>
                        <td><a href="/backend/User/uinfo_log/<?php echo $user['uid']?>" target="_blank" style="width:130px" class="btn-blue mt20 ml25">查看信息修改记录</a></td>
                        <?php if($user['userStatus'] == '1'): ?>
                        <td></td>
                        <td><a href="javascript:void(0);" class="btn-blue mt20 ml25" style="background-color:gray;">已注销</a></td>
                        <?php else: ?>
                        <?php if($user['userStatus'] == '2'): ?>
                        <td><a href="javascript:void(0);" class="btn-blue mt20 ml25 lockUser" data-val="0">解除冻结</a></td>
                        <?php else: ?>
                        <td><a href="javascript:void(0);" class="btn-blue mt20 ml25 lockUser" data-val="2">冻结账户</a></td>
                        <?php endif;?>
                        <td><a href="javascript:void(0);" class="btn-blue mt20 ml25 lockUser" data-val="1">注销账户</a></td>
                        <?php endif; ?>
                        <!-- <td><a href="javascript:void(0);" class="btn-blue mt20 ml25" onclick="reset_pay_pwd('<?php echo $user['uid'] ?>')">重置密码</a></td> -->
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- 基本信息 end -->
    <!-- 登录信息 -->
    <div class="item" id="login_info" has_load=false></div>
    <!-- 登录信息 end -->
    <!-- 订单记录 -->
    <div class="item" id="order_info" has_load='false'></div>
    <!-- 订单记录 end -->
    <!-- 合买记录 -->
    <div class="item" id="united_order" has_load='false'></div>
    <!-- 追号记录 end  -->
    <!-- 跟单记录 -->
    <div class="item" id="follow_order" has_load='false'></div>
    <!-- 跟单记录 end  -->
    <!-- 追号记录 -->
    <div class="item" id="chase_info" has_load='false'></div>
    <!-- 追号记录 end  -->
    <!-- 资金管理 -->
    <div class="item" id="transactions_info" has_load='false'></div>
    <!-- 资金管理 end -->
    <!-- 红包明细 -->
    <div class="item" id="redpack_info" has_load='false'></div>
    <!-- 红包明细end -->
    <!-- 积分明细 -->
    <div class="item" id="jifen_info" has_load='false'></div>
    <!-- 积分明细end -->
    <div class="item" style="display:none;" id="level_info" has_load='true'>
        <?php if ($growths): ?>
        <div class="data-table-log bd-width-item" style="width: 1100px">
            <table>
                <colgroup><col width="92" /><col width="200" /><col width="92" /><col width="199" /><col width="92" /><col width="199" /><col width="92" /><col width="80" /><col /></colgroup>
                <tbody>
                    <tr class="title"><th colspan="6">用户成长</th></tr>
                    <tr>
                        <th>用户名：</th>
                        <td><?php echo $user['uname'] ?></td>
                        <th>当前等级：</th>
                        <td><span style="color: #00f;"><?php echo $level[$growth['grade']].'彩民' ?></span>(打败<span style="color: #f00;"><?php echo $growth['rank']/100 ."%";?></span>的用户)</td>
                        <th>成长周期：</th>
                        <td><?php echo $growth['cycle_start'].' 至 '.$growth['cycle_end'] ?></td>
                    </tr>
                    <tr>
                        <th>成长值：</th>
                        <td><?php echo $growth['grade_value'] ?></td>
                        <th>成长天数：</th>
                        <td><?php echo $growth['grade_days'] ?></td>
                        <th></th>
                        <td></td>
                    </tr>
                    <tr>
                        <th>上次级别：</th>
                        <td><span style="color: #00f;"><?php echo $level[$growth['grade_before']]?$level[$growth['grade_before']].'彩民':'无' ; ?></span></td>
                        <th>上次周期：</th>
                        <td>
                        <?php if (!$growth['cycle_start'] || !$growth['cycle_end']): ?>
                          无
                        <?php else: ?>
                        <?php echo date('Y-m-d H:i:s',strtotime("-1year",strtotime($growth['cycle_start']))).' 至 '.date('Y-m-d H:i:s',strtotime("-1year",strtotime($growth['cycle_end']))) ?></td>
                        <?php endif ?>
                        <th></th>
                        <td></td>
                    </tr>
                    <tr class="hr"><td colspan="6"><div class="hr-dashed"></div></td></tr>
                    <tr class="title"><th colspan="6">用户特权</th></tr>
                    <?php if ($growth['grade']==1): ?>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1.身份勋章：等级身份勋章彰显会员尊贵身份</td>
                        </tr>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2.提现特权：会员提款免手续费，每天支持提现<span style="color: #f00;">3</span>次</td>
                        </tr>
                    <?php elseif($growth['grade']==2): ?>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1.身份勋章：等级身份勋章彰显会员尊贵身份</td>
                        </tr>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2.提现特权：会员提款免手续费，每天支持提现<span style="color: #f00;">3</span>次</td>
                        </tr>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3.积分兑换：会员享受积分商城超值兑换特权</td>
                        </tr>
                    <?php elseif($growth['grade']==3): ?>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1.身份勋章：等级身份勋章彰显会员尊贵身份</td>
                        </tr>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2.提现特权：会员提款免手续费，每天支持提现<span style="color: #f00;">3</span>次</td>
                        </tr>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3.积分兑换：会员享受积分商城超值兑换特权</td>
                        </tr>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4.升级彩金：会员升级即可领取<span style="color: #f00;">16元</span>升级礼包</td>
                        </tr>
                    <?php elseif($growth['grade']==4): ?>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1.身份勋章：等级身份勋章彰显会员尊贵身份</td>
                        </tr>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2.提现特权：会员提款免手续费，每天支持提现<span style="color: #f00;">4</span>次</td>
                        </tr>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3.积分兑换：会员享受积分商城超值兑换特权</td>
                        </tr>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4.升级彩金：会员升级即可领取<span style="color: #f00;">66元</span>升级礼包</td>
                        </tr>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5.生日礼包：会员生日（以实名身份信息为准）当天会获得超值满100减20（通用）红包1个</td>
                        </tr>
                    <?php elseif($growth['grade']==5): ?> 
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1.身份勋章：等级身份勋章彰显会员尊贵身份</td>
                        </tr>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2.提现特权：会员提款免手续费，每天支持提现<span style="color: #f00;">5</span>次</td>
                        </tr>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3.积分兑换：会员享受积分商城超值兑换特权</td>
                        </tr>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4.升级彩金：会员升级即可领取<span style="color: #f00;">266元</span>升级礼包</td>
                        </tr>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5.生日礼包：会员生日（以实名身份信息为准）当天会获得超值满100减20（通用）、满500减100（通用）红包各1个</td>
                        </tr>
                    <?php else: ?>  
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1.身份勋章：等级身份勋章彰显会员尊贵身份</td>
                        </tr>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2.提现特权：会员提款免手续费，每天支持提现<span style="color: #f00;">8</span>次</td>
                        </tr>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3.积分兑换：会员享受积分商城超值兑换特权</td>
                        </tr>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4.升级彩金：会员升级即可领取<span style="color: #f00;">1666元</span>升级礼包</td>
                        </tr>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5.生日礼包：会员生日（以实名身份信息为准）当天会获得超值满100减20（通用）、满500减100（通用）、满1000减200（通用）红包各1个</td>
                        </tr>
                        <tr>
                          <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;6.积分双倍：享受双倍购彩积分特权</td>
                        </tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
        <?php else: ?> 
         呐，这么做最重要的是要有权限啦！
        <?php endif ?>

    </div>
    <div class="item" style="display:none;" id="united_follow" has_load='false'>
    </div>
</div>
<div class="pop-mask" style="display:none;width:200%"></div>
<!-- 审核弹窗 -->
<form id='checkForm' method='post' action=''>
<div class="pop-dialog" id="J-dc-addAccount">
    <div class="pop-in">
        <div class="pop-head"><h2>重置支付密码</h2><span class="pop-close" title="关闭">关闭</span></div>
        <div class="pop-body">
            <div class="data-table-filter del-percent">
                <table>
                    <colgroup><col width="280" /></colgroup>
                    <tbody>   
                        <tr><td>请选择为该用户重置密码的方式</td></tr>
                        <tr><td><input type="radio" class="radio" name="reset_type" value="1">向该用户绑定邮箱发送重置密码邮件</td></tr>
                        <tr><td><input type="text" name="reset_email" value="<?php echo $user['email'] ?>"  class="ipt w222"></td></tr>
                        <tr><td><input type="radio" class="radio" name="reset_type" value="2">该用户手机修改密码时无需验证码</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pop-foot tac"><a href="javascript:;" class="btn-blue-h32 mlr15" id='submitForm'>提 交</a><a href="javascript:;" class="btn-b-white mlr15 pop-cancel" >取 消</a></div>
    </div>
</div>
    <input type="hidden" name="reset_uid" value="<?php echo $user['uid'] ?>" />
    <input type="hidden" name="reset_oldemail" value="<?php echo $user['email'] ?>" />
    <input type="hidden" name="reset_name" value="<?php echo $user['uname'] ?>" />
</form>
<!-- 注销用户 -->
<form id='lockForm' method='post' action=''>
<div class="pop-dialog" id="J-dc-lockUser">
    <div class="pop-in">
        <div class="pop-head"><h2 id="lockHead"></h2><span class="pop-close" title="关闭">关闭</span></div>
        <div class="pop-body"><div class="data-table-filter del-percent"><table><colgroup><col width="280" /></colgroup><tbody><tr><td id="lockBody"></td></tr></tbody></table></div></div>
        <div class="pop-foot tac"><a href="javascript:;" class="btn-blue-h32 mlr15" id='submitLockForm'>确 认</a><a href="javascript:;" class="btn-b-white mlr15 pop-cancel" >取 消</a></div>
    </div>
</div>
    <input type="hidden" name="lock_uid" value="<?php echo $user['uid'] ?>" /><input type="hidden" name="lock_uname" value="<?php echo $user['uname'] ?>" /><input type="hidden" name="userStatus" value="" />
</form>
<!-- 修改手机号码 -->
<div class="pop-dialog" id="J-dc-modifyPhone">
    <div class="pop-in">
        <div class="pop-head"><h2 id="lockHead">修改手机号码</h2><span class="pop-close" title="关闭">关闭</span></div>
        <div class="pop-body">
            <div class="data-table-filter del-percent">
                <table>
                    <colgroup><col width="280" /></colgroup>
                    <tbody><tr><td>旧手机号码：<?php echo $user['phone']; ?></td></tr><tr><td>新手机号码：<input type="text" class="ipt w108" name="newPhoneVal"></td></tr></tbody>
                </table>
            </div>
        </div>
        <div class="pop-foot tac"><a href="javascript:;" class="btn-blue-h32 mlr15" id='submitModifyPhone'>确 认</a><a href="javascript:;" class="btn-b-white mlr15 pop-cancel" >取 消</a></div>
    </div>
</div>
<!-- 修改邮箱 -->
<div class="pop-dialog" id="J-dc-modifyEmail">
    <div class="pop-in">
        <div class="pop-head"><h2>修改邮箱</h2><span class="pop-close" title="关闭">关闭</span></div>
        <div class="pop-body">
            <div class="data-table-filter del-percent">
                <table><colgroup><col width="280"/></colgroup><tbody><tr><td>旧邮箱：<?php echo $user['email']; ?></td></tr><tr><td>新邮箱：<input type="text" class="ipt w184" name="newEmail"></td></tr></tbody></table>
            </div>
        </div>
        <div class="pop-foot tac"><a href="javascript:;" class="btn-blue-h32 mlr15" id='submitModifyEmail'>确 认</a><a href="javascript:;" class="btn-b-white mlr15 pop-cancel">取 消</a></div>
    </div>
</div>
<!-- 修改实名 -->
<div class="pop-dialog" id="J-dc-modifyRealName">
    <div class="pop-in">
        <div class="pop-head"><h2 id="lockHead">修改真实姓名</h2><span class="pop-close" title="关闭">关闭</span></div>
        <div class="pop-body">
            <div class="data-table-filter del-percent">
                <table>
                    <colgroup><col width="280" /></colgroup>
                    <tbody><tr><td>旧真实姓名：<?php echo $user['real_name']; ?></td></tr><tr><td>新真实姓名：<input type="text" class="ipt w108" name="newRealName"></td></tr></tbody>
                </table>
            </div>
        </div>
        <div class="pop-foot tac"><a href="javascript:;" class="btn-blue-h32 mlr15" id='submitModifyRealName'>确 认</a><a href="javascript:;" class="btn-b-white mlr15 pop-cancel" >取 消</a></div>
    </div>
</div>
<!-- 修改身份证号 -->
<div class="pop-dialog" id="J-dc-modifyIdCard">
    <div class="pop-in">
        <div class="pop-head"><h2 id="lockHead">修改身份证号</h2><span class="pop-close" title="关闭">关闭</span></div>
        <div class="pop-body">
            <div class="data-table-filter del-percent">
                <table>
                    <colgroup><col width="280" /></colgroup>
                    <tbody><tr><td>旧身份证号：<?php echo $user['id_card']; ?></td></tr> <tr><td>新身份证号：<input type="text" class="ipt w130" name="newIdCard"></td></tr></tbody>
                </table>
            </div>
        </div>
        <div class="pop-foot tac"><a href="javascript:;" class="btn-blue-h32 mlr15" id='submitModifyIdCard'>确 认</a><a href="javascript:;" class="btn-b-white mlr15 pop-cancel" >取 消</a></div>
    </div>
</div>
<div class="pop-dialog" id="J-dc-AdjustUmoney">
    <div class="pop-in">
        <div class="pop-head"><h2 id="lockHead">手动调节用户账户金额</h2><span class="pop-close" title="关闭">关闭</span></div>
        <div class="pop-body">
            <div class="data-table-filter del-percent">
	            <form id="AdjustUmoney" action="">
	                <table>
	                    <colgroup><col width="280" /></colgroup>
	                    <tbody>
	                        <tr><td>用户名：<?php echo $user['uname']; ?><input type="hidden" value="<?php echo $user['uid']?>" name="ajust[uid]"></td></tr>
	                        <tr><td>当前可用余额：<?php echo m_format($user['money']); ?><input type="hidden" value="<?php echo $user['uname']?>" name="ajust[uname]"></td></tr>
	                        <tr><td>操作类型：<select name="ajust[type]" id="ajust_type"><option value="0">加款</option><option value="1">扣款</option></select></td></tr>
	                        <tr><td>账户明细类型：<select name="ajust[ctype]" id="ajust_ctype"><option value="1">彩金派送</option><option value="2">奖金派送</option><option value="3">其他</option></select></td></tr>
	                        <tr id="tr_iscapital"><td>是否在成本库<span id="ajuxt_text">扣除</span>：
	                        <label for="ck_capital_1"><input type="radio" name="ajust[iscapital]" value="1" id="ck_capital_1" checked>是</label>
	                        <label for="ck_capital_0"><input type="radio" name="ajust[iscapital]" value="0" id="ck_capital_0">否</label>
	                        </td></tr>
	                        <tr id="tr_ismustcost"><td>金额类型：
	                        <label for="ajust_ismustcost"><input type="radio" name="ajust[ismustcost]" id="ajust_ismustcost" value="0" checked>可提现</label>
	                        <label for="ajust_ismustcost_1"><input type="radio" name="ajust[ismustcost]" id="ajust_ismustcost_1" value="1">不可提现</label>
	                        </td></tr>
	                        <tr><td>关联订单编号：<input type="text" name="ajust[orderId]" class="ipt w222"></td></tr>
	                        <tr><td><span id="ajuxt_mtext">添加</span>：<input type="text" name="ajust[money]" id="ajust_money" class="ipt w84"> 元</td></tr>
	                        <tr><td>备注原因：</td></tr>
	                        <tr><td><textarea rows="" cols="" name="ajust[comment]"></textarea></td></tr>
	                    </tbody>
	                </table>
	            </form>
            </div>
        </div>
        <div class="pop-foot tac"><a href="javascript:;" class="btn-blue-h32 mlr15" id='submitAdjustUmoney'>确 认</a> <a href="javascript:;" class="btn-b-white mlr15 pop-cancel" >取 消</a></div>
    </div>
</div>
<script  src="/source/date/WdatePicker.js"></script>
<script src="/caipiaoimg/src/layer/layer.js"></script>
<script>
    var userId = <?php echo $user['uid'] ?>;
    $(function () {
        // tab切换
        $(".tab-nav li").bind("click", function () {
            var i = $(this).index();
            $(this).addClass('active').siblings().removeClass('active');
            $(this).parents(".tab-nav").next(".tab-content:eq(0)").find(".item").eq(i).show().siblings().hide();
        });
        
        $("#submitForm").click(function(){
            $.ajax({
                type: "post",
                url: '/backend/User/reset_pay_pwd',
                data: $("#checkForm").serialize(),
                success: function (data) {
                    var json = jQuery.parseJSON(data);
                    alert(json.message)
                    if(json.status =='y') {
                        closePop();
                    }
                }
            });
            return false;
        });

        $('#adjust_umoney').click(function(){
            <?php if ($adjust) {?>
            popdialog("J-dc-AdjustUmoney");
            <?php }else {?>
            alert('您没有手动调账权限！');
            <?php }?>
        })
        
        $('#submitAdjustUmoney').click(function(){
            if ($("#ajust_type").val() == 1 && $("#ajust_money").val() * 100 > <?php echo $user['money']?>) {
                alert('金额不能大于用户余额！');
                return false;
            }
        	$.ajax({
                type: "post",
                url: '/backend/User/ajustumoney',
                data: $("#AdjustUmoney").serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data.status === 'n') {
                        alert('您没有手动调账权限！');
                    }else if(data.status === '1') {
                    	alert('成功生成调账订单');
                        location.reload();
                    }else {
                    	alert('生成调账订单失败');
                    }
                }
            });
            return false;
        })
        
        $("#ajust_type").change(function(){
            if ($(this).val() === '1') {
                $('#ajust_ctype').html('<option value="4">其他</option>');
                $('#ajuxt_text').html('添加');
            	$('#ajuxt_mtext').html('减少');
            	$('#tr_ismustcost').hide();
            }else {
            	$('#ajust_ctype').html('<option value="1">彩金派送</option><option value="2">奖金派送</option><option value="3">其他</option>');
            	$('#ajuxt_text').html('扣除');
            	$('#ajuxt_mtext').html('添加');
            	$('#tr_ismustcost').show();
            }
        })
        
        $("#ajust_ctype").change(function(){
            if ($.inArray($(this).val(), ['2']) > -1) {
                $("#tr_iscapital").hide();
            }else {
            	$("#tr_iscapital").show();
            }
        })

        <?php if ($tab) {?>
        $("#<?php echo $tab?> a").trigger('click');
        <?php }?>

        // 注销账户
        $("#submitLockForm").click(function(){
            $.ajax({
                type: "post",
                url: '/backend/User/lockUser',
                data: $("#lockForm").serialize(),
                success: function (data) {
                    var json = jQuery.parseJSON(data);
                    alert(json.message)
                    if(json.status =='y') {
                        closePop();
                        location.reload();
                    } else {
                        closePop();
                    }
                }
            });
            return false;
        });
    });
    function stab(ele, url) {
        if($("#"+ele).attr("has_load") == 'false') {
            $("#"+ele).load("/backend/"+url+"?uid="+userId+"&fromType=ajax",function(){
                $("#"+ele).attr("has_load",'true')
            });
        }
    }
    function reset_pay_pwd($uid) {
         popdialog("J-dc-addAccount");
    }
   $(".msg").click(function(){
	   $_this = $(this);
	   $.ajax({
           type: 'post',
           url:  '/backend/User/updateMsgsendCapacity',
           data: {},
           success: function(response) {
               var response = $.parseJSON(response);
               if(response.status == 'n'){
                   alert(response.message);
               }else{
            	   $.ajax({
                       type: 'post',
                       url:  '/api/user/updateMsgsend',
                       data: {uid:'<?php echo $user['uid']?>', msg_send:$_this.data('modify'), type:$_this.data('id')},
                       dataType : 'json',
                       success: function(response) {
           	            location.reload();
                       }
                   });
               }
           },
           error: function () {
               alert('网络异常，请稍后再试');
           }
       });
	});
	
	$('.lockUser').click(function(){
		var val = $(this).attr('data-val');
		var head = '';
		var body = '是否注销用户：<?php echo $user['uname'];?>？确认后，前台用户登录显示不存在';
		if(val == 0){
			head = '确认解除用户';
			body = '是否解除用户：<?php echo $user['uname'];?>？确认后，用户将恢复正常状态';
		}else if(val == 1){
			head = '确认注销用户';
			body = '是否注销用户：<?php echo $user['uname'];?>？确认后，前台用户登录显示不存在';
		}else if(val == 2){
			head = '确认冻结用户';
			body = '是否冻结用户：<?php echo $user['uname'];?>？确认后，前台用户部分功能将无法操作';
		}
		$("#lockHead").html(head);
		$("#lockBody").html(body);
		$('input[name="userStatus"]').val(val);
		popdialog("J-dc-lockUser");
	});

    function submitLog(msg, extra){
        $.ajax({
            type: 'post',
            url:  '/backend/User/recordLog',
            data: {msg:msg, uname:'<?php echo $user['uname']; ?>', extra:extra},
            success: function(response) {}
        });
    }

    // 修改手机号码
    $('#modifyPhone').click(function(){
        $.ajax({
            type: 'post',
            url:  '/backend/User/checkModifyCapacity',
            data: {},
            success: function(response) {
                var response = $.parseJSON(response);
                console.log(response.status);
                if(response.status == 'n'){
                    alert(response.message);
                }else{
                    popdialog("J-dc-modifyPhone");
                }
            },
            error: function () {
                alert('网络异常，请稍后再试');
            }
        });
    });

    $('#submitModifyPhone').click(function(){
        $.ajax({
            type: 'post',
            url:  '/api/user/modifyPhone',
            data: {uid:'<?php echo $user['uid']?>', phone:$('input[name="newPhoneVal"]').val(), 'isbck':1},
            dataType : 'json',
            success: function(response) {
                if(response.status == 1){
                    submitLog('修改手机号码', '');
                    alert(response.msg);
                    location.reload();
                }else{
                    alert(response.msg);
                }
            },
            error: function () {
                alert('网络异常，请稍后再试');
            }
        });
    });

    // 修改邮箱
    $('#modifyEmail').click(function(){
        $.ajax({
            type: 'post',
            url:  '/backend/User/checkModifyCapacity',
            data: {},
            success: function(response) {
                var response = $.parseJSON(response);
                if(response.status == 'n'){
                    alert(response.message);
                }else{
                    popdialog("J-dc-modifyEmail");
                }
            },
            error: function () {
                alert('网络异常，请稍后再试');
            }
        });
    });

    $('#submitModifyEmail').click(function(){
        $.ajax({
            type: 'post',
            url:  '/api/user/modifyEmail',
            data: {uid:'<?php echo $user['uid']?>', email:$('input[name="newEmail"]').val(), 'isbck':1},
            dataType : 'json',
            success: function(response) {
                if(response.status == 1){
                    submitLog('修改邮箱地址', '');
                    alert(response.msg);
                    location.reload();
                }else{
                    alert(response.msg);
                }
            },
            error: function () {
                alert('网络异常，请稍后再试');
            }
        });
    });

    // 修改真实姓名
    $('#modifyRealName').click(function(){
        $.ajax({
            type: 'post',
            url:  '/backend/User/checkModifyCapacity',
            data: {},
            success: function(response) {
                var response = $.parseJSON(response);
                console.log(response.status);
                if(response.status == 'n'){
                    alert(response.message);
                }else{
                    popdialog("J-dc-modifyRealName");
                }
            },
            error: function () {
                alert('网络异常，请稍后再试');
            }
        });
    });

    $('#submitModifyRealName').click(function(){
        $.ajax({
            type: 'post',
            url:  '/api/user/modifyRealName',
            data: {uid:'<?php echo $user['uid']?>', real_name:$('input[name="newRealName"]').val(), 'isbck':1},
            dataType : 'json',
            success: function(response) {
                if(response.status == 1){
                    submitLog('修改真实姓名', '');
                    alert(response.msg);
                    location.reload();
                }else{
                    alert(response.msg);
                }
            },
            error: function () {
                alert('网络异常，请稍后再试');
            }
        });
    });
    
    // 修改身份证信息
    $('#modifyIdCard').click(function(){
        $.ajax({
            type: 'post',
            url:  '/backend/User/checkModifyCapacity',
            data: {},
            success: function(response) {
                var response = $.parseJSON(response);
                console.log(response.status);
                if(response.status == 'n'){
                    alert(response.message);
                }else{
                    popdialog("J-dc-modifyIdCard");
                }
            },
            error: function () {
                alert('网络异常，请稍后再试');
            }
        });
    });

    $('#submitModifyIdCard').click(function(){
        $.ajax({
            type: 'post',
            url:  '/api/user/modifyIdCard',
            data: {uid:'<?php echo $user['uid']?>', id_card:$('input[name="newIdCard"]').val(), 'isbck':1},
            dataType : 'json',
            success: function(response) {
                if(response.status == 1){
                    submitLog('修改身份证号', '');
                    alert(response.msg);
                    location.reload();
                }else{
                    alert(response.msg);
                }
            },
            error: function () {
                alert('网络异常，请稍后再试');
            }
        });
    });

    // 删除银行卡
    $('.delBank').click(function(){
        var bank_id = $(this).attr('data-bankid');
        $.ajax({
            type: 'post',
            url:  '/api/user/delBank',
            data: {uid:'<?php echo $user['uid']?>', id:$(this).attr('data-index')},
            dataType : 'json',
            success: function(response) {
                if(response.status == 1){
                    submitLog('删除银行卡', '银行卡号：' + bank_id);
                    alert(response.msg);
                    location.reload();
                }else{
                    alert(response.msg);
                }
            },
            error: function () {
                alert('网络异常，请稍后再试');
            }
        });
    });

    // 删除支付银行卡
    $('.delPayBank').click(function(){
        var bank_id = $(this).attr('data-bankid');
        $.ajax({
            type: 'post',
            url:  '/api/recharge/breakPayRequest',
            data: {uid:'<?php echo $user['uid']?>', bank_id:bank_id},
            dataType : 'json',
            success: function(response) {
                if(response.code == 1){
                    alert(response.msg);
                    location.reload();
                }else{
                    alert(response.msg);
                }
            },
            error: function () {
                alert('网络异常，请稍后再试');
            }
        });
    });
//比较时间
function compareDate(d1,d2)
{
  return ((new Date(d1.replace(/-/g,"\/"))) > (new Date(d2.replace(/-/g,"\/"))));
}  
$(function(){
    $(document).on('click', '#searchTransction', function() {
        if(compareDate($('input[name=created_s]').val(),$('input[name=created_e]').val()))
        {
　　　　　　layer.alert('你选择的时间段错误', {icon: 2,btn:'',title:'温馨提示',time:0});
　　　　　　return false; 
        }
        var value_s = $('input[name=value_s]').val();
        var value_e = $('input[name=value_e]').val();
        var re = /^[0-9]+$/ ;
        if(value_s && !re.test(value_s))
        {
            $('input[name=value_s]').val('');
            layer.alert('交易积分区间值错误', {icon: 2,btn:'',title:'温馨提示',time:0});
            return false; 
        } 
        if(value_e && !re.test(value_e))
        {
            $('input[name=value_e]').val('');
            layer.alert('交易积分区间值错误', {icon: 2,btn:'',title:'温馨提示',time:0});
            return false; 
        } 
        if(  (parseInt(value_s) > parseInt(value_e) || !re.test(value_s) || !re.test(value_e) ) && (value_e && value_s))
        {
            $('input[name=value_s]').val('');
            $('input[name=value_e]').val('');
            layer.alert('交易积分区间值错误', {icon: 2,btn:'',title:'温馨提示',time:0});
            return false; 
        }
        if ($("#fromType").val() == "ajax") 
        {
            $("#jifen_info").load("/backend/Growth/ajaxPointDetail?" + $("#search_form").serialize() + "&fromType=ajax");
            return false;
        }
        $('#search_form').submit();
    });
    $(document).on('submit', '#search_form', function(){
        $("#jifen_info").load("/backend/Growth/ajaxPointDetail?"+$("#search_form").serialize()+"&fromType=ajax");
        return false;
    });

    //载入新页面
    $(document).on('click', '.transactions_info a', function() {
        var _this = $(this);
        $("#jifen_info").load(_this.attr("href"));
        return false; 
    });

    $(document).on('focus', '.Wdate1', function() {
        dataPicker();
    });

});
function resetuname(uid) {
	if (confirm('是否将该用户修改用户名的次数重置为1？')) {
		$.post('/backend/User/resetuname', {uid:uid}, function(data){
			if (data.status == 'n') alert(data.message)
			else alert('重置成功')
		}, 'json')
	}
}
</script>
</body>
</html>

