<?php 
class MessageModel extends Model{
	function getMessagePageList($p,$where){
		$user = D('Message');
		import('@.ORG.Page');
		$count = $user->where($where)->count();
		$Page = new Page($count,15);
		$show  = $Page->show();
		$messagelist = $user->where($where)->page($p.',15')->order('send_time desc')->select();
		$users = array();
		foreach($messagelist as $key => $val){
			if(array_key_exists('to_user_id',$where)){
				if(!array_key_exists($val['to_user_id'],$users)){
					$user = D('user')->where(array('user_id'=>$val['user_id']))->getField('name');
					$users[$val['user_id']] = $user?$user:'系统管理员';
				}
				$messagelist[$key]['name'] = $users[$val['user_id']];
			}else{
				if(!array_key_exists($val['user_id'],$users)){
					$user = D('user')->where(array('user_id'=>$val['to_user_id']))->getField('name');
					$users[$val['to_user_id']] = $user?$user:'系统管理员';
				}
				$messagelist[$key]['to_name'] = $users[$val['to_user_id']];
			}
		}
		return array('page'=>$show ,'messagelist'=>$messagelist);
	}
	public function send($info){
		if(M('message')->create($info)){
			$message_id = M('message')->add();
			if( $message_id && $info['message'] != 0 ){
				M('message')->where(array('message_id'=>$info['message']))->setField('status',1);
			}
			return $message_id;
		}
		return false;
	}
	public function getMessageInfo($message_id){
		$info = M('message')->where(array('message_id'=>$message_id))->find();
		$user = D('User')->getUserInfo(array('user_id'=>$info['user_id']));
		$to_user = D('User')->getUserInfo(array('user_id'=>$info['to_user_id']));
		$info['name'] = $user['name']?$user['name']:'系统管理员';
		$info['to_name'] = $to_user['name']?$to_user['name']:'系统管理员';
		return $info;
	}
	public function deleteMessage($message_id){
		$message_info = $this->getMessageInfo($message_id);
		if(session('user_id') == $message_info['user_id']){
			if($message_info['is_deleted'] == 1){
				return M('message')->where(array('message_id'=>$message_id))->delete();
			}else{
				return M('message')->where(array('message_id'=>$message_id))->setField('is_deleted',2);
			}
		}elseif(session('user_id') == $message_info['to_user_id']){
			if($message_info['is_deleted'] == 2){
				return M('message')->where(array('message_id'=>$message_id))->delete();
			}else{
				return M('message')->where(array('message_id'=>$message_id))->setField('is_deleted',1);
			}
		}
		return false;
	}
	public function getContacts($p){
		$contacts =M('mycontacts');
		import('@.ORG.Page');
		$count = $contacts->where(array('user_id'=>session('user_id')))->count();
		$Page = new Page($count,15);
		$show  = $Page->show();
		$userlist = $contacts->where(array('user_id'=>session('user_id')))->page($p.',15')->select();
		return array('page'=>$show ,'userlist'=>$userlist);
	}
	public function getMyContacts($mycontacts_id){
		$info = M('mycontacts')->where(array('mycontacts_id'=>$mycontacts_id,'user_id'=>session('user_id')))->find();
		return $info;
	}
	public function addContacts($info){
		$contacts =M('mycontacts');
		if($contacts->create($info)){
			return $contacts->add();
		}
		return false;
	}
	public function editContacts($info){
		$contacts =M('mycontacts');
		if($contacts->create($info)){
			return $contacts->where(array('mycontacts_id'=>$info['mycontacts_id'],'user_id'=>$info['user_id']))->save();
		}
		return false;
	}
	
	public function getMessageTips($where){
		$message_tip = $this->where($where)->count();
		return $message_tip;
	}
}