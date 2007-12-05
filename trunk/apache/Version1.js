

html = '<div id="'+key+'-script-div"></div>'+data+'<br><br><p id="testP" onclick="yup(\''+key+'\')">Click Me!</p>';



FirePHPRenderer.Init = function() {

	document.yup = function(Key) {
		$(Key+'-script-div',document).innerHTML += 'Yes!!!';
	}

}

