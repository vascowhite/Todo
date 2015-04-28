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
 * File: TodoParserTest.php
 * @package todo
 */
 
 /**
  * @package 
  */

namespace Vascowhite\Todo\Tests;


use Vascowhite\Todo\Todo;
use Vascowhite\Todo\TodoParser;

class TodoParserTest extends \PHPUnit_Framework_TestCase
{
    private $testTodoText = '(A) 2015-04-26 This is a test todo +project @context Due:2015-04-26';
    private $testMultiples = 'tests to do +project1 +project2 +project3 @context1 @context2 @context3';

    public function testCanParse()
    {
        $testTodo = TodoParser::parse($this->testTodoText);
        $this->assertInstanceOf('Vascowhite\Todo\Todo', $testTodo);

    }

    /**
     * @ski
     */
    public function testCanParseText()
    {
        $testTodoText = '(A) 2015-04-26 This is a test todo with a random date in it 2015-04-04 +project @context Due:2015-04-26';
        $testTodo = TodoParser::parse($testTodoText);
        $this->assertEquals('This is a test todo with a random date in it 2015-04-04', $testTodo->getText(), 'Could not get text');
    }

    public function testCanParseDateCreated()
    {
        $testTodo = TodoParser::parse($this->testTodoText);
        $this->assertEquals('2015-04-26', $testTodo->getCreated()->format(Todo::TODO_DATE_FORMAT), 'Could not get date created.');

        $testTodoText = '(A) This is a test todo with a random date in it 2015-04-04 +project @context Due:2015-04-26';
        $testTodo = TodoParser::parse($testTodoText);
        $this->assertNull($testTodo->getCreated(), 'Mistakenly got date created.');
    }

    public function testCanParsePriority()
    {
        $testTodo = TodoParser::parse($this->testTodoText);
        $this->assertEquals('A', $testTodo->getPriority(), 'Could not get priority');

        $testTodoText = '(A)-> This is a test todo +project @context Due:2015-04-26';
        $testTodo = TodoParser::parse($testTodoText);
        $this->assertNull($testTodo->getPriority(), "Parsed invalid priority");

        $testTodoText = '(a) This is a test todo +project @context Due:2015-04-26';
        $testTodo = TodoParser::parse($testTodoText);
        $this->assertNull($testTodo->getPriority(), "Parsed invalid priority");

        $testTodoText = 'This (A) is a test todo +project @context Due:2015-04-26';
        $testTodo = TodoParser::parse($testTodoText);
        $this->assertNull($testTodo->getPriority(), "Parsed invalid priority");
    }

    public function testCanParseProjects()
    {
        $testTodo = TodoParser::parse($this->testTodoText);
        $this->assertEquals('project', $testTodo->getProjects()[0], 'Could not parse projects');
    }

    public function testCanParseContexts()
    {
        $testTodo = TodoParser::parse($this->testTodoText);
        $this->assertEquals('context', $testTodo->getContexts()[0], 'Could not parse contexts');
    }

    public function testCanParseDueDate()
    {
        $testTodo = TodoParser::parse($this->testTodoText);
        $this->assertEquals('2015-04-26', $testTodo->getDue()->format(Todo::TODO_DATE_FORMAT), 'Could not parse due date');
    }

    public function testCanParseCompleted()
    {
        $testTodo = TodoParser::parse($this->testTodoText);
        $this->assertFalse($testTodo->getCompleted(), 'Mistakenly parsed completed');

        $testTodoText = 'x 2015-04-26 This is a test todo +project @context Due:2015-04-26';
        $testTodo = TodoParser::parse($testTodoText);
        $this->assertTrue($testTodo->getCompleted(), 'Could not parse completed');
        $this->assertNull($testTodo->getPriority(), 'Priority not nulled when marked as done');
        $this->assertInstanceOf('\DateTime', $testTodo->getCompletedDate(), 'Completion date not set of marked as done');
    }

    public function testCanParseCompletedDate()
    {
        $testTodoText = 'x 2015-04-26 This is a test todo +projects @contexts Due:2015-04-26';
        $testTodo = TodoParser::parse($testTodoText);
        $this->assertEquals('2015-04-26', $testTodo->getCompletedDate()->format(Todo::TODO_DATE_FORMAT));
    }

    public function testCanParseMultiples()
    {
        $testTodo = TodoParser::parse($this->testMultiples);
        $this->assertEquals($this->testMultiples, $testTodo->__toString(), 'Could not parse multiple projects/contexts');
    }
}
