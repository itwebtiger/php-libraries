<?php
namespace PHP\Collections;

use PHP\Collections\Sequence\iSequence;
use PHP\Collections\Sequence\iReadOnlySequence;

/**
 * Defines a mutable, ordered set of indexed values
 *
 * This would have been named "List" had that not been reserved by PHP
 */
class Sequence extends \PHP\Object implements iSequence
{
    
    /**
     * List of values
     *
     * @var array
     */
    private $items;
    
    /**
     * Type requirement for all values
     *
     * @var string
     */
    private $type;
    
    
    public function __construct( string $type = '' )
    {
        $this->Clear();
        $this->type = $type;
    }
    
    
    public function Add( $value ): int
    {
        $index = -1;
        if ( $this->isValueValidType( $value )) {
            $this->items[] = $value;
            $index         = $this->GetLastIndex();
        }
        else {
            trigger_error( 'Cannot add value that does not match the type constraints' );
        }
        return $index;
    }
    
    
    public function Clear()
    {
        return $this->items = [];
    }
    
    
    public function Clone(): iReadOnlyCollection
    {
        $clone = new static( $this->type );
        $this->Loop( function( $index, $value, &$clone ) {
            $clone->Add( $value );
        }, $clone );
        return $clone;
    }
    
    
    public function ConvertToArray(): array
    {
        return $this->items;
    }
    
    
    public function Count(): int
    {
        return count( $this->items );
    }
    
    
    public function Get( $index, $defaultValue = null )
    {
        $value = $defaultValue;
        if ( $this->HasIndex( $index )) {
            $value = $this->items[ $index ];
        }
        return $value;
    }
    
    
    public function GetFirstIndex(): int
    {
        return 0;
    }
    
    
    public function GetLastIndex(): int
    {
        return ( $this->Count() - 1 );
    }
    
    
    public function GetIndexOf( $value, int $offset = 0, bool $isReverseSearch = false ): int
    {
        // Variables
        $index = -1;
    
        // Exit. Offset cannot be negative.
        if ( $offset < $this->GetFirstIndex() ) {
            trigger_error( 'Offset cannot be less than the first item\'s index' );
            return $index;
        }
        
        // Exit. Offset cannot surpass the end of the array.
        elseif ( $this->GetLastIndex() < $offset ) {
            trigger_error( 'Offset cannot be greater than the last item\'s index' );
            return $index;
        }
            
        // Get the sub-sequence to traverse
        $sequence = $this->Clone();
        if ( $isReverseSearch ) {
            $sequence->Reverse();
        }
        $sequence = $sequence->Slice( $offset, $sequence->GetLastIndex() );
        
        // Search the sub-sequence for the value
        $_index = array_search( $value, $sequence->ConvertToArray() );
        if ( false !== $_index ) {
            
            // Invert index for reverse search. Keep in mind that the last
            // index is actually the first in the original order.
            if ( $isReverseSearch ) {
                $index = $sequence->GetLastIndex() - $_index;
            }
            
            // Add the offset to forward searches
            else {
                $index = $_index + $offset;
            }
        }
    
        return $index;
    }
    
    
    public function HasIndex( $index ): bool
    {
        return ( is( $index, 'integer' ) && array_key_exists( $index, $this->items ));
    }
    
    
    public function Insert( int $index, $value ): int
    {
        // Index too small
        if ( $index < $this->GetFirstIndex() ) {
            trigger_error( 'Cannot insert value before the beginning' );
            $index = -1;
        }
        
        // Index too large
        elseif (( $this->GetLastIndex() + 1 ) < $index ) {
            trigger_error( 'Cannot insert value after the end' );
            $index = -1;
        }
        
        // Invalid value type
        elseif ( !$this->isValueValidType( $value )) {
            trigger_error( 'Cannot insert value that does not match the type constraints' );
            $index = -1;
        }
        
        // Insert value at the index
        else {
            array_splice( $this->items, $index, 0, $value );
        }
        
        return $index;
    }
    
    
    public function Loop( callable $function, &...$args )
    {
        $parameters = array_merge( [ $function ], $args );
        $iterable   = new Iterable( $this->items );
        return call_user_func_array( [ $iterable, 'Loop' ], $parameters );
    }
    
    
    public function Remove( $index )
    {
        unset( $this->items[ $index ] );
        $this->items = array_values( $this->items );
    }
    
    
    public function Reverse()
    {
        $this->items = array_reverse( $this->items, false );
    }
    
    
    public function Slice( int $start, int $end ): iReadOnlySequence
    {
        // Variables
        $subArray = [];
        
        // Error. Ending index cannot be less than the starting index.
        if ( $end < $start ) {
            trigger_error( 'Ending index cannot be less than the starting index.' );
        }
        
        // Create array subset
        else {
            
            // Sanitize the starting index
            if ( $start < $this->GetFirstIndex() ) {
                trigger_error( 'Starting index cannot be less than the first index of the item list.' );
                $start = $this->GetFirstIndex();
            }
            
            // Sanitize the ending index
            if ( $this->GetLastIndex() < $end ) {
                trigger_error( 'Ending index cannot surpass the last index of the item list.' );
                $end = $this->GetLastIndex();
            }
            
            // For each entry in the index range, push them into the subset array
            for ( $i = $start; $i <= $end; $i++ ) {
                $subArray[] = $this->items[ $i ];
            }
        }
        
        // Create Sequence subset
        $subSequence = new static( $this->type );
        foreach ( $subArray as $value ) {
            $subSequence->Add( $value );
        }
        
        return $subSequence;
    }
    
    
    public function Split( $delimiter, int $limit = -1 ): iReadOnlySequence
    {
        // Variables
        $start       = $this->GetFirstIndex();
        $sequences   = [];
        $canContinue = true;
        
        // While there are items left
        do {
            
            // Halt loop if there are no entries
            if ( 0 === $this->Count() ) {
                $canContinue = false;
            }
            
            // Halt loop if the limit has been reached.
            elseif (( 0 <= $limit ) && ( $limit === count( $sequences ))) {
                $canContinue = false;
            }
            
            else {
                
                // Get index of the next delimiter
                $end = $this->GetIndexOf( $delimiter, $start );
                
                // Delimiter not found. The end is the very last element.
                if ( $end < 0 ) {
                    $end = $this->GetLastIndex() + 1;
                }
                    
                // Group the items between the start and end, excluding the delimiter
                $sequence = $this->Slice( $start, $end - 1 );
                if ( 1 <= $sequence->Count() ) {
                    $sequences[] = $sequence;
                }
                
                // Move start index and halt loop if at the end of the sequence
                $start = $end + 1;
                if ( $this->GetLastIndex() <= $start ) {
                    $canContinue = false;
                }
            }
        } while ( $canContinue );
        
        // Return sequence of sequences
        $sequence = new static( $this->GetType() );
        foreach ( $sequences as $_sequence ) {
            $sequence->Add( $_sequence );
        }
        return $sequence;
    }
    
    
    public function Update( $index, $value )
    {
        if ( !$this->HasIndex( $index )) {
            trigger_error( 'Update index does not exist' );
            $index = null;
        }
        elseif ( !$this->isValueValidType( $value )) {
            trigger_error( 'Cannot update value that does not match the type constraints' );
            $index = null;
        }
        else {
            $this->items[ $index ] = $value;
            $index = null;
        }
        return $index;
    }
    
    
    /**
     * Determine if the value meets the type requirements
     *
     * @param mixed $value The value to check
     * @return bool
     */
    private function isValueValidType( $value ): bool
    {
        return (( '' === $this->type ) || is( $value, $this->type ));
    }
}
