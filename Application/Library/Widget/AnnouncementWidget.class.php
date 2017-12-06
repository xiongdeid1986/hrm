<?php 
class AnnouncementWidget extends Widget {
	public function render($data){
		$top_announcement = D('Announcement')->getTopAnnouncement();
		$list_announcement = D('Announcement')->getAnnouncement(1,array('department_id'=>array('like','%'.session('department_id').'%'),'status'=>1,'set_top'=>0));
		return $this->renderFile ("index", array('top_announcement'=>$top_announcement,'list_announcement'=>$list_announcement['announcementlist']));
	}
}