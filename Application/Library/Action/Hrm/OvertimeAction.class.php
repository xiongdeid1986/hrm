<?php
/**
 *
 * 加班 Action
 * author：悟空HR
**/
class OvertimeAction extends Action{

	public function _initialize(){
		$action = array(
			'users'=>array('index','add', 'edit', 'delete', 'view', 'auditing'),
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
		$overtimelist = D('Overtime')->getOvertime($p, $condition);
		$this->overtimelist = $overtimelist['overtimelist'];
		$this->assign('page', $overtimelist['page']);
		$this->alert = parseAlert();
		$this->display();
	}
	
	public function add(){
		if ($this->isPost()) {
			$data['user_id'] = trim($_POST['user_id']);
			$data['maker_user_id'] = session('user_id');
			$data['overtime_category_id'] = $_POST['overtime_category_id'];
			$data['type'] = $_POST['type'];
			$data['start_time'] = strtotime($_POST['start_time']);
			$data['end_time'] = strtotime($_POST['end_time']);
			$data['content'] = $_POST['content'];
			$data['create_time'] = time();
			$data['status'] = 0;
			
			if('' == $data['user_id']){
				alert('error','未选择加班的员工！',$_SERVER['HTTP_REFERER']);
			}
			if('' == $data['start_time']){
					alert('error','请设置开始时间！',$_SERVER['HTTP_REFERER']);
			}
			if('' == $data['end_time']){
				alert('error','请设置结束时间！',$_SERVER['HTTP_REFERER']);
			}
			if('' == $data['content']){
				alert('error','未填写加班原因！',$_SERVER['HTTP_REFERER']);
			}
			
			$d_overtime = D('Overtime');
			if($d_overtime->addOvertime($data)){
				alert('success','添加成功！', U('hrm/overtime/index'));
			}else{
				alert('error','添加失败！', U('hrm/overtime/index'));
			}
		}else{
			$this->category = D('Overtime')->getOvertimeCategory();
			$this->maker_user_name = session('name');
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function view(){
		$overtime_id = $_GET['id'];
		if(!empty($overtime_id)){
			$d_overtime = D('Overtime');
			$overtime = $d_overtime->getOvertimeById($overtime_id);
			$this->overtime = $overtime;
		}else{
			alert('error', '参数错误！', U('hrm/overtime/index'));
		}
		$this->alert = parseAlert();
		$this->display();
	}
	
	
	public function edit(){
		$overtime_id = $_REQUEST['id'];
		if(!empty($overtime_id)){
			$d_overtime = D('Overtime');
			if ($this->isPost()) {
				$data['overtime_id'] = $overtime_id;
				$data['user_id'] = trim($_POST['user_id']);
				$data['maker_user_id'] = session('user_id');
				$data['overtime_category_id'] = $_POST['overtime_category_id'];
				$data['type'] = $_POST['type'];
				$data['start_time'] = strtotime($_POST['start_time']);
				$data['end_time'] = strtotime($_POST['end_time']);
				$data['content'] = $_POST['content'];
				
				if('' == $data['user_id']){
					alert('error','未选择加班的员工！',$_SERVER['HTTP_REFERER']);
				}
				if('' == $data['start_time']){
						alert('error','请设置开始时间！',$_SERVER['HTTP_REFERER']);
				}
				if('' == $data['end_time']){
					alert('error','请设置结束时间！',$_SERVER['HTTP_REFERER']);
				}
				if('' == $data['content']){
					alert('error','未填写加班原因！',$_SERVER['HTTP_REFERER']);
				}
				
				if($d_overtime->editOvertime($data)){
					alert('success','编辑成功！', U('hrm/overtime/view', 'id='.$overtime_id));
				}else{
					alert('error','编辑失败！', U('hrm/overtime/view', 'id='.$overtime_id));
				}
			}else{
				$this->category = $d_overtime->getOvertimeCategory();
				$this->overtime = $d_overtime->getOvertimeById($overtime_id);
			}
		}else{
			alert('error', '参数错误！', U('hrm/overtime/index'));
		}
		$this->alert = parseAlert();
		$this->display();
	}
	
	//删除加班记录
	public function delete(){
		$overtime_id = $_REQUEST['id'];
		if (!empty($overtime_id)){
			$d_overtime = D('Overtime');
			if ($d_overtime->deleteOvertime($overtime_id)) {
				alert('success', '删除成功！', U('hrm/overtime/index'));
			}else{
				alert('error', '删除失败！', U('hrm/overtime/index'));
			}
		} else {
			alert('error', '删除失败，未选择需要删除的记录！', U('hrm/overtime/index'));
		}
	}
	
	//审核
	public function auditing(){
		$overtime_id = $_REQUEST['id'];
		$data['overtime_id'] = $overtime_id ;
		$data['status'] = $_REQUEST['status'];
		if (empty($data['overtime_id'])){
			alert('error', '未选择需要审核的记录！', U('hrm/overtime/index'));
		}
		if ('' == $data['status']){
			alert('error', '未选择审核状态！', U('hrm/overtime/index'));
		}
		$d_overtime = D('Overtime');
		$overtime = $d_overtime->getOvertimeById($overtime_id);
		if($data['status'] == $overtime['status']){
			alert('error', '请勿重复审核', U('hrm/overtime/index'));
		}else{
			if ($d_overtime->auditingOvertime($data)) {
				alert('success', '审核成功！', U('hrm/overtime/index'));
			}else{
				alert('error', '审核失败！', U('hrm/overtime/index'));
			}
		}
	}
	
	//加班类型列表
	public function category(){
		$d_overtime = D('Overtime');
		$overtime_category = $d_overtime->getOvertimeCategory();
		$this->overtimecategory = $overtime_category;
		$this->alert = parseAlert();
		$this->display();
	}
	
	//添加加班类型
	public function addCategory(){
		if($this->isPost()){
			$d_overtime = D('Overtime');
			$data['name'] = $_POST['name'];
			$data['payment'] = $_POST['payment'];
			$data['description'] = $_POST['description'];
			
			if(empty($data['name'])){
				alert('error', '未填写加班类型名称！', $_SERVER['HTTP_REFERER']);
			}
			
			if($d_overtime->addOvertimeCategory($data)){
				alert('success', '加班类型添加成功！', U('hrm/overtime/category'));
			}else{
				alert('error', '加班类型添加失败！', U('hrm/overtime/category'));
			}
		}else{
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function editCategory(){
		if($this->isPost()){
			$overtime_category_id = $_POST['id'];
			if(!empty($overtime_category_id)){
				$d_overtime = D('Overtime');
				$data['overtime_category_id'] = $overtime_category_id;
				$data['name'] = $_POST['name'];
				$data['payment'] = $_POST['payment'];
				$data['description'] = $_POST['description'];
				
				if(empty($data['name'])){
					alert('error', '未填写加班类型名称！', $_SERVER['HTTP_REFERER']);
				}
				
				if($d_overtime->editOvertimeCategory($data)){
					alert('success', '加班类型编辑成功！', U('hrm/overtime/category'));
				}else{
					alert('error', '加班类型编辑失败！', U('hrm/overtime/category'));
				}
			}else{
				alert('error', '参数错误！', $_SERVER['HTTP_REFERER']);
			}
		}else{
			$overtime_category_id = $_GET['id'];
			if(!empty($overtime_category_id)){
				$d_overtime = D('Overtime');
				$overtime_category = $d_overtime->getOvertimeCategoryById($overtime_category_id);
				$this->overtimecategory = $overtime_category;
			}else{
				alert('error', '参数错误！', $_SERVER['HTTP_REFERER']);
			}
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function deleteCategory(){
		$overtime_category_id = $_GET['id'];
		if(!empty($overtime_category_id)){
			$d_overtime = D('Overtime');
			if($d_overtime->deleteOvertimeCategory($overtime_category_id)){
				alert('success', '删除加班类型成功！', U('hrm/overtime/category'));
			}else{
				alert('error', '删除加班类型失败！', U('hrm/overtime/category'));
			}
		}else{
			alert('error', '参数错误！', U('hrm/overtime/category'));
		}
	}
}