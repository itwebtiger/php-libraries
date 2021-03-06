<?php

use PHP\Debug\StackTrace;

/**
 * StackTrace tests
 */
class StackTraceTest extends \PHPUnit\Framework\TestCase
{
    
    /***************************************************************************
    *                                  Get()
    ***************************************************************************/
    
    /**
     * Does Get() return an array?
     */
    public function testGetReturnsAnArray()
    {
        $this->assertInternalType(
            'array',
            StackTrace::Get(),
            "StackTrace::Get() didn't return an array, as expected"
        );
    }
    
    
    /**
     * Ensure Get() array is not empty
     */
    public function testGetReturnsNonEmptyArray()
    {
        $this->assertTrue(
            ( 0 !== count( StackTrace::Get() )),
            "StackTrace::Get() returned an empty array"
        );
    }
    
    
    
    
    /***************************************************************************
    *                                 ToString()
    ***************************************************************************/
    
    /**
     * Does ToString() return a string?
     */
    public function testToStringReturnsString()
    {
        $this->assertInternalType(
            'string',
            StackTrace::ToString(),
            "StackTrace::ToString() didn\'t return a string, as expected"
        );
    }
    
    
    /**
     * Ensure ToString() result is not empty
     */
    public function testToStringReturnsNonEmptyString()
    {
        $this->assertTrue(
            ( 0 !== strlen( StackTrace::ToString() )),
            "StackTrace::ToString() returned an empty string"
        );
    }
}
