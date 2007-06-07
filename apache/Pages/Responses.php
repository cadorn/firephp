<?php include('../.Init.php');
      include('.Header.tpl.php'); ?>

<p><font size="2"><b>Cache Control Headers</b></font></p>
<div style="padding-left: 10px;">
<p>By default FirePHP sets some cache control headers in the response to disable caching of the response in proxies and your browser. This ensures your intelligence data is always up to date and your request is processed by the server each time. If this is not a desirable mode of operation for you, the cache control headers can be disabled. See the <a href="DefaultAPIWrapper.php">Default API Wrapper</a> Reference.</p>
<p>The headers sent are:</p>

<div style="margin: 10px; padding: 5px; padding-left: 15px; padding-right: 15px; background-color:#F9F9F9; overflow: auto;"><pre style="font-family: Courier;">
<?php ob_start(); ?>
Last-Modified: <?php print gmdate('r', time()); ?> 
Expires: <?php print gmdate('r', time()-86400); ?> 
Pragma: no-cache
Cache-Control: no-cache, no-store, must-revalidate, max_age=0
Cache-Control: post-check=0, pre-check=0 
<?php print htmlentities(ob_get_clean()); ?></pre></div>
</div>



<p><font size="2"><b>Protocol Options</b></font></p>
<div style="padding-left: 10px;">
<p>FirePHP supports three different protocol options to send your intelligence data to the client. Each option has its pros and cons. By default the <i>Header</i> option is used which simply sets the intelligence data as a response header. There are obvious limitations to this (such as requiring output buffering and limited size) which is why the other options are available.</p>

<p><font size="2" color="red"><b>Note:</b></font></p>
<div style="padding-left: 10px;">
<p>At this time only the <i><b>Header</b></i> option is supported.</p>
</div>

</div>



<p><font size="2"><b>FirePHP Headers</b></font></p>
<div style="padding-left: 10px;">
<p>FirePHP assigns each request a unique ID wich is sent to the client via the <i>X-PINF-org.firephp-RequestID</i> response header. Depending on the protocol option used there may be additional headers sent.</p>
<p>The headers for a FirePHP response with the intelligence data sent with the <i>Header</i> option looks something like this:</p>
<div style="margin: 10px; padding: 5px; padding-left: 15px; padding-right: 15px; background-color:#F9F9F9; overflow: auto;"><pre style="font-family: Courier;">
<?php ob_start(); ?>
X-PINF-org.firephp-RequestID: cb8d3d8c72a45652733540ac2db56fc0
X-PINF-org.firephp-Data: [URL encoded intelligence data]
<?php print htmlentities(ob_get_clean()); ?></pre></div>
</div>


<?php include('.Footer.tpl.php'); ?>