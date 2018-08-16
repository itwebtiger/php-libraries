<?php
namespace PHP\Tests\TypesTest;

require_once( __DIR__ . '/../TypesData.php' );

use PHP\Types;
use PHP\Tests\TypesData;


/**
 * Tests the \PHP\Types\Type functionality
 */
class TypeTest extends \PHP\Tests\TestCase
{
    
    
    /**
     * Ensure Type->getName() returns the correct name
     */
    public function testGetNameReturnsCorrectName()
    {
        foreach ( TypesData::Get() as $data ) {
            $type  = self::getType( $data[ 'in' ] );
            $class = self::getClassName( $type );
            $this->assertEquals(
                $data[ 'out' ][ 'name' ],
                $type->getName(),
                "{$class}->getName() did not return the correct name"
            );
        }
    }
    
    
    /**
     * Ensure Type->getShortName() returns the correct short name
     */
    public function testGetShortNameReturnsCorrectShortName()
    {
        foreach ( TypesData::Get() as $data ) {
            $type  = self::getType( $data[ 'in' ] );
            $class = self::getClassName( $type );
            $this->assertEquals(
                $data[ 'out' ][ 'shortName' ],
                $type->getShortName(),
                "{$class}->getShortName() did not return the correct short name"
            );
        }
    }
    
    
    /**
     * Return the Type instance for the given data[ 'in' ] property
     *
     * @param array $dataIn The $data[ 'in' ] property
     * @return \PHP\Types\Type
     */
    private static function getType( array $dataIn ): \PHP\Types\Type
    {
        $type = null;
        if ( array_key_exists( 'value', $dataIn )) {
            $type = Types::GetByValue( $dataIn[ 'value' ] );
        }
        elseif ( array_key_exists( 'shortName', $dataIn )) {
            $type = Types::GetByName( $dataIn[ 'shortName'] );
        }
        elseif ( array_key_exists( 'name', $dataIn )) {
            $type = Types::GetByName( $dataIn[ 'name' ] );
        }
        else {
            throw new \Exception( 'Malformed Type test data. Type query is missing.' );
            
        }
        return $type;
    }
}
