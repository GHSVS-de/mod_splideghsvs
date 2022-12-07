<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Module\SplideGhsvs\Site\Helper\SplideGhsvsHelper;

$slides = SplideGhsvsHelper::getSlides($params);

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
