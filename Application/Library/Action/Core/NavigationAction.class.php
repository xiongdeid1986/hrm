<?php 
/**
 *
 * 导航菜单相关模块
 * author：悟空HR
 **/ 
class NavigationAction extends Action {
	/*
		users	登录后无权限可见
		anonymous 匿名无权限也可见
	*/
	public function _initialize(){
		$actions = array(
			'users'=>array('defaultdispalydialog','controldialog'),
			'anonymous'=>array('')
		);
		B('Authenticate', $actions);
	}

	//导航首页
	public function index() {
		$d_navigation = D('Navigation');
		$this->navigation = $d_navigation->getNavigationList();
		$this->alert=parseAlert();
		$this->display();
	}
	
	//添加导航
	public function add(){
		if($this->isPost()){
			$d_navigation = D('Navigation');
			$data['name'] = $this->_post('name','trim','');
			$data['description'] = $this->_post('description','trim','');
			$data['default_display'] = $this->_post('default_display','intval',0);
			$data['control_ids'] = $this->_post('control_ids','trim','');
			$data['sort_id'] = $this->_post('sort_id','intval',0);
			if ($d_navigation->checkNavigationName($data['name'])) {
				alert('error', '导航名称为空或已存在！', U('core/navigation/add'));
			}
			if ($data['default_display'] == 0) {
				alert('error', '请选择默认操作', U('core/navigation/add'));
			}
			
			if ($d_navigation->addNavigation($data)) {
				alert('success', '添加成功！', U('core/navigation/index'));
			} else {
				alert('error', '添加失败！', U('core/navigation/add'));
			}
		}else{
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	//编辑导航
	public function edit(){
		$d_navigation = D('Navigation');
		if ($this->isPost()) {
			$data['navigation_id'] = $this->_post('navigation_id','intval',0);
			$data['name'] = $this->_post('name','trim','');
			$data['description'] = $this->_post('description','trim','');
			$data['default_display'] = $this->_post('default_display','intval',0);
			$data['control_ids'] = $this->_post('control_ids','trim','');
			$data['sort_id'] = $this->_post('sort_id','intval',0);
			if ($data['navigation_id'] == 0) {
				alert('error', '参数错误！', U('core/navigation/index'));
			}
			if ($d_navigation->checkNavigationName($data['name'],$data['navigation_id'])) {
				alert('error', '导航名称为空或已存在！', U('core/navigation/index'));
			}
			if ($data['default_display'] == 0) {
				alert('error', '请选择默认操作', U('core/navigation/index'));
			}
			if($d_navigation->editNavigation($data)){
				alert('success', '修改成功！', U('core/navigation/index'));
			}else{
				alert('error', '数据无变化，修改失败！', U('core/navigation/index'));
			}
		} else {
			$navigation_id = $this->_get('id','intval',0);
			if($navigation_id == 0) alert('error', '参数错误！', U('core/navigation/index'));
			$this->navigation = $d_navigation->getNavigationById($navigation_id);
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	//删除导航
	public function delete(){
		$id = $_REQUEST['id'];
		if (!empty($id)){
			$d_navigation = D('Navigation');
			if ($d_navigation->deleteNavigation($id)) {
				alert('success', '删除成功！', U('core/navigation/index'));
			}else{
				alert('error', '删除失败！', U('core/navigation/index'));
			}
		} else {
			alert('error', '删除失败，未选择需要删除的记录！', U('core/navigation/index'));
		}
	}
	
	//排序
	public function sorts(){
		$sort = $_POST['sort'];
		$d_navigation = D('Navigation');
		if($d_navigation->sortNavigation($sort)){
			alert('success', '排序成功！', U('core/navigation/index'));
		}else{
			alert('error', '数据无变化，排序失败！', U('core/navigation/index'));
		}
	}
	
	public function defaultDispalyDialog(){
		$default_display = $_REQUEST['default_display'];
		$d_user = D('User');
		$this->controlList = D('Structure')->getControl(true);
		$this->default_display = $default_display;
		$this->assign('module_name',$d_user->getModuleName());
		$this->display();
	}
	
	public function controlDialog(){
		$control_ids = $_REQUEST['control_ids'];
		$control_idsArr = array_filter(explode(',',$control_ids));
		$d_user = D('User');
		$this->controlList = D('Structure')->getControl();
		$this->assign('module_name',$d_user->getModuleName());
		$this->control_idsArr = $control_idsArr;
		$this->display();
	}
}