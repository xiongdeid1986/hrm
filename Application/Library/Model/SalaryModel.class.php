<?php
class SalaryModel extends Model{
	public function getPageSalaryItem($p,$where=array()){
		import('@.ORG.Page');
		$count = M('salary_item')->where($where)->count();
		$Page = new Page($count,15);
		$Page->parameter = $where;
		$show  = $Page->show();
		$salary_item_list = M('salary_item')->where($where)->order('sort_id ASC')->page($p.',15')->select();
		return array('page'=>$show ,'list'=>$salary_item_list);
	}
	public function addSalaryItem($info){
		if(M('salary_item')->create($info)){
			return M('salary_item')->add();
		}
		return false;
	}
	public function editSalaryItem($info){
		if(M('salary_item')->create($info)){
			return M('salary_item')->save();
		}
		return false;
	}
	public function getSalaryItemInfo($salary_item_id){
		$info = M('salary_item')->where(array('salary_item_id'=>$salary_item_id))->find();
		return $info;
	}
	public function getAllSalaryItem(){
		$salary_item_list = M('salary_item')->order('sort_id ASC')->select();
		return $salary_item_list;
	}
	public function getPageSalarySuit($p,$where=array()){
		import('@.ORG.Page');
		$count = M('salary_suit')->where($where)->count();
		$Page = new Page($count,15);
		$Page->parameter = $where;
		$show  = $Page->show();
		$salarysuitlist = M('salary_suit')->where($where)->page($p.',15')->select();
		return array('page'=>$show ,'list'=>$salarysuitlist);
	}
	public function addSalarySuit($info){
		if(M('salary_suit')->create($info)){
			return M('salary_suit')->add();
		}
		return false;
	}
	public function editSalarySuit($info){
		if(M('salary_suit')->create($info)){
			return M('salary_suit')->save();
		}
		return false;
	}
	public function getSalarySuitInfo($salary_suit_id){
		$info = M('salary_suit')->where(array('salary_suit_id'=>$salary_suit_id))->find();
		if($info){
			$info['items'] = unserialize($info['items']);
			foreach($info['items'] as $k=>$v){
				$info['items'][$k]['name'] = M('Salary_item')->where(array('salary_item_id'=>$v['item']))->getField('name');
			}
		}
		return $info;
	}
	public function getAllSalarySuit(){
		$salary_item_list = M('salary_suit')->select();
		foreach($salary_item_list as $key=>$val){
			$val['items']=unserialize($val['items']);
			foreach($val['items'] as $k=>$v){
				$val['items'][$k]['name'] = M('Salary_item')->where(array('salary_item_id'=>$v['item']))->getField('name');
			}
			$salary_item_lists['id'.$val['salary_suit_id']]=$val;
		}
		return $salary_item_lists;
	}
	
	public function getPageSalary($p,$where){
		if(!empty($where['user_name'])){
			$user_id = M('user')->where(array('name'=>array('like', '%'.$where['user_name'].'%')))->getField('user_id');
			$where['user_id'] = array('eq', $user_id);
			unset($where['user_name']);
		}
		import('@.ORG.Page');
		$count = M('salary')->where($where)->count();
		$Page = new Page($count,15);
		$Page->parameter = $where;
		$show  = $Page->show();
		$salarylist = M('salary')->where($where)->page($p.',15')->select();
		foreach($salarylist as $key=>$val){
			$salarylist[$key]['month'] = date('Y年m月',mktime(0,0,0,substr($val['month'],-2),1,substr($val['month'],0,4)));
			$salarylist[$key]['username'] = M('user')->where(array('user_id'=>$val['user_id']))->getField('name');
			$salarylist[$key]['suit_name'] = M('salarySuit')->where(array('salary_suit_id'=>$val['suit_id']))->getField('name');
			$salarylist[$key]['item_detail'] = $this->getSalaryInfo($val['salary_id']);
		}
		return array('page'=>$show ,'list'=>$salarylist);
	}
	
	public function addSalary($infos){
		$return = false;
		foreach($infos as $info){
			if(M('salary')->create($info)){
				$return = M('salary')->add() || $return;
			}
		}
		return $return;
	}
	public function editSalary($info){
		if(M('salary')->create($info)){
			return M('salary')->save();
		}
		return false;
	}
	public function getSalaryInfo($salary_id){
		$info = M('salary')->where(array('salary_id'=>$salary_id))->find();
		if($info){
			$info['month_num'] = $info['month'];
			$info['month'] = date('Y年m月',mktime(0,0,0,substr($info['month'],-2),1,substr($info['month'],0,4)));
			$info['username'] = M('user')->where(array('user_id'=>$info['user_id']))->getField('name');
			$info['items'] = unserialize($info['items']);
			$info['suit'] = $this->getSalarySuitInfo($info['suit_id']);
		}
		return $info;
	}
	public function getMonthly($where){
		$salarylist = M('salary')->where($where)->getField('salary_id',true);
		foreach($salarylist as $key=>$val){
			$salarylist[$key] = $this->getSalaryInfo($val);
		}
		return $salarylist;
	}
}