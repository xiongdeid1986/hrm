<?php 
class TypeAction extends Action{
	public function _initialize(){
		$actions = array(
			'users'=>array(),
			'anonymous'=>array()
		);
		B('Authenticate', $actions);
	}
	
	public function index(){
		$p = $this->_get('p','intval',1);
		$m_type = M('type');
		$list = $m_type->select();
		
		$this->assign('list', $list);
		$this->assign('alert', parseAlert());
		$this->display();
	}
	
	public function add() {
		if ($this->isPost()) {
			$m_type = M('type');
			$data = $m_type->create();
			$data['create_time'] = time();
			if ($m_type->add($data)) {
				alert('success','添加成功',U('hrm/type/add'));
			} else {
				alert('error','添加失败，请联系管理员',U('hrm/type/add'));
			}
		} else {
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function edit() {
		if($this->isPost()){
			$m_type = M('type');
			$data = $m_type->create();			
			if ($m_type->save($data)) {
				alert('success', '更新成功', U('hrm/type/edit', 'id='.$data['type_id']));
			} else {
				alert('error', '更新失败，或数据没有变化', U('hrm/type/edit', 'id='.$data['type_id']));
			}
		} else {
			$id = $this->_get('id','intval',0);
			$info = M('type')->where('type_id', $id)->find();
			if(!$info){
				alert('error','没有找相应记录',U('hrm/type/index'));
			}
			$this->assign('info', $info);
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function delete() {
		$id = $this->_get('id','intval',0);
		
		$m_type = M('type');
		
		if ($m_type->delete($id)) {
			alert('success', '删除成功', U('hrm/type/index'));
		} else {
			alert('error', '删除失败', U('hrm/type/index'));
		}	
	}
	
}