<?php
/** 
 * @file index.php
 * @brief 
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package hiphp
 * @author Langr <hua@langr.org> 2011/11/15 00:50
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * $Id: index.php 21 2012-05-17 10:47:34Z loghua@gmail.com $
 */

include('hiphp/basics.php');
include('hiphp/router.php');
include('hiphp/define.php');
include('hiphp/hiobject.php');
include('hiphp/dispatcher.php');
$__inc_files[] = __FILE__;

phpinfo();
echo "<pre>"; var_dump($__inc_files); echo"</pre>";
/* end file */
