<?php
/**
 * @file Room.class.php
 * @brief 
 * 
 * Copyright (C) 2020 Langr.Org
 * All rights reserved.
 * 
 * @package worker-demo
 * @author xxx <xxx@xxx.org> 2020/05/16 21:37
 * 
 * $Id$
 */

/* 每桌最多人数 */
const DESK_PEOPLE = 4;
/* 最多允许的房间数 */
const ROOM_NUM = 1000;
/* 每手牌超时时间 */
const PALY_TIMEOUT = 15;

require_once __DIR__ . '/Dagong.class.php';

class Room
{
    private $game = null;
    //private $pk = '<>ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    /* 房间总连接(人数) */
    private $links = [];
    /* 房号 及 房间人员，[room_id=>[uid=>link_id,uid=>link_id,uid=>link_id,uid=>link_id], room_id=>[]] */
    private $room_ids = [];
    /* 桌座位顺序，每桌人员按加入时间逆时针排序 */
    private $room_uids = [];        // [0=>'', 1=>'', 2=>'', 3=>''];
    private $room_uids_poker = [];  // [uid=>[], uid=>[], uid=>[], uid=>[]];
    /* 上局出牌第一名，下局做庄 */
    private $room_prev_first = '';
    /* 当前局庄 */
    private $room_banker_uid = '';
    /* 当前局庄报的牌 */
    private $room_poker = '';
    /* 是否明鸡，庄跟明鸡同家，另外2边同一家 */
    private $room_mingji_uid = '';
    /* 出牌权 uid，第一手为庄 */
    private $pk_power = '';
    private $pk_power_timer_id = null;
    /**
     * 上一手牌，
     * ['uid'=>'uid1', 'px'=>'AAEEIJ', 'px_type'=>POKER_TYPE2, 'px_len'=>3]
     * 为空表示该轮第一手牌，可出任意牌；
     * uid = 当前用户，表示上一圈都 pass, 可以出任意牌；
     */
    private $pk_prev = [];
    //private $pk_prev_uid = '';
    //private $pk_prev_px = '';
    //private $pk_prev_px_type = '';
    //private $pk_prev_px_len = 0;
    /* 当圈出现的分数 */
    private $pk_quan_point = 0;
    /**
     * 当局得分结果
     * top => [uid1,uid2,uid3,uid4],
     * point => [uid=>0,uid=>0,uid=>0,uid=>0],
     * partner => [$this->room_banker_uid, $this->room_mingji_uid],
     * win => [uid1=>3,uid2=>3,uid3=>-3,uid4=>-3]
     */
    private $pk_res = ['top' => [], 'point' => [], 'partner' => [], 'win' => []];

    public function __construct()
    {
        if (empty($this->game)) {
            $this->game = new Dagong();
        }
        return ;
    }

    /**
     * @fn
     * @brief 
     *  登陆，保存 token
     * @param 
     *  request:
     *      c:a:args...
     *      controller:action:arg1:arg2:arg n...
     *  response:
     * @return 
     */
    public function token(& $connection, $ret = []) /* {{{ */
    {
        if (!empty($ret[1]) && !empty($ret[2])) {
            $connection->uid = $ret[1];     /* uid */
            $connection->id = $ret[2];      /* token */
            $connection->room_id = 0;       /* 房间号 */
            $connection->is_ready = false;  /* 准备好？ */
            $this->links[$ret[1]] = $connection;
        }
	    return ;
    } /* }}} */

    /**
     * @fn
     * @brief 
     *  加入房间，不存在则创建
     * @param 
     *  request:
     *      c:a:args...
     *      controller:action:arg1:arg2:arg n...
     *      room:add:room_id
     *  response:
     *      0:ok:[uids]
     * @return 
     */
    public function joinRoom(& $connection, $ret = []) /* {{{ */
    {
        if (empty($ret[2]) || $connection->room_id != 0) {
            $connection->send(pencode(['10011', 'room id error! '.$connection->room_id]));
	        return ;
        }
        $msg = 'join room';
        /* 检测房间人员 */
        if (empty($this->room_ids[$ret[2]])) {
            if (count($this->room_ids) >= ROOM_NUM) {
                $connection->send(pencode(['10012', 'server full!']));
                $connection->close();
	            return ;
            }
            $msg = 'create room';
            $this->room_prev_first = $connection->uid;
        } else if (count($this->room_ids[$ret[2]]) >= DESK_PEOPLE) {
            /* 满员 */
            $connection->send(pencode(['10013', 'room full!']));
	        return ;
        }
        $connection->room_id = $ret[2];
        $this->room_ids[$ret[2]][$connection->uid] = $connection;   /* room id */
        $this->room_uids[] = $connection->uid;                      /* room uid */
        $connection->send(pencode([0, $msg.' '.$ret[2].' ok!'], $this->room_uids));
        //foreach ($this->room_ids[$connection->room_id] as $uid => $con) {
        //    $con->send(pencode(['1', $connection->uid.' join room!']));
        //}
        $this->sendRoom($connection->room_id, pencode(['1', $connection->uid.' join room!']));
	    return ;
    } /* }}} */

    /**
     * @fn
     * @brief 
     *  获取房间信息，
     *  如人员信息，牌，牌权，庄，同伴，分数，等...
     * @param 
     *  request:
     *      controller:action:arg1:arg2:arg n...
     *      room:get:room_id
     *  response:
     * @return 
     */
    public function getRoom(& $connection, $ret = []) /* {{{ */
    {
        $room_info = [];
        return $room_info;
    } /* }}} */

    /**
     * @fn
     * @brief 
     *  准备好
     *  如果都准备好，则发牌。
     * @param 
     *  request:
     *      c:a:args...
     *      controller:action:arg1:arg2:arg n...
     *      room:ready:1
     * @return 
     */
    public function ready(& $connection, $ret = []) /* {{{ */
    {
        if (empty($ret[2])) {
            $connection->send(pencode(['10021', 'ready?']));
	        return ;
        } else {
            $connection->is_ready = true;
        }
        /* 向该房间所有人广播 我 已经准备好 */
        $all_ready = true;
        foreach ($this->room_ids[$connection->room_id] as $uid => $con) {
            $con->send(pencode(['1', $connection->uid.' ready!']));
            if ($con->is_ready != true) {
                $all_ready = false;
            }
        }
        /* 都准备好，发牌 */
        if ($all_ready && count($this->room_ids[$connection->room_id]) == DESK_PEOPLE) {
            /* 发牌后开始 标记庄家，叫牌，标记牌权，记每手牌超时时间，超时自动出牌：过牌/出最小单张 */
            $this->room_uids_poker = $this->game->sendPoker($this->room_uids, $this->room_prev_first);
            $this->pk_power = $this->room_prev_first;
            $this->room_banker_uid = $this->room_prev_first;
            $this->room_prev_first = '';
            $this->room_poker = $this->game->zhuang_poker;
            $this->pk_res = ['top' => [], 'point' => [], 'partner' => [$this->room_banker_uid], 'win' => []];
            wlog(LOG_PATH.'room-'.$connection->room_id.date('-Ym').'.log', json_encode([$this->room_poker, $this->room_uids_poker], JSON_UNESCAPED_UNICODE));
            foreach ($this->room_ids[$connection->room_id] as $uid => $con) {
                $this->pk_res['point'][$uid] = 0;
                $this->pk_res['win'][$uid] = 0;
                $msg = pencode(['1', $this->room_uids_poker[$uid]]);
                $con->send($msg);
                wlog(LOG_PATH.'room-'.$connection->room_id.date('-Ym').'.log', $uid.' '.$msg);
            }
        }
	    return ;
    } /* }}} */

    /**
     * @fn
     * @brief 
     *  出牌，
     *      检测规则，设置下手牌牌权，清除当手牌超时，设置下手牌超时，
     * @param 
     *  request:
     *      controller:action:arg1:arg2:arg n...
     *      room:play:AAA
     *      room:play:0     pass
     * @return 
     */
    public function play(& $connection, $ret = []) /* {{{ */
    {
        /* 检测是否有牌权 */
        if ($connection->uid != $this->pk_power) {
            $connection->send(pencode(['10031', $connection->uid.' 没有出牌权!', $this->pk_power]));
            return ;
        }
        wlog(LOG_PATH.'room-'.date('Ym').'.log', json_encode(['play:', $this->pk_power, $connection->uid, $ret, $this->pk_prev]));
        /* 打牌后开始清除当手牌超时时间，并设置下一手牌超时时间；超时自动出牌：过牌/出最小单张 */
        if (empty($ret[2]) || $ret[2] == '0') {
            /* pass */
            if (empty($this->pk_prev['uid']) || $connection->uid == $this->pk_prev['uid']) {
                $connection->send(pencode(['10032', $connection->uid.' 无上家牌!', $this->pk_power]));
                return ;
            }
            /* 是否需要积分，是否需要转让牌权 */
            /* 如果上一手牌是 下家 出的，当圈分数归下家；如果并且下家无牌了，转让出牌权给下家队友，如果没明鸡，则转给下家的下一家 */
            /* 如果下家无牌，上一手牌是下家的下一家出的 */
            /* 如果下家无牌，下家的下一家也无牌，上一手牌是上家出的 */

            /* 设置下一次牌权，设置超时时间 */
            /* 需要检测下家是否出完，已出完，则出牌权跳下一家 */
        wlog(LOG_PATH.'room-'.date('Ym').'.log', json_encode(['play:0 ', $connection->uid, $ret, empty($ret[2]), $ret[2] == '0']));
            $this->nextPokerPower($connection->uid, true);
            $this->sendRoom($connection->room_id, pencode(['1', $connection->uid.' pass!'.$ret[2]]));
        wlog(LOG_PATH.'room-'.date('Ym').'.log', json_encode(['play:01 ', $connection->uid, $this->pk_power]));
        } else {
            $tmp_prev = $this->pk_prev;
            /* 检测上一手牌 uid，如果是自己出的，则===当圈分数归自己，并===可以出任意牌 */
            if (!empty($this->pk_prev['uid']) && $this->pk_prev['uid'] == $connection->uid) {
                /*  */
                $tmp_prev = [];
            }
        wlog(LOG_PATH.'room-'.date('Ym').'.log', json_encode(['play:2 ', $connection->uid, $ret]));
            /* 检测规则，检测是否合法牌型，并且大于上一手牌 */
            $ret = $this->game->checkRule($ret[2], $tmp_prev);
        wlog(LOG_PATH.'room-'.date('Ym').'.log', json_encode(['play:3 ', $connection->uid, $ret]));
            if ($ret === false) {
                $connection->send(pencode(['10033', $connection->uid.' rule error!']));
                return ;
            }
            /**
             * 出牌，删掉用户出了的牌，用户无此牌则返回出错；
             * 同时更新 当圈分数，是否明鸡，检查此用户是否出完，出完，记录出完顺序，检测是否结束；
             */
            if ($this->userDoPoker($connection->uid, $ret['pk']) == false) {
                $connection->send(pencode(['10034', $connection->uid.' poker error!']));
                return ;
            }
            /* 更新 出牌权，上一手牌 */
            $this->sendRoom($connection->room_id, pencode(['1', $connection->uid.'-'.$ret['pk']]));
            //$this->pk_prev_uid = $connection->uid;
            //$this->pk_prev_px = $ret['pk'];
            $this->pk_prev = $ret;
            $this->pk_prev['uid'] = $connection->uid;
            /* 设置下一次牌权，设置超时时间 */
            /* 需要检测下家是否出完，已出完，则出牌权跳下一家 */
            $this->nextPokerPower($connection->uid);
        wlog(LOG_PATH.'room-'.date('Ym').'.log', json_encode(['play:4 ', $this->pk_power, $this->pk_prev]));
            wlog(LOG_PATH.'room-'.$connection->room_id.date('-Ym').'.log', json_encode([$connection->uid, $ret['pk'], $this->room_uids_poker[$connection->uid]]));
            $this->sendRoom($connection->room_id, pencode(['1', $connection->uid.' '.$ret['pk'], $this->pk_power]));
        }
	    return ;
    } /* }}} */

    /**
     * @fn
     * @brief 
     *  1.[普通出牌]跳过无牌玩家，设置下一次出牌权，重新设置超时；
     *  2.[过牌]跳过无牌玩家，检测是否(都过了)牌权回到上一手牌玩家，[得分]，并且上一手牌玩家是否出完，是否需要转让下一次出牌权，重新设置超时；
     * @param $uid  当前用户
     * @return $uid 找到的用户id
     */
    public function nextPokerPower($uid, $pass = false) /* {{{ */
    {
        wlog(LOG_PATH.'room-'.date('Ym').'.log', json_encode(['nextPokerPower:1 ', $uid, $pass]));
        $next_user = $this->nextUser($uid);
        if ($pass) {
            /* 下家有牌->牌权，上手牌是下家出，[1得分]/下家无牌，上手牌是下家出，[1得分]->转让出牌权 */
            /* 下家无牌，下下家有牌->牌权，上手牌是下下家出，[2得分]/下下家无牌->上手牌是下下家出，[2得分]->转让牌权 */
            /* 下下家无牌，上家有牌->牌权，[3得分][/上家无牌->结束] */
            if (empty($this->room_uids_poker[$next_user])) {
                if ($this->pk_prev['uid'] == $next_user) {
                    $this->pk_res['point'][$next_user] += $this->pk_quan_point;
                    $this->pk_quan_point = 0;
                    /* 转让 */
                    $next_user = $this->nextPartner($next_user);
                    $this->pk_power = $next_user;
                    $this->pk_power_timer_id = null;
                    return true;
                }
                $next_user = $this->nextUser($next_user);
                if (empty($this->room_uids_poker[$next_user])) {
                    if ($this->pk_prev['uid'] == $next_user) {
                        $this->pk_res['point'][$next_user] += $this->pk_quan_point;
                        $this->pk_quan_point = 0;
                        /* 转让 */
                        $next_user = $this->nextPartner($next_user);
                        $this->pk_power = $next_user;
                        $this->pk_power_timer_id = null;
                        return true;
                    }
                }
                $next_user = $this->nextUser($next_user);
            }
            if ($this->pk_prev['uid'] == $next_user) {
                $this->pk_res['point'][$next_user] += $this->pk_quan_point;
                $this->pk_quan_point = 0;
            }
            $this->pk_power = $next_user;
            $this->pk_power_timer_id = null;
            return true;
        }
        /* 需要检测下家是否出完，已出完，则出牌权顺序跳到下一家有牌玩家 */
        if (empty($this->room_uids_poker[$next_user])) {
            $next_user = $this->nextUser($next_user);
            if (empty($this->room_uids_poker[$next_user])) {
                $next_user = $this->nextUser($next_user);
            }
        }
        $this->pk_power = $next_user;
        $this->pk_power_timer_id = null;
        return true;
    } /* }}} */

    /**
     * @fn
     * @brief 
     *  当前房间下一个用户
     *  默认返回下家。
     * @param $uid  当前用户
     * @param $x    方位 0自己，1下家，2对家，3上家
     * @return $uid 找到的用户id
     */
    public function nextUser($uid, $x = 1) /* {{{ */
    {
        wlog(LOG_PATH.'room-'.date('Ym').'.log', json_encode(['nextUser:1 ', $uid, $x, $this->room_uids]));
        $k = array_search($uid, $this->room_uids);
        wlog(LOG_PATH.'room-'.date('Ym').'.log', json_encode(['nextUser:2 ', $k]));
        if ($k === false) {
	        return '';
        }
        //$index = ($k + $x) % count($this->room_uids);
        $index = ($k + $x) % DESK_PEOPLE;
        wlog(LOG_PATH.'room-'.date('Ym').'.log', json_encode(['nextUser:3 ', $k, $index]));
        if (!isset($this->room_uids[$index])) {
	        return $this->nextUser($uid, $x + 1);
        }
        return $this->room_uids[$index];
    } /* }}} */

    /**
     * @fn
     * @brief 
     *  当前用户的搭档，没有则返回下一家[有牌的用户]。
     * @param $uid  当前用户
     * @return $uid 找到的用户id
     */
    public function nextPartner($uid) /* {{{ */
    {
        $next_user = $uid;
        if (empty($this->room_mingji_uid)) {
            $next_user = $this->nextUser($next_user);
            if (empty($this->room_uids_poker[$next_user])) {
                $next_user = $this->nextUser($next_user);
            }
        } else {
            if (in_array($next_user, $this->pk_res['partner'])) {
                $v = array_diff($this->pk_res['partner'], [$next_user]);
            } else {
                $v = array_diff($this->room_uids, [$this->room_banker_uid, $this->room_mingji_uid, $next_user]);
            }
            $next_user = $v[key($v)];
        }
        return $next_user;
    } /* }}} */

    /**
     * @fn
     * @brief 
     *  此用户出牌
     *  检查用户是否有此牌，有就删除，没有就返回出错；
     *  检查是否有明鸡牌；
     *  检查是否有分；
     * @param $uid  当前用户
     * @param $pk   此手牌型
     * @return true/false
     */
    public function userDoPoker($uid, $pk = '') /* {{{ */
    {
        wlog(LOG_PATH.'room-'.date('Ym').'.log', json_encode(['userDoPower:1 ', $uid, $pk]));
        $len = strlen($pk);
        /* 检查是否有当手牌 */
        for ($i = 0; $i < $len; $i++) {
            $pos = strpos($this->room_uids_poker[$uid], $pk[$i]);
            if ($pos === false) {
	            return false;
            }
        }
        for ($i = 0; $i < $len; $i++) {
            /* 明鸡 */
            if ($uid != $this->room_banker_uid && $pk[$i] == $this->room_poker) {
                $this->room_mingji_uid = $uid;
            }
            /* 分 */
            if (isset($this->game->pk_points[$pk[$i]])) {
                $this->pk_quan_point += $this->game->pk_points[$pk[$i]];
            }
            /* 出牌，删除已出牌 */
            $pos = strpos($this->room_uids_poker[$uid], $pk[$i]);
            //$this->room_uids_poker[$uid] = str_replace($pk[$i], '', $this->room_uids_poker[$uid]);
            $this->room_uids_poker[$uid] = substr_replace($this->room_uids_poker[$uid], '', $pos, 1);
        }
        /* 是否出完 */
        return true;
    } /* }}} */

    /**
     * @fn
     * @brief 
     *  run
     *  request:
     *      c:a:args...
     *      controller:action:arg1:arg2:arg n...
     *  response:
     *      errno:errmsg/action:data...
     *      状态号:消息:数据s
     *      0     :ok[:data...]             请求正常
     *      1     :join-room[:data...]      广播
     *      1     :room-info[:data...]      广播
     *      1xxx  :出错描述[:data...]       请求异常
     * @param $connection websocket连接
     * @param $data 客户发送来的 request 数据
     * @return 
     */
    public function run(& $connection, $data = '') /* {{{ */
    {
        //$connection->send(date('YmdHis ').':room:'.$data);
        $ret = pdecode($data);
        if (empty($ret[0])) {
        } else if ($ret[0] == 'token') {
            $this->token($connection, $ret);
        } else if ($ret[0] == 'room') {
            if (empty($ret[1])) {
            } else if ($ret[1] == 'add') {
                $this->joinRoom($connection, $ret);
            } else if ($ret[1] == 'ready') {
                $this->ready($connection, $ret);
            } else if ($ret[1] == 'play') {
                $this->play($connection, $ret);
            }
        } else if ($ret[0] == 'test') {
            //$ret = $this->{$ret[1]}($ret[2]);
            $this->pk_power = $connection->uid;
            $this->play($connection, $ret);
            /*$ret1 = $this->game->checkRule($ret[2]);
            if ($ret1 != false && !empty($ret[3])) {
                $ret2 = $this->game->checkRule($ret[3], $ret1);
                $ret = [$ret1, $ret2, $ret];
            } else {
                $ret = $ret1;
            }*/
        }
        $ret = json_encode($ret, JSON_UNESCAPED_UNICODE);
        $connection->send(pencode(['0', 'ok', $ret]));
	    return ;
    } /* }}} */

    /**
     * @fn
     * @brief 
     *  向房间用户群发信息
     * @param 
     *  request:
     *      c:a:args...
     *      controller:action:arg1:arg2:arg n...
     * @return 
     */
    public function sendRoom($room_id = 0, $msg = '') /* {{{ */
    {
        if (empty($this->room_ids[$room_id])) {
            return false;
        }
        foreach ($this->room_ids[$room_id] as $uid => $con) {
            $con->send($msg);
        }
	    return true;
    } /* }}} */

    /**
     * @fn
     * @brief 
     *  有用户退出，断连接
     * @param 
     *  request:
     *      c:a:args...
     *      controller:action:arg1:arg2:arg n...
     * @return 
     */
    public function bye($connection, $ret = []) /* {{{ */
    {
        if (empty($ret[2])) {
	        return ;
        }
        if (!empty($connection->room_id) /* && 不在牌局[当局结束了，且没点准备] */) {
            $this->sendRoom($connection->room_id, pencode(['1', $connection->uid.' 离开了房间。']));
            unset($this->room_ids[$connection->room_id][$connection->uid]);     /* room id */
            unset($this->room_uids_poker[$connection->uid]);                    /* uid poker */
            foreach ($this->room_uids as $k => $v) {
                if ($connection->uid == $v) {
                    unset($this->room_uids[$k]);                                /* room uid */
                }
            }
            $connection->room_id = 0;
        }
	    return ;
    } /* }}} */
}

/* end file */
