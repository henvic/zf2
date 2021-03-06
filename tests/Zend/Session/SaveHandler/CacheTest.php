<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Session
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Session\SaveHandler;

use Zend\Session\SaveHandler\Cache,
    Zend\Session\SaveHandler\Exception as SaveHandlerException,
    Zend\Session\Manager,
    Zend\Cache\Storage\Adapter\Memory;

/**
 * Unit testing for DbTable include all tests for
 * regular session handling
 *
 * @category   Zend
 * @package    Zend_Session
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Session
 * @group      Zend_Cache
 */
class CacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend\Cache\Storage\Adapter\Memory
     */
    protected $memory;

    /**
     * @var string
     */
    protected $testArray;

    /**
     * Array to collect used Cache objects, so they are not
     * destroyed before all tests are done and session is not closed
     *
     * @var array
     */
    protected $usedSaveHandlers = array();

    public function setUp()
    {
        $this->memory = new Memory();
        $this->testArray = array('foo' => 'bar', 'bar' => array('foo' => 'bar'));
    }

    public function testReadWrite()
    {
        $this->_usedSaveHandlers[] =
            $saveHandler = new Cache($this->memory);

        $id = '242';

        $this->assertTrue($saveHandler->write($id, serialize($this->testArray)));

        $data = unserialize($saveHandler->read($id));
        $this->assertEquals($this->testArray, $data, 'Expected ' . var_export($this->testArray, 1) . "\nbut got: " . var_export($data, 1));
    }

    public function testReadWriteComplex()
    {
        $saveHandler = new Cache($this->memory);
        $saveHandler->open('savepath', 'sessionname');

        $id = '242';

        $this->assertTrue($saveHandler->write($id, serialize($this->testArray)));

        $this->assertEquals($this->testArray, unserialize($saveHandler->read($id)));
    }

    public function testReadWriteTwice()
    {
        $this->_usedSaveHandlers[] =
            $saveHandler = new Cache($this->memory);

        $id = '242';

        $this->assertTrue($saveHandler->write($id, serialize($this->testArray)));

        $this->assertEquals($this->testArray, unserialize($saveHandler->read($id)));

        $this->assertTrue($saveHandler->write($id, serialize($this->testArray)));

        $this->assertEquals($this->testArray, unserialize($saveHandler->read($id)));
    }
}
