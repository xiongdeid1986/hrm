<?php
/**
 *
 * 加班 模型
 * author：悟空HR
 **/
class OvertimeModel extends Model{
	/**
	 * 获取分页加班数据
	 **/
	public function getOvertime($p, $where){
		if(!empty($where['user_name'])){
			$user_id = M('user')->where(array('name'=>array('like', '%'.$where['user_name'].'%')))->getField('user_id');
			$where['user_id'] = array('eq', $user_id);
			unset($where['user_name']);
		}
		import('@.ORG.Page');
		$count = $this->count();
		$Page = new Page($count,15);
		$show  = $Page->show();
		$overtimelist = $this->order('create_time asc')->page($p.',15')->select();
		$d_user = D('User');
		$d_overtime_category = D('OvertimeCategory');
		foreach($overtimelist as $key=>$val){
			//加班人
			$user = $d_user->getUserInfo(array('user_id'=>$val['user_id']));
			$overtimelist[$key]['user_name'] = $user['name'];
			//填写人
			$user = $d_user->getUserInfo(array('user_id'=>$val['maker_user_id']));
			$overtimelist[$key]['maker_user_name'] = $user['name'];
			//加班类型
			$overtime_category = $d_overtime_category->where(array('overtime_category_id'=>$val['overtime_category_id']))->find();
			$overtimelist[$key]['category'] = $overtime_category;
			//审核
			if(0 == $val['status']){
				$overtimelist[$key]['status_name'] = '处理中';
			}elseif(1 == $val['status']){
				$overtimelist[$key]['status_name'] = '已通过';
			}else{
				$overtimelist[$key]['status_name'] = '未通过';
			}
			//计算天数和小时
			$timediff = $val['end_time'] - $val['start_time'];
			//天数
			$time_days = intval($timediff/86400);
			$overtimelist[$key]['overtime_days'] = $time_days;
			//小时
			$time_hours = intval($timediff/3600);
			$overtimelist[$key]['overtime_hours'] = $time_hours;
			//结算方式
			if(0 == $val['type']){
				$overtimelist[$key]['type_name'] = '计费加班';
			}else{
				$overtimelist[$key]['type_name'] = '调休';
			}
			//加班费用
			$overtimelist[$key]['overtime_payment'] = floatval($overtime_category['payment'] * $time_hours);
			
			
		}
		return array('page'=>$show ,'overtimelist'=>$overtimelist);
	}
	
	/**
	 * 添加加班记录
	 **/
	public function addOvertime($data){
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
	 * 根据overtime_id获取请假记录
	 **/
	public function getOvertimeById($overtime_id){
		$overtime = $this->where('overtime_id = %d', $overtime_id)->find();
		$d_user = D('User');
		$d_overtime_category = D('OvertimeCategory');
		//请假人
		$user = $d_user->getUserInfo(array('user_id'=>$overtime['user_id']));
		$overtime['user_name'] = $user['name'];
		//填写人
		$user = $d_user->getUserInfo(array('user_id'=>$overtime['maker_user_id']));
		$overtime['maker_user_name'] = $user['name'];
		//加班类型
		$overtime_category = $d_overtime_category->where(array('overtime_category_id'=>$overtime['overtime_category_id']))->find();
		$overtime['category'] = $overtime_category;
		//审核
		if(0 == $overtime['status']){
			$overtime['status_name'] = '处理中';
		}elseif(1 == $overtime['status']){
			$overtime['status_name'] = '已通过';
		}else{
			$overtime['status_name'] = '未通过';
		}
		$timediff = $overtime['end_time'] - $overtime['start_time'];
		//天数
		$overtime['overtime_days'] = intval($timediff/86400);
		//小时
		$overtime['overtime_hours'] = intval($timediff/3600);
		//结算方式
		if(0 == $overtime['type']){
			$overtime['type_name'] = '计费加班';
		}else{
			$overtime['type_name'] = '调休';
		}
		return $overtime;
	}
	
	/** 
	 * 编辑加班记录
	 **/
	public function editOvertime($data){
		//返回数据
		if($this->create($data) && $this->save()){
			return true;
		}else{
			return false;
		}
	}
	
	/** 
	 * 删除加班记录
	 * 如果$overtime_id为数组，则批量删除；否则删除一条记录
	 **/
	public function deleteOvertime($overtime_id){
		if (is_array($overtime_id)) {
			$overtime_idArr = implode(',', $overtime_id);
			if ($this->where(array('overtime_id'=>array('in',$overtime_idArr)))->delete()) {
				return true;
			}
		} else {
			$overtime_id = intval($overtime_id);
			if ($this->where(array('overtime_id'=>$overtime_id))->delete()) {
				return true;
			}
		}
		return false;
	}
	
	/** 
	 * 审核
	 * 如果$data为数组，如果$data['user_id']为数组，则批量审核，否则审核一条记录
	 **/
	public function auditingOvertime($data){
		if (is_array($data['overtime_id'])) {
			$overtime_idStr = implode(',', $data['overtime_id']);
			if ($this->create($data)) {
				if($this->where(array('overtime_id'=>array('in',$overtime_idStr)))->save($data)){
					return true;
				}
			}
		} else {
			if ($this->where(array('overtime_id'=>$data['overtime_id']))->save($data)) {
				return true;
			}
		}
		return false;
	}
	
	/** 
	 * 加班类型列表
	 * 
	 **/
	public function getOvertimeCategory(){
		$m_overtime_category = M('overtimeCategory');
		return  $m_overtime_category->select();
	}
	
	/** 
	 * 根据overtime_category_id获取加班类型
	 * 
	 **/
	public function getOvertimeCategoryById($overtime_category_id){
		$m_overtime_category = M('overtimeCategory');
		return  $m_overtime_category->where(array('overtime_category_id'=>$overtime_category_id))->find();
	}
	
	/** 
	 * 添加加班类型
	 * 
	 **/
	public function addOvertimeCategory($data){
		if(!empty($data)){
			$m_overtime_category = M('overtimeCategory');
			if($m_overtime_category->create($data)){
				return  $m_overtime_category->add($data);
			}
		}
		return false;
	}
	
	/** 
	 * 编辑加班类型
	 * 
	 **/
	public function editOvertimeCategory($data){
		if(!empty($data)){
			$m_overtime_category = M('overtimeCategory');
			if($m_overtime_category->create($data) && $m_overtime_category->save($data)){
				return  true;
			}
		}
		return false;
	}
	
	/** 
	 * 删除加班类型
	 * 
	 **/
	public function deleteOvertimeCategory($overtime_category_id){
		if(!empty($overtime_category_id)){
			$m_overtime_category = M('overtimeCategory');
			if($m_overtime_category->where(array('overtime_category_id'=>$overtime_category_id))->delete()){
				return  true;
			}
		}
		return false;
	}

	/** 
	 * 根据$time获取某月所有加班信息
	 * 
	 **/
	public function getOvertimeInfoByTime($time){
		$condition['status'] = 1;
		$data1['start_time'] = array('lt', $time -1 );
		$data1['end_time'] = array('gt', $time -1 );
		$next_year = date('Y',$time)+1;
		$next_month = date('m',$time)+1;
		$month_time = date('m',$time) ==12 ? strtotime($next_year.'-01-01') : strtotime(date('Y',$time).'-'.$next_month.'-01');
		$data['start_time'] = array('between',array($time -1 ,$month_time));
		$data['_logic'] = 'or';
		$data['_complex'] = $data1;
		$condition['_complex'] = $data;
		
		$overtime = $this->where($condition)->order('create_time desc')->select();
		return $overtime;
	}
}