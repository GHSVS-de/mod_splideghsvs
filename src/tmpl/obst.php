<?php
defined('_JEXEC') or die;

$myId = 'image-slider-' . $module->id;

$Config = [
	'type' => (string) $config->get('type', 'loop'),
	'speed' => (int) $config->get('speed', 4000),
	'interval' => (int) $config->get('interval', 7000),
	'rewind' => (bool) $config->get('rewind', 0),
	'width' => (string) $config->get('width', '100%'),
	'arrows' => (bool) $config->get('arrows', 0),
	'pagination' => (bool) $config->get('pagination', 0),
	'autoplay' => (bool) $config->get('autoplay', 1),
	'perPage' => (int) $config->get('perPage', 1),
	'pauseOnHover' => (bool) $config->get('pauseOnHover', 1),
	//'padding' => '1.5rem'
];

#echo json_encode($Config);exit;

$js =
"document.addEventListener( 'DOMContentLoaded', function () {"
	. "new Splide('#" . $myId . "', " . json_encode($Config) . " ).mount();});";

$wa->addInlineScript($js);
?>
<div class="p-1">
<div id="<?php echo $myId; ?>" class="splide mx-auto">
	<div class="splide__track">
		<ul class="splide__list">
<?php
foreach ($slides as $slide)
{
	$wh = '';
	$link = '';

	if ($slide->link)
	{
		$link = '<a href="' . $slide->link . '" target=_blank>';
	}

	if ($slide->width && $slide->height)
	{
		$wh = ' width=' . $slide->width . ' height=' . $slide->height;
	} ?>
			<li class="splide__slide"><?php echo $link; ?>
				<img class="w-100" src="<?php echo $slide->foto; ?>" alt=""<?php echo $wh; ?>>
			<?php echo $link ? '</a>' : ''; ?></li>
<?php
} ?>
		</ul><!--/splide__list-->
  </div><!--/splide__track-->
</div><!--/#<?php echo $myId; ?>-->
</div>
