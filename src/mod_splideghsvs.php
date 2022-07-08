<?php
defined('_JEXEC') or die;

if (version_compare(JVERSION, '4', 'lt'))
{
	JLoader::registerNamespace(
		'Joomla\\Module\\SplideGhsvs\\Site\\Helper',
		__DIR__ . '/src/Helper',
		false,
		false,
		'psr4'
	);
}

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Module\SplideGhsvs\Site\Helper\SplideGhsvsHelper;

$opts = null;
$optsThumbs = null;
$sliderType = $params->get('sliderType', '');

$slides = SplideGhsvsHelper::getSlides($params);

if (is_object($slides) && count(get_object_vars($slides)))
{
}
else
{
	return;
}

$wa = Factory::getDocument()->getWebAssetManager();
$wr = $wa->getRegistry()->addExtensionRegistryFile('mod_splideghsvs');
SplideGhsvsHelper::loadCss($params, $wa);
SplideGhsvsHelper::loadJs($params, $wa);

require ModuleHelper::getLayoutPath('mod_splideghsvs', $params->get('layout', 'default'));
