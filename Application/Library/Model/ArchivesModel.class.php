<?php
class ArchivesModel extends Model {
	protected $_auto = array ( 
		array('birthday','strtotime',3,'function'),
		array('work_date','strtotime',3,'function')
	);
	protected $_map = array(
        'to_user_id' =>'user_id'
    );
	protected $_validate = array(
		array('user_id','require','请选择员工！'),
		array('user_id','','员工档案已存在！',0,'unique',1),
		array('id_num','require','请填写员工证件编号！'),
		array('origin','require','请填写员工籍贯！')
	);

	public function getPageArchives($p,$where=array()){
		import('@.ORG.Page');
		$count = $this->where($where)->count();
		$Page = new Page($count,15);
		$Page->parameter = $where;
		$show  = $Page->show();
		$archiveslist = $this->where($where)->order('user_id desc')->page($p.',15')->select();
		foreach($archiveslist as $key=>$val){
			$archiveslist[$key]['username'] = M('user')->where(array('user_id'=>$val['user_id']))->getField('name');
		}
		return array('page'=>$show ,'list'=>$archiveslist);
	}
	public function addArchives(){
		if($this->create()){
			$this->create_user_id = session('user_id');
			$this->create_time = time();
			return $this->add();
		}
		return false;
	}
	public function editArchives(){
		if($this->create()){
			$this->update_time = time();
			return $this->save();
		}
		return false;
	}
	public function getArchivesInfo($user_id){
		$info = $this->where(array('user_id'=>$user_id))->find();
		if($info){
			$info['username'] = M('user')->where(array('user_id'=>$info['user_id']))->getField('name');
			return $info;
		}
		return false;
	}
	public function stats($field){
		if($field == 'birthday' || $field == 'work_date'){
			$sql = "select ".$field."s ,count(1) as stats from( 
					select case 
						when ".$field.">".strtotime((date('Y')-10).date('-m-d'))." then '10' 
						when ".$field."<=".strtotime((date('Y')-11).date('-m-d'))." and ".$field.">=".strtotime((date('Y')-20).date('-m-d'))." then '20' 
						when ".$field."<=".strtotime((date('Y')-21).date('-m-d'))." and ".$field.">=".strtotime((date('Y')-30).date('-m-d'))." then '30' 
						when ".$field."<=".strtotime((date('Y')-31).date('-m-d'))." and ".$field.">=".strtotime((date('Y')-40).date('-m-d'))." then '40' 
						when ".$field."<".strtotime((date('Y')-40).date('-m-d'))." then '50' 
					end as ".$field."s from __TABLE__ )a group by ".$field."s";
			$query = $this->query($sql);
			foreach($query as $key=>$val){
				$info[$val[$field.'s']] = $val['stats'];
			}
		}else{
			$info = $this->group($field)->getField($field.',count(1) as stats');
		}
		return $info;
	}
	public function statsTable($field){
		if($field == 'birthday' || $field == 'work_date'){
			$sql = "select department_id,".$field."s,sum(stats) as tstats from (select position_id,".$field."s ,count(1) as stats from( 
					select case 
						when ".$field.">".strtotime((date('Y')-10).date('-m-d'))." then '10' 
						when ".$field."<=".strtotime((date('Y')-11).date('-m-d'))." and ".$field.">=".strtotime((date('Y')-20).date('-m-d'))." then '20' 
						when ".$field."<=".strtotime((date('Y')-21).date('-m-d'))." and ".$field.">=".strtotime((date('Y')-30).date('-m-d'))." then '30' 
						when ".$field."<=".strtotime((date('Y')-31).date('-m-d'))." and ".$field.">=".strtotime((date('Y')-40).date('-m-d'))." then '40' 
						when ".$field."<".strtotime((date('Y')-40).date('-m-d'))." then '50' 
					end as ".$field."s,user_id from __TABLE__ )a left join __PREFIX__user as u on a.user_id = u.user_id group by ".$field."s) b join __PREFIX__position as p on p.position_id = b.position_id group by ".$field."s";
			$query = $this->query($sql);
			foreach($query as $key=>$val){
				$info[$val['department_id']][$val[$field.'s']] = $val['tstats'];
			}
		}else{
			$query = $this->join(C('DB_PREFIX').'user on '.C('DB_PREFIX').'user.user_id = '.$this->trueTableName.'.user_id')->join(C('DB_PREFIX').'position on '.C('DB_PREFIX').'position.position_id = '.C('DB_PREFIX').'user.position_id')->group($this->trueTableName.'.'.$field)->getField($this->trueTableName.'.archives_id,'.C('DB_PREFIX').'position.department_id ,'.$this->trueTableName.'.'.$field.',count(1) as tstats');
			foreach($query as $key=>$val){
				$info[$val['department_id']][$val[$field]] = $val['tstats'];
			}
		}
		return $info;
	}
}