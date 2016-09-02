<?php
	if (("GET" == $_SERVER['REQUEST_METHOD'])&&(empty($_GET['noHtml']))) {
		/* 获取当前项目分组的名称 腳本檔案名 -> index.php */
		$promptPrefixArr = split("\.",basename($_SERVER['SCRIPT_FILENAME']));
		$promptPrefix = $promptPrefixArr[0];
		$module = isset($_GET['module']) ? $_GET['module'] : "index";
		$action = isset($_GET['action']) ? $_GET['action'] : "index";
		$htmlIncFileName = "./include/config/".$promptPrefix."_html.inc.php";
		if (file_exists($htmlIncFileName)) {
			require_once($htmlIncFileName);
			if (is_array($htmlSetup[$module])) {
				/* 检查当前Action是否充许静态 */
				$checkAction = False;
				if (empty($htmlSetup[$module]['denyAction'])) {
					$accessActionArray = split(",",$htmlSetup[$module]['accessAction']);
					if (in_array($action,$accessActionArray)) {
						$checkAction = True;
					}
				} else {
					$denyActionArray = split(",",$htmlSetup[$module]['denyAction']);
					if (!in_array($action,$denyActionArray)) {
						$checkAction = True;
					}
				}
				if ($checkAction) {
					/* 获取静态文件名 */
					$htmlPath = CFG_TEMPLATE_PATH."/".$promptPrefix."/".$module."/";
					$effectiveGetVarArr = split(",",$htmlSetup[$module]['effectiveGetVarArr']);
					while (list($key,$val) = @each($effectiveGetVarArr)) {
						if (!empty($_GET[$val])) {
							$theGet[$val] = $_GET[$val];
						}
					}
					if (is_array($theGet)) {
						$htmlFileName = $action.implode("_",$theGet).".html";
					} else {
						$htmlFileName = $action."_.html";
					}
					define('HTML_FILE', $htmlPath.$htmlFileName);
					if (file_exists(HTML_FILE)) {
						$goToHtml = True;
						$htmlFileTime = filemtime(HTML_FILE);
						if ((time()-$htmlFileTime)>$htmlSetup[$module]['effectiveTime']) {
							$goToHtml = False;
						} else {
							/* 获取模板文件名 */
							$tmplPath = "./tmpl/".$promptPrefix."/";
							$tmplFile = $tmplPath.$module."/".$action.".tpl.php";
							if (filemtime($tmplFile) > $htmlFileTime) {
								$goToHtml = False;
							} else {
								$subTemplateArr = split(",",$htmlSetup[$module]['subTemplate']);
								while (list($key,$val) = @each($subTemplateArr)) {
									if (!empty($val)) {
										$tmplFile = $tmplPath.$val;
										if (filemtime($tmplFile) > $htmlFileTime) {
											$goToHtml = False;
											break;
										}
									}
								}
							}
						}
						if ($goToHtml) {
							include_once(HTML_FILE);
							exit;
						}
					}
				}
			}
		}
	}
?>
