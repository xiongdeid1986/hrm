<?php 
class LeaveViewModel extends ViewModel{
	public $viewFields = array(
		'leave'=>array('leave_id', 'user_id'=>'leave_user_id', 'maker_user_id', 'leave_category_id'=>'leave_category_id', 'start_time', 'end_time', 'content', 'create_time','status'=>'leave_status', '_type'=>'LEFT'),
		'user'=>array('user_id'=>'user_user_id', 'category_id'=>'user_category_id',  'position_id'=>'user_position_id', 'name'=>'user_name', 'working_shift_id', '_on'=>'leave.leave_user_id = user.user_user_id', '_type'=>'LEFT'),
		'leaveCategory'=>array('leave_category_id'=>'category_id','name'=>'leave_category_name', 'description', '_on'=>'leave.category_id = leave.leave_user_id')
	);
}