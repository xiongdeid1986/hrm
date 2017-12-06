<?php
class ReportAction extends Action{
	public function _initialize(){
		$action = array(
			'users'=>array('archivesajax','archivestable','appraisal'),
			'anonymous'=>array()
		);
		B('Authenticate', $action);
	}
	public function archives(){
		$this->alert = parseAlert();
		$this->display();
	}
	public function archivesajax(){
		$field = $this->_get('field','trim','sex');
		switch($field){
			case 'sex':
				$count = D("Archives")->stats('sex');
				foreach($count as $key=>$val){
					switch($key){
						case '1':
							$k = '男';
							break;
						case '2':
							$k = '女';
							break;
					}
					$data[] = array($k,(int)$val);
				}
				$return = array('员工性别统计',array('name'=>'数目','data'=>$data));
				break;
			case 'birthday':
				$count = D("Archives")->stats('birthday');
				foreach($count as $key=>$val){
					switch($key){
						case '10':
							$k = '小于10岁';
							break;
						case '20':
							$k = '10-20岁';
							break;
						case '30':
							$k = '20-30岁';
							break;
						case '40':
							$k = '30-40岁';
							break;
						case '50':
							$k = '40岁以上';
							break;
					}
					$data[] = array($k,(int)$val);
				}
				$return = array('员工年龄统计',array('name'=>'数目','data'=>$data));
				break;
			case 'education':
				$count = D("Archives")->stats('education');
				foreach($count as $key=>$val){
					switch($key){
						case '1':
							$k = '小学';
							break;
						case '2':
							$k = '初中';
							break;
						case '3':
							$k = '高中';
							break;
						case '4':
							$k = '大专';
							break;
						case '5':
							$k = '本科';
							break;
						case '6':
							$k = '硕士';
							break;
						case '7':
							$k = '博士';
							break;
					}
					$data[] = array($k,(int)$val);
				}
				$return = array('员工学历统计',array('name'=>'数目','data'=>$data));
				break;
			case 'degree':
				$count = D("Archives")->stats('degree');
				foreach($count as $key=>$val){
					switch($key){
						case '1':
							$k = '学士';
							break;
						case '2':
							$k = '双学士';
							break;
						case '3':
							$k = '硕士';
							break;
						case '4':
							$k = '博士';
							break;
						case '5':
							$k = '博士后';
							break;
					}
					$data[] = array($k,(int)$val);
				}
				$return = array('员工学位统计',array('name'=>'数目','data'=>$data));
				break;
			case 'ygsf':
				$count = D("Archives")->stats('ygsf');
				foreach($count as $key=>$val){
					switch($key){
						case '1':
							$k = '农民';
							break;
						case '2':
							$k = '工人';
							break;
						case '3':
							$k = '干部';
							break;
					}
					$data[] = array($k,(int)$val);
				}
				$return = array('员工身份统计',array('name'=>'数目','data'=>$data));
				break;
			case 'marital_status':
				$count = D("Archives")->stats('marital_status');
				foreach($count as $key=>$val){
					switch($key){
						case '1':
							$k = '已婚';
							break;
						case '2':
							$k = '未婚';
							break;
					}
					$data[] = array($k,(int)$val);
				}
				$return = array('员工婚姻状况统计',array('name'=>'数目','data'=>$data));
				break;
			case 'partisan':
				$count = D("Archives")->stats('partisan');
				foreach($count as $key=>$val){
					switch($key){
						case '1':
							$k = '群众';
							break;
						case '2':
							$k = '共青团员';
							break;
						case '3':
							$k = '共产党员';
							break;
						case '4':
							$k = '其它党派';
							break;
					}
					$data[] = array($k,(int)$val);
				}
				$return = array('员工政治面貌统计',array('name'=>'数目','data'=>$data));
				break;
			case 'health':
				$count = D("Archives")->stats('health');
				foreach($count as $key=>$val){
					switch($key){
						case '1':
							$k = '较差';
							break;
						case '2':
							$k = '一般';
							break;
						case '3':
							$k = '良好';
							break;
					}
					$data[] = array($k,(int)$val);
				}
				$return = array('员工健康状况统计',array('name'=>'数目','data'=>$data));
				break;
			case 'work_date':
				$count = D("Archives")->stats('work_date');
				foreach($count as $key=>$val){
					switch($key){
						case '10':
							$k = '小于10年';
							break;
						case '20':
							$k = '10-20年';
							break;
						case '30':
							$k = '20-30年';
							break;
						case '40':
							$k = '30-40年';
							break;
						case '50':
							$k = '40年以上';
							break;
					}
					$data[] = array($k,(int)$val);
				}
				$return = array('员工年龄统计',array('name'=>'数目','data'=>$data));
				break;
		}
		$this->ajaxReturn($return);
	}
	public function archivestable(){
		$field = $this->_get('field','trim','sex');
		switch($field){
			case 'sex':
				$data = D("Archives")->statsTable('sex');
				$table_header = array(0=>'部门',1=>'男',2=>'女');
				break;
			case 'birthday':
				$data = D("Archives")->statsTable('birthday');
				$table_header = array(0=>'部门',10=>'小于10岁',20=>'10-20岁',30=>'20-30岁',40=>'30-40岁',50=>'40岁以上');
				break;
			case 'education':
				$data = D("Archives")->statsTable('education');
				$table_header = array(0=>'部门',1=>'小学',2=>'初中',3=>'高中',4=>'大专',5=>'本科',6=>'硕士',7=>'博士');
				break;
			case 'degree':
				$data = D("Archives")->statsTable('degree');
				$table_header = array(0=>'部门',1=>'学士',2=>'双学士',3=>'硕士',4=>'博士',5=>'博士后');
				break;
			case 'ygsf':
				$data = D("Archives")->statsTable('ygsf');
				$table_header = array(0=>'部门',1=>'农民',2=>'工人',3=>'干部');
				break;
			case 'marital_status':
				$data = D("Archives")->statsTable('marital_status');
				$table_header = array(0=>'部门',1=>'已婚',2=>'未婚');
				break;
			case 'partisan':
				$data = D("Archives")->statsTable('partisan');
				$table_header = array(0=>'部门',1=>'群众',2=>'共青团员',3=>'共产党员',4=>'其它党派');
				break;
			case 'health':
				$data = D("Archives")->statsTable('health');
				$table_header = array(0=>'部门',1=>'较差',2=>'一般',3=>'良好');
				break;
			case 'work_date':
				$data = D("Archives")->statsTable('work_date');
				$table_header = array(0=>'部门',10=>'小于10年',20=>'10-20年',30=>'20-30年',40=>'30-40年',50=>'40年以上');
				break;
		}
		$this->assign('data',$data);
		$this->assign('table_header',$table_header);
		$department = D('Structure')->getDepartmentList(0);
		$this->assign('department',$department);
		$this->display();
	}
	public function appraisal(){
		if($this->isPost()){
			$appraisal_manager_id = $this->_post('appraisal_manager_id','intval',0);
			$user_id = $this->_post('user_id','intval',0);
			if($appraisal_manager_id && !$user_id){
				$appraisal_manager = D('AppraisalManager')->getAppraisalManagerById($appraisal_manager_id);
				foreach($appraisal_manager['examinee_user'] as $key=>$val){
					$appraisal_manager['examinee_user'][$key]['sum_point'] = D('AppraisalPoint')->getSumPoint($val['user_id'], $appraisal_manager_id);
				}
				$this->assign('appraisal_manager',$appraisal_manager);
			}elseif(!$appraisal_manager_id && $user_id){
				$user_appraisal_list = D('AppraisalPoint')->getUserPointInfo($user_id);
				foreach($user_appraisal_list as $key=>$val){
					$user_appraisal_list[$key]['appraisal_manager'] = D('AppraisalManager')->getAppraisalManagerById($val['appraisal_manager_id']);
				}
				$this->userappraisallist = $user_appraisal_list;
			}elseif($appraisal_manager_id && $user_id){
				$appraisal_manager = D('AppraisalManager')->getAppraisalManagerById($appraisal_manager_id);
				$preSocreAvgPoint = D('AppraisalPoint')->getPreSocreAvgPoint($user_id, $appraisal_manager_id);
				$this->appraisalmanager = $appraisal_manager;
				$this->preSocreAvgPoint = $preSocreAvgPoint;
			}
		}
		$appraisal = D('AppraisalManager')->where(array('status'=>2))->select();
		$this->assign('appraisal',$appraisal);
		$this->alert = parseAlert();
		$this->display();
	}
}