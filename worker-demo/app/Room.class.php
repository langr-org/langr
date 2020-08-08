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
const PK_POWER_TIMEOUT = 15;

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
    /* 上局出牌第一名，下局做庄，在上局游戏结束时标记，下局游戏开始时清空 */
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
     * partner => [2个庄家的对手(明鸡后有值)],
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
     *      token:uid:user-token
     *      {'a':'login','m':'token','d':{a1:uid,a2:token-value...}}
     *  response:
     * @return 
     */
    public function token(& $connection, $ret = []) /* {{{ */
    {
        if (!empty($ret['d']['a1']) && !empty($ret['d']['a2'])) {
            $connection->uid = $ret['d']['a1']; /* uid */
            $connection->id = $ret['d']['a2'];  /* token */
            $connection->room_id = 0;           /* 房间号 */
            $connection->is_ready = false;      /* 准备好？ */
            $this->links[$ret['d']['a1']] = $connection;
        } else {
            $connection->send(pencode(['50010', 'login error!']));
        }
	    return ;
    } /* }}} */

    /**
     * @fn
     * @brief 
     *  客户端[异常]断线重连。
     *  重连不需要重新登陆，只需要更新用户连接信息。
     *  找到 $this->links 旧连接对象，复制对象 id,uid,room_id,is_ready,
     *  更新 $this->links[$uid]，$this->room_ids[$room_id][$uid] 对象；
     *  用户异常退出： unset $this->room_ids[$room_id][$uid]；TODO:异常断开时，指定时间内发起重连，则清除定时。
     * @param 
     *  request:
     *      c:a:args...
     *      room:reconnection:uid:token
     *      {'a':'room','m':'reconnection','d':{a1:uid,a2:token-value...}}
     *  response:
     * @return 
     */
    public function reConnection(& $connection, $ret = []) /* {{{ */
    {
        if (!empty($ret['d']['a1']) && !empty($ret['d']['a2'])) {
            $connection->uid = $ret['d']['a1'];     /* uid */
            $connection->id = $this->links[$ret['d']['a1']]->id;      /* token */
            $connection->room_id = $this->links[$ret['d']['a1']]->room_id;      /* 房间号 */
            $connection->is_ready = $this->links[$ret['d']['a1']]->is_ready;    /* 准备好？ */
            $this->links[$ret['d']['a1']] = $connection;
            if ($connection->room_id) {
                $this->room_ids[$connection->room_id][$connection->uid] = $connection;
            }
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
     *      {'a':controller,'m':method,'d':{arg1:arg2:arg n...}}
     *      room:add:room_id
     *      {'a':'room','m':'add','d':{a1:room-id}}
     *  response:
     *      0:ok:[uids]
     * @return 
     */
    public function joinRoom(& $connection, $ret = []) /* {{{ */
    {
        if (empty($ret['d']['a1']) || $connection->room_id != 0) {
            $connection->send(pencode(['50110', 'room id error! '.$connection->room_id]));
	        return ;
        }
        $msg = 'join room';
        /* 检测房间人员 */
        if (empty($this->room_ids[$ret['d']['a1']])) {
            if (count($this->room_ids) >= ROOM_NUM) {
                $connection->send(pencode(['50120', 'server full!']));
                $connection->close();
	            return ;
            }
            $msg = 'create room';
            wlog(LOG_PATH.'room-'.date('Ym').'.log', 'create room:'.$ret['d']['a1'].' uid:'.$connection->uid);
        } else if (count($this->room_ids[$ret['d']['a1']]) >= DESK_PEOPLE) {
            /* 满员 */
            $connection->send(pencode(['50130', 'room full!']));
            wlog(LOG_PATH.'room-'.date('Ym').'.log', json_encode(['joinRoom:', $this->room_ids[$ret['d']['a1']], $this->room_uids], JSON_UNESCAPED_UNICODE));
	        return ;
        }
        $connection->room_id = $ret['d']['a1'];
        $this->room_ids[$ret['d']['a1']][$connection->uid] = $connection;   /* room id */
        $this->room_uids[] = $connection->uid;                      /* room uid */
        $connection->send(pencode([0, $msg.' '.$ret['d']['a1'].' ok!', $this->room_uids]));
        //foreach ($this->room_ids[$connection->room_id] as $uid => $con) {
        //    $con->send(pencode(['1', $connection->uid.' join room!']));
        //}
        $this->sendRoom($connection->room_id, pencode(['1', $connection->uid.' join room!']));
        wlog(LOG_PATH.'room-'.$connection->room_id.date('-Ym').'.log', $connection->uid.' join room');
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
     *      {'a':'room','m':'ready','d':{'a1':'1'}}
     *      room:ready:1
     * @return 
     */
    public function ready(& $connection, $ret = []) /* {{{ */
    {
        if (empty($ret['d']['a1'])) {
            $connection->send(pencode(['50210', 'ready?']));
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
            if (empty($this->room_prev_first)) {
                $this->room_prev_first = $this->room_uids[0];
            }
            /* 发牌后开始 标记庄家，叫牌，标记牌权，清除准备状态，TODO: 记每手牌超时时间，超时自动出牌：过牌/出最小单张 */
            $this->room_uids_poker = $this->game->sendPoker($this->room_uids, $this->room_prev_first);
            $this->pk_power = $this->room_prev_first;
            $this->room_banker_uid = $this->room_prev_first;
            $this->room_prev_first = '';
            $this->room_poker = $this->game->zhuang_poker;
            $this->pk_res = ['top' => [], 'point' => [], 'partner' => [], 'win' => []];
            wlog(LOG_PATH.'room-'.$connection->room_id.date('-Ym').'.log', 'game-start:'.json_encode([$this->room_poker, $this->room_uids_poker]));
            foreach ($this->room_ids[$connection->room_id] as $uid => $con) {
                $this->pk_res['point'][$uid] = 0;
                $this->pk_res['win'][$uid] = 0;
                $con->is_ready = false;
                $con->send(pencode(['1', $this->room_uids_poker[$uid]]));
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
     *      {'a':'room','m':'play','d':{'a1':'play-info'}}
     * @return 
     */
    public function play(& $connection, $ret = []) /* {{{ */
    {
        /* 检测是否有牌权 */
        if ($connection->uid != $this->pk_power) {
            $connection->send(pencode(['50310', $connection->uid.' 没有出牌权!', $this->pk_power]));
            return ;
        }
        wlog(LOG_PATH.'room-'.date('Ym').'.log', json_encode(['play:', $this->pk_power, $connection->uid, $ret, $this->pk_prev]));
        /* 打牌后开始清除当手牌超时时间，并设置下一手牌超时时间；超时自动出牌：过牌/出最小单张 */
        if (empty($ret['d']['a1']) || $ret['d']['a1'] == '0') {
            /* pass */
            if (empty($this->pk_prev['uid']) || $connection->uid == $this->pk_prev['uid']) {
                $connection->send(pencode(['50320', $connection->uid.' 无上家牌!', $this->pk_power]));
                return ;
            }
            /* 设置下一次牌权，设置超时时间 */
            $this->nextPokerPower($connection->uid, true);
            $this->sendRoom($connection->room_id, pencode(['1', $connection->uid.' pass!', $this->pk_power]));
        //wlog(LOG_PATH.'room-'.date('Ym').'.log', json_encode(['play:1 ', $connection->uid, $this->pk_power]));
        } else {
            /* 检测上一手牌 uid，如果是自己出的，则===当圈分数归自己，并===可以出任意牌 */
            if (!empty($this->pk_prev['uid']) && $this->pk_prev['uid'] == $connection->uid) {
                $this->pk_prev = [];
            }
        //wlog(LOG_PATH.'room-'.date('Ym').'.log', json_encode(['play:2 ', $connection->uid, $ret]));
            /* 检测规则，检测是否合法牌型，并且大于上一手牌 */
            $ret = $this->game->checkRule($ret['d']['a1'], $this->pk_prev);
        //wlog(LOG_PATH.'room-'.date('Ym').'.log', json_encode(['play:3 ', $connection->uid, $ret]));
            if ($ret === false) {
                $connection->send(pencode(['50330', $connection->uid.' rule error!']));
                return ;
            }
            /**
             * 出牌，删掉用户出了的牌，用户无此牌则返回出错；
             * 同时更新 当圈分数，是否明鸡，检查此用户是否出完，出完，记录出完顺序，检测是否结束；
             */
            if ($this->userDoPoker($connection->uid, $ret['pk']) == false) {
                $connection->send(pencode(['50340', $connection->uid.' poker error!']));
                return ;
            }
            wlog(LOG_PATH.'room-'.$connection->room_id.date('-Ym').'.log', 'play:'.json_encode([$connection->uid, $ret['pk'], $this->room_uids_poker[$connection->uid]]));
            /* 当局结束 */
            if (!empty($this->room_prev_first)) {
                $this->sendRoom($connection->room_id, pencode(['201', 'game-over', json_encode($this->pk_res, JSON_UNESCAPED_UNICODE)]));
                wlog(LOG_PATH.'room-'.$connection->room_id.date('-Ym').'.log', 'game-over:'.json_encode($this->pk_res));
                return ;
            }
            /* 更新 出牌权，上一手牌 */
            //$this->sendRoom($connection->room_id, pencode(['1', $connection->uid.'-'.$ret['pk']]));
            $this->pk_prev = $ret;
            $this->pk_prev['uid'] = $connection->uid;
            /* 设置下一次牌权，设置超时时间；需要检测下家是否出完，已出完，则出牌权跳下一家 */
            $this->nextPokerPower($connection->uid);
        wlog(LOG_PATH.'room-'.date('Ym').'.log', json_encode(['play:4 ', $this->pk_power, $this->pk_prev]));
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
        //wlog(LOG_PATH.'room-'.date('Ym').'.log', json_encode(['nextPokerPower:1 ', $uid, $pass]));
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
                    $this->pk_prev = [];
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
                        $this->pk_prev = [];
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
        $k = array_search($uid, $this->room_uids);
        //wlog(LOG_PATH.'room-'.date('Ym').'.log', json_encode(['nextUser:2 ', $k]));
        if ($k === false) {
	        return '';
        }
        //$index = ($k + $x) % count($this->room_uids);
        $index = ($k + $x) % DESK_PEOPLE;
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
                $next_user = $v[key($v)];
            } else {
                //$v = array_diff($this->room_uids, [$this->room_banker_uid, $this->room_mingji_uid, $next_user]);
                if ($uid == $this->room_banker_uid) {
                    $next_user = $this->room_mingji_uid;
                } else {
                    $next_user = $this->room_banker_uid;
                }
            }
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
        //wlog(LOG_PATH.'room-'.date('Ym').'.log', json_encode(['userDoPower:1 ', $uid, $pk]));
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
                $this->pk_res['partner'] = array_values(array_diff($this->room_uids, [$this->room_banker_uid, $this->room_mingji_uid]));
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
        /* 是否出完？出完 记录出牌顺序，检测是否结束； */
        if (empty($this->room_uids_poker[$uid])) {
            $this->pk_res['top'][] = $uid;
            if ($this->isGameOver()) {
                /* 当局结束 */
                $this->room_uids_poker = [];
            }
        }
        return true;
    } /* }}} */

    /**
     * @fn
     * @brief 
     *  当前局结束，返回结果
     * @param $
     * @return true/false
     */
    public function isGameOver() /* {{{ */
    {
        $c = count($this->pk_res['top']);
        if ($c <= 1) {
            return false;
        } else if ($c == 2) {
            if (empty($this->room_mingji_uid)) {
                /* 没明鸡，2个都必须为庄对手，否则游戏没结束 */
                if (in_array($this->room_banker_uid, $this->pk_res['top'])) {
                    return false;
                }
                /* 庄输 通子 */
                $this->pk_res['top'][2] = $this->room_banker_uid;
                $v = array_diff($this->room_uids, [$this->room_banker_uid, $this->pk_res['top'][0], $this->pk_res['top'][1]]);
                $this->pk_res['top'][3] = $v[key($v)];
                $this->pk_res['partner'] = [$this->pk_res['top'][0], $this->pk_res['top'][1]];
            } else {
                /* 明鸡，2个都必须为庄对手，或者都必须为庄和庄队友，否则游戏没结束 */
                if (in_array($this->room_banker_uid, $this->pk_res['top']) && in_array($this->room_mingji_uid, $this->pk_res['top'])) {
                    /* 庄赢 通子 */
                    $this->pk_res['top'][2] = $this->pk_res['partner'][0];
                    $this->pk_res['top'][3] = $this->pk_res['partner'][1];
                } else if (in_array($this->pk_res['partner'][0], $this->pk_res['top']) && in_array($this->pk_res['partner'][1], $this->pk_res['top'])) {
                    /* 庄输 通子 */
                    $this->pk_res['top'][2] = $this->room_banker_uid;
                    $this->pk_res['top'][3] = $this->room_mingji_uid;
                } else {
                    return false;
                }
            }
            $score1 = $this->pk_res['point'][$this->pk_res['top'][2]] + $this->pk_res['point'][$this->pk_res['top'][3]];
            if ($score1 == 0) {
                $this->pk_res['win'][$this->pk_res['top'][0]] = 6;
                $this->pk_res['win'][$this->pk_res['top'][1]] = 6;
                $this->pk_res['win'][$this->pk_res['top'][2]] = -6;
                $this->pk_res['win'][$this->pk_res['top'][3]] = -6;
            } else {
                $this->pk_res['win'][$this->pk_res['top'][0]] = 3;
                $this->pk_res['win'][$this->pk_res['top'][1]] = 3;
                $this->pk_res['win'][$this->pk_res['top'][2]] = -3;
                $this->pk_res['win'][$this->pk_res['top'][3]] = -3;
            }
        } else if ($c == 3) {
            /* 必定结束 */
            if (empty($this->room_mingji_uid)) {
                /* 没明鸡，3个中2个必须为庄对手，另一个为庄家，第4名队友为庄，并找出庄第几个出玩牌 */
                $this->pk_res['partner'] = array_values(array_diff($this->pk_res['top'], [$this->room_banker_uid]));
                $v = array_diff($this->room_uids, $this->pk_res['top']);
                $this->pk_res['top'][3] = $v[key($v)];
                $i = array_search($this->room_banker_uid, $this->pk_res['top']);
            } else {
                /* 明鸡，找出第4名的队友 第几个出玩牌 */
                $v = array_diff($this->room_uids, $this->pk_res['top']);
                $this->pk_res['top'][3] = $v[key($v)];
                $i = 0;
                if (in_array($this->pk_res['top'][3], $this->pk_res['partner'])) {
                    if (in_array($this->pk_res['top'][1], $this->pk_res['partner'])) {
                        $i = 1;
                    }
                } else if ($this->pk_res['top'][3] == $this->room_banker_uid) {
                    if ($this->pk_res['top'][1] == $this->room_mingji_uid) {
                        $i = 1;
                    }
                } else if ($this->pk_res['top'][3] == $this->room_mingji_uid) {
                    if ($this->pk_res['top'][1] == $this->room_banker_uid) {
                        $i = 1;
                    }
                }
            }
            /* 第4名的队友第几个出完？ */
            if ($i == 0) {
                /* 1,4 */
                $score1 = $this->pk_res['point'][$this->pk_res['top'][0]] + $this->pk_res['point'][$this->pk_res['top'][3]];
                $score2 = 200 - $score1;
            } else if ($i == 1) {
                /* 2,4 */
                $score1 = $this->pk_res['point'][$this->pk_res['top'][1]] + $this->pk_res['point'][$this->pk_res['top'][3]] - 20;
                $score2 = 200 - $score1;
            }
            if ($i == 0) {
                if ($score1 <= 0) {
                    $s = -3;
                } else if ($score1 < 50) {
                    $s = -2;
                } else if ($score1 < 100) {
                    $s = -1;
                } else if ($score1 <= 150) {
                    $s = 1;
                } else if ($score1 < 200) {
                    $s = 2;
                } else {
                    $s = 3;
                }
                $this->pk_res['win'][$this->pk_res['top'][0]] = $s;
                $this->pk_res['win'][$this->pk_res['top'][1]] = -$s;
                $this->pk_res['win'][$this->pk_res['top'][2]] = -$s;
                $this->pk_res['win'][$this->pk_res['top'][3]] = $s;
            } else {
                if ($score1 <= 0) {
                    $s = -3;
                } else if ($score1 < 50) {
                    $s = -2;
                } else if ($score1 <= 100) {
                    $s = -1;
                } else if ($score1 <= 150) {
                    $s = 1;
                } else if ($score1 < 200) {
                    $s = 2;
                } else {
                    $s = 3;
                }
                $this->pk_res['win'][$this->pk_res['top'][0]] = -$s;
                $this->pk_res['win'][$this->pk_res['top'][1]] = $s;
                $this->pk_res['win'][$this->pk_res['top'][2]] = -$s;
                $this->pk_res['win'][$this->pk_res['top'][3]] = $s;
            }
        }
        $this->room_prev_first = $this->pk_res['top'][0];
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
        wlog(LOG_PATH.'room-'.date('Ym').'.log', $data);
        $ret = pdecode($data);
        if (empty($ret['a'])) {
        } else if ($ret['a'] == 'login') {
            $this->token($connection, $ret);
        } else if ($ret['a'] == 'room') {
            if (empty($ret['m'])) {
            } else if ($ret['m'] == 'add') {
                $this->joinRoom($connection, $ret);
            } else if ($ret['m'] == 'ready') {
                $this->ready($connection, $ret);
            } else if ($ret['m'] == 'play') {
                $this->play($connection, $ret);
            } else if ($ret['m'] == 'exit') {
                $this->bye($connection, $ret);
            }
        } else if ($ret['a'] == 'test') {
            //$ret = $this->{$ret['m']}($ret['d']);
            $this->pk_power = $connection->uid;
            $this->play($connection, $ret);
            /*$ret1 = $this->game->checkRule($ret['d']);
            if ($ret1 != false && !empty($ret['d'])) {
                $ret2 = $this->game->checkRule($ret['d'], $ret1);
                $ret = [$ret1, $ret2, $ret];
            } else {
                $ret = $ret1;
            }*/
        }
        //$ret = json_encode($ret, JSON_UNESCAPED_UNICODE);
        //$connection->send(pencode(['0', 'ok', $ret]));
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
     *  有用户离开房间；
     *  如果用户当前 在房间，且有牌，不可退出(退出扣6分，队友不扣分)；
     *  用户正常离开房间： unset $this->room_ids[$room_id][$uid], $this->room_uids[n=>$uid], $this->room_uids_poker[$uid] 对象；
     * @param 
     *  request:
     *      controller:action:arg1:arg2:arg n...
     *      room:exit:1
     * @return 
     */
    public function bye($connection, $ret = []) /* {{{ */
    {
        if (empty($ret['d']['a1'])) {
	        return ;
        }
        /* 不在牌局[当局结束了，且没点准备] */
        if (empty($this->room_uids_poker[$connection->uid])) {
            unset($this->room_ids[$connection->room_id][$connection->uid]);     /* room id */
            unset($this->room_uids_poker[$connection->uid]);                    /* uid poker */
            foreach ($this->room_uids as $k => $v) {
                if ($connection->uid == $v) {
                    unset($this->room_uids[$k]);                                /* room uid */
                }
            }
            $this->room_uids = array_values($this->room_uids);
            if (!empty($connection->room_id)) {
                $this->sendRoom($connection->room_id, pencode(['1', $connection->uid.' 离开了房间。']));
                wlog(LOG_PATH.'room-'.$connection->room_id.date('-Ym').'.log', json_encode([$connection->uid.' 离开了房间', $this->room_uids], JSON_UNESCAPED_UNICODE));
            }
            $connection->room_id = 0;
        } else {
            $connection->send(pencode(['50910', '牌局进行中，不可离开房间。']));
        }
	    return ;
    } /* }}} */

    /**
     * @fn
     * @brief 
     *  正常/异常断连接
     *  如果用户当前 在房间，且有牌，退出/断网等 为异常；
     *  用户正常关闭： unset $this->links[$uid]，$this->room_ids[$room_id][$uid]，$this->room_uids[n=>$uid] 对象；
     *  用户异常退出： unset $this->room_ids[$room_id][$uid]；TODO:设置定时，异常断开时，指定时间内不发起重连，则清理连接。
     *  异常退出 定时器 $connection->e_timer
     * @param 
     *  request:
     *      c:a:args...
     *      controller:action:arg1:arg2:arg n...
     * @return 
     */
    public function onClose($connection) /* {{{ */
    {
        /* 在牌局[当局结束了，且没点准备]，异常关闭，设置定时清理 */
        if (!empty($connection->room_id) && !empty($this->room_uids_poker[$connection->uid])) {
            unset($this->room_ids[$connection->room_id][$connection->uid]);     /* room id */
            $this->sendRoom($connection->room_id, pencode(['1', $connection->uid.' 网络中断。']));
            wlog(LOG_PATH.'room-'.$connection->room_id.date('-Ym').'.log', json_encode([$connection->uid.' 网络中断', $this->room_uids_poker], JSON_UNESCAPED_UNICODE));
        } else {
            /* 正常关闭 */
            foreach ($this->room_uids as $k => $v) {
                if ($connection->uid == $v) {
                    unset($this->room_uids[$k]);                                /* room uid */
                }
            }
            $this->room_uids = array_values($this->room_uids);
            unset($this->links[$connection->uid]);
            if (!empty($connection->room_id)) {
                $this->sendRoom($connection->room_id, pencode(['1', $connection->uid.' 离开了房间！']));
                unset($this->room_ids[$connection->room_id][$connection->uid]);     /* room id */
                wlog(LOG_PATH.'room-'.$connection->room_id.date('-Ym').'.log', json_encode([$connection->uid.' 离开了房间！', $this->room_uids], JSON_UNESCAPED_UNICODE));
            }
        }
	    return ;
    } /* }}} */
}

/* end file */
