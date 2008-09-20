<?php

$firephp = FirePHP::getInstance(true);


$array = array();
$array['html1'] = '<p>Test Paragraph</p>';
$array['html2'] = '<p>Test Paragraph</p>'."\n".'<p>Another paragraph on a new line</p>';
$array['html3'] = '<p>jhgjhgf ghj hg hgfhgfh hgjvhgjfhgj h hgfhjgfhjg ghhgjfghf hgfhgfhgfhg hgfhgfhgf hgfhgjftfitf yt76i f tf76t67r76 7 76f7if 6f67f i76ff</p>';

$firephp->fb($array);

