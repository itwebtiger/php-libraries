<?php
namespace PHP\Collections;

/**
 * Defines a read only, unordered set of key-value pairs
 */
class ReadOnlyDictionary extends ReadOnlyCollection implements ReadOnlyDictionarySpec
{
    
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
        parent::__construct( $dictionary );
    }
    
    
    public function clone(): ReadOnlyCollectionSpec
    {
        $clone = $this->collection->clone();
        return new self( $clone );
    }
}