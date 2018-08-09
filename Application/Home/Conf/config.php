<?php

return array(
	'TMPL_PARSE_STRING' => array(
        '__UPLOAD__' => __ROOT__ . '/Upload',
        '__PUBLIC__' => __ROOT__ . '/Public',
        '__IMG__' => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
        '__CSS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
        '__JS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/js'
    ),
'HTML_FILE_SUFFIX'=> '.html',  // 设置静态缓存文件后缀
	
    //'TMPL_EXCEPTION_FILE' => './404.html',
);

?>