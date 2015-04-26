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
     * The project that the todo is associated with.
     * Represented in the todo.txt file by a '+' followed by the project name.
     * eg '+coolproject'
     *
     * @var String $project
     */
    private $project;

    /**
     * The context for the todo.
     * This could be a place or a method of communication
     * eg @home @ phone etc
     * Represented in the todo.txt file by '@'
     *
     * @var Strimg $context
     */
    private $context;

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
     * @param String|null   $project
     * @param String|null   $context
     * @param \DateTime     $due
     */
    public function __construct($text, $created = null, $priority = null, $project = null, $context = null, \DateTime $due = null, $completed = false)
    {
        $this->text = $text;
        $this->created = $created;
        $this->priority = $priority;
        $this->project = $project;
        $this->context = $context;
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
            $todoString = "x {$this->completedDate->format(Todo::TODO_DATE_FORMAT)}";
            $todoString .= " {$this->created->format(Todo::TODO_DATE_FORMAT)}";
            $todoString .= " {$this->text} +{$this->project} @{$this->context}";
            $todoString .= " Due:{$this->due->format(Todo::TODO_DATE_FORMAT)}";
        } else {
            $todoString = "({$this->priority}) {$this->created->format(Todo::TODO_DATE_FORMAT)}";
            $todoString .= " {$this->text} +{$this->project} @{$this->context}";
            $todoString .= " Due:{$this->due->format(Todo::TODO_DATE_FORMAT)}";
        }
        return $todoString;
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
     * @return String
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @return Strimg
     */
    public function getContext()
    {
        return $this->context;
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
}