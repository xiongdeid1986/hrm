<?php 
class CheckAction extends Action{
	public function _initialize(){
		$actions = array(
			'users'=>array(),
			'anonymous'=>array()
		);
		B('Authenticate', $actions);
	}
	
	public function index(){
		$p = $this->_get('p','intval',1);
		$m_check_template = M('checkTemplate');
		$list = $m_check_template->select();
		
		$this->assign('list', $list);
		$this->assign('alert', parseAlert());
		$this->display();
	}
	
	public function add() {
		if ($this->isPost()) {
			$m_check_template = M('checkTemplate');
			$data = $m_check_template->create();
			$data['create_time'] = time();
			if ($m_check_template->add($data)) {
				alert('success','添加成功',U('hrm/check/add'));
			} else {
				alert('error','添加失败，请联系管理员',U('hrm/check/add'));
			}
		} else {
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function edit() {
		if($this->isPost()){
			$m_check_template = M('checkTemplate');
			$data = $m_check_template->create();			
			if ($m_check_template->save($data)) {
				alert('success', '更新成功', U('hrm/check/edit', 'id='.$data['check_template_id']));
			} else {
				alert('error', '更新失败，或数据没有变化', U('hrm/check/edit', 'id='.$data['check_template_id']));
			}
		} else {
			$id = $this->_get('id','intval',0);
			$info = M('checkTemplate')->where('check_template_id', $id)->find();
			if(!$info){
				alert('error','没有找相应记录',U('hrm/check/index'));
			}
			$this->assign('info', $info);
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function delete() {
		$id = $this->_get('id','intval',0);
		
		$m_check_template = M('checkTemplate');
		
		if ($m_check_template->delete($id)) {
			alert('success', '删除成功', U('hrm/check/index'));
		} else {
			alert('error', '删除失败', U('hrm/check/index'));
		}	
	}
	
}