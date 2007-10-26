

/* Overrides code in: /Firebug/chrome/content/firebug/net.js */


FBL.ns(function() { with (FBL) {

//alert(Firebug.NetMonitor.NetInfoBody.tag);		
		
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


//alert(file.href);
            var responseTextBox = getChildByClass(netInfoBox, "netInfoServerText");

						for( var index in file.responseHeaders ) {
							if(file.responseHeaders[index].name=='X-PINF-org.firephp-Data') {
		            parseAndPrintData(FirePHPLib.urlDecode(file.responseHeaders[index].value), responseTextBox);
							}
						}
            
        }				
    }	
});


function parseAndPrintData(Data,responseTextBox) {
        
        var parser = new DOMParser();
        var doc = parser.parseFromString(Data, "text/xml");
				
        if(doc) {
        
          var applicationID = null;
          var requestID = null;
          var anchor = null;
        
          var findPattern = "//firephp[attribute::version=\"0.2\"]/application";
          var nodes = document.evaluate( findPattern, doc, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null ); 
          if(nodes) {
            while (res = nodes.iterateNext()) {
              applicationID = res.getAttribute('id');
            }
          }
          findPattern = "//firephp[attribute::version=\"0.2\"]/application[attribute::id=\""+applicationID+"\"]/request";
          nodes = document.evaluate( findPattern, doc, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null ); 
          if(nodes) {
            while (res = nodes.iterateNext()) {
              requestID = res.getAttribute('id');
              anchor = res.getAttribute('anchor');
              if(!anchor) anchor = '';
            }
          }
          if(requestID) {

            var requestData = FirePHP.FirePHPRequestHandler.getData(requestID);
            /* Create the request data object if we dont already have one for this request */
            if(!requestData) {
              requestData = FirePHP.FirePHPRequestHandler.data[requestID] = new FirePHP.FirePHPRequestData();
              requestData.setRequestID(requestID);
            }
            requestData.setApplicationID(applicationID);
        
            var findPattern = "//firephp[attribute::version=\"0.2\"]/application[attribute::id=\""+applicationID+"\"]/request[attribute::id=\""+requestID+"\"]/data[attribute::type=\"html\"]";
            var node = document.evaluate( findPattern, doc, null, XPathResult.FIRST_ORDERED_NODE_TYPE , null ); 
            if(node) {
              if(node.singleNodeValue.textContent) {
                //requestData.setData(node.singleNodeValue.textContent);
              }
            }
            
            findPattern = "//firephp[attribute::version=\"0.2\"]/application[attribute::id=\""+applicationID+"\"]/request[attribute::id=\""+requestID+"\"]/variable";
            nodes = document.evaluate( findPattern, doc, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE , null ); 
            if(nodes) {
							var html = "";

//							html = html + '<script type="application/x-javascript" src="chrome://firephp/content/ServerNetPanelRenderer.js"/>';
							
							//html = html + '<script type="application/x-javascript">';
							
							//html = html + '</script>';
							
//							dump(html);
							
//							responseTextBox.innerHTML = html;
							
		    try {
		  
		      var callback =
		      {
		        success: function(Response) {
//					      	alert(Response.responseText);
							responseTextBox.innerHTML = Response.responseText;
									
					  },
		        failure: function(Response) {
							
							eval(Response.responseText);
							
							
              while (res = nodes.iterateNext()) {
								
								//html = html + "renderLine('[ "+res.getAttribute('label')+"]');\n";
								if(res.textContent) {
									setVar(res.textContent);
								//	html = html + "document.write('"+res.textContent+"');\n";
								}
              }
							
							responseTextBox.innerHTML = renderServerData();
					  },
		        argument: 'chrome://firephp/content/ServerNetPanelRenderer.js',
		        timeout: 5000,
		        scope: this
		      }
		  
		      YAHOO.util.Connect.asyncRequest('GET', 'chrome://firephp/content/ServerNetPanelRenderer.js', callback, null);
		  
		    } catch(err) {
		      /* The detection request failed. Lets try again as the request should not fail here */
		      dump('Error trying to detect FirePHPServer at ['+url+']. We will try again!');
		    }							
							
							
            }
          }
        }  	
  	
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

}});


