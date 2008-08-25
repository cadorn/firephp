
FBL.ns(function() { with (FBL) {

// ************************************************************************************************

FirebugReps.PHPVariable = domplate(Firebug.Rep,
{
    className: "object",
    
    pinInspector: false,

    tag:
      A({
          class: "objectLink-PHPVariable",
          _repObject: "$object",
          onmouseover:"$onMouseOver",
          onmouseout:"$onMouseOut",
          onclick:"$onClick",
        },
        SPAN({class: "objectTitle"}, "$object|getTitle")
      ),
    
    onMouseOver: function(event) {
      
      this.pinInspector = false;

      FirePHP.showVariableInspectorOverlay(event.currentTarget.repObject);
    },
    
    onMouseOut: function() {

      if(this.pinInspector) return;
      
      FirePHP.hideVariableInspectorOverlay();
    },
    
    onClick: function(event) {

      this.pinInspector = true;
      
      FirePHP.showVariableInspectorOverlay(event.currentTarget.repObject,true);
    },
    
    getTitle: function(object) {
      
      if (object.constructor.toString().indexOf("Array") != -1 ||
          object.constructor.toString().indexOf("Object") != -1) {

        var count = 0;
        for (var key in object) {
          count++;
        }

        return 'Array('+count+')';
      
      } else {
        return object;
      }      
    },
    
    supportsObject: function(object, type)
    {
        return FirePHP.isLoggingData;
    }    
});

// ************************************************************************************************
Firebug.registerRep(
    FirebugReps.PHPVariable
);

}});


