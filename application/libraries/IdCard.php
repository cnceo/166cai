<?php

/**
 * Copyright (c) 2014,�Ϻ�������������Ƽ��ɷ����޹�˾
 * ժ    Ҫ: ���֤��֤
 * ��    ��: lizq
 * �޸�����: 2014-11-25 
 */

class IdCard
{
    //��֤���֤
    static function checkIdCard($idcard)
    {
        if(empty($idcard)){
            return false;
        }
        
        $City = array(
            11=>"����",12=>"���",13=>"�ӱ�",14=>"ɽ��",15=>"���ɹ�",
            21=>"����",22=>"����",23=>"������",
            31=>"�Ϻ�",32=>"����",33=>"�㽭",34=>"����",35=>"����",36=>"����",37=>"ɽ��",
            41=>"����",42=>"����",43=>"����",44=>"�㶫",45=>"����",46=>"����",
            50=>"����",51=>"�Ĵ�",52=>"����",53=>"����",54=>"����",
            61=>"����",62=>"����",63=>"�ຣ",64=>"����",65=>"�½�",
            71=>"̨��",81=>"���",82=>"����",91=>"����"
        );
        
        $iSum = 0;
        $idCardLength = strlen($idcard);
        
        //������֤
        if(!preg_match('/^\d{17}(\d|x)$/i',$idcard) and !preg_match('/^\d{15}$/i',$idcard))
        {
            return false;
        }

        // 15λ�����֤У�������⣬����Ʒ��ͨ��֮���ٶ�15λ�����֤������� by wangyi@2345.com 2014-09-18
        if ($idCardLength != 18)
        {
            return false;
        }
        
        //������֤
        if(!array_key_exists(intval(substr($idcard,0,2)),$City))
        {
           return false;
        }
        // 15λ���֤��֤���գ�ת��Ϊ18λ
        if ($idCardLength == 15)
        {
            $sBirthday = '19'.substr($idcard,6,2).'-'.substr($idcard,8,2).'-'.substr($idcard,10,2);
            try {
              $d = new DateTime($sBirthday);
            } catch(Exception $e) {
                return false;
            }
            $dd = $d->format('Y-m-d');
            if($sBirthday != $dd)
            {
                return false;
            }
            $idcard = substr($idcard,0,6)."19".substr($idcard,6,9);//15to18
            $Bit18 = self::getVerifyBit($idcard);//�����18λУ����
            $idcard = $idcard.$Bit18;
        }
        // �ж��Ƿ����2078�꣬С��1900��
        $year = substr($idcard,6,4);
        if ($year<1900 || $year>2078 )
        {
            return false;
        }

        //18λ���֤����
        $sBirthday = substr($idcard,6,4).'-'.substr($idcard,10,2).'-'.substr($idcard,12,2);
        $d = new DateTime($sBirthday);
        $dd = $d->format('Y-m-d');
        if($sBirthday != $dd)
        {
            return false;
        }

        //���֤����淶��֤
        $idcard_base = substr($idcard,0,17);
        if(strtoupper(substr($idcard,17,1)) != self::getVerifyBit($idcard_base))
        {
           return false;
        }
        return array(
            'idcard'=> $idcard, 
            'city' => $City[substr($idcard,0,2)]
        );
    }

    // �������֤У���룬���ݹ��ұ�׼GB 11643-1999
    static function getVerifyBit($idcard_base)
    {
        if(strlen($idcard_base) != 17)
        {
            return false;
        }
        //��Ȩ����
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //У�����Ӧֵ
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ($i = 0; $i < strlen($idcard_base); $i++)
        {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
    }

    // �������֤�����ж��Ƿ����ָ������
    static function isEnoughAgeByIdCard( $idcard, $ageYear = 18 )
    {
        $sBirthday = substr($idcard,6,4) . substr($idcard,10,2) . substr($idcard,12,2);
        try {
        $d = new DateTime($sBirthday);
        } 
        catch(Exception  $e)
        {
            return false; //��ʽ����
        }

        $beforeAgeDate = date( 'Ymd', strtotime('-' . $ageYear . ' year' ) );
        
        if( intval($sBirthday) >= intval( $beforeAgeDate ) )
        {
            return false; // ���䲻��
        }
        return true;
    }
}