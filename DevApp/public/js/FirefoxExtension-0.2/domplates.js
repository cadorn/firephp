
with (domplate) {


function FirePHPLogger() {};


FirePHPLogger.prototype = domplate(
{
  target: null,
  
  setTarget: function(target)
  {
    this.target = target;
  },
  
  log: function(vars)
  {
    vars.meta.type = 'log';
    this.row.append(vars, this.target);			
  },

	row:
    DIV({'class':'$meta|getRowClass'},
      IF('if1','$label|hasLabel',
        'Hello World'
      )
    ),
  
  getRowClass: function(meta)
  {
    return 'firephp-domplate-row firephp-domplate-row-' + meta.type ;
  },
  
  hasLabel: function(label)
  {
    var items = [];
    items.push({dssd:'sdsdf'});
    return items;  
  },
  
  
	dump:
		TAG('$value|format',{value:'$value|parseParts'}),
			
	arrayTable:
		TABLE({border:1},
			TBODY(
        TR(
					TH({colspan:2},'array')
				),				
				FOR('x','$value',
					TR(
						TD({'class':'firephp-domplate-row'},'$x.name'),
						TD(
							TAG('$x.val|format',{value:'$x.val|parseParts'})
						)							
					)
				)
      )						
		),
				
	structTable:
		TABLE({border:1},
      TBODY(        
				TR(
					TH({colspan:2},'struct')
				),				
				FOR('x','$value',
					TR(
						TD('$x.name'),
						TD(
							TAG('$x.val|format',{value:'$x.val|parseParts'})
						)							
					)
				)
      )
		),
		
	simpleDiv:
		DIV('$value'),
		
	unknownDiv:
		DIV('$value|formatString'),
		
	formatString: function(value){
		return "unknown object type:" + value.toString();
	},
				
	format: function(value){						
		
		switch (this.dumpType(value)) {				
			case "array":				
				return this.arrayTable;
			case "simpleValue":
				return this.simpleDiv;	
			case "struct":
				return this.structTable;
			default:
				return this.unknownDiv;
		}					
							
	},
				
	parseParts: function(value) {
		
		var parts = [];
		var part;
				
		switch (this.dumpType(value)) {
			
			case "array":					
				for (var i=0; i < value.length; i++) {
					part = {name: i+1, val: value[i] };
					parts.push(part);
				}
				return parts;				
				
			case "simpleValue":
				return value;
				
			case "struct":
				for (var i in value) {
					if (i != "__cftype__") {
						part = {name: i, val: value[i] };
						parts.push(part);						
					}					
				}
				return parts;	
			
			default:
				return value;		
			
		}
	},
	
	dumpType: function(value) {				
		if (value instanceof Array) {				
			return "array";					
		} else if (typeof(value) == "object" && value.hasOwnProperty("__cftype__")) {
			return value.__cftype__;	
		} else if (typeof(value) == "string" || typeof(value) == "number" || typeof(value) == "boolean") {
			return "simpleValue";					
		} else {					
			return "unknown";					
		}			
	},

});

};
			