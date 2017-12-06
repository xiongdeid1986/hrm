<?php
function deldir($dir) {
        $dh = opendir($dir);
         while ($file = readdir($dh)) {
             if ($file != "." && $file != "..") {
                 $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                     unlink($fullpath);
                } else {
					 deldir($fullpath);
				 }
             }
         }
    }

function format_price($num){
	$num = round($num, 0);
	$s_num = strval($num);
	$len = strlen($s_num)-1;
	$result = round($num, -$len);
	return $result;
}

function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}

/**
 * Warning提示信息
 * @param string $type 提示类型 默认支持success, error, info
 * @param string $msg 提示信息
 * @param string $url 跳转的URL地址
 * @return void
 */
function alert($type='info', $msg='', $url='') {
    //多行URL地址支持
    $url        = str_replace(array("\n", "\r"), '', $url);
	$alert = unserialize(stripslashes(cookie('alert')));
    if (!empty($msg)) {
        $alert[$type][] = $msg;
		cookie('alert', serialize($alert));
	}
    if (!empty($url)) {
		if (!headers_sent()) {
			// redirect
			header('Location: ' . $url);
			exit();
		} else {
			$str    = "<meta http-equiv='Refresh' content='0;URL={$url}'>";
			exit($str);
		}
	}

	return $alert;
}

function parseAlert() {
	$alert = unserialize(stripslashes(cookie('alert')));
	cookie('alert', null);

	return $alert;
}

function sendRequest($url, $params = array() , $headers = array()) {
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
	if (!empty($params)) {
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	}
	if (!empty($headers)) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$txt = curl_exec($ch);
	if (curl_errno($ch)) {
		$return = array(0, '连接服务器出错', -1);
	} else {
		$return = json_decode($txt, true);
		if (!$return) {
			$return = array(0, '服务器返回数据异常', -1);
		}
	}

	return $return;
}

function is_email($email)
{
	return strlen($email) > 8 && preg_match("/^[-_+.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+([a-z]{2,4})|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i", $email);
}
function is_phone($phone){
	return strlen(trim($phone)) == 11 && preg_match("/^1[3|5|8][0-9]{9}$/i", trim($phone));
}

function isMobile(){

    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $mobile_agents = Array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");

    $is_mobile = false;

    foreach ($mobile_agents as $device) {
        if (stristr($user_agent, $device)) {
            $is_mobile = true;
            break;
        }
    }

    return $is_mobile;
}

function is_utf8($liehuo_net) 
{
	if (preg_match("/^([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}/",$liehuo_net) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}$/",$liehuo_net) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){2,}/",$liehuo_net) == true) 
	{
		return true; 
	}
	else 
	{ 
		return false; 
	}
}

//验重二维数组排序  $arr 数组 $keys比较的键值
function array_sort($arr,$keys,$type='asc'){ 
	$keysvalue = $new_array = array();
	foreach ($arr as $k=>$v){
		$keysvalue[$k] = $v[$keys];
	}
	if($type == 'asc'){
		asort($keysvalue);
	}else{
		arsort($keysvalue);
	}
	reset($keysvalue);
	$i = 0;
	foreach ($keysvalue as $k=>$v){
		if($i < 8 && $arr[$k][search] > 0){
			$new_array[] = $arr[$k]['value'];
			$i++;
		}
		
	}
	return $new_array; 
}

//判断目录是否可写
function check_dir_iswritable($dir_path){
    $dir_path=str_replace('\\','/',$dir_path);
    $is_writale=1;
    if(!is_dir($dir_path)){
        $is_writale=0;
        return $is_writale;
    }else{
        $file_hd=@fopen($dir_path.'/test.txt','w');
        if(!$file_hd){
            
            $is_writale=0;
            return $is_writale;
        }else{
			@fclose($file_hd);
			@unlink($dir_path.'/test.txt');
		}
		
        $dir_hd=opendir($dir_path);
        while(false!==($file=readdir($dir_hd))){
            if ($file != "." && $file != "..") {
                if(is_file($dir_path.'/'.$file)){
                    //文件不可写，直接返回
                    if(!is_writable($dir_path.'/'.$file)){
                        return 0;
                    } 
                }else{
                    $file_hd2=@fopen($dir_path.'/'.$file.'/test.txt','w');
                    if(!$file_hd2){
                        @fclose($file_hd2);
                        $is_writale=0;
                        return $is_writale;
                    }else{
						@fclose($file_hd);
						@unlink($dir_path.'/test.txt');
					}
                    //递归
                    $is_writale=check_dir_iswritable($dir_path.'/'.$file);
                }
            }
        }
    }
    return $is_writale;
}

function pregtime($timestamp){
	if($timestamp){
		return date('Y-m-d',$timestamp);
	}else{
		return '';
	}
}
/* 
方便数组调试 默认开启die
$arr    : 传入参数
$offset : 0 die ，1 关闭die
 */
function println($arr , $offset = 0){
	echo '<pre>';print_r($arr);echo '</pre>';
	if($offset == 0){
		die;
	}
}
/*
	部门列表
*/
	function getSubDepartment($department_id, $department, $separate, $no_separater) {
		$array = array();
		if($no_separater){
			foreach($department AS $value) {
				if ($department_id == $value['parent_id']) {
					$array[] = array('department_id' => $value['department_id'], 'name' => $separate.$value['name'],'description'=>$value['description']);
					$array = array_merge($array, getSubDepartment($value['department_id'], $department, $separate, 1));
				}
			}
		}else{
			foreach($department AS $value) {
				if ($department_id == $value['parent_id']) {
					$array[] = array('department_id' => $value['department_id'], 'name' => $separate.$value['name'],'description'=>$value['description']);
					$array = array_merge($array, getSubDepartment($value['department_id'], $department, $separate.'--'));
				}
			}
		}
		return $array;
	}
/*
	根据$position_id在$position数组中寻找下级岗位列表
*/	
	function getSubPosition($position_id, $position, $separate) {
		$array = array();
		foreach($position AS $key=> $value) {
			if ($position_id == $value['parent_id']) {
				$m_department = M('Department');
				$department_name = $m_department->where('department_id = %d', $value['department_id'])->getField('name');
				$array[] = array('position_id' => $value['position_id'], 'name' => $separate . $department_name . ' | ' . $value['name'],'description'=>$value['description']);
				$array = array_merge($array, getSubPosition($value['position_id'], $position, $separate.' -- '));
			}
		}
		return $array;
	}

/*
	返回码说明 短信函数返回1发送成功  0进入审核阶段 -4手机号码不正确
*/
//单条短信
//发送到目标手机号码 $telphone手机号码 $message短信内容
function sendSMS($telphone, $message,  $sign_name="sign_name", $sendtime=''){
	// $sms = M('Config')->where('name = "sms"')->getField('value');
	// $sms = unserialize($sms);
	$sms = F('sms');
	//短信接口用户名 $uid
	$uid = $sms['uid'];
	//短信接口密码 $passwd
	$passwd = $sms['passwd'];
	$message = urlencode(iconv("UTF-8", "GBK//IGNORE", $message.'【'.$sms[$sign_name].'】'));
	$telphone = iconv("UTF-8", "GBK//IGNORE", $telphone);
	$gateway = "http://sms.3g6.com.cn/WS/Send.aspx?CorpID=$uid&Pwd=$passwd&Mobile=$telphone&Content=$message&Cell=&SendTime=$sendtime";
	$result = file_get_contents($gateway);
	return $result;
}
function sendtestSMS($uid, $uname, $telphone){
	$uid = trim($uid);
	$passwd = trim($uname);
	$message = "系统测试短信!【hrms管理员】";
	$message = urlencode(iconv("UTF-8", "GBK//IGNORE", $message));
	$telphone = iconv("UTF-8", "GBK//IGNORE", $telphone);
	$gateway = "http://sms.3g6.com.cn/WS/Send.aspx?CorpID=$uid&Pwd=$passwd&Mobile=$telphone&Content=$message&Cell=&SendTime=";
	$result = file_get_contents($gateway);
	return $result;
}

//多条短信 最多600条
//发送到目标手机号码字符串 用","隔开 $telphone手机号码 $message短信内容 
function sendGroupSMS($telphone, $message, $sign_name="sign_name",$sendtime=''){
	$sms = F('sms');
	$uid = $sms['uid'];
	$passwd = $sms['passwd'];
	if($sms['sign_name']) $message = $message.'【'.$sms[$sign_name].'】';
	$telphone = iconv("UTF-8", "GBK//IGNORE", $telphone);
	$post_data = array();
	$post_data['Mobile'] = $telphone;
	$post_data['Content'] = $message;
	$post_data['SendTime'] = $sendtime;
	$post_data['CorpID'] = $uid;
	$post_data['Pwd'] = $passwd;
	$url='http://sms.3g6.com.cn/WS/BatchSend.aspx';
	$o="";
	foreach ($post_data as $k=>$v)
	{
		$o.= "$k=".urlencode(iconv("UTF-8", "GBK//IGNORE",$v))."&";
	}
	$post_data=substr($o,0,-1);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	$result = curl_exec($ch);
	if(substr($result,0,1) == 1) $result = 1;
	return $result;
} 

function getSmsNum(){
	//M('Config')->where('name = "sms"')->getField('value');
	//$sms = unserialize($sms);
	$sms = F('sms');
	//短信接口用户名 $uid
	$uid = $sms['uid'];
	//短信接口密码 $passwd
	$passwd = $sms['passwd'];
	$gateway = "http://sms.3g6.com.cn/WS/SelSum.aspx?CorpID=$uid&Pwd=$passwd";
	$result = file_get_contents($gateway);
	return $result;
}

/*
	功能:发送邮件
	参数说明：  $to_user_id 收件人user_id
				$title 邮件主题
				$content 邮件内容
				$author 作者
*/
function sendEmail($to_user_id,$title,$content,$author){
	C(F('smtp'),'smtp');
	if(!$content) return false;
	if(!$to_user_id) return false;
	if(!$author) $author=C('defaultinfo.name')."系统管理员";
	import('@.ORG.Mail');
	$to_user = M('user')->where('user_id= %d', $to_user_id)->find();
	if(!is_email($to_user['email'])) return false;
	return SendMail($to_user['email'],$title,$content,$author);
}



