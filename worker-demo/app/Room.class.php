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
    private $pk = '<>ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    /* 房间总连接(人数) */
    private $links = [];
    /* 房号 及 房间人员，[room_id=>[link_id,link_id,link_id,link_id], room_id=>[]] */
    private $room_ids = [];
    /* 桌座位顺序，每桌人员按加入时间逆时针排序 */
    private $room_uids = [];        // [0=>'', 1=>'', 2=>'', 3=>''];
    private $room_uids_poker = [];  // [uid=>[], uid=>[], uid=>[], uid=>[]];
    /* 上局出牌第一名，下局做庄 */
    private $room_game_prev = '';
    /* 当前局庄 */
    private $room_banker_uid = '';
    /* 当前局庄报的牌 */
    private $room_poker = '';
    /* 是否明鸡，庄跟明鸡同家，另外2边同一家 */
    private $room_mingji_uid = '';
    /* 出牌权 uid，第一手为庄 */
    private $pk_power = null;
    private $pk_power_timer_id = null;
    /**
     * 上一手牌，
     * ['uid'=>'ABC']
     * 为空表示该轮第一手牌，可出任意牌；
     * uid = 当前用户，表示上一圈都 pass, 可以出任意牌；
     */
    private $pk_prev = [];
    private $pk_prev_uid = '';
    private $pk_prev_px = '';
    /* 当圈出现的分数 */
    private $pk_quan_point = 0;
    /* 当局得分结果 */
    private $pk_res = [];

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
     * @return 
     */
    public function add(& $connection, $ret = []) /* {{{ */
    {
        if (empty($ret[2])) {
            $connection->send(pencode(['10011', 'room id error!']));
	        return ;
        }
        $msg = 'join room';
        /* 检测房间人员 */
        if (empty($this->room_ids[$ret[2]])) {
            $msg = 'create room';
            $this->room_game_prev = $connection->uid;
        } else if (count($this->room_ids[$ret[2]]) >= DESK_PEOPLE) {
            /* 满员 */
            $connection->send(pencode(['10012', 'room full!']));
	        return ;
        }
        $connection->room_id = $ret[2];
        $this->room_ids[$ret[2]][$connection->uid] = $connection;   /* room id */
        $this->room_uids[] = $connection->uid;                      /* room uid */
        $connection->send(pencode([0, $msg.' '.$ret[2].' ok!']));
	    return ;
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
            $this->room_banker_uid = $this->room_game_prev;
            $this->room_poker = 'a';
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
            $connection->send(pencode(['10031', $connection->uid.' have no right!']));
            return ;
        }
        /* 打牌后开始清除当手牌超时时间，并设置下一手牌超时时间；超时自动出牌：过牌/出最小单张 */
        if (empty($ret[2]) || $ret[2] == 0) {
            $connection->send(pencode(['1', 'pass!']));
            $this->pk_power = '';
	        return ;
        }
        $connection->room_id = $ret[2];
        $this->room_ids[$ret[2]][] = $connection;      /* room id */
        $connection->send(pencode([0, 'add room '.$ret[2].' ok!']));
	    return ;
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
                $this->token($connection, $ret);
            }
        }
        $ret = json_encode($ret, JSON_UNESCAPED_UNICODE);
        $connection->send(pencode(['r', 'm', $ret]));
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
        $connection->room_id = $ret[2];
        $this->room_ids[$ret[2]][] = null;      /* room id */
	    return ;
    } /* }}} */
}

/* end file */
