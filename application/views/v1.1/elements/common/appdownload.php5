<?php 
    $bannerInfo = $cpbanner['ycfc'];
?>
<div class="l-concise-side">
    <?php if($bannerInfo): ?>
    <img src="/uploads/banner/<?php echo $bannerInfo['path']; ?>" width="240" height="350" alt="<?php echo $bannerInfo['title']; ?>">
    <?php endif; ?>
</div>
<input type="hidden" class="uid" name="type" value="<?php echo $this->uid ? $this->uid : 0; ?>"/>
<script>
$(function(){
    $('.btn-send').click(function(){
        var uid = $('.uid').val();
        var tel_num = $("#tel_num").val();
        if(tel_num == ''){
            $('.dl2msg-tips').html("请输入手机号码！");
            return;
        }else if(!(/^1[3-8]{1}\d{9}/.test(tel_num)) || tel_num.length != 11){
            $('.dl2msg-tips').html("请输入正确手机号码！");
            return;
        }          
        $.ajax({
            type: "POST",
            url: "/app_buy/sendSms",
            data: {
                'uid': uid,
                'tel_num':tel_num
                },
            dataType: "json",
            success: function (resp) {
                if (resp.ok) {
                    $('.dl2msg-tips').html("链接已发送，请注意查收！");              
                }
                else {
                    $('.dl2msg-tips').html(resp.msg);
                }
            }
        })
    })
});
</script>