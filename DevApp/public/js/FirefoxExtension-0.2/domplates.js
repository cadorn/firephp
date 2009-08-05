
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
      IF('$__in__|hasLabel',
        'Label $label'
      ),
      DIV('$data')
    ),

  hasLabel: function(row)
  {
top.console.log(row);    
    
      return row.label;
  },
  
  getRowClass: function(meta)
  {
    return 'firephp-domplate-row firephp-domplate-row-' + meta.type ;
  },
});

};
			