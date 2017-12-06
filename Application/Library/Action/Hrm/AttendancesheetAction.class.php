<?php
/**
 *
 * 考勤报表 Action
 * author：悟空HR
**/
class AttendancesheetAction extends Action{

	public function _initialize(){
		$action = array(
			'users'=>array('daily','monthly'),
			'anonymous'=>array()
		);
		B('Authenticate', $action);
	}

	//日报表
	public function daily(){
		if($this->isPost()){
			$data['user_name'] = $_POST['user_name'];
			$data['status'] = join(',', $_POST['status']);
			$data['work_status'] = join(',', $_POST['work_status']);
			$data['start_time'] = strtotime($_POST['start_time']);
			
			$d_attendancesheet = D('AttendanceSheet');
			$attendancesheet = $d_attendancesheet->getDaily($data);
			$this->daily = $attendancesheet;
		}
		$this->alert = parseAlert();
		$this->display();
	}
	
	//月报表
	public function monthly(){
		if($this->isPost()){
			$data['status'] = $_POST['status'];
			$data['name'] = $_POST['user_name'];
			$data['start_time'] = strtotime($_POST['start_time']);
			
			$d_attendancesheet = D('AttendanceSheet');
			$attendancesheet = $d_attendancesheet->getMonthly($data);
			$this->monthly = $attendancesheet;
		}
		$this->alert = parseAlert();
		$this->display();
	}
}