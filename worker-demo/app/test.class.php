<?php
/**
 * @file Dagong.class.php
 * @brief 
 * 
 * Copyright (C) 2020 Langr.Org
 * All rights reserved.
 * 
 * @package worker-demo
 * @author xxx <xxx@xxx.org> 2020/05/21 16:37
 * 
 * $Id$
 */

class test 
{
    /* 桌座位顺序，每桌人员按加入时间逆时针排序 */
    private $room_uids = [0=>'', 1=>'', 2=>'', 3=>''];
    private $zhuang_uid = '';
    private $poker = '';
    private $mingji_uid = '';

    /**
     * 规则：
     * A
     * AA
     * AABB/AABBCC...
     * AAA
     * AAABBB/AAABBBCCC...
     * 510K
     * AAAA/AAAAA/AAAAAA/AAAAAAA/AAAAAAAA/<<>>...
     */
    private $px = ['a','<','A'];
    private $pk = '<>ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    private $pkpoint = [3 => 'ABCD', 4 => 'EFGH', 5 => 'IJKL', 10 => 'cdef', 13 => 'opqr', 14 => 'stuv', 15 => 'wxyz', 16 => '<', 17 => '>'];
    private $ht = 'DHLPTXbfjnrvz';  /* 黑桃 */
    private $xh = 'CGKOSWaeimquy';  /* 杏花 */
    private $mh = 'BFJNRVZdhlptx';  /* 梅花 */
    private $fp = 'AEIMQUYcgkosw';  /* 方片 */

    public function __construct()
    {
        return ;
    }

    /**
     * @fn
     * @brief 
     *  随机产生4份牌
     * @param 
     * @return 
     */
    public function sendPoker() /* {{{ */
    {
	    return ;
    } /* }}} */

    /**
     * @fn
     * @brief 
     *  校验规则
     * @param 
     * @return 
     */
    public function chkRule() /* {{{ */
    {
	    return ;
    } /* }}} */

}

/* end file */
