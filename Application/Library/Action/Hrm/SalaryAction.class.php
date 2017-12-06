<?php
class SalaryAction extends Action{
	public function _initialize(){
		$actions = array(
			'users'=>array('selectuser'),
			'anonymous'=>array()
		);
		B('Authenticate', $actions);
	}
	
	public function salaryitem(){
		$p = $this->_get('p','intval',1);
		$salaryitempagelist = D('Salary')->getPageSalaryItem($p,array());
		$this->assign('list', $salaryitempagelist['list']);
		$this->assign('page', $salaryitempagelist['page']);
		$this->assign('alert', parseAlert());
		$this->display();
	}
	
	public function addSalaryItem(){
		if($this->isPost()){
			$info['name'] = $this->_post('name','trim','');
			if($info['name'] == ''){
				alert('error','项目名称不能为空',U(''));
			}
			$info['content'] = $this->_post('content','trim','');
			$info['sort_id'] = $this->_post('sort_id','intval',0);
			if(D('Salary')->addSalaryItem($info)){
				alert('success','添加薪资项目成功',U('hrm/salary/salaryitem'));
			}else{
				alert('error','添加薪资项目失败',U(''));
			}
		}else{
			$this->assign('alert', parseAlert());
			$this->display();
		}
	}
	public function editSalaryItem(){
		if($this->isPost()){
			$info['salary_item_id'] = $this->_post('salary_item_id','intval',0);
			$info['name'] = $this->_post('name','trim','');
			if($info['name'] == ''){
				alert('error','项目名称不能为空',U(''));
			}
			$info['content'] = $this->_post('content','trim','');
			$info['sort_id'] = $this->_post('sort_id','intval',0);
			if(D('Salary')->editSalaryItem($info)){
				alert('success','保存薪资项目成功',U('hrm/salary/salaryitem'));
			}else{
				alert('error','数据无变化，修改薪资项目失败',U(''));
			}
		}else{
			$salary_item_id = $this->_get('id','intval',0);
			$info = D('Salary')->getSalaryItemInfo($salary_item_id);
			if(!$info){
				alert('error','信息不存在',U('hrm/salary/salaryitem'));
			}
			$this->assign('info', $info);
			$this->assign('alert', parseAlert());
			$this->display();
		}
	}
	public function deleteSalaryItem(){
		if($this->isPost()){
			$where['salary_item_id'] = array('in',$this->_post('salary_item_id'));
		}else{
			$where['salary_item_id'] = $this->_get('id','intval',0);
			$info = D('Salary')->getSalaryItemInfo($where['salary_item_id']);
			if(!$info){
				alert('error','信息不存在',U('hrm/salary/salaryitem'));
			}
		}
		if(M('salary_item')->where($where)->delete()){
			alert('success','删除薪资项目成功',U('hrm/salary/salaryitem'));
		}else{
			alert('error','删除薪资项目失败',U('hrm/salary/salaryitem'));
		}
	}
	public function salarySuit(){
		$p = $this->_get('p','intval',1);
		$salarysuitpagelist = D('Salary')->getPageSalarySuit($p,array());
		$this->assign('list', $salarysuitpagelist['list']);
		$this->assign('page', $salarysuitpagelist['page']);
		$this->assign('alert', parseAlert());
		$this->display();
	}
	public function addSalarySuit(){
		if($this->isPost()){
			$info['name'] = $this->_post('name','trim','');
			if($info['name'] == ''){
				alert('error','请填写薪资套帐名称',U(''));
			}
			$item = $this->_post('item');
			$calculation = $this->_post('calculation');
			$itemArr = array();
			$items = array();
			foreach($item as $key=>$val){
				if($val && !in_array($val,$itemArr)){
					$itemArr[] = $val;
					$items[] = array(
						'item' => $item[$key],
						'calculation' => $calculation[$key],
					);
				}
			}
			if($items == array()){
				alert('error','请选择薪资项目',U(''));
			}
			$info['items'] = serialize($items);
			$info['create_user_id'] = session('user_id');
			$info['create_time'] = time();
			if(D('Salary')->addSalarySuit($info)){
				alert('success','添加薪资套帐成功',U('hrm/salary/salarysuit'));
			}else{
				alert('error','添加薪资套帐失败',U(''));
			}
		}else{
			$item_list = D('Salary')->getAllSalaryItem();
			$this->assign('item_list', $item_list);
			$this->assign('alert', parseAlert());
			$this->display();
		}
	}
	public function editSalarySuit(){
		if($this->isPost()){
			$info['salary_suit_id'] = $this->_post('salary_suit_id','intval',0);
			$info['name'] = $this->_post('name','trim','');
			if($info['name'] == ''){
				alert('error','请填写薪资套帐名称',U('hrm/salary/salarysuit','id='.$info['salary_suit_id']));
			}
			$item = $this->_post('item');
			$calculation = $this->_post('calculation');
			$itemArr = array();
			$items = array();
			foreach($item as $key=>$val){
				if($val && !in_array($val,$itemArr)){
					$itemArr[] = $val;
					$items[] = array(
						'item' => $item[$key],
						'calculation' => $calculation[$key],
					);
				}
			}
			if($items == array()){
				alert('error','请选择薪资项目',U(''));
			}
			$info['items'] = serialize($items);
			$info['update_time'] = time();
			if(D('Salary')->editSalarySuit($info)){
				alert('success','修改薪资套帐成功',U('hrm/salary/salarysuit'));
			}else{
				alert('error','数据无变化，修改薪资套帐失败',U('hrm/salary/salarysuit','id='.$info['salary_suit_id']));
			}
		}else{
			$salary_suit_id = $this->_get('id','intval',0);
			$info = D('Salary')->getSalarySuitInfo($salary_suit_id);
			if(!$info){
				alert('error','信息不存在',U('hrm/salary/salarysuit'));
			}
			$item_list = D('Salary')->getAllSalaryItem();
			$this->assign('info', $info);
			$this->assign('item_list', $item_list);
			$this->assign('alert', parseAlert());
			$this->display();
		}
	}
	public function deleteSalarySuit(){
		if($this->isPost()){
			$where['salary_suit_id'] = array('in',$this->_post('salary_suit_id'));
		}else{
			$where['salary_suit_id'] = $this->_get('id','intval',0);
			$info = D('Salary')->getSalarySuitInfo($where['salary_suit_id']);
			if(!$info){
				alert('error','信息不存在',U('hrm/salary/salaryitem'));
			}
		}
		if(M('salary_suit')->where($where)->delete()){
			alert('success','删除薪资套帐成功',U('hrm/salary/salarysuit'));
		}else{
			alert('error','删除薪资套帐失败',U('hrm/salary/salarysuit'));
		}
	}
	public function index(){
		$search_user_name = empty($_GET['search_user_name']) ? '' : trim($_GET['search_user_name']);
		$search_start_time = empty($_GET['search_start_time']) ? '' : $_GET['search_start_time'];
		$search_end_time = empty($_GET['search_end_time']) ? '' : $_GET['search_end_time'];
		
		if(!empty($search_user_name)){
			$condition['user_name'] = $search_user_name;
		}
		if(!empty($search_start_time)){
			if(!empty($search_end_time)){
				if($search_start_time > $search_end_time){
						alert('error','结束时间不能小于开始时间',U(''));
				}
				$month = $search_start_time;
				do{
					$date[] = $month;
					$month++;
					if(substr($month,-2) > 12){
						$month += 88;
					}
				}while($search_end_time >= $month);
				$condition['month'] = array('in',$date);
			}else{
				$condition['month'] = array('eq', $search_start_time);
			}
		}
		if(!empty($search_end_time)){
			if(!empty($search_start_time)){
				if($search_start_time > $search_end_time){
						alert('error','结束时间不能小于开始时间',U(''));
				}
				$month = $search_start_time;
				do{
					$date[] = $month;
					$month++;
					if(substr($month,-2) > 12){
						$month += 88;
					}
				}while($search_end_time >= $month);
				$condition['month'] = array('in',$date);
			}else{
				$condition['month'] = array('eq', $search_end_time);
			}
		}
		
		$p = $this->_get('p','intval',1);
		$salarypagelist = D('Salary')->getPageSalary($p, $condition);
		$this->assign('list', $salarypagelist['list']);
		$this->assign('page', $salarypagelist['page']);
		$this->assign('alert', parseAlert());
		$this->display();
	}
	public function add(){
		if($this->isPost()){
			$month = $this->_post('month','trim','');
			if($month == ''){
				alert('error','请选择发放月份',U(''));
			}
			$suit_id = $this->_post('suit_id','intval','0');
			$suit_info = D('Salary')->getSalarySuitInfo($suit_id);
			if(!$suit_info){
				alert('error','请选择薪资套帐',U(''));
			}
			foreach($suit_info['items'] as $v){
				$items[$v['item']] = $this->_post($v['item']);
				$items[$v['item'].'content'] = $this->_post($v['item'].'content');
			}
			$user_ids = $this->_post('user_id');
			foreach($user_ids as $key=>$val){
				$data = array(
					'user_id'=>$val,
					'month'=>$month,
					'suit_id'=>$suit_id,
					'create_user_id'=>session('user_id'),
					'create_time'=>time(),
					'money'=>0
					);
				foreach($suit_info['items'] as $v){
					$item[$v['item']] = $items[$v['item']][$key];
					$item[$v['item'].'content'] = $items[$v['item'].'content'][$key];
					if($v['calculation'] == 1){
						$data['money'] += $item[$v['item']];
					}elseif($v['calculation'] == 2){
						$data['money'] -= $item[$v['item']];
					}
				}
				$data['items'] = serialize($item);
				$info[] = $data;
			}
			if(D('Salary')->addSalary($info)){
				alert('success','薪资发放成功',U('hrm/salary/index'));
			}else{
				alert('error','薪资发放失败',U(''));
			}
		}else{
			$suit_list = D('Salary')->getAllSalarySuit($p,array());
			$this->assign('suit_list', $suit_list);
			$this->assign('alert', parseAlert());
			$this->display();
		}
	}
	public function edit(){
		if($this->isPost()){
			$info['salary_id'] = $this->_post('salary_id','intval',0);
			$info['user_id'] = $this->_post('user_id','intval',0);
			if($info['user_id'] == 0){
				alert('error','请选择员工',U('hrm/salary/edit','id='.$info['salary_id']));
			}
			$info['month'] = $this->_post('month','trim','');
			if($info['month'] == ''){
				alert('error','请选择发放月份',U('hrm/salary/edit','id='.$info['salary_id']));
			}
			$info['suit_id'] = $this->_post('suit_id','intval',0);
			$suit_info = D('Salary')->getSalarySuitInfo($info['suit_id']);
			if(!$suit_info){
				alert('error','请选择薪资套帐',U('hrm/salary/edit','id='.$info['salary_id']));
			}
			$info['items'] = $this->_post('items');
			$info['money'] = 0;
			foreach($suit_info['items'] as $v){
				$item[$v['item']] = $info['items'][$v['item']];
				$item[$v['item'].'content'] = $info['items'][$v['item'].'content'];
				if($v['calculation'] == 1){
					$info['money'] += $item[$v['item']];
					
				}elseif($v['calculation'] == 2){
					$info['money'] -= $item[$v['item']];

				}
			}
			$info['items'] = serialize($item);
			$info['update_time'] = time();
			if(D('Salary')->editSalary($info)){
				alert('success','修改成功',U('hrm/salary/index'));
			}else{
				alert('error','数据无变化，修改失败',U('hrm/salary/index'));
			}
			
		}else{
			$suit_list = D('Salary')->getAllSalarySuit($p,array());
			$this->assign('suit_list', $suit_list);
			$salary_id = $this->_get('id','intval',0);
			$info = D('Salary')->getSalaryInfo($salary_id);
			if(!$info){
				alert('error','信息不存在',U('hrm/salary/index'));
			}
			$this->assign('info', $info);
			$this->assign('alert', parseAlert());
			$this->display();
		}
	}
	public function view(){
		$salary_id = $this->_get('id','intval',0);
		$info = D('Salary')->getSalaryInfo($salary_id);
		if(!$info){
			alert('error','信息不存在',U('hrm/salary/index'));
		}
		$this->assign('info', $info);
		$this->assign('alert', parseAlert());
		$this->display();
	}
	public function delete(){
		if($this->isPost()){
			$where['salary_id'] = array('in',$this->_post('salary_id'));
		}else{
			$where['salary_id'] = $this->_get('id','intval',0);
			$info = D('Salary')->getSalaryInfo($where['salary_id']);
			if(!$info){
				alert('error','信息不存在',U('hrm/salary/salaryitem'));
			}
		}
		if(D('Salary')->where($where)->delete()){
			alert('success','删除信息成功',U('hrm/salary/index'));
		}else{
			alert('error','删除信息失败',U('hrm/salary/index'));
		}
	}
	public function selectuser(){
		$d_user = D('User');
		$userlist = $d_user->getUserList();
		$this->assign('iuputid', $this->_get('iuputid','trim'));
		$this->assign('userlist', $userlist);
		$this->display();
	}
	public function monthly(){
		if($this->isPost()){
			if($_POST['user_id']){
				$data['user_id'] = array('in',explode(',',$_POST['user_id']));
			}
			if($_POST['start_time']>$_POST['end_time']){
				alert('error','结束时间不能小于开始时间',U(''));
			}
			$month = $_POST['start_time'];
			do{
				$date[] = $month;
				$month++;
				if(substr($month,-2) > 12){
					$month += 88;
				}
			}while($_POST['end_time'] >= $month);
			$data['month'] = array('in',$date);
			$info_list = D('Salary')->getMonthly($data);
			$this->monthly = $info_list;
		}
		$this->alert = parseAlert();
		$this->display();
	}
}