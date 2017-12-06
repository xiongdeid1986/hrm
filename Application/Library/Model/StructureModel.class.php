<?php 
    class StructureModel extends Model{
		
		/**	
		 *	检测岗位名称
		 **/
		public function checkPositionName($name,$position_id=0){
			if(!$name) return -1;
			$position = M('position');
			$where['name'] = $name;
			if($position_id){
				$where['position_id'] = array('neq',$position_id);
			}
			if($position->where($where)->find()){
				return -2;
			}else{
				return 1;
			}
		}
		public function addPosition($info){
			if(!M('position')->create($info)){
				return false;
			}
			return M('position')->add();
		}
		public function editPosition($info){
			if(!M('position')->create($info)){
				return false;
			}
			return M('position')->save();
		}
		public function getPositionInfo($position_id){
			if(!empty($position_id)){
				$info = M('position')->where(array('position_id'=>$position_id))->find();
				if(!$info) return false;
				$info['user'] = M('user')->where(array('position_id'=>$position_id,'status'=>1))->select();
				$info['sub'] = M('position')->where(array('parent_id'=>$position_id))->select();
				return $info;
			}else{
				return false;
			}
		}
		//岗位上下级关系树形html
		function getSubPositionTreeCode($position_id=0, $first=0) {
			$string = "";
			$position_list = M('position')->where('parent_id = %d', $position_id)->select();

			if ($position_list) {
				if ($first) {
					$string = '<ul id="browser" class="filetree">';
				} else {
					$string = "<ul>";
				}
				foreach($position_list AS $value) {
					$department_name = M('Department')->where('department_id = %d', $value['department_id'])->getField('name');
					$edit = D('User')->checkControl(array('g'=>hrms,'m'=>'structure','a'=>'editposition')) ? "<a class='position_edit' rel=".$value['position_id']." href='javascript:void(0)'>编辑</a>" : "";
					$control = D('User')->checkControl(array('g'=>hrms,'m'=>'structure','a'=>'editcontrol')) ? "<a class='control_edit' rel=".$value['position_id']." href='javascript:void(0)'>授权</a>" : "";
					$delete = D('User')->checkControl(array('g'=>hrms,'m'=>'structure','a'=>'deleteposition')) ? "<a class='position_delete' rel=".$value['position_id']." href='javascript:void(0)'>删除</a> " : "";
					
					if($first){
						$string .= "<li><span rel='".$value['position_id']."' class='file'>".$value['name']." - $department_name"." (编制：".$value['plan_num']."&nbsp;在职：".$value['real_num'].")&nbsp; <span class='control' id='control_file".$value['position_id']."'>".$edit." &nbsp; ".$control." &nbsp; ".$delete."</span></span>".$this->getSubPositionTreeCode($value['position_id'])."</li>";
					} else {
						$string .= "<li class='closed'><span rel='".$value['position_id']."' class='file'>".$value['name']."-$department_name"." (编制：".$value['plan_num']."&nbsp;在职：".$value['real_num'].")&nbsp; <span class='control' id='control_file".$value['position_id']."'>".$edit." &nbsp; ".$control." &nbsp; ".$delete." </span></span>".$this->getSubPositionTreeCode($value['position_id'])."</li>";
					}
					
				}
				$string .= "</ul>";
			} 

			return $string;
		}
		
		/**	
		 *	检测部门名称
		 **/
		public function checkDepartmentName($name,$department_id=0){
			if(!$name) return -1;
			$department = M('department');
			$where['name'] = $name;
			if($department_id){
				$where['department_id'] = array('neq',$department_id);
			}
			if($department->where($where)->find()){
				return -2;
			}else{
				return 1;
			}
		}
		public function addDepartment($info){
			if(!M('department')->create($info)){
				return false;
			}
			return M('department')->add();
		}
		public function editDepartment($info){
			if(!M('department')->create($info)){
				return false;
			}
			return M('department')->save();
		}
		public function getDepartmentInfo($department_id){
			$info = M('department')->where(array('department_id'=>$department_id))->find();
			if(!$info) return false;
			$info['position'] = M('position')->where(array('department_id'=>$department_id))->select();
			$info['sub'] = M('department')->where(array('parent_id'=>$department_id))->select();
			return $info;
		}
		/**	
		 *	下级部门列表
		 *	$department_id 传入的岗位id  =0时为所有部门
		 *	$string   部门name前置字符
		 *	$me=1时  包括自身部门
		 *	$notin   被排除的部门id（包括他的下级部门）
		 **/
		function getDepartmentList($department_id=0,$string='',$me=0,$notin=0) {
			$array = array();
			$first = empty($department_id) ? '' : $string;
			if($me==1 && $department_id != 0){
				$me = M('department')->where('department_id = %d',$department_id)->find();
				$array[$me['department_id']] = array('department_id'=>$me['department_id'],'name'=>$first.$me['name']);
			}
			$rows = M('department')->where('parent_id = %d',$department_id)->select();
			foreach($rows as $v){
				if($v['department_id'] != $notin){
					$array[$v['department_id']] = array('department_id'=>$v['department_id'],'name'=>$first.$v['name']);
					$str = !empty($string) ? "&nbsp;&nbsp;&nbsp;".$string : '';
					$array = $array + $this->getDepartmentList($v['department_id'],$str,0,$notin);
				}
			}
			return $array;
		}
		
		// 部门岗位树形html
		function getSubDepartmentTreeCode($department_id, $first=0) {
			$string = "";
			$department_list = M('Department')->where('parent_id = %d', $department_id)->select();
			$position_list = M('position')->where('department_id = %d', $department_id)->select();

			if ($department_list || $position_list) {
				if ($first) {
					$string = '<ul id="browser" class="filetree">';
				} else {
					$string = "<ul>";
				}
				foreach($position_list AS $value) {
					$edit = D('User')->checkControl(array('g'=>hrms,'m'=>'structure','a'=>'editposition')) ? "<a class='position_edit' rel=".$value['position_id']." href='javascript:void(0)'>编辑</a>" : "";
					$delete = D('User')->checkControl(array('g'=>hrms,'m'=>'structure','a'=>'deleteposition')) ? "<a class='position_delete' rel=".$value['position_id']." href='javascript:void(0)'>删除</a> " : "";
					$string .= "<li><span rel='".$value['position_id']."' class='file'>".$value['name']."(编制:".$value['plan_num']."&nbsp;在职:".$value['real_num'].") &nbsp; <span class='control' id='control_file".$value['position_id']."'>".$edit." &nbsp; ".$delete." </span> </span></li>";
				}
				foreach($department_list AS $value) {
					$edit = D('User')->checkControl(array('g'=>hrms,'m'=>'structure','a'=>'editdepartment')) ? "<a class='department_edit' rel=".$value['department_id']." href='javascript:void(0)'>编辑</a>" : "";
					$delete = D('User')->checkControl(array('g'=>hrms,'m'=>'structure','a'=>'deletedepartment')) ? "<a class='department_delete' rel=".$value['department_id']." href='javascript:void(0)'>删除</a>" : "";
					if($first){
						$string .= "<li><span rel='".$value['department_id']."' class='folder'>".$value['name']." &nbsp; <span class='control' id='control_folder".$value['department_id']."'>".$edit." &nbsp; ".$delete." </span></span>".$this->getSubDepartmentTreeCode($value['department_id'])."</li>";
					} else {
						$string .= "<li class='closed'><span rel='".$value['department_id']."' class='folder'>".$value['name']." &nbsp; <span class='control' id='control_folder".$value['department_id']."'>".$edit." &nbsp; ".$delete." </span></span>".$this->getSubDepartmentTreeCode($value['department_id'])."</li>";
					}
					
				}
				$string .= "</ul>";
			} 
			return $string;
		}
		/*
		*获得部门下级直属岗位
		*$position_id 不需要包含的岗位id
		*/
		public function getDepartmentPosition($department_id){
			return $position = M('position')->where(array("department_id"=>$department_id))->select();
		}
		/*
		*获得部门下属岗位及上级部门岗位
		*$position_id 不需要包含的岗位id
		*/
		function getPositionDepartment($department_id,$position_id = 0){
			$position = M('position')->where(array("department_id"=>$department_id))->select();
			$parent_id = M('department')->where(array("department_id"=>$department_id))->getField('parent_id');
			if($parent_id != 0){
				$parent_position = $this->getPositionDepartment($parent_id);
				if(!$position){
					$position = $parent_position;
				}elseif($parent_position){
					$position = array_merge($position,$parent_position);
				}
			}
			if($position_id != 0){
				unset($position[array_search(M('position')->where(array("position_id"=>$position_id))->find(),$position)]);
			}
			return $position;
		}
		/**
		 * 从control表中获得g=>m=>a的多维数组
		 **/
		function getControl($is_display = false){
			$where = array();
			if($is_display){
				$where['is_display'] = 1;
			}
			$group = M('control')->where($where)->select();
			$group_array = array();
			foreach($group as $k=>$v){
				$group_array[$v['g']][$v['m']][] = $v;
			}
			return $group_array;
		}
		
		
		/**	
		 *	下级岗位列表
		 *	$position_id 传入的岗位id  =0时为所有岗位
		 *	$string   岗位name前置字符
		 *	$me=1时  包括自身岗位
		 *	$notin   被排除在外的岗位id（包括他的下级岗位）
		 **/
		function getPositiontList($position_id=0,$string='',$me=0,$notin=0) {
			$array = array();
			$first = empty($position_id) ? '' : $string;
			if($me==1){
				$me = M('position')->where('position_id = %d',$position_id)->find();
				$array[$me['position_id']] = array('position_id'=>$me['position_id'],'name'=>$first.$me['name']);
			}
			$rows = M('position')->where('parent_id = %d',$position_id)->select();
			foreach($rows as $v){
				if($v['position_id'] != $notin){
					$array[$v['position_id']] = array('position_id'=>$v['position_id'],'parent_id'=>$v['parent_id'],'name'=>$first.$v['name']);
					$str = !empty($string) ? "&nbsp;&nbsp;&nbsp;".$string : '';
					$array = array_merge($array,$this->getPositiontList($v['position_id'],$str,0,$notin));
				}
			}
			return $array;
		}
    }
		