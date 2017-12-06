<?php 
    class UserModel extends Model{
		public function getSalt(){
			return substr(md5(time()),0,4);			
		}
		
		public function getUserInfo($userinfo){
			if($userinfo['user_id'] != 0){
				$where['user_id'] = $userinfo['user_id'];
			}elseif($userinfo['name'] != ''){
				$where['name'] = $userinfo['name'];
			}else{
				return false; 
			}
			$d_userview = D('UserView');
			$user = $d_userview->where($where)->find();
			if($user['type'] == 0){
				$user['type_name'] = '试用员工';
			}elseif($user['type'] == 1){
				$user['type_name'] = '正式员工';
			}else{
				$user['type_name'] = '临时员工';
			}
			return $user;
		}
		
		public function addUser($info){
			if(!M('user')->create($info)){
				return false;
			}
			return M('user')->add();
		}
		public function editUserInfo($info){
			if($this->create($info)){
				return M('user')->save($info);
			}
			return false;
		}
		public function getUserList(){
			$user = D('UserView');
			$userlist = $user->where(array('status'=>1))->select();
			$m_workingshift = M('workingShift');
			foreach($userlist as $key=>$val){
				$userlist[$key]['working_shift_name'] = $m_workingshift->where(array('working_shift_id'=>$val['working_shift_id']))->getField('name');
			}
			return $userlist;
		}
		public function getUserPageList($p,$status = 1){
			$user = D('UserView');
			$where['status'] = $status;
			import('@.ORG.Page');
			$count = $user->where($where)->count();
			$Page = new Page($count,15);
			$Page->parameter = 'status='.$status;
			$show  = $Page->show();
			$userlist = $user->where($where)->page($p.',15')->select();
			return array('page'=>$show ,'userlist'=>$userlist);
		}
		
		/*	邮箱检测并验重
			-1 邮箱为空
			-2 邮箱格式不正确
			-3 用户重复
		*/
		public function checkEmail($email='',$user_id=0){
			if(!$email) return -1;
			if (!ereg('^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$',$email)){
				return -2;
			}
			$user = M('user');
			$where['email'] = $email;
			if($user_id){
				$where['user_id'] = array('neq',$user_id);
			}
			if($user->where($where)->select()){
				return -3;
			}else{
				return 1;
			}
		}
		/*
			用户名检测
			-1 用户名为空
			-2 用户重复
		*/
		public function checkUsername($name,$user_id=0){
			if(!$name) return -1;
			$user = M('user');
			$where['name'] = $name;
			if($user_id){
				$where['user_id'] = array('neq',$user_id);
			}
			if($user->where($where)->find()){
				return -2;
			}else{
				return 1;
			}
		}
		
		public function getModuleName(){
			$names = array();
			$rows = M('control_module')->select();
			foreach($rows as $k => $v){
				$names[$v['m']] = $v['name']; 
			}
			return $names;
		}
		/*
		 *	检测当前用户有没有$url的权限
		 *  $url = array('g'=>'','m'=>'','a'=>'')，a必须
		 */
		public function checkControl($url=array()){
			if(empty($url['a']) || empty($url['m'])) return false;
			if (session('?admin')) return true;
			$control_id = M('control')->where($url)->getField('control_id');
			if (!empty($control_id) && in_array($control_id , explode(',',session('control_ids')))) {
				return true;
			}else{
				return false;
			}
			
		}
		/**
		 * 
		 * 获得下级岗位列表
		 **/
		public function getSubPosition($position_id) {
			$m_position = M('position');
			$sub_position = $m_position->where(array('parent_id'=>$position_id))->select();
			if(empty($sub_position)){
				return array();
			}else{
				foreach($sub_position as $key=>$val){
					$sub_position = array_merge($sub_position, $this->getSubPosition($val['position_id']));
				}
			}
			return $sub_position;
		}
		/**
		 * 
		 * 获得下级员工列表
		 **/
		public function getSubUser($position_id){
			$positionArr = $this->getSubPosition($position_id);
			foreach($positionArr as $k=>$v){
				$positionidArr[] = $v['position_id'];
			}
			$user = $this->where(array('position_id'=>array('in',$positionidArr)),array('status'=>1),array('work_status'=>0))->select();
			$m_position = M('position'); 
			foreach($user as $k=>$v){
				if($v['type'] == 0){
					$user[$k]['type_name'] = '试用员工';
				}elseif($v['type'] == 1){
					$user[$k]['type_name'] = '正式员工';
				}else{
					$user[$k]['type_name'] = '临时员工';
				}
				$user[$k]['position_name'] = $m_position->where(array('position_id'=>$v['position_id']))->getField('name');
			}
			return $user;
		}
		
		/**
		 * 
		 * 获得岗位员工列表
		 **/
		public function getPositionUser($position_id){
			$users = D('UserView')->where(array('position_id'=>$position_id),array('status'=>1),array('work_status'=>0))->select();
			return $users;
		}
		/**
		* 
		* 根据working_shift_id获取用户列表
		**/
		public function getUserByWorkingShiftId($shift_id){
			$m_position = M('position');
			$m_workingshift = M('workingShift');
			$user = $this->where(array('working_shift_id'=>$shift_id))->select();
			foreach($user as $k=>$v){
				if($v['type'] == 0){
					$user[$k]['type_name'] = '试用员工';
				}elseif($v['type'] == 1){
					$user[$k]['type_name'] = '正式员工';
				}else{
					$user[$k]['type_name'] = '临时员工';
				}
				$user[$k]['position_name'] = $m_position->where(array('position_id'=>$v['position_id']))->getField('name');
				$user[$k]['working_shift_name'] = $m_workingshift->where(array('working_shift_id'=>$v['working_shift_id']))->getField('name');
			}
			return $user;
		}
    }