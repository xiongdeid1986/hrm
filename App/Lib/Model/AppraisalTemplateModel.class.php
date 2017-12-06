<?php
/**
 *
 * 绩效考核模板 模型
 * author：悟空HR
 **/
class AppraisalTemplateModel extends Model{
	/**
	 * 获取分页考核模板数据
	 **/
	public function getAppraisalTemplate($p){
		import('@.ORG.Page');
		$count = $this->count();
		$Page = new Page($count,15);
		$show  = $Page->show();
		$appraisal_template_list = $this->order('create_time asc')->page($p.',15')->select();
		$d_user = D('User');
		$d_appraisal_template_category = D('AppraisalTemplateCategory');
		foreach($appraisal_template_list as $key=>$val){
			//创建人
			$user = $d_user->getUserInfo(array('user_id'=>$val['creator_user_id']));
			$appraisal_template_list[$key]['creator_user_name'] = $user['name'];
			//考核类型
			$appraisal_template_category = $d_appraisal_template_category->where(array('category_id'=>$val['category_id']))->find();
			$appraisal_template_list[$key]['category'] = $appraisal_template_category;
		}
		return array('page'=>$show ,'templatelist'=>$appraisal_template_list);
	}
	
	/**
	 * 添加绩效考核模板记录
	 **/
	public function addAppraisalTemplate($data){
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
	 * 添加绩效考核模板详细评分规则记录
	 **/
	public function addScore($info){
		if(!empty($info)){
			//返回数据
			$m_score = M('appraisalTemplateScore');
			if($m_score->create($info)){
				return $m_score->add($info);
			}else{
				return false;
			}
		}
	}

	/**
	 * 编辑绩效考核模板详细评分规则记录
	 **/
	public function editScore($data){
		if(!empty($data)){
			//返回数据
			$m_score = M('appraisalTemplateScore');
			if($m_score->create($data) && $m_score->save($data)){
				return true;
			}else{
				return false;
			}
		}
	}
	
	/**
	 * 删除绩效考核详细评分规则内容
	 **/
	public function deleteScore($score_id){
		if(!empty($score_id)){
			//返回数据
			$m_score = M('appraisalTemplateScore');
			if($m_score->where('score_id = %d', $score_id)->delete()){
				return true;
			}else{
				return false;
			}
		}
	}
	
	/** 
	 * 根据socre_id获取效绩考核模板评分详细记录
	 **/
	public function getScoreById($socre_id){
		$m_score = M('appraisalTemplateScore');
		$score = $m_score->where('score_id = %d', $socre_id)->find();
		return $score;
	}
	
	/** 
	 * 根据appraisal_template_id获取效绩考核模板评分详细记录
	 **/
	public function getScoreByTemplateId($appraisal_template_id){
		$m_score = M('appraisalTemplateScore');
		$score = $m_score->where('appraisal_template_id = %d', $appraisal_template_id)->select();
		return $score;
	}
	
	/** 
	 * 根据appraisal_template_id获取效绩考核模板记录
	 **/
	public function getAppraisalTemplateById($appraisal_template_id){
		$appraisal_template = $this->where('appraisal_template_id = %d', $appraisal_template_id)->find();
		$d_user = D('User');
		$d_appraisal_template_category = D('AppraisalTemplateCategory');
		$m_appraisal_template_score = M('appraisalTemplateScore');
		//创建人
		$user = $d_user->getUserInfo(array('user_id'=>$appraisal_template['creator_user_id']));
		$appraisal_template['creator_user_name'] = $user['name'];
		//效绩考核类型
		$appraisal_template_category = $d_appraisal_template_category->where(array('category_id'=>$appraisal_template['category_id']))->find();
		$appraisal_template['category'] = $appraisal_template_category;
		//考核详细
		$socre = $m_appraisal_template_score->where('appraisal_template_id = %d', $appraisal_template_id)->select();
		foreach($socre as $key=>$val){
			$appraisal_template['score'][$key] = $this->getScoreById($val);
		}
		return $appraisal_template;
	}
	
	/** 
	 * 编辑绩效考核记录
	 **/
	public function editAppraisalTemplate($data){
		//返回数据
		if($this->create($data) && $this->save()){
			return true;
		}else{
			return false;
		}
	}
	
	/** 
	 * 删除绩效考核模板
	 * 如果$appraisal_template_id为数组，则批量删除；否则删除一条记录
	 **/
	public function deleteAppraisalTemplate($appraisal_template_id){
		$m_appraisal_template_score = M('appraisalTemplateScore');
		if (is_array($appraisal_template_id)) {
			$appraisal_template_idStr = implode(',', $appraisal_template_id);
			if ($this->where(array('appraisal_template_id'=>array('in',$appraisal_template_idStr)))->delete()) {
				$m_appraisal_template_score->where(array('appraisal_template_id'=>array('in',$appraisal_template_id)))->delete();
				return true;
			}
		} else {
			$appraisal_template_id = intval($appraisal_template_id);
			if ($this->where(array('appraisal_template_id'=>$appraisal_template_id))->delete()) {
				$m_appraisal_template_score->where(array('appraisal_template_id'=>$appraisal_template_id))->delete();
				return true;
			}
		}
		return false;
	}
	
	/** 
	 * 获取模板类别列表
	 * 
	 **/
	public function getAppraisalTemplateCategoryList(){
		$m_category = M('appraisalTemplateCategory');
		$category = $m_category->select();
		return $category;
	}

	/** 
	 * 添加模板类型
	 * 
	 **/
	public function addTemplateCategory($data){
		if(!empty($data)){
			$m_template_category = M('appraisalTemplateCategory');
			if($m_template_category->create($data)){
				return  $m_template_category->add($data);
			}
		}
		return false;
	}
	
	/** 
	 * 编辑模板类型
	 * 
	 **/
	public function editTemplateCategory($data){
		if(!empty($data)){
			$m_template_category = M('appraisalTemplateCategory');
			if($m_template_category->create($data) && $m_template_category->save($data)){
				return  true;
			}
		}
		return false;
	}
	
	/** 
	 * 根据template_category_id获取加班类型
	 * 
	 **/
	public function getTemplateCategoryById($template_category_id){
		$m_template_category = M('appraisalTemplateCategory');
		return  $m_template_category->where(array('category_id'=>$template_category_id))->find();
	}
	
	/** 
	 * 删除模板类型
	 * 
	 **/
	public function deleteTemplateCategory($template_category_id){
		if(!empty($template_category_id)){
			$m_template_category = M('appraisalTemplateCategory');
			if($m_template_category->where(array('category_id'=>$template_category_id))->delete()){
				return  true;
			}
		}
		return false;
	}
	
}