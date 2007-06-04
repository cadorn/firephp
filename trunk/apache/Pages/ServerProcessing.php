<?php include('../.Init.php');
      include('.Header.tpl.php'); ?>

<p><font size="2"><b>Intelligence Data</b></font></p>
<div style="padding-left: 10px;">
<p>FirePHP is all about sending intelligence data about your application and requests to the client. It is your responsibility to tell FirePHP what this intelligence data is using several methods of the server API.</p>
</div>

<p><font size="2"><b>Understanding the Overhead</b></font></p>
<div style="padding-left: 10px;">
<p>The FirePHP server code is loaded with every request by default (you can change that if you need to). Altough the core library is not large in size it helps a lot if you use an opcode cache such as APC which will also help your application a lot.</p>
<p>The library will only initialize and track the intelligence data if the client is requesting it (and only if the client is authorized). This means other than the time to load the code there is really not much processing overhead to enable FirePHP even for a live site.</p> 
<p>When the intelligence data should be sent the library tracks the data you set via the API methods and then sends it to the client at the end of the request. All data values are serialized and inserted into an XML envelope that the extension understands. This means that all data you tell FirePHP to send to the client is first stored in memory by the library on the server and then converted to ASCI text which is appended to the response. The more data you send the more memory is used and the longer it takes for the response to be received by the client.</p>
</div>

<p><font size="2"><b>Keeping things fast</b></font></p>
<div style="padding-left: 10px;">
<p>Keep your default intelligence data you send with every response (to FirePHP users) to a minimum. You don't have to be too stringent but it is likely that you don't need all data all the time. One great strategy is to setup debug or intelligence levels that you can set via a cookie or in the users's session. Provide a basic admin page that allows the user to change the level and on the server only set applicable intelligence data based on the level requested.</p>
<p>Make sure you don't try and track huge nested arrays nor arrays that have circular references to objects. This will cause problems when FirePHP converts them to serialized JSON strings and may lead to slow performance or cause an infinite loop. There will be protection available for this in future.</p>
<p>The data will be sent to the client faster if it is compressed. Since it is typically attached to the response or sent from the same webserver you just need to enable mod_deflate or equivalent which will also speed up the delivery of your site and reduce your bandwidth.</p>
</div>


<p><font size="2"><b>Headers and Output Buffering</b></font></p>
<div style="padding-left: 10px;">
<p>FirePHP must set a few response headers for the extension to be able to track the intelligence data for a request (see <a href="Responses.php">Responses</a>). As with any other headers, they must be set before any other output is sent. The easiest way to ensure this (the default FirePHP API Wrapper uses this by default) is to use output buffering which instructs PHP to store all output in memory and only send it once the script ends. While output buffering makes your job easier it does not always play nicely if your application makes use of output buffering or you want to send the response data to the client as your application executes without sending it all in one shot at the end. There are many pros and cons to output buffering and sending headers after content is a typical cause of the FirePHP extension not recognizing the intelligence data. If you have problems or questions about this feel free to post a message to <a href="mailto:FirePHP@googlegroups.com">FirePHP@googlegroups.com</a>.</p>
</div>



<?php include('.Footer.tpl.php'); ?>