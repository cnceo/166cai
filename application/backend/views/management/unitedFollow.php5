<div class="data-table-list wbase mt20">
    <table>
        <colgroup>
            <col width="150" />
            <col width="150" />
        </colgroup>
        <tbody>
            <tr>
                <th>关注人</th>
                <th>操作</th>
            </tr>
            <?php if(!empty($list)): ?>
            <?php foreach($list as $items): ?>
            <tr>
                <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $items['puid'] ?>" class="cBlue"><?php echo $items['uname']; ?></a></td>
                <td data-id="<?php echo $items['id']; ?>" data-uname="<?php echo $items['uname']; ?>" class="cancelFollow"><a href="javascript:;" class="cRed">取消关注</a></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            <input type="hidden" name="uid" value='<?php echo $uid; ?>' />
            <input type="hidden" name="uname" value='<?php echo $uname; ?>' />
        </tbody>
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
<div class="page mt10 united_follow">
    <?php echo $pages[0] ?>
</div>
<script src="/caipiaoimg/src/layer/layer.js"></script>
<script>
    $('.united_follow a').click(function(){
        var _this = $(this);
        $("#united_follow").load(_this.attr("href"));
        return false;
    });
    // 取消关注
    $('.cancelFollow').click(function(){
        var datas = {'id':$(this).attr("data-id"), 'uname':$('input[name="uname"]').val(), 'puname':$(this).attr("data-uname"), 'uid':$('input[name="uid"]').val()};
        layer.open({
            'title':'取消关注',
            'type': 1,
            'area': '300px;',
            'closeBtn': 1, //不显示关闭按钮
            'btn': ['确认', '取消'],
            'shadeClose': true, //开启遮罩关闭
            'content': '<div style="margin-left:15px;margin-top:15px;margin-right:15px;">'+"是否确定要取消" + datas.uname + "对" + datas.puname + "的关注？"+'</div>', 
            'btnAlign': 'c',
            'yes': function()
            {
                ajaxComm(datas,'/backend/User/cancelFollow');
                layer.load(0, {shade: [0.5, '#393D49']});
            }
        }); 
    });
    function ajaxComm(datas,url)
    {
        $.ajax({
            type: "post",
            url: url,
            data: datas,
            success: function(data)
            {
                var json = jQuery.parseJSON(data);
                layer.closeAll();
                if(json.status == 'SUCCESSS' || json.status == 'y' )
                {
                    layer.alert(json.message, {icon: 1, btn:'', title:'温馨提示', time:0, end:function(){}});
                    $("#united_follow").load("/backend/User/unitedFollow?uid="+datas.uid+"&fromType=ajax");
                    return false;
                }else{
                    layer.alert(json.message, {icon: 2, btn:'', title:'温馨提示', time:0});
                }
            }
        })
    }
</script>