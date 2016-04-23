<?php

namespace Homie\Sensors\Sensors\Misc;

use BrainExe\Annotations\Annotations\Inject;
use Homie\Expression\Language;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\None;
use Homie\Sensors\Interfaces\Parameterized;
use Homie\Sensors\Sensors\AbstractSensor;
use Homie\Sensors\SensorVO;

/**
 * @Sensor("Sensor.Misc.Expression")
 */
class Expression extends AbstractSensor implements Parameterized
{

    const TYPE = 'custom.expression';

    /**
     * @var Language
     */
    private $language;

    /**
     * @Inject({"@Expression.Language"})
     * @param Language $language
     */
    public function __construct(
        Language $language
    ) {
        $this->language = $language;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(SensorVO $sensor)
    {
        return (float)$this->language->evaluate($sensor->parameter, []);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(SensorVO $sensor) : bool
    {
        $current = $this->getValue($sensor);

        return $current !== null;
    }

    /**
     * @return Definition
     */
    public function getDefinition() : Definition
    {
        $definition            = new Definition();
        $definition->type      = Definition::TYPE_NONE;
        $definition->formatter = None::TYPE;

        return $definition;
    }
}
