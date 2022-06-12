<?php
# plg_system_onuserghsvs
$mainFinder = PhpCsFixer\Finder::create()
	->exclude('node_modules')
	->exclude('dist')
	->exclude('media')
	->in(
		[
			__DIR__,
		]

	);

$config = new PhpCsFixer\Config();

$phpCsFixerRules = require_once '../php-cs-fixer-ghsvs/.php-cs-fixer.rules.php';

$config
	->setRiskyAllowed(true)
	->setIndent("\t")
	->setRules($phpCsFixerRules)
	->setFinder($mainFinder);

return $config;
