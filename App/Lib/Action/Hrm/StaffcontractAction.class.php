<?php
class StaffcontractAction extends Action {
    public function _initialize(){
		$action = array(
			'users'=>array(),
			'anonymous'=>array()
		);
		B('Authenticate', $action);
	}
	public function index(){
		$p = $this->_get('p','intval',1);
		$where = array();
		if($user_id = $this->_get('user_id','intval',0)){
			$where['user_id'] = $user_id;
		}
		$contractpagelist = D('StaffContract')->getPageConcract($p,$where);
		$this->assign('contractlist', $contractpagelist['contractlist']);
		$this->assign('page', $contractpagelist['page']);
		$this->alert = parseAlert();
		$this->display();
	}
	public function add(){
		if($this->isPost()){
			$info['user_id'] = $this->_post('to_user_id','intval',0);
			if($info['user_id'] == 0){
				alert('error','请选择员工',U('hrm/staffcontract/add'));
			}
			$info['number'] = $this->_post('number','trim','');
			$info['number'] = $info['number']?$info['number']:'5khrm'.date('Ymd').rand(1000,9999);

			$info['name'] = $this->_post('name','trim','');
			if($info['name'] == ''){
				alert('error','请填写合同名称',U('hrm/staffcontract/add'));
			}
			$info['type'] = $this->_post('type','intval',1);
			$info['status'] = $this->_post('status','intval',1);
			$info['time_type'] = $this->_post('time_type','intval',1);
			$info['start_time'] = $this->_post('start_time','strtotime',time());
			$info['end_time'] = $this->_post('end_time','strtotime',0);
			if($info['time_type'] == 1 && $info['end_time'] == 0){
				alert('error','请选择合同截止日期',U('hrm/staffcontract/add'));
			}
			$info['content'] = $this->_post('content','trim','');
			$info['create_time'] = time();
			$info['create_user_id'] = session('user_id');
			if($concract_id = D('StaffContract')->addConcract($info)){
				if(array_sum($_FILES['file']['size'])){
					import('@.ORG.UploadFile');
					$upload = new UploadFile();
					$upload->maxSize = 3292200;
					$upload->allowExts  = explode(',',C('defaultinfo.allow_file_type'));
					$dirname = './uploads/' . date('Ym').'/'.date('d').'/';
					if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
						alert('error',"附件上传目录不可写!,附件上传失败。");
					}else{
						$upload->savePath = $dirname;
						if(!$upload->upload()) {
							alert('error',$upload->getErrorMsg());
						}else{
							$info =  $upload->getUploadFileInfo();
							D('File')->addFile($info,$concract_id,'staffcontract');
							alert('success','已成功上传'.count($info).'个附件');
						}
					}
				}
				alert('success','添加员工合同成功',U('hrm/staffcontract/index'));
			}else{
				alert('error','添加员工合同失败',U('hrm/staffcontract/add'));
			}
		}else{
			$concract_id = $this->_get('id','intval',0);
			if($info = D('StaffContract')->getConcractInfo($concract_id)){
				$this->assign('info', $info);
			}
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function edit(){
		if($this->isPost()){
			$info['staffcontract_id'] = $this->_post('contract_id','intval',0);
			if(!D('StaffContract')->getConcractInfo($info['staffcontract_id'])){
				alert('error','信息错误',U('hrm/staffcontract/index'));
			}
			$info['user_id'] = $this->_post('to_user_id','intval',0);
			if($info['user_id'] == 0){
				alert('error','请选择员工',U('hrm/staffcontract/edit','id='.$info['staffcontract_id']));
			}
			$info['number'] = $this->_post('number','trim','');
			$info['number'] = $info['number']?$info['number']:'5khrm'.date('Ymd').rand(1000,9999);

			$info['name'] = $this->_post('name','trim','');
			if($info['name'] == ''){
				alert('error','请填写合同名称',U('hrm/staffcontract/edit','id='.$info['staffcontract_id']));
			}
			$info['type'] = $this->_post('type','intval',1);
			$info['status'] = $this->_post('status','intval',1);
			$info['time_type'] = $this->_post('time_type','intval',1);
			$info['start_time'] = $this->_post('start_time','strtotime',time());
			$info['end_time'] = $this->_post('end_time','strtotime',0);
			if($info['time_type'] == 1 && $info['end_time'] == 0){
				alert('error','请选择合同截止日期',U('hrm/staffcontract/edit','id='.$info['staffcontract_id']));
			}
			$info['status'] = ($info['end_time'] == 0 || $info['end_time'] > time()) ? $info['status'] :  3;
			$info['content'] = $this->_post('content','trim','');
			$info['update_time'] = time();
			if(array_sum($_FILES['file']['size'])){
				import('@.ORG.UploadFile');
				$upload = new UploadFile();
				$upload->maxSize = 3292200;
				$upload->allowExts  = explode(',',C('defaultinfo.allow_file_type'));
				$dirname = './uploads/' . date('Ym').'/'.date('d').'/';
				if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
					alert('error',"附件上传目录不可写!,附件上传失败。");
				}else{
					$upload->savePath = $dirname;
					if(!$upload->upload()) {
						alert('error',$upload->getErrorMsg());
					}else{
						$file_info =  $upload->getUploadFileInfo();
						D('File')->addFile($file_info,$info['staffcontract_id'],'staffcontract');
						alert('success','已成功上传'.count($file_info).'个附件');
						$return = true;
					}
				}
			}
			if(D('StaffContract')->editConcract($info) || $return){
				alert('success','编辑员工合同成功',U('hrm/staffcontract/index'));
			}else{
				alert('error','员工合同信息无变化，修改失败',U('hrm/staffcontract/edit','id='.$info['staffcontract_id']));
			}
		}else{
			$contract_id = $this->_get('id','intval',0);
			if(!$info = D('StaffContract')->getConcractInfo($contract_id)){
				alert('error','合同不存在',U('hrm/staffcontract/index'));
			}
			if($info['end_time'] != 0 && $info['end_time'] < time()){
				$info['status'] = $data['status'] = 3;
				$data['staffcontract_id'] = $contract_id;
				D('StaffContract')->editConcract($data);
			}
			$this->assign('info', $info);
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function delete(){
		if($this->isPost()){
			$contract_ids = $this->_post('staffcontract_id');
			if($num = D('StaffContract')->deleteConcract($contract_ids)){
				alert('success','已删除'.$num.'个合同',U('hrm/staffcontract/index'));
			}else{
				alert('error','删除失败',U('hrm/staffcontract/index'));
			}
		}else{
			$contract_id = $this->_get('id','intval',0);
			if($num = D('StaffContract')->deleteConcract($contract_id)){
				alert('success','已删除'.$num.'个合同',U('hrm/staffcontract/index'));
			}else{
				alert('error','删除失败',U('hrm/staffcontract/index'));
			}
		}
	}
	public function view(){
		$contract_id = $this->_get('id','intval',0);
		if(!$info = D('StaffContract')->getConcractInfo($contract_id)){
			alert('error','合同不存在',U('hrm/staffcontract/index'));
		}
		if($info['end_time'] != 0 && $info['end_time'] < time()){
			$info['status'] = $data['status'] = 3;
			$data['staffcontract_id'] = $contract_id;
			D('StaffContract')->editConcract($data);
		}
		$this->assign('info', $info);
		$this->alert = parseAlert();
		$this->display();
	}
	public function filedelete(){
		$contract_ids = $this->_get('contract_id','intval',0);
		$file_id = $this->_get('id','intval',0);
		$return = D('File')->deteleFile($file_id,$contract_ids,'staffcontract');
		$this->ajaxReturn(array($return));
	}
}