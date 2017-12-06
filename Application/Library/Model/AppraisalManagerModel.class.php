<?php
/**
 *
 * 绩效考核管理 模型
 * author：悟空HR
 **/
class AppraisalManagerModel extends Model{
	/**
	 * 获取分页考核模板数据
	 **/
	public function getAppraisalManager($p, $where){
		import('@.ORG.Page');
		$count = $this->count();
		$Page = new Page($count,15);
		$show  = $Page->show();
		$d_user  = D('User');
		$m_appraisal_point = M('appraisalPoint');
		$d_appraisal_template = D('AppraisalTemplate');
		empty($where) ? $where = array() : $where = $where;
		$appraisal_manager_list = $this->where($where)->order('appraisal_manager_id desc')->page($p.',15')->select();
		foreach($appraisal_manager_list as $key=>$val){
			//考核模板
			$template = $d_appraisal_template->getAppraisalTemplateById($val['appraisal_template_id']);
			$appraisal_manager_list[$key]['template'] = $template;
			//被考核对象
			$examinee_user_idsArr = array_filter(explode(',',$val['examinee_user_id']));
			foreach($examinee_user_idsArr as $k => $v){
				$user = $d_user->getUserInfo(array('user_id'=>$v));
				$appraisal_manager_list[$key]['examinee_user_name'] .= $user['name'];
				$appraisal_manager_list[$key]['examinee_user_name'] .= ',';
			}
			//考核评分对象
			$examiner_user_idsArr = array_filter(explode(',',$val['examiner_user_id']));
			foreach($examiner_user_idsArr as $k=>$v){
				$user = $d_user->getUserInfo(array('user_id'=>$v));
				$appraisal_manager_list[$key]['examiner_user_name'] .= $user['name'];
				$appraisal_manager_list[$key]['examiner_user_name'] .= ',';
			}
			//负责人
			$user = $d_user->getUserInfo(array('user_id'=>$val['executor_id']));
			$appraisal_manager_list[$key]['executor_user_name'] = $user['name'];
			//状态
			if(1 == $val['status']){
				$appraisal_manager_list[$key]['status_name'] = '进行中';
			}else{
				$appraisal_manager_list[$key]['status_name'] = '已汇总';
			}
			//考核进度
			$template_socre_count = sizeOf($template['score']);
			$examinee_user_count = sizeOf($examinee_user_idsArr);
			$user_count = 0;
			foreach($examiner_user_idsArr as $m=>$n){
				$examiner_point_count = $m_appraisal_point->where(array('examiner_user_id'=>$n, 'appraisal_manager_id'=>$val['appraisal_manager_id']))->count();
				if($template_socre_count * $examinee_user_count == $examiner_point_count){
					$user_count++;
				}
			}
			
			$appraisal_manager_list[$key]['not_examin_examiner_num'] = $user_count;
			$appraisal_manager_list[$key]['total_examiner_num'] = sizeOf($examiner_user_idsArr);
		}
		return array('page'=>$show ,'managerlist'=>$appraisal_manager_list);
	}
	
	/**
	 * 获取所有考核表数据
	 **/
	public function getAllAppraisalManager($where){
		$d_user  = D('User');
		$d_appraisal_template = D('AppraisalTemplate');
		empty($where) ? $where = array() : $where = $where;
		$appraisal_manager_list = $this->where($where)->order('appraisal_manager_id desc')->select();
		foreach($appraisal_manager_list as $key=>$val){
			//考核模板
			$template = $d_appraisal_template->getAppraisalTemplateById($val['appraisal_template_id']);
			$appraisal_manager_list[$key]['template'] = $template;
			//被考核对象
			$examinee_user_idsArr = array_filter(explode(',',$val['examinee_user_id']));
			$appraisal_manager_list[$key]['examinee_userArr'] = $examinee_user_idsArr;
			foreach($examinee_user_idsArr as $k => $v){
				$user = $d_user->getUserInfo(array('user_id'=>$v));
				//$appraisal_manager_list[$key]['examinee_user'] = $user;
				$appraisal_manager_list[$key]['examinee_user_name'] .= $user['name'];
				$appraisal_manager_list[$key]['examinee_user_name'] .= ',';
			}
			//考核评分对象
			$examiner_user_idsArr = array_filter(explode(',',$val['examiner_user_id']));
			$appraisal_manager_list[$key]['examiner_userArr'] = $examiner_user_idsArr;
			foreach($examiner_user_idsArr as $k=>$v){
				$user = $d_user->getUserInfo(array('user_id'=>$v));
				//$appraisal_manager_list[$key]['examiner_user'] = $user;
				$appraisal_manager_list[$key]['examiner_user_name'] .= $user['name'];
				$appraisal_manager_list[$key]['examiner_user_name'] .= ',';
			}
			
			//负责人
			$user = $d_user->getUserInfo(array('user_id'=>$val['executor_id']));
			$appraisal_manager_list[$key]['executor_user_name'] = $user['name'];
			//状态
			if(1 == $val['status']){
				$appraisal_manager_list[$key]['status_name'] = '进行中';
			}else{
				$appraisal_manager_list[$key]['status_name'] = '已汇总';
			}
		}
		return $appraisal_manager_list;
	}
	
	/**
	 * 启用模板
	 **/
	public function enableTemplate($data){
		if(!empty($data)){
			//返回数据
			if($this->create($data)){
				return $this->add($data);
			}else{
				return false;
			}
		}
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
			$appraisal_manager['examinee_user'][] = $user;
			$appraisal_manager['examinee_user_name'] .= $user['name'];
			$appraisal_manager['examinee_user_name'] .= ',';
		}
		//考核评分对象
		$examiner_user_idsArr = array_filter(explode(',',$appraisal_manager['examiner_user_id']));
		foreach($examiner_user_idsArr as $k=>$v){
			$user = $d_user->getUserInfo(array('user_id'=>$v));
			$appraisal_manager['examiner_user'][] = $user;
			$appraisal_manager['examiner_user_name'] .= $user['name'];
			$appraisal_manager['examiner_user_name'] .= ',';
		}
	
		//负责人
		$user = $d_user->getUserInfo(array('user_id'=>$appraisal_manager['executor_id']));
		$appraisal_manager['executor_user_name'] = $user['name'];
		//状态
		if(1 == $appraisal_manager['status']){
			$appraisal_manager['status_name'] = '进行中';
		}else{
			$appraisal_manager['status_name'] = '已汇总';
		}
		return $appraisal_manager;
	}
	
	/** 
	 * 编辑已启用的绩效考核记录
	 * 
	 **/
	public function editAppraisalManager($data){
		if(!empty($data)){
			//返回数据
			if($this->create($data)){
				if(1 == $data['status']){
					M('appraisalAvgPoint')->where(array('appraisal_manager_id'=>$data['appraisal_manager_id']))->delete();
				}
				return $this->save($data);
			}else{
				return false;
			}
		}
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
	 * 删除绩效考核模板
	 * 如果$appraisal_manager_id为数组，则批量删除；否则删除一条记录
	 **/
	public function deleteAppraisalManager($appraisal_manager_id){
		if (is_array($appraisal_manager_id)) {
			$appraisal_manager_idStr = implode(',', $appraisal_manager_id);
			if ($this->where(array('appraisal_manager_id'=>array('in',$appraisal_manager_idStr)))->delete()) {
				return true;
			}
		} else {
			$appraisal_manager_id = intval($appraisal_manager_id);
			if ($this->where(array('appraisal_manager_id'=>$appraisal_manager_id))->delete()) {
				return true;
			}
		}
		return false;
	}
}