<?php include('../.Init.php');
      include('.Header.tpl.php'); ?>



<p><a name="Overview"></a><font size="2"><b>Overview</b></font></p>
<div style="padding-left: 10px;">
<p>An essential requirement of FirePHP is to be able to send additional intelligence data along with the standard server response without affecting how the browser renders the response. The intelligence data should only be sent if the browser supports it otherwise there will be unexpected results.</p>
</div>


<p><a name="RequestHeaders"></a><font size="2"><b>Request Headers</b></font></p>
<div style="padding-left: 10px;">
<p>The FirePHP server interaction starts with the Firefox browser. If FirePHP is enabled we want to indicate to the server that we accept FirePHP protocol compliant responses. To do this the extension appends <b>FirePHP/x.x.x</b> to the <b>User-Agent Header Field</b> as well as <b>text/firephp</b> to the <b>Accept Header Field</b>.</p>
<p>The request headers must also include a cookie called <b>FirePHP-AccessKey</b> which is used by the server code to ensure that the user requesting the intelligence information is authorized to receive it. You should set this cookie value to a unique MD5 hash (32 character string) using your own authentication system. The same key must be provided to the server API.</p>
<p>Once the header fields have been amended they should look something like this:</p>

<div style="margin: 10px; padding: 5px; padding-left: 15px; padding-right: 15px; background-color:#F9F9F9; overflow: auto;"><pre style="font-family: Courier;">
<?php ob_start(); ?>
User-Agent: Mozilla/5.0 (...) Gecko/20070309 Firefox/2.0.0.3 FirePHP/0.0.5
Accept: text/xml,text/html;q=0.9,text/plain;q=0.8,*/*;q=0.5, text/firephp
Cookie: FirePHP-AccessKey=j8938jghh383hh383ghihgkk93rd3kde;
<?php print htmlentities(ob_get_clean()); ?></pre></div>
<p>The directives are added to all requests (even image, css, etc...) as we may be using a PHP script to handle any request dynamically and may want to include intelligence data for all different types of requests.</p>


</div>

<p><a name="YourHeaders"></a><font size="2"><b>Your Headers for this Request</b></font></p>
<div style="padding-left: 10px;">
<?php
$found_agent = false;
$found_accept = false;
$found_key = false;
$headers = apache_request_headers();
ob_start();
print '<table border="0" cellpadding="2" cellspacing="0">';
foreach( $headers as $name => $value ) {
  $value = str_replace(';','; ',$value);
  $value = str_replace(',',', ',$value);
  if($name=='User-Agent' && ($offset = strpos($value,'FirePHP/'))!==false) {
    $end_offset = strpos($value,' ',$offset);
    if(!$end_offset) $end_offset = strlen($value);
    $value = substr($value,0,$offset).'<font color="orange"><b>'.substr($value,$offset,$end_offset-$offset).'</b></font>'.substr($value,$end_offset);
    $found_agent = true;
  }
  if($name=='Accept' && ($offset = strpos($value,'text/firephp'))!==false) {
    $value = substr($value,0,$offset).'<font color="orange"><b>'.substr($value,$offset,12).'</b></font>'.substr($value,$offset+12);
    $found_accept = true;
  }
  if($name=='Cookie' && ($offset = strpos($value,'FirePHP-AccessKey'))!==false) {
    $end_offset = strpos($value,';',$offset);
    if(!$end_offset) $end_offset = strlen($value);
    $value = substr($value,0,$offset).'<font color="orange"><b>'.substr($value,$offset,$end_offset-$offset).'</b></font>'.substr($value,$end_offset);
    $found_key = true;
  }
  print '<tr>';
  print '<td align="right" valign="top"><b>'.$name.'</b>:</td><td>'.wordwrap($value,80,' ',true).'</td>';
  print '</tr>';
}
print '</table>';
$headers_html = ob_get_clean();
?>

<?php if($found_agent && $found_accept && $found_key) { ?>
  <p><font color="green"><b>The approriate <i>User-Agent</i>, <i>Accept</i> and <i>Cookie</i> directives were found in the request headers!</b></font></p>
<?php } else { ?>
  <p><font color="red"><b>The approriate <i>User-Agent</i>, <i>Accept</i> and <i>Cookie</i> directives were NOT found in the request headers! You must install and/or enable the FirePHP extension and ensure the <i>FirePHP-AccessKey</i> cookie is set.</b></font></p>
<?php } ?>
<?php print $headers_html; ?>
</div>

<p><a name="FoundNotFoundInfo"></a><font size="2"><b>Found / Not Found - Whats the difference?</b></font></p>

<div style="padding-left: 10px;">

  <p>If <font color="green"><b>Found</b></font>:</p>
  <ul>
    <li>The server can send back a FirePHP response if it decides to do so. The server library must verify the FirePHP-AccessKey before sending the intelligence data as it may contain sensitive information that should not be sent to all users (Its usually only sent to developers). All request headers including cookies are available as usual to the server code so you can tie the FirePHP API into your authentication and security code.</li>
    <li>The FirePHP extension is enabled on the client and the client is requesting intelligence data to be sent along with the reponse if available.</li>
    <li>The client warrants that it will render the response as usual and will separate the FirePHP intelligence data to be displayed separately as long as the response adheres to the FirePHP protocol.</li>
  </ul>

  <p>If <font color="red"><b>NOT Found</b></font>:</p>
  <ul>
    <li>The FirePHP extension is NOT enabled on the client and NO FirePHP intelligence data should be sent along with the response. If data is sent along it may compromise the usual rendering of the page and may degrade or break the user experience.</li>
    <li>Most users of your site will likely not have the FirePHP extension installed so you should not send along any intelligence data as they cannot interpret it and it will increase your server bandwidth and may degrade the speed of your site if your intelligence data takes a lot of time to generate.</li>
  </ul>
</div>




<?php include('.Footer.tpl.php'); ?>