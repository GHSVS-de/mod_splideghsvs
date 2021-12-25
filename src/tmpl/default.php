<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Module\SplideGhsvs\Site\Helper\SplideGhsvsHelper;

/* Don't remove this from the base layout file, e.g. from default.php or
mymoduleoverride.php. */
$currentLayoutFileName = basename(__FILE__, '.php');

$myId = $module->id;

if ($sliderType !== '')
{
	$path = ModuleHelper::getLayoutPath(
		'mod_splideghsvs', $currentLayoutFileName . '_' . $sliderType
	);
}


switch ($sliderType) :
	case 'synced':
		$path = ModuleHelper::getLayoutPath(
			'mod_splideghsvs', $currentLayoutFileName . '_' . $sliderType
		);


		require ModuleHelper::getLayoutPath(
			'mod_splideghsvs', $currentLayoutFileName . '_' . $sliderType
		);
		break;
	default:
		break;
endswitch;

// Load configured CSS files.
SplideGhsvsHelper::loadCss($params);

// Load the basic splide.min.js'.
SplideGhsvsHelper::loadJs($params);

// An array of object(s).
$sliderConf = SplideGhsvsHelper::loadConfiguration($params);
//echo ' 4654sd48sa7d98sD81s8d71dsa sliderConf <pre>' . print_r($sliderConf, true) . '</pre>';exit;

/*
$primarySlider = [
//'heightRatio' => 0.5,
'pagination' => false,
'arrows' => true,
'cover' => true,
'height' => 350,
'perPage' => 2,
'type' => 'loop',
'focus' => 'center',
'gap' => 5,
//'width' => 350,
//'autoWidth' => true
];

# Thumbnail slider.
$secondarySlider = [
'rewind' => true,
'fixedWidth' => 100,
'fixedHeight' => 64,
'isNavigation' => true,
'gap' => 5,
'focus' => 'center',
'pagination' => false,
'cover' => true,
];

$secondarySlider['breakpoints'] = [
	'600' => [
		'fixedWidth' => 66,
		'fixedHeight' => 40,
	]
];

$primarySlider = json_encode($primarySlider);

$secondarySlider = json_encode($secondarySlider, JSON_PRETTY_PRINT);
*/
#echo ' 4654sd48sa7d98sD81s8d71dsa <pre>' . print_r($secondarySlider, true) . '</pre>';exit;

$primId = 'primarySlider_' . $myId;
$secId = 'secondarySlider_' . $myId;

$inlineJs = ['document.addEventListener("DOMContentLoaded", function (){'];

// Create the main slider.
$inlineJs[] = 'var ' . $primId .' = new Splide("#' . $primId. '",'
	. $sliderConf['primarySlider'] . ')';

if (!isset($sliderConf['secondarySlider']))
{
	// Mount the main slider if no secondary.
	$inlineJs[] = '.mount();';
}
else
{
	// Create and mount the thumbnails slider.
	$inlineJs[] = ';var ' . $secId . ' = new Splide("#' . $secId . '",'
		. $sliderConf['secondarySlider'] . ').mount();';

	// Set the thumbnails slider as a sync target and mount the main slider.
	$inlineJs[] = $primId . '.sync('. $secId . ').mount();';
}

$inlineJs[] = '});';

Factory::getDocument()->addScriptDeclaration(implode("", $inlineJs));
?>
<div id="primarySlider_<?php echo $myId; ?>" class="splide">
	<div class="splide__track">
		<ul class="splide__list">
			<li class="splide__slide">
				<div class="splide__slide__container">
					<img src="images/Test1.jpg">
				</div>
				<div>
				<h4>Lorem ipsum</h4>
				</div>
			</li>
			<li class="splide__slide">
				<div class="splide__slide__container">
					<img src="images/Test1.jpg">
				</div>
				<div>
					<h4>Lorem ipsum 111</h4>
				</div>
			</li>
			<li class="splide__slide">
				<div class="splide__slide__container">
					<img src="images/Test1.jpg">
				</div>
				Lorem ipsum
			</li>
		</ul>
	</div>
</div>
