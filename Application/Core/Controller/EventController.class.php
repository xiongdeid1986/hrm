<?php
namespace Core\Controller;
use Think\Controller;

class EventController extends Controller {
    public function _initialize(){
		$action = array(
			'users'=>array('ajaxmonth'),
			'anonymous'=>array('')
		);
		B('Authenticate', $action);
	}
    public function ajaxMonth(){
		$year = $this->_get('year','intval',date('Y'));
		$month = $this->_get('month','intval',date('m'));
		$time = mktime(0,0,0,$month+1,1,$year);
		$month_event = D('Event')->getMonthEvent($year,$month+1,session('user_id'));
		$return = array();
		$start_time = mktime(0,0,0,$month,1,$year);
		for($i = 1;$i<= date('t',$start_time);$i++){
			$count = count($month_event[$i]);
			switch($count){
				case 0:
					$return[$i] = null;
					break;
				case 1:
					$return[$i] = array('url'=>U('core/event/view','id='.$month_event[$i][0]['event_id'].'&year='.date('Y',$time).'&momth='.date('n',$time).'&day='.$i),'title'=>$month_event[$i][0]['title']);
					break;
				default:
					$return[$i] = array('url'=>U('core/event/day','year='.date('Y',$time).'&momth='.date('n',$time).'&day='.$i),'title'=>'点击查看'.date('Y年m月',$time).$i.'日日程');
					break;
			}
		}
		$this->Ajaxreturn($return);
    }
	public function index(){
		$search_title = empty($_GET['search_title']) ? '' : trim($_GET['search_title']);
		$user_id = empty($_GET['user_id']) ? '' : trim($_GET['user_id']);
		$search_start_time = empty($_GET['search_start_time']) ? '' : strtotime($_GET['search_start_time']);
		$search_end_time = empty($_GET['search_end_time']) ? '' : strtotime($_GET['search_end_time']);
		
		if(!empty($search_title)){
			$condition['title'] = array('like', '%'.$search_title.'%');
		}
		if(!empty($user_id)){
			$condition['user_id'] = array('eq', $user_id);
		}else{
			$condition['user_id'] = array('eq', session('user_id'));
		}
		if(!empty($search_start_time)){
			if(!empty($search_end_time)){
				$condition['start_time'] = array('between', array($search_start_time, $search_end_time));
			}else{
				$condition['start_time'] = array('between', array($search_start_time-1, $search_start_time+86400));
			}
		}
		if(!empty($search_end_time)){
			if(!empty($search_start_time)){
				$condition['end_time'] = array('between', array($search_start_time, $search_end_time));
			}else{
				$condition['end_time'] = array('between', array($search_end_time-1, $search_end_time+86400));
			}
		}
		
		$p = $this->_get('p','intval',1);
		$eventpagelist = D('Event')->getPageEvent($p,$condition);
		$this->assign('eventlist', $eventpagelist['eventlist']);
		$this->assign('page', $eventpagelist['page']);
		$this->alert = parseAlert();
		$this->display();
	}
	public function month(){
		$this->alert = parseAlert();
		$this->display();
	}
	public function add(){
		if($this->isPost()){
			$info['title'] = $this->_post('title','trim','');
			if($info['title'] == ''){
				alert('error','请填写日程主题',U('core/event/add'));
			}
			$info['user_id'] = $this->_post('executor_id','intval',0);
			if($info['user_id'] == ''){
				alert('error','请选择日程负责人',U('core/event/add'));
			}
			$info['content'] = $this->_post('content','trim','');
			if($info['content'] == ''){
				alert('error','请填写日程描述',U('core/event/add'));
			}
			$info['address'] = $this->_post('address','trim','');
			$info['start_time'] = $this->_post('start_time','strtotime',0);
			if($info['start_time'] == ''){
				alert('error','请选择日程开始时间',U('core/event/add'));
			}
			$info['end_time'] = $this->_post('end_time','strtotime',0);
			if($info['end_time'] == ''){
				alert('error','请选择日程结束时间',U('core/event/add'));
			}
			$info['creator_user_id'] = session('user_id');
			$info['create_time'] = time();
			if(D('Event')->addEvent($info)){
				alert('success','日程添加成功',U('core/event/index'));
			}else{
				alert('error','日程添加失败',U('core/event/add'));
			}
		}else{
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function edit(){
		if($this->isPost()){
			$info['event_id'] = $this->_post('event_id','intval',0);
			$data = D('Event')->getEventInfo($info['event_id']);
			if(!$data){
				alert('error','参数错误',U('core/event/index'));
			}
			if(!session('?admin') && session('user_id') != $data['user_id'] && session('user_id') != $data['creator_user_id']){
				alert('error','你没有权限编辑此日程',U('core/event/index'));
			}
			$info['title'] = $this->_post('title','trim','');
			if($info['title'] == ''){
				alert('error','请填写日程主题',U('core/event/edit','id='.$info['event_id']));
			}
			$info['user_id'] = $this->_post('executor_id','intval',0);
			if($info['user_id'] == ''){
				alert('error','请选择日程负责人',U('core/event/edit','id='.$info['event_id']));
			}
			$info['content'] = $this->_post('content','trim','');
			if($info['user_id'] == ''){
				alert('error','请填写日程描述',U('core/event/edit','id='.$info['event_id']));
			}
			$info['address'] = $this->_post('address','trim','');
			$info['start_time'] = $this->_post('start_time','strtotime',0);
			if($info['user_id'] == ''){
				alert('error','请选择日程开始时间',U('core/event/edit','id='.$info['event_id']));
			}
			$info['end_time'] = $this->_post('end_time','strtotime',0);
			if($info['user_id'] == ''){
				alert('error','请选择日程结束时间',U('core/event/edit','id='.$info['event_id']));
			}
			if(D('Event')->editEvent($info)){
				alert('success','日程保存成功',U('core/event/index'));
			}else{
				alert('error','日程数据无变化，保存失败',U('core/event/edit','id='.$info['event_id']));
			}
		}else{
			$event_id = $this->_get('id','intval',0);
			$info = D('Event')->getEventInfo($event_id);
			if(!$info){
				alert('error','参数错误,日程不存在',U('core/event/index'));
			}
			if(!session('?admin') && session('user_id') != $info['user_id'] && session('user_id') != $info['creator_user_id']){
				alert('error','你没有权限编辑此日程',U('core/event/index'));
			}
			$this->assign('info', $info);
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function delete(){
		if($this->isPost()){
			$event_ids = $this->_post('event_id','intval',0);
			if(!$event_ids){
				alert('error','请选择日程',U('core/event/index'));
			}
			if(!session('?admin')){
				$str = '';
				foreach($event_ids as $value){
					$info = D('Event')->getEventInfo($value);
					$str .= (session('user_id') != $info['user_id'] || session('user_id') != $info['creator_user_id'])? $info['title'].',':'';
				}
				if($str != ''){
					alert('error','日程'.$str.'等你没有删除权限',U('core/event/index'));
				}
			}
			if(D('Event')->deleteEvent($event_ids)){
				alert('success','日程删除成功',U('core/event/index'));
			}else{
				alert('error','日程删除失败',U('core/event/index'));
			}
		}else{
			$event_id = $this->_get('id','intval',0);
			$info = D('Event')->getEventInfo($event_id);
			if(session('?admin')  || session('user_id') != $info['user_id'] || session('user_id') != $info['creator_user_id']){
				alert('error','你没有权限删除此日程',U('core/event/index'));
			}
			if(D('Event')->deleteEvent($event_id)){
				alert('success','日程删除成功',U('core/event/index'));
			}else{
				alert('error','日程删除失败',U('core/event/index'));
			}
		}
	}
	public function day(){
		$year = $this->_get('year','intval',date('Y'));
		$momth = $this->_get('momth','intval',date('n'));
		$day = $this->_get('day','intval',date('j'));
		$eventlist = D('Event')->getDateEvent(mktime(0,0,0,$momth,$day,$year),session('user_id'));
		$this->assign('eventlist', $eventlist);
		$this->assign('taday', date('Y年m月d日',mktime(0,0,0,$momth,$day,$year)));
		$this->alert = parseAlert();
		$this->display();
	}
	public function view(){
		$year = $this->_get('year','intval',0);
		$momth = $this->_get('momth','intval',0);
		$day = $this->_get('day','intval',0);
		if($year && $momth && $day){
			$this->assign('taday', date('Y年m月d日',mktime(0,0,0,$momth,$day,$year)));
		}
		$event_id = $this->_get('id','intval',0);
		$info = D('Event')->getEventInfo($event_id);
		if(!$info){
			alert('error','参数错误,日程不存在',U('core/event/index'));
		}
		$this->assign('info', $info);
		$this->alert = parseAlert();
		$this->display();
	}
}