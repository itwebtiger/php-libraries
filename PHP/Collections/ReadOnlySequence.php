<?php
namespace PHP\Collections;

/**
 * Defines a read-only, ordered, and iterable set of key-value pairs
 */
class ReadOnlySequence extends ReadOnlyCollection implements ReadOnlySequenceSpec
{
    
    /**
     * Create a read-only sequence instance
     *
     * As entries are added to / removed from the sequence, the changes will
     * be reflected here. To change that, simply clone() this after creation.
     *
     * @param SequenceSpec &$sequence The sequence to make read-only
     */
    public function __construct( SequenceSpec &$sequence )
    {
        parent::__construct( $sequence );
    }
    
    
    public function clone(): ReadOnlyCollectionSpec
    {
        $clone = $this->collection->clone();
        return new self( $clone );
    }
    
    public function convertToArray(): array
    {
        return $this->collection->convertToArray();
    }
    
    public function getFirstKey(): int
    {
        return $this->collection->getFirstKey();
    }
    
    public function getLastKey(): int
    {
        return $this->collection->getLastKey();
    }
    
    public function getKeyOf( $value, int $offset = 0, bool $isReverseSearch = false ): int
    {
        return $this->collection->getKeyOf( $value, $offset, $isReverseSearch );
    }
    
    public function slice( int $startingKey, int $count ): ReadOnlySequenceSpec
    {
        $sequence = $this->collection->slice( $startingKey, $count );
        return new self( $sequence );
    }
    
    public function split( $delimiter, int $limit = -1 ): ReadOnlySequenceSpec
    {
        // Variables
        $splitSequence = $this->collection->split( $delimiter, $limit );
        $outerSequence = new \PHP\Collections\Sequence( __CLASS__ );
        
        // For each inner sequence, make it read-only and add it to the outer
        foreach ( $splitSequence as $innerSequence ) {
            $outerSequence->add( new self( $innerSequence ));
        }
        return new self( $outerSequence );
    }
}