<?php
/**
 *
 * 日志模型
 * author：悟空HR
 **/
class LogModel extends Model{
	/**
	 * 获取所有日志分类
	 **/
	public function getLogCategoryList(){
		$m_log_category = M('logCategory');
		$log_category = $m_log_category->order('sort_id asc')->select();
		return $log_category;
	}
	
	/** 
	 * 添加日志
	 **/
	public function addLog($info){
		if($this->create($info)){
			$result = $this->add($info);
			return $result;
		}else{
			return false;
		}
		return $log;
	}

	/**
	 * 获取日志分页列表
	 **/
	public function getLogList($p, $where){
		import('@.ORG.Page');
		$count = $this->where($where)->count();
		$Page = new Page($count,15);
		$show  = $Page->show();
		$logList = $this->where($where)->page($p.',15')->select();
		$m_log_category = M('logCategory');
		$d_user= D('User');
		foreach($logList as $key=>$val){
			$log_category = $m_log_category->where(array('log_category_id'=>$val['log_category_id']))->find();
			$logList[$key]['log_category_name'] = $log_category['name'];
			$user = $d_user->getUserInfo(array('user_id'=>$val['creator_user_id']));
			$logList[$key]['creator_user_name'] = $user['name'];
		}
		return array('page'=>$show ,'log_list'=>$logList);
	}
	
	/**
	 * 根据$log_id获取的日志
	 **/
	public function getLogById($log_id){
		$log = $this->where(array('log_id'=>$log_id))->find();
		$m_log_category = M('logCategory');
		$d_user= D('User');
		$log_category = $m_log_category->where('log_category_id = %d', $log['log_category_id'])->find();
		$log['log_category_name'] = $log_category['name'];
		$user = $d_user->getUserInfo(array('user_id'=>$log['creator_user_id']));
		$log['creator_user_name'] = $user['name'];
		if(!empty($log)){
			return $log;
		}else{
			return null;
		}
	}
	
	/**
	 * 编辑日志
	 **/
	public function editLog($data){
		//返回数据		
		if($this->create($data) and $this->save()){
			return true;
		}else{
			return false;
		}
	}
	
	/**无用
	 * 删除任务
	 * 如果$task_id为数组，则批量删除；否则删除一条记录
	 **/
	public function deleteTask($task_id){
		if (is_array($task_id)) {
			$task_idArr = implode(',',$task_id);
			if ($this->where(array('task_id'=>array('in',$task_idArr)))->delete()) {
				return true;
			}
		} else {
			$task_id = intval($task_id);
			if ($this->where(array('task_id'=>$task_id))->delete()) {
				return true;
			}
		}
		return false;
	}
}