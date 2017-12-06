<?php 
namespace Library\Behavior;

class AppBehavior{
	protected $options = array();
	
	public function run(&$params) {
		if (!file_exists(APP_PATH . 'Conf/hrm-install.lock') && MODULE_NAME != 'Install') {
			//redirect(U('Core/install/index'));
            
		} elseif(MODULE_NAME != 'Install') {
			if (!F('smtp')) {
				F('smtp',D('Config')->getConfig('smtp'));			
			}
			C('smtp', F('smtp'));
			if (!F('defaultinfo')) {
				F('defaultinfo',D('Config')->getConfig('defaultinfo'));			
			}
			C('defaultinfo', F('defaultinfo'));
			if (!F('contracttype')) {
				F('contracttype',D('Config')->getConfig('contracttype'));			
			}
			C('contracttype', F('contracttype'));
			if (!F('contractstatus')) {
				F('contractstatus',D('Config')->getConfig('contractstatus'));			
			}
			C('contractstatus', F('contractstatus'));
		}
	}
}