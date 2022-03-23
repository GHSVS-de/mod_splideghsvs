<?php

namespace Joomla\Module\SplideGhsvs\Site\Helper;

\defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\Filesystem\File;

class SplideGhsvsHelper
{
	private static $loaded = [];

	/**
	 * Retrieve list of slide items from module params.
	 *
	 * @param   \Joomla\Registry\Registry  &$params  module parameters
	 *
	 * @return  array
	 */
	public static function getSlides(&$params)
	{
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

				$wurmInfos = HTMLHelper::_('cleanImageURL', $slide->foto);
				$slide->foto = $wurmInfos->url;
				$slide->width = $wurmInfos->attributes['width'];
				$slide->height = $wurmInfos->attributes['height'];

				$check = ['headline', 'text'];

				foreach ($check as $checkKey)
				{
					$slide->$checkKey = isset($slide->$checkKey) ?
						htmlspecialchars(trim($slide->$checkKey), ENT_COMPAT, 'utf-8') : '';
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

	public static function loadCss(&$params)
	{
		if ($loadCss = self::collectCss($params)['loadCss'])
		{
			foreach ($loadCss as $file)
			{
				if (!isset(self::$loaded['css'][$file]))
				{
					self::$loaded['css'][$file] = 1;
					HTMLHelper::_('stylesheet', $file,
						['version' => self::getMediaVersion()]);
				}
			}
		}
	}

	private static function collectCss(&$params, &$sliderConf = [])
	{
		$sliderConf['loadCss'] = [];

		if ($theme = $params->get('theme', 'splide-core.css'))
		{
			$theme = HTMLHelper::_('stylesheet',
				'mod_splideghsvs/splide/' . $theme,
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

		return $sliderConf;
	}

	public static function loadJs(&$params)
	{
		if (!isset(self::$loaded['js']))
		{
			self::$loaded['js'] = 'mod_splideghsvs/splide/splide.min.js';
			HTMLHelper::_('script', self::$loaded['js'],
				['relative' => true, 'version' => self::getMediaVersion()]);
		}
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
	public static function getMediaVersion()
	{
		if (!isset(self::$loaded['mediaVersion']))
		{
			if (!(self::$loaded['mediaVersion'] = file_get_contents(
				JPATH_SITE . '/media/mod_splideghsvs/mediaVersion.txt'))
			)
			{
				self::$loaded['mediaVersion'] = 'auto';
			}
		}

		return self::$loaded['mediaVersion'];
	}
}
