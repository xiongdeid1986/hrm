
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;




CREATE TABLE IF NOT EXISTS `hr_announcement` (
  `announcement_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '公告id',
  `creator_user_id` int(10) NOT NULL COMMENT '创建人id',
  `title` varchar(200) NOT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `create_time` int(10) NOT NULL COMMENT '发表时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `color` varchar(50) DEFAULT NULL,
  `department_id` varchar(100) NOT NULL COMMENT '通知部门id',
  `status` int(1) NOT NULL COMMENT '1：发布 2：停用',
  `set_top` int(1) NOT NULL COMMENT '是否顶置 0：不顶置 1：顶置',
  PRIMARY KEY (`announcement_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='公告表';

CREATE TABLE IF NOT EXISTS `hr_appraisal_avg_point` (
  `appraisal_avg_point_id` int(10) NOT NULL AUTO_INCREMENT,
  `examinee_user_id` int(10) NOT NULL COMMENT '考核对象ID',
  `appraisal_manager_id` int(10) NOT NULL COMMENT '考核ID',
  `score_id` int(10) NOT NULL COMMENT '考核内容ID',
  `avg_point` float(10,2) NOT NULL COMMENT '平均分',
  PRIMARY KEY (`appraisal_avg_point_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='平均分';

CREATE TABLE IF NOT EXISTS `hr_appraisal_manager` (
  `appraisal_manager_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL COMMENT '考核名称',
  `start_time` int(10) NOT NULL COMMENT '启用时间',
  `end_time` int(10) NOT NULL COMMENT '截止时间',
  `status` int(1) NOT NULL COMMENT '1：进行中  2：已汇总',
  `executor_id` int(10) NOT NULL COMMENT '负责人',
  `appraisal_template_id` int(10) NOT NULL COMMENT '考核模板',
  `examinee_user_id` varchar(5000) NOT NULL COMMENT '被考核对象',
  `examiner_user_id` varchar(5000) NOT NULL COMMENT '考核评分对象',
  PRIMARY KEY (`appraisal_manager_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='考核管理表';

CREATE TABLE IF NOT EXISTS `hr_appraisal_point` (
  `appraisal_point_id` int(10) NOT NULL AUTO_INCREMENT,
  `examinee_user_id` int(10) NOT NULL COMMENT '被考核对象',
  `examiner_user_id` int(10) NOT NULL COMMENT '考核评分对象',
  `score_id` int(10) NOT NULL COMMENT '考核内容ID',
  `appraisal_manager_id` int(10) NOT NULL COMMENT '考核表ID',
  `point` int(11) NOT NULL COMMENT '单项考核内容得分',
  `comment` text NOT NULL COMMENT '单项考核内容评语',
  PRIMARY KEY (`appraisal_point_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='考核得分表';

CREATE TABLE IF NOT EXISTS `hr_appraisal_template` (
  `appraisal_template_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(1000) NOT NULL COMMENT '模板名称',
  `category_id` int(10) NOT NULL COMMENT '模板类型ID',
  `creator_user_id` int(10) NOT NULL COMMENT '创建人',
  `create_time` int(10) NOT NULL COMMENT '创建日期',
  `description` varchar(1000) NOT NULL COMMENT '模板说明',
  PRIMARY KEY (`appraisal_template_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='模板表';

CREATE TABLE IF NOT EXISTS `hr_appraisal_template_category` (
  `category_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL COMMENT '分类名称',
  `description` varchar(1000) NOT NULL COMMENT '描述',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


INSERT INTO `hr_appraisal_template_category` (`category_id`, `name`, `description`) VALUES
(1, '月度考核', '月度考核'),
(2, '季度考核', '季度考核'),
(3, '年度考核', '年度考核');

CREATE TABLE IF NOT EXISTS `hr_appraisal_template_score` (
  `score_id` int(10) NOT NULL AUTO_INCREMENT,
  `appraisal_template_id` int(10) NOT NULL COMMENT '模板ID',
  `name` varchar(1000) NOT NULL,
  `standard_score` int(10) NOT NULL COMMENT '标准分',
  `low_scope` int(10) NOT NULL COMMENT '评分范围：最低分',
  `high_scope` int(10) NOT NULL COMMENT '评分范围：最高分',
  `description` varchar(2000) NOT NULL COMMENT '考核评分细则',
  PRIMARY KEY (`score_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='评分表';

CREATE TABLE IF NOT EXISTS `hr_archives` (
  `archives_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '档案id',
  `user_id` int(11) NOT NULL COMMENT '员工id',
  `id_num` varchar(200) NOT NULL COMMENT '证件号码',
  `origin` varchar(500) NOT NULL COMMENT '籍贯',
  `archives_num` varchar(200) NOT NULL COMMENT '档案编号',
  `bankcard` varchar(200) NOT NULL COMMENT '工资卡号',
  `tbno` varchar(200) NOT NULL COMMENT '社保号',
  `old_name` varchar(200) NOT NULL COMMENT '曾用名',
  `sex` int(1) NOT NULL COMMENT '性别',
  `birthday` int(11) NOT NULL COMMENT '出生日期',
  `birthplace` varchar(500) NOT NULL COMMENT '出生地',
  `nation` varchar(200) NOT NULL COMMENT '民族',
  `partisan` int(1) NOT NULL COMMENT '政治面貌',
  `accounts` varchar(500) NOT NULL COMMENT '户口',
  `accounts_status` int(1) NOT NULL COMMENT '户口性质',
  `marital_status` int(1) NOT NULL COMMENT '婚姻状况',
  `ygsf` int(1) NOT NULL COMMENT '员工身份',
  `nationality` varchar(500) NOT NULL COMMENT '国籍',
  `document_type` int(1) NOT NULL COMMENT '证件类型',
  `health` int(1) NOT NULL COMMENT '健康状况',
  `height` float(10,2) NOT NULL COMMENT '身高',
  `weight` float(10,2) NOT NULL COMMENT '体重',
  `vision` varchar(200) NOT NULL COMMENT '视力',
  `education` int(1) NOT NULL COMMENT '最高学历',
  `degree` int(1) NOT NULL COMMENT '最高学位',
  `school` varchar(500) NOT NULL COMMENT '毕业院校',
  `speciality` varchar(500) NOT NULL COMMENT '专业',
  `job_title` varchar(500) NOT NULL COMMENT '职称',
  `work_date` int(11) NOT NULL COMMENT '参加工作日期',
  `length_work` varchar(500) NOT NULL COMMENT '工龄',
  `feel_length_work` varchar(500) NOT NULL COMMENT '空闲工龄',
  `create_user_id` int(11) NOT NULL COMMENT '创建人id',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`archives_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hr_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `value` text NOT NULL,
  `description` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


INSERT INTO `hr_config` (`id`, `name`, `value`, `description`) VALUES
(1, 'defaultinfo', 'a:4:{s:4:"name";s:9:"悟空HRM";s:11:"description";s:39:"开源免费的人力资源管理系统";s:15:"allow_file_type";s:45:"pdf,doc,jpg,jpeg,png,gif,txt,doc,xls,zip,docx";s:4:"logo";s:21:"./Public/img/logo.gif";}', '系统默认设置'),
(2, 'contracttype', 'a:3:{i:1;s:15:"试用期合同";i:2;s:12:"正式合同";i:3;s:12:"培训合同";}', '合同类型设置'),
(3, 'contractstatus', 'a:3:{i:1;s:6:"有效";i:2;s:6:"终止";i:3;s:6:"过期";}', '合同状态设置');



CREATE TABLE IF NOT EXISTS `hr_control` (
  `control_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '操作id',
  `name` varchar(20) NOT NULL COMMENT '操作名',
  `g` varchar(20) NOT NULL,
  `m` varchar(50) NOT NULL COMMENT '对应Action',
  `a` varchar(200) NOT NULL COMMENT '行为',
  `is_display` int(1) NOT NULL DEFAULT '1' COMMENT '导航是否显示：0不显示，1显示',
  `sort_id` int(10) NOT NULL COMMENT '排序',
  `description` varchar(200) NOT NULL COMMENT '操作描述',
  PRIMARY KEY (`control_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='本表存放操作信息';


INSERT INTO `hr_control` (`control_id`, `name`, `g`, `m`, `a`, `is_display`, `sort_id`, `description`) VALUES
(1, '添加用户', 'core', 'user', 'adduser', 0, 0, '添加用户'),
(2, '添加部门', 'hrm', 'structure', 'adddepartment', 0, 0, '添加部门'),
(3, '添加岗位', 'hrm', 'structure', 'addposition', 0, 0, '添加岗位'),
(4, '员工列表', 'core', 'user', 'index', 1, 0, ''),
(5, '系统设置', 'core', 'setting', 'defaultinfo', 1, 0, ''),
(6, 'smtp设置', 'core', 'setting', 'smtp', 1, 0, ''),
(7, '菜单设置', 'core', 'navigation', 'index', 1, 0, ''),
(8, '工作台', 'core', 'index', 'index', 1, 0, '首页工作台'),
(9, '培训项目', 'hrm', 'train', 'trainpro', 1, 0, ''),
(10, '下级直属岗位弹出框', 'hrm', 'structure', 'getdepartmentposition', 0, 0, ''),
(11, '站内信', 'core', 'message', 'index', 1, 0, ''),
(12, '编辑员工', 'core', 'user', 'editinfo', 0, 0, '个人资料'),
(13, '人事合同', 'hrm', 'staffcontract', 'index', 1, 0, '人事合同'),
(14, '培训计划', 'hrm', 'train', 'index', 1, 0, '培训计划'),
(15, '部门岗位', 'hrm', 'structure', 'department', 1, 0, ''),
(16, '上下级关系', 'hrm', 'structure', 'position', 1, 0, ''),
(17, '修改密码', 'core', 'user', 'editpassword', 1, 0, ''),
(18, '编辑岗位', 'hrm', 'structure', 'editposition', 0, 0, ''),
(19, '编辑部门', 'hrm', 'structure', 'editdepartment', 0, 0, ''),
(20, '个人资料', 'core', 'user', 'edit', 1, 0, ''),
(21, '岗位删除', 'hrm', 'structure', 'deleteposition', 0, 0, ''),
(22, '删除部门', 'hrm', 'structure', 'deletedepartment', 0, 0, ''),
(26, '通讯录', 'core', 'message', 'contacts', 1, 0, ''),
(24, '查看', 'core', 'message', 'view', 0, 0, ''),
(25, '写信', 'core', 'message', 'send', 0, 0, ''),
(27, '任务列表', 'core', 'task', 'index', 1, 0, '任务列表'),
(28, '添加任务', 'core', 'task', 'add', 0, 0, '添加任务'),
(29, '编辑任务', 'core', 'task', 'edit', 0, 0, '编辑任务'),
(30, '查看任务', 'core', 'task', 'view', 0, 0, '查看任务'),
(31, '删除任务', 'core', 'task', 'delete', 0, 0, '删除任务'),
(32, '添加联系人', 'core', 'message', 'addcontacts', 0, 0, ''),
(33, '修改联系人', 'core', 'message', 'editcontacts', 0, 0, ''),
(34, '删除联系人', 'core', 'message', 'deletecontacts', 0, 0, ''),
(35, '日程列表', 'core', 'event', 'index', 0, 0, ''),
(36, '每月日程', 'core', 'event', 'month', 0, 1, ''),
(48, '找回密码', 'core', 'user', 'active', 0, 0, ''),
(56, '下级部门岗位弹出框', 'hrm', 'structure', 'getpositiondepartment', 0, 0, ''),
(39, '添加日程', 'core', 'event', 'add', 0, 0, ''),
(37, '编辑导航', 'core', 'navigation', 'edit', 0, 0, ''),
(45, '员工登陆', 'core', 'user', 'login', 0, 0, ''),
(38, '删除导航', 'core', 'navigation', 'delete', 0, 0, ''),
(40, '编辑日程', 'core', 'event', 'edit', 0, 0, ''),
(41, '删除日程', 'core', 'event', 'delete', 0, 0, ''),
(42, '日程详情', 'core', 'event', 'view', 0, 0, ''),
(43, '今日日程', 'core', 'event', 'day', 1, 0, ''),
(44, '删除信件', 'core', 'message', 'delete', 0, 0, ''),
(46, '安全退出', 'core', 'user', 'logout', 0, 0, ''),
(47, '授权权限', 'core', 'user', 'editcontrol', 0, 0, ''),
(49, '获得员工列表', 'core', 'user', 'getuserindex', 0, 0, ''),
(50, '下级员工列表', 'core', 'user', 'getsubuserdialog', 0, 0, ''),
(51, '下级员工列表（弹出框）', 'core', 'user', 'getsubusercbdialog', 0, 0, ''),
(52, '添加导航菜单', 'core', 'navigation', 'add', 0, 0, ''),
(53, '导航排序', 'core', 'navigation', 'sorts', 0, 0, ''),
(54, '导航默认链接弹出框', 'core', 'navigation', 'defaultdispalydialog', 0, 0, ''),
(55, '导航功能弹出框', 'core', 'navigation', 'controldialog', 0, 0, ''),
(57, '获得员工列表（单选框）', 'core', 'user', 'getuserrindex', 0, 0, ''),
(58, '添加合同', 'hrm', 'staffcontract', 'add', 0, 0, ''),
(59, '变更合同', 'hrm', 'staffcontract', 'edit', 0, 0, ''),
(60, '删除合同', 'hrm', 'staffcontract', 'delete', 0, 0, ''),
(61, '查看合同', 'hrm', 'staffcontract', 'view', 0, 0, ''),
(62, '合同类型设置', 'core', 'setting', 'contracttype', 1, 0, ''),
(63, '合同状态设置', 'core', 'setting', 'contractstatus', 1, 0, ''),
(64, '删除合同附件', 'hrm', 'staffcontract', 'filedelete', 0, 0, ''),
(65, '添加日志', 'core', 'log', 'add', 0, 1, '添加日志'),
(66, '编辑日志', 'core', 'log', 'edit', 0, 2, '编辑日志'),
(67, '查看日志', 'core', 'log', 'view', 0, 3, '查看日志'),
(68, '删除日志', 'core', 'log', 'delete', 0, 4, '删除日志'),
(69, '日志列表', 'core', 'log', 'index', 1, 0, '日志列表'),
(70, '班次排班', 'hrm', 'workingshift', 'index', 1, 1, ''),
(71, '添加班次', 'hrm', 'workingshift', 'add', 0, 2, ''),
(72, '编辑班次', 'hrm', 'workingshift', 'edit', 0, 3, ''),
(73, '删除班次', 'hrm', 'workingshift', 'delete', 0, 4, ''),
(74, '打卡列表', 'hrm', 'punch', 'index', 1, 0, ''),
(75, '打卡', 'hrm', 'punch', 'add', 0, 0, ''),
(76, '打卡详细', 'hrm', 'punch', 'view', 0, 0, ''),
(77, '人事档案', 'hrm', 'archives', 'index', 1, 0, ''),
(78, '添加人事档案', 'hrm', 'archives', 'add', 0, 0, ''),
(79, '查看人事档案', 'hrm', 'archives', 'view', 0, 0, ''),
(80, '编辑人事档案', 'hrm', 'archives', 'edit', 0, 0, ''),
(81, '删除人事档案', 'hrm', 'archives', 'delete', 0, 0, ''),
(82, '添加培训计划', 'hrm', 'train', 'add', 0, 0, ''),
(83, '编辑培训计划', 'hrm', 'train', 'edit', 0, 0, ''),
(84, '查看培训计划', 'hrm', 'train', 'view', 0, 0, ''),
(85, '删除培训计划', 'hrm', 'train', 'delete', 0, 0, ''),
(86, '添加培训项目', 'hrm', 'train', 'addtrainpro', 0, 0, ''),
(87, '编辑培训项目', 'hrm', 'train', 'editTrainPro', 0, 0, ''),
(88, '查看培训项目', 'hrm', 'train', 'viewTrainPro', 0, 0, ''),
(89, '删除培训项目', 'hrm', 'train', 'deleteTrainPro', 0, 0, ''),
(90, '社保项目', 'hrm', 'insurance', 'insuranceitem', 1, 0, ''),
(91, '社保套帐', 'hrm', 'insurance', 'insurancesuit', 1, 0, ''),
(92, '请假管理', 'hrm', 'leave', 'index', 1, 1, ''),
(93, '添加请假条', 'hrm', 'leave', 'add', 0, 2, ''),
(94, '查看请假条', 'hrm', 'leave', 'view', 0, 3, ''),
(95, '编辑请假条', 'hrm', 'leave', 'edit', 0, 4, ''),
(96, '删除请假条', 'hrm', 'leave', 'delete', 0, 5, ''),
(97, '社保投保', 'hrm', 'insurance', 'index', 1, 0, ''),
(98, '加班管理', 'hrm', 'overtime', 'index', 1, 1, ''),
(99, '添加加班', 'hrm', 'overtime', 'add', 0, 2, ''),
(100, '查看加班', 'hrm', 'overtime', 'view', 0, 3, ''),
(101, '编辑加班', 'hrm', 'overtime', 'edit', 0, 4, ''),
(102, '删除加班', 'hrm', 'overtime', 'delete', 0, 5, ''),
(103, '添加社保项目', 'hrm', 'insurance', 'addInsuranceItem', 0, 0, ''),
(104, '编辑社保项目', 'hrm', 'insurance', 'editInsuranceItem', 0, 0, ''),
(105, '删除社保项目', 'hrm', 'insurance', 'deleteInsuranceItem', 0, 0, ''),
(106, '添加社保套帐', 'hrm', 'insurance', 'addInsuranceSuit', 0, 0, ''),
(107, '编辑社保套帐', 'hrm', 'insurance', 'editInsuranceSuit', 0, 0, ''),
(108, '查看社保套帐', 'hrm', 'insurance', 'viewinsurancesuit', 0, 0, ''),
(109, '删除社保套帐', 'hrm', 'insurance', 'deleteinsurancesuit', 0, 0, ''),
(110, '添加社保投保', 'hrm', 'insurance', 'add', 0, 0, ''),
(111, '编辑社保投保', 'hrm', 'insurance', 'edit', 0, 0, ''),
(112, '查看社保投保', 'hrm', 'insurance', 'view', 0, 0, ''),
(113, '删除投保信息', 'hrm', 'insurance', 'delete', 0, 0, ''),
(114, '调休管理', 'hrm', 'lieu', 'index', 1, 1, ''),
(115, '添加调休', 'hrm', 'lieu', 'add', 0, 2, ''),
(116, '查看调休', 'hrm', 'lieu', 'view', 0, 3, ''),
(117, '编辑调休', 'hrm', 'lieu', 'edit', 0, 4, ''),
(118, '删除调休', 'hrm', 'lieu', 'delete', 0, 5, ''),
(119, '薪资项目', 'hrm', 'salary', 'salaryitem', 1, 0, ''),
(120, '薪资套帐', 'hrm', 'salary', 'salarysuit', 1, 0, ''),
(121, '薪资管理', 'hrm', 'salary', 'index', 1, 0, ''),
(122, '考核模板', 'hrm', 'appraisaltemplate', 'index', 1, 1, ''),
(123, '添加模板', 'hrm', 'appraisaltemplate', 'add', 0, 2, ''),
(124, '编辑模板', 'hrm', 'appraisaltemplate', 'edit', 0, 3, ''),
(125, '删除模板', 'hrm', 'appraisaltemplate', 'delete', 0, 4, ''),
(126, '添加薪资项目', 'hrm', 'salary', 'addSalaryItem', 0, 0, ''),
(127, '修改薪资项目', 'hrm', 'salary', 'editSalaryItem', 0, 0, ''),
(128, '删除薪资项目', 'hrm', 'salary', 'deleteSalaryItem', 0, 0, ''),
(129, '添加薪资套帐', 'hrm', 'salary', 'addSalarySuit', 0, 0, ''),
(130, '修改薪资套帐', 'hrm', 'salary', 'editSalarySuit', 0, 0, ''),
(131, '删除薪资套帐', 'hrm', 'salary', 'deleteSalarySuit', 0, 0, ''),
(132, '发放薪资', 'hrm', 'salary', 'add', 0, 0, ''),
(133, '修改薪资薪资', 'hrm', 'salary', 'edit', 0, 0, ''),
(134, '查看薪资详细', 'hrm', 'salary', 'view', 0, 0, ''),
(135, '删除薪资薪资', 'hrm', 'salary', 'delete', 0, 0, ''),
(136, '发放薪资选择人员', 'hrm', 'salary', 'selectuser', 0, 0, ''),
(137, '人力结构', 'hrm', 'report', 'archives', 1, 0, ''),
(138, '绩效考核管理', 'hrm', 'appraisalmanager', 'index', 1, 1, ''),
(139, '删除绩效考核', 'hrm', 'appraisalmanager', 'delete', 0, 2, ''),
(140, '汇总', 'hrm', 'appraisalmanager', 'summary', 0, 3, ''),
(141, '撤销汇总', 'hrm', 'appraisalmanager', 'reset', 0, 4, ''),
(142, '查看绩效考核', 'hrm', 'appraisalmanager', 'view', 0, 5, ''),
(143, '启用模板', 'hrm', 'appraisalmanager', 'enabletemplate', 0, 6, ''),
(144, '人力结构数据', 'hrm', 'report', 'archivesajax', 0, 0, ''),
(145, '在线评分', 'hrm', 'appraisalpoint', 'index', 1, 1, ''),
(146, '评分', 'hrm', 'appraisalpoint', 'edit', 0, 2, ''),
(147, '添加考核内容', 'hrm', 'appraisaltemplate', 'addscoredialog', 0, 6, ''),
(148, '编辑考核内容', 'hrm', 'appraisaltemplate', 'editscoredialog', 0, 7, ''),
(149, '删除考核内容', 'hrm', 'appraisaltemplate', 'deletescore', 0, 9, ''),
(150, '模板列表', 'hrm', 'appraisaltemplate', 'templatelistdialog', 0, 10, ''),
(151, '成绩', 'hrm', 'appraisalpoint', 'results', 0, 3, ''),
(152, '得分详细', 'hrm', 'appraisalpoint', 'detailresults', 0, 4, ''),
(153, '评分对象', 'hrm', 'appraisalmanager', 'getuserlistdialog', 0, 0, ''),
(154, '查看模板', 'hrm', 'appraisaltemplate', 'view', 0, 5, ''),
(155, '添加任务日志', 'core', 'task', 'addLog', 0, 6, ''),
(156, '编辑任务日志', 'core', 'task', 'editLog', 0, 7, ''),
(157, '删除任务日志', 'core', 'task', 'deletelog', 0, 8, ''),
(158, '添加任务日志弹窗', 'core', 'task', 'addtasklogdialog', 0, 0, ''),
(159, '显示任务日志弹窗', 'core', 'task', 'viewtasklogdialog', 0, 9, ''),
(160, '编辑任务日志弹窗', 'core', 'task', 'edittasklogdialog', 0, 10, ''),
(161, '退还任务申请弹窗', 'core', 'task', 'refundingapplicationdialog', 0, 11, ''),
(162, '驳回退还任务申请弹窗', 'core', 'task', 'sendbackdialog', 0, 12, ''),
(163, '用户列表', 'hrm', 'workingshift', 'getuserdialog', 0, 5, ''),
(164, '排班', 'hrm', 'workingshift', 'shiftwork', 0, 6, ''),
(165, '排班人员列表', 'hrm', 'workingshift', 'workingshiftuserdialog', 0, 8, ''),
(166, '导出打卡记录', 'core', 'punch', 'exportpunch', 0, 4, ''),
(167, '导入打卡记录', 'core', 'punch', 'importpunchdialog', 0, 5, ''),
(168, '请假审核', 'hrm', 'leave', 'auditing', 0, 0, ''),
(169, '加班审核', 'hrm', 'overtime', 'auditing', 0, 6, ''),
(170, '调休审核', 'core', 'lieu', 'auditing', 0, 6, ''),
(171, '加班类型管理', 'hrm', 'overtime', 'category', 0, 7, ''),
(172, '添加加班类型', 'hrm', 'overtime', 'addcategory', 0, 8, ''),
(173, '编辑加班类型', 'hrm', 'overtime', 'editcategory', 0, 9, ''),
(174, '删除加班类型', 'hrm', 'overtime', 'deletecategory', 0, 10, ''),
(175, '考勤日报表', 'hrm', 'attendancesheet', 'daily', 1, 1, ''),
(176, '考勤月报表', 'hrm', 'attendancesheet', 'monthly', 1, 2, ''),
(178, '薪资报表', 'hrm', 'salary', 'monthly', 1, 0, ''),
(179, '考核报表', 'hrm', 'report', 'appraisal', 1, 0, ''),
(181, '公告管理', 'core', 'announcement', 'index', 1, 0, ''),
(182, '添加公告', 'core', 'announcement', 'add', 1, 1, ''),
(183, '查看公告', 'core', 'announcement', 'view', 0, 2, ''),
(184, '编辑公告', 'core', 'announcement', 'edit', 0, 3, ''),
(185, '删除公告', 'core', 'announcement', 'delete', 0, 4, ''),
(186, '模板类型', 'hrm', 'appraisaltemplate', 'category', 0, 0, ''),
(187, '添加模板类型', 'hrm', 'appraisaltemplate', 'addcategory', 0, 0, ''),
(188, '编辑模板类型', 'hrm', 'appraisaltemplate', 'editcategory', 0, 0, ''),
(189, '删除模板类型', 'hrm', 'appraisaltemplate', 'deletecategory', 0, 0, '');



CREATE TABLE IF NOT EXISTS `hr_control_module` (
  `control_module_id` int(10) NOT NULL AUTO_INCREMENT,
  `m` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`control_module_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


INSERT INTO `hr_control_module` (`control_module_id`, `m`, `name`) VALUES
(1, 'user', '用户'),
(2, 'setting', '设置'),
(3, 'navigation', '菜单'),
(4, 'customer', '客户'),
(5, 'structure', '组织架构'),
(6, 'task', '任务'),
(7, 'message', '站内信'),
(8, 'index', '桌面'),
(9, 'event', '日程'),
(10, 'staffcontract', '员工合同'),
(11, 'log', '日志'),
(12, 'workingshift', '班次'),
(13, 'punch', '打卡'),
(14, 'train', '培训管理'),
(15, 'archives', '人事档案'),
(16, 'insurance', '社保公积金'),
(17, 'leave', '请假'),
(18, 'overtime', '加班管理'),
(19, 'lieu', '调休管理'),
(20, 'salary', '薪资管理'),
(21, 'appraisaltemplate', '绩效考核模板'),
(22, 'report', '报表统计'),
(23, 'appraisalmanager', '绩效考核管理'),
(24, 'appraisalpoint', '在线评分'),
(25, 'attendancesheet', '考勤报表'),
(26, 'announcement', '公告');



CREATE TABLE IF NOT EXISTS `hr_department` (
  `department_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '部门id',
  `parent_id` int(10) NOT NULL COMMENT '父类部门id',
  `name` varchar(50) NOT NULL COMMENT '部门名',
  `description` varchar(200) NOT NULL COMMENT '部门描述',
  PRIMARY KEY (`department_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='本表存放部门信息';


INSERT INTO `hr_department` (`department_id`, `parent_id`, `name`, `description`) VALUES
(1, 0, '办公室', '');

CREATE TABLE IF NOT EXISTS `hr_event` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `creator_user_id` int(11) NOT NULL,
  `title` varchar(500) NOT NULL,
  `content` text NOT NULL,
  `address` varchar(500) NOT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `is_deleted` int(1) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hr_file` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `module` varchar(500) NOT NULL,
  `url` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `type` varchar(100) NOT NULL,
  `size` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `hr_insurance` (
  `insurance_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `id_num` varchar(200) NOT NULL,
  `tbno` varchar(200) NOT NULL,
  `tbtime` int(11) NOT NULL,
  `accounts_status` int(1) NOT NULL,
  `ygsf` int(1) NOT NULL,
  `insurance_address` varchar(500) NOT NULL,
  `fund_address` varchar(500) NOT NULL,
  `suit_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `create_user_id` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`insurance_id`),
  FULLTEXT KEY `id_num` (`id_num`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hr_insurance_item` (
  `insurance_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `insurance_type` int(1) NOT NULL,
  `create_user_id` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`insurance_item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hr_insurance_suit` (
  `insurance_suit_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `items` text NOT NULL,
  `create_user_id` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`insurance_suit_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hr_leave` (
  `leave_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(2000) NOT NULL COMMENT '请假人',
  `maker_user_id` int(10) NOT NULL COMMENT '填写人',
  `leave_category_id` int(2) NOT NULL COMMENT '请假类型',
  `start_time` int(10) NOT NULL COMMENT '开始时间',
  `end_time` int(10) NOT NULL COMMENT '结束时间',
  `content` varchar(2000) NOT NULL COMMENT '请假事由',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `status` int(1) NOT NULL COMMENT '0:审核中  1:通过 2:未通过',
  PRIMARY KEY (`leave_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='请假表';


CREATE TABLE IF NOT EXISTS `hr_leave_category` (
  `leave_category_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '类型名称',
  `description` varchar(800) NOT NULL COMMENT '表明哪些理由属于该类',
  PRIMARY KEY (`leave_category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


INSERT INTO `hr_leave_category` (`leave_category_id`, `name`, `description`) VALUES
(1, '事假', ''),
(2, '病假', ''),
(3, '出差', ''),
(4, '婚假', ''),
(5, '产假', ''),
(6, '年假', ''),
(7, '丧假', '');



CREATE TABLE IF NOT EXISTS `hr_lieu` (
  `lieu_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(2000) NOT NULL COMMENT '调休人',
  `maker_user_id` int(10) NOT NULL COMMENT '填写人',
  `lieu_category_id` int(2) NOT NULL COMMENT '调休类型',
  `start_time` int(10) NOT NULL COMMENT '开始时间',
  `end_time` int(10) NOT NULL COMMENT '结束时间',
  `content` varchar(2000) NOT NULL COMMENT '调休原因',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `status` int(1) NOT NULL COMMENT '0:审核中  1:通过 2:未通过',
  PRIMARY KEY (`lieu_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='加班';

CREATE TABLE IF NOT EXISTS `hr_lieu_category` (
  `lieu_category_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '类型名称',
  `description` varchar(800) NOT NULL,
  PRIMARY KEY (`lieu_category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


INSERT INTO `hr_lieu_category` (`lieu_category_id`, `name`, `description`) VALUES
(1, '加班调休', ''),
(2, '年假调休', '');



CREATE TABLE IF NOT EXISTS `hr_log` (
  `log_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '日志id',
  `creator_user_id` int(11) NOT NULL COMMENT '创建人',
  `log_category_id` int(10) NOT NULL,
  `title` varchar(200) NOT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `comment_id` int(10) NOT NULL COMMENT '评论id',
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='日志表';

CREATE TABLE IF NOT EXISTS `hr_log_category` (
  `log_category_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `name` varchar(200) NOT NULL COMMENT '分类名',
  `description` varchar(500) NOT NULL COMMENT '描述',
  `sort_id` int(10) NOT NULL COMMENT '排序',
  PRIMARY KEY (`log_category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='日志类型表';


INSERT INTO `hr_log_category` (`log_category_id`, `name`, `description`, `sort_id`) VALUES
(1, '日报', '每日日志', 1),
(2, '周报', '每周周至', 2),
(3, '月报', '每月月志', 3);



CREATE TABLE IF NOT EXISTS `hr_message` (
  `message_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `to_user_id` int(11) unsigned NOT NULL,
  `content` text NOT NULL,
  `title` varchar(500) NOT NULL,
  `read_time` int(11) unsigned NOT NULL,
  `send_time` int(11) unsigned NOT NULL,
  `status` int(1) NOT NULL,
  `is_deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `hr_mycontacts` (
  `mycontacts_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(500) NOT NULL,
  `sex` int(1) NOT NULL,
  `telephone` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(500) NOT NULL,
  `description` varchar(100) NOT NULL COMMENT '备注',
  PRIMARY KEY (`mycontacts_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='本表存放用户类别信息';


CREATE TABLE IF NOT EXISTS `hr_navigation` (
  `navigation_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '导航名称',
  `description` varchar(300) NOT NULL COMMENT '描述',
  `default_display` int(10) NOT NULL COMMENT '默认显示的操作id',
  `control_ids` varchar(500) NOT NULL COMMENT '导航下的操作',
  `sort_id` int(10) NOT NULL COMMENT '排序',
  PRIMARY KEY (`navigation_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='导航';


INSERT INTO `hr_navigation` (`navigation_id`, `name`, `description`, `default_display`, `control_ids`, `sort_id`) VALUES
(4, '个人', '个人', 20, '17,20,27,28,29,30,31,155,156,157,158,159,160,161,162,35,36,39,40,41,42,43,65,66,67,68,69,', 1),
(3, '组织管理', '管理公司组织部门，岗位，以及员工信息', 15, '2,3,10,15,16,18,19,21,22,56,', 98),
(16, '系统设置', '系统设置', 5, '5,6,7,37,38,52,53,54,55,', 99),
(1, '桌面', '桌面', 8, '8,11,26,24,25,32,33,34,44,', 0),
(11, '薪资社保', '薪资社保', 97, '90,91,97,103,104,105,106,107,108,109,110,111,112,113,119,120,121,126,127,128,129,130,131,132,133,134,135,136,', 7),
(10, '人事', '人事', 4, '1,4,12,47,62,63,9,14,82,83,84,85,86,87,88,89,13,58,59,60,61,64,77,78,79,80,81,', 5),
(12, '绩效考核', '绩效考核', 122, '122,123,124,125,147,148,149,150,154,186,187,188,189,138,139,140,141,142,143,153,145,', 9),
(13, '统计报表', '统计报表', 137, '178,137,179,175,176,', 8),
(14, '排班考勤', '排班考勤', 98, '166,167,170,70,71,72,73,163,164,165,74,75,76,92,93,94,95,96,168,98,99,100,101,102,169,171,172,173,174,114,115,116,117,118,', 6),
(15, '公告管理', '公告管理', 181, '181,182,183,184,185,', 10);



CREATE TABLE IF NOT EXISTS `hr_overtime` (
  `overtime_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(2000) NOT NULL COMMENT '加班人',
  `maker_user_id` int(10) NOT NULL COMMENT '填写人',
  `overtime_category_id` int(2) NOT NULL COMMENT '加班类型',
  `type` int(1) NOT NULL COMMENT '0:加班费 1:调休',
  `start_time` int(10) NOT NULL COMMENT '开始时间',
  `end_time` int(10) NOT NULL COMMENT '结束时间',
  `content` varchar(2000) NOT NULL COMMENT '加班原因',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `status` int(1) NOT NULL COMMENT '0:审核中  1:通过 2:未通过',
  PRIMARY KEY (`overtime_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='加班';



CREATE TABLE IF NOT EXISTS `hr_overtime_category` (
  `overtime_category_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '加班类型名称',
  `payment` float(9,2) NOT NULL COMMENT '加班费',
  `description` varchar(800) NOT NULL COMMENT '描述',
  PRIMARY KEY (`overtime_category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `hr_position` (
  `position_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '岗位id',
  `parent_id` int(10) NOT NULL,
  `name` varchar(20) NOT NULL,
  `department_id` int(10) NOT NULL,
  `plan_num` int(10) NOT NULL COMMENT '编制人数',
  `real_num` int(10) NOT NULL COMMENT '在职人数',
  `control_ids` varchar(500) NOT NULL COMMENT '权限集',
  `description` varchar(200) NOT NULL COMMENT '描述',
  PRIMARY KEY (`position_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='岗位表控制权限';


INSERT INTO `hr_position` (`position_id`, `parent_id`, `name`, `department_id`, `plan_num`, `real_num`, `control_ids`, `description`) VALUES
(1, 0, '总经理', 1, 1, 1, '1,4,12,17,20,48,45,46,47,49,50,51,57,5,6,62,63,7,37,38,52,53,54,55,8,11,26,24,25,32,33,34,44,27,28,29,30,31,155,156,157,158,159,160,161,162,35,36,39,40,41,42,43,65,66,67,68,69,166,167,170,2,3,10,15,16,18,19,21,22,56,9,14,82,83,84,85,86,87,88,89,13,58,59,60,61,64,70,71,72,73,163,164,165,74,75,76,77,78,79,80,81,90,91,97,103,104,105,106,107,108,109,110,111,112,113,92,93,94,95,96,168,98,99,100,101,102,169,171,172,173,174,114,115,116,117,118,119,120,121,126,127,128,129,130,131,132,133,134,135,136,178', '');



CREATE TABLE IF NOT EXISTS `hr_punch` (
  `punch_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `create_time` int(10) NOT NULL COMMENT '打卡时间',
  `from_ip` varchar(100) NOT NULL COMMENT '来自哪里的IP',
  `type` int(1) NOT NULL COMMENT '0：上班 1：下班',
  PRIMARY KEY (`punch_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hr_salary` (
  `salary_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `month` varchar(200) NOT NULL,
  `suit_id` int(11) NOT NULL,
  `items` text NOT NULL,
  `money` float(10,2) NOT NULL,
  `create_user_id` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`salary_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `hr_salary_item` (
  `salary_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `content` varchar(500) NOT NULL,
  `sort_id` int(10) NOT NULL,
  PRIMARY KEY (`salary_item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hr_salary_suit` (
  `salary_suit_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `items` text NOT NULL,
  `create_user_id` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`salary_suit_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hr_staffcontract` (
  `staffcontract_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '员工合同id',
  `user_id` int(11) NOT NULL,
  `number` varchar(200) NOT NULL COMMENT '合同编号',
  `name` varchar(500) NOT NULL COMMENT '合同名称',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '合同类型',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '合同状态',
  `time_type` int(1) NOT NULL DEFAULT '1' COMMENT '期限类型',
  `start_time` int(11) NOT NULL COMMENT '开始时间',
  `end_time` int(11) NOT NULL COMMENT '截止时间',
  `content` text NOT NULL COMMENT '合同内容',
  `create_time` int(11) NOT NULL,
  `create_user_id` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`staffcontract_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hr_task` (
  `task_id` int(10) NOT NULL AUTO_INCREMENT,
  `creator_user_id` int(10) NOT NULL COMMENT '创建人',
  `executor_id` int(10) NOT NULL COMMENT '主要执行人',
  `coordinate_ids` varchar(500) NOT NULL COMMENT '协同执行人',
  `name` varchar(500) NOT NULL COMMENT '主题名称',
  `content` text NOT NULL,
  `status` varchar(500) NOT NULL,
  `level` int(1) NOT NULL COMMENT '重要等级： 1.普通 2.紧急 3.闲时处理',
  `start_time` int(10) NOT NULL COMMENT '开始时间',
  `end_time` int(10) NOT NULL COMMENT '截止时间',
  `create_time` int(10) NOT NULL,
  `update_time` int(10) NOT NULL,
  PRIMARY KEY (`task_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hr_task_log` (
  `task_log_id` int(10) NOT NULL AUTO_INCREMENT,
  `task_id` int(10) NOT NULL COMMENT '所属的任务ID',
  `creator_user_id` int(10) NOT NULL COMMENT '任务日志创建人',
  `title` varchar(500) NOT NULL,
  `content` text NOT NULL,
  `create_time` int(10) NOT NULL,
  `update_time` int(10) NOT NULL,
  PRIMARY KEY (`task_log_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='任务日志表';

CREATE TABLE IF NOT EXISTS `hr_train` (
  `train_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '培训计划id',
  `name` varchar(200) NOT NULL COMMENT '项目名称',
  `train_type` int(1) NOT NULL COMMENT '项目类型',
  `organizers` varchar(200) NOT NULL COMMENT '主办单位',
  `org` varchar(200) NOT NULL COMMENT '培训机构',
  `address` varchar(500) NOT NULL COMMENT '培训地址',
  `day` int(11) NOT NULL COMMENT '培训天数',
  `money` float(10,2) NOT NULL COMMENT '预算费用',
  `change` int(1) NOT NULL COMMENT '是否已转',
  `start_time` int(11) NOT NULL COMMENT '计划开始时间',
  `end_time` int(11) NOT NULL COMMENT '计划结束时间',
  `content` text NOT NULL COMMENT '备注',
  `create_user_id` int(11) NOT NULL COMMENT '创建人',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `owner_user_id` int(11) NOT NULL COMMENT '负责人',
  PRIMARY KEY (`train_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hr_train_pro` (
  `train_pro_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '培训项目id',
  `name` varchar(200) NOT NULL COMMENT '项目名称',
  `train_type` int(1) NOT NULL COMMENT '项目类型',
  `organizers` varchar(200) NOT NULL COMMENT '主办单位',
  `org` varchar(200) NOT NULL COMMENT '培训机构',
  `address` varchar(500) NOT NULL COMMENT '培训地址',
  `day` int(11) NOT NULL COMMENT '培训天数',
  `money` float(10,2) NOT NULL COMMENT '预算费用',
  `start_time` int(11) NOT NULL COMMENT '计划开始时间',
  `end_time` int(11) NOT NULL COMMENT '计划结束时间',
  `train_status` int(1) NOT NULL,
  `class_num` int(11) NOT NULL,
  `user_num` int(11) NOT NULL,
  `total_value` int(1) NOT NULL,
  `total_value_txt` text NOT NULL,
  `content` text NOT NULL COMMENT '备注',
  `create_user_id` int(11) NOT NULL COMMENT '创建人',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `owner_user_id` int(11) NOT NULL COMMENT '负责人',
  PRIMARY KEY (`train_pro_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hr_user` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `category_id` int(11) NOT NULL COMMENT '用户类别',
  `position_id` int(10) NOT NULL COMMENT '岗位id',
  `status` int(1) NOT NULL COMMENT '1在职，2，离职3，退休，0未激活',
  `name` varchar(20) NOT NULL COMMENT '用户名',
  `password` varchar(32) NOT NULL COMMENT '用户密码',
  `salt` varchar(4) NOT NULL COMMENT '安全符',
  `sex` int(1) NOT NULL COMMENT '用户性别1男2女',
  `email` varchar(30) NOT NULL COMMENT '用户邮箱',
  `telephone` varchar(20) NOT NULL COMMENT '用户的电话',
  `address` varchar(100) NOT NULL COMMENT '用户的联系地址',
  `reg_ip` varchar(15) NOT NULL COMMENT '注册时的ip',
  `reg_time` int(10) NOT NULL COMMENT '用户的注册时间',
  `last_login_time` int(10) NOT NULL COMMENT '用户最后一次登录的时间',
  `lostpw_time` int(10) NOT NULL COMMENT '用户申请找回密码的时间',
  `type` int(1) NOT NULL DEFAULT '0' COMMENT '员工类型：0试用期，1正式工2临时工',
  `work_status` int(1) NOT NULL COMMENT '0正常1休假2出差3请假4调休',
  `working_shift_id` int(10) NOT NULL COMMENT '班次',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='本表用来存放用户的相关基本信息';

CREATE TABLE IF NOT EXISTS `hr_working_shift` (
  `working_shift_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '班次ID',
  `name` varchar(500) NOT NULL COMMENT '班次名称',
  `description` varchar(500) NOT NULL COMMENT '班次描述',
  `type` int(1) NOT NULL COMMENT '班次类型：0为标准班次 1为周期班次',
  `start_time` int(10) NOT NULL COMMENT '上班时间',
  `end_time` int(10) NOT NULL COMMENT '下班时间',
  `working_days` varchar(300) NOT NULL COMMENT '工作日',
  `creator_user_id` int(10) NOT NULL COMMENT '班次创建人',
  `create_time` int(10) NOT NULL COMMENT '班次创建时间',
  PRIMARY KEY (`working_shift_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='班次';


CREATE TABLE IF NOT EXISTS `hr_working_shift_log` (
  `working_shift_log_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '班次变更日志id',
  `user_id` int(10) NOT NULL COMMENT '用户',
  `old_working_shift_id` int(10) NOT NULL COMMENT '以前的班次',
  `new_working_shift_id` int(10) NOT NULL COMMENT '现在的班次',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`working_shift_log_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='排班表';