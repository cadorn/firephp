<?php include('../.Init.php');
      include('.Header.tpl.php'); ?>

<p>To customize the Defaut API Wrapper initialize it with the following method:</p>

<div style="padding: 5px; padding-left: 15px; background-color: #EEEEEE;">void FirePHP::<b>Init</b> ( [ array $Options ] )</div>

<div style="padding: 15px;">
<table border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td style="padding-right: 15px; vertical-align: top;">$Options</td>
    <td style="vertical-align: top;">
      <p>Is specified in the format <b>array ( 'OptionName' => 'OptionValue' [, ... ] )</b></p>
      <p>The default options list is as follows:</p>
<?php ob_start(); ?>

array( 'ApplicationID' => 'Default',
       'RequestID' => md5(uniqid(rand(), true)),
       'AccessKeyValue' => $_COOKIE['FirePHP-AccessKey'],
       'InspectorTarget' => 'Default',
       'ContentType' => 'text/html',
       'ProtocolMode' => 'Header',
       'RegisterShutdown' => true,
       'StartContent' => true,
       'BufferOutput' => true,
       'SetCacheControlHeaders' => true,
       'DefaultVariables' =>
          array(array(true,array('REQUEST','$_GET'),$_GET),
                array(true,array('REQUEST','$_POST'),$_POST),
                array(true,array('REQUEST','$_COOKIE'),$_COOKIE),
                array(true,array('REQUEST','$_SERVER'),$_SERVER)
               )
     );

<?php
print highlight_string('<?php '."\n".ob_get_clean().'?>',true);
?>

      <p>There is no detailed documentation for these options available yet. You can check the <a target="_blank" href="http://www.firephp.org/SVNHighlightSource.php?file=/trunk/PEARPackage/FirePHP/Init.inc.php">wrapper source file</a> to see how the options are used.</p>

    </td>
  </tr>
</table>
</div>

<?php include('.Footer.tpl.php'); ?>