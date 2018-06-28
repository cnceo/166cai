
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

// 滚动吸顶table
!function($) {
    function ScrollTable (el, opt) {
    var defaults = {
        overflowHeight: '400px',
        width: [100, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100],
        leftFixed: 1
    }
    this.table = el
    this.thead = this.table.find('.cp-table-thead')
    this.tbody = this.table.find('.cp-table-tbody')
    this.options = $.extend({}, defaults, opt)
    }

    ScrollTable.prototype = {
    constructor: ScrollTable,
    init: function () {
        this.setWidth(this.thead.find('tr').eq(0).find('th'))
        this.setWidth(this.tbody.find('tr').eq(0).find('td'))

        this.table.find('.cp-table-overflow').css({'maxHeight': this.options.overflowHeight})

        var tpl = $('<div class="cp-table-fixed"><div class="cp-table-thead"><table><thead></thead></table></div></div>')
        this.appendTpl($('.cp-table-main > .cp-table-thead tr'), 'thead', tpl)
        tpl.append('<div class="cp-table-tbody"><table><tbody></tbody></table></div>')
        this.appendTpl($('.cp-table-overflow .cp-table-tbody tr'), 'tbody', tpl)
        $('.cp-table-main').append(tpl)
        var lWidth = null
            _this = this
        $(this.options.width).each(function (i, it) {
        if (i >= _this.options.leftFixed) return
        lWidth += (it + 1)
        })

        $('.cp-table-fixed').css({'width': lWidth + 'px'})

        var fixedTable = this.table.find('.cp-table-fixed'),
            fixedTableTbody = this.table.find('.cp-table-fixed .cp-table-tbody'),
            fixedTableThead = this.table.find('.cp-table-main > .cp-table-thead').find('table'),
            mainTable = this.table.find('.cp-table-main')

        fixedTable.hide()
        $('.cp-table-overflow').on('scroll', function () {
        if (this.scrollLeft > 0) {
            fixedTableThead.css({'display': 'block', 'position': 'relative', 'left': -$(this).scrollLeft() + 'px'})
            fixedTable.show().height(mainTable.height() - 15)
            if (this.scrollTop > 0) {
            fixedTableTbody.css({'position': 'relative', 'top': -$(this).scrollTop() + 'px'})
            } else {
            fixedTableTbody.css({'position': 'relative', 'top': 0})
            }
        } else {
            fixedTableThead.css({'display': 'block', 'position': 'relative', 'left': 0})
            fixedTable.hide()
        }
        })
    },
    setWidth: function (arr) {
        var _this = this
        arr.each(function(i, it) {
        $(it).html('<div style="width: ' + _this.options.width[i] + 'px">' + $(it).html() + '</div>')
        })
    },
    appendTpl: function (targetEl, el, tpl) {
        var _this = this
        targetEl.each(function (idx, item) {
        tpl.find(el).append('<tr></tr>')
        $(item).find('th, td').each(function (i, it) {
            if (i >= _this.options.leftFixed) return
            tpl.find(el).find('tr').eq(idx).append($(it).clone())
        })
        })
    }
    }

    $.fn.ScrollTable = function (options) {
        new ScrollTable(this, options).init()
    }
}(jQuery)