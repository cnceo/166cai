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
                result = "����͸";
                break;
            }
        case 51:
            {
                result = "˫ɫ��";
                break;
            }
        case 54:
            {
                result = "11ѡ5";
                break;
            }
        case 42:
            {
                result = "��������";
                break;
            }
        default:
            {
                result = "����͸";
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
                result = "δ����";
                break;
            }
        case 400:
            {
                result = "��Ʊ��";
                break;
            }
        case 500:
            {
                result = "��Ʊ�ɹ�";
                break;
            }
        case 501:
            {
                result = "��Ʊʧ��";
                break;
            }
        case 600:
            {
                result = "��Ʊȡ��";
                break;
            }
        case 1000:
            {
                result = "δ�н�";
                break;
            }
        case 2000:
            {
                result = "���н�";
                break;
            }
        default:
            {
                result = "��Ʊ��";
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
                result = "֧��";
                break;
            }
        case 1:
            {
                result = "ƽ̨����";
                break;
            }
        case 2:
            {
                result = "ƽ̨�ۿ�";
                break;
            }
        case 3:
            {
                result = "ƽ̨����";
                break;
            }
        case 4:
            {
                result = "�Ź�����";
                break;
            }
        case 5:
            {
                result = "ƽ̨�ν�";
                break;
            }
        case 6:
            {
                result = "ƽ̨���";
                break;
            }
        case 7:
            {
                result = "��ֵ";
                break;
            }
        case 8:
            {
                result = "���";
                break;
            }
        case 9:
            {
                result = "���";
                break;
            }
        case 10:
            {
                result = "�ֹ�����";
                break;
            }
        case 11:
            {
                result = "��Ʊ����";
                break;
            }
        case 12:
            {
                result = "��Ʊ��ֵ";
                break;
            }
        case 13:
            {
                result = "ϵͳ�ⶳ";
                break;
            }
        case 14:
            {
                result = "��������";
                break;
            }
        default:
            {
                result = "��Ʊ��";
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
    //                if (result.error == "-107") {//����
    //                    //alert(-107);
    //                    location.href = "/Home/Room/OnlinePay/Default.aspx?BuyID=" + result.buyID;

    //                    return;
    //                }
    //                else if (result.msg == "�����µ�¼��") {
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
