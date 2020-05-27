<?php
/**
 * @file Dagong.class.php
 * @brief 打拱游戏规则
 * 
 * Copyright (C) 2020 Langr.Org
 * All rights reserved.
 * 
 * @package worker-demo
 * @author xxx <xxx@xxx.org> 2020/05/21 21:46
 * 
 * $Id$
 */

const TOTAL_POKER = 108;
const EVERYONE_POKER = 27;
/* 牌型类型：1 单张，2 对子/连对，3 3张/连3，4 炸弹/510k/通压牌 */
const POKER_TYPE1 = 1;
const POKER_TYPE2 = 2;
const POKER_TYPE3 = 3;
const POKER_TYPE4 = 4;

class Dagong 
{
    /* 桌座位顺序，每桌人员按加入时间逆时针排序 */
    private $room_uids = [0=>'', 1=>'', 2=>'', 3=>''];
    private $zhuang_uid = '';
    private $mingji_uid = '';

    /**
     */
    private $poker = '<>ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz<>ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    private $pk_arr = ['<', '>', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 
            'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 
            'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 
            'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 
            'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 
            'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
    /* 牌点 */
    private $pk_point = ['<' => 3,'>' => 3, 
            'A' => 3, 'B' => 3, 'C' => 3, 'D' => 3, 
            'E' => 4, 'F' => 4, 'G' => 4, 'H' => 4,
            'I' => 5, 'J' => 5, 'K' => 5, 'L' => 5,
            'M' => 6, 'N' => 6, 'O' => 6, 'P' => 6,
            'Q' => 7, 'R' => 7, 'S' => 7, 'T' => 7,
            'U' => 8, 'V' => 8, 'W' => 8, 'X' => 8,
            'Y' => 9, 'Z' => 9, 'a' => 9, 'b' => 9,
            'c' => 10, 'd' => 10, 'e' => 10, 'f' => 10,
            'g' => 11, 'h' => 11, 'i' => 11, 'j' => 11,
            'k' => 12, 'l' => 12, 'm' => 12, 'n' => 12,
            'o' => 13, 'p' => 13, 'q' => 13, 'r' => 13,
            's' => 14, 't' => 14, 'u' => 14, 'v' => 14,
            'w' => 15, 'x' => 15, 'y' => 15, 'z' => 15];
    /* 分 */
    public $pk_points = [
            'I' => 5, 'J' => 5, 'K' => 5, 'L' => 5,
            'c' => 10, 'd' => 10, 'e' => 10, 'f' => 10,
            'o' => 10, 'p' => 10, 'q' => 10, 'r' => 10];
    /* 庄报牌 */
    public $zhuang_poker = '';
    //private $ht = 'DHLPTXbfjnrvz';  /* 黑桃 */
    //private $xh = 'CGKOSWaeimquy';  /* 杏花 */
    //private $mh = 'BFJNRVZdhlptx';  /* 梅花 */
    //private $fh = 'AEIMQUYcgkosw';  /* 方花 */
    //private $ht_510k = 'Lfr';   /* 黑桃510k */
    //private $xh_510k = 'Keq';   /* 杏花510k */
    //private $mh_510k = 'Jdp';   /* 梅花510k */
    //private $fh_510k = 'Ico';   /* 方花510k */
    private $ht_510ks = ['Lfr', '<Lf', '<Lr', '<fr', '>Lf', '>Lr', '>fr', '<>L', '<>f', '<>r'];   /* 黑桃510k */
    private $xh_510ks = ['Keq', '<Ke', '<Kq', '<eq', '>Ke', '>Kq', '>eq', '<>K', '<>e', '<>q'];   /* 杏花510k */
    private $mh_510ks = ['Jdp', '<Jd', '<Jp', '<dp', '>Jd', '>Jp', '>dp', '<>J', '<>d', '<>p'];   /* 梅花510k */
    private $fh_510ks = ['Ico', '<Ic', '<Io', '<co', '>Ic', '>Io', '>co', '<>I', '<>c', '<>o'];   /* 方花510k */
    //private $pk_510k = [5 => 'IJKL', 10 => 'cdef', 13 => 'opqr'];

    public function __construct()
    {
        return ;
    }

    /**
     * @fn
     * @brief 
     *  随机产生4份牌
     *  TODO: 检测7喜/4王/8喜
     * @param $uids     [0=>'', 1=>'', 2=>'', 3=>'']
     * @param $first    第一个发牌的人/庄
     * @return [uid1=>'', uid2=>'', uid3=>'', uid4=>'']
     */
    public function sendPoker($uids = ['', '', '', ''], $first = '') /* {{{ */
    {
        $i = TOTAL_POKER;
        $current_poker = $this->poker;
        $pk = [$uids[0] => '', $uids[1] => '', $uids[2] => '', $uids[3] => ''];
        for ($i = TOTAL_POKER; $i > 0; ) {
            $index = mt_rand(0, $i - 1);
            $pk[$uids[0]] = $pk[$uids[0]].$current_poker[$index];
            //$current_poker = str_replace($current_poker[$index], '', $current_poker);
            $current_poker = substr_replace($current_poker, '', $index, 1);

            $index = mt_rand(0, $i - 2);
            $pk[$uids[1]] = $pk[$uids[1]].$current_poker[$index];
            $current_poker = substr_replace($current_poker, '', $index, 1);

            $index = mt_rand(0, $i - 3);
            $pk[$uids[2]] = $pk[$uids[2]].$current_poker[$index];
            $current_poker = substr_replace($current_poker, '', $index, 1);

            $index = mt_rand(0, $i - 4);
            $pk[$uids[3]] = $pk[$uids[3]].$current_poker[$index];
            $current_poker = substr_replace($current_poker, '', $index, 1);

            $i = $i - 4;
        }
        /* 报牌 */
        for ($i = 0; $i < EVERYONE_POKER; $i++) {
            $n = substr_count($pk[$first], $pk[$first][$i]);
            if ($n == 1) {
                $this->zhuang_poker = $pk[$first][$i];
                break;
            }
        }
        foreach ($pk as $uid => $poker) {
            $pk_arr = str_split($poker);
            sort($pk_arr);
            $pk[$uid] = implode('', $pk_arr);
        }

	    return $pk;
    } /* }}} */

    /**
     * @fn
     * @brief 
     *  校验规则
     *      检测牌型是否符合游戏规则；
     *      检测牌型是否相同或是否通压牌型，并且比上一手大；
     *  规则：
     *      A
     *      AA/AABB/AABBCC...
     *      AAA/AAABBB/AAABBBCCC...
     *      510K/AAAA/AAAAA/AAAAAA/AAAAAAA/AAAAAAAA/<<>>...
     *      大小王可以当任意牌，单出表示3，4个王是天炸，最大；
     *      大小王当任意牌时，一手牌最多有3张王；
     *  NOTE！万能牌规则bug：
     *      当玩家1出第一手牌：<>AABB 时，玩家2可能会当玩家一的牌是 AABBCC 或者 AAABBB，此时会造成出牌分歧；
     *      为此，我们规定：大小王除在 510k 时可以当该手不存在的牌，在其他牌型时只能当该手牌中已经有的牌；
     *  记分：
     *      AB
     *      CD
     *      7喜，4王，赢3家3倍；8喜赢3家6倍；
     * @param $pk       被检测的牌型
     * @param $pk_prev  上一手牌/为空表示被检测牌是第一手牌
     *  ['uid'=>'uid1', 'pk'=>'AAEEIJ', 'px_type'=>POKER_TYPE2, 'px_len'=>3]
     * @return 通过返回 当手牌牌型，牌型错误/比上家牌小 返回false
     *  ['uid'=>'', 'pk'=>'<FGIJM', 'px_type'=>POKER_TYPE2, 'px_len'=>3]
     */
    public function checkRule($pk, $pk_prev = []) /* {{{ */
    {
        $len = strlen($pk);
        $len2 = strlen($pk_prev);
        $is_big = false;
        $ret = ['uid' => '', 'pk' => $pk, 'px_type' => POKER_TYPE1, 'px_len' => 1];

        $pk_arr = str_split($pk);
        sort($pk_arr);
        $pk_str = implode('', $pk_arr);
        /* 非法牌 */
        for ($i = 0; $i < $len; $i++) {
        //foreach ($pk_arr as $v) {}
            if (in_array($pk_str[$i], $this->pk_arr) == false) {
	            return false;
            }
        }
        /* 当手为天王 直接通过，上手为天王 直接返回 false； */
        if ($len == 4 && $pk_str[0] == '<' && $pk_str[1] == '<' && $pk_str[2] == '>' && $pk_str[3] == '>') {
            $ret = ['uid' => '', 'pk' => $pk_str, 'px_type' => POKER_TYPE4, 'px_len' => 4];
            return $ret;
        } else if ($pk_prev['px_len'] == 4 && $pk_prev['pk'][0] == '<' && $pk_prev['pk'][1] == '<' 
                && $pk_prev['pk'][2] == '>' && $pk_prev['pk'][3] == '>') {
            return false;
        }
        /* 先检测牌类型，再比较大小 */
        if ($len == 1) {
            //$ret = ['uid' => '', 'pk' => $pk_str, 'px_type' => POKER_TYPE1, 'px_len' => 1];
            if ($pk_prev['px_type'] != $ret['px_type'] || $pk_prev['px_len'] != $ret['px_len']) {
                return false;
            } else if ($pk_prev['pk'] >= $ret['pk']) {
                return false;
            }
            return $ret;
        }
        /* 炸弹？ */
        $ret = $this->isType4($pk_str);
        if ($ret != false) {
            $ret['pk'] = $pk_str;
            if ($pk_prev['px_type'] != $ret['px_type']) {
                return $ret;
            } else if ($pk_prev['px_type'] == $ret['px_type'] && $pk_prev['px_len'] < $ret['px_len']) {
                return $ret;
            } else if ($pk_prev['px_len'] == $ret['px_len']) {
                if ($ret['px_len'] > 3 && $this->pk_point[$ret['pk'][$ret['px_len'] - 1]] > $this->pk_point[$pk_prev['pk'][$pk_prev['px_len'] - 1]]) {
                    return $ret;
                } else if ($ret['px_len'] == 3) {
                    /* 都为510k，比花色 */
                    if (in_array($pk_prev['pk'], $this->ht_510ks)) {
                        return false;
                    } else if (in_array($ret['pk'], $this->ht_510ks)) {
                        return $ret;
                    } else if (in_array($pk_prev['pk'], $this->xh_510ks)) {
                        return false;
                    } else if (in_array($ret['pk'], $this->xh_510ks)) {
                        return $ret;
                    } else if (in_array($pk_prev['pk'], $this->mh_510ks)) {
                        return false;
                    } else if (in_array($ret['pk'], $this->mh_510ks)) {
                        return $ret;
                    } else if (in_array($pk_prev['pk'], $this->fh_510ks)) {
                        return false;
                    } else if (in_array($ret['pk'], $this->fh_510ks)) {
                        return $ret;
                    }
                    //return false;
                }
                //return false;
            }
            return false;
        }
        /* 对子/连对？ */
        if ($len % 2 == 0) {
            $ret = $this->isType2($pk_str);
            if ($ret != false) {
                $ret['pk'] = $pk_str;
                /* 长度相同 且 比上一手大 */
                if ($ret['px_len'] == $pk_prev['px_len'] 
                        && $this->pk_point[$ret['pk'][$ret['px_len'] - 1]] > $this->pk_point[$pk_prev['pk'][$pk_prev['px_len'] - 1]]) {
                    return $ret;
                }
            }
        }
        /* 3条/连3条？ */
        if ($len % 3 == 0) {
            $ret = $this->isType3($pk_str);
            if ($ret != false) {
                $ret['pk'] = $pk_str;
                /* 长度相同 且 比上一手大 */
                if ($ret['px_len'] == $pk_prev['px_len'] 
                        && $this->pk_point[$ret['pk'][$ret['px_len'] - 1]] > $this->pk_point[$pk_prev['pk'][$pk_prev['px_len'] - 1]]) {
                    return $ret;
                }
            }
        }
        /* error */
        return false;

        $px_type = POKER_TYPE1;
        $px_len = 1;
        switch ($len) {
            case 1:
                if (empty($pk_prev)) {
                    $is_big = true;
                } else if ($this->pk_point[$pk] > $this->pk_point[$pk_prev['pk']]) {
                    $is_big = true;
                }
                break;
            case 2:
                /* 检测类型，不为对子 */
                if ($this->pk_point[$pk[0]] != $this->pk_point[$pk[1]]) {
                    break;
                }
                /* 第一手牌 */
                if (empty($pk_prev)) {
                    $is_big = true;
                } else if ($this->pk_point[$pk[0]] == $this->pk_point[$pk[1]] && $this->pk_point[$pk[0]] > $this->pk_point[$pk_prev['pk'][0]]) {
                    $is_big = true;
                }
                $px_type = POKER_TYPE2;
                $px_len = 1;
                break;
            case 3:
                break;
            case 4:
                break;
            case 5:
                break;
            case 6:
                break;
            case 7:
                break;
            case 8:
                break;
            case 9:
                break;
            case 10:
                break;
            case 11:
                break;
            case 12:
                break;
            //case 13:
            //    break;
            case 14:
                break;
            case 15:
                break;
            case 16:
                break;
            case 17:
                break;
            case 18:
                break;
            //case 19:
            //    break;
            case 20:
                break;
            case 21:
                break;
            case 22:
                break;
            //case 23:
            //    break;
            case 24:
                break;
            //case 25:
            //    break;
            //case 26:
            //    break;
            case 27:
                break;
            default :
                break;
        }

	    return $is_big;
    } /* }}} */

    /**
     * @fn
     * @brief 
     *  判断是否是牌型1：单个
     * @param $poker 'A'
     * @return [px_type=>1, px_len=>1] / false 正常：返回牌型类型和长度，否则返回false
     */
    public function isType1($poker = '') /* {{{ */
    {
        $is_type1 = false;
        $px = ['px_type' => POKER_TYPE1, 'px_len' => 1];
        if (strlen($poker) == 1) {
            $is_type1 = true;
        }

	    return $is_type1 ? $px : $is_type1;
    } /* }}} */

    /**
     * @fn
     * @brief 
     *  判断是否是牌型2：对子/连对
     * @param $poker 
     *  ’AB'
     *  ’<ABFHI'
     *  ['A',‘B'] 
     *  ['A','A','F','H','I','L']
     * @return [px_type=>2, px_len=>1] / false 正常：返回牌型类型和长度，否则返回false
     */
    public function isType2($poker = '') /* {{{ */
    {
        $px = ['px_type' => POKER_TYPE2, 'px_len' => 1];
        $len = strlen($poker);
        if (($len % 2) != 0) {
            return false;
        }
        $px['px_len'] = $len / 2;
        /* 万能牌检测 和 替换 */
        $wang = 0;
        $use_wang = 0;
        for ($i = 0; $i < $len; $i++) {
            if ($poker[$i] == '<' || $poker[$i] == '>') {
                $wang++;
            } else {
                break;
            }
        }
        /* 替换 */
        for ($i = $wang; $i < $len + $wang; $i = $i + 2) {
            if ($wang == $use_wang) {
                break;
            }
            /* <AAE 这些暂时不考虑： <>AAEE  <>AAII <<>AAIMM */
            if (empty($poker[$i + 1]) && empty($poker[$i])) {
                return false;
            }
            if (empty($poker[$i + 1])) {
                $use_wang++;
                $poker = $poker.$poker[$i];
            }
            if ($this->pk_point[$poker[$i]] != $this->pk_point[$poker[$i + 1]]) {
                $use_wang++;
                $poker = substr($poker, 0, $i).$poker[$i].substr($poker, $i);
            }
        }
        if ($wang > 0) {
            $poker = substr($poker, $wang);
        }
        /* 检测 对子，连对 */
        for ($i = 0; $i < $len; $i = $i + 2) {
            if ($this->pk_point[$poker[$i]] != $this->pk_point[$poker[$i + 1]]) {
                return false;
            }
            if (($i + 2 < $len) && ($this->pk_point[$poker[$i + 2]] - $this->pk_point[$poker[$i]] != 1)) {
                return false;
            }
        }

	    return $px;
    } /* }}} */

    /**
     * @fn
     * @brief 
     *  判断是否是牌型3：三条/连三条
     * @param $poker 
     *  ’ABC'
     *  ’<ABFGH'
     * @return [px_type=>3, px_len=>1] / false 正常：返回牌型类型和长度，否则返回false
     */
    public function isType3($poker = '') /* {{{ */
    {
        $px = ['px_type' => POKER_TYPE3, 'px_len' => 1];
        $len = strlen($poker);
        if (($len % 3) != 0) {
            return false;
        }
        $px['px_len'] = $len / 3;
        /* 万能牌检测 和 替换 */
        $wang = 0;
        $use_wang = 0;
        for ($i = 0; $i < $len; $i++) {
            if ($poker[$i] == '<' || $poker[$i] == '>') {
                $wang++;
            } else {
                break;
            }
        }
        /* 替换 */
        for ($i = $wang; $i < $len + $wang; $i = $i + 3) {
            if ($wang == $use_wang) {
                break;
            }
            /* <AAAEE <>AAAE <>AAEE 此牌型这些暂时不考虑： <<>AAA */
            if (empty($poker[$i + 2]) && empty($poker[$i + 1]) && empty($poker[$i])) {
                return false;
            }
            if (empty($poker[$i + 1]) && empty($poker[$i])) {
                $use_wang += 2;
                $poker = $poker.$poker[$i].$poker[$i];
            }
            if (empty($poker[$i + 1])) {
                $use_wang++;
                $poker = $poker.$poker[$i];
            }
            if ($this->pk_point[$poker[$i]] != $this->pk_point[$poker[$i + 1]]) {
                $use_wang++;
                $poker = substr($poker, 0, $i).$poker[$i].substr($poker, $i);
            }
            if ($this->pk_point[$poker[$i]] != $this->pk_point[$poker[$i + 2]]) {
                $use_wang++;
                $poker = substr($poker, 0, $i).$poker[$i].substr($poker, $i);
            }
        }
        if ($wang > 0) {
            $poker = substr($poker, $wang);
        }
        /* 检测 三条，连三条 */
        for ($i = 0; $i < $len; $i = $i + 3) {
            if ($this->pk_point[$poker[$i]] != $this->pk_point[$poker[$i + 1]]) {
                return false;
            }
            if ($this->pk_point[$poker[$i]] != $this->pk_point[$poker[$i + 2]]) {
                return false;
            }
            if (($i + 3 < $len) && ($this->pk_point[$poker[$i + 3]] - $this->pk_point[$poker[$i]] != 1)) {
                return false;
            }
        }

	    return $px;
    } /* }}} */

    /**
     * @fn
     * @brief 
     *  判断是否是牌型4：510k/炸弹/5炸/678炸
     *  此牌型：
     *      px_len = 3 表示 510k
     *      px_len = 4 表示 4张的炸
     *      px_len = 5 表示 5张的炸
     *      ...
     * @param $poker 
     *  ’Ico' '>Io' '<>c'
     *  ’<>AAA'
     * @return [px_type=>4, px_len=>3] / false 正常：返回牌型类型和长度，否则返回false
     */
    public function isType4($poker = '') /* {{{ */
    {
        $px = ['px_type' => POKER_TYPE4, 'px_len' => 3];
        $len = strlen($poker);
        if ($len < 3) {
            return false;
        }
        $px['px_len'] = $len;
        /* 万能牌检测 */
        $wang = 0;
        for ($i = 0; $i < $len; $i++) {
            if ($poker[$i] == '<' || $poker[$i] == '>') {
                $wang++;
            } else {
                break;
            }
        }
        /* 510k */
        if ($len == 3) {
            if ($wang > 2) {
                return false;
            }
            if ($wang == 2) {
                if (in_array($this->pk_point[$poker[2]], [5, 10, 13])) {
	                return $px;
                }
            } else if ($wang == 1) {
                if ($this->pk_point[$poker[1]] != $this->pk_point[$poker[2]] 
                        && in_array($this->pk_point[$poker[1]], [5, 10, 13])
                        && in_array($this->pk_point[$poker[2]], [5, 10, 13])) {
	                return $px;
                }
            } else {
                if ($this->pk_point[$poker[0]] == 5 && $this->pk_point[$poker[1]] == 10 && $this->pk_point[$poker[2]] == 13) {
	                return $px;
                }
            }
	        return false;
        }
        if (($len == 4 && $wang == 4) || ($len == 5 && $wang == 4) || ($len == 4 && $wang == 3)) {
	        //return $px;
        }
        /* 检测 炸弹 */
        for ($i = $wang; $i < $len - 1; $i++) {
            if ($this->pk_point[$poker[$i]] != $this->pk_point[$poker[$i + 1]]) {
                return false;
            }
        }

	    return $px;
    } /* }}} */

}

/* end file */
