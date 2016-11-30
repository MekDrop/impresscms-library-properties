<?php
/**
 * Code required for common var of meta_description type
 *
 * @copyright   The ImpressCMS Project http://www.impresscms.org/
 * @license		https://opensource.org/licenses/MIT MIT
 * @author		mekdrop@impresscms.org
 */

$value = $default != 'notdefined' ? $default : '';
$this->initVar($varname, \ImpressCMS\Properties::DTYPE_STRING, $value, false, null, '', false, _CO_ICMS_META_DESCRIPTION, _CO_ICMS_META_DESCRIPTION_DSC, false, true, $displayOnForm);
$this->setControl('meta_description', array(
        'name' => 'textarea',
        'form_editor'=>'textarea'
));