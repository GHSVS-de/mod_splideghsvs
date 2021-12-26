<?php
\defined('_JEXEC') or die;

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

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Module\SplideGhsvs\Site\Helper\SplideGhsvsHelper;
use Joomla\CMS\HTML\HTMLHelper;

$opts = null;
$optsThumbs = null;
$sliderType = $params->get('sliderType', '');

$slides = SplideGhsvsHelper::getSlides($params);

if (is_object($slides) && count(get_object_vars($slides)))
{
	/* $imageResizer = SplideGhsvsHelper::getImageResizer();

	if ($imageResizer !== null)
	{
		$opts = \Bs3ghsvsItem::parseImageResizeOptions('w=450,quality=80,maxOnly=TRUE');

		if ($sliderType === 'synced')
		{
			$optsThumbs = \Bs3ghsvsItem::parseImageResizeOptions('w=120,quality=80,maxOnly=TRUE');
		}
	} */
}
else
{
	return;
}

SplideGhsvsHelper::loadCss($params);
SplideGhsvsHelper::loadJs($params);

require ModuleHelper::getLayoutPath('mod_splideghsvs', $params->get('layout', 'default'));
