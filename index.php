<?php
/**
 * index.php 文件单一入口
 */

if(!file_exists(dirname(__FILE__).'/cache/install.lock'))
{
    header('Location:/install.php');
    exit();
}

//调试模式：开发阶段设为开启true，部署阶段设为关闭false。
define('APP_DEBUG', false);

//URL模式: 0=>mca兼容模式，1=>s兼容模式，2=>REWRITE模式，3=>SEO模式。
define('URL_MODEL', '2');

//yzmphp根路径
define('YZMPHP_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR); 

//加载yzmphp框架的入口文件      
require(YZMPHP_PATH.'yzmphp'.DIRECTORY_SEPARATOR.'yzmphp.php'); 

//创建应用
yzm_base::creat_app();