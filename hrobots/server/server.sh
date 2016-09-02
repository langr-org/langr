#!/bin/sh
# category collect.
# $Id: server.sh 101 2014-06-10 01:49:31Z huanghua $
# 
# rpath=/web/kitsmall/rebots/hrebots/server
php_path=/usr/bin/php
# 
# collect category
# $php_path -f index.php Collect/category
$php_path -f index.php Collect/category/mod/digikey
$php_path -f index.php Collect/category/mod/mouser
# 
# collect goods list
# $php_path -f index.php Collect/goodslist
$php_path -f index.php Collect/goodslist/mod/digikey
$php_path -f index.php Collect/goodslist/mod/mouser
#
# push to bigdata
$php_path -f index.php Push/index/mod/digikey
$php_path -f index.php Push/index/mod/mouser
#
