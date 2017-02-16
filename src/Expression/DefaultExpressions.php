<?php

namespace Homie\Expression;

use BrainExe\Annotations\Annotations\Service;
use Generator;
use Homie\Expression\CompilerPass\DefaultExpression;

/**
 * @Service(tags={{"name"="default_expressions"}})
 */
class DefaultExpressions implements DefaultExpression
{

    /**
     * @return Generator|Entity[]
     */
    public static function getDefaultExpressions()
    {
        yield from self::getTodoListActions();
        yield from self::getShoppingListActions();

        $item = new Entity();
        $item->expressionId = 'eggTimer';
        $item->conditions   = [
            'voice(\'/(Timer|Wecker) (in|auf) ([0-9]+) Minuten?/i\')'
        ];
        $item->actions = [
            'eggTimer(voice(3) ~ "m")',
            'say("Timer ist auf " ~ voice(3) ~ " Minuten gestellt")',
        ];
        yield $item;
    }

    private static function getTodoListActions() : Generator
    {
        $item = new Entity();
        $item->expressionId = 'voiceReminder';
        $item->conditions   = [
            'voice("/^erinnere? mich an (.*)$/i")'
        ];
        $item->actions      = [
            'say("Ich werde dich an " ~ voice(1) ~ " erinnern")',
            'addTodoTodoItem(voice(1))'
        ];
        yield $item;

        $item = new Entity();
        $item->expressionId = 'voiceSayTodoList';
        $item->conditions   = [
            'voice("/^was gibt es (für|an) Aufgaben/i")'
        ];
        $item->actions = [
            'sayTodoList()'
        ];
        yield $item;
    }

    private static function getShoppingListActions() : Generator
    {
        $item = new Entity();
        $item->expressionId = 'addShoppingListItem';
        $item->conditions   = [
            'voice("/setze? (.*) auf die (Liste|Einkaufsliste)$/i")'
        ];
        $item->actions = [
            'addShoppingListItem(voice(1))',
            'say("Ich habe " ~ voice(1) ~ " auf die Einkaufsliste gesetzt")',

        ];
        yield $item;

        $item = new Entity();
        $item->expressionId = 'sayShoppingList';
        $item->conditions   = [
            'voice("/^was steht auf der (Liste|Einkaufsliste)$/i")'
        ];
        $item->actions = [
            'sayShoppingList()',
        ];
        yield $item;
    }
}
