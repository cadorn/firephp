<?php

$firephp = FirePHP::getInstance(true);


$table = array();

$table[] = array('Column 1', 'Column 2', 'Column 3');

$table[] = array('Row 1 Col 1', 'Row 1 Col 2', 'Row 1 Col3');
$table[] = array('Row 2 Col 1', 'Row 2 Col 2', 'Row 2 Col3');

$row = array();
$row[] = '<p>Value for column 1</p>';
$row[] = 'This is a very long value for column 2. kjhsdgf ksd sadkfhgsadhfs adfjhksagdfkhjsadgf sakjhdfgasdhkfgsjhakdf jkhsadfggksadfg iweafiuwaehfiulawhef liawefiluhawefiuhwaeiufl iulhaweiuflhwailuefh iluwahefiluawhefuiawefh lwaieufhwaiulefhawef liawuefhawiluefhawfl';
$row[] = '<p>First paragraph</p>'."\n".'<p>Second paragraph</p>';
$table[] = $row;

$firephp->fb(array('This is the table label', $table),
             FirePHP::TABLE);

