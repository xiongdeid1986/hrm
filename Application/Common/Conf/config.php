<?php
return array(
	'URL_MODEL'				=>0,
	'URL_CASE_INSENSITIVE'	=>true,
	'TMPL_ACTION_ERROR'		=>'Public:message', 
	'TMPL_ACTION_SUCCESS'	=>'Public:message',
	'TMPL_EXCEPTION_FILE'	=>'./Application/Hrm/View/exception.html',
	'DEFAULT_TIMEZONE'		=>'PRC',
    'LOG_LEVEL'				=>'EMERG',
    'APP_GROUP_LIST'		=>'Core,Hrm,Crm',
    'APP_GROUP_NAME'		=>array('hrm'=>'人力资源管理','core'=>'系统','crm'=>'客户管理'),
    'DEFAULT_GROUP'			=>'Core',//默认组.
	'LOAD_EXT_CONFIG'		=>'db_config,version',
	/*------配置项'=>'配置值-------*/
    'LOAD_EXT_FILE'			=>'extend_function',
    'SHOW_PAGE_TRACE'		=>true,
);