<?php
/**
 *
 * 导航模型
 * author：悟空HR
 **/
class NavigationModel extends Model{
	protected $_validate = array(
		array('name','require','导航名称不能为空！'),
		array('name','','导航名称已经存在！',0,'unique',2),
	);
	
	/**
	 * 添加导航
	 **/
	public function addNavigation($navigation){
		$control_idsArr = array_filter(explode(',',$navigation['control_ids']));
		if(!in_array($navigation['default_display'],$control_idsArr)){
			$navigation['control_ids'] .= $navigation['default_display'];
		}
		if ($this->create($navigation) && $this->add()) {
			return true;
		}
		return false;
	}
	/**
	 * 编辑导航
	 **/
	public function editNavigation($navigation){
		$control_idsArr = array_filter(explode(',',$navigation['control_ids']));
		if(!in_array($navigation['default_display'],$control_idsArr)){
			$navigation['control_ids'] .= $navigation['default_display'];
		}
		if($this->create($navigation) && $this->save()){
			return true;
		}
		return false;
	}
	
	/**
	 * 删除导航
	 * 如果$id为数组，则批量删除；否则删除一条记录
	 **/
	public function deleteNavigation($id){
		if (is_array($id)) {
			$navigation_idArr = implode(',',$id);
			if ($this->where(array('navigation_id'=>array('in',$navigation_idArr)))->delete()) {
				return true;
			}
		} else {
			$navigation_id = intval($id);
			if ($this->where(array('navigation_id'=>$navigation_id))->delete()) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 根据名称验证导航名称是否存在验证,存在返回true否则返回false
	 * $name:导航名称
	 * $navigation_id:导航id,默认false
	 **/
	public function checkNavigationName($name,$navigation_id = false){
		if (empty($name)) {
			return false;
		}
		$where['name'] = $name;
		if($navigation_id !== false){
			$where['navigation_id'] = array('neq',$navigation_id);
		}
		$result = $this->where($where)->find();
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 *
	 * 获取所有导航
	 **/
	public function getNavigationList(){
		$result = $this->order('sort_id asc')->select();
		$m_control = M('control');
		foreach($result as $key=>$val){
			$result[$key]['control_ids'] = array_filter(explode(',',$val['control_ids']));
			$controls = $m_control->where(array('control_id'=>array('in',$result[$key]['control_ids'])))->order('sort_id asc')->select();
			foreach($controls as $k=>$v){
				$controls[$k]['url'] = U($v['g'].'/'.$v['m'].'/'.$v['a']);
				$result[$key]['controls'] = $controls;
				if($v['control_id'] == $val['default_display']){
					$result[$key]['default_control'] = $controls[$k];
				}
			}
		}
		return $result;
	}
	
	/**
	 * 通过导航ID获取导航
	 * $navigation_id: 导航id
	 **/
	public function getNavigationById($navigation_id){
		$navigation = $this->where(array('navigation_id'=>$navigation_id))->find();
		$m_control = M('control');
		$control_idsArr = array_filter(explode(',',$navigation['control_ids']));
		$control_ids = array();
		foreach($control_idsArr as $val){
			if(!in_array($val,$vals)){
				$control_ids[] = $val;
			}
		}
		$navigation['control_ids'] = implode(',',$control_ids);
		$control_ids_name = $m_control->where(array('control_id'=>array('in',$control_ids)))->getField('control_id,name');
		$navigation['control_ids_name'] = implode(',',$control_ids_name);
		$navigation['default_name'] = $control_ids_name[$navigation['default_display']];
		return $navigation;
	}
	
	/**
	 * 
	 * 排序
	 **/
	public function sortNavigation($sort){
		$return = false;
		foreach($sort as $key=>$val){
			if($this->where(array('navigation_id'=>$key))->setField('sort_id',$val)){
				$return = true;
			}
		}
		return $return;
	}
	/**
	 * 获得当前操作的control_id
	 *
	 **/
	public function getCurrentControl(){
		$info = M('control')->where(array('g'=>strtolower(GROUP_NAME),'m'=>strtolower(MODULE_NAME),'a'=>strtolower(ACTION_NAME)))->find();
		return $info;
	}
	/**
	 * 从control表中获取所有操作
	 **/
	public function getControlList($is_display = false){
		$where = array();
		if($is_display){
			$where['is_display'] = 1;
		}
		$d_control = D('Control');
		$control = $d_control->where($where)->order('sort_id ASC')->select();
		return $control;
	}
}