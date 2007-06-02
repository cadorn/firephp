<?php include('../.Init.php');
      include('.Header.tpl.php'); ?>


<div style="padding: 5px; padding-left: 15px; background-color: #EEEEEE;">void FirePHP::<b>SetVariable</b> ( int $Options , mixed $Name , mixed $Value )</div>
<div style="padding: 15px;">
Sets the <i>$Value</i> for the given variable <i>$Name</i> in the intelligence data set that will be sent to the client at the end of the request.
</div>
<div style="padding: 15px; padding-top: 0px;">
<table border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td style="padding-bottom: 10px; padding-right: 15px; vertical-align: top;">$Options</td>
    <td style="padding-bottom: 10px; vertical-align: top;">Always set to <i>TRUE</i> for now.</td>
  </tr>
  <tr>
    <td style="padding-bottom: 10px; padding-right: 15px; vertical-align: top;">$Name</td>
    <td style="padding-bottom: 10px; vertical-align: top;">
      <p>Can be a string to specify the name of the variable that will appear in the variable list in the extension.</p>
      <p>You can also specify an array: <b>array ( string $Scope , string $Name )</b><br>
         Where the <i>$Scope</i> is one of <i>REQUEST</i>, <i>SESSION</i> or <i>APPLICATION</i> and specifies which variable list in the extension to place the variable in to. The <i>$Name</i> specifies the name displayed.</p>
      <p>The default scope is <i>REQUEST</i>. The scopes help you to seperate the variables for easier review and debugging in the extension and should be consistent with the lifecycle of the <i>$Value</i> in your application. It is your responsibility to re-send the variables each time they change.</p>         
    </td>
  </tr>
  <tr>
    <td style="padding-right: 15px; vertical-align: top;">$Value</td>
    <td style="vertical-align: top;">
      <p>Specifies the value of the variable to be sent to the extension. It can be any PHP type (string, integer, array, etc...) but keep in mind that it must be serializable and should not contain too much data as it will be sent over the internet to the client.</p>
      <p>The client will display any value sent but it may not have a nice renderer for the data you are sending. More renderers will be added in time.</p>
    </td>
  </tr>
</table>
</div>

<div style="padding: 15px;">
<p>Example:</p>

<?php ob_start(); ?>

FirePHP::SetVariable(true,array('SESSION','$_SESSION')),$_SESSION);

<?php
print highlight_string('<?php '."\n".ob_get_clean().'?>',true);
?>

</div>


<?php include('.Footer.tpl.php'); ?>