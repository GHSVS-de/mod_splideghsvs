<?php

namespace Joomla\Module\SplideGhsvs\Site\Helper;

\defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\Filesystem\File;

class SplideGhsvsHelper
{
	/**
	 * Retrieve list of slide items from module params.
	 *
	 * @param   \Joomla\Registry\Registry  &$params  module parameters
	 *
	 * @return  array
	 */
	public static function getSlides(&$params)
	{
		$resizePossible = false;

		$slides = $params->get('fotos');

		if (is_object($slides) && count(get_object_vars($slides)))
		{
			foreach ($slides as $key => $slide)
			{
				$slide->foto = trim($slide->foto);

				if ($slide->active !== 1 || $slide->foto === '')
				{
					unset($slides->$key);
					continue;
				}

				$slide->headline = trim($slide->headline);
				$slide->text = trim($slide->text);

				if ($slide->headline !== '')
				{
					$slide->headline = htmlspecialchars(
						$slide->headline, ENT_COMPAT, 'utf-8'
					);
				}

				if ($slide->text !== '')
				{
					$slide->text = nl2br(htmlspecialchars(
						$slide->text, ENT_COMPAT, 'utf-8'
					));
				}
			}
		}
		return $slides;
	}

	public static function getImageResizer()
	{
		$imageResizer = null;
		$resizeFile = JPATH_PLUGINS . '/system/bs3ghsvs/Helper/ImgResizeCache.php';

		if (is_file($resizeFile))
		{
			\JLoader::register('ImgResizeCache', $resizeFile);
			\JLoader::register('Bs3ghsvsItem',
				JPATH_PLUGINS . '/system/bs3ghsvs/Helper/ItemHelper.php');

			// use default settings
			$imageResizer = new \ImgResizeCache(new Registry);
		}

		return $imageResizer;
	}

	public static function loadCss($loadCss)
	{
		if ($loadCss)
		{
			foreach ($loadCss as $file)
			{
				HTMLHelper::_('stylesheet', $file, ['version' => 'auto']);
			}
		}
	}

	private static function collectCss(&$params, &$sliderConf)
	{
		$sliderConf['loadCss'] = [];

		// Can be '0'.
		if ($theme = $params->get('theme', 'themes/splide-default.min.css'))
		{
			$theme = HTMLHelper::_('stylesheet',
				'mod_splideghsvs/vendor/splidejs/splide/' . $theme,
				['pathOnly' => true, 'relative' => true]);

			if ($theme !== '')
			{
				$sliderConf['loadCss'][] = ltrim($theme, '/\\');
			}
		}

		if (isset($sliderConf['css']) && $sliderConf['css'] !== '')
		{
			$theme = HTMLHelper::_('stylesheet',
				'mod_splideghsvs/' . $sliderConf['css'],
				['pathOnly' => true, 'relative' => true]);

			if ($theme !== '')
			{
				$sliderConf['loadCss'][] = ltrim($theme, '/\\');
				unset($sliderConf['css']);
			}
		}

		if ($theme = trim($params->get('customCssFile', '')))
		{
			$theme = str_replace('$template',
				Factory::getApplication()->getTemplate(), $theme);
			$sliderConf['loadCss'][] = $theme;
		}
	}

	public static function loadJs()
	{
		HTMLHelper::_('script',
			'mod_splideghsvs/vendor/splidejs/splide/splide.min.js',
			['relative' => true, 'version' => 'auto']);
	}

	public static function loadConfiguration(&$params)
	{
		$sliderConf = (string) $params->get('sliderConf', '0');

		if ($sliderConf === '0')
		{
			$sliderConf = trim($params->get('sliderConfCustom', ''));
		}
		else
		{
			$sliderConf = 'media/mod_splideghsvs/json/sliderConfig/' . $sliderConf;
		}

		if ($sliderConf)
		{
			$extension = strtolower(
				substr($sliderConf, strlen($sliderConf) - 5, strlen($sliderConf)));
			$temp = File::stripExt($sliderConf);

			$sliderConf = JPATH_SITE . '/' . $sliderConf;

			if ($extension === '.json' && is_file($sliderConf))
			{
				$sliderConf = file_get_contents($sliderConf);

			if (($sliderConf = self::prepareConfig($sliderConf)) !== false)
			{
					$sliderConf['configFile'] = $temp;
					self::collectCss($params, $sliderConf);
				return $sliderConf;
			}
		}
		}

		return false;
	}

	public static function loadInlineJs($sliderConf, $primId, $secId = null)
	{
		$inlineJs = ['document.addEventListener("DOMContentLoaded", function (){'];

		// Create the main slider.
		$inlineJs[] = 'var ' . $primId .' = new Splide("#' . $primId. '",'
			. $sliderConf['primSlider'] . ')';

		if (!isset($sliderConf['secSlider']))
		{
			// Mount the main slider if no secondary.
			$inlineJs[] = '.mount();';
		}
		else
		{
			// Create and mount the thumbnails slider if exists.
			$inlineJs[] = ';var ' . $secId . ' = new Splide("#' . $secId . '",'
				. $sliderConf['secSlider'] . ').mount();';

			// Set the thumbnails slider as a sync target and mount the main slider.
			$inlineJs[] = $primId . '.sync('. $secId . ').mount();';
		}

		$inlineJs[] = '});';

		return implode("", $inlineJs);
	}

	private static function prepareConfig($sliderConf)
	{
		$config = [];

		if (substr($sliderConf, 0, 1) === '{')
		{
			$sliderConf = json_decode($sliderConf);

			if (json_last_error() === JSON_ERROR_NONE)
			{
				if (
					!is_object($sliderConf)
					|| !property_exists($sliderConf, 'primSlider')
					|| !is_object($sliderConf->primSlider)
				){
					return false;
				}

				$config['primSlider'] = json_encode($sliderConf->primSlider);

				// a bit clumsier
				if (property_exists($sliderConf, 'secSlider'))
				{
					$config['secSlider'] = json_encode($sliderConf->secSlider);
				}
				// a bit clumsier
				if (property_exists($sliderConf, 'css'))
				{
					$config['css'] = trim($sliderConf->css);
				}

				return $config;
			}
		}

		return false;
	}
}
