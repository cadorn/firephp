/*
 * Variable: data      Contains data from FirePHP-Data header
 * Variable: html      Will be displayed in the panel
 * Variable: key       A unique hash for every request
 * Variable: FirePHPRenderer    The FirePHPRenderer object
 */

data = eval('(' + data + ')');

html = '<style>                                  '+
       '  #'+key+' DIV      { display: inline; } '+
       '  #'+key+' DIV.name { cursor:pointer;  } '+
       '  #'+key+' DIV.hide { display: none;   } '+
       '</style>                                 '+
       '<div id="'+key+'">                       ';
             
for( var index in data ) {
  html += '<div class="name" key="'+index+'">'+index+'</div>';
  html += '<div class="hide" id="'+key+''+index+'">         '+
          ' = ' + data[index]                                +
          '</div><br>                                       ';
}

html += '</div>';

/* 
 * Called once when FirePHP initializes
 */
FirePHPRenderer.Init = function() {
}

/* 
 * Called once for each request when "Server" tab is clicked
 */
FirePHPRenderer.InitRequest = function(Key) {
	Firebug.Console.log('sdfsdfsdfsdfsd');
  $('#'+Key+' DIV.name').bind("click", function(e) {
    
		$('#'+Key+' #'+Key+$(this).attr('key')).toggle();
    obj.css('display',
            (obj.css('display')=='none')?
            'inline':'none');
  });
}