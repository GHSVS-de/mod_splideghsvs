<?php

namespace GHSVS\Module\SplideGhsvs\Site\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;

class SplideGhsvsHelper implements DatabaseAwareInterface
{
	use DatabaseAwareTrait;

	private static $loaded = [];

	private static $name = 'mod_splideghsvs';

	/**
	 * Retrieve list of slide items from module params. If mode=fotos.
	 *
	 * @param   \Joomla\Registry\Registry  &$params  module parameters
	 *
	 * @return  object of objects or FALSE
	 */
	public static function getSlides(&$params, $subform = 'foto')
	{
		$slides = $params->get($subform);

		if (\is_object($slides) && \count(get_object_vars($slides)))
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

				// B\C
				$check = ['link', 'text', 'cssClass'];

				foreach ($check as $checkKey)
				{
					$slide->$checkKey = isset($slide->$checkKey) ?
						trim($slide->$checkKey) : '';
				}

				$check = ['headline'];

				foreach ($check as $checkKey)
				{
					$slide->$checkKey = isset($slide->$checkKey) ?
						htmlspecialchars(trim($slide->$checkKey), ENT_COMPAT, 'utf-8') : '';
				}
			}

			if (\is_object($slides) && \count(get_object_vars($slides)))
			{
				return $slides;
			}
		}

		return false;
	}

	/**
	 * Retrieve list of slide items from module params. If mode=modulePosition.
	 *
	 * @param   \Joomla\Registry\Registry  &$params  module parameters
	 *
	 * @return  object of objects or FALSE
	 */
	public static function getModules(&$params)
	{
		$modulePosition = $params->get('modulePosition', '');
		$moduleOrdering = $params->get('moduleOrdering', 'default');
		$forceLayoutDefault = $params->get('forceLayoutDefault', 1);

		// ?? Mal sehen. Das betrifft die Einzel-Module, die gerendert werden.
		$attribs = [
			'style' => 'none',
			'contentOnly' => true,
			'layout' => 'kujmj4:boxen',
		];

		$output = [];
		$modules = ModuleHelper::getModules($modulePosition);

		foreach ($modules as $i => $module)
		{
			$tmp = new Registry($module->params);

			if ($forceLayoutDefault === 1)
			{
			$tmp->set('layout', 'default');
			}

			$module->params = $tmp->toString();
			$contentOnly = trim(ModuleHelper::renderModule($module, $attribs));

			if ($contentOnly)
			{
				$output[$i] = new \stdClass();
				$output[$i]->contentOnly = $contentOnly;
				$output[$i]->moduleId = $module->id;
				$output[$i]->module = $module;
			}
		}

		if ($output)
		{
			switch ($moduleOrdering)
			{
				case 'random':
					shuffle($output);
				break;
				case 'idAsc':
					$output = ArrayHelper::sortObjects($output, 'moduleId', 1);
				break;
				case 'idDes':
					$output = ArrayHelper::sortObjects($output, 'moduleId', -1);
				break;
			}
		}
		return (empty($output) ? false : $output);
	}

	public static function getSlidesForCustomLoad(&$params)
	{
		return true;
	}

	/**
	 * Retrieve slider configurations from module params.
	 *
	 * @param   \Joomla\Registry\Registry  &$params  module parameters
	 *
	 * @return  Registry object
	 */
	public static function getConfig(&$params)
	{
		$data = $params->get('config');

		if (\is_object($data) && \count(get_object_vars($data)))
		{
			return new Registry($data);
		}

		return new Registry();
	}

	public static function loadCss(&$params, $wa)
	{
		if ($theme = $params->get('theme', 'splide-core.css'))
		{
			// Strip extension.
			$assetName = substr($theme, 0, (strrpos($theme, ".")));
			$assetName = self::$name . '.' . str_replace('/', '.', $assetName);
			$wa->useStyle($assetName);
		}

		if ($loadCss = self::collectCss($params)['loadCss'])
		{
			foreach ($loadCss as $file)
			{
				if (!isset(self::$loaded['css'][$file]))
				{
					self::$loaded['css'][$file] = 1;
					HTMLHelper::_(
						'stylesheet',
						$file,
						['version' => self::getMediaVersion()]
					);
				}
			}
		}

		$wa->useStyle('mod_splideghsvs.override.template');
	}

	private static function collectCss(&$params, &$sliderConf = [])
	{
		$sliderConf['loadCss'] = [];

		if (isset($sliderConf['css']) && $sliderConf['css'] !== '')
		{
			$theme = HTMLHelper::_(
				'stylesheet',
				self::$name . '/' . $sliderConf['css'],
				['pathOnly' => true, 'relative' => true]
			);

			if ($theme !== '')
			{
				$sliderConf['loadCss'][] = ltrim($theme, '/\\');
				unset($sliderConf['css']);
			}
		}

		if ($theme = trim($params->get('customCssFile', '')))
		{
			$theme = str_replace(
				'$template',
				Factory::getApplication()->getTemplate(),
				$theme
			);
			$sliderConf['loadCss'][] = $theme;
		}

		return $sliderConf;
	}

	public static function loadJs(&$params, $wa)
	{
		$wa->useScript(self::$name . '.core');
	}

	public static function loadInlineJs($sliderConf, $primId, $secId = null)
	{
		$inlineJs = ['document.addEventListener("DOMContentLoaded", function (){'];

		// Create the main slider.
		$inlineJs[] = 'var ' . $primId . ' = new Splide("#' . $primId . '",'
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
			$inlineJs[] = $primId . '.sync(' . $secId . ').mount();';
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
					!\is_object($sliderConf)
					|| !property_exists($sliderConf, 'primSlider')
					|| !\is_object($sliderConf->primSlider)
				) {
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
			if (!(self::$loaded['mediaVersion'] =  json_decode(
				file_get_contents(
				JPATH_ROOT . '/media/' . self::$name . '/joomla.asset.json'
			)
			)->version)
			) {
				self::$loaded['mediaVersion'] = 'auto';
			}
		}

		return self::$loaded['mediaVersion'];
	}

	/*
		Datenbank-Helferlein für mode customLoad.
		Vorübergehende Krücke. Wenn ich mir z.B. ArticlesArchiveHelper.php ansehe, scheint
		bei moderner Modulstruktur setDatabase() nicht nötig.
	*/
	public function getDBO()
	{
		$this->setDatabase(Factory::getDbo());
		return $this->getDatabase();;
	}
}
