<?php
namespace Core\Controller;
use Think\Controller;

class MessageController extends Controller {
	/*
		users	登录后无权限可见
		anonymous 匿名无权限也可见
	*/
    public function _initialize(){
		$actions = array(
			'users'=>array('index','send','view','delete','tips'),
			'anonymous'=>array('login')
		);
		B('Authenticate', $actions);
	}
	public function index(){
		$p = $this->_get('p','intval',1);
		$read = $this->_get('read','intval',1);
		$send = $this->_get('send','intval',0);
		if($send == 1){
			$where = array('user_id'=>session('user_id'),'is_deleted'=>array('neq',2));
		}elseif($read == 0){
			$where = array('to_user_id'=>session('user_id'),'read_time'=>0,'is_deleted'=>array('neq',1));
		}else{
			$where = array('to_user_id'=>session('user_id'),'is_deleted'=>array('neq',1));
		}
		$d_user = D('Message');
		$userpagelist = $d_user->getMessagePageList($p,$where);
		$this->assign('messagelist', $userpagelist['messagelist']);
		$this->assign('page', $userpagelist['page']);
		$this->assign('read', $read);
		$this->assign('send', $send);
		$this->alert = parseAlert();
		$this->display();
	}
	public function send(){
		if($this->_post()){
			$to_user_id = array_filter(explode(',',$this->_post('to_user_id')));
			if(empty($to_user_id)) alert('error','请选择收件人',U('core/message/send'));
			$info['title'] = $this->_post('title','trim','');
			if($info['title'] == '') alert('error','请填写信件标题',U('core/message/send'));
			$info['content'] = $this->_post('content','trim','');
			if($info['content'] == '') alert('error','请填写信件内容',U('core/message/send'));
			$info['user_id'] = session('user_id');
			$info['message'] = $this->_post('message','intval',0);
			$info['send_time'] = time();
			$return = false;
			foreach($to_user_id as $user_id){
				$info['to_user_id'] = $user_id;
				if($info['user_id'] != $info['to_user_id'] && D('Message')->send($info)){
					$return = true;
				}
			}
			if($return){
				alert('success','发送成功',U('core/message/index'));
			}else{
				alert('error','发送失败',U('core/message/send'));
			}
		}else{
			$message = $this->_get('id','intval',0);
			if($message != 0){
				$message_info = D('Message')->getMessageInfo($message);
				if($message_info['to_user_id'] == 0){
					alert('error','系统邮件禁止回复',U('core/message/index'));
				}
				$this->message_info = $message_info;
				$this->message = $message;
			}
			if($user_id = $this->_get('user_id','intval',0)){
				$userinfo = D('User')->getUserInfo(array('user_id'=>$user_id));
				$this->message_info = array('name'=>$userinfo['name'],'user_id'=>$userinfo['user_id']);
			}
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function view(){
		$message_id = $this->_get('id','intval',0);
		$message_info = D('Message')->getMessageInfo($message_id);
		if($message_info['read_time'] == 0 && $message_info['to_user_id'] == session('user_id')){
			D('Message')->where(array('message_id'=>$message_id))->setField('read_time',time());
		}
		$this->message_info = $message_info;
		$this->alert = parseAlert();
		$this->display();
	}
	public function delete(){
		if($this->isPost()){
			$message_ids = $this->_post('message_id');
			$return = false;
			foreach($message_ids as $message_id){
				$deletereutrn =  D('Message')->deleteMessage($message_id);
				$return = $deletereutrn || $return;
			}
			if($return){
				alert('success','删除成功',U('core/message/index'));
			}else{
				alert('error','删除失败',U('core/message/index'));
			}
			
		}else{
			$message_id = $this->_get('id','intval',0);
			if(D('Message')->deleteMessage($message_id)){
				alert('success','删除成功',U('core/message/index'));
			}else{
				alert('error','删除失败',U('core/message/index'));
			}
		}
		
	}
	public function contacts(){
		$type = $this->_get('type','intval',0) ;
		$p = $this->_get('p','intval',1);
		if($type == 0){
			$userpagelist = D('User')->getUserPageList($p);
			$this->assign('userlist', $userpagelist['userlist']);
			$this->assign('page', $userpagelist['page']);
			$this->assign('status_array',array('1'=>'在职','2'=>'离职','3'=>'退休','0'=>'未激活'));
		}else{
			$userpagelist = D('Message')->getContacts($p);
			$this->assign('userlist', $userpagelist['userlist']);
			$this->assign('page', $userpagelist['page']);
		}
		$this->type = $type;
		$this->alert = parseAlert();
		$this->display();
	}
	public function addContacts(){
		if($this->isPost()){
			$info['name'] = $this->_post('name','trim','');
			if($info['name'] == ''){
				alert('error','请填写联系人姓名',U('core/message/editContacts','id='.$info['mycontacts_id']));
			}
			$info['sex'] = $this->_post('sex','intval',0);
			$info['telephone'] = $this->_post('telephone','trim','');
			$info['email'] = $this->_post('email','trim','');
			$info['address'] = $this->_post('address','trim','');
			$info['description'] = $this->_post('description','trim','');
			$info['user_id'] = session('user_id');
			if(D('Message')->addContacts($info)){
				alert('success','联系人保存成功',U('core/message/contacts','type=1'));
			}else{
				alert('error','联系人保存失败',U('core/message/addContacts'));
			}
		}else{
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function editContacts(){
		if($this->isPost()){
			$info['mycontacts_id'] = $this->_post('mycontacts_id','intval',0);
			if($info['mycontacts_id'] == 0){
				alert('error','联系人不存在',U('core/message/contacts','type=1'));
			}
			$info['name'] = $this->_post('name','trim','');
			if($info['name'] == ''){
				alert('error','请填写联系人姓名',U('core/message/editContacts','id='.$info['mycontacts_id']));
			}
			$info['sex'] = $this->_post('sex','intval',0);
			$info['telephone'] = $this->_post('telephone','trim','');
			$info['email'] = $this->_post('email','trim','');
			$info['address'] = $this->_post('address','trim','');
			$info['description'] = $this->_post('description','trim','');
			$info['user_id'] = session('user_id');
			if(D('Message')->editContacts($info)){
				alert('success','联系人保存成功',U('core/message/contacts','type=1'));
			}else{
				alert('error','数据无变化，联系人保存失败',U('core/message/editContacts','id='.$info['mycontacts_id']));
			}
		}else{
			$mycontacts_id = $this->_get('id','intval',0);
			$info = D('Message')->getMyContacts($mycontacts_id);
			if(!$info){
				alert('error','联系人不存在',U('core/message/contacts','type=1'));
			}
			$this->info = $info;
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function deleteContacts(){
		$where['user_id'] = session('user_id');
		if($this->isPost()){
			$where['mycontacts_id'] = array('in',$this->_post('mycontacts_id'));
		}else{
			$where['mycontacts_id'] = $this->_get('id','intval',0);
		}
		if(M('mycontacts')->where($where)->delete()){
			alert('success','删除成功',U('core/message/contacts','type=1'));
		}else{
			alert('error','删除失败',U('core/message/contacts','type=1'));
		}
	}
	
	public function tips(){
		$tipArr = array();
		$d_message = D('Message');
		$message = $d_message->getMessageTips(array('to_user_id'=>session('user_id'), 'read_time'=>0 , 'is_deleted'=>array('neq', 1)));
		$tipArr['message'] = $message;
		$this->ajaxReturn($tipArr);
	}
}