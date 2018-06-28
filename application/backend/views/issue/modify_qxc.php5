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
                        <input type="text" class="ipt w150 none_input" name="awardNum" value="<?php echo $awardNum;?>" readonly='true'>
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
                    <col width="20%">
                    <col width="40%">
                    <col width="40%">
                </colgroup>
                <thead>
                    <tr>
                       <th>奖级</th>
                       <th>注数</th>
                       <th>奖金</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>一等奖</td>
                        <td><input type="text" class="ipt w150" name="1dj_zs" value="<?php echo $bonusDetail['1dj']['zs'];?>"></td>
                        <td><input type="text" class="ipt w150" name="1dj_dzjj" value="<?php echo $bonusDetail['1dj']['dzjj'];?>"></td>
                    </tr>
                    <tr>
                        <td>二等奖</td>
                        <td><input type="text" class="ipt w150" name="2dj_zs" value="<?php echo $bonusDetail['2dj']['zs'];?>"></td>
                        <td><input type="text" class="ipt w150" name="2dj_dzjj" value="<?php echo $bonusDetail['2dj']['dzjj'];?>"></td>
                    </tr>
                    <tr>
                        <td>三等奖</td>
                        <td><input type="text" class="ipt w150" name="3dj_zs" value="<?php echo $bonusDetail['3dj']['zs'];?>"></td>
                        <td><input type="text" class="ipt w150 none_input" readonly="true" name="3dj_dzjj" value="1800"></td>
                    </tr>
                    <tr>
                        <td>四等奖</td>
                        <td><input type="text" class="ipt w150" name="4dj_zs" value="<?php echo $bonusDetail['4dj']['zs'];?>"></td>
                        <td><input type="text" class="ipt w150 none_input" readonly="true" name="4dj_dzjj" value="300"></td>
                    </tr>
                    <tr>
                        <td>五等奖</td>
                        <td><input type="text" class="ipt w150" name="5dj_zs" value="<?php echo $bonusDetail['5dj']['zs'];?>"></td>
                        <td><input type="text" class="ipt w150 none_input" readonly="true" name="5dj_dzjj" value="20"></td>
                    </tr>
                    <tr>
                        <td>六等奖</td>
                        <td><input type="text" class="ipt w150" name="6dj_zs" value="<?php echo $bonusDetail['6dj']['zs'];?>"></td>
                        <td><input type="text" class="ipt w150 none_input" readonly="true" name="6dj_dzjj" value="5"></td>
                    </tr>
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
        var zs1 = $('input[name="1dj_zs"]').val();
        var dzjj1 = $('input[name="1dj_dzjj"]').val();
        var zs2 = $('input[name="2dj_zs"]').val();
        var dzjj2 = $('input[name="2dj_dzjj"]').val();
        var zs3 = $('input[name="3dj_zs"]').val();
        var dzjj3 = $('input[name="3dj_dzjj"]').val();
        var zs4 = $('input[name="4dj_zs"]').val();
        var dzjj4 = $('input[name="4dj_dzjj"]').val();
        var zs5 = $('input[name="5dj_zs"]').val();
        var dzjj5 = $('input[name="5dj_dzjj"]').val();
        var zs6 = $('input[name="6dj_zs"]').val();
        var dzjj6 = $('input[name="6dj_dzjj"]').val();
        var reg = /^[0-9]\d*$/;
        if (issue !== '<?php echo $issue;?>') {
        	layer.alert('期次信息有误，请确认后填写', {icon: 2,btn:'',title:'温馨提示',time:0});
            return false;
        }
        if(!reg.test(sale) || !reg.test(pool) || !reg.test(zs1) || !reg.test(dzjj1) || !reg.test(zs2) || !reg.test(dzjj2) || !reg.test(zs3) || !reg.test(dzjj3) || !reg.test(zs4) || !reg.test(dzjj4) || !reg.test(zs5) || !reg.test(dzjj5) || !reg.test(zs6) || !reg.test(dzjj6))
        {
            layer.alert('修改内容必须为非负整数~', {icon: 2,btn:'',title:'温馨提示',time:0});
            return false;
        }
        //开奖号码验证
        var awardNum = $('input[name="awardNum"]').val();
        ball = awardNum.split(',');
        if(ball.length != 7)
        {
            layer.alert('请输入7个开奖号码~', {icon: 2,btn:'',title:'温馨提示',time:0});
            return false;
        }
        for(var i in ball)
        {
            if(!reg.test(ball[i]))
            {
                layer.alert('开奖号码必须为非负整数~', {icon: 2,btn:'',title:'温馨提示',time:0});
                return false;
            }
            if(ball[i] < 0 || ball[i] > 9)
            {
                layer.alert('开奖号码范围为0-9~', {icon: 2,btn:'',title:'温馨提示',time:0});
                return false;
            }
        }
        popdialog("confirm-submit");
    });
</script>