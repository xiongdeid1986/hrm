<?php 
class NavigationWidget extends Widget {
	public function render($data){
		$navigation = D('Navigation')->getNavigationList();
		$module = D('User')->getModuleName();
		$info = D('Navigation')->getCurrentControl();
		return $this->renderFile ("index", array('navigation'=>$navigation,'info'=>$info,'module'=>$module));
	}
}