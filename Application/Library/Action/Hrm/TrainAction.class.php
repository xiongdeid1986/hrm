<?php
class TrainAction extends Action {
	public function _initialize(){
		$action = array(
			'users'=>array(),
			'anonymous'=>array()
		);
		B('Authenticate', $action);
	}
	
	public function index(){
		$p = $this->_get('p','intval',1);
		$trainpagelist = D('Train')->getPageTrain($p,array());
		$this->assign('trainlist', $trainpagelist['trainlist']);
		$this->assign('page', $trainpagelist['page']);
		$this->alert = parseAlert();
		$this->display();
	}
	public function add(){
		if($this->isPost()){
			$info['name'] = $this->_post('name','trim','');
			if($info['name'] == ''){
				alert('error','请填写项目名称',U(''));
			}
			$info['train_type'] = $this->_post('train_type','intval',0);
			if($info['train_type'] == 0){
				alert('error','请选择项目类型',U(''));
			}
			$info['organizers'] = $this->_post('organizers','trim','');
			if($info['organizers'] == ''){
				alert('error','请填写主办单位',U(''));
			}
			$info['owner_user_id'] = $this->_post('to_user_id','intval',0);
			if($info['owner_user_id'] == 0){
				alert('error','请选择负责人',U(''));
			}
			$info['org'] = $this->_post('org','trim','');
			$info['address'] = $this->_post('address','trim','');
			$info['start_time'] = $this->_post('start_time','strtotime',0);
			$info['end_time'] = $this->_post('end_time','strtotime',0);
			$info['day'] = $this->_post('day','intval',0);
			$info['money'] = $this->_post('money','intval',0);
			$info['content'] = $this->_post('content','trim','');
			$info['create_user_id'] = session('user_id');
			$info['create_time'] = time();
			if(D('Train')->addTrain($info)){
				alert('success','添加成功',U('hrm/train/index'));
			}else{
				alert('error','添加失败，请联系管理员',U(''));
			}
		}else{
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function edit(){
		if($this->isPost()){
			$info['train_id'] = $this->_post('train_id','intval',0);
			$info['name'] = $this->_post('name','trim','');
			if($info['name'] == ''){
				alert('error','请填写项目名称',U('hrm/train/edit','id='.$info['train_id']));
			}
			$info['train_type'] = $this->_post('train_type','intval',0);
			if($info['train_type'] == 0){
				alert('error','请选择项目类型',U(''));
			}
			$info['organizers'] = $this->_post('organizers','trim','');
			if($info['organizers'] == ''){
				alert('error','请填写主办单位',U(''));
			}
			$info['owner_user_id'] = $this->_post('to_user_id','intval',0);
			if($info['owner_user_id'] == 0){
				alert('error','请选择负责人',U(''));
			}
			$info['org'] = $this->_post('org','trim','');
			$info['address'] = $this->_post('address','trim','');
			$info['start_time'] = $this->_post('start_time','strtotime',0);
			$info['end_time'] = $this->_post('end_time','strtotime',0);
			$info['day'] = $this->_post('day','intval',0);
			$info['money'] = $this->_post('money','intval',0);
			$info['content'] = $this->_post('content','trim','');
			if(D('Train')->editTrain($info)){
				alert('success','修改成功',U('hrm/train/index'));
			}else{
				alert('error','数据无变化，修改失败',U(''));
			}
		}else{
			$train_id = $this->_get('id','intval',0);
			$info = D('Train')->getTrainInfo($train_id);
			if(!$info){
				alert('error','信息不存在',U('hrm/train/index'));
			}
			$this->assign('info', $info);
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function view(){
		$train_id = $this->_get('id','intval',0);
		$info = D('Train')->getTrainInfo($train_id);
		if(!$info){
			alert('error','信息不存在',U('hrm/train/index'));
		}
		$this->assign('info', $info);
		$this->alert = parseAlert();
		$this->display();
	}
	public function delete(){
		if($this->isPost()){
			$where['train_id'] = array('in',$this->_post('train_id'));
		}else{
			$where['train_id'] = $this->_get('id','intval',0);
		}
		if(M('train')->where($where)->delete()){
			alert('success','删除成功',U('hrm/train/index'));
		}else{
			alert('error','删除失败',U('hrm/train/index'));
		}
	}
	public function addtrainpro(){
		if($this->isPost()){
			$info['name'] = $this->_post('name','trim','');
			if($info['name'] == ''){
				alert('error','请填写项目名称',U(''));
			}
			$info['train_type'] = $this->_post('train_type','intval',0);
			if($info['train_type'] == 0){
				alert('error','请选择项目类型',U(''));
			}
			$info['train_status'] = $this->_post('train_status','intval',0);
			if($info['train_status'] == 0){
				alert('error','请选择培训状态',-1);
			}
			$info['organizers'] = $this->_post('organizers','trim','');
			if($info['organizers'] == ''){
				alert('error','请填写主办单位',U(''));
			}
			$info['owner_user_id'] = $this->_post('to_user_id','intval',0);
			if($info['owner_user_id'] == 0){
				alert('error','请选择负责人',U(''));
			}
			$info['org'] = $this->_post('org','trim','');
			$info['address'] = $this->_post('address','trim','');
			$info['start_time'] = $this->_post('start_time','strtotime',0);
			$info['end_time'] = $this->_post('end_time','strtotime',0);
			$info['day'] = $this->_post('day','intval',0);
			$info['money'] = $this->_post('money','intval',0);
			$info['class_num'] = $this->_post('class_num','intval',0);
			$info['user_num'] = $this->_post('user_num','intval',0);
			$info['train_status'] = $this->_post('train_status','intval',0);
			$info['total_value'] = $this->_post('total_value','intval',0);
			$info['total_value_txt'] = $this->_post('total_value_txt','trim','');
			$info['content'] = $this->_post('content','trim','');
			$info['create_user_id'] = session('user_id');
			$info['create_time'] = time();
			if(D('Train')->addTrainPro($info)){
				if($train_id = $this->_post('train_id','intval',0)){
					D('Train')->editTrain(array('train_id'=>$train_id,'change'=>1));
					alert('success','转换成功',U('hrm/train/trainpro'));
				}else{
					alert('success','添加成功',U('hrm/train/trainpro'));
				}
			}else{
				alert('error','添加失败，请联系管理员',U(''));
			}
		}else{
			if($train_id = $this->_get('train_id','intval',0)){
				$this->assign('info', D('Train')->getTrainInfo($train_id));
			}
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function trainPro(){
		$p = $this->_get('p','intval',1);
		$trainpropagelist = D('Train')->getPageTrainPro($p,array());
		$this->assign('trainprolist', $trainpropagelist['trainprolist']);
		$this->assign('page', $trainpropagelist['page']);
		$this->alert = parseAlert();
		$this->display();
	}
	public function editTrainPro(){
		if($this->isPost()){
			$info['train_pro_id'] = $this->_post('train_pro_id','intval',0);
			$info['name'] = $this->_post('name','trim','');
			if($info['name'] == ''){
				alert('error','请填写项目名称',U(''));
			}
			$info['train_type'] = $this->_post('train_type','intval',0);
			if($info['train_type'] == 0){
				alert('error','请选择项目类型',U(''));
			}
			$info['train_status'] = $this->_post('train_status','intval',0);
			if($info['train_status'] == 0){
				alert('error','请选择培训状态',-1);
			}
			$info['organizers'] = $this->_post('organizers','trim','');
			if($info['organizers'] == ''){
				alert('error','请填写主办单位',U(''));
			}
			$info['owner_user_id'] = $this->_post('to_user_id','intval',0);
			if($info['owner_user_id'] == 0){
				alert('error','请选择负责人',U(''));
			}
			$info['org'] = $this->_post('org','trim','');
			$info['address'] = $this->_post('address','trim','');
			$info['start_time'] = $this->_post('start_time','strtotime',0);
			$info['end_time'] = $this->_post('end_time','strtotime',0);
			$info['day'] = $this->_post('day','intval',0);
			$info['money'] = $this->_post('money','intval',0);
			$info['class_num'] = $this->_post('class_num','intval',0);
			$info['user_num'] = $this->_post('user_num','intval',0);
			$info['train_status'] = $this->_post('train_status','intval',0);
			$info['total_value'] = $this->_post('total_value','intval',0);
			$info['total_value_txt'] = $this->_post('total_value_txt','trim','');
			$info['content'] = $this->_post('content','trim','');
			if(D('Train')->editTrainPro($info)){
				alert('success','修改成功',U('hrm/train/trainPro'));
			}else{
				alert('error','数据无变化，修改失败',U(''));
			}
		}else{
			$train_pro_id = $this->_get('id','intval',0);
			$info = D('Train')->getTrainProInfo($train_pro_id);
			if(!$info){
				alert('error','信息不存在',U('hrm/train/trainPro'));
			}
			$this->assign('info', $info);
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function viewTrainPro(){
		$train_pro_id = $this->_get('id','intval',0);
		$info = D('Train')->getTrainProInfo($train_pro_id);
		if(!$info){
			alert('error','信息不存在',U('hrm/train/trainPro'));
		}
		$this->assign('info', $info);
		$this->alert = parseAlert();
		$this->display();
	}
	public function deleteTrainPro(){
		if($this->isPost()){
			$where['train_pro_id'] = array('in',$this->_post('train_pro_id'));
		}else{
			$where['train_pro_id'] = $this->_get('id','intval',0);
		}
		if(M('trainPro')->where($where)->delete()){
			alert('success','删除成功',U('hrm/train/trainpro'));
		}else{
			alert('error','删除失败',U('hrm/train/trainpro'));
		}
	}
}