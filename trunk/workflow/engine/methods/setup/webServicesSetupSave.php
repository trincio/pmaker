<?

$_SESSION['END_POINT']=$_POST['form']['WS_PROTOCOL'].'://' .$_POST['form']['WS_HOST'] . ':' .$_POST['form']['WS_PORT'] .'/sys' .$_POST['form']['WS_WORKSPACE'].'/en/green/services/wsdl';
G::header('location: webServices');

?>