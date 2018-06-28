(function() {
    window.cx || (window.cx = {});

    var Order = cx.Order = (function() {
        var me = {};

        me.getStatus = function(status, returnFlag) {
            status = parseInt(status, 10);
            var cnStatus = 'δ֪';
            if (status <= 100) {
                cnStatus = 'δ����';
            } else if (status == 200) {
                cnStatus = '����Ʊ';
            } else if (status <= 300 || status == 501) {
                cnStatus = '��Ʊ��';
            } else if (status == 500) {
                cnStatus = '�ȴ�����';
            } else if (status == 600) {
                if (returnFlag == 2) {
                    cnStatus = 'ϵͳ����';
                } else {
                    cnStatus = '�û�����';
                }
            } else if (status == 1000) {
                cnStatus = 'δ�н�';
            } else if (status == 1500) {
                cnStatus = '�󽱴����';
            } else if (status == 2000) {
                cnStatus = '�н�';
            } else if (status == 5000) {
                cnStatus = '���ɽ�'
            }
            return cnStatus;
        };

        return me;
    })();

})();
