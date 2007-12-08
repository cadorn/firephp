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

FirePHPRenderer.Init = function() {
}

FirePHPRenderer.InitRequest = function(Key) {
	$('#'+Key+' DIV.name').bind("click", function(e) {
		var obj = $('#'+Key+' #'+Key+$(this).attr('key'));
		obj.css('display',
            (obj.css('display')=='none')?
						'inline':'none');
	});
}
