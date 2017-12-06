<?php
class ConfigModel extends Model{
	protected $_validate = array();
	
	public function getConfig($name){
		return unserialize(M('config')->where(array('name'=>$name))->getField('value'));
	}
	public function setConfig($name,$value){
		$value = serialize($value);
		if(M('config')->where(array('name'=>$name))->find()){
			return M('config')->where(array('name'=>$name))->setField('value',$value);
		}else{
			$data = array('name'=>$name,'value'=>$value);
			return M('config')->add($data);
		}
	}
}