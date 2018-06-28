<?php

/* *
 * 配置文件
 * 版本：1.0
 * 日期：2016-11-28
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 */

//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
//商户编号是商户在连连钱包支付平台上开设的商户号码，为18位数字，如：201306081000001016
$llpay_config['oid_partner'] = '201605111000852692';

//秘钥格式注意不能修改（左对齐，右边有回车符）  商户私钥，通过openssl工具生成,私钥需要商户自己生成替换，对应的公钥通过商户站上传
$llpay_config['RSA_PRIVATE_KEY'] ='-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQDrjxRvweJCWvIKNi5OHEXgS3wgbnQ1tif+cUwAKkGGVFhPfmr5
2ETol07/audGVFgGEoi7ZROvmxdb9KMDTgP6OgZxPIJyxZsiKaI3jFCOfx9shADv
obntYt4UesZaY4brf1sI+FdK0Yu9VMV22fKlaj41O6A+eM5ZDwWeWDhqIwIDAQAB
AoGBAKZGgEGHFaSLN/EXX8ZJVNXH0t29uhAz/bUw2ln/efNNVG0AqpikHbglHmFT
X9+YJ+5ZZOUKq0O48Vs6q1ro1gqP72vpqi04/jzoy3jJShmZ7nmkW+/kATT9Lus0
UMTvsWbZXL4vD2Dy0+k7v77jmf27/xG4us1ucYSZMQ6Qb2+hAkEA+gIUaKWvCXXa
QoTM06Ag29YZEbJgGq8iy+iEZ63RtWPf7H1S9pv31Zc9UaRxGkiBZltZTE5pMhiY
wvIrg6RB8wJBAPE0WOGpyDo7LFayXKfiMzYrj9lcJ+bLFJ5z2RHlKBLjpv4H3VTH
ubOpe7beF8l6C14cUuhT2iDFN8vK+qCpExECQFS+tbpPR0j2qPhZWbD2m4zJQxAr
ncYNzca+13rpgadx5mqchK3Raq39KSzuh+Q35Z0To+5oueHgUo/qVPO3jx8CQQDv
p69YKDWFhh271mQxepKflBDNSr9qlQTbmwdmvGVgv0jAxlenUPq2BAOj4m+IA/cf
fszxgb8NKGcT2Y3D67nBAkBpAmjdgRZoaysV6305F8b0VxvFHDD9RnnX2QQUqgIA
rxK7y/C+xgLUm5UZP9zl/z9KwL71Rz/J+3vrrnn/kSpt
-----END RSA PRIVATE KEY-----';	

//连连银通公钥
$llpay_config['LIANLIAN_PUBLICK_KEY'] ='-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCSS/DiwdCf/aZsxxcacDnooGph3d2JOj5GXWi+
q3gznZauZjkNP8SKl3J2liP0O6rU/Y/29+IUe+GTMhMOFJuZm1htAtKiu5ekW0GlBMWxf4FPkYlQ
kPE0FtaoMP3gYfh+OwI+fIRrpW3ySn3mScnc6Z700nU/VYrRkfcSCbSnRwIDAQAB
-----END PUBLIC KEY-----';	

//安全检验码，以数字和字母组成的字符
$llpay_config['key'] = '201408071000001539_sahdisa_20141205';

//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑


//签名方式 不需修改
$llpay_config['sign_type'] = strtoupper('RSA');


//字符编码格式 目前支持 gbk 或 utf-8
$llpay_config['input_charset'] = strtolower('utf-8');
