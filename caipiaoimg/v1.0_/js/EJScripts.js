function ReplaceNull(source) {
    var result = "";

    if (source == null) {
        result = "";
    }
    else {
        result = source;
    }

    return result;
}

function DoSwitchTab(clickTab) {
    var currentTitleTab = clickTab;
    var currentTitleContainer = currentTitleTab.parentNode;
    var currentTitleTabsJQ = $(currentTitleContainer).children();
    var currentTitleIndex = 0;
    var totalTabCount = currentTitleTabsJQ.length;
    for (var tabCount = 0; tabCount < totalTabCount; tabCount++) {
        if (currentTitleTabsJQ[tabCount] == currentTitleTab) {
            currentTitleIndex = tabCount;
            $(currentTitleTab).attr("class", "selected");
        }
        else {
            $(currentTitleTabsJQ[tabCount]).attr("class", "");
        }
    }

    var currentContentContainer = $(currentTitleContainer).next();
    var currentContentsJQ = currentContentContainer.children();
    var totalContentCount = currentContentsJQ.length;
    for (var tabCount = 0; tabCount < totalContentCount; tabCount++) {
        if (tabCount == currentTitleIndex) {
            $(currentContentsJQ[tabCount]).show();
        }
        else {
            $(currentContentsJQ[tabCount]).hide();
        }
    }
}

function ClearContent(destControl) {
    destControl.value = "";
}

function ChangeToPassword(destControl) {
    destControl.value = "";
    $(destControl).attr("type", "password");
}

function GetTimeFromStamp(timestamp) {
    function padd(num) {
        return ('0' + num).slice(-2);
    }
    var datetime = new Date(timestamp);
    return datetime.getFullYear() + '-' + padd((datetime.getMonth() + 1)) + '-' + padd(datetime.getDate()) + ' ' + padd(datetime.getHours()) + ':' + padd(datetime.getMinutes()) + ':' + padd(datetime.getSeconds());
}

function GetLotCNName(lotteryId) {
    var result = "";

    switch (lotteryId) {
        case 23529:
            {
                result = "大乐透";
                break;
            }
        case 51:
            {
                result = "双色球";
                break;
            }
        case 54:
            {
                result = "11选5";
                break;
            }
        case 42:
            {
                result = "竞彩足球";
                break;
            }
        default:
            {
                result = "大乐透";
                break;
            }
    }

    return result;
}

function GetOrderStatus(status) {
    var result = "";

    switch (status) {
        case 100:
            {
                result = "未付款";
                break;
            }
        case 400:
            {
                result = "出票中";
                break;
            }
        case 500:
            {
                result = "出票成功";
                break;
            }
        case 501:
            {
                result = "出票失败";
                break;
            }
        case 600:
            {
                result = "出票取消";
                break;
            }
        case 1000:
            {
                result = "未中奖";
                break;
            }
        case 2000:
            {
                result = "已中奖";
                break;
            }
        default:
            {
                result = "出票中";
                break;
            }
    }

    return result;
}

function GetTransType(type) {
    var result = "";

    switch (type) {
        case 0:
            {
                result = "支付";
                break;
            }
        case 1:
            {
                result = "平台补款";
                break;
            }
        case 2:
            {
                result = "平台扣款";
                break;
            }
        case 3:
            {
                result = "平台奖励";
                break;
            }
        case 4:
            {
                result = "团购奖励";
                break;
            }
        case 5:
            {
                result = "平台嘉奖";
                break;
            }
        case 6:
            {
                result = "平台红包";
                break;
            }
        case 7:
            {
                result = "充值";
                break;
            }
        case 8:
            {
                result = "提款";
                break;
            }
        case 9:
            {
                result = "提成";
                break;
            }
        case 10:
            {
                result = "手工冻结";
                break;
            }
        case 11:
            {
                result = "彩票返奖";
                break;
            }
        case 12:
            {
                result = "彩票充值";
                break;
            }
        case 13:
            {
                result = "系统解冻";
                break;
            }
        case 14:
            {
                result = "撤单返款";
                break;
            }
        default:
            {
                result = "出票中";
                break;
            }
    }

    return result;
}

function ForFurtherUsage() {
    //    var post_data = {
    //        IsuseID: "",
    //        IsuseEndTime: "",
    //        PlayTypeID: "",
    //        TotalShare: "",
    //        BuyShare: ""
    //    };

    //    $.ajax({
    //        type: "post",
    //        url: "/Services/TestService.ashx",
    //        data: post_data,
    //        cache: false,
    //        async: false,
    //        dataType: "json",
    //        success: function (result) {
    //            jsonData = resultInfo;
    //            if (parseInt(result.error, 10) > 0) {
    //                location.href = "/Home/Room/UserBuySuccess.aspx?LotteryID=" + Lottery.LotID + "&Type=" + investType + "&Money=" + BuyMoney + "&SchemeID=" + result.error;

    //                return;
    //            } else {
    //                if (result.error == "-107") {//余额不足
    //                    //alert(-107);
    //                    location.href = "/Home/Room/OnlinePay/Default.aspx?BuyID=" + result.buyID;

    //                    return;
    //                }
    //                else if (result.msg == "请重新登录！") {
    //                    alert(result.msg);

    //                    $.ajax({
    //                        type: "POST",
    //                        url: "/ajax/UserLogin.ashx",
    //                        data: "action=loginout",
    //                        timeout: 20000,
    //                        cache: false,
    //                        async: false,
    //                        dataType: "json",
    //                        complete: function () { location.href = "../UserLogin.aspx"; }
    //                    });

    //                    return;
    //                }
    //                msg(result.msg);
    //            }
    //        },

    //        error: function (XMLHttpRequest, textStatus, errorThrown) {
    //            alert("error");
    //        },

    //        complete: function (XMLHttpRequest, SuccessOrErrorthrown) {
    //            Lottery.ChangeSubmitButtonState(true);
    //        }
    //    });
}
