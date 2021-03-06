<?php

namespace PHPCR\Tests\Util;

use PHPCR\Util\PathHelper;

class PathHelperTest extends \PHPUnit_Framework_TestCase
{
    // assertValidPath tests

    public function testAssertValidPath()
    {
        $this->assertTrue(PathHelper::assertValidAbsolutePath('/parent/child'));
    }

    public function testAssertValidPathRoot()
    {
        $this->assertTrue(PathHelper::assertValidAbsolutePath('/'));
    }

    public function testAssertValidPathNamespaced()
    {
        $this->assertTrue(PathHelper::assertValidAbsolutePath('/jcr:foo_/b-a/0^.txt'));
    }

    public function testAssertValidPathIndexed()
    {
        $this->assertTrue(PathHelper::assertValidAbsolutePath('/parent[7]/child'));
    }

    /**
     * @expectedException \PHPCR\RepositoryException
     */
    public function testAssertValidTargetPathNoIndex()
    {
        PathHelper::assertValidAbsolutePath('/parent/child[7]', true);
    }

    /**
     * @expectedException \PHPCR\RepositoryException
     */
    public function testAssertValidPathNotAbsolute()
    {
        PathHelper::assertValidAbsolutePath('parent');
    }

    /**
     * @expectedException \PHPCR\RepositoryException
     */
    public function testAssertValidPathDouble()
    {
        PathHelper::assertValidAbsolutePath('/parent//child');
    }

    /**
     * @expectedException \PHPCR\RepositoryException
     */
    public function testAssertValidPathParent()
    {
        PathHelper::assertValidAbsolutePath('/parent/../child');
    }

    /**
     * @expectedException \PHPCR\RepositoryException
     */
    public function testAssertValidPathSelf()
    {
        PathHelper::assertValidAbsolutePath('/parent/./child');
    }

    /**
     * @expectedException \PHPCR\RepositoryException
     */
    public function testAssertValidPathTrailing()
    {
        PathHelper::assertValidAbsolutePath('/parent/child/');
    }

    public function testAssertValidPathNoThrow()
    {
        $this->assertFalse(PathHelper::assertValidAbsolutePath('parent', false, false));
    }

    // assertValidLocalName tests

    public function testAssertValidLocalName()
    {
        $this->assertTrue(PathHelper::assertValidLocalName('nodename'));
    }

    public function testAssertValidLocalNameRootnode()
    {
        $this->assertTrue(PathHelper::assertValidLocalName(''));
    }

    /**
     * @expectedException \PHPCR\RepositoryException
     */
    public function testAssertValidLocalNameNamespaced()
    {
        $this->assertTrue(PathHelper::assertValidLocalName('jcr:nodename'));
    }

    /**
     * @expectedException \PHPCR\RepositoryException
     */
    public function testAssertValidLocalNamePath()
    {
        $this->assertTrue(PathHelper::assertValidLocalName('/path'));
    }

    /**
     * @expectedException \PHPCR\RepositoryException
     */
    public function testAssertValidLocalNameSelf()
    {
        PathHelper::assertValidLocalName('.');
    }

    /**
     * @expectedException \PHPCR\RepositoryException
     */
    public function testAssertValidLocalNameParent()
    {
        PathHelper::assertValidLocalName('..');
    }

    // normalizePath tests

    /**
     * @dataProvider dataproviderNormalizePath
     */
    public function testNormalizePath($inputPath, $outputPath)
    {
        $this->assertSame($outputPath, PathHelper::normalizePath($inputPath));
    }

    public static function dataproviderNormalizePath()
    {
        return array(
            array('/../foo',       '/foo'),
            array('/../',           '/'),
            array('/foo/../bar',   '/bar'),
            array('/foo/./bar',    '/foo/bar'),
        );
    }

    /**
     * @expectedException \PHPCR\RepositoryException
     */
    public function testNormalizePathInvalid()
    {
        PathHelper::normalizePath('foo/bar');
    }

    /**
     * @expectedException \PHPCR\RepositoryException
     */
    public function testNormalizePathShortInvalid()
    {
        PathHelper::normalizePath('bar');
    }

    /**
     * @expectedException \PHPCR\RepositoryException
     */
    public function testNormalizePathTrailing()
    {
        PathHelper::normalizePath('/foo/bar/');
    }

    /**
     * @expectedException \PHPCR\RepositoryException
     */
    public function testNormalizePathEmpty()
    {
        PathHelper::normalizePath('');
    }

    // absolutizePath tests

    /**
     * @dataProvider dataproviderAbsolutizePath
     */
    public function testAbsolutizePath($inputPath, $context, $outputPath)
    {
        $this->assertSame($outputPath, PathHelper::absolutizePath($inputPath, $context));
    }

    public static function dataproviderAbsolutizePath()
    {
        return array(
            array('/../foo',    '/',    '/foo'),
            array('../',        '/',    '/'),
            array('../foo/bar', '/baz', '/foo/bar'),
            array('foo/./bar',  '/baz', '/baz/foo/bar'),
        );
    }

    // getParentPath tests

    public function testGetParentPath()
    {
        $this->assertEquals('/parent', PathHelper::getParentPath('/parent/child'));
    }

    public function testGetParentPathNamespaced()
    {
        $this->assertEquals('/jcr:parent', PathHelper::getParentPath('/jcr:parent/ns:child'));
    }

    public function testGetParentPathNodeAtRoot()
    {
        $this->assertEquals('/', PathHelper::getParentPath('/parent'));
    }

    public function testGetParentPathRoot()
    {
        $this->assertEquals('/', PathHelper::getParentPath('/'));
    }

    // getNodeName tests

    public function testGetNodeName()
    {
        $this->assertEquals('child', PathHelper::getNodeName('/parent/child'));
    }

    public function testGetNodeNameNamespaced()
    {
        $this->assertEquals('ns:child', PathHelper::getNodeName('/parent/ns:child'));
    }

    public function testGetNodeNameRoot()
    {
        $this->assertEquals('', PathHelper::getNodeName('/'));
    }

    public function testGetPathDepth()
    {
        $this->assertEquals(0, PathHelper::getPathDepth('/'));
        $this->assertEquals(1, PathHelper::getPathDepth('/foo'));
        $this->assertEquals(2, PathHelper::getPathDepth('/foo/bar'));
        $this->assertEquals(2, PathHelper::getPathDepth('/foo/bar/'));
    }
}
