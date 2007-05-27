<?php include('.Header.tpl.php'); ?>


<p><a name="SourceCode"></a><font size="2"><b>Source Code hosted at Google</b></font></p>
<div style="padding-left: 10px;">

<p>The source code is hosted in an SVN repository at google.<br>
You can find information on how to obtain a copy of the code <a target="_blank" href="http://code.google.com/p/firephp/source">here</a>.</p>
<p>If you are familiar with svn and google code hosting here is the checkout URL:</p>
<blockquote>
<i>svn checkout http://firephp.googlecode.com/svn/trunk</i>
</blockquote>
</div>

<p><a name="SourceCode"></a><font size="2"><b>Source code directory structure</b></font></p>
<div style="padding-left: 10px;">

<table border="1" cellpadding="3" cellspacing="0" style="border-collapse: collapse; border-color: #CCCCCC;">
  <tr>
    <td valign="top">/apache/</td>
    <td valign="top">Intended to be setup as an apache vhost used for development testing (contains this developer companion).</td>
  </tr>
  <tr>
    <td valign="top">/BareExtension/</td>
    <td valign="top">A simple Firefox extension used to test specific code without being wrapped by Firebug.</td>
  </tr>
  <tr>
    <td valign="top">/FirebugTestExtension/</td>
    <td valign="top">A simple Firefox extension illustrating how to <a target="_blank" href="http://www.firephp.org/Reference/Developers/ExtendingFirebug.htm">extend Firebug</a>.</td>
  </tr>
  <tr>
    <td valign="top"><b>/FirefoxExtension/</b></td>
    <td valign="top">The core FirePHP Firefox extension that builds on top of Firebug.</td>
  </tr>
  <tr>
    <td valign="top">/init/</td>
    <td valign="top">Intended to contain initialization code used during development.</td>
  </tr>
  <tr>
    <td valign="top">/PEARPackage/</td>
    <td valign="top">The core FirePHP PEAR package that provides all PHP-related server components.</td>
  </tr>
</table>
</div>

<?php include('.Footer.tpl.php'); ?>