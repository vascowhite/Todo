<?php
/*
    Copyright (C) 2015  Paul White

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * User: Paul White
 * Date: 26/04/2015
 * 
 * File: Todo.php
 * @package todo
 */
 
 /**
  * @package todo
  */

namespace Vascowhite\Todo;


class Todo 
{
    /**
     * The Date the todo was created.
     * This is optional and may or may not be present.
     * Format is Y-m-d
     *
     * @var \DateTime $created
     */
    private $created;

    /**
     * The text of the todo.
     *
     * @var String $text
     */
    private $text;

    /**
     * The priority of the todo
     * Represented in the todo.txt file by (A), or (B) etc..
     *
     * @var String $priority
     */
    private $priority;

    /**
     * The projects that the todo is associated with.
     * Represented in the todo.txt file by a '+' followed by the projects name.
     * eg '+coolproject'
     *
     * @var string[] $projects
     */
    private $projects = [];

    /**
     * The contexts for the todo.
     * This could be a place or a method of communication
     * eg @home @ phone etc
     * Represented in the todo.txt file by '@'
     *
     * @var string[] $contexts
     */
    private $contexts = [];

    /**
     * The due date of the todo.
     * This need not be supplied and is not supported by all apps.
     * Format is Y-m-d
     * Represented in the todo.txt file by 'Due:2015-04-26'
     *
     * @var \DateTime $due
     */
    private $due;

    /**
     * Has the todo been completed?
     * If so, it should start with 'x'
     *
     * @var Bool $completed
     */
    private $completed = false;

    /**
     * The date that the task was completed
     *
     * @var \DateTime $completedDate
     */
    private $completedDate;

    const TODO_DATE_FORMAT = 'Y-m-d';

    /**
     * @param String        $text
     * @param \DateTime     $created
     * @param String|null   $priority
     * @param String[]      $projects
     * @param String[]      $contexts
     * @param \DateTime     $due
     */
    public function __construct($text, $created = null, $priority = null, array $projects = [], array $contexts = [], \DateTime $due = null, $completed = false)
    {
        $this->text = $text;
        $this->created = $created;
        $this->priority = $priority;
        $this->projects = $projects;
        $this->contexts = $contexts;
        $this->due = $due;
        $this->completed = $completed;

        if($this->completed){
            $this->done();
        }
    }

    /**
     * @param $todoString
     * @return Todo
     */
    public static function createFromString($todoString)
    {
        return TodoParser::parse($todoString);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if($this->completed){
            $todoString = "x {$this->completedDate->format(Todo::TODO_DATE_FORMAT)} ";
        } else {
            if($this->priority){
                $todoString = "({$this->priority}) ";
            } else {
                $todoString = '';
            }
        }
        $todoString = $this->buildTodo($todoString);
        return trim($todoString);
    }

    /**
     * @param string $todoString
     * @return string
     */
    private function buildTodo($todoString)
    {
        $todoString .= $this->buildCreated();
        $todoString .= "{$this->text} ";
        $todoString .= $this->buildProjects();
        $todoString .= $this->buildContexts();
        $todoString .= $this->buildDue();
        return $todoString;
    }

    private function buildCreated()
    {
        $created = null;
        if($this->created){
            $created = $this->created->format(Todo::TODO_DATE_FORMAT) . ' ';
        }
        return $created;
    }

    /**
     * @return string
     */
    private function buildProjects()
    {
        if(count($this->projects) > 0){
            return '+' . implode(' +', $this->projects) . ' ';
        }
    }

    /**
     * @return string
     */
    private function buildContexts()
    {
        if(count($this->contexts) > 0){
            return '@' . implode(' @', $this->contexts) . ' ';
        }
    }

    private function buildDue()
    {
        $due = null;
        if($this->due){
            $due = "due:{$this->due->format(Todo::TODO_DATE_FORMAT)}";
        }
        return $due;
    }

    /**
     * @return String
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return String
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @return String[]
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * @return String[]
     */
    public function getContexts()
    {
        return $this->contexts;
    }

    /**
     * @return \DateTime
     */
    public function getDue()
    {
        return $this->due;
    }

    /**
     * @return Bool
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    public function done(\DateTime $completedDate = null)
    {
        $this->completed = true;
        $this->priority = null;
        if($completedDate){
            $this->completedDate = $completedDate;
        } else {
            $this->completedDate = (new \DateTime())->setTime(0, 0, 0);
        }
    }

    /**
     * @return \DateTime
     */
    public function getCompletedDate()
    {
        return $this->completedDate;
    }

    /**
     * Sets Todo as not completed.
     * Priority of completed Todo's are lost.
     */
    public function undo()
    {
        $this->completed = false;
        $this->completedDate = null;
    }

    /**
     * @param Todo $comparedWith
     * @return bool
     */
    public function sameAs(Todo $comparedWith)
    {
        return $this->__toString() === $comparedWith->__toString();
    }
}