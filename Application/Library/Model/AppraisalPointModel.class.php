<?php
/**
 *
 * 绩效考核评分 模型
 * author：悟空HR
 **/
class AppraisalPointModel extends Model{
	/**
	 * 获取考核表数据
	 **/
	public function getAppraisalPoint(){
		$appraisal_point = array();
		$d_appraisal_manager = D('AppraisalManager');
		$where['status'] = 1;
		$where['end_time'] = array('gt',time());
		$appraisal_manager = $d_appraisal_manager->getAllAppraisalManager($where);
		foreach($appraisal_manager as $key=>$val){
			$havePoint = $this->havePoint(session('user_id'), $val['appraisal_manager_id']);
			//如果状态为进行中(status = 1)，未过期，并且登陆用户在考核评分对象里面，则加入在线评分数组
			if(in_array(session('user_id'),$val['examiner_userArr']) && sizeOf($havePoint) < sizeOf($val['examinee_userArr'])){
				$val['not_appraisal_user_num'] = sizeOf($val['examinee_userArr']) - sizeOf($havePoint);
				$appraisal_point[] = $val;
			}
		}
		return $appraisal_point;
	}
	
	/**
	 * 评分
	 **/
	public function addPoint($data){
		if(!empty($data)){
			//返回数据
			if($this->create($data)){
				return $this->addAll($data);
			}else{
				return false;
			}
		}
	}
	
	/**
	 * 已打分用户
	 **/
	public function havePoint($examiner_user_id , $appraisal_manager_id){
		$havePoint = $this->where(array('examiner_user_id'=>$examiner_user_id, 'appraisal_manager_id'=>$appraisal_manager_id))->group('examinee_user_id')->getField('examinee_user_id', true);
		return $havePoint ? $havePoint : array();
	}
	
	/** 
	 * 根据appraisal_manager_id获取已启用的效绩考核记录
	 **/
	public function getAppraisalManagerById($appraisal_manager_id){
		$d_user = D('User');
		$d_appraisal_template = D('AppraisalTemplate');
		$appraisal_manager = $this->where('appraisal_manager_id= %d', $appraisal_manager_id)->find();
		//考核模板
		$template = $d_appraisal_template->getAppraisalTemplateById($appraisal_manager['appraisal_template_id']);
		$appraisal_manager['template'] = $template;
		//被考核对象
		$examinee_user_idsArr = array_filter(explode(',',$appraisal_manager['examinee_user_id']));
		foreach($examinee_user_idsArr as $k => $v){
			$user = $d_user->getUserInfo(array('user_id'=>$v));
			$appraisal_manager['examinee_user_name'] .= $user['name'];
			$appraisal_manager['examinee_user_name'] .= ',';
		}
		//考核评分对象
		$examiner_user_idsArr = array_filter(explode(',',$appraisal_manager['examiner_user_id']));
		foreach($examiner_user_idsArr as $k=>$v){
			$user = $d_user->getUserInfo(array('user_id'=>$v));
			$appraisal_manager['examiner_user_name'] .= $user['name'];
			$appraisal_manager['examiner_user_name'] .= ',';
		}
		//状态
		if(1 == $appraisal_manager['status']){
			$appraisal_manager['status_name'] = '进行中';
		}else{
			$appraisal_manager['status_name'] = '已汇总';
		}
		return $appraisal_manager;
	}
	
	
	/** 
	 * 根据appraisal_manager_id获取被考核的用户列表
	 * 
	 **/
	public function getAppraisalExamineeUser($appraisal_manager_id){
		$examinee_userArr = array();
		$d_user = D('User');
		$examinee_user = $this->where(array('appraisal_manager_id'=>$appraisal_manager_id))->getField('examinee_user_id');
		$examinee_user_idsArr = array_filter(explode(',', $examinee_user));
		foreach($examinee_user_idsArr as $k => $v){
			$user = $d_user->getUserInfo(array('user_id'=>$v));
			$examinee_userArr[] = $user;
		}
		return $examinee_userArr;
	}
	
	/** 
	 * 获取得分
	 * 
	 **/
	public function get_point($examinee_user_id, $appraisal_manager_id){
		$appraisal_point = $this->where(array('examinee_user_id'=>$examinee_user_id, 'appraisal_manager_id'=>$appraisal_manager_id))->group('score_id')->getField('score_id, AVG(point)');
		return $appraisal_point;
	}
	
	/** 
	 * 保存平均分
	 * 
	 **/
	public function addAvgPoint($data){
		if(!empty($data)){
			//返回数据
			$m_appraisal_avg_point = M('appraisalAvgPoint');
			if($m_appraisal_avg_point->create($data)){
				return $m_appraisal_avg_point->addAll($data);
			}else{
				return false;
			}
		}
	}
	/** 
	 * 获取总分
	 * 
	 **/
	public function getSumPoint($examinee_user_id, $appraisal_manager_id){
		$m_appraisal_avg_point = M('appraisalAvgPoint');
		$appraisal_sum_point = $m_appraisal_avg_point->where(array('examinee_user_id'=>$examinee_user_id, 'appraisal_manager_id'=>$appraisal_manager_id))->sum('avg_point');
		return $appraisal_sum_point;
	}
	
	/** 
	 * 获取个人每项平均分
	 * 
	 **/
	public function getPreSocreAvgPoint($examinee_user_id, $appraisal_manager_id){
		$m_appraisal_avg_point = M('appraisalAvgPoint');
		$appraisal_pre_score_point = $m_appraisal_avg_point->where(array('examinee_user_id'=>$examinee_user_id, 'appraisal_manager_id'=>$appraisal_manager_id))->group('score_id')->getField('score_id, avg_point');
		return $appraisal_pre_score_point;
	}
	public function getUserPointInfo($examinee_user_id){
		$m_appraisal_avg_point = M('appraisalAvgPoint');
		$user_appraisal_list = $m_appraisal_avg_point->where(array('examinee_user_id'=>$examinee_user_id))->group('appraisal_manager_id')->getField('examinee_user_id,appraisal_manager_id, sum(avg_point) as sum_point');
		return $user_appraisal_list;
	}
}