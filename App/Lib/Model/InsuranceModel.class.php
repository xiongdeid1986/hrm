<?php
class InsuranceModel extends Model{
	public function getPageInsuranceItem($p ,$where = array()){
		import('@.ORG.Page');
		$count = M('insuranceItem')->where($where)->count();
		$Page = new Page($count,15);
		$Page->parameter = $where;
		$show  = $Page->show();
		$insuranceitemlist = M('insuranceItem')->where($where)->page($p.',15')->select();
		return array('page'=>$show ,'list'=>$insuranceitemlist);
	}
	public function getItemAll(){
		$list = M('insuranceItem')->select();
		foreach( $list as $val){
			$return[$val['insurance_item_id']] = $val;
		}
		return $return;
	}
	public function addInsuranceItem($info){
		if(M('insuranceItem')->create($info)){
			return M('insuranceItem')->add();
		}
		return false;
	}
	public function editInsuranceItem($info){
		if(M('insuranceItem')->create($info)){
			return M('insuranceItem')->save();
		}
		return false;
	}
	public function getInsuranceItemInfo($insurance_item_id){
		return M('insuranceItem')->where(array('insurance_item_id'=>$insurance_item_id))->find();
	}
	public function getPageInsuranceSuit($p ,$where = array()){
		import('@.ORG.Page');
		$count = M('insuranceSuit')->where($where)->count();
		$Page = new Page($count,15);
		$Page->parameter = $where;
		$show  = $Page->show();
		$insurancesuitlist = M('insuranceSuit')->where($where)->page($p.',15')->select();
		foreach($insurancesuitlist as $key=>$val){
			$insurancesuitlist[$key]['username'] = D('User')->where(array('user_id'=>$val['create_user_id']))->getField('name');
		}
		return array('page'=>$show ,'list'=>$insurancesuitlist);
	}
	public function addInsuranceSuit($info){
		if(M('insuranceSuit')->create($info)){
			return M('insuranceSuit')->add();
		}
		return false;
	}
	public function editInsuranceSuit($info){
		if(M('insuranceSuit')->create($info)){
			return M('insuranceSuit')->save();
		}
		return false;
	}
	public function getSuitInfo($insurance_suit_id){
		$info = M('insuranceSuit')->where(array('insurance_suit_id'=>$insurance_suit_id))->find();
		if($info){
			$info['items'] = unserialize($info['items']);
		}
		return $info;
	}
	public function getSuitAll(){
		$list = M('insuranceSuit')->select();
		foreach( $list as $val){
			$return[$val['insurance_suit_id']] = $val;
		}
		return $return;
	}
	public function getPageInsurance($p ,$where = array()){
		import('@.ORG.Page');
		$count = M('insurance')->where($where)->count();
		$Page = new Page($count,15);
		$Page->parameter = $where;
		$show  = $Page->show();
		$insurancelist = M('insurance')->where($where)->page($p.',15')->select();
		foreach($insurancelist as $key=>$val){
			$userinfo = D('User')->getUserInfo(array('user_id'=>$val['user_id']));
			$insurancelist[$key] = $val+$userinfo;
		}
		return array('page'=>$show ,'list'=>$insurancelist);
	}
	public function getInsuranceInfo($insurance_id){
		$info = M('insurance')->where(array('insurance_id'=>$insurance_id))->find();
		if($info){
			$userinfo = D('User')->getUserInfo(array('user_id'=>$info['user_id']));
			$info = $info+$userinfo;
		}
		return $info;
	}
	public function addInsurance($info){
		if(M('insurance')->create($info)){
			return M('insurance')->add();
		}
		return false;
	}
	public function editInsurance($info){
		if(M('insurance')->create($info)){
			return M('insurance')->save();
		}
		return false;
	}
}