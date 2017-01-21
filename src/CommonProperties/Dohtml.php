<?php

namespace IPFLibraries\Properties\CommonProperties;

use IPFLibraries\Properties\CommonPropertyInterface;
use IPFLibraries\Properties\ConfigOption;
use IPFLibraries\Properties\DataType;

/**
 * Do HTML field type
 *
 * @package IPFLibraries\Properties\CommonVariables
 */
class Dohtml implements CommonPropertyInterface
{
	/**
	 * @inheritDoc
	 */
	public function parseValue($default)
	{
		return $default != 'notdefined' ? $default : 0;
	}

	/**
	 * @inheritDoc
	 */
	public function getDataType()
	{
		return DataType::INTEGER;
	}

	/**
	 * @inheritDoc
	 */
	public function isRequired()
	{
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function getOtherConfig()
	{
		if (!defined('_CM_DOAUTOWRAP')) {
			icms_loadLanguageFile('core', 'comment');
		}
		return [
			ConfigOption::FORM_CAPTION => _CM_DOHTML,
			ConfigOption::MAX_LENGTH => null,
			'options' => '',
			'multilingual' => false,
			ConfigOption::FORM_DESC => '',
			'sortby' => false,
			'persistent' => true
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getControl()
	{
		return [
			'name' => 'yesno'
		];
	}

}