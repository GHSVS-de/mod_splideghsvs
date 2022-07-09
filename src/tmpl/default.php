<?php
defined('_JEXEC') or die;

$myId = 'image-slider-' . $module->id;

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
		autoplay: true,
		//mediaQuery: 'min',
		//breakpoints: {
			//720: {
				//perPage: 2,
			//},
		//}
  } ).mount();
} );
JS;
$wa->addInlineScript($js);
?>
<div id="<?php echo $myId; ?>" class="splide mx-auto">
	<div class="splide__track">
		<ul class="splide__list">
<?php
foreach ($slides as $slide)
{
	$wh = '';

	if ($slide->width && $slide->height)
	{
		$wh = ' width=' . $slide->width . ' height=' . $slide->height;
	} ?>
			<li class="splide__slide">
				<img class="w-100" src="<?php echo $slide->foto; ?>" alt=""<?php echo $wh; ?>>
			</li>
<?php
} ?>
		</ul><!--/splide__list-->
  </div><!--/splide__track-->
</div><!--/#<?php echo $myId; ?>-->
