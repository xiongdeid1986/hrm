<?php
class InsuranceAction extends Action{
	public function _initialize(){
		$actions = array(
			'users'=>array(),
			'anonymous'=>array()
		);
		B('Authenticate', $actions);
	}
	public function insuranceItem(){
		$p = $this->_get('p','intval',1);
		$insuranceitempagelist = D('Insurance')->getPageInsuranceItem($p,array());
		$this->assign('list', $insuranceitempagelist['list']);
		$this->assign('page', $insuranceitempagelist['page']);
		$this->assign('alert', parseAlert());
		$this->display();
	}
	public function addInsuranceItem(){
		if($this->isPost()){
			$info['name'] = $this->_post('name','trim','');
			if($info['name'] == ''){
				alert('error','请填写社保项目名称',U(''));
			}
			$info['insurance_type'] = $this->_post('insurance_type','intval',0);
			if($info['insurance_type'] == 0){
				alert('error','请选择社保项目类型',U(''));
			}
			$info['create_user_id'] = session('user_id');
			$info['create_time'] = time();
			if(D('Insurance')->addInsuranceItem($info)){
				alert('success','添加社保项目成功',U('hrm/insurance/insuranceitem'));
			}else{
				alert('error','添加社保项目失败',U(''));
			}
		}else{
			$this->assign('alert', parseAlert());
			$this->display();
		}
	}
	public function editInsuranceItem(){
		if($this->isPost()){
			$info['insurance_item_id'] = $this->_post('insurance_item_id','intval',0);
			$info['name'] = $this->_post('name','trim','');
			if($info['name'] == ''){
				alert('error','请填写社保项目名称',U(''));
			}
			$info['insurance_type'] = $this->_post('insurance_type','intval',0);
			if($info['insurance_type'] == 0){
				alert('error','请选择社保项目类型',U(''));
			}
			if(D('Insurance')->editInsuranceItem($info)){
				alert('success','修改社保项目成功',U('hrm/insurance/insuranceitem'));
			}else{
				alert('error','修改社保项目失败',U('hrm/insurance/insuranceitem'));
			}
		}else{
			$insurance_item_id = $this->_get('id','intval',0);
			$info = D('Insurance')->getInsuranceItemInfo($insurance_item_id);
			if(!$info){
				alert('error','数据不存在',U('hrm/insurance/insuranceitem'));
			}
			$this->assign('info', $info);
			$this->assign('alert', parseAlert());
			$this->display();
		}
	}
	public function deleteInsuranceItem(){
		if($this->isPost()){
			$where['insurance_item_id'] = array('in',$this->_post('insurance_item_id'));
		}else{
			$where['insurance_item_id'] = $this->_get('id','intval',0);
		}
		if(M('insuranceItem')->where($where)->delete()){
			alert('success','删除社保项目成功',U('hrm/insurance/insuranceitem'));
		}else{
			alert('error','删除社保项目失败',U('hrm/insurance/insuranceitem'));
		}
	}
	public function insuranceSuit(){
		$p = $this->_get('p','intval',1);
		$insurancesuitpagelist = D('Insurance')->getPageInsuranceSuit($p,array());
		$this->assign('list', $insurancesuitpagelist['list']);
		$this->assign('page', $insurancesuitpagelist['page']);
		$this->assign('alert', parseAlert());
		$this->display();
	}
	public function addInsuranceSuit(){
		if($this->isPost()){
			$info['name'] = $this->_post('name','trim','');
			if($info['name'] == ''){
				alert('error','请填写社保套帐名称',U(''));
			}
			$item = $this->_post('item');
			$base = $this->_post('base');
			$paybycom = $this->_post('paybycom');
			$paybycomtype = $this->_post('paybycomtype');
			$paybyper = $this->_post('paybyper');
			$paybypertype = $this->_post('paybypertype');
			$itemArr = array();
			$items = array();
			foreach($item as $key=>$val){
				if($val && !in_array($val,$itemArr)){
					$itemArr[] = $val;
					$items[] = array(
						'item' => $item[$key],
						'base' => $base[$key],
						'paybycom' => $paybycom[$key],
						'paybycomtype' => $paybycomtype[$key],
						'paybyper' => $paybyper[$key],
						'paybypertype' => $paybypertype[$key]
					);
				}
			}
			if($items == array()){
				alert('error','请选择社保项目',U(''));
			}
			$info['items'] = serialize($items);
			$info['create_user_id'] = session('user_id');
			$info['create_time'] = time();
			if(D('Insurance')->addInsuranceSuit($info)){
				alert('success','添加社保套帐成功',U('hrm/insurance/insurancesuit'));
			}else{
				alert('error','添加社保套帐失败',U(''));
			}
		}else{
			$this->assign('insuranceitem', D('Insurance')->getItemAll());
			$this->assign('alert', parseAlert());
			$this->display();
		}
	}
	public function editInsuranceSuit(){
		if($this->isPost()){
			$info['insurance_suit_id'] = $this->_post('insurance_suit_id','intval',0);
			$info['name'] = $this->_post('name','trim','');
			if($info['name'] == ''){
				alert('error','请填写社保套帐名称',U(''));
			}
			$item = $this->_post('item');
			$base = $this->_post('base');
			$paybycom = $this->_post('paybycom');
			$paybycomtype = $this->_post('paybycomtype');
			$paybyper = $this->_post('paybyper');
			$paybypertype = $this->_post('paybypertype');
			$itemArr = array();
			$items = array();
			foreach($item as $key=>$val){
				if($val && !in_array($val,$itemArr)){
					$itemArr[] = $val;
					$items[] = array(
						'item' => $item[$key],
						'base' => $base[$key],
						'paybycom' => $paybycom[$key],
						'paybycomtype' => $paybycomtype[$key],
						'paybyper' => $paybyper[$key],
						'paybypertype' => $paybypertype[$key]
					);
				}
			}
			if($items == array()){
				alert('error','请选择社保项目',U(''));
			}
			$info['items'] = serialize($items);
			$info['update_time'] = time();
			if(D('Insurance')->editInsuranceSuit($info)){
				alert('success','编辑社保套帐成功',U('hrm/insurance/insurancesuit'));
			}else{
				alert('error','数据无变化，编辑社保套帐失败',U('hrm/insurance/insurancesuit'));
			}
		}else{
			$insurance_suit_id = $this->_get('id','intval',0);
			$info = D('Insurance')->getSuitInfo($insurance_suit_id);
			if(!$info){
				alert('error','数据不存在',U('hrm/insurance/insurancesuit'));
			}
			$this->assign('info', $info);
			$this->assign('insuranceitem', D('Insurance')->getItemAll());
			$this->assign('alert', parseAlert());
			$this->display();
		}
	}
	public function viewinsurancesuit(){
		$insurance_suit_id = $this->_get('id','intval',0);
		$info = D('Insurance')->getSuitInfo($insurance_suit_id);
		if(!$info){
			alert('error','数据不存在',U('hrm/insurance/insurancesuit'));
		}
		$this->assign('info', $info);
		$this->assign('insuranceitem', D('Insurance')->getItemAll());
		$this->assign('alert', parseAlert());
		$this->display();
	}
	public function deleteinsurancesuit(){
		if($this->isPost()){
			$where['insurance_suit_id'] = array('in',$this->_post('insurance_suit_id'));
		}else{
			$where['insurance_suit_id'] = $this->_get('id','intval',0);
		}
		if(M('insuranceSuit')->where($where)->delete()){
			alert('success','删除成功',U('hrm/insurance/insurancesuit'));
		}else{
			alert('error','删除失败',U('hrm/insurance/insurancesuit'));
		}
	}
	public function index(){
		$p = $this->_get('p','intval',1);
		$user_id = $this->_get('user_id','intval',0);
		if($user_id){$where['user_id'] = $user_id;}
		$tbno = $this->_get('tbno','trim','');
		if($tbno){$where['tbno'] = $tbno;}
		$insurancepagelist = D('Insurance')->getPageInsurance($p,$where);
		$this->assign('list', $insurancepagelist['list']);
		$this->assign('page', $insurancepagelist['page']);
		$this->assign('alert', parseAlert());
		$this->display();
	}
	public function add(){
		if($this->isPost()){
			$info['user_id'] = $this->_post('user_id','intval',0);
			if($info['user_id'] == 0){
				alert('error','请选择员工',U(''));
			}
			$info['id_num'] = $this->_post('id_num','trim','');
			if($info['id_num'] ==''){
				alert('error','请填写员工身份证号',U(''));
			}
			$info['tbno'] = $this->_post('tbno','trim','');
			if($info['tbno'] ==''){
				alert('error','请填写员工社保号',U(''));
			}
			$info['tbtime'] = $this->_post('tbtime','strtotime',0);
			if($info['tbtime'] ==0){
				alert('error','请填写员工参保日期',U(''));
			}
			$info['accounts_status'] = $this->_post('accounts_status','intval',0);
			if($info['accounts_status'] ==0){
				alert('error','请选择员工户口类型',U(''));
			}
			$info['ygsf'] = $this->_post('ygsf','intval',0);
			if($info['ygsf'] ==0){
				alert('error','请选择员工身份',U(''));
			}
			$info['suit_id'] = $this->_post('suit_id','intval',0);
			if($info['suit_id'] ==0){
				alert('error','请选择社保套帐',U(''));
			}
			$info['content'] = $this->_post('content','trim','');
			$info['create_user_id'] = session('user_id');
			$info['create_time'] = time();
			if(D('Insurance')->addInsurance($info)){
				alert('success','添加社保信息成功',U('hrm/insurance/index'));
			}else{
				alert('error','添加社保信息失败',U('hrm/insurance/index'));
			}
		}else{
			$this->assign('insurancesuit', D('Insurance')->getSuitAll());
			$this->assign('alert', parseAlert());
			$this->display();
		}
	}
	public function edit(){
		if($this->isPost()){
			$info['insurance_id'] = $this->_post('insurance_id','intval',0);
			$info['user_id'] = $this->_post('user_id','intval',0);
			if($info['user_id'] == 0){
				alert('error','请选择员工',U(''));
			}
			$info['id_num'] = $this->_post('id_num','trim','');
			if($info['id_num'] ==''){
				alert('error','请填写员工身份证号',U(''));
			}
			$info['tbno'] = $this->_post('tbno','trim','');
			if($info['tbno'] ==''){
				alert('error','请填写员工社保号',U(''));
			}
			$info['tbtime'] = $this->_post('tbtime','strtotime',0);
			if($info['tbtime'] ==0){
				alert('error','请填写员工参保日期',U(''));
			}
			$info['accounts_status'] = $this->_post('accounts_status','intval',0);
			if($info['accounts_status'] ==0){
				alert('error','请选择员工户口类型',U(''));
			}
			$info['ygsf'] = $this->_post('ygsf','intval',0);
			if($info['ygsf'] ==0){
				alert('error','请选择员工身份',U(''));
			}
			$info['suit_id'] = $this->_post('suit_id','intval',0);
			if($info['suit_id'] ==0){
				alert('error','请选择社保套帐',U(''));
			}
			$info['content'] = $this->_post('content','trim','');
			$info['update_time'] = time();
			if(D('Insurance')->editInsurance($info)){
				alert('success','编辑社保信息成功',U('hrm/insurance/index'));
			}else{
				alert('error','数据无变化，编辑社保信息失败',U('hrm/insurance/index'));
			}
		}else{
			$insurance_id = $this->_get('id','intval',0);
			$info = D('Insurance')->getInsuranceInfo($insurance_id);
			if(!$info){
				alert('error','信息不存在',U('hrm/insurance/index'));
			}
			$this->assign('info', $info);
			$this->assign('suitinfo', D('Insurance')->getSuitInfo($info['suit_id']));
			$this->assign('insurancesuit', D('Insurance')->getSuitAll());
			$this->assign('alert', parseAlert());
			$this->display();
		}
	}
	public function view(){
		$insurance_id = $this->_get('id','intval',0);
		$info = D('Insurance')->getInsuranceInfo($insurance_id);
		if(!$info){
			alert('error','数据不存在',U('hrm/insurance/index'));
		}
		$this->assign('info', $info);
		$this->assign('suitinfo', D('Insurance')->getSuitInfo($info['suit_id']));
		$this->assign('insurancesuit', D('Insurance')->getSuitAll());
		$this->assign('alert', parseAlert());
		$this->display();
	}
	public function delete(){
		if($this->isPost()){
			$insurance_id = $this->_post('insurance_id');
			$where['insurance_id'] = array('in',$insurance_id);
		}else{
			$insurance_id = $this->_get('id','intval',0);
			$where['insurance_id'] = $insurance_id;
		}
		if(D('Insurance')->where($where)->delete()){
			alert('success','删除成功',U('hrm/insurance/index'));
		}else{
			alert('error','删除失败',U('hrm/insurance/index'));
		}
	}
}