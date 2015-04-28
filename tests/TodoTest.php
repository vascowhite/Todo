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
    private $testTodoText = '(A) 2015-04-26 This is a test todo +projects @contexts Due:2015-04-26';
    private $testDoneTodoText;

    protected function setUp()
    {
        $this->testDoneTodoText =  'x ' . (new \DateTime())->format(Todo::TODO_DATE_FORMAT) .
            " 2015-04-04 This is a test todo +projects @contexts Due:2015-04-26";
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
            Todo::createFromString('(A) 2015-04-26 This is a test todo +projects @contexts Due:2015-04-26'),
            'Could not create from string'
        );

        $this->assertInstanceOf(
            'Vascowhite\Todo\Todo',
            Todo::createFromString('This is a test todo'),
            'Could not create from string'
        );
    }

    public function testCanConvertToString()
    {
        $testTodo = TodoParser::parse($this->testTodoText);
        $this->assertEquals($this->testTodoText, $testTodo->__toString(), 'Could not convert to string');

        $testTodo = TodoParser::parse('(A) 2015-04-26 This is a test todo +projects @contexts Due:2015-04-26');
        $done = new \DateTime();
        $testTodo->done($done);
        $doneString = 'x ' . $done->format(Todo::TODO_DATE_FORMAT) . ' 2015-04-26 This is a test todo +projects @contexts Due:2015-04-26';
        $this->assertEquals($doneString, $testTodo->__toString(), 'Could not convert to string after calling done()');

        $testTodo = TodoParser::parse($this->testDoneTodoText);
        $this->assertEquals($this->testDoneTodoText, $testTodo->__toString(), 'Could not convert to string when completed');

        $testTodo = TodoParser::parse('test to do');
        $this->assertEquals('test to do', $testTodo->__toString(), 'Could not convert to string on minimal todo');
    }
}
