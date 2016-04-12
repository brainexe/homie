<?php

namespace Homie\Sensors\Sensors\Humid;

use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\Percentage;
use Homie\Sensors\Interfaces\Parameterized;
use Homie\Sensors\Sensors\Misc\Expression;
use Homie\Sensors\SensorVO;

/**
 * r = relative Luftfeuchte
 * T = Temperatur in °C
 * TK = Temperatur in Kelvin (TK = T + 273.15)
 * TD = Taupunkttemperatur in °C
 * DD = Dampfdruck in hPa
 * SDD = Sättigungsdampfdruck in hPa

 * Parameter:
 * a = 7.5, b = 237.3 für T >= 0
 * a = 7.6, b = 240.7 für T < 0 über Wasser (Taupunkt)
 * a = 9.5, b = 265.5 für T < 0 über Eis (Frostpunkt)

 * R* = 8314.3 J/(kmol*K) (universelle Gaskonstante)
 * mw = 18.016 kg/kmol (Molekulargewicht des Wasserdampfes)
 * AF = absolute Feuchte in g Wasserdampf pro m3 Luft
 *
 * SDD(T) = 6.1078 * 10^((a*T)/(b+T))
 * DD(r,T) = r/100 * SDD(T)
 * r(T,TD) = 100 * SDD(TD) / SDD(T)
 * TD(r,T) = b*v/(a-v) mit v(r,T) = log10(DD(r,T)/6.1078)
 * AF(TD,TK) = 10^5 * mw/R* * SDD(TD)/TK
 * AF(r,TK) = 10^5 * mw/R* * DD(r,T)/TK
 *
 * AF(r,TK) = 10^5 * mw/R* * r/100 * 6.1078 * 10^((a*T)/(b+T))/TK
 * AF(r,TK) = 10^5 * 18.016/8314.3 * r/100 * 6.1078 * 10^((7.5*T)/(237.3+T))/(T + 273.15)
 * AF(r,TK) = 1000 * 18.016/8314.3 * 6.1078 * %s * (10 ** (((7.5*T)/(237.3+T)) / (T + 273.15)))
 * @Sensor("Sensor.Humid.Absolute")
 */
class Absolute extends Expression implements Parameterized
{

    const TYPE = 'humid.absolute';

    /**
     * {@inheritdoc}
     */
    public function getValue(SensorVO $sensor)
    {
        list ($temperatureId, $humidId) = explode(":", $sensor->parameter);

        $temperature = sprintf('getSensorValue(%d)', $temperatureId);
        $humidity   = sprintf('getSensorValue(%d)', $humidId);

        $expression = sprintf(
            '1000 * 18.016/8314.3 * 6.1078 * %s * 10**((7.5*%s)/(237.3+%s))/(%s + 273.15)',
            $humidity,
            $temperature,
            $temperature,
            $temperature
        );
        $sensorParameter = clone $sensor;
        $sensorParameter->parameter = $expression;

        return $this->round(parent::getValue($sensorParameter), 0.01);
    }

    /**
     * @return Definition
     */
    public function getDefinition() : Definition
    {
        $definition            = new Definition();
        $definition->formatter = Percentage::TYPE;
        $definition->type      = Definition::TYPE_NONE;
        $definition->unit      = 'g/m³';

        return $definition;
    }
}
