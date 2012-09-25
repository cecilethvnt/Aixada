<?php 
	
$slash = explode('/', getenv('SCRIPT_NAME'));
$app = getenv('DOCUMENT_ROOT') . '/' . $slash[1] . '/';

require_once($app . 'php/inc/cookie.inc.php');
require_once($app . 'local_config/config.php');
require_once($app . 'php/utilities/general.php');

require_once($app . 'FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);

$default_theme = get_session_theme();
$dev = configuration_vars::get_instance()->development;
$tpl_print_orders = configuration_vars::get_instance()->print_order_template;
$tpl_print_myorders = configuration_vars::get_instance()->print_my_orders_template;
$tpl_print_bill = configuration_vars::get_instance()->print_bill_template;


require_once($app . 'local_config/lang/' . get_session_language() . '.php');

//should be deleted in the end, and globally set. 
$_SESSION['dev'] = true;

   try {
       $cookie = new Cookie();
       $cookie->validate();
       if (isset($_SESSION['userdata']) 
	   and isset($_SESSION['userdata']['current_role']) 
	   and $_SESSION['userdata']['current_role'] !== false) {
	   $fp = configuration_vars::get_instance()->forbidden_pages;
	   $uri = $_SERVER['REQUEST_URI'];
	   $role = $_SESSION['userdata']['current_role'];
	   $forbidden = false;
	   foreach($fp[$role] as $page) {
	       if (strpos($uri, $page) !== false) {
		   $forbidden = true;
		   break;
	       }
	   }
	   if ($forbidden) {
	       $firephp->log('forbidden');
	       $firephp->log($uri, 'uri');
	       $firephp->log($role, 'role');
	       $firephp->log($_SESSION, 'session');
	       $firephp->log($_SERVER, 'server');
	       header("Location: index.php");
	   }
     }
     
   }   
   catch (AuthException $e) {
     echo("caught AuthException: $e");
     header("Location: login.php?originating_uri=".$_SERVER['REQUEST_URI']);
     exit;
   }

?>