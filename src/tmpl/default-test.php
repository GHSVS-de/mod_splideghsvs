<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Module\SplideGhsvs\Site\Helper\SplideGhsvsHelper;

$myId = 'image-slider-' . $module->id;
$doc = Factory::getDocument();
$js = <<<JS
document.addEventListener( 'DOMContentLoaded', function () {
  new Splide('#$myId', {
		type: 'fade',
		rewind: true,
		// Bei 'fade' die Zeit, die für die gesamte Überblendung verwendet wird.
		speed: 4000,
		interval: 7000,
		width: '100%',
		//height: 'auto',
		//fixedWidth: '100%',
		arrows: false,
		pagination: false,
		autoplay: true
  } ).mount();
} );
JS;
$doc->addScriptDeclaration($js);
?>
<div id="<?php echo $myId; ?>" class="splide mx-auto">
	<div class="splide__track">
		<ul class="splide__list">
<?php
foreach ($slides as $slide)
{ ?>
			<li class="splide__slide">
				<img class="w-100" src="<?php echo $slide->foto; ?>" alt="">
				<div>
					Description 01
				</div>
			</li>
<?php
} ?>
		</ul><!--/splide__list-->
  </div><!--/splide__track-->
</div><!--/#<?php echo $myId; ?>-->
