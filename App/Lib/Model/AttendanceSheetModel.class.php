<?php
/**
 *
 * 考勤报表 模型
 * author：悟空HR
 **/
class AttendanceSheetModel extends Model{
	/**
	 * 获取日报表
	 **/
	public function getDaily($data){
		$d_user = D('User');
		$d_workingshift = D('WorkingShift');
		$d_punch = D('Punch');
		$where['name'] = array('like', '%'.$data['user_name'].'%');
		$where['status'] = array('in', $data['status']);
		$where['work_status'] = array('in', $data['work_status']);
		$user = $d_user->where($where)->select();

		$today_week = date("w",$data['start_time']);
		$daily = array();
		foreach($user as $key=>$val){
			$working_shift = $d_workingshift->getShiftById($val['working_shift_id']);
			$working_days = explode(',', $working_shift['working_days']);
			
			$begin_today = $data['start_time'];
			$end_today = $data['start_time'] + 24*60*60;
			$condtion['user_id'] = $val['user_id'];
			$condtion['create_time'] = array('between', array($begin_today, $end_today));
			$punch = $d_punch->where($condtion)->select();

			if(in_array($today_week, $working_days)){
				$val['working_shift'] = $working_shift;
				$val['punch'] = $punch;
				$daily[] = $val;
			}else{
				$val['work_status'] = -1;
				$daily[] = $val;
			}
		}
		return $daily;
	}	
	
	
	/**
	 * 获取月报表
	 **/
	public function getMonthly($data){
		$d_user = D('User');
		$d_leave = D('Leave');
		$d_overtime = D('Overtime');
		$d_lieu = D('Lieu');	
		$d_workingshift = D('WorkingShift');	
		$next_year = date('Y',$data['start_time'])+1;
		$next_month = date('m',$data['start_time'])+1;
		$month_time = date('m',$data['start_time']) == 12 ? strtotime($next_year.'-01-01') : strtotime(date('Y',$data['start_time']).'-'.$next_month.'-01');
		$where['name'] = array('like', '%'.$data['name'].'%');
		$where['status'] = array('in', $data['status']);
		$user = $d_user->where($where)->select();
		$leave = $d_leave->getLeaveInfoByTime($data['start_time']);
		$overtime = $d_overtime->getOvertimeInfoByTime($data['start_time']);
		$lieu = $d_lieu->getLieuInfoByTime($data['start_time']);
		foreach($user as $key=>$val){
			$user[$key]['leave_all_hours'] = 0; //请假时长
			$user[$key]['leave_casual_counts'] = 0;//事假
			$user[$key]['leave_sick_counts'] = 0;//病假
			$user[$key]['leave_business_counts'] = 0;//出差
			$user[$key]['leave_marry_counts'] = 0;//婚假
			$user[$key]['leave_maternity_counts'] = 0;//产假
			$user[$key]['leave_annual_counts'] = 0;//年假
			$user[$key]['leave_funeral_counts'] = 0;//丧假
			
			$user[$key]['overtime_all_hours'] = 0;//加班时长
			$user[$key]['overtime_normal_counts'] = 0;//正常加班
			$user[$key]['overtime_weekends_counts'] = 0;//周末加班
			$user[$key]['overtime_holiday_counts'] = 0;//节假日加班
			
			$user[$key]['lieu_all_hours'] = 0;//调休时长
			$user[$key]['lieu_overtime_counts'] = 0;//加班调休
			$user[$key]['lieu_annual_counts'] = 0;//年假调休
			
			//请假信息
			foreach($leave as $k=>$v){
				$userIdArr = array_filter(explode(',',$v['user_id']));
				if(in_array($val['user_id'], $userIdArr)){
					$start_date = $v['start_time'] < $data['start_time'] ? $data['start_time'] :$v['start_time'];
					$end_date = $v['end_time'] > $month_time ? $month_time :$v['end_time'];
					$user[$key]['leave_all_hours'] += $end_date - $start_date;
					$user[$key]['leave'][] = $v;
					if(1 == $v['leave_category_id']){
						$user[$key]['leave_casual_counts']++;
					}elseif(2 == $v['leave_category_id']){
						$user[$key]['leave_sick_counts']++;
					}elseif(3 == $v['leave_category_id']){
						$user[$key]['leave_business_counts']++;
					}elseif(4 == $v['leave_category_id']){
						$user[$key]['leave_marry_counts']++;
					}elseif(5 == $v['leave_category_id']){
						$user[$key]['leave_maternity_counts']++;
					}elseif(6 == $v['leave_category_id']){
						$user[$key]['leave_annual_counts']++;
					}elseif(7 == $v['leave_category_id']){
						$user[$key]['leave_funeral_counts']++;
					}
				}
			}
			$user[$key]['leave_all_hours'] = intval($user[$key]['leave_all_hours']/(60*60));
			$user[$key]['leave_all_counts'] = sizeOf($user[$key]['leave']);
			
			//加班信息
			foreach($overtime as $k=>$v){
				if($val['user_id'] == $v['user_id']){
					$start_date = $v['start_time'] < $data['start_time'] ? $data['start_time'] :$v['start_time'];
					$end_date = $v['end_time'] > $month_time ? $month_time :$v['end_time'];
					$user[$key]['overtime_all_hours'] += $end_date - $start_date;
					$user[$key]['overtime'][] = $v;
					if(1 == $v['overtime_category_id']){
						$user[$key]['overtime_normal_counts']++;
					}elseif(2 == $v['overtime_category_id']){
						$user[$key]['overtime_weekends_counts']++;
					}elseif(3 == $v['overtime_category_id']){
						$user[$key]['overtime_holiday_counts']++;
					}
				}
			}
			$user[$key]['overtime_all_hours'] = intval($user[$key]['overtime_all_hours']/(60*60));
			$user[$key]['overtime_all_counts'] = sizeOf($user[$key]['overtime']);
			
			//调休信息
			foreach($lieu as $k=>$v){
				if($val['user_id'] == $v['user_id']){
					$start_date = $v['start_time'] < $data['start_time'] ? $data['start_time'] :$v['start_time'];
					$end_date = $v['end_time'] > $month_time ? $month_time :$v['end_time'];
					$user[$key]['lieu_all_hours'] += $end_date - $start_date;
					$user[$key]['lieu'][] = $v;
					if(1 == $v['lieu_category_id']){
						$user[$key]['lieu_overtime_counts']++;
					}elseif(2 == $v['lieu_category_id']){
						$user[$key]['lieu_annual_counts']++;
					}
				}
			}
			$user[$key]['lieu_all_hours'] = intval($user[$key]['lieu_all_hours']/(60*60));
			$user[$key]['lieu_all_counts'] = sizeOf($user[$key]['lieu']);
			
			//应上班工时
			$working_days = 0;
			$working_shift = $d_workingshift->getShiftById($val['working_shift_id']);
			$beginThismonth = $data['start_time'];
			$monthdays = date('t', $beginThismonth);
			$endThismonth = strtotime(date('Y-m-'.$monthdays.' 23:59:59', $beginThismonth));
			for($i=1;$i<=$monthdays;$i++){
				$temp = date('w',$beginThismonth + ($i-1)*24*60*60);
				$temp = $temp == 0 ? 7 : $temp;
				if(in_array($temp , $working_shift['working_daysArr'])){
					$working_days++;
				}
			}
			$diff_time = ($working_shift['end_time'] - $working_shift['start_time']);
			$diff_time = $diff_time > 0 ? $diff_time : ($diff_time + 24*60*60);
			$workingshift_all_hours = ($diff_time * $working_days)/(60*60);
			$user[$key]['should_work_hours'] = $workingshift_all_hours;
		}
		//echo '<pre>';print_r($workingshift_all_hours);echo '</pre>';die;
		return $user;
	}

}
