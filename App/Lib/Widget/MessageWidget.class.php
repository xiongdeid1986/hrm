<?php 
class MessageWidget extends Widget {
	public function render($data){
		$message_list = D('Message')->getMessagePageList(1,array('read_time'=>0,'to_user_id'=>session('user_id'),'is_deleted'=>array('neq',1)));
		return $this->renderFile ("index", array('message_list'=>$message_list['messagelist']));
	}
}