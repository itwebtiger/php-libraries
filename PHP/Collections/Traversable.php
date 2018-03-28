<?php
namespace PHP\Collections;

use PHP\Collections\TraversableSpec;

/**
 * Defines a set of entries that can be iterated over
 */
class Traversable extends \PHP\PHPObject implements TraversableSpec
{
    
    /**
     * The indexed set of entries
     *
     * @var array
     */
    private $entries;
    
    
    /**
     * Creates a new Traversable instance for the entries
     *
     * @param array $entries The indexed set of entries
     */
    public function __construct( array $entries )
    {
        $this->entries = $entries;
    }
    
    
    final public function loop( callable $function, &...$args )
    {
        foreach ( $this->entries as $index => $value ) {
            
            // Add index and value the callback function parameters
            $parameters = array_merge(
                [
                    $index,
                    $value
                ],
                $args
            );
            
            // Execute the callback function, exiting when a non-null value is returned
            $result = call_user_func_array( $function, $parameters );
            if ( null !== $result ) {
                return $result;
            }
        }
    }
}