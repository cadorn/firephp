<?php include('../.Init.php');
      include('.Header.tpl.php'); ?>

<p><font size="2"><b>Idea</b></font></p>
<div style="padding-left: 10px;">
<p>This developer companion is designed to work alongside your application with minimal requirements to get it running. The idea is that you can use the tools provided to debug and improve your FirePHP integration.</p>
</div>

<p><font size="2"><b>Requirements</b></font></p>
<div style="padding-left: 10px;">
<p>This companion has been tested with PHP5 and the Apache webserver.</p>
</div>

<p><font size="2"><b>Download</b></font></p>
<div style="padding-left: 10px;">
<p>The companion is available via SVN only at this time. It is located at:<br>
<a target="_blank" href="http://firephp.googlecode.com/svn/trunk/apache/">http://firephp.googlecode.com/svn/trunk/apache/</a></p>
</div>

<p><font size="2"><b>Install</b></font></p>
<div style="padding-left: 10px;">
<p>I am assuming you are using Apache as your webserver. If you are using a different webserver you can translate the instructions below.</p>
<p>This companion reveals internal information about your FirePHP integration and should be protected from public access. Only developers that work on integrating FirePHP into applications need access to this companion. To avoid unauthorized access you can limit requests to specific IP's as well as prompt the user for a password using the Apache basic authentication features.</p>
<p>The companion must run on the same domain as your application. You can place the code for the companion anywhere and setup an alias from your application vhost.</p>
<p>A typical apache configuration looks something like this:</p>

<div style="margin: 10px; padding: 5px; padding-left: 15px; padding-right: 15px; background-color:#F9F9F9; overflow: auto;"><pre style="font-family: Courier;">
<?php ob_start(); ?>
<VirtualHost *:80>
  ServerName www.YourDomain.com
  ServerAlias www.YourDomain.org YourDomain.com
  DocumentRoot /www/www.YourDomain.com
  
  Alias /FirePHPCompanion/  [CompanionPath]

  <Directory [CompanionPath]>

    AuthName "FirePHP Developer Companion"
    AuthUserFile /etc/httpd/conf/.passwd
    AuthType Basic

    <Limit GET POST>
      order deny,allow
      deny from all

      allow from 10.0.0.1

      require valid-user
    </Limit>
  </Directory>

  CustomLog /wwwlogs/apache/svn.cadorn.com combined
  ErrorLog /wwwlogs/apache/svn.cadorn.com.err
</VirtualHost>
<?php print htmlentities(ob_get_clean()); ?></pre></div>
<p>You need to replace <i>[CompanionPath]</i> with the path to your working copy of the companion source (e.g. /php/lib/FirePHPCompanion/). Update the IP and add more if needed. Use the <i>htpasswd</i> command to create a password file for a username you choose.</p>
<p>After restarting Apache you should be able to access the companion at <i>http://www.YourDomain.com/FirePHPCompanion/</i> depending on the alias you have setup.</p>
</div>

<p><font size="2"><b>Available Tools</b></font></p>
<div style="padding-left: 10px;">
<ul style="margin-left: 20px; padding-left: 0px;">
  <li><a href="RequestHeaders.php#YourRequestHeaders">Request Header Verification</a> - Shows all request headers sent by the client including the value of your <i>FirePHP-AccessKey</i>. There is also an informational message that will indicate if everything is in order.</li>
</ul>
</div>


<?php include('.Footer.tpl.php'); ?>