/*
 * Variable: data      Contains data from FirePHP-Data header
 * Variable: html      Will be displayed in the panel
 * Variable: document  The document object for the panel
 * Variable: key       A unique hash for every request
 * Variable: FirePHPRenderer    The FirePHP object
 */

data = eval('(' + data + ')');

html = '<style>                         '+
       '  DIV      { display: inline; } '+
       '  DIV.name { cursor:pointer;  } '+
       '  DIV.hide { display: none;   } '+
       '</style>                        ';

for( var index in data ) {
  html += '<div class="name"                                '+
          '     onClick="showValue(\''+key+'-'+index+'\')"> '+
            index                                            +
          '</div>                                           ';
  html += '<div class="hide"                                '+
          '     id="'+key+'-'+index+'-value-div">           '+
          ' = ' + data[index]                                +
          '</div><br>                                       ';
}

FirePHPRenderer.Init = function() {
  document.showValue = function(Key) {
    var obj = $(Key+'-value-div',document);
    if(obj.style.display!='inline') {
      obj.style.display = 'inline';
    } else {
      obj.style.display = 'none';
    }
  }
}