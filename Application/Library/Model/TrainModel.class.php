<?php
class TrainModel extends Model {
	public function getPageTrain($p,$where=array()){
		import('@.ORG.Page');
		$count = $this->where($where)->count();
		$Page = new Page($count,15);
		$Page->parameter = $where;
		$show  = $Page->show();
		$trainlist = $this->where($where)->order('create_time desc')->page($p.',15')->select();
		foreach($trainlist as $key=>$val){
			$trainlist[$key]['username'] = M('user')->where(array('user_id'=>$val['owner_user_id']))->getField('name');
		}
		return array('page'=>$show ,'trainlist'=>$trainlist);
	}
	public function addTrain($info){
		$d_train = M('train');
		if($d_train->create($info)){
			return $d_train->add();
		}
		return false;
	}
	public function editTrain($info){
		$d_train = M('train');
		if($d_train->create($info)){
			return $d_train->save();
		}
		return false;
	}
	public function getTrainInfo($train_id){
		$info = M('train')->where(array('train_id'=>$train_id))->find();
		if($info){
			$info['username'] = M('user')->where(array('user_id'=>$info['owner_user_id']))->getField('name');
			return $info;
		}
		return false;
	}
	public function addTrainPro($info){
		$d_train = M('trainPro');
		if($d_train->create($info)){
			return $d_train->add();
		}
		return false;
	}
	public function getPageTrainPro($p,$where=array()){
		import('@.ORG.Page');
		$count = M('trainPro')->where($where)->count();
		$Page = new Page($count,15);
		$Page->parameter = $where;
		$show  = $Page->show();
		$trainprolist = M('trainPro')->where($where)->order('create_time desc')->page($p.',15')->select();
		foreach($trainprolist as $key=>$val){
			$trainprolist[$key]['username'] = M('user')->where(array('user_id'=>$val['owner_user_id']))->getField('name');
		}
		return array('page'=>$show ,'trainprolist'=>$trainprolist);
	}
	public function editTrainPro($info){
		$d_train = M('trainPro');
		if($d_train->create($info)){
			return $d_train->save();
		}
		return false;
	}
	public function getTrainProInfo($train_pro_id){
		$info = M('trainPro')->where(array('train_pro_id'=>$train_pro_id))->find();
		if($info){
			$info['username'] = M('user')->where(array('user_id'=>$info['owner_user_id']))->getField('name');
			return $info;
		}
		return false;
	}
}