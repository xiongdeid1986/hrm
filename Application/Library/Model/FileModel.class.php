<?php
class FileModel extends Model{
	protected $_validate = array();
	public function addFile($info,$id,$module){
		foreach($info as $val){
			$data[] = array(
				'module_id'=>$id,
				'module'=>$module,
				'url'=>$val['savepath'].$val['savename'],
				'name'=>$val['name'],
				'type'=>$val['type'],
				'size'=>$val['size'],
				'create_time'=>time()
			);
		}
		return M('file')->addAll($data);
	}
	public function deteleFile($file_id,$id,$module){
		$where['module_id'] = $id;
		$where['module'] = $module;
		$where['file_id'] = is_array($file_id)?array('in',$file_id):$file_id;
		$url = M('file')->where($where)->getField('url');
		if(M('file')->where($where)->delete()){
			@unlink($url);
			return true;
		}else{
			return false;
		}
	}
}