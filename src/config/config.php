<?php
$uglifyCss = new \Assetic\Filter\UglifyCssFilter('/home/crynick/node_modules/uglifycss/uglifycss');
$uglifyCss->setUglyComments(true);

$uglifyJs = new \Assetic\Filter\UglifyJs2Filter('/home/crynick/node_modules/uglify-js/bin/uglifyjs');

$JSqueeze = new \Assetic\Filter\JSqueezeFilter;
$JSqueeze->keepImportantComments(false);
$JSqueeze->setSpecialVarRx('(\$+[a-zA-Z_])[a-zA-Z0-9_$]*');

$lessFilter = new \Assetic\Filter\LessFilter("/usr/local/bin/node", ["/home/crynick/node_modules/"]);

return array(
	'filters' => array(
		'.min.js' => array(
		),
		'.min.css' => array(
			new \Assetic\Filter\CssRewriteFilter,
			
			$uglifyCss,
			
			new \Assetic\Filter\PhpCssEmbedFilter
		),
		'.js' => array(
			#$JSqueeze
			# $uglifyJs
		),
		'.less'	=> array(
			$lessFilter,
			$uglifyCss
		),
		'.css' => array(
			new \Assetic\Filter\CssRewriteFilter,
			$uglifyCss,
			new \Assetic\Filter\PhpCssEmbedFilter
			# new \Assetic\Filter\CssMinFilter
		),
	),
	
	/**
	 * directories relative to the app path
	*/
	'directories'	=> array(
		'javascripts'	=> 'assets/javascripts',
		'stylesheets'	=> 'assets/stylesheets'
	),
	
	/**
	 * build path relative to the public path
	*/
	'build_path'	=> 'builds',
);