<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use GHSVS\Module\SplideGhsvs\Site\Helper\SplideGhsvsHelper;

$mode = $params->get('mode', 'fotos');
$slides = false;
$modulePosition = '';

if ($mode === 'fotos')
{
	$slides = SplideGhsvsHelper::getSlides($params);
}
else if ($mode === 'modulePosition')
{
	// Das muss sein, da ModuleHelper auch welche mit leerer Position einliest.
	if ($modulePosition = trim($params->get('modulePosition', '')))
	{
		$slides = SplideGhsvsHelper::getModules($params);
	}
}

if ($slides === false)
{
	return;
}

// Lädt Moduleinstellungen mit Settings aus Form/config.xml.
$config = SplideGhsvsHelper::getConfig($params);
$wa = Factory::getDocument()->getWebAssetManager();
$wr = $wa->getRegistry()->addExtensionRegistryFile('mod_splideghsvs');
SplideGhsvsHelper::loadCss($params, $wa);
SplideGhsvsHelper::loadJs($params, $wa);

require ModuleHelper::getLayoutPath('mod_splideghsvs', $params->get('layout', 'default'));
