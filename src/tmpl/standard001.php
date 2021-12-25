<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Module\SplideGhsvs\Site\Helper\SplideGhsvsHelper;

/* Collect configuration from plugin and json file.
An array of strings. */
$sliderConf = SplideGhsvsHelper::loadConfiguration($params);

// Load collected CSS files (found in plugin configuration and JSON file).
SplideGhsvsHelper::loadCss($sliderConf['loadCss']);

// Load the basic splide.min.js'.
SplideGhsvsHelper::loadJs($params);

// The id="..." of the main slider (outest container with class="splide".
$primId = 'primSlider_' . $module->id;

$inlineJs = SplideGhsvsHelper::loadInlineJs($sliderConf, $primId);
Factory::getDocument()->addScriptDeclaration($inlineJs);

$class = basename(__FILE__, '.php');

// Optional. A monster string ;-)
$class .= ' ' . str_replace('/', '_', $sliderConf['configFile']);
?>
<div class="mod_splideghsvs_container <?php echo $class; ?>">
	<div id="<?php echo $primId; ?>" class="splide">
		<div class="splide__track">
			<ul class="splide__list">
				<?php foreach ($slides as $key => $slide)
				{ ?>
				<li class="splide__slide">
					<img src="<?php echo $slide->foto; ?>">
					<div>
					<h4>Lorem ipsum</h4>
					</div>
				</li>
				<?php
				} ?>
			</ul>
		</div>
	</div>
</div>
