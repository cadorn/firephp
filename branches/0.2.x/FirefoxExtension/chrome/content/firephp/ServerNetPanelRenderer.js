
function IsNumeric(sText)
{
  
  if(sText=='') return false;
  
   var ValidChars = "0123456789.";
   var IsNumber=true;
   var Char;
 
   for (i = 0; i < sText.length && IsNumber == true; i++) 
      { 
      Char = sText.charAt(i); 
      if (ValidChars.indexOf(Char) == -1) 
         {
         IsNumber = false;
         }
      }
   return IsNumber;
}

function countAttributes(obj) {
  var count = 0;
  if(!obj) return count;
  for (var key in obj) {
    count++;
  }
  return count;  
}

function str_repeat(str, repeat) {
  var output = '';
  for (var i = 0; i < repeat; i++) {
    output += str;
  }
  return output;
}


var MAX_DEPTH = 10;

var UniqueIndex = 0;

function print_r(RequestKey,obj, indent, depth) {
  var nl = '<br/>\n';
  var ws = '&nbsp;';
  var output = '';
  indent = (!indent) ? 0 : indent;
  depth = (!depth) ? 0 : depth;
  UniqueIndex = (!UniqueIndex) ? 0 : UniqueIndex;
  
  if (depth > MAX_DEPTH) {
    return str_repeat(ws, indent) + '<font color="orange">*Maximum Depth Reached*</font>'+nl;
  }
  if (typeof(obj) == "object") {
    
    if(indent == 0) {
      output += 'array(' + nl;
    }
    
    indent++;
    var child = '';
    var child_count = countAttributes(obj);
    var child_index = 0;
    for (var key in obj) {
      
      UniqueIndex++;
      
      try {
        child = obj[key];
      }
      catch (e) {
        child = '<font color="orange">*Unable To Evaluate*</font>';
      }
      
      if ( key == '__SKIP__' || child=='__SKIP__' ) {
        /* Skip */
      }
      else {
      
        output += '<div class="name" key="' + hex_md5(UniqueIndex + key) + '">';
        
        if (IsNumeric(key)) {
          output += str_repeat(ws, indent) + '[' + '<font color="green">' + key + '</font>' + '] => ';
        }
        else 
        if (typeof(key) == "string") {
          output += str_repeat(ws, indent) + '[' + '<font color="red">\'' + key + '\'</font>' + '] => ';
        }
        else {
          output += str_repeat(ws, indent) + '[' + key + '] => ';
        }
        
        output += '</div>';
        
        if (typeof(child) == "object") {
          indent++;

          output += '<div class="name" key="' + hex_md5(UniqueIndex + key) + '">';
          
          output += 'array(';
          output += '<div class="hide" id="' + RequestKey + hex_md5(UniqueIndex + key) + 'k">';
          var count = countAttributes(child);
          output += ' <font color="blue">... ' + count + ' element' + ((count > 1 || count == 0) ? 's' : '') + ' ...</font> )';
          output += '</div>';
          output += nl;
          
          output += '</div>';
          
          //        output += typeof(child) + nl;
          //        output += str_repeat(ws, indent) + '(' + nl;
          
          output += '<div id="' + RequestKey + hex_md5(UniqueIndex + key) + 'v">';
          
          output += print_r(RequestKey, child, indent, depth + 1);
          
          output += str_repeat(ws, indent) + ')' + nl;
          
          output += '</div>';
          indent--;
        }
        else 
        if (IsNumeric(child)) {
        
          output += '<div id="' + RequestKey + hex_md5(UniqueIndex + key) + 'v">';
          output += '<font color="green">' + child + '</font>';
          output += '</div>';
          output += '<div class="hide" id="' + RequestKey + hex_md5(UniqueIndex + key) + 'k">';
          output += '<div class="name" key="' + hex_md5(UniqueIndex + key) + '">';
          output += ' <font color="blue">...</font>';
          output += '</div>';
          output += '</div>';
          output += nl;
          
        }
        else 
        if (typeof(child) == "string") {
        
          output += '<div id="' + RequestKey + hex_md5(UniqueIndex + key) + 'v">';
          output += '<font color="red">\'' + child + '\'</font>';
          output += '</div>';
          output += '<div class="hide" id="' + RequestKey + hex_md5(UniqueIndex + key) + 'k">';
          output += '<div class="name" key="' + hex_md5(UniqueIndex + key) + '">';
          output += ' <font color="blue">...</font>';
          output += '</div>';
          output += '</div>';
          output += nl;
          
        }
        else {
        
          output += '<div id="' + RequestKey + hex_md5(UniqueIndex + key) + 'v">';
          output += child;
          output += '</div>';
          output += '<div class="hide" id="' + RequestKey + hex_md5(UniqueIndex + key) + 'k">';
          output += '<div class="name" key="' + hex_md5(UniqueIndex + key) + '">';
          output += ' <font color="blue">...</font>';
          output += '</div>';
          output += '</div>';
          output += nl;
        }
      }
      child_index++;
    }
    indent--;
    output += (indent == 0) ? ')' + nl : '';
    return output;
  }
  else {
    return str_repeat(ws, indent) + obj + nl;
  }
}


 
 /*
 * Variable: data      Contains data from FirePHP-Data header
 * Variable: html      Will be displayed in the panel
 * Variable: key       A unique hash for every request
 * Variable: FirePHPRenderer    The FirePHPRenderer object
 */

data = json_parse(data);

html = '<style>                                  '+
       '  #'+key+' DIV      { display: inline; } '+
       '  #'+key+' DIV.name { cursor:pointer;  } '+
       '  #'+key+' DIV.hide { display: none;   } '+
       '  #'+key+' DIV#header { display: block; border: 1px solid #D7D7D7; background-color: #D7D7D7; padding: 0px; padding-top: 2px; padding-left: 0px; margin-bottom: 10px; } '+
       '  #'+key+' DIV#header DIV { display: inline; cursor: pointer; background-color: white; margin-right: 1px; padding: 2px; padding-left: 5px; padding-right: 5px; font-family: Lucida Grande, Tahoma, sans-serif; font-weight: bold; color: #565656; } '+
       '  #'+key+' DIV#header DIV:hover { color: blue; } '+
       '  #'+key+' DIV#header DIV.selected { background-color: LightYellow; } '+
       '  #'+key+' DIV#body { display: block; min-height: 20px; } '+
       '  #'+key+' DIV#body.loading { margin-left: 10px; background: url(chrome://firebug/skin/loading_16.gif) no-repeat; } '+
       '  #'+key+' DIV#body.nodata { margin-left: 10px; color: gray; } '+
       '</style>                                 '+
       '<div id="'+key+'">                       ';
			 
html += '<div id="header"><div id="variables">Variables</div><div id="messages">Messages</div><div id="mod_rewrite">Mod Rewrite</div><div id="all">All</div></div>';
html += '<div id="body"></div>';

//html += print_r(key,data);
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
  $('#'+Key+' DIV#header DIV').bind("click", function(e) {
    $('#'+Key+' DIV#header DIV').removeClass('selected');
    $(this).addClass('selected');
    
    var body_value = null;
    
    switch($(this).attr('id')) {
      case 'variables':
        body_value = data['FirePHP.Dump'];
        break;
      case 'messages':
        body_value = data['FirePHP.Firebug.Console'];
        break;
      case 'mod_rewrite':
        body_value = data['org.apache.httpd.mod_rewrite'];
        break;
      case 'all':
        body_value = data;
        break;
    }
    
    var obj = $('#' + Key + ' DIV#body');
    obj.removeClass('nodata');
    obj.removeClass('loading');
    if (body_value) {
      obj.html('&nbsp;');
      obj.addClass('loading');
      setTimeout(function()
        {
          obj.html(print_r(Key, body_value));
          
          $('#'+Key+' DIV#body DIV.name').bind("click", function(e) {
            var obj = $('#'+Key+' DIV#body #'+Key+$(this).attr('key')+'k');
            obj.css('display',
                    (obj.css('display')=='none')?
                    'inline':'none');
            var obj = $('#'+Key+' DIV#body #'+Key+$(this).attr('key')+'v');
            obj.css('display',
                    (obj.css('display')=='none')?
                    'inline':'none');
          });
          
          obj.removeClass('loading');
        },100);
    } else {
      obj.addClass('nodata');
      obj.html('No Data Available');
    }
  });
  
  $('#'+Key+' DIV#header DIV#variables').click();
}
