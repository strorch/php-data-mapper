<?php
/**
 * Data Mapper
 *
 * @link      https://github.com/hiqdev/php-data-mapper
 * @package   php-data-mapper
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\DataMapper\Validator;

use yii\base\DynamicModel;

/**
 * Class DynamicValidationModel is used to validate a WHERE condition
 * before passing it to the Specification
 *
 * @see WhereValidator
 */
class DynamicValidationModel extends DynamicModel
{
    public function defineAttribute($name, $value = null)
    {
        parent::defineAttribute($name, $value);

        if ($value instanceof self) {
            $this->addRule($name, function (string $attribute) {
                /** @var self $child */
                $child = $this->$attribute;
                $isValid = $child->validate();
                if (!$isValid) {
                    foreach ($child->getErrors() as $childAttr => $errors) {
                        $this->addError($attribute . '.' . $childAttr, reset($errors));
                    }
                }

                return $isValid;
            });
        }
    }

    public function setAttributes($values, $safeOnly = true)
    {
        if (is_array($values)) {
            $attributes = array_flip($this->safeAttributes());
            foreach ($values as $name => $value) {
                if (isset($attributes[$name])) {
                    if ($this->$name instanceof self) {
                        $this->$name->load($value, '');
                    } else {
                        $this->$name = $value;
                    }
                }
            }
        }
    }
}
