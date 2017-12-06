<?php
class IndexAction extends Action {
    public function _initialize(){
		$action = array(
			'users'=>array('index'),
			'anonymous'=>array('')
		);
		B('Authenticate', $action);
	}
    public function index(){
        $this->assign('list',$list);
		$this->alert = parseAlert();
        $this->display();
    }
}