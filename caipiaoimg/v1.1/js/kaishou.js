$(function() {
        var ie6=!-[1,]&&!window.XMLHttpRequest;
        if(ie6){
            $('#tip-form').css({top: ($(window).height() - $('#tip-form').innerHeight())}).show();
            $(window).on('scroll', function(){
                var cookies = document.cookie;
                var start = cookies.indexOf("kaishou");
                start = cookies.indexOf("=", start) + 1;
                var end = cookies.indexOf(";", start);
                if(end == -1){
                    end = cookies.length;
                }
                var value = unescape(cookies.substring(start, end));
                if(value == 0){
                    setTimeout(function(){
                        $('#tip-form').css({top: ($(window).scrollTop() + $(window).height() - $('#tip-form').innerHeight())})
                    }, 100)
                    function setCookie(name, value, iDay)
                    {
                        var oDate=new Date();
                        oDate.setDate(oDate.getDate()+iDay);
                        document.cookie=name+'='+encodeURIComponent(value)+';expires='+oDate;
                    }
                    setCookie('kaishou','1',1);
                    //$.cookie('kaishou','1',{ expires: 60*60*24 });
                }

            })
        }else {
            var cookies = document.cookie;
            var start = cookies.indexOf("kaishou");
            start = cookies.indexOf("=", start) + 1;
            var end = cookies.indexOf(";", start);
            if(end == -1){
                end = cookies.length;
            }
            var value = unescape(cookies.substring(start, end));
            if(value == 0){
                $.ajax({
                    type: 'post',
                    url:  '/pop/getKaishou',
                    success: function(response) {
                        $('body').append(response);
                        setTimeout(function () {
                            $('#tip-form').show().animate({bottom: 0}, 800);
                        }, 400)
                        //function setCookie(name, value, iDay)
                        //{
                        //    var oDate=new Date();
                        //    oDate.setDate(oDate.getDate()+iDay);
                        //    document.cookie=name+'='+encodeURIComponent(value)+';expires='+oDate;
                        //}
                        //setCookie('kaishou','1',60*60*24);
                        $.cookie('kaishou','1',{ expires: 1 });
                    }
                });

            }
        }
    $('.slide-wrap').on('click', 'li:nth-child(1)', function(){
        if($('.pop-kstx').length > 0){
            $('.pop-kstx').hide().addClass('pop-kstx-alc').removeClass('pop-kstx-result').show();
            $('.pop-mask').removeClass('hidden');
        }else{
            $.ajax({
                type: 'post',
                url:  '/pop/getKaishou',
                success: function(response) {
                    $('body').append(response);
                    $('.pop-kstx').addClass('pop-kstx-alc').show();
                    $('.pop-mask').removeClass('hidden');
                }
            })
        }
        return false;


})
});