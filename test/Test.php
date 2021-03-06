<?php

/**
 * Created by PhpStorm.
 * User: samaneh
 * Date: 2015/10/15
 * Time: 11:12 AM
 */

use \Salarmehr\Ary;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Ary.php';

class Test extends PHPUnit\Framework\TestCase

{
  protected $item;

  public function setup()
  {
    $this->item = [];
  }

  public function provider()
  {
    return array(
      array(''),
      array(['ali', 'reza', 'mohammad']),
      array([1, 2, 3, 4]),
    );
  }

  /**
   * @param array $originalary array to get
   * @param array $expectedary What we expect to get
   *
   * @dataProvider various
   */

  public function testAll($originalary, $expectedary)
  {
    $ary = new Ary();
    $ary->all();
    $this->assertEquals($expectedary, $originalary);
  }

  public function various()
  {
    return array(
      array('', ''),
      array(null, null),
      array(['ali', 'reza', 'mohammad'], ['ali', 'reza', 'mohammad']),
      array(['name' => 'ali', 'lastname' => 'reza', 'age' => 30], ['name' => 'ali', 'lastname' => 'reza', 'age' => 30]),
      array((object)['name' => 'ali', 'lastname' => 'reza', 'age' => 30], (object)['name' => 'ali', 'lastname' => 'reza', 'age' => 30]),
      array(['ali', 'reza', 'mohammad'], ['ali', 'reza', 'mohammad']),
      array([1, 2, 3, 4], [1, 2, 3, 4]),
    );
  }


  public function testGet()
  {
    $ary = new Ary();
    $this->assertEquals($ary[0], null);

    $ary = new Ary(['x' => ['xx' => ['m' => 'xxx']]]);
    $this->assertEquals($ary->get('x.xx.m'), 'xxx');
  }


  /**
   * @dataProvider Provider
   */
  public function testCount($objects)
  {
    $ary = new Ary($objects);
    $result = count($objects);
    $num = count($ary);
    $this->assertEquals($result, $num);
  }

  /**
   * @dataProvider various
   */
  public function testGetArrayableItems($original, $expected)
  {
    $ary = new Ary($original);
    $this->invokeMethod($ary, 'getArrayableItems', array($original));
    $this->assertEquals($expected, $original);
  }

  /**
   * @param object &$ary              Instantiated object that we will run method on.
   * @param string $getArrayableItems Method name to call
   * @param array  $parameters        Array of parameters to pass into method.
   *
   * @return mixed Method return.
   */
  public function invokeMethod(&$ary, $getArrayableItems, array $parameters = array())
  {
    $reflection = new \ReflectionClass(get_class($ary));
    $method = $reflection->getMethod($getArrayableItems);
    $method->setAccessible(true);
    return $method->invokeArgs($ary, $parameters);
  }

  public function testAssignment()
  {
    $ary = new Ary();
//        var_dump($ary);die();
    $ary[] = 3;
    $this->assertEquals($ary[0], 3);
    $this->assertEquals($ary->{0}, 3);
    $this->assertTrue($ary->has(0));
    $ary['x'] = ['z' => 'y'];
    $ary['foo'] = 'bar';
    $this->assertEquals($ary['foo'], 'bar');
    $this->assertEquals($ary->foo, 'bar');
    $this->assertTrue($ary->has('foo'));
    $this->assertEquals($ary['x']['z'], 'y');
    $ary['x']['z'] = 'm';
    $this->assertEquals($ary['x']['z'], 'm');

  }

  /**
   * @dataProvider various
   * @param $original
   */
  public function testJsonSerialize($original)
  {
    $this->assertEquals(json_encode(new Ary($original)), json_encode((array)$original));
  }

  /**
   * @dataProvider various
   */
  public function ary()
  {
    $test = ['x' => ['xx' => 'xxx']];
    $ary = new Ary($test);
    $this->assertEquals(ary($test)->x['xx'], $ary->ary('x')->xx);
    $this->assertEquals(ary(ary($test)->x['xx']), $ary->ary('x')->ary('xx'));
  }

  public function testReplace()
  {
    $c = new Ary(['foo' => 'x', 'bar' => 'y']);
    $this->assertEquals(['foo' => 'f', 'bar' => 'y', 'baz' => 'z'], $c->replace(new Ary(['foo' => 'f', 'baz' => 'z']))->all());
  }

  public function testReplaceAryRecursively()
  {
    $base = ['citrus' => ["orange"], 'berries' => ["blackberry", "raspberry"],];
    $replacements = ['citrus' => ['pineapple'], 'berries' => ['blueberry']];
    $expect = ['citrus' => ['pineapple'], 'berries' => ['blueberry', 'raspberry']];
    $c = new Ary($base);
    $this->assertEquals($expect, $c->replaceRecursively(new Ary($replacements))->all());
  }

//    public function testOffsetExists()
//    {
//        $parameters = array(7,8,9,4);
//        $ary= new Ary($parameters);
//        $key= $ary[1];
//        $result=$ary->offsetExists($key);
//        $this->assert($result);
//    }
}
