<?php
/**
 *
 * 班次 Action
 * author：悟空HR
**/
class WorkingshiftAction extends Action{

	public function _initialize(){
		$action = array(
			'users'=>array('index','add', 'edit', 'delete', 'getuserdialog', 'workingshiftuserdialog'),
			'anonymous'=>array()
		);
		B('Authenticate', $action);
	}

	public function index(){
		$working_shift_name = empty($_GET['working_shift_name']) ? '' : trim($_GET['working_shift_name']); 
		$search_user_name = empty($_GET['search_user_name']) ? '' : trim($_GET['search_user_name']); 
		if(!empty($working_shift_name)){
			$condition['name'] = array('like', '%'.$working_shift_name.'%');
		}
		if(!empty($search_user_name)){
			$condition['user_name'] = array('eq', $search_user_name);
		}
		$p = $this->_get('p','intval',1);
		$shiftlist = D('WorkingShift')->getShift($p, $condition);
		$this->shiftlist = $shiftlist['shiftlist'];
		$this->assign('page', $shiftlist['page']);
		$this->alert = parseAlert();
		$this->display();
	}
	
	public function add(){
		if ($this->isPost()) {
			$data['name'] = trim($_POST['name']);
			$data['description'] = $_POST['description'];
			$data['type'] = intval($_POST['type']);
			$data['start_time'] = strtotime($_POST['start_time']);
			$data['end_time'] = strtotime($_POST['end_time']);
			$data['working_days'] = implode(',', $_POST['working_days']);	//工作日
			$data['creator_user_id'] = session('user_id');	//创建人
			$data['create_time'] = time();	//创建时间
			
			if('' == $data['name']){
				alert('error','班次名称不能为空！',$_SERVER['HTTP_REFERER']);
			}
			if('' == $data['start_time']){
					alert('error','请设置开始时间！',$_SERVER['HTTP_REFERER']);
			}
			if('' == $data['end_time']){
				alert('error','请设置结束时间！',$_SERVER['HTTP_REFERER']);
			}
			if('' == $data['working_days']){
				alert('error','请设置工作日！',$_SERVER['HTTP_REFERER']);
			}
			
			$d_workingshift = D('WorkingShift');
			if($d_workingshift->addShift($data)){
				alert('success','添加成功！', U('hrm/workingshift/index'));
			}else{
				alert('error','添加失败！', U('hrm/workingshift/index'));
			}
		}else{
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function edit(){
		$shift_id = intval($_REQUEST['id']);
		$d_workingshift = D('WorkingShift');
		if($shift_id){
			if($this->isPost()){
				$data['working_shift_id'] = $shift_id;
				$data['name'] = trim($_POST['name']);
				$data['description'] = $_POST['description'];
				$data['type'] = intval($_POST['type']);
				$data['start_time'] = strtotime($_POST['start_time']);
				$data['end_time'] = strtotime($_POST['end_time']);
				$data['working_days'] = implode(',', $_POST['working_days']);	//工作日
				
				if('' == $data['name']){
					alert('error','班次名称不能为空！',$_SERVER['HTTP_REFERER']);
				}
				if('' == $data['start_time']){
						alert('error','请设置开始时间！',$_SERVER['HTTP_REFERER']);
				}
				if('' == $data['end_time']){
					alert('error','请设置结束时间！',$_SERVER['HTTP_REFERER']);
				}
				if('' == $data['working_days']){
					alert('error','请设置工作日！',$_SERVER['HTTP_REFERER']);
				}
				
				if($d_workingshift->editShift($data)){
					alert('success','编辑成功！', U('hrm/workingshift/index'));
				}else{
					alert('error','信息无变化，编辑失败！', U('hrm/workingshift/index'));
				}
			}else{
				$shift = $d_workingshift->getShiftById($shift_id); 
				$shift['working_daysArr'] = explode(',', $shift['working_days']);
				$this->shift = $shift;
			}
		}else{
			alert('error','参数错误！',$_SERVER['HTTP_REFERER']);
		}
		$this->alert = parseAlert();
		$this->display();
	}
	
	//删除任务
	public function delete(){
		$shift_id = $_REQUEST['id'];
		if (!empty($shift_id)){
			$d_workingshift = D('WorkingShift');
			if ($d_workingshift->deleteShift($shift_id)) {
				alert('success', '删除成功！', U('hrm/workingshift/index'));
			}else{
				alert('error', '删除失败！', U('hrm/workingshift/index'));
			}
		} else {
			alert('error', '删除失败，未选择需要删除的记录！', U('hrm/workingshift/index'));
		}
	}
	
	public function getUserDialog(){
		$working_shift_id = $_GET['working_shift_id'];
		$d_user = D('User');
		$user = $d_user->getUserList();
		$this->working_shift_id = $working_shift_id;
		$this->user = $user;
		$this->display();
	}
	
	//排班
	public function shiftWork(){
		$user_id = $_POST['user_id'];
		$working_shift_id = $_POST['working_shift_id'];
		if(!empty($user_id)){
			if(!empty($working_shift_id)){
				$d_user = D('User');
				$d_working_shift = D('WorkingShift');
				foreach($user_id as $key=>$val){
					$data['user_id'] = $val;
					$data['working_shift_id'] = $working_shift_id;
					$result = $d_user->editUserInfo($data);
					//写入班次日志
					$user = $d_user->getUserInfo(array('user_id'=>$val));
					$info['user_id'] = $val;
					$info['old_working_shift_id'] = $user['working_shift_id'];
					$info['new_working_shift_id'] = $working_shift_id;
					$info['update_time'] = time();
					$d_working_shift->addWorkingShiftLog($info);
				}
				if($result){
					alert('success', '已生成排班计划！', U('hrm/workingshift/index'));
				}else{
					alert('error', '排班失败！', U('hrm/workingshift/index'));
				}
			}else{
				alert('error', '参数错误！', U('hrm/workingshift/index'));
			}
		}else{
			alert('error', '未选择记录！', U('hrm/workingshift/index'));
		}
	}
	
	//获取排班人员
	public function workingShiftUserDialog(){
		$working_shift_id = $_GET['working_shift_id'];
		if(!empty($working_shift_id)){
			$d_workingshift = D('WorkingShift');
			$working_shift_user = $d_workingshift->getShiftUseById($working_shift_id);
		}else{
			alert('error', '参数错误！', U('hrm/workingshift/index'));
		}
		$this->working_shift_user = $working_shift_user;
		$this->working_shift_id = $working_shift_id;
		$this->display();
	}
	
	
}