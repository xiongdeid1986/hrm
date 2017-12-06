<?php
class LogAction extends Action{
	public function _initialize(){
		$action = array(
			'users'=>array('index', 'add', 'view', 'edit', 'delete'),
			'anonymous'=>array('')
		);
		B('Authenticate', $action);
	}
	
	//日志列表
	public function index(){
		$search_title = empty($_GET['search_title']) ? '' : trim($_GET['search_title']);
		$search_content = empty($_GET['search_content']) ? '' : trim($_GET['search_content']);
		$search_user = empty($_GET['search_creator_id']) ? '' : intval($_GET['search_creator_id']);
		$search_create_time = empty($_GET['search_create_time']) ? '' : strtotime($_GET['search_create_time']);
		
		if(!empty($search_title)){
			$condition['title'] = array('like', '%'.$search_title.'%');
		}
		if(!empty($search_content)){
			$condition['content'] = array('like', '%'.$search_content.'%');
		}
		if(!empty($search_user)){
			$condition['creator_user_id'] = array('eq', $search_user);
		}else{
			$condition['creator_user_id'] = array('eq', session('user_id'));
		}
		if(!empty($search_create_time)){
			$condition['create_time'] = array('between', array($search_create_time-1, $search_create_time+86400));
		}	
		
		$d_log = D('Log');
		$p = $this->_get('p','intval',1);
		$log_list = $d_log->getLogList($p, $condition);
		$this->assign('log_list', $log_list['log_list']);
		$this->assign('page', $log_list['page']);
		$this->alert = parseAlert();
		$this->display();
	}
	
	//添加日志
	public function add(){
		if($this->isPost()){
			$info['creator_user_id'] = session('user_id');
			$info['log_category_id'] = intval($_POST['log_category_id']);
			$info['title'] = trim($_POST['title']);
			$info['content'] = $_POST['content'];
			$info['create_time'] = time();
			if(empty($info['log_category_id'])){
				alert('error','未选择日志类型',$_SERVER['HTTP_REFERER']);
			}
			if($info['title'] == ''){
				alert('error','请填写日志标题',$_SERVER['HTTP_REFERER']);
			}
			if($info['content'] == ''){
				alert('error','请填写日志内容',$_SERVER['HTTP_REFERER']);
			}
			$d_log = D('Log');
			if($d_log->addLog($info)){
				alert('success','添加日志成功',U('core/log/index'));
			}else{
				alert('error','添加日志失败',$_SERVER['HTTP_REFERER']);
			}
		}else{
			$this->alert = parseAlert();
			$this->display();
		}
	}

	//日志详情
	public function view() {
		$log_id = intval($_GET['id']);
		if($log_id){
			$d_log = D('Log');
			$log = $d_log->getLogById($log_id);
		}else{
			alert('error','参数错误！',$_SERVER['HTTP_REFERER']);
		}
		$this->log = $log;
		$this->alert = parseAlert();
		$this->display();
	}
	
	//日志编辑
	public function edit(){
		$log_id = intval($_REQUEST['id']);
		if(!empty($log_id)){
			if($this->isPost()){
				$data['log_id'] = $log_id;
				$data['title'] = trim($_POST['title']);
				$data['content'] = $_POST['content'];
				$data['update_time'] = time();
				
				if('' == $data['title']){
					alert('error','日志标题不能为空！',$_SERVER['HTTP_REFERER']);
				}
				if('' == $data['content']){
					alert('error','日志内容不能为空！',$_SERVER['HTTP_REFERER']);
				}
				
				$d_log = D('Log');
				if($d_log->editLog($data)){
					alert('success','编辑成功！', U('core/log/index'));
				}else{
					alert('error','信息无变化，编辑失败！', U('core/log/index'));
				}
			}else{
				$d_log = D('Log');
				$log = $d_log->getLogById($log_id);
			}
		}else{
			alert('error','参数错误！',$_SERVER['HTTP_REFERER']);
		}
		$this->log = $log;
		$this->alert = parseAlert();
		$this->display();
	}
	
}
