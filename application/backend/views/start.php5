<?php $this->load->view("templates/head") ?>
        <!-- <div class="mod-a mb15">
            <div class="hd">
                <h2>用户概况</h2>
            </div>
            <div class="bd">
                <div class="home-profile">
                    <h3>昨日数据</h3>
                    <ul class="profile-list clearfix">
                        <li><span class="title">注册用户数</span><span class="num"><?php //echo intval($yestoday['reg_num']);   ?></span></li>
                        <li><span class="title">充值用户数</span><span class="num"><?php //echo intval($yestoday['recharge_num']);   ?></span></li>
                        <li><span class="title">出票用户数</span><span class="num"><?php //echo intval($yestoday['order_suc_num']);   ?></span></li>
                        <li><span class="title">提款用户数</span><span class="num"><?php //echo intval($yestoday['withdraw_num']);   ?></span></li>
                        <li><span class="title">有效用户数</span><span class="num"><?php //echo intval($yestoday['effective_num']);   ?></span></li>
                        <li><span class="title">充值总额(元)</span><span class="num"><?php //echo m_format($yestoday['recharge_money']);   ?></span></li>
                        <li><span class="title">出票总额(元)</span><span class="num"><?php //echo m_format($yestoday['order_suc_money']);   ?></span></li>
                        <li><span class="title">提款总额(元)</span><span class="num"><?php //echo m_format($yestoday['withdraw_money']);   ?></span></li>
                        <li><span class="title">中奖用户数</span><span class="num"><?php //echo intval($yestoday['win_num']);   ?></span></li>
                        <li><span class="title">中奖总额(元)</span><span class="num"><?php //echo m_format($yestoday['win_money']);   ?></span></li>
                    </ul>
                    <div class="hr-dashed"></div>
                    <h3>最近30日数据</h3>
                    <ul class="profile-list clearfix">
                        <li><span class="title">注册用户数</span><span class="num"><?php //echo intval($thirty['reg_num']);   ?></span></li>
                        <li><span class="title">充值用户数</span><span class="num"><?php //echo intval($thirty['recharge_num']);   ?></span></li>
                        <li><span class="title">出票用户数</span><span class="num"><?php //echo intval($thirty['order_suc_num']);   ?></span></li>
                        <li><span class="title">提款用户数</span><span class="num"><?php //echo intval($thirty['withdraw_num']);   ?></span></li>
                        <li><span class="title">有效用户数</span><span class="num"><?php //echo intval($thirty['effective_num']);   ?></span></li>
                        <li><span class="title">充值总额(元)</span><span class="num"><?php //echo m_format($thirty['recharge_money']);   ?></span></li>
                        <li><span class="title">出票总额(元)</span><span class="num"><?php //echo m_format($thirty['order_suc_money']);   ?></span></li>
                        <li><span class="title">提款总额(元)</span><span class="num"><?php //echo m_format($thirty['withdraw_money']);   ?></span></li>
                        <li><span class="title">中奖用户数</span><span class="num"><?php //echo intval($thirty['win_num']);   ?></span></li>
                        <li><span class="title">中奖总额(元)</span><span class="num"><?php //echo m_format($thirty['win_money']);   ?></span></li>
                    </ul>
                </div>
            </div>
        </div> -->
        <div class="tab-nav">
            <ul class="clearfix">
                <li class="active"><a href="javascript:stab('abnomal','Order/abnormal_list')"><span>订单监控</span></a></li>
                <li><a href="javascript:stab('big_order','Order/check_list')"><span>大奖提醒</span></a></li>
            </ul>
        </div>
        <div class="tab-content wbase">
            <div class="item" style="display:block;" id="abnomal" has_load='false'></div>
            <div class="item"   id="big_order" has_load='false'></div>
        </div>
<script  src="/source/date/WdatePicker.js"></script>
<script>

    $(function () {
        // tab切换
        $(".tab-nav li").bind("click", function () {
            var i = $(this).index();
            $(this).addClass('active').siblings().removeClass('active');
            $(this).parents(".tab-nav").next(".tab-content:eq(0)").find(".item").eq(i).show().siblings().hide();
        });
    });
    function stab(ele, url)
    {
        if($("#"+ele).attr("has_load") == 'false')
        {
            $("#"+ele).load("/backend/"+url+"?fromType=ajax",function(){
                $("#"+ele).attr("has_load",'true')
            });
        }
    }
   stab('abnomal','Order/abnormal_list');
</script>
    </body>
</html>