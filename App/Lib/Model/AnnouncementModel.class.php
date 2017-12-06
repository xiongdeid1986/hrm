<?php
/**
 * 公告 模型
 * author：悟空HR
 **/
class AnnouncementModel extends Model{
	/**
	 * 获取分页请假数据
	 **/
	public function getAnnouncement($p, $where){
		import('@.ORG.Page');
		$count = $this->where($where)->count();
		$Page = new Page($count,15);
		$show  = $Page->show();
		$announcementlist = $this->where($where)->order('create_time desc')->page($p.',15')->select();
		$d_user = D('User');
		foreach($announcementlist as $key=>$val){
			//创建人
			$user = $d_user->getUserInfo(array('user_id'=>$val['creator_user_id']));
			$announcementlist[$key]['creator_user_name'] = $user['name'];
			//公告状态
			if(1 == $val['status']){
				$announcementlist[$key]['status_name'] = '发布中';
			}else{
				$announcementlist[$key]['status_name'] = '停用';
			}
		}
		return array('page'=>$show ,'announcementlist'=>$announcementlist);
	}
	
	/**
	 * 发布公告
	 **/
	public function addAnnouncement($data){
		if(!empty($data)){
			//返回数据
			if($this->create($data)){
				$result = $this->add($data);
				if($result && 1 == $data['set_top']){
					$this->change_set_top($result);
				}
				return $result;
			}else{
				return false;
			}
		}
	}
	
	/** 
	 * 根据announcement_id获取公告
	 **/
	public function getAnnouncementById($announcement_id){
		$announcement = $this->where('announcement_id = %d', $announcement_id)->find();
		$d_user = D('User');
		//发布人
		$user = $d_user->getUserInfo(array('user_id'=>$announcement['creator_user_id']));
		$announcement['creator_user_name'] = $user['name'];
		//部门
		$announcement['department_idArr'] = array_filter(explode(',', $announcement['department_id']));
		$department_name = '';
		$m_department = M('department');
		foreach($announcement['department_idArr'] as $key=>$val){
			$department_name .= $m_department->where(array('department_id'=>$val['department_id']))->getField('name');
			$department_name .= ', ';
		}
		$announcement['department_name'] = $department_name;
		//状态
		if(1 == $announcement['status']){
			$announcement['status_name'] = '发布中';
		}else{
			$announcement['status_name'] = '停用';
		}
		//置顶
		if(0 == $announcement['set_top']){
			$announcement['set_top_name'] = '否';
		}else{
			$announcement['set_top_name'] = '是';
		}
		return $announcement;
	}
	
	/** 
	 * 编辑请假条
	 **/
	public function editAnnouncement($data){
		//返回数据
		if($this->create($data) && $this->save()){
			if(1 == $data['set_top']){
				$this->change_set_top($data['announcement_id']);
			}
			return true;
		}else{
			return false;
		}
	}
	
	/** 
	 * 删除公告
	 * 如果$announcement_id为数组，则批量删除；否则删除一条记录
	 **/
	public function deleteAnnouncement($announcement_id){
		if (is_array($announcement_id)) {
			$announcement_idArr = implode(',', $announcement_id);
			if ($this->where(array('announcement_id'=>array('in', $announcement_idArr)))->delete()) {
				return true;
			}
		} else {
			$announcement_id = intval($announcement_id);
			if ($this->where(array('announcement_id'=>$announcement_id))->delete()) {
				return true;
			}
		}
		return false;
	}
	
	/** 
	 * 更新置顶
	 * 更新除 announcement_id 之外所有记录的set_top为0
	 **/
	public function change_set_top($announcement_id){
		$set_top_announcement = $this->where(array('set_top'=>1))->select();
		foreach($set_top_announcement as $key=>$val){
			if($val['announcement_id'] !=  $announcement_id){
				$this->where(array('announcement_id'=>$val['announcement_id']))->save(array('set_top' => 0));
			}
		}
	}
	
	/*
	*获得置顶公告
	*
	*/
	public function getTopAnnouncement(){
		$info = $this->where(array('department_id'=>array('like','%'.session('department_id').'%'),'status'=>1,'set_top'=>1))->find();
		if($info){
			$user_info = D('User')->getUserInfo(array('user_id'=>$info['creator_user_id']));
			$info['creator_user_name'] = $user_info['name'];
		}
		return $info;
	}
	
}