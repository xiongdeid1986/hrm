<?php
/**
 *
 * 绩效考核评分 Action
 * author：悟空HR
**/
class AppraisalpointAction extends Action{
	public function _initialize(){
		$action = array(
			'users'=>array(),
			'anonymous'=>array()
		);
		B('Authenticate', $action);
	}

	public function index(){
		$p = $this->_get('p','intval',1);
		$d_appraisal_point = D('AppraisalPoint');
		$appraisal_point = $d_appraisal_point->getAppraisalPoint();
		$this->appraisalpoint = $appraisal_point;
		$this->alert = parseAlert();
		$this->display();
	}
	
	public function edit(){
		$appraisal_manager_id = $_REQUEST['id'];
		if(!empty($appraisal_manager_id)){
			$d_appraisal_manager = D('AppraisalManager');
			$d_appraisal_point = D('AppraisalPoint');
			if($this->isPost()){
				$appraisal_manager = $d_appraisal_manager->getAppraisalManagerById($appraisal_manager_id);
				foreach($appraisal_manager['template']['score'] as $val){
					$temp['appraisal_manager_id'] = $appraisal_manager_id;
					$temp['point'] = $_POST['point'][$val['score_id']];
					$temp['comment'] = $_POST['comment'][$val['score_id']];
					$temp['examinee_user_id'] = $_POST['examinee_user_id'];
					$temp['examiner_user_id'] = session('user_id');
					$temp['score_id'] = $val['score_id'];
					$data[] = $temp;
					
					if(!is_numeric($temp['point']) || $temp['point'] > $val['high_scope']){
						alert('error', "【 ".$val['name']." 】".'份数格式不正确！', $_SERVER['HTTP_REFERER']);
					}
				}
				if($d_appraisal_point->addPoint($data)){
					$have_point_user = $d_appraisal_point->havePoint(session('user_id'), $appraisal_manager_id);
					if(sizeOf($have_point_user) == sizeOf($appraisal_manager['examinee_user'])){
						alert('success', '本次评分已结束！', U('hrm/appraisalpoint/index'));
					}else{
						alert('success', '评分成功！', $_SERVER['HTTP_REFERER']);
					}
				}else{
					alert('error', '评分失败！', $_SERVER['HTTP_REFERER']);
				}
			}else{
				$appraisal_manager = $d_appraisal_manager->getAppraisalManagerById($appraisal_manager_id);
				$have_point_user = $d_appraisal_point->havePoint(session('user_id'), $appraisal_manager_id);
				if(sizeOf($have_point_user) == sizeOf($appraisal_manager['examinee_user'])){
					alert('error', '您已为该考核表打过分！', U('hrm/appraisalpoint/index'));
				}
				$this->have_point_user = $have_point_user;
				$this->appraisalmanager = $appraisal_manager;
			}
		}else{
			alert('error', '参数错误！', U('hrm/appraisalpoint/index'));
		}
		$this->alert = parseAlert();
		$this->display();
	}
	
	//成绩
	public function results(){
		$appraisal_manager_id = intval($_GET['id']);
		if(!empty($appraisal_manager_id)){
			$d_appraisal_manager = D('AppraisalManager');
			$d_appraisal_point = D('AppraisalPoint');
			$appraisal_manager = $d_appraisal_manager->getAppraisalManagerById($appraisal_manager_id);
			foreach($appraisal_manager['examinee_user'] as $key=>$val){
				$appraisal_manager['examinee_user'][$key]['sum_point'] = $d_appraisal_point->getSumPoint($val['user_id'], $appraisal_manager_id);
			}
			$this->appraisal = $appraisal_manager;
		}else{
			alert('error','参数错误！', $_SERVER['HTTP_REFERER']);
		}
		$this->alert = parseAlert();
		$this->display();
	}
	
	//详细成绩
	public function detailResults(){
		$examinee_user_id = $_GET['uid'];
		$appraisal_manager_id = $_GET['id'];
		$d_appraisal_manager = D('AppraisalManager');
		$d_appraisal_point = D('AppraisalPoint');
		$appraisal_manager = $d_appraisal_manager->getAppraisalManagerById($appraisal_manager_id);
		$preSocreAvgPoint = $d_appraisal_point->getPreSocreAvgPoint($examinee_user_id, $appraisal_manager_id);
		$this->appraisalmanager = $appraisal_manager;
		$this->preSocreAvgPoint = $preSocreAvgPoint;
		$this->display();
	}
}