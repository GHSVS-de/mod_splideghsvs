<?php
defined('_JEXEC') or die;

$myId = 'image-slider-' . $module->id;

$Config = [
	'type' => (string) $config->get('type', 'loop'),
	'speed' => (int) $config->get('speed', 4000),
	'interval' => (int) $config->get('interval', 7000),
	//'rewind' => (bool) $config->get('rewind', 1),
	//'width' => (string) $config->get('width', '100%'),
	'arrows' => (bool) $config->get('arrows', 1),
	'pagination' => (bool) $config->get('pagination', 0),
	'autoplay' => (bool) $config->get('autoplay', 0),
	'perPage' => (int) $config->get('perPage', 3),
	'perMove' => (int) $config->get('perPage', 1),
	'pauseOnHover' => (bool) $config->get('pauseOnHover', 1),
	//'padding' => (string) $config->get('padding', 0),
	//'keyboard' => (bool) $config->get('keyboard', 0),
	'gap' => (string) $config->get('gap', '0px'),
	'focus' => 'center',
	'breakpoints' => [
		991.998 => [
			'perPage' => 2
		],
		713 => [
			'perPage' => 1,
			'gap' => '0px'
		],
	],
];

$js =
"document.addEventListener( 'DOMContentLoaded', function () {"
	. "new Splide('#" . $myId . "', " . json_encode($Config) . " ).mount();});";

$wa->addInlineScript($js);
?>
<div class="d-block">
<div id="<?php echo $myId; ?>" class="splide">
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
				<img class="w-100ssss mx-autoss d-blockss" src="<?php echo $slide->foto; ?>" alt=""<?php echo $wh; ?>>
			</li>
<?php
} ?>
		</ul><!--/splide__list-->
  </div><!--/splide__track-->
</div><!--/#<?php echo $myId; ?>-->
</div>
