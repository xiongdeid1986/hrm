<?php 
class EventModel extends Model{
	public function getMonthEvent($year,$month,$user_id){
		$where['user_id'] = $user_id;
		$where['is_deteled'] = 0;
		$start_time = mktime(0,0,0,$month,1,$year);
		$end_time = mktime(0,0,0,$month+1,1,$year);
		$data1['start_time'] = array('lt', $start_time -1 );
		$data1['end_time'] = array('gt', $start_time);
		$data['start_time'] = array('between',array($start_time-1 ,$end_time));
		$data['_logic'] = 'or';
		$data['_complex'] = $data1;
		$where['_complex'] = $data;
		$event_list = $this->where($where)->select();
		$month_event = array();
		foreach($event_list as $val){
			for($i = 1;$i<= date('t',$start_time);$i++){
				if(($val['start_time'] <= mktime(0,0,0,$month,$i,$year) && $val['end_time'] > mktime(0,0,0,$month,$i,$year)) || ($val['start_time'] > mktime(0,0,0,$month,$i,$year) && $val['start_time'] < mktime(0,0,0,$month,$i+1,$year))){
					$month_event[$i][] = $val;
				}
			}
		}
		return $month_event;
	}
	public function getDateEvent($start_time,$user_id){
		$where['user_id'] = $user_id;
		$where['is_deteled'] = 0;
		$end_time = $start_time + 24*60*60;
		$data1['start_time'] = array('lt', $start_time -1 );
		$data1['end_time'] = array('gt', $start_time);
		$data['start_time'] = array('between',array($start_time-1 ,$end_time));
		$data['_logic'] = 'or';
		$data['_complex'] = $data1;
		$where['_complex'] = $data;
		return $event_list = $this->where($where)->select();
	}
	public function getPageEvent($p,$where){
		import('@.ORG.Page');
		$count = M('event')->where($where)->count();
		$Page = new Page($count,15);
		$Page->parameter = $where;
		$show  = $Page->show();
		$eventlist = M('event')->where($where)->page($p.',15')->select();
		foreach($eventlist as $key=>$val){
			$eventlist[$key]['user_name'] = M('user')->where(array('user_id'=>$val['user_id']))->getField('name');
		}
		return array('page'=>$show ,'eventlist'=>$eventlist);
	}
	public function getEventInfo($event_id){
		$info = $this->where(array('event_id'=>$event_id))->find();
		$info['user_name'] = D('User')->where(array('user_id'=>$info['user_id']))->getField('name');
		return $info;
	}
	public function addEvent($info){
		if(M('event')->create($info)){
			return M('event')->add();
		}
		return false;
	}
	public function editEvent($info){
		if(M('event')->create($info)){
			return M('event')->where(array('event_id'=>$info['event_id']))->save();
		}
		return false;
	}
	public function deleteEvent($event_ids){
		return M('event')->where(array('event_id'=>array('in',$event_ids)))->delete();
	}
}