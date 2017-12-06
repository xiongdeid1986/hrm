<?php
	class SettingAction extends Action{
		/*
			users	登录后无权限可见
			anonymous 匿名无权限也可见
		*/
		public function _initialize(){
			$actions = array(
				'users'=>array(''),
				'anonymous'=>array('')
			);
			B('Authenticate', $actions);
		}
		
		public function smtp(){
			if ($this->isAjax()) {
				if($this->_post('address','trim')){
					if (ereg('^([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+.[a-zA-Z]{2,3}$',$_POST['address'])){
						if (ereg('^([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+.[a-zA-Z]{2,3}$',$_POST['test_email'])){
							$smtp = array('MAIL_ADDRESS'=>$this->_post('address','trim'),'MAIL_SMTP'=>$this->_post('smtp','trim'),'MAIL_LOGINNAME'=>$this->_post('loginName','trim'),'MAIL_PASSWORD'=>$this->_post('password','trim'),'MAIL_CHARSET'=>'UTF-8','MAIL_AUTH'=>true,'MAIL_HTML'=>true);
							C($smtp,'smtp');
							import('@.ORG.Mail');
							$content ='这是一封悟空HRM系统自动生成测试邮件，如果你成功收到，证明悟空HRM的SMTP设置成功！请勿回复';
							if(SendMail($_POST['test_email'],'悟空HRM邮箱测试',$content,'悟空HRM管理员')){
								$message = '发送成功！';
							} else {
								$message = '发送失败，信息错误！';
							}
						} else {
							$message = '测试收件箱格式错误!';
						}
					} else {
						$message = '邮箱格式错误！';
					}
					$this->ajaxReturn("", $message, 1);
				}else{
					if($this->_post('uid','trim') && $this->_post('passwd','trim') && $this->_post('phone','trim')){
						$result = sendtestSMS($this->_post('uid','trim'), $this->_post('passwd','trim'), $this->_post('phone','trim'));
						if(strstr($this->_post('uid','trim'), 'BST') === false){
							$message = '账号名格式错误!';
						}elseif($result == 0 || $result == 1){
							$message = '发送成功,请先保存设置，短信如有延迟，请稍候确认！';
						}elseif($result == -1 ){
							$message = '账号未注册，请联系悟空HRM客服!';
						}elseif($result == -3 ){
							$message = '密码错误!';
						}else{
							$message = '发送失败，请确认短信接口信息!';
						}
					}else{
						$message = '发送失败，请确认短信接口信息!';
					}
					$this->ajaxReturn("", $message, 1);
				}
			} elseif($this->isPost()) {
				$edit = false;
				$m_config = M('Config');
				if($_POST['address']){
					if(is_email($_POST['address'])){
						$smtp = array('MAIL_ADDRESS'=>$this->_post('address','trim'),'MAIL_SMTP'=>$this->_post('smtp','trim'),'MAIL_LOGINNAME'=>$this->_post('loginName','trim'),'MAIL_PASSWORD'=>$this->_post('password','trim'),'MAIL_CHARSET'=>'UTF-8','MAIL_AUTH'=>true,'MAIL_HTML'=>true);
						if(D('Config')->setConfig('smtp',$smtp)){
							F('smtp',$smtp);
							$edit = true;
						} else {
							alert('error','添加失败，请联系管理员！',U('setting/smtp'));
						}
					}else{
						alert('error','邮箱格式错误！',U('setting/smtp'));
					}
				}
				
				if($_POST['uid']){
					if(strstr(trim($_POST['uid']), 'BST') === false)	$message = '账号名格式错误!';
					$sms = array('uid'=>$this->_post('uid','trim'),'passwd'=>$this->_post('passwd','trim'),'sign_name'=>$this->_post('sign_name','trim'),'sign_sysname'=>$this->_post('sign_sysname','trim'));
					if(D('Config')->setConfig('sms',$sms)){
						F('sms',$sms);
						$edit = true;
					} else {
						alert('error','添加失败，请联系管理员！',U('setting/smtp'));
					}
				}
				if($edit){
					alert('success','设置成功并保存！',U('setting/smtp'));
				}else{
					alert('error','数据无变化',U('setting/smtp'));
				}
			} else {
				$this->smtp = D('Config')->getConfig('smtp');
				$this->sms = D('Config')->getConfig('sms');
				$this->alert = parseAlert();
				$this->display();			
			}
		}

		public function defaultinfo(){
			if($this->isPost()){
				$defaultinfo = $this->_Post();
				if (isset($_FILES['logo']['size']) && $_FILES['logo']['size'] > 0) {
					import('@.ORG.UploadFile');
					$upload = new UploadFile();
					$upload->maxSize = 20000000;
					$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');
					$dirname = './Uploads/' . date('Ym', time()).'/'.date('d', time()).'/';
					if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
						alert('error',"附件上传目录不可写!",U('core/setting/defaultinfo'));
					}
					$upload->savePath = $dirname;
					if(!$upload->upload()) {
						alert('error',$upload->getErrorMsg(),U('core/setting/defaultinfo'));
					}else{
						$info =  $upload->getUploadFileInfo();
					}
					if(is_array($info[0]) && !empty($info[0])){
						$defaultinfo['logo'] = './Uploads/' . date('Ym', time()).'/'.date('d', time()).'/' . $info[0]['savename'];;
					}else{
						alert('error','Logo保存失败',U('core/setting/defaultinfo'));
					}
				}else{
					$defaultinfo['logo'] = C('defaultinfo.logo') ? C('defaultinfo.logo') : './Public/img/logo.gif';
				}
				if(D('Config')->setConfig('defaultinfo',$defaultinfo)){
					F('defaultinfo',D('Config')->getConfig('defaultinfo'));	
					alert('success','保存系统信息',U('core/setting/defaultinfo'));
				}else{
					alert('error','数据无变化',U('core/setting/defaultinfo'));
				}
			}else{
				$defaultinfo = D('Config')->getConfig('defaultinfo');
				$this->assign('defaultinfo',$defaultinfo);
				$this->alert = parseAlert();
				$this->display();
			}
		}
		public function contractType(){
			if($this->isPost()){
				$contracttype = $this->_Post('name');
				ksort($contracttype);
				$i = 1;
				foreach($contracttype as $type){
					if($type){
						$type_arr[$i] = $type;
						$i++;
					}
				}
				if(D('Config')->setConfig('contracttype',$type_arr)){
					F('contracttype',D('Config')->getConfig('contracttype'));	
					alert('success','设置成功并保存！',U('core/setting/contracttype'));
				}else{
					alert('error','数据无变化',U('core/setting/contracttype'));
				}
			}else{
				$contracttype = D('Config')->getConfig('contracttype');
				$this->assign('contracttype',$contracttype);
				$this->alert = parseAlert();
				$this->display();
			}
		}
		public function contractStatus(){
			if($this->isPost()){
				$contractstatus = $this->_Post('name');
				ksort($contractstatus);
				$i = 1;
				foreach($contractstatus as $status){
					if($status){
						$status_arr[$i] = $status;
						$i++;
					}
				}
				if(D('Config')->setConfig('contractstatus',$status_arr)){
					F('contractstatus',D('Config')->getConfig('contractstatus'));	
					alert('success','设置成功并保存！',U('core/setting/contractstatus'));
				}else{
					alert('error','数据无变化',U('core/setting/contractstatus'));
				}
			}else{
				$contractstatus = D('Config')->getConfig('contractstatus');
				$this->assign('contractstatus',$contractstatus);
				$this->alert = parseAlert();
				$this->display();
			}
		}
	}