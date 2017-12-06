<?php 
class StaffContractModel extends Model{
	public function getPageConcract($p,$where){
		import('@.ORG.Page');
		$count = M('Staffcontract')->where($where)->count();
		$Page = new Page($count,15);
		$show  = $Page->show();
		$contractlist = M('Staffcontract')->where($where)->page($p.',15')->select();
		foreach($contractlist as $key=>$val){
			$contractlist[$key]['user_name'] = M('user')->where(array('user_id'=>$val['user_id']))->getField('name');
		}
		return array('page'=>$show ,'contractlist'=>$contractlist);
	}
	public function getConcractInfo($concract_id){
		$info = M('Staffcontract')->where(array('staffcontract_id'=>$concract_id))->find();
		if($info){
			$info['user_name'] = D('User')->where(array('user_id'=>$info['user_id']))->getField('name');
			$info['file'] = D('File')->where(array('module_id'=>$info['staffcontract_id'],'module'=>'staffcontract'))->select();
		}
		return $info;
	}
	public function addConcract($info){
		if(M('Staffcontract')->create($info)){
			return M('Staffcontract')->add();
		}
		return false;
	}
	public function editConcract($info){
		if(M('Staffcontract')->create($info)){
			return M('Staffcontract')->save();
		}
		return false;
	}
	public function deleteConcract($contract_id){
		$where['staffcontract_id'] = is_array($contract_id)?array('in',$contract_id):$contract_id;
		return M('Staffcontract')->where($where)->delete();
	}
}