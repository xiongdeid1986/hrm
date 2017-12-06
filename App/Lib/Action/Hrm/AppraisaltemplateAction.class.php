<?php
/**
 *
 * 绩效考核模板 Action
 * author：悟空HR
**/
class AppraisaltemplateAction extends Action{
	public function _initialize(){
		$action = array(
			'users'=>array('editscoredialog', 'addscoredialog', 'templatelistdialog'),
			'anonymous'=>array()
		);
		B('Authenticate', $action);
	}

	public function index(){
		$p = $this->_get('p','intval',1);
		$appraisal_template_list = D('AppraisalTemplate')->getAppraisalTemplate($p);
		$this->templatelist = $appraisal_template_list['templatelist'];
		$this->assign('page', $appraisal_template_list['page']);
		$this->alert = parseAlert();
		$this->display();
	}
	
	public function add(){
		$d_appraisal_template = D('AppraisalTemplate');
		if ($this->isPost()) {
			$data['name'] = trim($_POST['name']);
			$data['category_id'] = $_POST['category_id'];
			$data['creator_user_id'] = session('user_id');
			$data['create_time'] = time();
			$data['score_id'] = 0;
			$data['description'] = $_POST['description'];
			
			if('' == $data['name']){
				alert('error','未填写模板名称！',$_SERVER['HTTP_REFERER']);
			}
			if('' == $data['category_id']){
				alert('error','未选择模板类型！',$_SERVER['HTTP_REFERER']);
			}
			
			if($result = $d_appraisal_template->addAppraisalTemplate($data)){
				$info['name'] = $_POST['score_name'];
				$info['standard_score'] = $_POST['standard_score'];
				$info['low_scope'] = $_POST['low_scope'];
				$info['high_scope'] = $_POST['high_scope'];
				$info['description'] = $_POST['score_description'];
				foreach($info['name'] as $key=>$val){
					$tempArr = array('name'=>$info['name'][$key], 'standard_score'=>$info['standard_score'][$key], 'low_scope'=>$info['low_scope'][$key], 'high_scope'=>$info['high_scope'][$key], 'description'=>$info['description'][$key], 'appraisal_template_id'=>$result);
					$d_appraisal_template->addScore($tempArr);
				}
				alert('success','添加成功！', U('hrm/appraisaltemplate/index'));
			}else{
				alert('error','添加失败！', U('hrm/appraisaltemplate/index'));
			}
		}else{
			$template_category = $d_appraisal_template->getAppraisalTemplateCategoryList();
			$this->template_category = $template_category;
			$this->creator_user_name = session('name');
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function view(){
		$appraisal_template_id = $_GET['id'];
		if(!empty($appraisal_template_id)){
			$d_appraisal_template = D('AppraisalTemplate');
			$appraisal_template = $d_appraisal_template->getAppraisalTemplateById($appraisal_template_id);
			$this->appraisal_template = $appraisal_template;
		}else{
			alert('error', '参数错误！', U('hrm/appraisaltemplate/index'));
		}
		$this->alert = parseAlert();
		$this->display();
	}
	
	
	public function edit(){
		$appraisal_template_id = intval($_REQUEST['id']);
		if(!empty($appraisal_template_id)){
			$d_appraisal_template = D('AppraisalTemplate');
			if ($this->isPost()) {
				$data['appraisal_template_id'] = $appraisal_template_id;
				$data['name'] = trim($_POST['name']);
				$data['category_id'] = $_POST['category_id'];
				$data['description'] = $_POST['description'];
					
				if('' == $data['name']){
					alert('error','未填写模板名称！',$_SERVER['HTTP_REFERER']);
				}
				if('' == $data['category_id']){
					alert('error','未选择模板类型！',$_SERVER['HTTP_REFERER']);
				}
				
				if($d_appraisal_template->editAppraisalTemplate($data)){
					alert('success','编辑成功！', U('hrm/appraisaltemplate/view', 'id='.$appraisal_template_id));
				}else{
					alert('error','编辑失败！', U('hrm/appraisaltemplate/view', 'id='.$appraisal_template_id));
				}
			}else{
				$this->appraisal_template = $d_appraisal_template->getAppraisalTemplateById($appraisal_template_id);
				$template_category = $d_appraisal_template->getAppraisalTemplateCategoryList();
				$this->template_category = $template_category;
			}
		}else{
			alert('error', '参数错误！', U('hrm/appraisaltemplate/index'));
		}
		$this->alert = parseAlert();
		$this->display();
	}
	
	//删除绩效考核模板
	public function delete(){
		$appraisal_template_id = $_REQUEST['id'];
		if (!empty($appraisal_template_id)){
			$d_appraisal_template = D('AppraisalTemplate');
			if ($d_appraisal_template->deleteAppraisalTemplate($appraisal_template_id)) {
				alert('success', '删除成功！', U('hrm/appraisaltemplate/index'));
			}else{
				alert('error', '删除失败！', U('hrm/appraisaltemplate/index'));
			}
		} else {
			alert('error', '删除失败，未选择需要删除的记录！', U('hrm/appraisaltemplate/index'));
		}
	}
	
	public function addScoreDialog(){
		$appraisal_template_id = intval($_REQUEST['appraisal_template_id']);
		if($this->isPost()){
			$d_appraisal_template = D('AppraisalTemplate');
			$data['appraisal_template_id'] = $appraisal_template_id;
			$data['name'] = $_POST['score_name'];
			$data['standard_score'] = $_POST['standard_score'];
			$data['low_scope'] = $_POST['low_scope'];
			$data['high_scope'] = $_POST['high_scope'];
			$data['description'] = $_POST['description'];
			
			if($d_appraisal_template->addScore($data)){
				alert('success', '添加成功！' , $_SERVER['HTTP_REFERER']);
			}else{
				alert('error', '添加考核内容失败！' , $_SERVER['HTTP_REFERER']);
			}
		}else{
			$this->appraisal_template_id = $appraisal_template_id;
			$this->display();
		}
	}
	
	public function editScoreDialog(){
		$score_id = intval($_REQUEST['score_id']);
		if(!empty($score_id)){
			$d_appraisal_template = D('AppraisalTemplate');
			if($this->isPost()){
				$data['score_id'] = $score_id;
				$data['name'] = $_POST['score_name'];
				$data['standard_score'] = $_POST['standard_score'];
				$data['low_scope'] = $_POST['low_scope'];
				$data['high_scope'] = $_POST['high_scope'];
				$data['description'] = $_POST['description'];

				if($d_appraisal_template->editScore($data)){
					alert('success', '修改成功！' , $_SERVER['HTTP_REFERER']);
				}else{
					alert('error', '修改考核内容失败！' , $_SERVER['HTTP_REFERER']);
				}
			}else{
				$score = $d_appraisal_template->getScoreById($score_id);
				$this->score = $score;
			}
		}else{
			alert('error', '参数错误！', $_SERVER['HTTP_REFERER']);
		}
		$this->display();
	}
	
	public function deleteScore(){
		$score_id = intval($_GET['score_id']);
		if (!empty($score_id)){
			$d_appraisal_template = D('AppraisalTemplate');
			if ($d_appraisal_template->deleteScore($score_id)) {
				alert('success', '删除成功！', $_SERVER['HTTP_REFERER']);
			}else{
				alert('error', '删除失败！', $_SERVER['HTTP_REFERER']);
			}
		} else {
			alert('error', '删除失败，未选择需要删除的记录！', $_SERVER['HTTP_REFERER']);
		}
	}
	
	//模板列表Dialog
	public function templateListDialog(){
		$p = $this->_get('p','intval',1);
		$appraisal_template_list = D('AppraisalTemplate')->getAppraisalTemplate($p);
		$this->templatelist = $appraisal_template_list['templatelist'];
		$this->assign('page', $appraisal_template_list['page']);
		$this->alert = parseAlert();
		$this->display();
	}
	
	//模板类型列表
	public function category(){
		$appraisal_template = D('AppraisalTemplate');
		$template_category = $appraisal_template->getAppraisalTemplateCategoryList();
		$this->category = $template_category;
		$this->alert = parseAlert();
		$this->display();
	}
	
	public function addCategory(){
		if($this->isPost()){
			$appraisal_template = D('AppraisalTemplate');
			$data['name'] = $_POST['name'];
			$data['description'] = $_POST['description'];
			
			if(empty($data['name'])){
				alert('error', '未填写模板类型名称！', $_SERVER['HTTP_REFERER']);
			}
			
			if($appraisal_template->addTemplateCategory($data)){
				alert('success', '模板类型添加成功！', U('hrm/appraisaltemplate/category'));
			}else{
				alert('error', '模板类型添加失败！', U('hrm/appraisaltemplate/category'));
			}
		}else{
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function editCategory(){
		if($this->isPost()){
			$category_id = $_POST['id'];
			if(!empty($category_id)){
				$appraisal_template = D('AppraisalTemplate');
				$data['category_id'] = $category_id;
				$data['name'] = $_POST['name'];
				$data['description'] = $_POST['description'];
				
				if(empty($data['name'])){
					alert('error', '未填写模板类型名称！', $_SERVER['HTTP_REFERER']);
				}
				
				if($appraisal_template->editTemplateCategory($data)){
					alert('success', '模板类型编辑成功！', U('hrm/appraisaltemplate/category'));
				}else{
					alert('error', '加班类型编辑失败！', U('hrm/appraisaltemplate/category'));
				}
			}else{
				alert('error', '参数错误！', $_SERVER['HTTP_REFERER']);
			}
		}else{
			$category_id = $_GET['id'];
			if(!empty($category_id)){
				$appraisal_template = D('AppraisalTemplate');
				$template_category = $appraisal_template->getTemplateCategoryById($category_id);
				$this->category = $template_category;
			}else{
				alert('error', '参数错误！', $_SERVER['HTTP_REFERER']);
			}
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function deleteCategory(){
		$category_id = $_GET['id'];
		if(!empty($category_id)){
			$appraisal_template = D('AppraisalTemplate');
			if($appraisal_template->deleteTemplateCategory($category_id)){
				alert('success', '删除模板类型成功！', U('hrm/appraisaltemplate/category'));
			}else{
				alert('error', '删除模板类型失败！', U('hrm/appraisaltemplate/category'));
			}
		}else{
			alert('error', '参数错误！', U('hrm/appraisaltemplate/category'));
		}
	}
}