<?php

namespace Homie\Display;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Annotations\Annotations\Inject;
use Homie\Display\Devices\Factory;
use Homie\Expression\Language;
use Homie\Node\Gateway as NodeGateway;
use Throwable;

/**
 * @Service("Display.Renderer")
 */
class Renderer
{
    /**
     * @var Language
     */
    private $language;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var NodeGateway
     */
    private $node;

    /**
     * @param Language $language
     * @param Factory $factory
     * @param NodeGateway $node
     */
    public function __construct(
        Language $language,
        Factory $factory,
        NodeGateway $node
    ) {
        $this->language = $language;
        $this->factory  = $factory;
        $this->node     = $node;
    }

    /**
     * @param Settings $settings
     * @return string[]
     */
    public function render(Settings $settings) : array
    {
        $result = [];
        foreach ($settings->content as $line) {
            $result[] = $this->language->evaluate($line, []);
        }

        $this->updateDisplay($settings, $result);

        return $result;
    }

    /**
     * @param Settings $settings
     * @param array $result
     */
    private function updateDisplay(Settings $settings, array $result)
    {
        try {
            $node = $this->node->get($settings->nodeId);

            $device = $this->factory->getDevice($node->getOption('deviceType'));
            $device->display($node, implode(PHP_EOL, $result));
        } catch (Throwable $e) {
        }
    }
}
