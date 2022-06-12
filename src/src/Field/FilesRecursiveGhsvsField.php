<?php
/*
Rekursive Liste von Dateien in einem directory.
*/
namespace Joomla\Module\SplideGhsvs\Site\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\FilelistField;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\Path;

class FilesRecursiveGhsvsField extends FilelistField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.7.0
	 */
	protected $type = 'FilesRecursiveGhsvs';

	protected function getOptions()
	{
		$options = array();

		$path = $this->directory;

		if (!is_dir($path))
		{
			$path = JPATH_ROOT . '/' . $path;
		}

		$path = Path::clean($path);
		$files = Folder::files($path, $this->fileFilter, true, true);
		$parentOptions = parent::getOptions();
		$checker = array_flip(array_column($parentOptions, 'value'));

		if (\is_array($files))
		{
			foreach ($files as $file)
			{
				// Check to see if the file is in the exclude mask.
				if ($this->exclude)
				{
					if (preg_match(chr(1) . $this->exclude . chr(1), $file))
					{
						continue;
					}
				}

				// If the extension is to be stripped, do it.
				if ($this->stripExt)
				{
					$file = File::stripExt($file);
				}

				$file = ltrim(str_replace($path, '', $file), '/\\');

				if (isset($checker[$file]))
				{
					continue;
				}

				$options[] = HTMLHelper::_('select.option', $file, $file);
			}
		}

		$options = array_merge($parentOptions, $options);

		return $options;
	}
}
