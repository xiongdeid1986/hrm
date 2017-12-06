<?php
/**
 *
 * 调休 模型
 * author：悟空HR
 **/
class LieuModel extends Model{
	/**
	 * 获取分页调休数据
	 **/
	public function getLieu($p, $where){
		if(!empty($where['user_name'])){
			$user_id = M('user')->where(array('name'=>array('like', '%'.$where['user_name'].'%')))->getField('user_id');
			$where['user_id'] = array('eq', $user_id);
			unset($where['user_name']);
		}
		import('@.ORG.Page');
		$count = $this->where($where)->count();
		$Page = new Page($count,15);
		$show  = $Page->show();
		$lieulist = $this->where($where)->order('create_time asc')->page($p.',15')->select();
		$d_user = D('User');
		$d_lieu_category = D('LieuCategory');
		foreach($lieulist as $key=>$val){
			//调休人
			$user = $d_user->getUserInfo(array('user_id'=>$val['user_id']));
			$lieulist[$key]['user_name'] = $user['name'];
			//填写人
			$user = $d_user->getUserInfo(array('user_id'=>$val['maker_user_id']));
			$lieulist[$key]['maker_user_name'] = $user['name'];
			//调休类型
			$lieu_category = $d_lieu_category->where(array('lieu_category_id'=>$val['lieu_category_id']))->find();
			$lieulist[$key]['category'] = $lieu_category;
			//审核
			if(0 == $val['status']){
				$lieulist[$key]['status_name'] = '处理中';
			}elseif(1 == $val['status']){
				$lieulist[$key]['status_name'] = '已通过';
			}else{
				$lieulist[$key]['status_name'] = '未通过';
			}
			//计算天数和小时
			$timediff = $val['end_time'] - $val['start_time'];
			//天数
			$time_days = intval($timediff/86400);
			$lieulist[$key]['lieu_days'] = $time_days;
			//小时
			$time_hours = intval($timediff/3600);
			$lieulist[$key]['lieu_hours'] = $time_hours;
		}
		return array('page'=>$show ,'lieulist'=>$lieulist);
	}
	
	/**
	 * 添加调休记录
	 **/
	public function addLieu($data){
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
	 * 根据lieu_id获取调休记录
	 **/
	public function getLieuById($lieu_id){
		$lieu = $this->where('lieu_id = %d', $lieu_id)->find();
		$d_user = D('User');
		$d_lieu_category = D('LieuCategory');
		//请假人
		$user = $d_user->getUserInfo(array('user_id'=>$lieu['user_id']));
		$lieu['user_name'] = $user['name'];
		//填写人
		$user = $d_user->getUserInfo(array('user_id'=>$lieu['maker_user_id']));
		$lieu['maker_user_name'] = $user['name'];
		//调休类型
		$lieu_category = $d_lieu_category->where(array('lieu_category_id'=>$lieu['lieu_category_id']))->find();
		$lieu['category'] = $lieu_category;
		//审核
		if(0 == $lieu['status']){
			$lieu['status_name'] = '处理中';
		}elseif(1 == $lieu['status']){
			$lieu['status_name'] = '已通过';
		}else{
			$lieu['status_name'] = '未通过';
		}
		//计算天数和小时
		$timediff = $lieu['end_time'] - $lieu['start_time'];
		//天数
		$lieu['lieu_days'] = intval($timediff/86400);
		//小时
		$lieu['lieu_hours'] = intval($timediff/3600);
		return $lieu;
	}
	
	/** 
	 * 编辑调休记录
	 **/
	public function editLieu($data){
		//返回数据
		if($this->create($data) && $this->save()){
			return true;
		}else{
			return false;
		}
	}
	
	/** 
	 * 删除调休记录
	 * 如果$lieu_id为数组，则批量删除；否则删除一条记录
	 **/
	public function deleteLieu($lieu_id){
		if (is_array($overtime_id)) {
			$lieu_idArr = implode(',', $lieu_id);
			if ($this->where(array('lieu_id'=>array('in',$lieu_idArr)))->delete()) {
				return true;
			}
		} else {
			$lieu_id = intval($lieu_id);
			if ($this->where(array('lieu_id'=>$lieu_id))->delete()) {
				return true;
			}
		}
		return false;
	}
	
	/** 
	 * 审核
	 * 如果$data为数组，如果$data['lieu_id']为数组，则批量审核，否则审核一条记录
	 **/
	public function auditingLieu($data){
		if (is_array($data['lieu_id'])) {
			$lieu_idStr = implode(',', $data['lieu_id']);
			if ($this->create($data)) {
				if($this->where(array('lieu_id'=>array('in',$lieu_idStr)))->save($data)){
					return true;
				}
			}
		} else {
			if ($this->where(array('lieu_id'=>$data['lieu_id']))->save($data)) {
				return true;
			}
		}
		return false;
	}
	
	/** 
	 * 根据$time获取某月所有调休信息
	 * 
	 **/
	public function getLieuInfoByTime($time){
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
		
		$lieu = $this->where($condition)->order('create_time desc')->select();
		return $lieu;
	}

}