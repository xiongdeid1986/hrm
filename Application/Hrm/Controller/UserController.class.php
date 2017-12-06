<?php
namespace Hrm\Controller;
use Think\Controller;

class UserController extends Controller {
	/*
		users	登录后无权限可见
		anonymous 匿名无权限也可见
	*/
    public function _initialize(){
		$actions = array(
			'users'=>array('logout','getuserindex','getuserrindex','getsubuserdialog','getsubusercbdialog','getdepartmentuser','getpositionuser'),
			'anonymous'=>array('login','active')
		);
		B('Authenticate', $actions);
	}
	public function index(){
		$p = $this->_get('p','intval',1);
		$status = $this->_get('status','trim','1');
		$d_user = D('User');
		$userpagelist = $d_user->getUserPageList($p,$status);
		$this->assign('userlist', $userpagelist['userlist']);
		$this->assign('page', $userpagelist['page']);
		$this->assign('status_array',array('1'=>'在职','2'=>'离职','3'=>'退休','0'=>'未激活'));
		$this->assign('status', $status);
		$this->alert = parseAlert();
		$this->display();
	}
	
    public function login(){
        if (session('?name')){
			$this->redirect('core/index/index',array(), 0, '');
		}elseif($this->isPost()){
            $name     = $this->_post('name','trim','');
            $password = $this->_post('password','trim','');
			if(!$name || !$password){
				alert('error', '请正确输入用户名和密码！',U('core/user/login')); 
			}else{
                $d_user = D('User');
                $user = $d_user->getUserInfo(array('name'=>$name));
                if ($user && $user['password'] == md5(md5($password) . $user['salt'])) {			
                    if (-1 == $user['status']) {
                        alert('error', '您的账号未通过审核，请联系管理员！',U('core/user/login'));
                    } elseif (0 == $user['status']) {
                        alert('error', '您的账号正在审核中，请耐心等待！',U('core/user/login'));
                    }else {
                        if ($_POST['autologin'] == 1) {
                            session(array('expire'=>259200));
                        } else {
                            session(array('expire'=>3600));
                        }
						
						if($user['category_id'] == 1){
							session('admin', 1);
						}
						session('position_id', $user['position_id']);
						session('control_ids', $user['control_ids']);
						session('department_id', $user['department_id']);
						session('name', $user['name']);
						session('user_id', $user['user_id']);
						alert('success', '登录成功', U('core/index/index'));
                    }
                } else {
                    alert('error', '用户名或密码错误！',U('core/user/login'));
                }
            }
		}else{
			$this->alert = parseAlert();
			$this->display();
		}
    }
	
	public function logout() {
		session(null);
		alert('success', '已经退出！',U('core/user/login'));
	}
	public function addUser(){
		$d_user = D('User');
		if($this->isPost()) {
			$user['name'] = $this->_post('name','tirm','');
			if($return_name = $d_user->checkUsername($user['name'],$user['user_id'])){
				switch($return_name){
					case -1:
						alert('error','请填写姓名',U('core/user/adduser'));
						break;
					case -2:
						alert('error','用户名重复',U('core/user/adduser'));
						break;
				}
			}
			$user['salt'] = D('User')->getSalt();
			if($_POST['radio_type'] == 'add'){
				$password = $this->_post('password','tirm','');
				if($password == ''){
					alert('error','请填写用户密码',U('core/user/adduser'));
				}
				$user['password'] = md5(md5($password) . $user['salt']);
				$user['status'] = 1;
			}else{
				$user['status'] = 0;
			}
			$user['salt'] = D('User')->getSalt();
			$user['category_id'] = $this->_post('category_id','intval',0);
			$user['position_id'] = $this->_post('position_id','intval',0);
			$d_structure = D('Structure');
			if(!($d_structure->getPositionInfo($user['position_id']))){
				alert('error','所选岗位不存在',U('core/user/adduser'));
			}
			$user['email'] = $this->_post('email','tirm','');
			if($return_email = $d_user->checkEmail($user['email'],$user['user_id'])){
				switch($return_email){
					case -1:
						alert('error','请填写用户邮箱',U('core/user/adduser'));
						break;
					case -2:
						alert('error','邮箱格式不正确',U('core/user/adduser'));
						break;
					case -3:
						alert('error','用户邮箱重复',U('core/user/adduser'));
						break;
				}
			}
			$user['sex'] = $this->_post('sex','intval',1);
			$user['telephone'] = $this->_post('telephone','trim','');
			$user['address'] = $this->_post('address','trim','');
			$user['type'] = $this->_post('type','intval',0);
			$user['reg_ip'] = get_client_ip();
			$time = time();
			$user['reg_time'] = $time;
			
			if($user_id = D('User')->addUser($user)){
				if($_POST['radio_type'] == 'email'){
					$verify_code = md5(md5($user['reg_time']) . $user['salt']);
					C(F('smtp'),'smtp');
					import('@.ORG.Mail');
					$url = U('user/active', array('user_id'=>$user_id, 'verify_code'=>$verify_code),'','',true);
					$content ='尊敬的' . $_POST['name'] . '：<br/><br/>您好！您的'.C('defaultinfo.name').'管理员已经给您发送了邀请，请查收！
						请点击下面的链接完成注册：<br/><br/>' . $url .'<br/><br/>如果以上链接无法点击，请将上面的地址复制到你的浏览器(如IE)的地址栏进入网站。<br/><br/>--'.C('defaultinfo.name').'(这是一封自动产生的email，请勿回复。)';
					if (SendMail($user['email'], '从'.C('defaultinfo.name').'添加用户邀请', $content,C('defaultinfo.name').'管理员')){
						alert('success', '添加成功，等待被邀请用户激活!', U('user/index'));
					} else {
						M('user')->where(array('user_id'=>$user['user_id']))->delete();
						alert('error', '无法发送邀请，请检查smtp设置信息!', U('core/setting/smtp'));
					}
				}else{
					alert('success', '添加成功，该用户已可以登录系统', U('core/user/index'));
				}
			}else{
				alert('error','添加失败，请联系管理员！',U('core/user/adduser'));
			}
		}else{
			$this->assign('type',array('0'=>'试用期','1'=>'正式工','2'=>'临时工'));
			$department_list = D('Structure')->getDepartmentList(0,'--',1);
			$this->assign('department_list', $department_list);
			$this->alert = parseAlert();
			$this->display();
		}
    }

	/*
	*邮箱邀请用户密码设置
	*/	
	public function active() {
		$verify_code = trim($_REQUEST['verify_code']);
		$user_id = intval($_REQUEST['user_id']);
		$m_user = M('User');
		$user = $m_user->where('user_id = %d', $user_id)->find();
		if (is_array($user) && !empty($user)) {
			if (md5(md5($user['reg_time']) . $user['salt']) == $verify_code) {
				if ($_REQUEST['password']) {
					$password = md5(md5(trim($_REQUEST["password"])) . $user['salt']);
					$m_user->where('user_id =' . $_REQUEST['user_id'])->save(array('password'=>$password,'status'=>1, 'reg_time'=>time(), 'reg_ip'=>get_client_ip()));
					alert('success', '设置密码成功，请登录', U('user/login'));
				} else {
					$this->alert = parseAlert();
					$this->display();
				}
			} else {
				$this->error('找回密码链接无效或链接已失效！');
			}
		} else {
			$this->error('找回密码链接无效或链接已失效！');
		}
	}
	public function edit(){
		$d_user = D('User');
		if ($this->isPost()) {
			$info['user_id'] = session('user_id');
			$info['work_status'] = intval($_POST['work_status']);
			$info['sex'] = intval($_POST['sex']);
			$info['telephone'] = trim($_POST['telephone']);
			$info['address'] = trim($_POST['address']);
			$info['email'] = $this->_post('email','tirm','');
			if($return_email = $d_user->checkEmail($info['email'],$info['user_id'])){
				switch($return_email){
					case -1:
						alert('error','请填写用户邮箱',U('core/user/edit'));
						break;
					case -2:
						alert('error','邮箱格式不正确',U('core/user/edit'));
						break;
					case -3:
						alert('error','用户邮箱重复',U('core/user/edit'));
						break;
				}
			}
			
			if(D('User')->editUserInfo($info)){
				alert('success', '员工信息修改成功！', U('core/user/edit'));
			}else{
				alert('error','员工信息无变化，修改失败！',U('core/user/edit'));
			}
		}else{
			$user_id = session('user_id');
			if($user_id == 0){
				alert('error','参数错误',U('core/user/edit'));
			}
			$user = $d_user->getUserInfo(array('user_id'=>$user_id));
			$position = D('Structure')->getPositionInfo($user['position_id']);
			$user['department_id'] = $position['department_id'];
			$department_list = D('Structure')->getDepartmentList();
			$this->assign('department_list', $department_list);
			$this->assign('position', $position);
			$this->assign('user',$user);
			$this->assign('status',array('0'=>'未激活','1'=>'在职','2'=>'离职','3'=>'退休'));
			$this->assign('type',array('0'=>'试用期','1'=>'正式工','2'=>'临时工'));
			$this->assign('work_status',array('0'=>'正常','1'=>'休假','2'=>'出差'));
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function editInfo(){
		$d_user = D('User');
		if ($this->isPost()) {
			$user['user_id'] = $this->_post('user_id','intval',session('user_id'));
			if(session('?admin')){
				$user['name'] = $this->_post('name','tirm','');
				if($return_name = $d_user->checkUsername($user['name'],$user['user_id'])){
					switch($return_name){
						case -1:
							alert('error','请填写姓名',U('core/user/editInfo','id='.$user['user_id']));
							break;
						case -2:
							alert('error','用户名重复',U('core/user/editInfo','id='.$user['user_id']));
							break;
					}
				}
				$user['category_id'] = $this->_post('category_id','intval',0);
				$user['position_id'] = $this->_post('position_id','intval',0);
				if(!D('Structure')->getPositionInfo($user['position_id'])){
					alert('error','所选岗位不存在',U('core/user/editInfo','id='.$user_id));
				}
			}
			$user['email'] = $this->_post('email','tirm','');
			if($return_email = $d_user->checkEmail($user['email'],$user['user_id'])){
				switch($return_email){
					case -1:
						alert('error','请填写用户邮箱',U('core/user/editInfo','id='.$user['user_id']));
						break;
					case -2:
						alert('error','邮箱格式不正确',U('core/user/editInfo','id='.$user['user_id']));
						break;
					case -3:
						alert('error','用户邮箱重复',U('core/user/editInfo','id='.$user['user_id']));
						break;
				}
			}
			$user['sex'] = $this->_post('sex','intval',1);
			$user['telephone'] = $this->_post('telephone','trim','');
			$user['address'] = $this->_post('address','trim','');
			if(D('User')->editUserInfo($user)){
				alert('success', '员工信息修改成功！', U('core/user/editInfo','id='.$user['user_id']));
			}else{
				alert('error','员工信息无变化，修改失败！',U('core/user/editInfo','id='.$user['user_id']));
			}
		}else{
			$user_id = $this->_get('id','intval',session('user_id'));
			if($user_id == 0){
				alert('error','参数错误',U('core/user/department'));
			}
			$user = $d_user->getUserInfo(array('user_id'=>$user_id));
			$position = D('Structure')->getPositionInfo($user['position_id']);
			$user['department_id'] = $position['department_id'];
			$position_list = D('Structure')->getDepartmentPosition($position['department_id']);
			$department_list = D('Structure')->getDepartmentList(0,'--');
			$this->assign('department_list', $department_list);
			$this->assign('position_list', $position_list);
			$this->assign('user',$user);
			$this->assign('status',array('0'=>'未激活','1'=>'在职','2'=>'离职','3'=>'退休'));
			$this->assign('type',array('0'=>'试用期','1'=>'正式工','2'=>'临时工'));
			$this->assign('work_status',array('0'=>'正常','1'=>'休假','2'=>'出差'));
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function editPassword(){
		if($this->isPost()){
			$old = $this->_post('old','trim','');
			$new = $this->_post('new','trim','');
			$new_again = $this->_post('new_again','trim','');
			if($new == ''){
				alert('error','请输入新密码',U('core/user/editpassword'));
			}
			if($new != $new_again){
				alert('error','两次输入不一致！',U('core/user/editpassword'));
			}
			$d_user = D('User');
			$userinfo = $d_user->getUserInfo(array('user_id'=>session('user_id')));
			if($userinfo['password'] != md5(md5($old) . $userinfo['salt'])){
				alert('error','原密码输入错误！',U('core/user/editpassword'));
			}
			if($old == $new){
				alert('error','新密码与原密码不能相同！',U('core/user/editpassword'));
			}
			$info['salt'] = D('User')->getSalt();
			$info['user_id'] = $userinfo['user_id'];
			$info['password'] = md5(md5($new) . $info['salt']);
			if(D('User')->editUserInfo($info)){
				alert('success', '密码修改成功！', U('core/user/editpassword'));
			}else{
				alert('error','密码修改失败！',U('core/user/editpassword'));
			}
		}else{
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function editControl(){
		if($this->isPost()){
			$info['position_id'] = $this->_post('position_id','intval',0);
			$control_arr = $this->_post('control_arr');
			if($info['position_id'] == 0) alert('error','参数错误，请联系管理员！',U('core/user/position'));
			$info['control_ids'] = implode(',',$control_arr);
			$rows = D('Structure')->editPosition($info);
			if($rows !== false){
				alert('success', '设置权限成功', U('hrm/Structure/position'));
			}else{
				alert('error','权限修改失败',U('hrm/Structure/position'));
			}
		}else{
			$position_id = $this->_get('id','intval');
			$group_array = D('Structure')->getControl();
			$position_info = D('Structure')->getPositionInfo($position_id);
			$position_info['control_ids'] = explode(',',$position_info['control_ids']);
			$this->assign('module_name',D('User')->getModuleName());
			$this->assign('list',$group_array);
			$this->assign('position_info',$position_info);
			$this->display();
		}
	}
	//多选
	public function getUserIndex(){
		$d_user = D('User');
		$department_list = D('Structure')->getDepartmentList(0,'--',1);
		$this->assign('department_list',$department_list);
		$userlist = $d_user->getUserList();
		$this->assign('userlist', $userlist);
		$this->display();
	}
	//单选
	public function getUserRIndex(){
		$d_user = D('User');
		$department_list = D('Structure')->getDepartmentList(0,'--',1);
		$this->assign('department_list',$department_list);
		$userlist = $d_user->getUserList();
		$this->assign('userlist', $userlist);
		$this->display();
	}
	
	public function getSubUserDialog(){
		$d_user = D('User');
		$self = $this->_get('self','intval',0);
		$subUser = $d_user->getSubUser(session('position_id'));
		if($self){
			$subUser[] = $d_user->getUserInfo(array('user_id'=>session('user_id')));
		}
		$this->subUser = $subUser;
		$this->display();
	}
	public function getDepartmentUser(){
		$department_id = $this->_get('id','intval',0);
		if($department_id == 0){
			$userlist = D("User")->getUserList();
		}else{
			$position = D('Structure')->getDepartmentPosition($department_id);
			$userlist = array();
			foreach($position as $val){
				$userlist = array_merge($userlist,D("User")->getPositionUser($val['position_id']));
			}
		}
		$this->AjaxReturn($userlist);
	}
	public function getPositionUser(){
		$position_id = $this->_get('id','intval',0);
		if($position_id == 0){
			$userlist = null;
		}else{
			$userlist = D("User")->getPositionUser($position_id);
		}
		$this->AjaxReturn($userlist);
	}
	public function getSubUserCBDialog(){
		$d_user = D('User');
		$self = $this->_get('self','intval',0);
		$subUser = $d_user->getSubUser(session('position_id'));
		if($self){
			$subUser[] = $d_user->getUserInfo(array('user_id'=>session('user_id')));
		}
		$this->subCBUser = $subUser;
		$this->display();
	}
}