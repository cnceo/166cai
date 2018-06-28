<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：客户端管理&nbsp;&gt;&nbsp;<a href="/backend/Appconfig/banner?platform=<?php echo $platform;?>"><?php echo $platform;?>配置</a></div>
<div class="mod-tab mt20">
    <div class="mod-tab-hd remind-hd">
        <ul>
            <?php $this->load->view("appconfig/tag", array('platform' => $platform, 'tag' => 'webBanner')) ?>
        </ul>
        <!-- <div class="remind-infor">注意：banner图文件名不支持中文字符</div> -->
    </div>
    <div class="mod-tab-bd">
        <ul>
            <li style="display: block">
                <form action="" method="post" id="banner_form">
                    <div class="data-table-list mt10">
                        <table>
                            <colgroup>
                                <col width="5%" />
                                <col width="27%" />
                                <col width="28%" />
                                <col width="15%" />
                                <col width="15%" />
                                <col width="10%" />
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>序号</th>
                                    <th>图片</th>
                                    <th>成功页彩种</th>
                                    <th>链接</th>
                                    <th>点击跳转原生页（高优先级）</th>
                                    <th>跳转至彩种</th>
                                </tr>
                            </thead>
                            <tbody id="pic-table">
                                <?php for ($i = 0; $i < 4; $i++) {?>
                                <tr>
                                    <td>
                                        <?php echo $i + 1;?>
                                    </td>
                                    <td>
                                        <div class="btn-white file">选择文件</div>
                                        <div class="btn-white upload" data-index="<?php echo $i?>">开始上传</div>
                                        <input type="hidden" name="info[<?php echo $i?>][path]" id="path_<?php echo $i?>" value="<?php echo $info[$i]['imgUrl']?>">
                                        <div id="imgdiv0" class="imgDiv"><img id="imgShow<?php echo $i?>" src="<?php echo $info[$i]['imgUrl']?>" width="100" height="50" /></div>
                                    </td>
                                    <td>
                                        <?php $lidArr = explode(',', $info[$i]['clid']);  $count = 0; foreach ($lotteryInfo as $lid => $lname) {?>
                                        <input type="checkbox" class="clid" data-type="lid" <?php if(in_array($lid, $lidArr)){echo "checked"; }?> value="<?php echo $lid; ?>" name="info[<?php echo $i?>][clid][]"><?php echo $lname; ?>
                                        <?php if (in_array($count, array(3, 8))) {?>
                                        <?php }
                                        $count++;
                                        }?>
                                    </td>
                                    <td>
                                        <input type="text" class="ipt tac w130" name="info[<?php echo $i?>][webUrl]" value="<?php echo $info[$i]['webUrl']; ?>">
                                    </td>
                                    <td>
                                        <select class="selectList w98" id="" name="info[<?php echo $i?>][appAction]">
                                            <option value="" <?php if($info[$i]['appAction'] === ''){echo 'selected';} ?> >不使用</option>
                                            <option value="bet" <?php if($info[$i]['appAction'] === 'bet'){echo 'selected';} ?>>投注页</option>
                                            <option value="email" <?php if($info[$i]['appAction'] === 'email'){echo 'selected';} ?>>绑定邮箱</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="selectList w98" id="" name="info[<?php echo $i?>][tlid]">
                                            <option value="" <?php if($info[$i]['tlid'] === ''){echo 'selected';} ?> >不使用</option>
                                            <?php foreach ($lotteryInfo as $lid => $lname):?>
                                            <option value="<?php echo $lid; ?>" <?php if($info[$i]['tlid'] == $lid){echo 'selected';} ?> ><?php echo $lname; ?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </td>
                                </tr>
                                <?php }?>
                            </tbody>
                        </table>
                        <div class="tac">
                            <a class="btn-blue mt20 submit">保存并上线</a>
                        </div>
                    </div>
                    <input type="hidden" name="platform" value="<?php echo $platform; ?>">
                </form>
            </li>
        </ul>
    </div>
</div>
<script src="/source/js/webuploader.min.js"></script>
<script>
    $(function() {
        // 表单提交
        $(".submit").click(function(){
            $("form").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").submit();
        })

        // 初始化
        var uploader = WebUploader.create({
            swf: '/caipiaoimg/v1.1/js/jUploader.swf',
            pick: '.file',
        });

        // 上传
        $(".upload").click(function(){
            var platform = $('input[name="platform"]').val();
            uploader.options.server = "/backend/Appconfig/uploadbanner/" + platform + "/" + $(this).data('index');
            var files = uploader.getFiles();
            var index = files.length - 1;
            // 分割文件名
            if(!(/^\w+\.\w+$/.test(files[index].name))){
                alert('文件名只能包含字母和数字！');
                uploader.removeFile(files);
                return false;
            }
            uploader.upload();
        })

        // 上传成功
        uploader.on( 'uploadSuccess', function( file, data) {
            $("#imgShow" + data.index).attr('src', data.path + data.name);
            $("#path_" + data.index).val(data.path + data.name);
        });

        // 同彩种切换
        $('.clid').click(function(){
            if ($(this).attr('checked')) {
                var val = $(this).val();
                var type = $(this).data('type');
                $(".clid[value='"+val+"'][data-type='"+type+"']").removeAttr('checked');
                $(this).attr('checked', 'checked');
            }
        })
    });
</script>
