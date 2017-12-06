<?php
/**
 *
 * 打卡模型
 * author：悟空HR
 **/
class PunchModel extends Model{
	/**
	 * 打卡
	 **/
	public function addPunch($data){
		if(!empty($data)){
			//返回数据
			if($this->create($data)){
				return $this->add($data);
			}else{
				return false;
			}
		}
	}
	
	/**
	 * 根据用户获取用户打卡记录
	 * @param user_id(int) 用户id
	 * @param where(array) 查询条件
	 **/
	public function getPunch($user_id, $where){
		$where['user_id'] = $user_id;
		$punch = $this->where($where)->select();
		return $punch;
	}
	
	/**
	 * 获取所有用户打卡记录
	 **/
	public function getPunchAll(){
		$punch = $this->select();
		$d_user = D('User');
		foreach($punch as $k => $v){
			$user = $d_user->getUserInfo(array('user_id'=>$v['user_id']));
			$punch[$k]['user_name'] = $user['name'];
		}
		return $punch;
	}
	
	/**
	 * 获取分页打卡
	 **/
	public function getPunchList($p, $where){
		$d_user = D('User');
		$weekArr = array(
			'0'=>'周日',
			'1'=>'周一',
			'2'=>'周二',
			'3'=>'周三',
			'4'=>'周四',
			'5'=>'周五',
			'6'=>'周六',
		);
		if(!empty($where['user_name'])){
			$user_id = M('user')->where(array('name'=>array('eq', $where['user_name'])))->getField('user_id');
			$where['user_id'] = array('eq', $user_id);
			unset($where['user_name']);
		}

		import('@.ORG.Page');
		$count = $this->where($where)->count();
		$Page = new Page($count,10);
		$show  = $Page->show();
		$punchlist = $this->where($where)->order('create_time desc')->page($p.',10')->select();
		foreach($punchlist as $key=>$val){
			//打卡人
			$user =  $d_user->getUserInfo(array('user_id'=>$val['user_id']));
			$punchlist[$key]['user_name'] = $user['name'];
			//打卡类型
			if(0 == $val['type']){
				$punchlist[$key]['type_name'] = '上班打卡';
			}else{
				$punchlist[$key]['type_name'] = '下班打卡';
			}
			//星期
			$week = date("w",$val['create_time']);
			foreach($weekArr as $k=>$v){
				if($k == $week){
					$punchlist[$key]['week'] = $v;
				}
			}
		}
		return array('page'=>$show ,'punchlist'=>$punchlist);
	}
	
	/**
	 * 导出打卡记录
	 **/
	public function exportExcel(){
		import("ORG.PHPExcel.PHPExcel");
		$objPHPExcel = new PHPExcel();    
		$objProps = $objPHPExcel->getProperties();    
		$objProps->setCreator("5khrm");    
		$objProps->setLastModifiedBy("5khrm");    
		$objProps->setTitle("5khrm Punch Data");    
		$objProps->setSubject("5khrm Punch Data");    
		$objProps->setDescription("5khrm Punch Data");    
		$objProps->setKeywords("5khrm Punch Data");    
		$objProps->setCategory("Punch");
		$objPHPExcel->setActiveSheetIndex(0);     
		$objActSheet = $objPHPExcel->getActiveSheet(); 
		   
		$objActSheet->setTitle('Sheet1');
		$objActSheet->setCellValue('A1', 'ID');
		$objActSheet->setCellValue('B1', '姓名');
		$objActSheet->setCellValue('C1', '打卡时间');
		$objActSheet->setCellValue('D1', '类型');
		$objActSheet->setCellValue('E1', 'IP');

		$list = $this->getPunchAll();
		$i = 1;
		foreach($list as $v){
			$i++;
			$objActSheet->setCellValue('A'.$i, $v['punch_id']);
			$objActSheet->setCellValue('B'.$i, $v['user_name']);
			$objActSheet->setCellValue('C'.$i, date('Y-m-d H:i:s', $v['create_time']));
			$v['type'] == 0 ? $objActSheet->setCellValue('D'.$i, '上班打卡') : $objActSheet->setCellValue('D'.$i, '下班打卡');
			$objActSheet->setCellValue('E'.$i, $v['from_ip']);
		}
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		header("Content-Type: application/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=5khrm_punch_".date('Y-m-d',mktime()).".xls");
        header("Pragma:no-cache");
        header("Expires:0");
        $objWriter->save('php://output'); 
	}
	
}