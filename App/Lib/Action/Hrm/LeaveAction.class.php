<?php
/**
 *
 * 请假 Action
 * author：悟空HR
**/
class LeaveAction extends Action{

	public function _initialize(){
		$action = array(
			'users'=>array('index','add', 'edit', 'delete', 'view'),
			'anonymous'=>array()
		);
		B('Authenticate', $action);
	}

	public function index(){
		$search_user_name = empty($_GET['search_user_name']) ? '' : trim($_GET['search_user_name']);
		$search_category = empty($_GET['search_category']) ? '' : intval($_GET['search_category']);
		$search_status = '' == $_GET['search_status'] ? '' : intval($_GET['search_status']);
		$search_start_time = empty($_GET['search_start_time']) ? '' : strtotime($_GET['search_start_time']);
		$search_end_time = empty($_GET['search_end_time']) ? '' : strtotime($_GET['search_end_time']);

		if(!empty($search_user_name)){
			$condition['user_name'] = $search_user_name;
		}
		if(!empty($search_category)){
			$condition['leave_category_id'] = $search_category;
		}
		if('' !== $search_status){
			$condition['status'] = $search_status;
		}
		if(!empty($search_start_time)){
			if(!empty($search_end_time)){
				$condition['start_time'] = array('between', array($search_start_time, $search_end_time));
			}else{
				$condition['start_time'] = array('between', array($search_start_time-1, $search_start_time+86400));
			}
		}
		if(!empty($search_end_time)){
			if(!empty($search_start_time)){
				$condition['end_time'] = array('between', array($search_start_time, $search_end_time));
			}else{
				$condition['end_time'] = array('between', array($search_end_time-1, $search_end_time+86400));
			}
		}
		
		
		$p = $this->_get('p','intval',1);
		$leavelist = D('Leave')->getLeave($p, $condition);
		$this->leavelist = $leavelist['leavelist'];
		$this->assign('page', $leavelist['page']);
		$this->alert = parseAlert();
		$this->display();
	}
	
	public function add(){
		if ($this->isPost()) {
			$data['user_id'] = trim($_POST['user_id']);
			$data['maker_user_id'] = session('user_id');
			$data['leave_category_id'] = $_POST['leave_category_id'];
			$data['start_time'] = strtotime($_POST['start_time']);
			$data['end_time'] = strtotime($_POST['end_time']);
			$data['content'] = $_POST['content'];
			$data['create_time'] = time();
			$data['status'] = 0;
			
			if('' == $data['user_id']){
				alert('error','未选择请假的员工！',$_SERVER['HTTP_REFERER']);
			}
			if('' == $data['start_time']){
					alert('error','请设置开始时间！',$_SERVER['HTTP_REFERER']);
			}
			if('' == $data['end_time']){
				alert('error','请设置结束时间！',$_SERVER['HTTP_REFERER']);
			}
			if('' == $data['content']){
				alert('error','未填写请假原因！',$_SERVER['HTTP_REFERER']);
			}
			
			$d_leave = D('Leave');
			if($d_leave->addLeave($data)){
				alert('success','添加成功！', U('hrm/leave/index'));
			}else{
				alert('error','添加失败！', U('hrm/leave/index'));
			}
		}else{
			$this->maker_user_name = session('name');
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function view(){
		$leave_id = $_GET['id'];
		if(!empty($leave_id)){
			$d_leave = D('Leave');
			$leave = $d_leave->getLeaveById($leave_id);
			$this->leave = $leave;
		}else{
			alert('error', '参数错误！', U('hrm/leave/index'));
		}
		$this->alert = parseAlert();
		$this->display();
	}
	
	
	public function edit(){
		$leave_id = $_REQUEST['id'];
		if(!empty($leave_id)){
			if ($this->isPost()) {
				$data['leave_id'] = intval($leave_id);
				$data['user_id'] = trim($_POST['user_id']);
				$data['maker_user_id'] = session('user_id');
				$data['leave_category_id'] = $_POST['leave_category_id'];
				$data['start_time'] = strtotime($_POST['start_time']);
				$data['end_time'] = strtotime($_POST['end_time']);
				$data['content'] = $_POST['content'];
				
				if('' == $data['user_id']){
					alert('error','未选择请假的员工！',$_SERVER['HTTP_REFERER']);
				}
				if('' == $data['start_time']){
						alert('error','请设置开始时间！',$_SERVER['HTTP_REFERER']);
				}
				if('' == $data['end_time']){
					alert('error','请设置结束时间！',$_SERVER['HTTP_REFERER']);
				}
				if('' == $data['content']){
					alert('error','未填写请假原因！',$_SERVER['HTTP_REFERER']);
				}
				
				$d_leave = D('Leave');
				if($d_leave->editLeave($data)){
					alert('success','编辑成功！', U('hrm/leave/view', 'id='.$leave_id));
				}else{
					alert('error','编辑失败！', U('hrm/leave/view', 'id='.$leave_id));
				}
			}else{
				$d_leave = D('Leave');
				$this->leave = $d_leave->getLeaveById($leave_id);
			}
		}else{
			alert('error', '参数错误！', U('hrm/leave/index'));
		}
		$this->alert = parseAlert();
		$this->display();
	}
	
	//删除任务
	public function delete(){
		$leave_id = $_REQUEST['id'];
		if (!empty($leave_id)){
			$d_leave = D('Leave');
			if ($d_leave->deleteLeave($leave_id)) {
				alert('success', '删除成功！', U('hrm/leave/index'));
			}else{
				alert('error', '删除失败！', U('hrm/leave/index'));
			}
		} else {
			alert('error', '删除失败，未选择需要删除的记录！', U('hrm/leave/index'));
		}
	}
	
	//审核
	public function auditing(){
		$leave_id = $_REQUEST['id'];
		$ref = $_GET['ref'];
		$data['leave_id'] = $leave_id ;
		$data['status'] = $_REQUEST['status'];
		if (empty($data['leave_id'])){
			alert('error', '未选择需要审核的记录！', U('hrm/leave/index'));
		}
		if (empty($data['status'])){
			alert('error', '未选择审核状态！', U('hrm/leave/index'));
		}
		$d_leave = D('Leave');
		$leave = $d_leave->getLeaveById($leave_id);
		if($data['status'] == $leave['status']){
			alert('error', '请勿重复审核', U('hrm/leave/index'));
		}else{
			if ($d_leave->auditingLeave($data)) {
				alert('success', '审核成功！', U('hrm/leave/index'));
			}else{
				alert('error', '审核失败！', U('hrm/leave/index'));
			}
		}
	}
	
}