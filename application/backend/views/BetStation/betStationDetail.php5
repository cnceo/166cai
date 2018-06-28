<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="/backend/betStation/index">投注站管理</a>&nbsp;&gt;&nbsp;<a href="/backend/betStation/BetStationDetail">详情</a></div>
<div class="data-table-list mt20 table-no-border">
    <table>
        <colgroup>
            <col width="8%" />
            <col width="92%" />
        </colgroup>
        <tbody class = "body" data-id = "<?php echo $datas['id']?>" data-pid = "<?php echo $datas['partnerId']?>" data-sid = "<?php echo $datas['shopNum']?>" data-name = "<?php echo $datas['partner_name']?>">
        <tr>
            <td class="tar"><label for="">合作商：</label></td>
            <td class="tal pl10"><?php echo $datas['partner_name']?></td>
        </tr>
        <tr>
            <td class="tar"><label for="">编号：</label></td>
            <td class="tal pl10"><?php echo $datas['shopNum']?></td>
        </tr>
        <tr>
            <td class="tar"><label for="">名称：</label></td>
            <td class="tal pl10"><?php echo $datas['cname']?></td>
        </tr>
        <tr>
            <td class="tar"><label for="">彩种类别：</label></td>
            <td class="tal pl10"><?php echo $datas['lottery_type'] == 0 ? '体彩': '福彩'?></td>
        </tr>
        <tr>
            <td class="tar"><label for="">电话：</label></td>
            <td class="tal pl10"><?php echo $datas['phone']?></td>
        </tr>
        <tr>
            <td class="tar"><label for="">QQ：</label></td>
            <td class="tal pl10"><?php echo $datas['qq']?></td>
        </tr>
        <tr>
            <td class="tar"><label for="">微信：</label></td>
            <td class="tal pl10"><?php echo $datas['webchat']?></td>
        </tr>
        <tr>
            <td class="tar"><label for=""></label>其他联系方式：</td>
            <td class="tal pl10"><?php echo $datas['other_contact']?></td>
        </tr>
        <tr>
            <td class="tar"><label for="">地址：</label></td>
            <td class="tal pl10"><label for=""><?php echo $datas['address']?></label></td>
        </tr>
        <tr>
            <td class="tar"><label for="">创建时间：</label></td>
            <td class="tal pl10"><?php echo $datas['created']?></td>
        </tr>
        <tr>
            <td class="tar"><label for="">附件：</label></td>
            <td class="tal pl10">
                <?php foreach($files as $key => $file):?>
                <a href="/backend/betStation/download?id=<?php echo $file['id']?>" ><?php echo $file['filename']?></a><br/>
                <?php endforeach;?>
            </td>

        </tr>
        <tr>
            <td class="tar"><label for="">状态：</label></td>
            <td class="tal pl10"><?php switch ($datas['status'])
                {
                    case 0:
                        echo "待审核";
                        break;
                    case 10:
                        echo "审核未通过";
                        break;
                    case 20:
                        echo "审核通过";
                        break;
                    case 30:
                        echo "已上架";
                        break;
                    case 40:
                        echo "审核通过";
                        break;
                }
                ?></td>
        </tr>
        <tr>
            <td class="tar"><label for="">彩种：</label></td>
            <td class="tal pl10"><label for=""><?php echo $lids[$datas['lid']]['cname']?></label></td>
        </tr>
        </tbody>
    </table>
</div>
<div class="audit-detail-btns mt20 ml40">
    <?php
        $id = $datas['id'];
     switch ($datas['status'])
    {
        case 0:
            echo '<a href="/backend/betStation/BetStationEdit?id='.$id.'" class="btn-blue" target = "_self">编辑</a>
                <a href="javascritp:;" class="btn-blue  auditpass" id="audit-through">审核通过</a>
                <a href="javascritp:;" class="btn-blue auditnp">审核不通过</a>';
            break;
        case 10:
            echo '<a href="/backend/betStation/BetStationEdit?id='.$id.'" class="btn-blue" target = "_self">编辑</a>
                <a href="javascritp:;" class="btn-blue auditpass" id="audit-through">审核通过</a>';
            break;
        case 20:
            echo '<a href="/backend/betStation/BetStationEdit?id='.$id.'" class="btn-blue" target = "_self">编辑</a>
                <a href="javascritp:;" class="btn-blue auditnp">审核不通过</a>
                <a href="javascritp:;" class="btn-blue shelve">上架</a>';
            break;
        case 30:
            echo '<a href="javascritp:;" class="btn-blue offshelve">下架</a>';
            break;
         case 40:
             echo '<a href="/backend/betStation/BetStationEdit?id='.$id.'" class="btn-blue" target = "_self">编辑</a>
                <a href="javascritp:;" class="btn-blue auditnp">审核不通过</a>
                <a href="javascritp:;" class="btn-blue shelve">上架</a>';
             break;
    }
    ?>
<!--    <a href="audit-edit.htm" class="btn-blue">编辑</a>-->
<!--    <a href="###" class="btn-blue" id="audit-through">审核通过</a>-->
<!--    <a href="###" class="btn-blue">审核不通过</a>-->
<!--    <a href="###" class="btn-blue">上架</a>-->
<!--    <a href="###" class="btn-blue">下架</a>-->
</div>
<!-- 审核通过 start -->
<div class="pop-dialog" id="auditpass" style="display:none;">
    <div class="pop-in pop-examine">
        <div class="pop-head">
            <h2 class="tac">确定审核通过？</h2>
            <span class="pop-close" title="关闭">关闭</span>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:;" class="btn-blue pop-btn tac  auditp">审核通过</a>
        </div>
    </div>
</div>
<!-- 审核通过 end -->
<!-- 审核不通过 start -->
<div class="pop-dialog" id="auditnpass" style="display:none;">
    <div class="pop-in">
        <div class="pop-head">
            <h2 class="tac">确定审核不通过？</h2>
            <span class="pop-close" title="关闭">关闭</span>
        </div>
        <div class="pop-body">
            <div class="data-table-list table-no-border layouttable">
                <table>
                    <colgroup>
                        <col width="40" />
                        <col width="200" />
                    </colgroup>
                    <tbody>
                    <tr>
                        <td class="vat">
                            <label for="" >原因：</label>
                        </td>
                        <td class="vat">
                            <textarea name="" id="npass" class="examine-text"></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:;" class="btn-blue pop-btn tac  auditnpass">审核不通过</a>
        </div>
    </div>
</div>
<!-- 审核不通过 end -->
<!-- 上架 start -->
<div class="pop-dialog" id="shelve" style="display:none;">
    <div class="pop-in pop-examine">
        <div class="pop-head">
            <h2 class="tac">确定上架？</h2>
            <span class="pop-close" title="关闭">关闭</span>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:;" class="btn-blue pop-btn tac  upshelve">上架</a>
        </div>
    </div>
</div>
<!-- 上架 end -->
<!-- 下架 start -->
<div class="pop-dialog" id="offshelve" style="display:none;">
    <div class="pop-in">
        <div class="pop-head">
            <h2 class="tac">确定下架？</h2>
            <span class="pop-close" title="关闭">关闭</span>
        </div>
        <div class="pop-body">
            <div class="data-table-list table-no-border layouttable">
                <table>
                    <colgroup>
                        <col width="40" />
                        <col width="200" />
                    </colgroup>
                    <tbody>
                    <tr>
                        <td class="vat">
                            <label for="" >原因：</label>
                        </td>
                        <td class="vat">
                            <textarea name="" id="oshelve" class="examine-text"></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:;" class="btn-blue pop-btn tac oshelve">下架</a>
        </div>
    </div>
</div>
<!-- 下架 end -->
<script type="text/javascript">
    $(function(){
        var id = $('.body').data('id'),
            name = $('.body').data('name');
       $('.auditpass').click(function(){
           popdialog("auditpass");
           return false;
       });

        $('.auditnp').click(function(){
            popdialog("auditnpass");
            return false;
        });

        $('.shelve').click(function(){
            popdialog("shelve");
            return false;
        });

        $('.offshelve').click(function(){
            popdialog("offshelve");
            return false;
        });
        $('.auditp').click(function(){
            $.ajax({
                type : "post",
                url : '/backend/betStation/BetStationUpdate',
                data : {id:id, name:name, status:20},
                success:function(res){
                	if(res == 1)
                    {
                    	location.reload();
                    }else{
                        alert(res);
                    }
                }
            })

        });


        $('.auditnpass').click(function(){
            var reason = $('#npass').val();
            $.ajax({
                type : "post",
                url : '/backend/betStation/BetStationUpdate',
                data : {id:id, name:name, status:10, reason:reason},
                success:function(res){
                	if(res == 1)
                    {
                    	location.reload();
                    }else{
                        alert(res);
                    }
                }
            })

        });

        $('.upshelve').click(function(){
            $.ajax({
                type : "post",
                url : '/backend/betStation/BetStationUpdate',
                data : {id:id, name:name, status:30},
                success:function(res){
                	if(res == 1)
                    {
                    	location.reload();
                    }else{
                        alert(res);
                    }
                }
            })

        });

        $('.oshelve').click(function(){
            var reason = $('#oshelve').val();
            $.ajax({
                type : "post",
                url : '/backend/betStation/BetStationUpdate',
                data : {id:id, name:name, status:20, reason:reason},
                success:function(res){
                    if(res == 1)
                    {
                    	location.reload();
                    }else{
                        alert(res);
                    }
                    
                }
            })

        });

    });

</script>
