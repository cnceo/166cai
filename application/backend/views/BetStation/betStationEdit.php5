<?php $this->load->view("templates/head") ?>
<?php
$type = array(
    '0' => '体彩',
    '1' => '福彩'
);
?>
<div class="path">您的位置：<a href="/backend/betStation/index">投注站管理</a>&nbsp;&gt;&nbsp;<a href="/backend/betStation/BetStationEdit">编辑</a></div>
<link rel="stylesheet" href="/caipiaoimg/v1.0/styles/admin/admin.css">
<form id = "betStationEidt" action = "/backend/betStation/BetStationUpload" method = "post" enctype="multipart/form-data">
<div class="data-table-list mt20 table-no-border">
    <table class="table">
        <colgroup>
            <col width="10%" />
            <col width="90%" />
        </colgroup>
        <tbody class="partner" data-id = "<?php echo $datas['id']?>" data-pid = "<?php echo $datas['partnerId']?>">
        <tr>
            <td class="tar"><label for="">合作商：</label></td>
            <td class="tal pl10"><?php echo $datas['partner_name']?></td>
            <input type = "text" name = "partnerId" value = "<?php echo $datas['partnerId']?>" hidden>
            <input type = "text" name = "id" value = "<?php echo $datas['id']?>" hidden>
        </tr>
        <tr>
            <td class="tar"><label for="">* 编号：</label></td>
            <td class="tal pl10"><input type="text" name = "bid" class="ipt w184" value = "<?php echo $datas['shopNum']?>" required="true"></td>
        </tr>
        <tr>
            <td class="tar"><label for="">名称：</label></td>
            <td class="tal pl10"><input type="text" name = "cname" class="ipt w184" value = "<?php echo $datas['cname']?>"></td>
        </tr>
        <tr>
            <td class="tar"><label for="">彩种类别：</label></td>
            <td class="tal pl10">
            	<select name="lottery_type">
            		<option value="0" <?php if ($datas['lottery_type'] == 0) {?>selected<?php }?>>体彩</option>
            		<option value="1" <?php if ($datas['lottery_type'] == 1) {?>selected<?php }?>>福彩</option>
            	</select>
            </td>
        </tr>
        <tr>
            <td class="tar"><label for="">* 电话：</label></td>
            <td class="tal pl10"><input type="text" name = "phone" class="ipt w184" value = "<?php echo $datas['phone']?>" required></td>
        </tr>
        <tr>
            <td class="tar"><label for="">QQ：</label></td>
            <td class="tal pl10"><input type="text" name = "qq" class="ipt w184" value = "<?php echo $datas['qq']?>"></td>
        </tr>
        <tr>
            <td class="tar"><label for="">微信：</label></td>
            <td class="tal pl10"><input type="text" name = "wechat" class="ipt w184" value = "<?php echo $datas['webchat']?>"></td>
        </tr>
        <tr>
            <td class="tar"><label for=""></label>其他联系方式：</td>
            <td class="tal pl10"><input type="text" name = "othercontact" class="ipt w184" value = "<?php echo $datas['other_contact']?>"></td>
        </tr>
        <tr>
            <td class="tar"><label for="">* 地址：</label></td>
            <td class="tal pl10"><input type="text" name = "address" class="ipt w360" value = "<?php echo $datas['address']?>" required></td>
        </tr>
        <tr>
            <td class="tar"><label for="">创建时间：</label></td>
            <td class="tal pl10"><?php echo $datas['created']?></td>
        </tr>
        <tr>
            <td class="tar vat"><label for=""> 附件：</label></td>
            <td class="tal pl10">
                <div id="uploader" class="wu-example">
                    <!--用来存放文件信息-->
                    <div class="btns">
                        <div id="picker" class="btn-white">选择文件</div>
                        <div id="ctlBtn" class="btn-white">开始上传</div>
                    </div>

                    <?php if(isset($files)):?>
                        <div id="thelist" class="uploader-list tr-uploader pfile">
                        <?php foreach($files as $key => $file):?>
                    <div class="item ufile">
                        <h4 class="info"><?php echo $file['filename']?></h4>
                        <p class="state">已上传</p>
                        <span class="remove-this  delete" data-id = "<?php echo $file['id']?>">×</span>
                    </div>
                        <?php endforeach;?>
                        </div>
                    <?php endif;?>

                </div>
            </td>
        </tr>
        <tr>
            <td class="tar"><label for="">彩种类别：</label></td>
            <td class="tal pl10">
            	<select name="lid">
            		<option value="0" <?php if ($datas['lid'] == 0) {?>selected<?php }?>>不限</option>
            		<?php foreach ($lids as $l => $lid) {?>
            		<option value="<?php echo $l?>" <?php if ($l == $datas['lid']) {?>selected<?php }?>><?php echo $lid['cname']?></option>
            		<?php }?>
            	</select>
            </td>
        </tr>
        </tbody>
    </table>

</div>
<div class="audit-detail-btns mt20 ml10bf">
    <a href="javascript:;" class="btn-blue  conservation" type = "submit">保存</a>
</div>
</form>
<script src="/source/js/webuploader.min.js"></script>
<script>

    var $ = jQuery,
        $list = $('#thelist'),
        $btn = $('#ctlBtn'),
        state = 'pending',
        str,
        uploader;
    $(function(){
        var id = $('.partner').data('id'),
            pid = $('.partner').data('pid');
        $('.conservation').click(function(){
            if($('input[name = bid]').val() == '')
            {
                alert('编号不能为空');
                return false;
            }
            if($('input[name = phone]').val() == '')
            {
                alert('电话不能为空');
                return false;
            }
            if($('input[name = address]').val() == '')
            {
                alert('地址不能为空');
                return false;
            }
            $.ajax({
                type : "post",
                url : "/backend/betStation/BetStationUpload",
                data : $('#betStationEidt').serialize(),
                success:function(xhr){
                    var data = jQuery.parseJSON(xhr);
                    if(data.status == '00')
                    {
                        alert(data.msg);
                    }
                    else
                    {
                        window.location.href = "/backend/betStation/BetStationDetail?id="+id;
                    }
                }
            })

        });
        $('.delete').click(function(){
            var ids = $(this).data('id');
            $(this).attr('flag','1');
            $(this).parents('.ufile').attr('hidden','hidden');
            $(this).parents('.ufile').addClass('del');
            var jsonArray = [];
            $('.ufile').each(function(){
                var flag = $(this).find('.delete').attr('flag');
                var arr = {
                    id : ids
                }
                if(flag == 1)
                {
                    jsonArray.push(arr);
                }
            });
            var data = JSON.stringify(jsonArray);
            $('.conservation').click(function(){
                $.ajax({
                    type : "post",
                    url : "/backend/betStation/deleteFile",
                    data : {data:data},
                    success:function(){
                        window.location.href = "/backend/betStation/BetStationDetail?id="+id;
                    }
                });
            });
        });

        var BASE_URL = '/';
        var uploader = WebUploader.create({

            // swf文件路径
            swf: BASE_URL + 'caipiaoimg/v1.1/js/jUploader.swf',

            // 文件接收服务端。
            server: '/backend/betStation/uploadFile?id='+id+'&pid='+pid,

            // 选择文件的按钮。可选。
            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: '#picker',

            // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
            resize: false,
           //文件去重
            duplicate: undefined,
          //一次上传文件数量限制
            fileNumLimit: 5,

            fileSizeLimit: 1024 * 1024 * 10,
//
            fileSingleSizeLimit: 1024 * 1024 * 10,

            accept: {
                title: 'Image,Applications',
                extensions: 'pdf,jpg,jpeg,bmp,png,doc,docx,zip,xls,xlsx,txt',
                mimeTypes: 'image/*,  application/*, text/* '
            }
        });

        uploader.on( 'fileQueued', function( file ) {
            $list.append( '<div id="' + file.id + '" class="item">' +
            '<h4 class="info">' + file.name + '</h4>' +
            '<p class="state">等待上传...</p>' +
            '<span class="remove-this">&times;</span>'+
            '</div>' );



            // 移除等待上传的文件，并且移除DOM元素
            $( '#'+file.id ).on('click', '.remove-this', function() {
                uploader.removeFile(file, true);
                $(this).parents('.item').remove();
            })

        });


    uploader.on('error', function(handler) {

        if(handler == "Q_EXCEED_NUM_LIMIT"){
            alert("超出上传数量限制,文件一次最多上传5个");
        }
        if(handler == "F_DUPLICATE"){
            alert("不可上传相同文件");
        }

        if(handler == "Q_EXCEED_SIZE_LIMIT"){
            alert("文件大小不能超过10M");
        }

        if(handler == "Q_TYPE_DENIED"){
            alert("仅支持以下格式：PDF,DOC,DOCX,TXT,XLS,XLSX,JPG,PNG,BMP,JPEG,ZIP");
        }

    });

        // 文件上传过程中创建进度条实时显示。
        uploader.on( 'uploadProgress', function( file, percentage ) {
            var $li = $( '#'+file.id ),
                $percent = $li.find('.progress .progress-bar');

            // 避免重复创建
            if ( !$percent.length ) {
                $percent = $('<div class="progress progress-striped active">' +
                '<div class="progress-bar" role="progressbar" style="width: 0%">' +
                '</div>' +
                '</div>').appendTo( $li ).find('.progress-bar');
            }

            $li.find('p.state').text('上传中');

            $percent.css( 'width', percentage * 100 + '%' );

        });



        uploader.on( 'uploadSuccess', function( file, data) {
                $( '#'+file.id ).find('.state').text('已上传');
                str = "<input type='hidden' class='filename' name='filename[]' value='"+data.filename+"'>";
                str += "<input type='hidden' name='filepath[]' value='"+data.filepath+"'>";
                $("#uploader").before(str);
        });

        uploader.on( 'uploadError', function( file ) {
            $( '#'+file.id ).find('p.state').text('上传出错');
        });

        uploader.on( 'uploadComplete', function( file ) {
            $( '#'+file.id ).find('.progress').fadeOut();
        });

        $btn.on( 'click', function() {
            $.ajax({
                type: "post",
                data: {id:id},
                url: '/backend/betStation/getNum',
                success: function (xhr) {
                    var data = jQuery.parseJSON(xhr),
                        length1 = $('.del').length,
                        length2 = $('.item').length,
                        length3 = $('.ufile').length,
                        num = data.num,
                        length;
                    length = num - length1 + length2 - length3;
                    if(length > 5)
                    {
                        alert('最多只有5个附件！');
                        location.reload();
                        return false;
                    }
                    else
                    {
                        if ( state === 'uploading' ) {
                            uploader.stop();
                        } else {
                            uploader.upload();
                        }
                    }
                }
            });

        });

    });
</script>
