<?php

namespace Imponeer\Properties\Types;


use Imponeer\Properties\AbstractType;

class FileType extends AbstractType {

	/**
	 * File save path
	 *
	 * @var string
	 */
	public $path;

	/**
	 * Allowed mime types
	 *
	 * @var array
	 */
	public $allowedMimeTypes = 0;

	/**
	 * Max file size
	 *
	 * @var double|null
	 */
	public $maxFileSize = 1000000;

	/**
	 * Max image width
	 *
	 * @var null|int
	 */
	public $maxWidth = 500;

	/**
	 * Max image height
	 *
	 * @var null|int
	 */
	public $maxHeight = 500;

	/**
	 * Filename generator function
	 *
	 * @var null|callable
	 */
	public $filenameGenerator = null;

	/**
	 * Prefix for filename
	 *
	 * @var null|string
	 */
	public $prefix = null;

	/**
	 * @inheritDoc
	 */
	public function __construct(&$parent, $defaultValue, $required, $otherCfg) {
		if (!isset($otherCfg['prefix'])) {
			$parts = explode('//', str_replace(['icms_ipf_', 'mod_'], '', get_class($parent)));
			$otherCfg['prefix'] = $parts[count($parts) - 1];
			unset($parts);
		}
		if (!isset($otherCfg['path']) && defined('ICMS_UPLOAD_PATH')) {
			$otherCfg['path'] = ICMS_UPLOAD_PATH;
		}
		parent::__construct($parent, $defaultValue, $required, $otherCfg);
	}

	/**
	 * @inheritDoc
	 */
	public function isDefined() {
		return (isset($this->value['filename']) && !empty($this->value['filename']));
	}

	/**
	 * @inheritDoc
	 */
	public function getForDisplay() {
		return str_replace(array("&amp;", "&nbsp;"), array('&', '&amp;nbsp;'), @htmlspecialchars($this->value, ENT_QUOTES, _CHARSET));
	}

	/**
	 * @inheritDoc
	 */
	public function getForEdit() {
		return str_replace(array("&amp;", "&nbsp;"), array('&', '&amp;nbsp;'), @htmlspecialchars($this->value, ENT_QUOTES, _CHARSET));
	}

	/**
	 * @inheritDoc
	 */
	public function getForForm() {
		return str_replace(array("&amp;", "&nbsp;"), array('&', '&amp;nbsp;'), @htmlspecialchars($this->value, ENT_QUOTES, _CHARSET));
	}

	/**
	 * Set var from request
	 *
	 * @param mixed $key Key to read
	 *
	 * @throws PropertyIsLockedException
	 * @throws ValueIsNotInPossibleValuesListException
	 */
	public function setFromRequest($key) {
		if (is_array($key)) {
			$value = &$_FILES;
			foreach ($key as $k) {
				$value = &$value[$k];
			}
			$this->set($value);
		} else {
			$this->set($_FILES[$key]);
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function clean($value) {
		if (is_string($value)) {
			if (file_exists($value)) {
				return array(
					'filename' => $value,
					'mimetype' => $this->getFileMimeType($value),
				);
			}
			$uploader = new icms_file_MediaUploadHandler($this->path, $this->allowedMimeTypes, $this->maxFileSize, $this->maxWidth, $this->maxHeight);
			if ($uploader->fetchFromURL($value)) {
				if (is_callable($this->filenameGenerator)) {
					$filename = call_user_func($this->filenameGenerator, 'post', $uploader->getMediaType(), $uploader->getMediaName());
					if (!empty($this->prefix)) {
						$filename = $this->prefix . $filename;
					}
					$uploader->setTargetFileName($filename);
				} elseif (!empty($this->prefix)) {
					$uploader->setPrefix($this->prefix);
				}
				if ($uploader->upload()) {
					return array(
						'filename' => $uploader->getSavedFileName(),
						'mimetype' => $uploader->getMediaType(),
					);
				}
				trigger_error(strip_tags($uploader->getErrors()), E_USER_NOTICE);
				return null;
			}
			return null;
		} elseif (isset($value['filename']) || isset($value['mimetype'])) {
			if (!isset($value['filename']) || !isset($value['mimetype'])) {
				return null;
			}
			return $value;
		}
		return null;
	}

	/**
	 * Gets mymetype from filename
	 *
	 * @param string $filename Filename
	 *
	 * @return string
	 */
	private function getFileMimeType($filename) {
		if (function_exists('finfo_open')) {
			$info = finfo_open(FILEINFO_MIME_TYPE);
			$rez = finfo_file($info, $filename);
			finfo_close($info);
			return $rez;
		}
		if (function_exists('mime_content_type')) {
			return mime_content_type($filename);
		}
		return 'unknown/unknown';
	}

}