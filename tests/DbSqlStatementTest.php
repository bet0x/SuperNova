<?php

/**
 * Class DbSqlStatementPublisher
 *
 * Publishes protected method of DbSqlStatement
 */
class DbSqlStatementPublisher extends DbSqlStatement {
  /**
   * @var db_mysql $db
   */
  public $db;

  public function selectFieldsToString($fields) {
    return parent::selectFieldsToString($fields);
  }
}


/**
 * Class DbSqlStatementTest
 *
 * @coversDefaultClass DbSqlStatement
 */
class DbSqlStatementTest extends PHPUnit_Framework_TestCase {

  /**
   * @var DbSqlStatementPublisher $object
   */
  protected $object;

  public function setUp() {
    parent::setUp();
    $this->object = new DbSqlStatementPublisher(null);
  }

  public function tearDown() {
    unset($this->object);
    parent::tearDown(); // TODO: Change the autogenerated stub
  }


  /**
   * @covers ::__construct
   */
  public function test__construct() {
    new DbSqlStatementPublisher(null);
  }

  /**
   * @covers ::select
   */
  public function testSelect() {
    $this->assertEquals($this->object, $this->object->select());

    $this->assertEquals(DbSqlStatement::SELECT, $this->object->operation);
    $this->assertEquals(array('*'), $this->object->fields);
  }

  /**
   * @covers ::select
   * @covers ::fields
   * @covers ::from
   * @covers ::fromAlias
   * @covers ::setIdField
   */
  public function testChain() {
    $this->assertEquals($this->object, $this->object->select()->fields(array('qwe'))->from('aTable', 'theTable')->setIdField('idField'));

    $this->assertEquals(DbSqlStatement::SELECT, $this->object->operation);
    $this->assertEquals(array('qwe'), $this->object->fields);
    $this->assertEquals('aTable', $this->object->table);
    $this->assertEquals('theTable', $this->object->alias);
    $this->assertEquals('idField', $this->object->idField);
  }

  /**
   * @covers ::getParamsFromStaticClass
   */
  public function testGetParamsFromStaticClass() {
    $this->assertEquals($this->object, $this->object->getParamsFromStaticClass('DBStaticRecord'));

    $this->assertEquals('_table', $this->object->table);
    $this->assertEquals('id', $this->object->idField);
  }


  // selectFieldsToString ----------------------------------------------------------------------------------------------
  public function testSelectFieldsToStringDataProvider() {
    return array(
      // Testing single values

      // --- Every field
      array('*', '*'),

      // --- DbSqlLiteral
      array('MAX()', new DbSqlLiteral('MAX()')),

      // --- Booleans
      array('1', true),
      array('0', false),

      // --- Numeric
      array('2', 2),
      array('1.2', 1.2),
      array('1.5E+100', 1.5e100),
      array('1.5E-100', 1.5e-100),

      // --- Null
      array('NULL', null),

      // --- String
      array('`test`', 'test'),

      // --- Testing escaping with mocked stringEscape function
      array('`t\\\'e\\"s\\\\t`', 't\'e"s\\t'),

      // Testing arrays
      array('`t\\\'e\\"s\\\\t`,1,NULL,2,*', array('t\'e"s\\t', true, null, 2, '*')),
      array('0', array('', '', false)),
    );
  }


  /**
   * @dataProvider testSelectFieldsToStringDataProvider
   * @covers ::selectFieldsToString
   * @covers ::processFieldDefault
   */
  public function testSelectFieldsToString($expected, $param) {
    // Testing with mocked stringEscape function
    $mock = $this->getMock('DbSqlStatementPublisher', array('stringEscape'));
    $mock->expects($this->any())
      ->method('stringEscape')
      ->will($this->returnCallback(function ($string) { return addslashes($string); }));

    $this->assertEquals($expected, $mock->selectFieldsToString($param));
  }

  public function testSelectFieldsToStringExceptionDataProvider() {
    return array(
      array(''),
      array(array()),
      array(array('', '')),
    );
  }

  /**
   * @dataProvider testSelectFieldsToStringExceptionDataProvider
   * @covers ::selectFieldsToString
   * @expectedException ExceptionDBFieldEmpty
   */
  public function testSelectFieldsToStringException($badValue) {
    $this->object->selectFieldsToString($badValue);
  }



  // __toString --------------------------------------------------------------------------------------------------------
  /**
   * @covers ::__toString
   * @expectedException ExceptionDbOperationEmpty
   */
  public function test__toStringExceptionDbOperationEmpty() {
    $this->object->operation = '';
    $this->object->__toString();
  }

  /**
   * @covers ::__toString
   * @expectedException ExceptionDbOperationRestricted
   */
  public function test__toStringExceptionDbOperationRestricted() {
    $this->object->operation = 'RESTRICTED';
    $this->object->__toString();
  }

}