<?php
class StructureAction extends Action {
	/*
		users	登录后无权限可见
		anonymous 匿名无权限也可见
	*/
    public function _initialize(){
		$actions = array(
			'users'=>array('getdepartmentposition','getpositiondepartment'),
			'anonymous'=>array('login')
		);
		B('Authenticate', $actions);
	}
	
	public function department(){
		$tree_code = D('Structure')->getSubDepartmentTreeCode(0, 1);
		$this->assign('tree_code', $tree_code);
		$this->alert = parseAlert();
		$this->display('department');
	}
	
	public function addDepartment(){
		if($this->isPost()){
			$info['parent_id'] = $this->_post('parent_id','intval',0);
			$info['description'] = $this->_post('description','trim','');
			$info['name'] = $this->_post('name','trim','');
			$d_user = D('Structure');
			if($return_name = $d_user->checkDepartmentName($info['name']) < 0){
				switch($return_name){
					case -1:
						alert('error','请填写部门名称',U('hrm/structure/adddepartment'));
						break;
					case -2:
						alert('error','部门名称已存在，请重新输入',U('hrm/structure/adddepartment'));
						break;
				}
			}
			if($d_user->addDepartment($info)){
				alert('success','添加部门成功！',U('hrm/structure/department'));
			}else{
				alert('error','添加部门失败！',U('hrm/structure/adddepartment'));
			}
		}else{
			$list = D('Structure')->getDepartmentList(0,'--',1);
			$this->assign('list',$list);
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function editDepartment(){
		if($this->isPost()){
			$info['parent_id'] = $this->_post('parent_id','intval',0);
			$info['description'] = $this->_post('description','trim','');
			$info['name'] = $this->_post('name','trim','');
			$info['department_id'] = $this->_post('department_id','intval',0);
			if($info['department_id'] == 0) alert('error','参数错误',U('hrm/structure/department'));
			$d_user = D('Structure');
			if($return_name = $d_user->checkDepartmentName($info['name'],$info['department_id']) < 0){
				switch($return_name){
					case -1:
						alert('error','请填写部门名称',U('hrm/structure/department'));
						break;
					case -2:
						alert('error','部门名称已存在，请重新输入',U('hrm/structure/department'));
						break;
				}
			}
			if($d_user->editDepartment($info)){
				alert('success','修改部门信息成功！',U('hrm/structure/department'));
			}else{
				alert('error','部门信息变化，修改失败！',U('hrm/structure/department'));
			}
		}else{
			$department_id = $this->_get('id','intval');
			if($department_id == 0){
				alert('error','参数错误',U('hrm/structure/department'));
			}
			$department_list = D('Structure')->getDepartmentList(0,'--',0,$department_id);
			$info = D('Structure')->getDepartmentInfo($department_id);
			$this->assign('info',$info);
			$this->assign('department_list',$department_list);
			$this->display();
		}
	}
	public function deleteDepartment(){
		$department_id = $this->_get('id','intval',0);
		if($department_id == 0){
			alert('error','参数错误',U('hrm/structure/department'));
		}
		$info = D('Structure')->getDepartmentInfo($department_id);
		if($info['position']) alert('error','该部门有下属部门，不允许删除',U('hrm/structure/department'));
		if($info['sub']) alert('error','该部门下有直属岗位，不允许删除',U('hrm/structure/department'));
		if(M('department')->where(array('department_id'=>$department_id))->delete()){
			alert('success','部门删除成功！',U('hrm/structure/department'));
		}else{
			alert('error','部门删除失败！',U('hrm/structure/department'));
		}
	}
	public function position(){
		$tree_code = D('Structure')->getSubPositionTreeCode(0, 1);
		$this->assign('tree_code', $tree_code);
		$this->alert=parseAlert();
		$this->display();
	}
	public function addPosition(){
		if($this->isPost()){
			$info['name'] = $this->_post('name','trim','');
			$info['department_id'] = $this->_post('department_id','intval',0);
			$info['parent_id'] = $this->_post('parent_id','intval',0);
			$info['plan_num'] = $this->_post('plan_num','intval',1);
			$info['real_num'] = $this->_post('real_num','intval',0);
			$info['description'] = $this->_post('description','trim','');
			$d_user = D('Structure');
			if($return_name = $d_user->checkPositionName($info['name']) <0){
				switch($return_name){
					case -1:
						alert('error','请填写岗位名称',U('hrm/structure/addposition'));
						break;
					case -2:
						alert('error','岗位名称已存在，请重新输入',U('hrm/structure/addposition'));
						break;
				}
			}
			if(!$d_user->getDepartmentInfo($info['department_id'])){
				alert('error','所选部门不存在',U('hrm/structure/addposition'));
			}
			if($info['parent_id'] !=0 && !$d_user->getPositionInfo($info['parent_id'])){
				alert('error','所选上级岗位不存在',U('hrm/structure/addposition'));
			}
			
			if($d_user->addPosition($info)){
				alert('success','添加部门成功！',U('hrm/structure/position'));
			}else{
				alert('error','添加部门失败！',U('hrm/structure/addposition'));
			}
		}else{
			$department_list = D('Structure')->getDepartmentList(0,'--',1);
			$this->assign('department_list',$department_list);
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function editPosition(){
		if($this->isPost()){
			$info['name'] = $this->_post('name','trim','');
			$info['department_id'] = $this->_post('department_id','intval',0);
			$info['parent_id'] = $this->_post('parent_id','intval',0);
			$info['plan_num'] = $this->_post('plan_num','intval',1);
			$info['real_num'] = $this->_post('real_num','intval',0);
			$info['description'] = $this->_post('description','trim','');
			$info['position_id'] = $this->_post('position_id','intval',0);
			if($info['position_id'] == 0){
				alert('error','参数错误',U('hrm/structure/addposition'));
			}
			$d_user = D('Structure');
			if($return_name = $d_user->checkPositionName($info['name'],$info['position_id']) <0){
				switch($return_name){
					case -1:
						alert('error','请填写岗位名称',U('hrm/structure/addposition'));
						break;
					case -2:
						alert('error','岗位名称已存在，请重新输入',U('hrm/structure/addposition'));
						break;
				}
			}
			if(!$d_user->getDepartmentInfo($info['department_id'])){
				alert('error','所选部门不存在',U('hrm/structure/addposition'));
			}
			if($info['parent_id'] !=0 && !$d_user->getPositionInfo($info['parent_id'])){
				alert('error','所选上级岗位不存在',U('hrm/structure/addposition'));
			}
			
			if($d_user->editPosition($info)){
				alert('success','修改岗位信息成功！',U('hrm/structure/position'));
			}else{
				alert('error','岗位信息无变化，修改失败！',U('hrm/structure/addposition'));
			}
		}else{
			$position_id = $this->_get('id','intval',0);
			$position = D('Structure')->getPositionInfo($position_id);
			if(!$position){
				alert('error','参数错误',U('hrm/structure/addposition'));
			}
			$department_list = D('Structure')->getDepartmentList(0,'--');
			$position_list = D('Structure')->getPositionDepartment($position['department_id'],$position['position_id']);
			$this->assign('department_list', $department_list);
			$this->assign('position_list', $position_list);
			$this->assign('position', $position);
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function deletePosition(){
		$position_id = $this->_get('id','intval',0);
		if($position_id == 0){
			alert('error','参数错误',U('hrm/structure/department'));
		}
		$info = D('Structure')->getPositionInfo($position_id);
		if($info['user']) alert('error','该岗位下有直属员工，不允许删除',U('hrm/structure/department'));
		if($info['sub']) alert('error','该岗位有下属岗位，不允许删除',U('hrm/structure/department'));
		if(M('position')->where(array('position_id'=>$position_id))->delete()){
			alert('success','部门删除成功！',U('hrm/structure/department'));
		}else{
			alert('error','部门删除失败！',U('hrm/structure/department'));
		}
	}
	/*
	*Ajax请求
	*获得部门下级直属岗位
	*/
	public function getDepartmentPosition(){
		$department_id = $this->_get('id','intval','');
		$position_list = D('Structure')->getDepartmentPosition($department_id);
		$this->ajaxReturn($position_list);
	}
	/*
	*Ajax请求
	*获得部门下级直属岗位及所有上级部门的直属岗位
	*/
	public function getPositionDepartment(){
		$department_id = $this->_get('id','intval','');
		$position_list = D('Structure')->getPositionDepartment($department_id);
		$this->ajaxReturn($position_list);
	}
}