<?php
/**
 * @file inc.class.php
 * @brief 
 * 
 * Copyright (C) 2011 WiseTalk.cn
 * All rights reserved.
 * 
 * @package tests
 * @author Langr <hua@langr.org> 2011/11/23 17:23
 * 
 * $Id: inc.class.php 17 2011-12-13 10:34:09Z loghua@gmail.com $
 */

	/**
	 *通道数据分析
	 */
	function channelAnalyse($exten, $data)
	{
		$res	= explode("active", $data);
		$res1	= explode("(Data)", $res[0]);
		$i		= 0;
		//var_dump($res);
		//var_dump($res1);

		$mydata = preg_split('/\n/',$res1[1],-1,1);
		//print_r($mydata);
		$count	= count($mydata);

		while($i <=($count-1)) {
			$channelpro	= preg_split('/ /',$mydata[$i],-1,1);
		//echo "\r\nmydate$i:"; var_dump($channelpro);
			$channel[$i]	= $channelpro[0];
			$i++;
		}
		$i=1;
		while($i <=($count-1))
		{
			if(strstr($exten,'Zap')){
				$num	= explode("-", $channel[$i]);
				$num	= $num[0];
			}else{
				$tem	= preg_match('/\/(\d*)-/',$channel[$i],$matches);
				$num	= $matches[1];
				$tem1	= preg_match('/\/(\d*)@/',$channel[$i],$matches);
				$num1	= $matches[1];
			}
			/*if ($num == $exten) {} */
			if( strstr($channel[$i], $exten)  ) {
				if (!strstr($mydata[$i],"Local")) {     //added by jeff if2
					if (strstr($mydata[$i],"Bridged")){
						$mydata[$i]		= str_replace("(None)","@",$mydata[$i]);
						$tem			= preg_match('/\(.*\)/',$mydata[$i],$matches);
						$data			= str_replace("(","",$matches[0]);
						$mychannel[1]	= str_replace(")","",$data);
						$mychannel[0]	= $channel[$i];
						break;
					} else {
						for ($j=1;$j<=$count;$j++)
						{
							if (strstr($mydata[$j],$channel[$i]) & strstr($mydata[$j],"Bridged")) {
								$mychannel[1]	= $channel[$j];
								$mychannel[0]	= $channel[$i];
								break;
							}
						}//end for
					}//end if
				}else{//通道信息中包含'Local'字样
					if (strstr($mydata[$i],"Bridged")){
						$mydata[$i]		= str_replace("(None)","@",$mydata[$i]);
						$tem			= preg_match('/\(.*@/',$mydata[$i],$matches); //只匹配左花括号到'@'(最大长度限制) 
						$mid_channel	= str_replace("(","",$matches[0]);
						$mychannel[0]	= $channel[$i];

						//利用内部转接形成的通道查找(带'@from-internal'字样)
						for ($j=1;$j<=$count-1;$j++)
						{
							if (strstr($mydata[$j],$mid_channel) && strstr($mydata[$j],"Transferred")) {
								$mychannel[1]	= $channel[$j];
								if( !strstr($mychannel[1], "Local") )//如果找到外线通道，退出
									break;
								else{//找到的是'Local/xxx@xxx'(多次转接)，继续查找
									$ch			= preg_match('/(Local.*)@/',$mychannel[1], $matches);
									$mid_channel= $matches[1];
								}
							}//end if
						}//end for
					}//end if
					//------------------------
				}//end if
			 }//end if
			/*if ($num1 == $exten) {}*/
			/*
			if( strstr($num1, $exten)  ) {
				  if (strstr($mydata[$i],"Bridged")){
					  $mydata[$i]	= str_replace("(None)","@",$mydata[$i]);
					  $tem			= preg_match('/\(.*\)/',$mydata[$i],$matches);
					  $data			= str_replace("(","",$matches[0]);
					  $mychannel[1]	= str_replace(")","",$data);
					  $mychannel[0]	= $channel[$i];
					  //echo $mychannel[1];
					  break;
				  }
			}
			*/
		$i++;
		}
		return $mychannel;
	}

	/**
	 * @fn
	 * @brief 
	 * @param 
	 * @param  
	 * @return 
	 */
	function getHints()
	{
		$ainfo = lagi_command("core show hints");
		$lines = explode("\n", $ainfo['data']);
		foreach ( $lines as $key => $line )
		{
				if(preg_match("/State:/", $line))
				{
					$array =  preg_split("/\s+/",$line);
					if (strstr($array[1],"@"))
					{
					   $tmp=explode( "@",$array[1] );
					   $array[1]=$tmp[0];
					}
					$type = explode("/", $line);
					$type = explode(':', $type[0]);
					$hints[$array[1]]['type'] = $type[1];
					$hints[$array[1]]['stat'] = $array[4];
					$hints[$array[3]]['dial'] = $array[4];
				}
		}
		return $hints;
	}

	/**
	 * @fn
	 * @brief 点击拔号
	 * @param $exten
	 * @param $callTel 被呼电话 
	 * @return '0' ok, xxx 出错号
	 */
	function onClickCall($exten, $callTel)
	{
		$fd = lagi_connect('192.168.1.226', 5038, 'admin', 'amp111');
		$re = getHints();
		$re = lagi_outcall('SIP/801', "801", '810');
	}

	/**
	 * @fn
	 * @brief 示忙/闲
	 * @param $exten
	 * @param $status YES/NO
	 * @return '0' ok, xxx 出错号
	 */
	function extenDND($enten, $status)
	{
		lagi_put_db('DND/$exten', $status);
	}

	function parkAnalyse($channel,$test){
		$res=explode("Timeout", $test);
		$res1=explode("parked", $res[1]);
		$parkpro=preg_split('/ /',$res1[0],-1,1);
		$count=count($parkpro);
		$i=1;
		foreach ($parkpro as $key => $val){
			if ( $val == $channel ){
				$pre = $key-1;
				$result=$parkpro[$pre];
				break;
			 }
		}
		return $result;
	}

	function parkChannelAnalyse($park,$test){
		$res=explode("Timeout", $test);
		$res1=explode("parked", $res[1]);

		$parkpro=preg_split('/ /',$res1[0],-1,1);
		$count=count($parkpro);

		$i=0;
		while ($i<=$count) {
			$next=$i+1;
			if ($parkpro[$next]== $park) {
				$result=$parkpro[$next];
				break;
			}
			$i=$i+1;
		}
		return $result;
	}

/* end file */
