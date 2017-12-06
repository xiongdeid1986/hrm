<?php 
class ArchivesAction extends Action{
	public function _initialize(){
		$actions = array(
			'users'=>array(),
			'anonymous'=>array()
		);
		B('Authenticate', $actions);
	}
	public function view(){
		$user_id = $this->_get('user_id','intval',0);
		$info = D('Archives')->getArchivesInfo($user_id);
		if(!$info){
			alert('error','没有找相应记录',U('hrm/archives/index'));
		}
		$this->assign('info', $info);
		$this->alert = parseAlert();
		$this->display();
	}
	public function add(){
		if($this->isPost()){
			if(D('Archives')->addArchives()){
				alert('success','添加员工档案成功',U('hrm/archives/index'));
			}else{
				alert('error','添加员工档案失败,' . D('Archives')->getError(),U(''));
			}
		}else{
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function index(){
		$p = $this->_get('p','intval',1);
		$archivespagelist = D('Archives')->getPageArchives($p,array());
		$this->assign('list', $archivespagelist['list']);
		$this->assign('page', $archivespagelist['page']);
		$this->assign('alert', parseAlert());
		$this->display();
	}
	public function edit(){
		if($this->isPost()){
			if(D('Archives')->editArchives()){
				alert('success','编辑员工档案成功',U('hrm/archives/index'));
			}else{
				alert('error','编辑员工档案失败,' . D('Archives')->getError(),U(''));
			}
		}else{
			$user_id = $this->_get('user_id','intval',0);
			$info = D('Archives')->getArchivesInfo($user_id);
			if(!$info){
				alert('error','没有找相应记录',U('hrm/archives/index'));
			}
			$this->assign('info', $info);
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function delete(){
		if($this->isPost()){
			$where['user_id'] = array('in',$this->_post('user_id'));
		}else{
			$where['user_id'] = $this->_get('id','intval',0);
		}
		if(D('Archives')->where($where)->delete()){
			alert('success','删除档案成功',U('hrm/archives/index'));
		}else{
			alert('success','删除档案失败，请联系管理员',U('hrm/archives/index'));
		}
	}
}
?>