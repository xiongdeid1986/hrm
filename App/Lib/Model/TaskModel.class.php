<?php
/**
 *
 * 任务模型
 * author：悟空HR
 **/
class TaskModel extends Model{
	/**
	 * 获取所有任务
	 **/
	public function getTask($p, $where){
		import('@.ORG.Page');
		$count = $this->where($where)->count();
		$Page = new Page($count,15);
		$show  = $Page->show();
		$tasklist = $this->where($where)->order('create_time desc')->page($p.',15')->select();
		foreach($tasklist as $key=>$val){
			$tasklist[$key]['executor_name'] = M('user')->where('user_id = %d',$val['executor_id'])->getField('name');
		}
		return array('page'=>$show ,'tasklist'=>$tasklist);
	}
	
	/**
	 * 根据ID获取任务
	 **/
	public function getTaskById($task_id){
		$task = $this->where(array('task_id'=>$task_id))->find();
		//任务创建人
		$m_creator_user = M('user')->where('user_id = %d',$task['creator_user_id'])->find();
		$task['creator_user_name'] = $m_creator_user['name'];
		//任务主要执行人
		$m_executor_user = M('user')->where('user_id = %d',$task['executor_id'])->find();
		$task['executor_name'] = $m_executor_user['name'];
		//任务协同执行人
		$coordinate_idsArr = array_filter(explode(',',$task['coordinate_ids']));
		foreach($coordinate_idsArr as $key=>$val){
			$m_user = M('user')->where(array('user_id'=>$val))->find();
			$task['coordinate_name'] .= $m_user['name'];
			$task['coordinate_name'] .= ',';
		}
		return $task;
	}
	/**
	 * 获取某月任务
	 **/
	public function getMonthWork($year,$month,$user_id){
		$where['executor_id'] = $user_id;
		$where['is_deteled'] = 0;
		$start_time = mktime(0,0,0,$month,1,$year);
		$end_time = mktime(0,0,0,$month+1,1,$year);
		$data1['start_time'] = array('lt', $start_time -1 );
		$data1['end_time'] = array('gt', $start_time);
		$data['start_time'] = array('between',array($start_time-1 ,$end_time));
		$data['_logic'] = 'or';
		$data['_complex'] = $data1;
		$where['_complex'] = $data;
		$task_list = $this->where($where)->select();
		$event_list = D('Event')->where(array('user_id'=>$user_id,'is_deteled'=>0,'_complex'=>$data))->select();
		
		$month_task = array();
		foreach($task_list as $key=>$val){
			for($i = 1;$i<= date('t',$start_time);$i++){
				if(($val['start_time'] <= mktime(0,0,0,$month,$i,$year) && $val['end_time'] > mktime(0,0,0,$month,$i,$year)) || ($val['start_time'] > mktime(0,0,0,$month,$i,$year) && $val['start_time'] < mktime(0,0,0,$month,$i+1,$year))){
					$val['url'] = U('core/task/view','id='.$val['task_id']);
					$month_work[$i]['task'][] = $val;
				}
			}
			for($i = 1;$i<= date('t',$start_time);$i++){
				if(($event_list[$key]['start_time'] <= mktime(0,0,0,$month,$i,$year) && $event_list[$key]['end_time'] > mktime(0,0,0,$month,$i,$year)) || ($event_list[$key]['start_time'] > mktime(0,0,0,$month,$i,$year) && $event_list[$key]['start_time'] < mktime(0,0,0,$month,$i+1,$year))){
					$event_list[$key]['url'] = U('core/event/view','id='.$event_list[$key]['event_id']);
					$event_list[$key]['name'] = $event_list[$key]['title'];
					$month_work[$i]['event'][] = $event_list[$key];
				}
			}
		}
		return $month_work;
	}
	/**
	 * 获取某天任务
	 **/
	public function getDateTask($start_time,$user_id){
		$where['executor_id'] = $user_id;
		$where['is_deteled'] = 0;
		$end_time = $start_time + 24*60*60;
		$data1['start_time'] = array('lt', $start_time -1 );
		$data1['end_time'] = array('gt', $start_time);
		$data['start_time'] = array('between',array($start_time-1 ,$end_time));
		$data['_logic'] = 'or';
		$data['_complex'] = $data1;
		$where['_complex'] = $data;
		$task_list = $this->where($where)->select();
		foreach($task_list as $key=>$val){
			$task_list[$key]['executor_name'] = M('user')->where('user_id = %d',$val['executor_id'])->getField('name');
		}
		return $task_list;
	}
	/**
	 * 添加任务
	 **/
	public function addTask($data){
		//从协同执行人中去除主要执行人
		$data['coordinate_ids'] = array_filter(explode(',',$data['coordinate_ids']));
		$coordinate_idsArr = $data['coordinate_ids'];
		$key = array_search($data['executor_id'],$coordinate_idsArr);
		if(false !== $key){
			unset($coordinate_idsArr[$key]);
		}
		$data['coordinate_ids'] = implode(',', $coordinate_idsArr);
		//返回数据
		if($this->add($data)){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 编辑任务
	 **/
	public function editTask($data){
		if(!empty($data['coordinate_ids'])){
			//从协同执行人中去除主要执行人
			$data['coordinate_ids'] = array_filter(explode(',',$data['coordinate_ids']));
			$coordinate_idsArr = $data['coordinate_ids'];
			$key = array_search($data['executor_id'],$coordinate_idsArr);
			if(false !== $key){
				unset($coordinate_idsArr[$key]);
			}
			$data['coordinate_ids'] = implode(',', $coordinate_idsArr);
		}
		//返回数据		
		if($this->create($data) and $this->save()){
			return true;
		}else{
			return false;
		}
	}
	
	/**
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
	
	/**
	 * 添加任务日志
	 **/
	public function addTaskLog($data){
		$m_task_log = M('taskLog');
		if($m_task_log->add($data)){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 编辑任务日志
	 **/
	public function editTaskLog($data){
		$m_task_log = M('taskLog');
		if($m_task_log->create($data) && $m_task_log->save($data)){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 删除任务日志
	 **/
	public function deleteTaskLog($task_log_id){
		$task_log_id = intval($task_log_id);
		$m_task_log = M('taskLog');
		if($m_task_log->where(array('task_log_id'=>$task_log_id))->delete()){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 根据$task_id获取任务的日志列表
	 * @param  $task_id(int)  所属任务task_id 
	 * @param  $limit(int)    显示显示条数 
	 **/
	public function getTaskLogByTaskId($task_id, $p){
		$m_task_log = M('taskLog');
		import('@.ORG.Page');
		$count = $m_task_log->where(array('task_id'=>$task_id))->count();
		$Page = new Page($count,10);
		$show  = $Page->show();
		$task_log = $m_task_log->where(array('task_id'=>$task_id))->order('create_time desc')->page($p.',10')->select();
		foreach($task_log as $key => $val){
			//任务创建人名字
			$m_user = M('user');
			$user = $m_user->where(array('user_id'=>$val['creator_user_id']))->find();
			$task_log[$key]['creator_user_name'] = $user['name'];
		}
		if(!empty($task_log)){
			return array('page'=>$show, 'tasklog'=>$task_log);
		}else{
			return null;
		}
	}
	
	/**
	 * 根据$task_log_id获取任务的日志
	 **/
	public function getTaskLogById($task_log_id){
		$m_task_log = M('taskLog');
		$task_log = $m_task_log->where(array('task_log_id'=>$task_log_id))->find();
		//任务创建人名字
		$m_user = M('user');
		$user = $m_user->where(array('user_id'=>$task_log['creator_user_id']))->find();
		$task_log['creator_user_name'] = $user['name'];
		if(!empty($task_log)){
			return $task_log;
		}else{
			return null;
		}
	}
}