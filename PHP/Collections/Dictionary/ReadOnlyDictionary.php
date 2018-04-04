<?php
namespace PHP\Collections\Dictionary;

use \PHP\Collections\Collection\ReadOnlyCollectionSpec;
use \PHP\Collections\DictionarySpec;
use PHP\Collections\Sequence\ReadOnlySequenceSpec;

/**
 * Defines a read only, unordered set of indexed values
 */
class ReadOnlyDictionary extends \PHP\PHPObject implements ReadOnlyDictionarySpec
{
    
    /**
     * The dictionary instance
     *
     * @var DictionarySpec
     */
    private $dictionary;
    
    
    /**
     * Create a new read-only Dictionary instance
     *
     * As entries are added to / removed from the dictionary, the changes will
     * be reflected here. To change that, simply clone() this after creation.
     *
     * @param DictionarySpec &$dictionary The dictionary to make read-only
     */
    public function __construct( DictionarySpec &$dictionary )
    {
        $this->dictionary = $dictionary;
    }
    
    
    public function clone(): ReadOnlyCollectionSpec
    {
        $class           = get_class( $this );
        $dictionaryClone = $this->dictionary->clone();
        return new $class( $dictionaryClone );
    }
    
    
    public function convertToArray(): array
    {
        return $this->dictionary->convertToArray();
    }
    
    public function count(): int
    {
        return $this->dictionary->count();
    }
    
    public function get( $index )
    {
        return $this->dictionary->get( $index );
    }
    
    public function getIndices(): ReadOnlySequenceSpec
    {
        return $this->dictionary->getIndices();
    }
    
    public function getValues(): ReadOnlySequenceSpec
    {
        return $this->dictionary->getValues();
    }
    
    public function hasIndex( $index ): bool
    {
        return $this->dictionary->hasIndex( $index );
    }
    
    public function loop( callable $function, &...$args )
    {
        $args = array_merge( [ $function ], $args );
        return call_user_func_array( [ $this->dictionary, 'loop' ], $args );
    }
}
