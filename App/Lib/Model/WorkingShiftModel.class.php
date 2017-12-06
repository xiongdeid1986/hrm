<?php
/**
 *
 * 班次 模型
 * author：悟空HR
 **/
class WorkingshiftModel extends Model{
	/**
	 * 获取分页班次
	 **/
	public function getShift($p, $where){
		if(!empty($where['user_name'])){
			$user_working_shift_id = M('user')->where(array('name'=>$where['user_name']))->getField('working_shift_id');
			$where['working_shift_id'] = $user_working_shift_id;
			unset($where['user_name']);
		}
		
		import('@.ORG.Page');
		$count = $this->where($where)->count();
		$Page = new Page($count,10);
		$show  = $Page->show();
		$shiftlist = $this->where($where)->order('create_time asc')->page($p.',10')->select();
		foreach($shiftlist as $key=>$val){
			//类型
			if($val['type'] == 0){
				$shiftlist[$key]['typename'] = '标准工作制';
			}elseif($val['type'] == 1){
				$shiftlist[$key]['typename'] = '周期工作制';
			}else{
				$shiftlist[$key]['typename'] = null;
			}
			//工作日
			$weekStr = '';
			$weekArr = array(
				'1'=>'周一',
				'2'=>'周二',
				'3'=>'周三',
				'4'=>'周四',
				'5'=>'周五',
				'6'=>'周六',
				'7'=>'周日',
			);
			$working_daysArr = array_filter(explode(',',$val['working_days']));
			foreach($weekArr as $k=>$v){
				if(in_array($k, $working_daysArr)){
					$weekStr .= $v.',';
				}
			}
			$shiftlist[$key]['working_days_name'] = $weekStr;
			//创建人
			$shiftlist[$key]['creator_user_name'] = M('user')->where('user_id = %d', $val['creator_user_id'])->getField('name');
		}
		return array('page'=>$show ,'shiftlist'=>$shiftlist);
	}
	
	/**
	 * 根据shift_id获取班次
	 **/
	public function getShiftById($shift_id){
		$shift = $this->where(array('working_shift_id'=>$shift_id))->find();
		//类型
		if($shift['type'] == 0){
			$shift['typename'] = '标准工作制';
		}elseif($shift['type'] == 1){
			$shift['typename'] = '周期工作制';
		}else{
			$shift['typename'] = null;
		}
		//工作日
		$weekStr = '';
		$weekArr = array(
			'1'=>'周一',
			'2'=>'周二',
			'3'=>'周三',
			'4'=>'周四',
			'5'=>'周五',
			'6'=>'周六',
			'7'=>'周日',
		);
		$working_daysArr = array_filter(explode(',',$shift['working_days']));
		foreach($weekArr as $k=>$v){
			if(in_array($k, $working_daysArr)){
				$weekStr .= $v.',';
			}
		}
		$shift['working_days_name'] = $weekStr;
		$shift['working_daysArr'] = $working_daysArr;
		//创建人
		$shiftlist[$key]['creator_user_name'] = M('user')->where('user_id = %d', $val['creator_user_id'])->getField('name');
		return $shift;
	}

	/**
	 * 添加班次
	 **/
	public function addShift($data){
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
	 * 编辑班次
	 **/
	public function editShift($data){
		//返回数据
		if($this->create($data) && $this->save()){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 删除班次
	 * 如果$shift_id为数组，则批量删除；否则删除一条记录
	 **/
	public function deleteShift($shift_id){
		if (is_array($shift_id)) {
			$shift_idArr = implode(',', $shift_id);
			if ($this->where(array('working_shift_id'=>array('in',$shift_idArr)))->delete()) {
				return true;
			}
		} else {
			$shift_id = intval($shift_id);
			if ($this->where(array('working_shift_id'=>$shift_id))->delete()) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 
	 * 根据working_shift_id获取用户
	 **/
	public function getShiftUseById($shift_id){
		$d_user = D('User');
		$working_shift_user = $d_user->getUserByWorkingShiftId($shift_id);
		return $working_shift_user;
	}
	
	/**
	 * 
	 * 写入班次更新日志
	 **/
	public function addWorkingShiftLog($info){
		$m_working_shift_log = M('workingShiftLog');
		if($m_working_shift_log->create($info)){
			return $m_working_shift_log->add($info);
		}else{
			return false;
		}
	}

}