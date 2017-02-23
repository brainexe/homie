<?php

namespace Homie\TodoList;

use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Translation\TranslationTrait;
use Homie\Espeak\EspeakEvent;
use Homie\Espeak\EspeakVO;
use Homie\TodoList\VO\TodoItemVO;

/**
 * @Service
 */
class TodoReminder
{

    use EventDispatcherTrait;
    use TranslationTrait;

    /**
     * @var TodoList
     */
    private $todoList;

    /**
     * @param TodoList $todoList
     */
    public function __construct(TodoList $todoList)
    {
        $this->todoList = $todoList;
    }

    public function sendNotification() : void
    {
        $issuesPerState = $this->getGroupedIssues();

        if (empty($issuesPerState)) {
            return;
        }

        $this->doSendNotification($issuesPerState);
    }

    /**
     * @param array $issuesPerState
     */
    private function doSendNotification(array $issuesPerState)
    {
        $text = $this->translate('Erinnerung');
        $text .= ': ';

        foreach ($issuesPerState as $state => $issuesPerStatus) {
            $text .= $this->getStateName(count($issuesPerStatus), $state);
            $text .= ': ';

            /** @var TodoItemVO $todo */
            foreach ($issuesPerStatus as $todo) {
                $text .= sprintf('%s.', $todo->name);
            }
        }

        $espeakVo = new EspeakVO($text);
        $event    = new EspeakEvent($espeakVo);

        $this->dispatchInBackground($event);
    }

    /**
     * @return array[]
     */
    private function getGroupedIssues() : array
    {
        $todos = $this->todoList->getList();

        $issuesPerState = [];
        foreach ($todos as $todo) {
            if (TodoItemVO::STATUS_COMPLETED === $todo->status) {
                continue;
            }

            $issuesPerState[$todo->status][] = $todo;
        }

        return $issuesPerState;
    }

    /**
     * @param int $count
     * @param string $state
     * @return string
     */
    private function getStateName(int $count, string $state) : string
    {
        $stringCount = $this->getNumber($count);

        switch ($state) {
            case TodoItemVO::STATUS_PROGRESS:
                return sprintf(ngettext('%d task in progress', '%d tasks in progress', $count), $stringCount);
            case TodoItemVO::STATUS_OPEN:
            default:
                return sprintf(ngettext('%d open task', '%d open tasks', $count), $stringCount);
        }
    }

    /**
     * @param int $count
     * @return string
     */
    private function getNumber(int $count) : string
    {
        switch ($count) {
            case 1:
                return $this->translate('one');
            default:
                return (string)$count;
        }
    }
}
