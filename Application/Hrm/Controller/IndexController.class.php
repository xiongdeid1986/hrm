<?php

namespace Hrm\Controller;
use Think\Controller;
use Think\Hook;


class IndexController extends Controller {
    public function _initialize(){
		$action = array(
			'users'=>array('index'),
			'anonymous'=>array('')
		);
		exit("fdsadfas");
		B('Authenticate', $action);
	}
    public function index(){
        $this->assign('list',$list);
		$this->alert = parseAlert();
        $this->display();
    }
}