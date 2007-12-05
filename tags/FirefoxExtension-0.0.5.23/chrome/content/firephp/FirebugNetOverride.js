

/* Overrides code in: /Firebug/chrome/content/firebug/net.js */


FBL.ns(function() { with (FBL) {


/* Only override the net code for firebug version 1.01 */

if(top.Firebug.version=='1.05') {
	
			
const binaryCategoryMap =
{
    "image": 1,
    "flash" : 1
};
		
		
Firebug.NetMonitor.NetInfoBody = domplate(Firebug.NetMonitor.NetInfoBody,
{
    tag:
        DIV({class: "netInfoBody", _repObject: "$file"},
            DIV({class: "netInfoTabs"},
                A({class: "netInfoParamsTab netInfoTab", onclick: "$onClickTab",
                    view: "Params",
                    $collapsed: "$file|hideParams"},
                    $STR("URLParameters")
                ),
                A({class: "netInfoHeadersTab netInfoTab", onclick: "$onClickTab",
                    view: "Headers"},
                    $STR("Headers")
                ),
                A({class: "netInfoPostTab netInfoTab", onclick: "$onClickTab",
                    view: "Post",
                    $collapsed: "$file|hidePost"},
                    $STR("Post")
                ),
                A({class: "netInfoResponseTab netInfoTab", onclick: "$onClickTab",
                    view: "Response",
                    $collapsed: "$file|hideResponse"},
                    $STR("Response")
                ),
                A({class: "netInfoServerTab netInfoTab", onclick: "$onClickTab",
                    view: "Server",
                    $collapsed: "$file|hideResponse"},
                    "Server"
                )
            ),
            TABLE({class: "netInfoParamsText netInfoText netInfoParamsTable",
                    cellpadding: 0, cellspacing: 0}, TBODY()),
            TABLE({class: "netInfoHeadersText netInfoText netInfoHeadersTable",
                    cellpadding: 0, cellspacing: 0},
                TBODY(
                    TR({class: "netInfoResponseHeadersTitle"},
                        TD({colspan: 2},
                            DIV({class: "netInfoHeadersGroup"}, $STR("ResponseHeaders"))
                        )
                    ),
                    TR({class: "netInfoRequestHeadersTitle"},
                        TD({colspan: 2},
                            DIV({class: "netInfoHeadersGroup"}, $STR("RequestHeaders"))
                        )
                    )
                )
            ),
            DIV({class: "netInfoPostText netInfoText"},
                TABLE({class: "netInfoPostTable", cellpadding: 0, cellspacing: 0},
                    TBODY()
                )
            ),
            DIV({class: "netInfoResponseText netInfoText"}, 
                $STR("Loading")
            ),
            DIV({class: "netInfoServerText netInfoText"}, 
                $STR("Loading")
            )            
        ),
    updateInfo: function(netInfoBox, file, context)
    {
        var tab = netInfoBox.selectedTab;
        if (hasClass(tab, "netInfoParamsTab"))
        {
            if (file.urlParams && !netInfoBox.urlParamsPresented)
            {
                netInfoBox.urlParamsPresented = true;
                this.insertHeaderRows(netInfoBox, file.urlParams, "Params");
            }
        }
        
        if (hasClass(tab, "netInfoHeadersTab"))
        {
            if (file.responseHeaders && !netInfoBox.responseHeadersPresented)
            {
                netInfoBox.responseHeadersPresented = true;
                this.insertHeaderRows(netInfoBox, file.responseHeaders, "Headers", "ResponseHeaders");
            }

            if (file.requestHeaders && !netInfoBox.requestHeadersPresented)
            {
                netInfoBox.requestHeadersPresented = true;
                this.insertHeaderRows(netInfoBox, file.requestHeaders, "Headers", "RequestHeaders");
            }
        }

        if (hasClass(tab, "netInfoPostTab"))
        {
            var postTextBox = getChildByClass(netInfoBox, "netInfoPostText");
            if (!netInfoBox.postPresented)
            {
                netInfoBox.postPresented  = true;

                var text = getPostText(file);
                if (text)
                {
                    if (isURLEncodedFile(file, text))
                    {
                        var lines = text.split("\n");
                        var params = parseURLEncodedText(lines[lines.length-1]);
                        this.insertHeaderRows(netInfoBox, params, "Post");
                    }
                    else
                    {
                        var postText = formatPostText(text);
                        if (postText)
                            insertWrappedText(postText, postTextBox);
                    }
                }
            }
        }
        
        if (hasClass(tab, "netInfoResponseTab") && file.loaded && !netInfoBox.responsePresented)
        {
            netInfoBox.responsePresented = true;

            var responseTextBox = getChildByClass(netInfoBox, "netInfoResponseText");
            if (file.category == "image")
            {
                var responseImage = netInfoBox.ownerDocument.createElement("img");
                responseImage.src = file.href;
                responseTextBox.replaceChild(responseImage, responseTextBox.firstChild);
            }
            else if (!(file.category in binaryCategoryMap))
            {
                var text = file.responseText
                    ? file.responseText
                    : context.sourceCache.loadText(file.href);
                
                if (text)
                    insertWrappedText(text, responseTextBox);
            }
        }
				
        if (hasClass(tab, "netInfoServerTab") && file.loaded && !netInfoBox.serverPresented)
        {
            netInfoBox.serverPresented = true;

            var responseTextBox = getChildByClass(netInfoBox, "netInfoServerText");

						var data = '';
						var mask = '';
						var name = '';
						
						for( var index in file.responseHeaders ) {
							
							name = file.responseHeaders[index].name.toLowerCase();
							
							if(name=='firephp-data') {
								data += file.responseHeaders[index].value;
							} else
							if(name.substr(0,13)=='firephp-data-') {
								data += file.responseHeaders[index].value;
							} else							
							if(name=='firephp-rendererurl' || name=='firephp-mask') {
								/* Ensure that mask is from same domain as file for security reasons */
								if(FirebugLib.getDomain(file.href) == FirebugLib.getDomain(file.responseHeaders[index].value)) {
									mask = file.responseHeaders[index].value;
								}
							}
						}
						
						var hash = hex_md5(file.href);
						
						if(top.FirePHP.isURIAllowed(FirebugLib.getDomain(file.href))) {
							if(data) {
		            parseAndPrintData(data, mask, responseTextBox,netInfoBox.ownerDocument,hash);
							} else {
								responseTextBox.innerHTML = '"FirePHP-Data" response header not found in request response! Visit <a target="_blank" href="http://www.firephp.org/">http://www.firephp.org/</a> for more information.';
							}
						} else {
							responseTextBox.innerHTML = '<p>FirePHP is disabled for host <b>'+FirebugLib.getDomain(file.href)+'</b>.</p>'+
																					'<p>To enable FirePHP for this host <a onclick="top.FirePHP.enableSite(\''+FirebugLib.getDomain(file.href)+'\'); alert(\'FirePHP has been enabled for '+FirebugLib.getDomain(file.href)+' and will start working with the next request from this host!\');" href="#">click here</a>.</p>'+
																					'<p><font color="red"><b>WARNING:</b> FirePHP works by allowing a server script to insert code into your browser. <b>Only enable FirePHP for hosts you trust!</b> If enabled for a malicious host your browser may be hijacked!</font></p>'+
																					'<p>FirePHP is distributed subject to the Mozilla Public License on an "AS IS" basis,<br>'+
																					'<b>WITHOUT WARRANTY OF ANY KIND</b>, either express or implied. <b>USE AT YOUR OWN RISK</b>.<br>'+
																					'IN NO EVENT WILL ANY COPYRIGHT HOLDER OR ANY OTHER PARTY BE LIABLE TO YOU FOR DAMAGES.<br>'+
																					'By using FirePHP you agree to all terms of the Mozilla Public License.<br>'+
																					'You can view the License at <a target="_blank" href="http://www.mozilla.org/MPL/">http://www.mozilla.org/MPL/</a>.</p>';
																					
						}
        }				
    }	
});


function parseAndPrintData(Data, Mask, responseTextBox,doc,hash) {
        
	if(!Mask) {
		Mask = 'chrome://firephp/content/ServerNetPanelRenderer.js';
	}
	
	if(!document.FirePHPRenderer) {
		document.FirePHPRenderer = function() {
			var initialized = false;
			return {
				_Init: function() {
					if(this.initialized) return;
					this.Init();
					this.initialized = true;
				},
				Init : function() {
				}
			}
		}();
	}
	
	var context = {document:doc,window:doc.defaultView,key:'k'+hash,FirePHPRenderer:document.FirePHPRenderer};
	
	jQuery.ajax({
		type: "GET",
		url: Mask,
		success: function(ReturnData){
			context.html = '';
			context.data = Data;
			with(context) {
				eval(ReturnData);
			}
			responseTextBox.innerHTML = context.html;
			context.FirePHPRenderer._Init();
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			if(Mask.substr(0,9)=='chrome://') {
				context.html = '';
				context.data = Data;
				with(context) {
					eval(XMLHttpRequest.responseText);
				}
				responseTextBox.innerHTML = context.html;
				context.FirePHPRenderer._Init();
			} else {
				responseTextBox.innerHTML = 'Error[1] loading mask from: '+Mask;
			}
		}
	});
}



function insertWrappedText(text, textBox)
{
    var reNonAlphaNumeric = /[^A-Za-z_$0-9'"-]/;

    var html = [];
    var wrapWidth = Firebug.textWrapWidth;
        
    var lines = splitLines(text);
    for (var i = 0; i < lines.length; ++i)
    {
        var line = lines[i];
        while (line.length > wrapWidth)
        {
            var m = reNonAlphaNumeric.exec(line.substr(wrapWidth, 100));
            var wrapIndex = wrapWidth+ (m ? m.index : 0);
            var subLine = line.substr(0, wrapIndex);
            line = line.substr(wrapIndex);

            html.push("<pre>");
            html.push(escapeHTML(subLine));
            html.push("</pre>");
        }

        html.push("<pre>");
        html.push(escapeHTML(line));
        html.push("</pre>");
    }

    textBox.innerHTML = html.join("");    
}

}

}});


