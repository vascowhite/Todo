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

    public function testCanParseProjectsWithHyphen()
    {
        $testTodoText = "(A) A test with +hyphenated-project +hyphenated-project2";
        $testTodo = TodoParser::parse($testTodoText);
        $this->assertEquals('hyphenated-project', $testTodo->getProjects()[0], 'Could not parse hyphenated projects');
        $this->assertEquals('hyphenated-project2', $testTodo->getProjects()[1], 'Could not parse hyphenated projects');
        $this->assertEquals($testTodoText, $testTodo->__toString(), 'Could not parse hyphenated projects');
    }

    public function testCanParseProjectsWithUnderscore()
    {
        $testTodoText = "(A) A test with +underscore_project +underscore_project2";
        $testTodo = TodoParser::parse($testTodoText);
        $this->assertEquals('underscore_project', $testTodo->getProjects()[0], 'Could not parse underscore projects');
        $this->assertEquals('underscore_project2', $testTodo->getProjects()[1], 'Could not parse underscore projects');
    }

    public function testCanParseContexts()
    {
        $testTodo = TodoParser::parse($this->testTodoText);
        $this->assertEquals('context', $testTodo->getContexts()[0], 'Could not parse contexts');
    }

    public function testCanParseContextsWithHyphen()
    {
        $testTodoText = "(A) A test with @hyphenated-context @hyphenated-context2";
        $testTodo = TodoParser::parse($testTodoText);
        $this->assertEquals('hyphenated-context', $testTodo->getContexts()[0], 'Could not parse hyphenated contexts');
        $this->assertEquals('hyphenated-context2', $testTodo->getContexts()[1], 'Could not parse hyphenated contexts');
        $this->assertEquals($testTodoText, $testTodo->__toString(), 'Could not parse hyphenated contexts');
    }

    public function testCanParseContextsWithUnderscore()
    {
        $testTodoText = "(A) A test with @underscore_context @underscore_context2";
        $testTodo = TodoParser::parse($testTodoText);
        $this->assertEquals('underscore_context', $testTodo->getContexts()[0], 'Could not parse underscore contexts');
        $this->assertEquals('underscore_context2', $testTodo->getContexts()[1], 'Could not parse underscore_ contexts');
    }

    public function testCanParseDueDate()
    {
        $testTodo = TodoParser::parse($this->testTodoText);
        $this->assertEquals('2015-04-26', $testTodo->getDue()->format(Todo::TODO_DATE_FORMAT), 'Could not parse due date');

        $testTodoText = '(A) This is a test todo Due:2015-04-26 +project @context';
        $testTodo = TodoParser::parse($testTodoText);
        $this->assertEquals('2015-04-26', $testTodo->getDue()->format(Todo::TODO_DATE_FORMAT), 'Could not parse due date in middle of string');

        $testTodoText = '(A) This is a test todo due:2015-04-26 +project @context';
        $testTodo = TodoParser::parse($testTodoText);
        $this->assertNotNull($testTodo->getDue(), 'Could not parse due date with lc D');
        if($testTodo->getDue()){
            $this->assertEquals('2015-04-26', $testTodo->getDue()->format(Todo::TODO_DATE_FORMAT), 'Could not parse due date with lc D');
        }

        $testTodoText = '(A) This is a test todo due:2015-4-6 +project @context';
        $testTodo = TodoParser::parse($testTodoText);
        $this->assertNotNull($testTodo->getDue(), 'Could not parse due date with single digit month and date');
        if($testTodo->getDue()){
            $this->assertEquals('2015-04-06', $testTodo->getDue()->format(Todo::TODO_DATE_FORMAT), '\'Could not parse due date with single digit month and date');
        }
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

    public function testCanParseTodoBeginningWithXAsIncomplete()
    {
        $testTodoText = 'x-ray to test starting with x';
        $testTodo = TodoParser::parse($testTodoText);
        $this->assertFalse($testTodo->getCompleted(), 'Falsely marked as complete');
    }

    public function testCanParseMultiples()
    {
        $testTodo = TodoParser::parse($this->testMultiples);
        $this->assertEquals($this->testMultiples, $testTodo->__toString(), 'Could not parse multiple projects/contexts');
    }
}
