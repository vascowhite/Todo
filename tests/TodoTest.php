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
 * File: TodoTest.php
 * @package todo
 */
namespace Vascowhite\Todo\Tests;
use Vascowhite\Todo\Todo;
use Vascowhite\Todo\TodoParser;

/**
  * @package 
  */

class TodoTest extends \PHPUnit_Framework_TestCase
{
    private $testTodoText = '(A) 2015-04-26 This is a test todo +projects @contexts due:2015-04-26';
    private $testDoneTodoText;

    protected function setUp()
    {
        $this->testDoneTodoText =  'x ' . (new \DateTime())->format(Todo::TODO_DATE_FORMAT) .
            " 2015-04-04 This is a test todo +projects @contexts due:2015-04-26";
    }

    public function testCanInstantiate()
    {
        $testTodo = new Todo('Just a test todo');
        $this->assertInstanceOf('Vascowhite\Todo\Todo', $testTodo, 'Could not instantiate');
    }

    public function testCanCreateFromString()
    {
        $this->assertInstanceOf(
            'Vascowhite\Todo\Todo',
            Todo::createFromString('(A) 2015-04-26 This is a test todo +projects @contexts due:2015-04-26'),
            'Could not create from string'
        );

        $this->assertInstanceOf(
            'Vascowhite\Todo\Todo',
            Todo::createFromString('This is a test todo'),
            'Could not create from string'
        );
    }

    public function testCanInstantiateFromStringWithHyphensAndUnderscores()
    {
        $testTodo = Todo::createFromString('(A) 2015-04-26 This is a test todo +projects_underscore @contexts-hypenated Due:2015-04-26');
        $this->assertInstanceOf(
            'Vascowhite\Todo\Todo',
            $testTodo,
            'Could not create from string'
        );

        $this->assertEquals('This is a test todo', $testTodo->getText(), 'Could not parse with hyphens/underscores');
    }

    public function testCanConvertToString()
    {
        $testTodo = TodoParser::parse($this->testTodoText);
        $this->assertEquals($this->testTodoText, $testTodo->__toString(), 'Could not convert to string');

        $testTodo = TodoParser::parse('(A) 2015-04-26 This is a test todo +projects @contexts due:2015-04-26');
        $done = new \DateTime();
        $testTodo->done($done);
        $doneString = 'x ' . $done->format(Todo::TODO_DATE_FORMAT) . ' 2015-04-26 This is a test todo +projects @contexts due:2015-04-26';
        $this->assertEquals($doneString, $testTodo->__toString(), 'Could not convert to string after calling done()');

        $testTodo = TodoParser::parse($this->testDoneTodoText);
        $this->assertEquals($this->testDoneTodoText, $testTodo->__toString(), 'Could not convert to string when completed');

        $testTodo = TodoParser::parse('test to do');
        $this->assertEquals('test to do', $testTodo->__toString(), 'Could not convert to string on minimal todo');
    }

    public function testCanWriteCompleted()
    {
        $testTodo = TodoParser::parse('(A) This is a test todo +projects @contexts due:2015-04-26');
        $completed = 'x 2015-04-30 This is a test todo +projects @contexts due:2015-04-26';
        $testTodo->done(new \DateTime('2015-04-30'));

        $this->assertEquals($completed, $testTodo);
    }

    public function testCanUndoCompletedTodo()
    {
        $completed = 'x 2015-04-30 This is a test todo +projects @contexts due:2015-04-26';
        $unCompleted = 'This is a test todo +projects @contexts due:2015-04-26';

        $testTodo = TodoParser::parse($completed);
        $testTodo->undo();
        $this->assertEquals($unCompleted, $testTodo, 'Could not undo completed Todo');
    }

    public function testSameAs()
    {
        $todo1 = TodoParser::parse('This is a test todo +projects @contexts due:2015-04-26');
        $todo2 = TodoParser::parse('This is a test todo +projects @contexts due:2015-04-26');
        $todo3 = TodoParser::parse('This is a different test todo +projects @contexts Due:2015-04-26');

        $this->assertTrue($todo1->sameAs($todo2), 'Failed to compare similar Todos');
        $this->assertFalse($todo2->sameAs($todo3), 'Failed to detect different Todos');
    }
}
