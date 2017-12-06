<?php 
class UserViewModel extends ViewModel{
	public $viewFields = array(
		'user'=>array('user_id','position_id','name'=>'name','password','salt','status','type','work_status','category_id', 'sex', 'address', 'email', 'telephone','working_shift_id', '_type'=>'LEFT'),
		'position'=>array('name'=>'position_name', 'parent_id',  'department_id', 'description', 'control_ids','_on'=>'position.position_id=user.position_id', '_type'=>'LEFT'),
		'department'=>array('name'=>'department_name', '_on'=>'department.department_id=position.department_id')
	);
}