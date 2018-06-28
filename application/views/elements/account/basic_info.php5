<style type="text/css">
.avatar-helper {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: none;
}
*+html .avatar-helper{
     position:static;
}
.avatar-mask {
    opacity: .5;
    filter: alpha(opacity=0.5);
    background: #000;
}
.avatar-form {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    overflow: hidden;
}
.avatar-file {
    width: 100%;
    height: 100%;
    display: block;
    font-size: 48px;
    opacity: 0;
    filter: alpha(opacity=0);
}
.avatar-save {
    float: left;
    width: 50%;
}
.avatar-cancel {
    float: left;
    width: 50%;
}
</style>
<script type="text/javascript">
$(function() {
    var avatarUrl = '';
    var $container = $('.userIinfoImg');
    var $img = $('#avatar-img');
    var $mask = $('.avatar-mask');
    var $upload = $('.avatar-upload');
    $container.hover(function() {
        $mask.show();
        $upload.show();
    }, function() {
        $mask.hide();
        $upload.hide();
    });
    $('#avatar-iframe').load(function() {
        location.href = location.href;
    });
    $('.avatar-file').change(function(e) {
        if (e.target.value == '') {
            return ;
        }
        var filePath = e.target.value.split('.');
        if ($.inArray(filePath[filePath.length - 1].toLowerCase(), ['jpg', 'png', 'gif']) == -1) {
            return;
        }
        $(this).prop('form').submit();
    });
    $('.avatar-save').click(function() {
        if (avatarUrl == '') {
            return ;
        }
        cx.ajax.post({
            url: cx.url.getPassUrl('update/portrait.do'),
            data: {
                isToken: 1,
                portrait: avatarUrl
            },
            success: function(response) {
                if (response.code == 0) {
                    new cx.Alert({
                        content: '头像保存成功',
                        confirmCb: function() {
                            location.href = location.href;
                        }
                    });
                } else {
                    new cx.Alert({
                        content: response.msg
                    });
                }
            }
        });
    });
    $('.avatar-cancel').click(function() {
        avatarUrl = '';
        $img.attr('src', '/caipiaoimg/v1.0/images/bg/userImg.gif');
        $container.removeClass('uploaded');
    });
});
</script>
<div class="userIinfo">
    <div class="userIinfoImg">
        <?php if (empty($account['portrait'])): ?>
        <img id="avatar-img" src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/userImg.gif');?>" alt="头像" width="126" height="126" />
        <?php else: ?>
        <img id="avatar-img" src="<?php echo $fileUrl . 'avatar/' . $account['portrait'] . '?t=' . time() . rand(); ?>" alt="头像" width="126" height="126" />
        <?php endif; ?>
        <iframe id="avatar-iframe" name="avatar-preview" style="display: none"></iframe>
        <div class="avatar-helper avatar-mask"></div>
        <div class="avatar-helper avatar-upload">
            <div style="position: absolute; bottom: 0; width: 100%; line-height: 37px; height: 37px; background: #e4e9ec;">
                <p>上传头像</p>
                <form class="avatar-form" method="post" target="avatar-preview" action="<?php echo $avatarUrl; ?>images/avatar/upload" enctype="multipart/form-data" >
                    <input type="file" class="avatar-file" name="dd" />
                    <input type="hidden" style="display: none;" name="token" value="<?php echo $token; ?>" />
                </form>
            </div>
        </div>
        <div class="avatar-helper avatar-op">
            <p class="clearfix" style="position: absolute; bottom: 0; width: 100%; line-height: 37px; height: 37px; background: #e4e9ec;">
                <a class="wordRed avatar-save">保存</a>
                <a class="wordBlue avatar-cancel">取消</a>
            </p>
        </div>
    </div>
    <div class="userIinfoName">
        <h3><?php if (!empty($account['suggestDisplayName'])) echo $account['suggestDisplayName']; ?></h3>
        <h4></h4>
        <ul>
        <li class="userIconPhone <?php if (!empty($account['mobile'])): ?>userIconLight<?php endif; ?>">手机</li>
            <li class="userIconEmail <?php if (!empty($account['email'])): ?>userIconLight<?php endif; ?>">邮箱</li>
            <li class="userIconCard <?php if (!empty($account['identification'])): ?>userIconLight<?php endif; ?>">身份证</li>
            <li class="userIconWeixin">微信</li>
            <li class="userIconQQ">QQ</li>
        </ul>
    </div>
    <div class="fr">
        <h1>￥<?php echo number_format($wallet['amount'], 2); ?></h1>
        <h2>冻结：￥<?php echo number_format($wallet['tkFrozenAmount'] + $wallet['sgFrozenAmount'], 2); ?></h2>
        <p class="withDrawals"><a href="<?php echo $baseUrl; ?>account/withdraw">提款</a></p>
        <p class="topUp"><a href="<?php echo $baseUrl; ?>account/recharge">充值</a></p>  
    </div>
</div>
