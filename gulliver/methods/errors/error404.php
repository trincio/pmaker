<?
  session_start();
  if ( isset ( $_SESSION['phpFileNotFound'] ) )
    $uri =      $_SESSION['phpFileNotFound'];
  else
    $uri = 'undefined';
  $referer =  isset ( $_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
  
 $ERROR_TEXT = "404 Not Found ";
 $ERROR_DESCRIPTION = "
      Your browser (or proxy) sent a request
      that this server could not understand.<br />
      <br />
      <strong>Possible reasons: </strong><br />
      Your link is broken. This may occur when you receive
      a link via email, but your client software adds line breaks, thus distorting
      long URLs. <br />
      <br />
      The page you requested is no longer active. <br />
      <br />
      There is a typographic
      error in the link, in case you entered the URL into the browser's address
      toolbar.<br />
      <br />
      <br />
      <table>
      <tr><td><small>url</small></td>    <td><small>$uri</small></td></tr>
      <tr><td><small>referer</small></td><td><small>$referer</small></td></tr>
      </table>
  ";

  $fileHeader = PATH_GULLIVER_HOME . 'methods/errors/header.php' ;
  include ( $fileHeader);
?>
