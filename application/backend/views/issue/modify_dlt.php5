<style type="text/css">
    .none_input{border: none;box-shadow:none;-webkit-box-shadow:none;}
</style>
<form action="/backend/Issue/modifyBonusDetail" method="get"  id="submit_form">
        <h2 class="kj-dtail-fix-title">
            <em><?php echo $name;?></em>第<input type="text" class="ipt w84" name="issue_fill">期开奖详情
        </h2>
        <div class="data-table-filter" style=" width: 100%;">
            <table>
                <tbody>
                    <tr>
                        <td colspan="9">
                        开奖号码
                        <input type="text" class="ipt w150 none_input" name="awardNum" value="<?php echo $awardNum;?>" readonly="true">
                        </td>
                    </tr>
                    <tr>
                        <td>
                        全国销售
                        <input type="text" class="ipt w150" name="sale" value="<?php echo $sale;?>">元
                        </td>
                        <td>
                          奖池滚存
                          <input type="text" class="ipt w150" name="pool" value="<?php echo $pool;?>">元
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
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
                        <td><input type="text" class="ipt w150" name="1dj_jb_zs" value="<?php echo $bonusDetail['1dj']['jb']['zs'];?>"></td>
                        <td><input type="text" class="ipt w150" name="1dj_jb_dzjj" value="<?php echo $bonusDetail['1dj']['jb']['dzjj'];?>"></td>
                    </tr>
                    <tr>
                        <td>追加</td>
                        <td><input type="text" class="ipt w150" name="1dj_zj_zs" value="<?php echo $bonusDetail['1dj']['zj']['zs'];?>"></td>
                        <td><input type="text" class="ipt w150" name="1dj_zj_dzjj" value="<?php echo $bonusDetail['1dj']['zj']['dzjj'];?>"></td>
                    </tr>
                    <tr>
                        <td rowspan="2">二等奖</td>
                        <td>基本</td>
                        <td><input type="text" class="ipt w150" name="2dj_jb_zs" value="<?php echo $bonusDetail['2dj']['jb']['zs'];?>"></td>
                        <td><input type="text" class="ipt w150" name="2dj_jb_dzjj" value="<?php echo $bonusDetail['2dj']['jb']['dzjj'];?>"></td>
                    </tr>
                    <tr>
                        <td>追加</td>
                        <td><input type="text" class="ipt w150" name="2dj_zj_zs" value="<?php echo $bonusDetail['2dj']['zj']['zs'];?>"></td>
                        <td><input type="text" class="ipt w150" name="2dj_zj_dzjj" value="<?php echo $bonusDetail['2dj']['zj']['dzjj'];?>"></td>
                    </tr>
                    <tr>
                        <td rowspan="2">三等奖</td>
                        <td>基本</td>
                        <td><input type="text" class="ipt w150" name="3dj_jb_zs" value="<?php echo $bonusDetail['3dj']['jb']['zs'];?>"></td>
                        <td><input type="text" class="ipt w150" name="3dj_jb_dzjj" value="<?php echo $bonusDetail['3dj']['jb']['dzjj'];?>"></td>
                    </tr>
                    <tr>
                        <td>追加</td>
                        <td><input type="text" class="ipt w150" name="3dj_zj_zs" value="<?php echo $bonusDetail['3dj']['zj']['zs'];?>"></td>
                        <td><input type="text" class="ipt w150" name="3dj_zj_dzjj" value="<?php echo $bonusDetail['3dj']['zj']['dzjj'];?>"></td>
                    </tr>
                    <tr>
                        <td rowspan="2">四等奖</td>
                        <td>基本</td>
                        <td><input type="text" class="ipt w150" name="4dj_jb_zs" value="<?php echo $bonusDetail['4dj']['jb']['zs'];?>"></td>
                        <td><input type="text" class="ipt w150 none_input" readonly='true' name="4dj_jb_dzjj" value="<?php echo isset($bonusDetail['4dj']['jb']['dzjj']) ? $bonusDetail['4dj']['jb']['dzjj'] : 200;?>"></td>
                    </tr>
                    <tr>
                        <td>追加</td>
                        <td><input type="text" class="ipt w150" name="4dj_zj_zs" value="<?php echo $bonusDetail['4dj']['zj']['zs'];?>"></td>
                        <td><input type="text" class="ipt w150 none_input" readonly='true' name="4dj_zj_dzjj" value="<?php echo isset($bonusDetail['4dj']['zj']['dzjj']) ? $bonusDetail['4dj']['zj']['dzjj'] : 100;?>"></td>
                    </tr>
                    <tr>
                        <td rowspan="2">五等奖</td>
                        <td>基本</td>
                        <td><input type="text" class="ipt w150" name="5dj_jb_zs" value="<?php echo $bonusDetail['5dj']['jb']['zs'];?>"></td>
                        <td><input type="text" class="ipt w150 none_input" readonly='true' name="5dj_jb_dzjj" value="<?php echo isset($bonusDetail['5dj']['jb']['dzjj']) ? $bonusDetail['5dj']['jb']['dzjj'] : 10;?>"></td>
                    </tr>
                    <tr>
                        <td>追加</td>
                        <td><input type="text" class="ipt w150" name="5dj_zj_zs" value="<?php echo $bonusDetail['5dj']['zj']['zs'];?>"></td>
                        <td><input type="text" class="ipt w150 none_input" readonly='true' name="5dj_zj_dzjj" value="<?php echo isset($bonusDetail['5dj']['zj']['dzjj']) ? $bonusDetail['5dj']['zj']['dzjj'] : 5;?>"></td>
                    </tr>
                    <?php if($bonusDetail['6dj']['zj']['zs'] || $bonusDetail['6dj']['zj']['dzjj']):?>
                    <tr>
                        <td rowspan="2">六等奖</td>
                        <td>基本</td>
                        <td><input type="text" class="ipt w150" name="6dj_jb_zs" value="<?php echo $bonusDetail['6dj']['jb']['zs'];?>"></td>
                        <td><input type="text" class="ipt w150 none_input" name="6dj_jb_dzjj" value="<?php echo isset($bonusDetail['6dj']['jb']['dzjj']) ? $bonusDetail['6dj']['jb']['dzjj'] : 5;?>"></td>
                    </tr>
                    <tr>
                        <td>追加</td>
                        <td><input type="text" class="ipt w150" name="6dj_zj_zs" value="<?php echo $bonusDetail['6dj']['zj']['zs'];?>"></td>
                        <td><input type="text" class="ipt w150 none_input" name="6dj_zj_dzjj" value="<?php echo isset($bonusDetail['6dj']['zj']['dzjj']) ? $bonusDetail['6dj']['zj']['dzjj'] : 5;?>"></td>
                    </tr>
                    <?php  else:?>
                    <tr>
                        <td colspan="2">六等奖</td>
                        <td><input type="text" class="ipt w150" name="6dj_jb_zs" value="<?php  echo $bonusDetail['6dj']['jb']['zs'];?>"></td>
                        <td><input type="text" class="ipt w150 none_input" name="6dj_jb_dzjj" value="<?php  echo isset($bonusDetail['6dj']['jb']['dzjj']) ? $bonusDetail['6dj']['jb']['dzjj'] : 5;?>"></td>
                    </tr>
                    <?php  endif;?>
                </tbody>
            </table>
            <div class="btn-group mt10">
                 <input type='hidden' class='' name='backUrl' value=''/>
                <input type='hidden' class='' name='type' value='<?php echo $lid;?>'/>
                <input type='hidden' class='' name='issue' value='<?php echo $issue;?>'/>
                <input type='hidden' class='' name='status' value='<?php echo $matchStatus;?>'/>
                <a href="javascript:void(0);" class="btn-blue-h32" id="submit">确认</a>
            </div>
        </div>
</form>
<!--引入第三方插件 layer.js-->
<script src="/caipiaoimg/src/layer/layer.js"></script>
<script>
    $('input[name=backUrl]').val(window.location.href);
    $("#submit").click(function(){
        var issue = $('input[name="issue_fill"]').val();
        var sale = $('input[name="sale"]').val();
        var pool = $('input[name="pool"]').val();
        var dj_jb_zs1 = $('input[name="1dj_jb_zs"]').val();
        var dj_jb_dzjj1 = $('input[name="1dj_jb_dzjj"]').val();
        var dj_zj_zs1 = $('input[name="1dj_zj_zs"]').val();
        var dj_zj_dzjj1 = $('input[name="1dj_zj_dzjj"]').val();

        var dj_jb_zs2 = $('input[name="2dj_jb_zs"]').val();
        var dj_jb_dzjj2 = $('input[name="2dj_jb_dzjj"]').val();
        var dj_zj_zs2 = $('input[name="2dj_zj_zs"]').val();
        var dj_zj_dzjj2 = $('input[name="2dj_zj_dzjj"]').val();

        var dj_jb_zs3 = $('input[name="3dj_jb_zs"]').val();
        var dj_jb_dzjj3 = $('input[name="3dj_jb_dzjj"]').val();
        var dj_zj_zs3 = $('input[name="3dj_zj_zs"]').val();
        var dj_zj_dzjj3 = $('input[name="3dj_zj_dzjj"]').val();

        var dj_jb_zs4 = $('input[name="4dj_jb_zs"]').val();
        var dj_jb_dzjj4 = $('input[name="4dj_jb_dzjj"]').val();
        var dj_zj_zs4 = $('input[name="4dj_zj_zs"]').val();
        var dj_zj_dzjj4 = $('input[name="4dj_zj_dzjj"]').val();

        var dj_jb_zs5 = $('input[name="5dj_jb_zs"]').val();
        var dj_jb_dzjj5 = $('input[name="5dj_jb_dzjj"]').val();
        var dj_zj_zs5 = $('input[name="5dj_zj_zs"]').val();
        var dj_zj_dzjj5 = $('input[name="5dj_zj_dzjj"]').val();

        var dj_jb_zs6 = $('input[name="6dj_jb_zs"]').val();
        var dj_jb_dzjj6 = $('input[name="6dj_jb_dzjj"]').val();
        var reg = /^[0-9]\d*$/;
        if (issue !== '<?php echo $issue;?>') {
        	layer.alert('期次信息有误，请确认后填写', {icon: 2,btn:'',title:'温馨提示',time:0});
            return false;
        }
        if(!reg.test(sale) || !reg.test(pool) || !reg.test(dj_jb_zs1) || !reg.test(dj_jb_dzjj1) || !reg.test(dj_zj_zs1) || !reg.test(dj_zj_dzjj1) || !reg.test(dj_jb_zs2) || !reg.test(dj_jb_dzjj2) || !reg.test(dj_zj_zs2) || !reg.test(dj_zj_dzjj2) || !reg.test(dj_jb_zs3) || !reg.test(dj_jb_dzjj3) || !reg.test(dj_zj_zs3) || !reg.test(dj_zj_dzjj3) || !reg.test(dj_jb_zs4) || !reg.test(dj_jb_dzjj4) || !reg.test(dj_zj_zs4) || !reg.test(dj_zj_dzjj4) || !reg.test(dj_jb_zs5) || !reg.test(dj_jb_dzjj5) || !reg.test(dj_zj_zs5) || !reg.test(dj_zj_dzjj5) || !reg.test(dj_jb_zs6) || !reg.test(dj_jb_dzjj6))
        {
            layer.alert('修改内容必须为非负整数~', {icon: 2,btn:'',title:'温馨提示',time:0});
            return false;
        }
        //开奖号码验证
        var awardNum = $('input[name="awardNum"]').val();
        preBall = awardNum.split('|');
        redBall = preBall[0].split(',');
        //红球规则
        if(checkUni(redBall))
        {
            layer.alert('红球号码必须唯一~', {icon: 2,btn:'',title:'温馨提示',time:0});
            return false;
        }
        if(redBall.length != 5)
        {
            layer.alert('请输入5个红球号码~', {icon: 2,btn:'',title:'温馨提示',time:0});
            return false;
        }
        for(var i in redBall)
        {
            if(!reg.test(redBall[i]))
            {
                layer.alert('开奖号码必须为非负整数~', {icon: 2,btn:'',title:'温馨提示',time:0});
                return false;
            }
            if(redBall[i] < 0 || redBall[i] > 35)
            {
                layer.alert('红球号码范围为01-35~', {icon: 2,btn:'',title:'温馨提示',time:0});
                return false;
            }
        }
        //篮球规则
        blueBall = preBall[1].split(',');
        if(blueBall.length != 2)
        {
            layer.alert('请输入2个蓝球号码~', {icon: 2,btn:'',title:'温馨提示',time:0});
            return false;
        }
        if(checkUni(blueBall))
        {
            layer.alert('蓝球号码必须唯一~', {icon: 2,btn:'',title:'温馨提示',time:0});
            return false;
        }
        for(var i in blueBall)
        {
            if(!reg.test(blueBall[i]))
            {
                layer.alert('开奖号码必须为非负整数~', {icon: 2,btn:'',title:'温馨提示',time:0});
                return false;
            }
            if(blueBall[i] < 0 || blueBall[i] > 12)
            {
                layer.alert('蓝球号码范围为01-12~', {icon: 2,btn:'',title:'温馨提示',time:0});
                return false;
            }
        }
        popdialog("confirm-submit");
    });

    function checkUni(a)
    {
        return /(\x0f[^\x0f]+)\x0f[\s\S]*\1/.test("\x0f"+a.join("\x0f\x0f") +"\x0f");
    }
</script>
