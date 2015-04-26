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
 * File: TodoParser.php
 * @package todo
 */
 
 /**
  * @package todo
  *
  * Parses todo's from a todo string
  */

namespace Vascowhite\Todo;


/**
 * Class TodoParser
 * @package Vascowhite\Todo
 */
class TodoParser 
{
    /**
     * @param String $todoString
     * @return Todo
     */
    public static function parse($todoString)
    {
        $completed = false;
        if('x' === substr($todoString, 0, 1)){
            $completed = true;
        }

        if($completed){
            $todo = new Todo(
                self::getText($todoString),
                self::getCreationDate($todoString),
                null,
                self::getProject($todoString),
                self::getContext($todoString),
                self::getDueDate($todoString),
                true
            );
            $todo->done(self::getCompletedDate($todoString));
        } else {
            $todo = new Todo(
                self::getText($todoString),
                self::getCreationDate($todoString),
                self::getPriority($todoString),
                self::getProject($todoString),
                self::getContext($todoString),
                self::getDueDate($todoString)
            );
        }
        return $todo;
    }

    private static function getText($todoString)
    {
        return trim(preg_replace(
            [
                '/\Ax \d{4}-\d{2}-\d{2} /m', //Completion date & 'x' if completed
                '/\A\([A-Z]\) /m',          //Priority
                '/ \+\w+/m',                //Project
                '/ \@\w+/m',                //Context
                '/Due:\d{4}-\d{2}-\d{2}/m', //Due date
                '/\A\d{4}-\d{2}-\d{2} /m',  //Date - we do this last so we can anchor it to the start of the string.
            ],
            null,
            $todoString));
    }

    /**
     * @param $todoString
     * @return \DateTime|null
     */
    private static function getCreationDate($todoString)
    {
        $creationDate = null;
        $matches = [];
        if('x' === substr($todoString, 0, 1)){
            $pattern = '/x \d{4}-\d{2}-\d{2} (\d{4}-\d{2}-\d{2}) /';
        } else {
            $pattern = '/ (\d{4}-\d{2}-\d{2})/m';
        }
        if(preg_match($pattern, substr($todoString, 0, 24), $matches)){
            $creationDate = \DateTime::createFromFormat(Todo::TODO_DATE_FORMAT, trim($matches[1]));
            $creationDate->setTime(0, 0, 0);
        }
        return $creationDate;
    }

    /**
     * @param $todoString
     * @return null|string
     */
    private static function getPriority($todoString)
    {
        $priority = null;
        $matches = [];
        if(preg_match('/\A\([A-Z]\) /m', $todoString, $matches)){
            $priority = trim($matches[0], ' ()');
        }
        return $priority;
    }

    /**
     * @param $todoString
     * @return null|string
     */
    private static function getProject($todoString)
    {
        $project = null;
        $matches = [];
        if(preg_match('/ \+\w+/m', $todoString, $matches)){
            $project = trim($matches[0], ' +');
        }
        return $project;
    }

    /**
     * @param $todoString
     * @return null|string
     */
    private static function getContext($todoString)
    {
        $context = null;
        $matches = [];
        if(preg_match('/ \@\w+/m', $todoString, $matches)){
            $context = trim($matches[0], ' @');
        }
        return $context;
    }

    /**
     * @param $todoString
     * @return \DateTime|null
     */
    private static function getDueDate($todoString)
    {
        $dueDate = null;
        $matches = [];
        if(preg_match('/Due:\d{4}-\d{2}-\d{2}/m', $todoString, $matches)){
            $dueDate = \DateTime::createFromFormat(Todo::TODO_DATE_FORMAT, substr($matches[0], 4, 10));
        }
        return $dueDate;
    }

    private static function getCompletedDate($todoString)
    {
        $completedDate = null;
        $matches = [];
        if(preg_match('/\Ax (\d{4}-\d{2}-\d{2}) /m', $todoString, $matches)){
            $completedDate = \DateTime::createFromFormat(Todo::TODO_DATE_FORMAT, $matches[1]);
        }
        return $completedDate;
    }
}