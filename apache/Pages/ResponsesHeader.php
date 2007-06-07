<?php include('../.Init.php');
      include('.Header.tpl.php'); ?>


<p><font size="2"><b>Overview</b></font></p>
<div style="padding-left: 10px;">
<p>The <i>Header</i> protocol option sends the intelligence data to the client via a response header called <i>X-PINF-org.firephp-Data</i>.</p>
</div>


<p><font size="2"><b>Pros</b></font></p>
<div style="padding-left: 10px;">
<ul style="margin-left: 20px; padding-left: 0px;">
  <li>Works with all types of responses without interfering with response parsing in Firefox</li>
  <li>All intelligence data is sent with the response, thus not requiring a second request</li>
</ul>
</div>

<p><font size="2"><b>Cons</b></font></p>
<div style="padding-left: 10px;">
<ul style="margin-left: 20px; padding-left: 0px;">
  <li>Requires output buffering on the server side as intelligence data is added as a header at the end of the request</li>
  <li>Intelligence data is not compressed (if mod_deflate or equivalent is used)</li>
  <li>Some firewalls limit the length of the response headers making this method of transfering the intelligence data not possible</li>
</ul>
</div>

<?php include('.Footer.tpl.php'); ?>