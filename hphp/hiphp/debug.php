<?php
/** 
 * @file debug.php
 * @brief 
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package hiphp
 * @author Langr <hua@langr.org> 2011/11/17 00:17
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * $Id: debug.php 21 2012-05-17 10:47:34Z loghua@gmail.com $
 */
$__inc_files[] = __FILE__;

if ( !class_exists('hiObject') ) {
	require_once c('HIPHP').'hiObject.php';
}

/**
 * @class debug
 * @brief 调试，日志记录，出错异常(安装hooks)主函数
 * debug::dump(), debug::assert(true, $errno, $msg, $callback=errorHandler::doerror())
 * debug::log(), wlog()
 * debug::error(), debug::warning(), debug::message() => debug::_debug()
 * debug::addHandler($errno, $callback = null)
 * debug($level, $errno, $msg, $callback = null)
 */
class debug extends hiObject 
{
	/**
	 * @var $level
	 * debug level:
	 * 0, no debug info output
	 * 1, error info
	 * 2, warning info
	 * 3, note info
	 * 4, message
	 * 调试级别越高, 输出调试信息越多, 0 不输出调试信息.
	 */
	protected $level = 0;

	/**
	 * @var $warn_action
	 * error 告警方式:
	 * 当出现 error 错误时的处理动作, 或在应用层调用时指定 $callback 处理.
	 * 除 file 外, 其他需要安装相应的 errorHandler.
	 * file, $warn_receiver = filename
	 * email, $warn_receiver = email
	 * sms, $warn_receiver = mobile phone
	 * tel, $warn_receiver = telephone number
	 */
	protected $warn_action = 'file';
	protected $warn_receiver = 'hiphp-debug.txt';

	function __construct($level = 0) /* {{{ */
	{
		$level = 0;
	} /* }}} */

	function & getInstance($class = null) /* {{{ */
	{
		static $instance = array();
		if ( !empty($class) ) {
			if ( !$instance || strtolower($class) != strtolower(get_class($instance[0])) ) {
				$instance[0] = & new $class();
			}
		}

		if ( !$instance ) {
			$instance[0] = & new debug();
		}

		return $instance[0];
	} /* }}} */

	/**
	 * @fn
	 * @brief 功能, 参数待定.
	 * @param 
	 * @return 
	 */
	function set($level = null, $warn_action = null, $warn_receiver = null) /* {{{ */
	{
		return ;
	} /* }}} */

	function dump($var) /* {{{ */
	{
		$_this = & debug::getInstance();
		pr($_this->exportVar($var));
	} /* }}} */

	function log($var, $level = LOG_DEBUG) {
		$_this =& Debugger::getInstance();
		$source = $_this->trace(array('start' => 1)) . "\n";
		CakeLog::write($level, "\n" . $source . $_this->exportVar($var));
	}

	function excerpt($file, $line, $context = 2) {
		$data = $lines = array();
		if (!file_exists($file)) {
			return array();
		}
		$data = @explode("\n", file_get_contents($file));

		if (empty($data) || !isset($data[$line])) {
			return;
		}
		for ($i = $line - ($context + 1); $i < $line + $context; $i++) {
			if (!isset($data[$i])) {
				continue;
			}
			$string = str_replace(array("\r\n", "\n"), "", highlight_string($data[$i], true));
			if ($i == $line) {
				$lines[] = '<span class="code-highlight">' . $string . '</span>';
			} else {
				$lines[] = $string;
			}
		}
		return $lines;
	}

/**
 * Converts a variable to a string for debug output.
 *
 * @param string $var Variable to convert
 * @return string Variable as a formatted string
 * @access public
 * @static
 * @link http://book.cakephp.org/view/1191/Using-the-Debugger-Class
 */
	function exportVar($var, $recursion = 0) {
		$_this =& Debugger::getInstance();
		switch (strtolower(gettype($var))) {
			case 'boolean':
				return ($var) ? 'true' : 'false';
			break;
			case 'integer':
			case 'double':
				return $var;
			break;
			case 'string':
				if (trim($var) == "") {
					return '""';
				}
				return '"' . h($var) . '"';
			break;
			case 'object':
				return get_class($var) . "\n" . $_this->__object($var);
			case 'array':
				$var = array_merge($var,  array_intersect_key(array(
					'password' => '*****',
					'login'  => '*****',
					'host' => '*****',
					'database' => '*****',
					'port' => '*****',
					'prefix' => '*****',
					'schema' => '*****'
				), $var));

				$out = "array(";
				$vars = array();
				foreach ($var as $key => $val) {
					if ($recursion >= 0) {
						if (is_numeric($key)) {
							$vars[] = "\n\t" . $_this->exportVar($val, $recursion - 1);
						} else {
							$vars[] = "\n\t" .$_this->exportVar($key, $recursion - 1)
										. ' => ' . $_this->exportVar($val, $recursion - 1);
						}
					}
				}
				$n = null;
				if (!empty($vars)) {
					$n = "\n";
				}
				return $out . implode(",", $vars) . "{$n})";
			break;
			case 'resource':
				return strtolower(gettype($var));
			break;
			case 'null':
				return 'null';
			break;
		}
	}

/**
 * Handles object to string conversion.
 *
 * @param string $var Object to convert
 * @return string
 * @access private
 * @see Debugger::exportVar()
 */
	function __object($var) {
		$out = array();

		if (is_object($var)) {
			$className = get_class($var);
			$objectVars = get_object_vars($var);

			foreach ($objectVars as $key => $value) {
				if (is_object($value)) {
					$value = get_class($value) . ' object';
				} elseif (is_array($value)) {
					$value = 'array';
				} elseif ($value === null) {
					$value = 'NULL';
				} elseif (in_array(gettype($value), array('boolean', 'integer', 'double', 'string', 'array', 'resource'))) {
					$value = Debugger::exportVar($value);
				}
				$out[] = "$className::$$key = " . $value;
			}
		}
		return implode("\n", $out);
	}

/**
 * Switches output format, updates format strings
 *
 * @param string $format Format to use, including 'js' for JavaScript-enhanced HTML, 'html' for
 *    straight HTML output, or 'txt' for unformatted text.
 * @param array $strings Template strings to be used for the output format.
 * @access protected
 */
	function output($format = null, $strings = array()) {
		$_this =& Debugger::getInstance();
		$data = null;

		if (is_null($format)) {
			return $_this->_outputFormat;
		}

		if (!empty($strings)) {
			if (isset($_this->_templates[$format])) {
				if (isset($strings['links'])) {
					$_this->_templates[$format]['links'] = array_merge(
						$_this->_templates[$format]['links'],
						$strings['links']
					);
					unset($strings['links']);
				}
				$_this->_templates[$format] = array_merge($_this->_templates[$format], $strings);
			} else {
				$_this->_templates[$format] = $strings;
			}
			return $_this->_templates[$format];
		}

		if ($format === true && !empty($_this->_data)) {
			$data = $_this->_data;
			$_this->_data = array();
			$format = false;
		}
		$_this->_outputFormat = $format;

		return $data;
	}

/**
 * Renders error messages
 *
 * @param array $data Data about the current error
 * @access private
 */
	function _output($data = array()) {
		$defaults = array(
			'level' => 0,
			'error' => 0,
			'code' => 0,
			'helpID' => null,
			'description' => '',
			'file' => '',
			'line' => 0,
			'context' => array()
		);
		$data += $defaults;

		$files = $this->trace(array('start' => 2, 'format' => 'points'));
		$code = $this->excerpt($files[0]['file'], $files[0]['line'] - 1, 1);
		$trace = $this->trace(array('start' => 2, 'depth' => '20'));
		$insertOpts = array('before' => '\{:', 'after' => '\}');
		$context = array();
		$links = array();
		$info = '';

		foreach ((array)$data['context'] as $var => $value) {
			$context[] = "\${$var}\t=\t" . $this->exportVar($value, 1);
		}

		switch ($this->_outputFormat) {
			case false:
				$this->_data[] = compact('context', 'trace') + $data;
				return;
			case 'log':
				$this->log(compact('context', 'trace') + $data);
				return;
		}

		if (empty($this->_outputFormat) || !isset($this->_templates[$this->_outputFormat])) {
			$this->_outputFormat = 'js';
		}

		$data['id'] = 'cakeErr' . count($this->errors);
		$tpl = array_merge($this->_templates['base'], $this->_templates[$this->_outputFormat]);
		$insert = array('context' => join("\n", $context), 'helpPath' => $this->helpPath) + $data;

		$detect = array('help' => 'helpID', 'context' => 'context');

		if (isset($tpl['links'])) {
			foreach ($tpl['links'] as $key => $val) {
				if (isset($detect[$key]) && empty($insert[$detect[$key]])) {
					continue;
				}
				$links[$key] = String::insert($val, $insert, $insertOpts);
			}
		}

		foreach (array('code', 'context', 'trace') as $key) {
			if (empty($$key) || !isset($tpl[$key])) {
				continue;
			}
			if (is_array($$key)) {
				$$key = join("\n", $$key);
			}
			$info .= String::insert($tpl[$key], compact($key) + $insert, $insertOpts);
		}
		$links = join(' | ', $links);
		unset($data['context']);

		echo String::insert($tpl['error'], compact('links', 'info') + $data, $insertOpts);
	}

/**
 * Verifies that the application's salt and cipher seed value has been changed from the default value.
 *
 * @access public
 * @static
 */
	function checkSecurityKeys() {
		if (Configure::read('Security.salt') == 'DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi') {
			trigger_error(__('Please change the value of \'Security.salt\' in app/config/core.php to a salt value specific to your application', true), E_USER_NOTICE);
		}

		if (Configure::read('Security.cipherSeed') === '76859309657453542496749683645') {
			trigger_error(__('Please change the value of \'Security.cipherSeed\' in app/config/core.php to a numeric (digits only) seed value specific to your application', true), E_USER_NOTICE);
		}
	}

/**
 * Invokes the given debugger object as the current error handler, taking over control from the
 * previous handler in a stack-like hierarchy.
 *
 * @param object $debugger A reference to the Debugger object
 * @access public
 * @static
 * @link http://book.cakephp.org/view/1191/Using-the-Debugger-Class
 */
	function invoke(&$debugger) {
		set_error_handler(array(&$debugger, 'handleError'));
	}
}

if (!defined('DISABLE_DEFAULT_ERROR_HANDLING')) {
	Debugger::invoke(Debugger::getInstance());
}

/* end file */
