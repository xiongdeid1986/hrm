<?php
/**
 *
 * 任务Action
 * author：悟空HR
**/
class TaskAction extends Action{

	public function _initialize(){
		$action = array(
			'users'=>array('index','add', 'view', 'edit', 'delete', 'addlog', 'editlog', 'deletelog', 'addtasklogdialog', 'viewtasklogdialog' , 'edittasklogdialog' , 'refundingapplicationdialog', 'sendbackdialog','ajaxmonth'),
			'anonymous'=>array()
		);
		B('Authenticate', $action);
	}

	public function index(){
		$name = empty($_GET['search_name']) ? '' : trim($_GET['search_name']);
		$level = empty($_GET['search_level']) ? '' : intval($_GET['search_level']);
		$status = empty($_GET['search_status']) ? '' : trim($_GET['search_status']);
		$start_time = empty($_GET['search_start_time']) ? '' : strtotime($_GET['search_start_time']);
		$end_time = empty($_GET['search_end_time']) ? '' : strtotime($_GET['search_end_time']);
		
		if(!empty($name)){
			$condition['name'] = array('like', '%'.$name.'%');
		}
		if(!empty($level)){
			$condition['level'] = array('eq', $level);
		}
		if(!empty($status)){
			$condition['status'] = array('eq', $status);
		}
		if(!empty($start_time)){
			if(!empty($end_time)){
				$condition['start_time'] = array('between', array($start_time, $end_time));
			}else{
				$condition['start_time'] = array('between', array($start_time-1, $start_time+86400));
			}
		}
		if(!empty($end_time)){
			if(!empty($start_time)){
				$condition['end_time'] = array('between', array($start_time, $end_time));
			}else{
				$condition['end_time'] = array('between', array($end_time-1, $end_time+86400));
			}
		}
		
		$p = $this->_get('p','intval',1);
		$tasklist = D('Task')->getTask($p, $condition);
		$this->tasklist = $tasklist['tasklist'];
		$this->assign('page', $tasklist['page']);
		$this->alert = parseAlert();
		$this->display();
	}
	 public function ajaxMonth(){
		$year = $this->_get('year','intval',date('Y'));
		$month = $this->_get('month','intval',date('m'));
		$time = mktime(0,0,0,$month+1,1,$year);
		$month_event = D('Task')->getMonthWork($year,$month+1,session('user_id'));
		$return = array();
		$start_time = mktime(0,0,0,$month,1,$year);
		for($i = 1;$i<= date('t',$start_time);$i++){
			$count = count($month_event[$i]);
			switch($count){
				case 0:
					$return[$i] = null;
					break;
				default:
					$return[$i] = array('url'=>'javascript:void(0)','title'=>date('Y年m月',$time).$i.'日','tips'=>$month_event[$i]);
					break;
			}
		}
		$this->Ajaxreturn($return);
    }
	
	public function day(){
		$year = $this->_get('year','intval',date('Y'));
		$momth = $this->_get('momth','intval',date('n'));
		$day = $this->_get('day','intval',date('j'));
		$tasklist = D('Task')->getDateTask(mktime(0,0,0,$momth,$day,$year),session('user_id'));
		$this->assign('tasklist', $tasklist);
		$this->assign('taday', date('Y年m月d日',mktime(0,0,0,$momth,$day,$year)));
		$this->alert = parseAlert();
		$this->display();
	}
	
	public function add(){
		if ($this->isPost()) {
			$data['creator_user_id'] = session('user_id');
			$data['name'] = trim($_POST['name']);
			$notice_type = $_POST['notice_type'];// 1:站内信  2:邮件
			$data['executor_id'] = intval($_POST['executor_id']);
			$data['coordinate_ids'] = trim($_POST['coordinate_ids']);
			$data['status'] = $_POST['status'];
			$data['level'] = $_POST['level'];
			$data['start_time'] = strtotime($_POST['start_time']);
			$data['end_time'] = strtotime($_POST['end_time']);
			$data['content'] = $_POST['content'];
			$data['create_time'] = time();
			
			if('' == $data['name']){
				alert('error','任务名称不能为空！',$_SERVER['HTTP_REFERER']);
			}
			if(empty($data['executor_id'])){
				alert('error','未选择任务执行人！',$_SERVER['HTTP_REFERER']);
			}
			if('' == $data['start_time']){
					alert('error','请设置开始时间！',$_SERVER['HTTP_REFERER']);
				}
			if('' == $data['end_time']){
				alert('error','请设置截止时间！',$_SERVER['HTTP_REFERER']);
			}
			if('' == $data['content'] ){
				alert('error','未填写任务内容！',$_SERVER['HTTP_REFERER']);
			}
			
			$d_task = D('Task');
			if($d_task->addTask($data)){
				//发送站内信或邮件通知
				if(!empty($notice_type)){
					$coordinate_idsArr = array_filter(explode(',',$_POST['coordinate_ids']));
					if(in_array($_POST['executor_id'],$coordinate_idsArr)){
						$to_user_idArr = $coordinate_idsArr;
					}else{
						$coordinate_idsArr[] = $_POST['executor_id'];
						$to_user_idArr =$coordinate_idsArr;
					}
					$info['content'] = $_POST['content'];
					//任务添加成功，发送站内信
					if(in_array('1',$notice_type)){
						$d_message = D('Message');
						$info['title'] = '您有一封新的站内信通知：'.session('name').' 将任务“ '.trim($_POST['name']).' ”分配给了您！';
						$info['user_id'] = session('user_id');
						$info['send_time'] = time();
						foreach($to_user_idArr as $v){
							$info['to_user_id'] = $v;
							$d_message->send($info);
						}
					}
					//任务添加成功，发送邮件
					if(in_array('2',$notice_type)){
						$info['title'] = '您有一封新的邮件通知：'.session('name').' 将任务“ '.trim($_POST['name']).' ”分配给了您！';
						foreach($to_user_idArr as $v){
							sendEmail($v, $info['title'], $info['content']);
						}
					}
				}
				alert('success','添加成功！', U('core/task/index'));
			}else{
				alert('error','添加失败！', U('core/task/index'));
			}
		}else{
			$this->userList = D('User')->getSubUser(session('position_id'));
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function view() {
		$task_id = intval($_GET['id']);
		$p = $this->_get('p','intval',1);
		if($task_id){
			$d_task = D('Task');
			$task = $d_task->getTaskById($task_id);
		}else{
			alert('error','参数错误！',$_SERVER['HTTP_REFERER']);
		}
		$task_log = $d_task->getTaskLogByTaskId($task_id, $p);
		$this->task = $task;
		$this->task_log = $task_log['tasklog'];
		$this->assign('page', $task_log['page']);
		$this->alert = parseAlert();
		$this->display();
	}
	
	public function edit(){
		$task_id = intval($_REQUEST['id']);
		if($task_id){
			if($this->isPost()){
				$data['task_id'] = $task_id;
				$data['name'] = trim($_POST['name']);
				$notice_type = $_POST['notice_type'];
				$data['executor_id'] = intval($_POST['executor_id']);
				$data['coordinate_ids'] = trim($_POST['coordinate_ids']);
				$data['status'] = $_POST['status'];
				$data['level'] = $_POST['level'];
				$data['start_time'] = strtotime($_POST['start_time']);
				$data['end_time'] = strtotime($_POST['end_time']);
				$data['content'] = $_POST['content'];
				$data['update_time'] = time();
				
				if('' == $data['name']){
					alert('error','任务名称不能为空！',$_SERVER['HTTP_REFERER']);
				}
				if(empty($data['executor_id'])){
					alert('error','未选择任务执行人！',$_SERVER['HTTP_REFERER']);
				}
				if('' == $data['start_time']){
					alert('error','请设置开始时间！',$_SERVER['HTTP_REFERER']);
				}
				if('' == $data['end_time']){
					alert('error','请设置截止时间！',$_SERVER['HTTP_REFERER']);
				}
				if('' == $data['content'] ){
					alert('error','未填写任务内容！',$_SERVER['HTTP_REFERER']);
				}
				if('退还' == $data['status']){
					alert('error','未填写退还申请！',$_SERVER['HTTP_REFERER']);
				}
				$d_task = D('Task');
				$task = $d_task->getTaskById($task_id);
				if('退还' == $task['status']){
					if('退还' != $data['status']){
						alert('error','未填写驳回理由！',$_SERVER['HTTP_REFERER']);
					}
				}
				
				$d_task = D('Task');
				if($d_task->editTask($data)){
					alert('success','编辑成功！', U('core/task/index'));
				}else{
					alert('error','信息无变化，编辑失败！', U('core/task/index'));
				}
			}else{
				$d_task = D('Task');
				$task = $d_task->getTaskById($task_id);
				if(!session('?admin') && session('user_id') != $task['creator_user_id'] && session('user_id') != $task['executor_id']){
					alert('error','您无权操作该内容！', $_SERVER['HTTP_REFERER']);
				}
				if('退还' == $task['status']){
					if(!session('?admin') && session('user_id') != $task['creator_user_id']){
						alert('error','该任务已退还，无法修改！', $_SERVER['HTTP_REFERER']);
					}
				}
			}
		}else{
			alert('error','参数错误！',$_SERVER['HTTP_REFERER']);
		}
		$this->task = $task;
		$this->alert = parseAlert();
		$this->display();
	}
	
	//删除任务
	public function delete(){
		$task_id = $_REQUEST['id'];
		if (!empty($task_id)){
			$d_task = D('Task');
			if ($d_task->deleteTask($task_id)) {
				alert('success', '删除成功！', U('core/task/index'));
			}else{
				alert('error', '删除失败！', U('core/task/index'));
			}
		} else {
			alert('error', '删除失败，未选择需要删除的记录！', U('core/task/index'));
		}
	}
	
	//添加任务日志
	public function addLog(){
		$task_id = intval($_POST['task_id']);
		$data['task_id'] = $task_id;
		$data['creator_user_id'] = intval(session('user_id'));
		$data['title'] = $_POST['title'];
		$data['content'] = $_POST['content'];
		$data['create_time'] = time();
		
		if('' == $data['title']){
			alert('error','标题不能为空！',$_SERVER['HTTP_REFERER']);
		}
		if('' == $data['content']){
			alert('error','内容不能为空！',$_SERVER['HTTP_REFERER']);
		}
		
		$d_task = D('Task');
		//如果是Ajax请求，则添加一条任务退还日志然后退还任务；否则直接添加任务日志。
		if($this->isAjax()){
			//如果 status_type = refunding_application 执行退还申请操作，如果 status_type = send_back 执行驳回退还申请操作
			if('refunding_application' == $_POST['status_type']){
				if($d_task->addTaskLog($data)){
					//改变任务的状态为"退还"
					$info['task_id'] = $task_id;
					$info['status'] = '退还';
					$info['update_time'] = time();
					if($d_task->editTask($info)){
						$task = $d_task->getTaskById($task_id);
						//退还成功发送站内信
						$d_message = D('Message');
						$message_data['title'] = '您有一封新的站内信通知：'.session('name').' 将任务“ '.$task['name'].' ”退还给了任务创建人 '.$task['creator_user_name'].' ，您将无法操作该任务！';
						$message_data['content'] = session('name').' 将任务<span style="color:red;">“ '.$task['name'].' ”</span>退还给了任务创建人 '.'<span style="color:red;"> '.$task['creator_user_name'].' </span>，您将无法操作该任务！如果您有任何疑问，请及时联系 <span style="color:red;">'.session('name').' </span>！';
						$message_data['user_id'] = 0;
						$message_data['send_time'] = time();
						$coordinate_idsArr = array_filter(explode(',',$task['coordinate_ids']));
						if(in_array($task['executor_id'],$coordinate_idsArr)){
							$to_user_idArr = $coordinate_idsArr;
						}else{
							$coordinate_idsArr[] = $task['executor_id'];
							$to_user_idArr =$coordinate_idsArr;
						}
						foreach($to_user_idArr as $v){
							$message_data['to_user_id'] = $v;
							$d_message->send($message_data);
						}

						//退还成功发送发送邮件
						$email_data['title'] = '您有一封新的站内信通知：'.session('name').' 将任务“ '.$task['name'].' ”退还给了任务创建人 '.$task['creator_user_name'].' ，您将无法操作该任务！';
						$email_data['content'] = session('name').' 将任务<span style="color:red;">“ '.$task['name'].' ”</span>退还给了任务创建人 '.'<span style="color:red;"> '.$task['creator_user_name'].' </span>，您将无法操作该任务！如果您有任何疑问，请及时联系 <span style="color:red;">'.session('name').' </span>！';
						foreach($to_user_idArr as $v){
							sendEmail($v, $email_data['title'], $email_data['content']);
						}
						
						$this->ajaxReturn(1, "退还申请已提交，等待审核！", 1);
					}else{
						$this->ajaxReturn(1, "任务状态更新失败！", 0);
					}
				}else{
					$this->ajaxReturn(1, "退回申请提交失败，请联系管理员！", 0);
				}
			}else{
				if($d_task->addTaskLog($data)){
					//改变任务的状态为“非退还”状态
					$info['task_id'] = $task_id;
					$info['status'] = $_POST['select_option_value'];
					$info['update_time'] = time();
					if($d_task->editTask($info)){
						$task = $d_task->getTaskById($task_id);
						//驳回成功发送站内信
						$d_message = D('Message');
						$message_data['title'] = '您有一封新的站内信通知：您申请退还的任务 “'.$task['name'].'” 已被 '.session('user_id').' 驳回！';
						$message_data['content'] = '驳回理由：<br />&nbsp;&nbsp;&nbsp;&nbsp;<pre>'.$_POST['content'].'</pre>';
						$message_data['user_id'] = session('user_id');
						$message_data['send_time'] = time();
						$d_message->send($message_data);
						
						//驳回成功发送发送邮件
						$email_data['title'] = '您有一封新的邮件通知：您申请退还的任务 “'.$task['name'].'” 已被 '.session('user_id').' 驳回！';
						$email_data['content'] = '驳回理由：<br />&nbsp;&nbsp;&nbsp;&nbsp;<pre>'.$_POST['content'].'</pre>';;
						$this->ajaxReturn(1, "退还申请驳回成功！", 1);
					}else{
						$this->ajaxReturn(1, "任务状态更新失败！", 0);
					}
				}else{
					$this->ajaxReturn(1, "退回申请驳回失败，请联系管理员！", 0);
				}
			}
		}else{
			if($d_task->addTaskLog($data)){
				alert('success', '添加任务日志成功！', $_SERVER['HTTP_REFERER']);
			}else{
				alert('error', '添加任务日志失败！', $_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	//编辑任务日志
	public function editLog(){
		$task_log_id = intval($_POST['task_log_id']);
		$data['task_log_id'] = $task_log_id;
		$data['title'] = $_POST['title'];
		$data['content'] = $_POST['content'];
		$data['update_time'] = time();
		
		if('' == $data['title']){
			alert('error','日志标题不能为空！',$_SERVER['HTTP_REFERER']);
		}
		if('' == $data['content']){
			alert('error','日志内容不能为空！',$_SERVER['HTTP_REFERER']);
		}
		
		$d_task = D('Task');
		if ($d_task->editTaskLog($data)) {
			alert('success', '编辑任务日志成功！', $_SERVER['HTTP_REFERER']);
		}else{
			alert('error', '编辑任务日志失败！', $_SERVER['HTTP_REFERER']);
		}
	}
	
	//删除任务日志
	public function deleteLog(){
		$task_id = $_GET['task_id'];
		$task_log_id = $_GET['id'];
		if (!empty($task_log_id)){
			//任务日志创建人、任务主要执行人、任务创建人、管理员可删除任务
			$d_task = D('Task');
			$task = $d_task->getTaskById($task_id);
			$task_log = $d_task->getTaskLogById($task_log_id);
			$user_id = session('user_id');
			if($user_id == $task_log['creator_user_id'] || $user_id == $task['executor_id'] || $user_id == $task['creator_user_id'] || session('?admin')){
				if ($d_task->deleteTaskLog($task_log_id)) {
					alert('success', '任务日志删除成功！', U('core/task/view','id='.$task_id));
				}else{
					alert('error', '任务日志删除失败！', U('core/task/view','id='.$task_id));
				}
			}else{
				alert('error', '您无权操作该内容！' ,$_SERVER['HTTP_REFERER']);
			}
		} else {
			alert('error', '任务日志删除失败，未选择需要删除的记录！', U('core/task/view','id='.$task_id));
		}
	}
	
	//添加任务日志Dialog
	public function addTaskLogDialog(){
		$task_id = $_GET['task_id'];
		$this->task_id = $task_id;
		$this->display();
	}
	
	//显示任务日志Dialog
	public function viewTaskLogDialog() {
		$task_log_id = intval($_GET['id']);
		if($task_log_id){
			$d_task = D('Task');
			$task_log = $d_task->getTaskLogById($task_log_id);
		}else{
			alert('error','参数错误！',$_SERVER['HTTP_REFERER']);
		}
		$this->task_log_view = $task_log;
		$this->alert = parseAlert();
		$this->display();
	}
	
	//编辑任务日志Dialog
	public function editTaskLogDialog() {
		$task_log_id = intval($_GET['id']);
		if($task_log_id){
			$d_task = D('Task');
			$task_log = $d_task->getTaskLogById($task_log_id);
		}else{
			alert('error','参数错误！',$_SERVER['HTTP_REFERER']);
		}
		$this->task_log_edit = $task_log;
		$this->alert = parseAlert();
		$this->display();
	}
	
	//退还申请Dialog
	public function refundingApplicationDialog(){
		$task_id = $_GET['task_id'];
		$this->task_id = $task_id;
		$this->display();
	}
	
	//退还驳回Dialog
	public function sendBackDialog(){
		$task_id = $_GET['task_id'];
		$this->task_id = $task_id;
		$this->display();
	}
	
}