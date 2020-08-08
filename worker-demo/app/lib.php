<?php
/**
 * @file lib.php
 * @brief 
 * 
 * Copyright (C) 2020 Langr.Org
 * All rights reserved.
 * 
 * @package app
 * @author xxx <xxx@xxx.org> 2020/05/18 16:01
 * 
 * $Id$
 */

if (!function_exists('wlog')) {
    /**
     * @fn
     * @brief 日志记录函数
     * @param $log_file    日志文件名
     * @param $log_str    日志内容
     * @param $show        日志内容是否show出
     * @param $log_size    日志文件最大大小，默认20M
     * @return void
     */
    function wlog($log_file, $log_str, $show = false, $log_size = 20971520) /* {{{ */
    {
        ignore_user_abort(TRUE);
    
        $time = '['.date('Y-m-d H:i:s').'] ';
        if ( $show ) {
            echo $time.$log_str.((PHP_SAPI == "cli") ? "\r\n" : "<br>\r\n");
        }
        if ( empty($log_file) ) {
            $log_file = 'wlog.txt';
        }
        if ( defined('APP_LOG_PATH') ) {
            $log_file = APP_LOG_PATH.$log_file;
        }
        // 判断目录是否存在， 不存在创建
        $path = dirname($log_file);
        if(!file_exists($path)){
            @mkdir($path,0755,true);
        }
        $newfile = false;
        if ( !file_exists($log_file) ) { 
            $fp = fopen($log_file, 'a');
            $newfile = true;
        } else if ( filesize($log_file) > $log_size ) {
            $fp = fopen($log_file, 'w');
        } else {
            $fp = fopen($log_file, 'a');
        }
    
        if ( flock($fp, LOCK_EX) ) {
            $cip = empty($_SERVER["HTTP_X_FORWARDED_FOR"]) ? (empty($_SERVER['REMOTE_ADDR']) ? '' : $_SERVER['REMOTE_ADDR']) : $_SERVER["HTTP_X_FORWARDED_FOR"];
            $log_str = $time.'['.$cip.'] '.$log_str."\r\n";
            fwrite($fp, $log_str);
            flock($fp, LOCK_UN);
        }
        fclose($fp);

        if ($newfile === TRUE) {
            chmod($log_file, 0644);
        }
    
        ignore_user_abort(FALSE);
    } /* }}} */
}

if (!function_exists('pdecode')) {
    /**
     * @fn
     * @brief 协议解包函数
     * @param $content    包内容
     * @param $p    txt/json/xml
     * @return 解包后的内容数组[]
     */
    function pdecode($content = '', $p = 'txt') /* {{{ */
    {
        $ret = json_decode($content, true);
        return $ret;
    } /* }}} */
}

if (!function_exists('pencode')) {
    /**
     * @fn
     * @brief 协议打包函数
     * @param $msg  内容s
     * @return 打包后的包体
     */
    function pencode($msg = [], $p = 'txt') /* {{{ */
    {
        $ret['c'] = isset($msg[0]) ? $msg[0] : 0;
        $ret['m'] = isset($msg[1]) ? $msg[1] : 'ok';
        if (!empty($msg[2])) {
            $ret['d'] = $msg[2];
        }
        return json_encode($ret, JSON_UNESCAPED_UNICODE);
    } /* }}} */
}

/* end file */
