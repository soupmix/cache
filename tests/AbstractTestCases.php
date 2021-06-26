<?php
namespace Soupmix\Cache\Tests;

use DateInterval;
use Soupmix\Cache\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AbstractTestCases extends TestCase
{
    protected $client = null;
    /**
     * @test
     */
    public function shouldSetGetAndDeleteAnItemSuccessfully() : void
    {
        $ins1 = $this->client->set('test1', 'value1', new DateInterval('PT60S'));
        $this->assertTrue($ins1);
        $value1 = $this->client->get('test1');
        $this->assertSame('value1', $value1);
        $delete = $this->client->delete('test1');
        $this->assertTrue($delete);
    }

    public function testSetGetAndDeleteMultipleItems() : void
    {
        $cacheData = [
            'test1' => 'value1',
            'test2' => 'value2',
            'test3' => 'value3',
            'test4' => 'value4'
        ];
        $insMulti = $this->client->setMultiple($cacheData, new DateInterval('PT60S'));
        $this->assertTrue($insMulti);

        $getMulti = $this->client->getMultiple(array_keys($cacheData));

        foreach ($cacheData as $key => $value) {
            $this->assertArrayHasKey($key, $getMulti);
            $this->assertEquals($value, $getMulti[$key]);
        }
        $deleteMulti = $this->client->deleteMultiple(array_keys($cacheData));
        $this->assertTrue($deleteMulti);
    }


    public function testHasItem() : void
    {
        $has = $this->client->has('has');
        $this->assertFalse($has);
        $this->client->set('has', 'value');
        $has = $this->client->has('has');
        $this->assertTrue($has);
    }

    /**
     * @test
     */
    public function failForReservedCharactersInKeyNames() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->client->set('@key', 'value');
    }

    /**
     * @test
     */
    public function failForInvalidStringInKeyNames() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->client->set(1, 'value');
    }

    public function testClear() : void
    {
        $clear = $this->client->clear();
        $this->assertTrue($clear);
    }
}
