<?php
/**
 *
 * 打卡 Action
 * author：悟空HR
**/
class PunchAction extends Action{

	public function _initialize(){
		$action = array(
			'users'=>array('index', 'add', 'exportpunch', 'importpunchdialog'),
			'anonymous'=>array()
		);
		B('Authenticate', $action);
	}
	
	public function index(){
		$search_user_name = empty($_GET['search_user_name']) ? '' : trim($_GET['search_user_name']);
		$search_type = $_GET['search_type'] == '' ? '' : intval($_GET['search_type']);
		$search_start_time = empty($_GET['search_start_time']) ? '' : strtotime($_GET['search_start_time']);
		$search_end_time = empty($_GET['search_end_time']) ? '' : strtotime($_GET['search_end_time']);
		
		if(!empty($search_user_name)){
			$condition['user_name'] = $search_user_name;
		}
		if('' !== $search_type){
			$condition['type'] = $search_type;
		}
		if(!empty($search_start_time)){
			if(!empty($search_end_time)){
				$condition['create_time'] = array('between', array($search_start_time, $search_end_time));
			}else{
				$condition['create_time'] = array('between', array($search_start_time-1, $search_start_time+86400));
			}
		}
		if(!empty($search_end_time)){
			if(!empty($search_start_time)){
				$condition['create_time'] = array('between', array($search_start_time, $search_end_time));
			}else{
				$condition['create_time'] = array('between', array($search_end_time-1, $search_end_time+86400));
			}
		}
		
		$d_punch = D('Punch');
		$p = $this->_get('p','intval',1);
		$punchlist = $d_punch->getPunchList($p, $condition);
		$this->punchlist = $punchlist['punchlist'];
		$this->assign('page', $punchlist['page']);
		$this->alert = parseAlert();
		$this->display();
	}
	public function add(){
		if ($this->isAjax()) {
			$data['user_id'] = $_POST['user_id'];
			$data['type'] = $_POST['punch_type'];
			$data['from_ip'] = get_client_ip();
			$data['create_time'] = time();
			
			$d_punch = D('Punch');
			$dayBegin = mktime(0, 0, 0, date("m"), date("d"), date("Y"));//当天开始时间戳
			$dayEnd = mktime(23, 59, 59, date("m"), date("d"), date("Y"));//当天结束时间戳
			$where['create_time'] = array('between', array($dayBegin, $dayEnd));
			$where['type'] = $data['type'];
			if($d_punch->getPunch($_POST['user_id'],$where)){
				if($data['type'] == 0){
					$this->ajaxReturn('', '您已打过上班卡，请勿重复打卡！', 1);
				}else{
					$this->ajaxReturn('', '您已打过下班卡，请勿重复打卡！', 1);
				}
			}else{
				if($d_punch->addPunch($data)){
					$now_time = date('Y-m-d H:i:s',time());
					if($data['type'] == 0){
						$this->ajaxReturn($now_time,$now_time.'  上班打卡成功，开始愉快的一天工作吧！',1);
					}else{
						$this->ajaxReturn($now_time,$now_time.'  下班打卡成功，早点回家不要让家人等太久了，路上注意安全！',1);
					}
				}else{
					$this->ajaxReturn('','',0);
				}
			}
		}
	}
	
	public function exportPunch(){
		$d_punch = D('Punch');
		$d_punch->exportExcel();
	}
	
	public function importPunchDialog(){
		if($this->isPost()){
			$m_punch = M('punch');
			if (isset($_FILES['excel']['size']) && $_FILES['excel']['size'] != null) {
				import('@.ORG.UploadFile');
				$upload = new UploadFile();
				$upload->maxSize = 20000000;
				$upload->allowExts  = array('xls');
				$dirname = './Uploads/' . date('Ym', time()).'/'.date('d', time()).'/';
				if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
					alert('error', '附件上传目录不可写', U('hrm/punch/index'));
				}
				$upload->savePath = $dirname;
				if(!$upload->upload()) {
					alert('error', $upload->getErrorMsg(), U('hrm/punch/index'));
				}else{
					$info =  $upload->getUploadFileInfo();
				}
			}
			if(is_array($info[0]) && !empty($info[0])){
				$savePath = $dirname . $info[0]['savename'];
			}else{
				alert('error', '上传失败', U('hrm/punch/index'));
			}
			import("ORG.PHPExcel.PHPExcel");
			$PHPExcel = new PHPExcel();
			$PHPReader = new PHPExcel_Reader_Excel2007();
			if(!$PHPReader->canRead($savePath)){
				$PHPReader = new PHPExcel_Reader_Excel5();
			}
			$PHPExcel = $PHPReader->load($savePath);
			$currentSheet = $PHPExcel->getSheet(0);
			$allRow = $currentSheet->getHighestRow();
			if ($allRow <= 1) {
				alert('error', '上传文件无有效数据', U('hrm/punch/index'));
			} else {
				for($currentRow = 2;$currentRow <= $allRow;$currentRow++){
					$data = array();
					$data['user_id'] = $currentSheet->getCell('A'.$currentRow)->getValue();
					$time_info = $currentSheet->getCell('B'.$currentRow)->getValue();
					$create_time = intval(PHPExcel_Shared_Date::ExcelToPHP($time_info))-8*60*60;
					$data['create_time'] = $create_time;
					$data['from_ip'] = get_client_ip();
					
					if (!$m_punch->add($data)) {
						alert('error', '导入至第' . $currentRow . '行出错', U('hrm/punch/index'));
						break;
					}
				}
				alert('success', '导入成功！', U('hrm/punch/index'));
			}
		}else{
			$this->display();
		}
	}
}