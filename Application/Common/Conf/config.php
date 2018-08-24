<?php

return array(
	'DB_TYPE'              => DB_TYPE,
	'DB_HOST'              => DB_HOST,
	'DB_NAME'              => DB_NAME,
	'DB_USER'              => DB_USER,
	'DB_PWD'               => DB_PWD,
	'DB_PORT'              => DB_PORT,
	'DB_PREFIX'            => 'qq3479015851_',
	'ACTION_SUFFIX'        => '',
	'MULTI_MODULE'         => true,
	'MODULE_DENY_LIST'     => array('Common', 'Runtime'),
	'MODULE_ALLOW_LIST'    => array('Home', 'Admin'),
	'DEFAULT_MODULE'       => 'Home',
	'URL_CASE_INSENSITIVE' => false,
	'URL_MODEL'            => 2,
	'URL_HTML_SUFFIX'      => 'html',
	'UPDATE_PATH'          => '',
	'CLOUD_PATH'           => '',
    'HOST_IP'	=> '',
	'TMPL_CACHFILE_SUFFIX' =>'.html',
	'DATA_CACHE_TYPE'      => 'file',
	'URL_PARAMS_SAFE'	   => true,
	'URL_PARAMS_FILTER'    => true,
	'DEFAULT_FILTER'       =>'check_qq3479015851,htmlspecialchars,strip_tags',
	'URL_PARAMS_FILTER_TYPE' =>'check_qq3479015851,htmlspecialchars,strip_tags',
    'LANG_SWITCH_ON'       =>     true,    //开启语言包功能
    'LANG_AUTO_DETECT'     =>     true, // 自动侦测语言
    'DEFAULT_LANG'         =>     'en-us', // 默认语言
    'LANG_LIST'            =>     'en-us,zh-cn', //必须写可允许的语言列表
    'VAR_LANGUAGE'         =>     'l', // 默认语言切换变量
	);
?>