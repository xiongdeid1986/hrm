<?PHP 
class AnnouncementAction extends Action{
    public function _initialize(){
		$action = array(
			'users'=>array('index', 'add', 'view', 'edit', 'delete'),
			'anonymous'=>array('')
		);
		B('Authenticate', $action);
	}

	//公告列表
	public function index(){
		$search_title = empty($_GET['search_title']) ? '' : trim($_GET['search_title']);
		$search_content = empty($_GET['search_content']) ? '' : $_GET['search_content'];
		$search_user_id = empty($_GET['search_user_id']) ? '' : intval($_GET['search_user_id']);
		$search_status = '' == $_GET['search_status'] ? '' : intval($_GET['search_status']);
		$search_create_time = empty($_GET['search_create_time']) ? '' : strtotime($_GET['search_create_time']);

		if(!empty($search_title)){
			$condition['title'] = array('like', '%'.$search_title.'%');
		}
		if(!empty($search_content)){
			$condition['content'] = array('like', '%'.$search_content.'%');
		}
		if(!empty($search_user_id)){
			$condition['creator_user_id'] = $search_user_id;
		}
		if('' !== $search_status){
			$condition['status'] = $search_status;
		}
		if(!empty($search_create_time)){
			$condition['create_time'] = array('between', array($search_create_time-1, $search_create_time+86400));
		}
		
		$p = $this->_get('p','intval',1);
		$announcementlist = D('Announcement')->getAnnouncement($p, $condition);
		$this->announcementlist = $announcementlist['announcementlist'];
		$this->assign('page', $announcementlist['page']);
		$this->alert = parseAlert();
		$this->display();
	}
	public function add(){
		if($this->isPost()){
			$info['creator_user_id'] = session('user_id');
			$info['title'] = trim($_POST['title']);
			$info['color'] = $_POST['color'];
			$info['content'] = $_POST['content'];
			$info['create_time'] = time();
			$info['department_id'] = implode(',', $_POST['department_id']);
			$info['status'] = intval($_POST['status']);
			$info['set_top'] = intval($_POST['set_top']);
			if(empty($info['title'])){
				alert('error','未填写公告标题！',$_SERVER['HTTP_REFERER']);
			}
			if($info['content'] == ''){
				alert('error','未填写日志内容！',$_SERVER['HTTP_REFERER']);
			}
			if(empty($info['department_id'])){
				alert('error','未选择通告部门！',$_SERVER['HTTP_REFERER']);
			}
			$d_announcement = D('Announcement');
			if($d_announcement->addAnnouncement($info)){
				alert('success','添加公告成功',U('core/announcement/index'));
			}else{
				alert('error','添加公告失败',$_SERVER['HTTP_REFERER']);
			}
		}else{
			$this->department_list = D('Structure')->getDepartmentList(0,'',1);
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function view(){
		$announcement_id = intval($_GET['id']);
		if(!empty($announcement_id)){
			$d_announcement = D('Announcement');
			$announcement = $d_announcement->getAnnouncementById($announcement_id);
			$this->announcement = $announcement;
		}else{
			alert('error', '参数错误！', $_SERVER['HTTP_REFERER']);
		}
		$this->alert = parseAlert();
		$this->display();
	}
	
	
	public function edit(){
		$announcement_id = $_REQUEST['id'];
		if(!empty($announcement_id)){
			$d_announcement = D('Announcement');
			if($this->isPost()){
				$data['announcement_id'] = intval($announcement_id);
				$data['title'] = trim($_POST['title']);
				$data['color'] = $_POST['color'];
				$data['content'] = $_POST['content'];
				$data['update_time'] = time();
				$data['department_id'] = implode(',', $_POST['department_id']);
				$data['status'] = intval($_POST['status']);
				$data['set_top'] = intval($_POST['set_top']);
				
				if(empty($data['title'])){
					alert('error','未填写公告标题！',$_SERVER['HTTP_REFERER']);
				}
				if($data['content'] == ''){
					alert('error','未填写日志内容！',$_SERVER['HTTP_REFERER']);
				}
				if(empty($data['department_id'])){
					alert('error','未选择通告部门！',$_SERVER['HTTP_REFERER']);
				}
				if($d_announcement->editAnnouncement($data)){
					alert('success','编辑公告成功',U('core/announcement/index'));
				}else{
					alert('error','编辑公告失败',$_SERVER['HTTP_REFERER']);
				}
			}else{
				$announcement = $d_announcement->getAnnouncementById($announcement_id);
				$this->announcement = $announcement;
				$this->department_list = D('Structure')->getDepartmentList(0,'',1);
			}
		}else{
			alert('error','参数错误！',$_SERVER['HTTP_REFERER']);
		}
		$this->alert = parseAlert();
		$this->display();
	}
	
	public function delete(){
		$announcement_id = $_REQUEST['id'];
		if (!empty($announcement_id)){
			$d_announcement = D('Announcement');
			if ($d_announcement->deleteAnnouncement($announcement_id)) {
				alert('success', '删除成功！', U('core/announcement/index'));
			}else{
				alert('error', '删除失败！', U('core/announcement/index'));
			}
		}else{
			alert('error', '删除失败，未选择需要删除的记录！', U('core/announcement/index'));
		}
	}
}
