<?php
/**
 * 模型
 */
class UsersModel extends Model {
	//字段映射
	protected $_map	= array(
		'name'	=> 'user_name',
		'pass'	=> 'password',
	);
	
	//自动验证
	protected $_validate	=	array(
		//array('user_name','/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]{4,24}+$/u','请认真填写用户名',1,'regex'),	
		array('password','/^.{6,24}$/','密码必须',1,'regex'),
		array('pass1','/^.{6,24}$/','确认密码必须',1,'regex'),		
		array('password','pass1','确认密码不一致',1,'confirm'),
		array('email','email','邮箱格式错误'),
		//array('verify','chkVerify','验证码错误',1,'callback'),
		array('user_name','require','用户名已经被使用','1','unique',3),
		array('email','require','邮箱已经被使用','1','unique',3),
	);
	
	//自动验证时要使用的函数函数，验证用户名是否存在
	public function chkVerify(){
		if ($_SESSION['verify'] != md5($_POST['verify'])) {		
			return false;		
		}
		return true;			
	}
	
	//自动验证时要使用的函数函数，验证用户名是否存在
	/*public function chkName(){
		$map['user_name'] = $_POST['name'];
		if ($this->where($map)->find()) {
			return false;	
		}
		return true;
	} */
}
