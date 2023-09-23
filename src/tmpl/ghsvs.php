<?php
defined('_JEXEC') or die;

$myId = 'image-slider-' . $module->id;

$Config = [
	'type' => (string) $config->get('type', 'fade'),
	'interval' => (string) $config->get('interval', 7000),

	'speed' => (int) $config->get('speed', 800),
	'autoplay' => (bool) $config->get('autoplay', 1),
	'pauseOnHover' => (bool) $config->get('pauseOnHover', 1),
	'gap' => (string) $config->get('gap', '0px'),
	'arrows' => (bool) $config->get('arrows', 0),

	'rewind' => (bool) $config->get('rewind', 1),
	'width' => (string) $config->get('width', '100%'),

	'pagination' => (bool) $config->get('pagination', 0),

	'perPage' => (int) $config->get('perPage', 1),
	'perMove' => (int) $config->get('perPage', 1),

	//'padding' => (string) $config->get('padding', 0),
	//'keyboard' => (bool) $config->get('keyboard', 0),

	'focus' => 'center',
	/* 'breakpoints' => [
		991.998 => [
			'perPage' => 2
		],
		713 => [
			'perPage' => 1,
			'gap' => '0px'
		],
	], */
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
foreach ($slides as $key => $slide)
{
	$wh = '';
	$link = '';

	if ($slide->link)
	{
		$link = '<a href="' . $slide->link . '">';
	}

	if ($slide->width && $slide->height)
	{
		$wh = ' width=' . $slide->width . ' height=' . $slide->height;
	} ?>
			<li class="splide__slide slideOf-<?php echo $key; ?>">
				<?php echo $link; ?>
				<img src="<?php echo $slide->foto; ?>" alt=""<?php echo $wh; ?>>
				<?php echo $link ? '</a>' : ''; ?>

				<?php if ($slide->text)
				{ ?>
					<div class="slideText <?php echo $slide->cssClass; ?>">
						<?php echo $slide->text; ?>
					</div>
				<?php
				} ?>
			</li>
<?php
} ?>
		</ul><!--/splide__list-->
  </div><!--/splide__track-->
</div><!--/#<?php echo $myId; ?>-->
</div>
