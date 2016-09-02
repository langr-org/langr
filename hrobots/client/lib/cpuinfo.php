<?php
/**
 * Windows System Info Plugin
 * $Id: cpuinfo.php 68 2014-05-24 07:59:05Z huanghua $
 */

function sys_windows() /* {{{ */
{
	$objLocator = new COM("WbemScripting.SWbemLocator");
	$wmi = $objLocator->ConnectServer();
	$prop = $wmi->get("Win32_PnPEntity");
	//CPU
	$cpuinfo = GetWMI($wmi, "Win32_Processor", array("Name","L2CacheSize","NumberOfCores"));
	$res['cpuinfo'] = $cpuinfo;
	//SYSINFO
	$sysinfo = GetWMI($wmi, "Win32_OperatingSystem", array('LastBootUpTime','TotalVisibleMemorySize','FreePhysicalMemory','Caption','CSDVersion','SerialNumber','InstallDate'));
	$res['OS'] = iconv('GBK', 'UTF-8', $sysinfo[0]['Caption']." ".$sysinfo[0]['CSDVersion']);
	$res['OS_SN'] = "{$sysinfo[0]['SerialNumber']} ".date('Y-m-d H:i:s',strtotime(substr($sysinfo[0]['InstallDate'],0,14)))." installed";
	//UPTIME
	$res['last_start'] = $sysinfo[0]['LastBootUpTime'];
	 
	//MEMORY
	$res['memory'] = $sysinfo[0]['TotalVisibleMemorySize'];
	$res['memory_free'] = $sysinfo[0]['FreePhysicalMemory'];
	$res['memory_used'] = $res['memory'] - $res['memory_free'];
	$res['memory_u/a'] = round($res['memory_used'] / $res['memory']*100,2);
 
	$swapinfo = GetWMI($wmi, "Win32_PageFileUsage", array('AllocatedBaseSize','CurrentUsage'));
 
	//swap区获取
	$res['swap'] = $swapinfo[0]['AllocatedBaseSize'];
	$res['swap_used'] = $swapinfo[0]['CurrentUsage'];
	$res['swap_free'] = $res['swap'] - $res['swap_used'];
	//$res['swap_u/a'] = (floatval($res['swap'])!=0)?round($res['swap_used']/$res['swap']*100,2):0;
 
	//LoadPercentage
	$loadinfo = GetWMI($wmi, "Win32_Processor", array("LoadPercentage"));
	$res['load_average'] = $loadinfo[0]['LoadPercentage'];
	 
	return $res;
} /* }}} */
 
function GetWMI($wmi, $strClass, $strValue = array()) /* {{{ */
{
	$arrData = array();
 
	$objWEBM = $wmi->Get($strClass);
	$arrProp = $objWEBM->Properties_;
	$arrWEBMCol = $objWEBM->Instances_();
	foreach($arrWEBMCol as $objItem) {
		@reset($arrProp);
		$arrInstance = array();
		foreach($arrProp as $propItem) {
			eval("\$value = \$objItem->" . $propItem->Name . ";");
			if (empty($strValue)) {
				$arrInstance[$propItem->Name] = trim($value);
			} else {
				if (in_array($propItem->Name, $strValue)) {
					$arrInstance[$propItem->Name] = trim($value);
				}
			}
		}
		$arrData[] = $arrInstance;
	}
	return $arrData;
} /* }}} */

/* end file */
