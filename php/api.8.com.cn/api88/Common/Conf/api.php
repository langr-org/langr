<?php
/* #!/usr/local/bin/php -f */

//if (!defined('APP_DEBUG')) { define('APP_DEBUG', true); }
//if (!defined('API_DEBUG')) { define('API_DEBUG', true); }
if (!defined('KEY_PREFIX')) { define('KEY_PREFIX', 'api'); };
if (!defined('EXPIRES_TIME')) { define('EXPIRES_TIME', 3600); };	/* token过期时间 */

if (!defined('SMS_CC')) { define('SMS_CC', true); }					/* 短信验证码防刷验证 */
if (!defined('SMS_KEY')) { define('SMS_KEY', '05ff957b67730b1e'); }	/* crypt key */
if (!defined('SMS_IV')) { define('SMS_IV', 'ThisIsASecretKet'); }	/* crypt iv */

if (!defined('H2H_COOKIE_PATH')) { define('H2H_COOKIE_PATH', LOG_PATH); }
if (!defined('APP_LOG_PATH')) { define('APP_LOG_PATH', LOG_PATH); }

if (!defined('CLIENT_IP')) { define('CLIENT_IP', getenv('HTTP_X_FORWARDED_FOR') ? getenv('HTTP_X_FORWARDED_FOR') : getenv('REMOTE_ADDR')); }
/* end file */
