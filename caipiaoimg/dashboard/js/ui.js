
// tab切换
/*$(".tab-nav li").bind("click", function(){
 var i = $(this).index();
 $(this).addClass('active').siblings().removeClass('active');
 $(this).parents(".tab-nav").next(".tab-content:eq(0)").find(".item").eq(i).show().siblings().hide();
 })*/

//设置frame高度
function setFrameHeight() {
    if ($(".frame-column").length != 0) {
        $(".frame-column").css("height", $(document).height() - 62);
    }
}

$(window).bind("resize load", function () {
    setFrameHeight();
})

// 弹层foo
function popdialog(obj) {
    $(".pop-mask").hide().height($(document).height()).show();
    $('.pop-dialog').hide();
    $("#" + obj).css({
        display: "inline",
        marginTop: -($("#" + obj).outerHeight()) / 2,
        marginLeft: -($("#" + obj).outerWidth()) / 2
    });
    $(".pop-close,.pop-cancel").bind("click", function () {
        closePop();
    })
}
function closePop() {
    $(".pop-dialog").hide();
    $(".pop-mask").hide();
    $(".pop-foot").show();
}

function dataPicker(_options)
{
    _commonOptions = {
        dateFmt:'yyyy-MM-dd HH:mm:ss',
        isShowClear:true,
        readOnly:true,
        maxDate:'2035-01-01 00:00:00',
        errDealMode:-1,
        minDate:'2014-11-01 00:00:00',
        startDate:'%y-%M-%d 00:00:00',
        onpicked:function(){$(".Wdate1").blur();},
        oncleared:function(){$(".Wdate1").blur();},
        changed:function(){$(".Wdate1").blur();}
    };
    if(_options)
    {
        _commonOptions = extend(_commonOptions,_options,true);
    }
    WdatePicker(_commonOptions);
}

function extend(des, src, override){
    if(src instanceof Array){
        for(var i = 0, len = src.length; i < len; i++)
             extend(des, src[i], override);
    }
    for( var i in src){
        if(override || !(i in des)){
            des[i] = src[i];
        }
    } 
    return des;
}

function checkAll(clickele,stsele)
{
    clickele.click(function(){
        if(!clickele.attr("checked"))
        {
            stsele.attr("checked",false);
        }
        else
        {
            stsele.attr("checked",true);
        }
        
    });
}
function getCheckVal(name)
{
    var s='';
    $('input[name="'+name+'"]:checked').each(function(){ 
           s+=$(this).val()+','; 
    }); 
    if (s.length > 0) 
    { 
        s = s.substring(0,s.length - 1);        
    } 
    else
    {
        alert('你还没有选择任何内容');
        return false;
    }    
    return s;
}
