
data = eval('(' + data + ')');

html = '<div id="'+key+'-message-div"></div>';
html += '<p onclick="showMessage(\''+key+'\')">Click Me ['+key+']!</p>';
html += '<br>';

for( var index in data ) {
    html += index+" = "+data[index]+"<br>";
}

FirePHPRenderer.Init = function() {
	document.showMessage = function(Key) {
		$(Key+'-message-div',document).innerHTML = 'You clicked me :)';
	}
}

