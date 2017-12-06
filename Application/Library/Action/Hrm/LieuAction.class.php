<?php
/**
 *
 * 调休 Action
 * author：悟空HR
**/
class LieuAction extends Action{

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
			$condition['lieu_category_id'] = $search_category;
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
		$lieulist = D('Lieu')->getLieu($p, $condition);
		$this->lieulist = $lieulist['lieulist'];
		$this->assign('page', $lieulist['page']);
		$this->alert = parseAlert();
		$this->display();
	}
	
	public function add(){
		if ($this->isPost()) {
			$data['user_id'] = trim($_POST['user_id']);
			$data['maker_user_id'] = session('user_id');
			$data['lieu_category_id'] = $_POST['lieu_category_id'];
			$data['start_time'] = strtotime($_POST['start_time']);
			$data['end_time'] = strtotime($_POST['end_time']);
			$data['content'] = $_POST['content'];
			$data['create_time'] = time();
			$data['status'] = 0;
			
			if('' == $data['user_id']){
				alert('error','未选择调休的员工！',$_SERVER['HTTP_REFERER']);
			}
			if('' == $data['start_time']){
					alert('error','请设置开始时间！',$_SERVER['HTTP_REFERER']);
			}
			if('' == $data['end_time']){
				alert('error','请设置结束时间！',$_SERVER['HTTP_REFERER']);
			}
			if('' == $data['content']){
				alert('error','未填写调休原因！',$_SERVER['HTTP_REFERER']);
			}
			
			$d_lieu = D('Lieu');
			if($d_lieu->addLieu($data)){
				alert('success','添加成功！', U('hrm/lieu/index'));
			}else{
				alert('error','添加失败！', U('hrm/lieu/index'));
			}
		}else{
			$this->maker_user_name = session('name');
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function view(){
		$lieu_id = $_GET['id'];
		if(!empty($lieu_id)){
			$d_lieu = D('Lieu');
			$lieu = $d_lieu->getLieuById($lieu_id);
			$this->lieu = $lieu;
		}else{
			alert('error', '参数错误！', U('hrm/lieu/index'));
		}
		$this->alert = parseAlert();
		$this->display();
	}
	
	
	public function edit(){
		$lieu_id = $_REQUEST['id'];
		if(!empty($lieu_id)){
			$d_lieu = D('Lieu');
			if ($this->isPost()) {
				$data['lieu_id'] = $lieu_id;
				$data['user_id'] = trim($_POST['user_id']);
				$data['maker_user_id'] = session('user_id');
				$data['lieu_category_id'] = $_POST['lieu_category_id'];
				$data['start_time'] = strtotime($_POST['start_time']);
				$data['end_time'] = strtotime($_POST['end_time']);
				$data['content'] = $_POST['content'];
				
				if('' == $data['user_id']){
					alert('error','未选择调休的员工！',$_SERVER['HTTP_REFERER']);
				}
				if('' == $data['start_time']){
						alert('error','请设置开始时间！',$_SERVER['HTTP_REFERER']);
				}
				if('' == $data['end_time']){
					alert('error','请设置结束时间！',$_SERVER['HTTP_REFERER']);
				}
				if('' == $data['content']){
					alert('error','未填写调休原因！',$_SERVER['HTTP_REFERER']);
				}
				
				if($d_lieu->editLieu($data)){
					alert('success','编辑成功！', U('hrm/lieu/view', 'id='.$lieu_id));
				}else{
					alert('error','编辑失败！', U('hrm/lieu/view', 'id='.$lieu_id));
				}
			}else{
				$this->lieu = $d_lieu->getLieuById($lieu_id);
			}
		}else{
			alert('error', '参数错误！', U('hrm/lieu/index'));
		}
		$this->alert = parseAlert();
		$this->display();
	}
	
	//删除加班记录
	public function delete(){
		$lieu_id = $_REQUEST['id'];
		if (!empty($lieu_id)){
			$d_lieu = D('Lieu');
			if ($d_lieu->deleteLieu($lieu_id)) {
				alert('success', '删除成功！', U('hrm/lieu/index'));
			}else{
				alert('error', '删除失败！', U('hrm/lieu/index'));
			}
		} else {
			alert('error', '删除失败，未选择需要删除的记录！', U('hrm/lieu/index'));
		}
	}
	
	//审核
	public function auditing(){
		$lieu_id = $_REQUEST['id'];
		$data['lieu_id'] = $lieu_id ;
		$data['status'] = $_REQUEST['status'];
		if (empty($data['lieu_id'])){
			alert('error', '未选择需要审核的记录！', U('hrm/lieu/index'));
		}
		if ('' == $data['status']){
			alert('error', '未选择审核状态！', U('hrm/lieu/index'));
		}
		$d_lieu = D('Lieu');
		$lieu = $d_lieu->getLieuById($lieu_id);
		if($data['status'] == $lieu['status']){
			alert('error', '请勿重复审核', U('hrm/lieu/index'));
		}else{
			if ($d_lieu->auditingLieu($data)) {
				alert('success', '审核成功！', U('hrm/lieu/index'));
			}else{
				alert('error', '审核失败！', U('hrm/lieu/index'));
			}
		}
	}
	
}