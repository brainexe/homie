<?php

namespace Homie\Display;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Expression\Language;

class Renderer
{

    /**
     * @var Language
     */
    private $language;

    /**
     * @Inject("@Expression.Language")
     * @param Language $language
     */
    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    /**
     * @param Settings $settings
     * @return string[]
     */
    public function render(Settings $settings)
    {
        $result = [];
        foreach ($settings->content as $line) {
            $result[] = $this->language->evaluate($line);
        }

        return $result;
    }
}
