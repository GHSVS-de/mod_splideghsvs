<?php
namespace GHSVS\Module\SplideGhsvs\Site\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;
use Exception;

// client_id = 0 = site
// element = mod_countghsvs
// type = module

class VersionField extends FormField
{
	protected $type = 'Version';

	protected function getInput()
	{
		$version = (string) $this->element['extensionVersion'];
		return $version;
	}
}
