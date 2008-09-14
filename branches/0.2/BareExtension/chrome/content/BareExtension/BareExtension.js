
  
  
function StatusPanelClicked() {

  var idBareExtensionBrowser = top.document.getElementById('idBareExtensionBrowser');
  
  try {
  
    idBareExtensionBrowser.loadURIWithFlags('http://www.google.com/', Components.interfaces.nsIWebNavigation.LOAD_FLAGS_BYPASS_CACHE, null, null, null);
  
  } catch (err) {

    dump(err);
  
  }
  
//  alert(idBareExtensionBrowser);

}
