<?php 
class AuthenticateBehavior extends Behavior {
	protected $options = array();
	
	public function run(&$actions) {
		$g = GROUP_NAME;
		$m = MODULE_NAME;
		$a = ACTION_NAME;
		$users = $actions['users'];
		$anonymous = $actions['anonymous'];
		
		if (session('?admin')) {
			return true;
		}
		if (in_array($a, $anonymous)) {
			return true;
		} elseif (session('?user_id')) {
			if (in_array($a, $users)) {
				return true;
			} else {
				$control_id = M('control')->where(array('g'=>strtolower($g),'m'=>strtolower($m),'a'=>strtolower($a)))->getField('control_id');
				if (!empty($control_id) && in_array($control_id , explode(',',session('control_ids')))) {
					return true;
				} else {
					$url = empty($_SERVER['HTTP_REFERER']) ? U($g.'/index/index') : $_SERVER['HTTP_REFERER'];
					alert('error', '您没有此权利!',$url);
				}
			}
		} else {
			if($g ==  C('DEFAULT_GROUP') && $m == C('DEFAULT_MODULE') && $a == C('DEFAULT_ACTION')){
				redirect(U('core/user/login'));
			}else{
				alert('error',  '请先登录...', U('core/user/login'));
			}
		}
	}
}