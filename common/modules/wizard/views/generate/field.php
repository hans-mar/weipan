<?php
/**
 * This is the template for generating the format method of a specified field.
 */
?>

    // Map method of field `<?= $field ?>`
    public static function get<?= $methodName ?>Map($prepend = false)
    {
        $map = [
            self::<?= strtoupper($field) ?> => '',
        ];

        return self::resetMap($map, $prepend);
    }

    // Format method of field `<?= $field ?>`
    public function get<?= $methodName ?>Value($value = null)
    {
        return $this->resetValue($value);
    }
