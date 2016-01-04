<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Http;

use PHPUnit_Framework_TestCase;
use System\ParameterBag;
use System\Http\Request;

class RequestTest extends PHPUnit_Framework_TestCase
{
    public $request;

    public function setUp()
    {
        $inputData = [
            '0-indexed value',
            '1-indexed value',
            'index1' => 'value1',
            'index2' => 'value2',
            'multi' => [
                'dimension' => [
                    'index' => 'multi dimension value'
                ]
            ],
            'array' => [
                'value 1',
                'value 2',
                'value 3'
            ],
            'integer' => 123456,
            'float' => 123.456,
            'empty-string' => '',
            'null' => null
        ];

        $this->request = new Request(
            array_merge($inputData, [ 'input' => 'get', 'only-get' => 'only-get' ]),
            array_merge($inputData, [ 'input' => 'post', 'only-post' => 'only-post']),
            null,
            null,
            array_merge($_SERVER, [
                'SCRIPT_NAME' => '/system/script/name/index.php',
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/path/to/some-resource.html?any=get&values=yes',
                'SERVER_NAME' => 'some.fake-domain.com'
            ])
        );
    }

    public function tearDown()
    {
        $this->request = null;
    }

    public function testGetRequestMicrotime()
    {
        $this->assertNotEquals(0, $this->request->getRequestMicrotime());
    }

    public function testGetStandardizedRequestData()
    {
        $this->assertEquals('0-indexed value', $this->request->get(0));
        $this->assertEquals('1-indexed value', $this->request->get(1));
        $this->assertEquals('value1', $this->request->get('index1'));
        $this->assertEquals('value2', $this->request->get('index2'));
        $this->assertTrue(is_array($this->request->get('multi')));
        $this->assertTrue(is_array($this->request->get('array')));
        $this->assertTrue(is_integer($this->request->get('integer')));
        $this->assertTrue(is_float($this->request->get('float')));
        $this->assertEmpty($this->request->get('empty-string'));
        $this->assertNull($this->request->get('null'));
        $this->assertNull($this->request->get('-not-existed-index-'));
    }

    public function testGetQueryRequestData()
    {
        $this->assertEquals('0-indexed value', $this->request->query->get(0));
        $this->assertEquals('1-indexed value', $this->request->query->get(1));
        $this->assertEquals('value1', $this->request->query->get('index1'));
        $this->assertEquals('value2', $this->request->query->get('index2'));
        $this->assertTrue(is_array($this->request->query->get('multi')));
        $this->assertTrue(is_array($this->request->query->get('array')));
        $this->assertTrue(is_integer($this->request->query->get('integer')));
        $this->assertTrue(is_float($this->request->query->get('float')));
        $this->assertEmpty($this->request->query->get('empty-string'));
        $this->assertNull($this->request->query->get('null'));
        $this->assertNull($this->request->query->get('-not-existed-index-'));
    }

    public function testGetRequestRequestData()
    {
        $this->assertEquals('0-indexed value', $this->request->request->get(0));
        $this->assertEquals('1-indexed value', $this->request->request->get(1));
        $this->assertEquals('value1', $this->request->request->get('index1'));
        $this->assertEquals('value2', $this->request->request->get('index2'));
        $this->assertTrue(is_array($this->request->request->get('multi')));
        $this->assertTrue(is_array($this->request->request->get('array')));
        $this->assertTrue(is_integer($this->request->request->get('integer')));
        $this->assertTrue(is_float($this->request->request->get('float')));
        $this->assertEmpty($this->request->request->get('empty-string'));
        $this->assertNull($this->request->request->get('null'));
        $this->assertNull($this->request->request->get('-not-existed-index-'));
    }

    public function testGetOwnedStandardizedRequestData()
    {
        $this->assertEquals('get', $this->request->get('input'));
        $this->assertEquals('only-get', $this->request->get('only-get'));
        $this->assertEquals('only-post', $this->request->get('only-post'));
    }

    public function testGetOwnedQueryRequestData()
    {
        $this->assertEquals('get', $this->request->query->get('input'));
        $this->assertEquals('only-get', $this->request->query->get('only-get'));
        $this->assertNull($this->request->query->get('only-post'));
    }

    public function testGetOwnedRequestRequestData()
    {
        $this->assertEquals('post', $this->request->request->get('input'));
        $this->assertNull($this->request->request->get('only-get'));
        $this->assertEquals('only-post', $this->request->request->get('only-post'));
    }

    public function testGetScriptName()
    {
        $this->assertEquals('/system/script/name/index.php', $this->request->getScriptName());
    }

    public function testMethodGetSet()
    {
        $this->assertEquals('GET', $this->request->getMethod());

        $this->request->setMethod('POST');

        $this->assertEquals('POST', $this->request->getMethod());
    }

    public function testContentTypeGet()
    {
        $this->assertEquals('text/plain', $this->request->getContentType());

        $this->request->headers->set('CONTENT_TYPE', 'text/html');

        $this->assertEquals('text/html', $this->request->getContentType());
    }

    public function testDefaultLocaleGetSet()
    {
        $this->assertEquals('pl', $this->request->getDefaultLocale());

        $this->request->setDefaultLocale('en');

        $this->assertEquals('en', $this->request->getDefaultLocale());
    }

    public function testLocaleGetSet()
    {
        $this->assertEquals('pl', $this->request->getLocale());

        $this->request->setLocale('en');

        $this->assertEquals('en', $this->request->getLocale());
    }

    public function testIsMethod()
    {
        $this->assertTrue($this->request->isMethod('GET'));

        $this->request->setMethod('POST');

        $this->assertTrue($this->request->isMethod('POST'));
    }

    public function testIsMethodSafe()
    {
        $this->assertTrue($this->request->isMethodSafe());

        $this->request->setMethod('POST');

        $this->assertFalse($this->request->isMethodSafe());
    }

    public function testIsXmlHttpRequest()
    {
        $this->assertFalse($this->request->isXmlHttpRequest());
        $this->assertFalse($this->request->isAjax());

        $this->request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $this->assertTrue($this->request->isXmlHttpRequest());
        $this->assertTrue($this->request->isAjax());
    }

    public function testGetBasePath()
    {
        $this->assertEquals('/system/script/name/', $this->request->getBasePath());
    }

    /**
     * @dataProvider providerGetPath
     */
    public function testGetPath($input, $output)
    {
        $this->request->server->set('REQUEST_URI', $input);
        $this->assertEquals($output, $this->request->getPath());
    }

    public static function providerGetPath()
    {
        return [
            [ '', '' ],
            [ '/', '/' ],
            [ '/some-resource', '/some-resource' ],
            [ '/path/to/some-resource', '/path/to/some-resource' ],
            [ '/path/to/some-resource.', '/path/to/some-resource.' ],
            [ '/path/to/some-resource.html', '/path/to/some-resource.html' ],
            [ '/path/to/some-resource.html?', '/path/to/some-resource.html' ],
            [ '/path/to/some-resource.html?any', '/path/to/some-resource.html' ],
            [ '/path/to/some-resource.html?any=get&values=yes', '/path/to/some-resource.html' ],
            [ '?', '' ],
            [ '?any=get&values=yes', '' ],
            [ '/?any=get&values=yes', '/' ],
        ];
    }

    /**
     * @dataProvider providerGetUriForPath
     */
    public function testGetUriForPath($input, $output)
    {
        $this->assertEquals($output, $this->request->getUriForPath($input));
    }

    public static function providerGetUriForPath()
    {
        return [
            [ '', 'http://some.fake-domain.com/system/script/name/' ],
            [ '/', 'http://some.fake-domain.com/system/script/name/' ],
            [ '/some-resource', 'http://some.fake-domain.com/system/script/name/some-resource' ],
            [ '/path/to/some-resource', 'http://some.fake-domain.com/system/script/name/path/to/some-resource' ],
            [ '/path/to/some-resource.', 'http://some.fake-domain.com/system/script/name/path/to/some-resource.' ],
            [ '/path/to/some-resource.html', 'http://some.fake-domain.com/system/script/name/path/to/some-resource.html' ],
            [ '/path/to/some-resource.html?', 'http://some.fake-domain.com/system/script/name/path/to/some-resource.html?' ],
            [ '/path/to/some-resource.html?any', 'http://some.fake-domain.com/system/script/name/path/to/some-resource.html?any' ],
            [ '/path/to/some-resource.html?any=get&values=yes', 'http://some.fake-domain.com/system/script/name/path/to/some-resource.html?any=get&values=yes' ],
            [ '?', 'http://some.fake-domain.com/system/script/name/?' ],
            [ '?any=get&values=yes', 'http://some.fake-domain.com/system/script/name/?any=get&values=yes' ],
            [ '/?any=get&values=yes', 'http://some.fake-domain.com/system/script/name/?any=get&values=yes' ],
        ];
    }

    public function testFullUrl()
    {
        $this->request->server->set('REQUEST_URI', '/');
        $this->assertEquals('http://some.fake-domain.com/system/script/name/', $this->request->getFullUrl());

        $this->request->server->set('REQUEST_URI', '/some/fake-path');
        $this->assertEquals('http://some.fake-domain.com/system/script/name/some/fake-path', $this->request->getFullUrl());

        $this->request->server->set('REQUEST_URI', '/some/fake-path.html');
        $this->assertEquals('http://some.fake-domain.com/system/script/name/some/fake-path.html', $this->request->getFullUrl());
    }

    /**
     * @dataProvider providerQueryArray
     */
    public function testQueryArray($input, $output)
    {
        $this->request->server->set('QUERY_STRING', $input);
        $this->assertEquals($output, $this->request->getQueryArray());
    }

    public static function providerQueryArray()
    {
        return [
            [
                '',
                []
            ],
            [
                '?',
                ['?' => '']
            ],
            [
                '?asd=1',
                ['?asd' => 1]
            ],
            [
                'asd=1',
                ['asd' => 1]
            ],
            [
                'asd=1&qwe=2',
                ['asd' => 1, 'qwe' => 2]
            ],
        ];
    }
}
