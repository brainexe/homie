<?php

namespace Homie\Sensors\Formatter;

use Homie\Sensors\CompilerPass\Annotation\SensorFormatter;

/**
 * @SensorFormatter("Formatter.Number")
 */
class Number extends Formatter
{

    const TYPE = 'number';

    /**
     * @param float $number
     * @return float|int|string
     */
    public function formatValue($number)
    {
        $thresh = 1000;
        if ($number < $thresh) {
            return round($number, 1);
        }

        $units = ['k', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'];
        $u = -1;
        do {
            $number /= $thresh;
            ++$u;
        } while ($number >= $thresh && $u < count($units) - 1);

        return round($number) . $units[$u];
    }
}
