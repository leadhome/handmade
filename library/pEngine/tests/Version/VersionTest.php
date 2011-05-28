<?php

class pEngine_VersionTest extends PHPUnit_Framework_TestCase
{
    public function testGetVersion()
    {
        //get version
        $v = pEngine_Version::getVersion();
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $v);

        //compare version
        $this->assertNotEquals(pEngine_Version::compareVersion('120.4.1'), 1);
        $this->assertEquals(pEngine_Version::compareVersion('0.0.1'), 1);
    }
}