<html>
<head>
  <title>FirePHP - Test Site</title>
  <link rel="stylesheet" href="/Style.css"></link>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>

<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" marginheight="0">

<center>

<table style="margin-top: 3px;" border="0" cellpadding="0" cellspacing="0" width="90%" height="98%">
  <tr>
    <td><img src="/images/PageBorder_NWC.gif" border="0" width="4" height="3"></td>
    <td align="left" background="/images/PageBorder_N.gif"><img src="/images/PageBorder_NW.gif" border="0" width="4" height="3"></td>
    <td><img src="/images/PageBorder_NEC.gif" border="0" width="10" height="3"></td>
  </tr>
  <tr>
    <td valign="top" background="/images/PageBorder_W.gif"><img src="/images/PageBorder_WN.gif" border="0" width="4" height="3"></td>
    <td nowrap bgcolor="#FFFFFF" valign="top" width="100%" height="100%">


<table border="0" width="100%" height="100%" cellspacing="0" cellpadding="0">
  <tr>
	<td>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
	<td bgcolor="#EEEEEE" rowspan="3" nowrap valign="bottom"><a href="/"><img src="/images/LadderMenuLogo.gif" with="74" height="77" border="0"></a></td>
    <td bgcolor="#EEEEEE" width="100%" style="height: 50px;">
    

<table border="0" width="100%" height="100%" cellspacing="0" cellpadding="0" style="margin-top: 0px; border-collapse: collapse" bordercolor="#FFFFFF">
  <tr>
	<td nowrap style="padding: 10px; font-family:verdana, arial, helvetica, sans-serif; font-size:14px; font-weight: bold;" bgcolor="#EEEEEE" valign="middle">
    Firefox Extension for PHP Development
	</td>
  <td nowrap width="100%" style="padding: 0px; font-family:verdana, arial, helvetica, sans-serif; font-size:11px" bgcolor="#EEEEEE" valign="middle" align="center">
    <script>
      if(navigator.userAgent.indexOf("Firefox")==-1) {
        document.write('<font color="red">Please use <b>Firefox</b><br>to view this site!</font>');
      }
    </script>
    <noscript>
      <font color="red">Please enable <b>JavaScript</b><br>to view this site!</font> 
    </noscript>
  </td>
  <td>
  	
  	
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
	<td nowrap style="padding-right: 20px; font-size: 16px; color:red; text-align: center;">Developer Companion</td>
  </tr>
  <tr>
	<td nowrap style="padding-right: 20px; text-align:center;"><font color="gray">for</font> &nbsp;&nbsp; Extension: 0.3.3 &nbsp;&nbsp; PHP API: 0.3.4</td>
  </tr>
</table>    
  
  
  
  </td>
  </tr>
</table>    
    
    </td>
  </tr>
  <tr>
	<td>
		<table width="100%" border="0" cellspacing="0" cellpadding="3" bordercolor="#FFFFFF" style="border-collapse: collapse">
			<tr>
				<td nowrap bgcolor="#000000" width="100%">&nbsp;</td>
			</tr>
		</table>	
	</td>
  </tr>
  <tr>
	<td style="height: 17px;">&nbsp;</td>
  </tr>
</table>  
				
	</td>
 </tr>			
 <tr>
	<td height="100%">		
				
<table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bgcolor="#FFFFFF">
  <tr>
  	<td style="text-align: center; padding-top: 5px; padding-bottom: 5px; font-weight: bold;">FirePHP Information &amp; Tools</td>
  	<td style="text-align: right; padding-right: 15px;">You are using version <b>0.1</b> (<b>BETA</b>) of the FirePHP Firefox Extension. <a target="_blank" href="http://www.firephp.org/Downloads/#LatestBuildRelease">Latest Version</a>: <b>0.3.4</b></td>
  </tr>  
  <tr>
	  <td valign="top" style="background-color: #ECECEC; padding:15px; padding-top: 5px;">
	  	<img src="/images/spacer.gif" width="170" height="1" border="0"><br>
	    <?php if(function_exists('run_LeftContentMenu')) {
	    		print run_LeftContentMenu();
	    	  } else { ?>
	    	  	<ul style="margin-left: 20px; padding-left: 0px;">
	    	  		<li><a target="content" href="/Pages/QuickstartIntegration.php">Quickstart Integration</a></li>
	    	  		<li><a target="content" href="/Pages/APIReference.php">API Reference</a></li>
	    	  		<li><a target="content" href="/Pages/DefaultAPIWrapper.php">Default API Wrapper</a></li>
	    	  	</ul>
	    	  	<p><b>In-Depth</b></p>
	    	  	<ul style="margin-left: 20px; padding-left: 0px;">
	    	  		<li><a target="content" href="/Pages/RequestHeaders.php">Request Headers</a></li>
	    	  		<li><a target="content" href="/Pages/ServerProcessing.php">Server Processing</a></li>
	    	  		<li><a target="content" href="/Pages/Responses.php">Responses</a></li>
	    	  		<ul style="margin-left: 20px; padding-left: 0px;">
	    	  		<li><a target="content" href="/Pages/ResponsesMultipart.php">Multipart</a></li>
	    	  		<li><a target="content" href="/Pages/ResponsesHeader.php">Header</a></li>
	    	  		<li><a target="content" href="/Pages/ResponsesSecondaryRequest.php">Secondary Request</a></li>
	    	  		</ul>
	    	  	</ul>
	    	  	<br><br><br>
	    	  	<p><b>Development</b></p>
	    	  	<ul style="margin-left: 20px; padding-left: 0px;">
	    	  		<li><a target="content" href="/Pages/SourceCode.php">Source Code</a></li>
	    	  		<li><a target="content" href="/Pages/DesiredContributions.php">Desired Contributions</a></li>
	    	  	</ul>
	    	  <?php } ?>
	  </td>
	<td width="100%" valign="top" height="100%" style="padding: 25px; padding-top: 15px; font-family: verdana, arial, helvetica, sans-serif; font-size: 11px">
    	<iframe name="content" src="/Pages/Welcome.php" style="width: 100%; height: 100%; border: none;" border="0"></iframe>