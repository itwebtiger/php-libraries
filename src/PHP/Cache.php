<?php
namespace PHP;

/**
 * Caches and retrieves items from system memory
 */
class Cache
{
    /**
     * Memory cache
     *
     * @var array
     */
    protected $cache;
    
    /**
     * Are the items in this cache complete?
     *
     * @var bool
     */
    protected $isComplete;
    
    
    /**
     * Create new cache instance
     *
     * @param array $items Key => value item pairs
     * @param bool  $markCacheComplete After setting items, mark the cache complete
     */
    public function __construct( array $items = [], bool $markCacheComplete = false )
    {
        $this->set( $items, $markCacheComplete );
    }
    
    
    /***************************************************************************
    *                             CACHE OPERATIONS
    ***************************************************************************/
    
    /**
     * Add cached value; fails if key already exists
     *
     * @param int|string $key   Key to store the value at
     * @param mixed      $value The value to store
     * @return int|string Sanitized key. NULL on failure.
     */
    public function add( $key, $value )
    {
        $key = self::sanatizeKey( $key );
        if ( !$this->isSet( $key )) {
            $key = $this->update( $key, $value );
        }
        return $key;
    }
    
    
    /**
     * Add or replace cached value
     *
     * @param int|string $key   Key to store the value at
     * @param mixed      $value The value to store
     * @return int|string Sanitized key. NULL on failure.
     */
    public function update( $key, $value )
    {
        $key = self::sanatizeKey( $key );
        if ( isset( $key )) {
            $this->cache[ $key ] = $value;
        }
        return $key;
    }
    
    
    /**
     * Set all the cache items
     *
     * @param array $items             Key => value item pairs
     * @param bool  $markCacheComplete After setting items, mark the cache complete
     */
    public function set( array $items, bool $markCacheComplete = true )
    {
        $this->cache = [];
        foreach ( $items as $key => $value) {
            $this->update( $key, $value );
        }
        if ( $markCacheComplete ) {
            $this->markComplete();
        }
        else {
            $this->markIncomplete();
        }
    }
    
    
    /***************************************************************************
    *                                CACHE STATUS
    ***************************************************************************/
    
    /**
     * Is the cache key set?
     *
     * @param int|string $key Key to store the value at
     * @return bool
     */
    public function isSet( $key )
    {
        $key = self::sanatizeKey( $key );
        return ( isset( $key ) && array_key_exists( $key, $this->cache ));
    }
    
    
    /**
     * Has this cache been marked complete?
     *
     * Useful for flagging an interative cache as "complete", to prevent further
     * lookups.
     *
     * @return bool
     */
    public function isComplete()
    {
        return $this->isComplete;
    }
    
    
    /**
     * Mark cache as complete
     *
     * Useful for flagging an interative cache as "complete", to prevent further
     * lookups.
     */
    public function markComplete()
    {
        $this->isComplete = true;
    }
    
    
    /**
     * Mark cache as incomplete
     *
     * Useful for flagging an interative cache as "complete", to prevent further
     * lookups.
     */
    public function markIncomplete()
    {
        $this->isComplete = false;
    }
    
    
    /***************************************************************************
    *                                STATIC HELPERS
    ***************************************************************************/
    
    /**
     * Sanitize the cache key
     *
     * @param mixed $key The cache key
     * @return mixed NULL will be returned if invalid
     */
    final protected static function sanatizeKey( $key )
    {
        $type = gettype( $key );
        
        // Sanatize strings; attempting to convert strings to integers
        if ( 'string' == $type ) {
            $key = trim( $key );
            if ( 0 !== intval( $key )) {
                $key = intval( $key );
            }
            elseif ( '0' == $key ) {
                $key = 0;
            }
            elseif ( '' == $key ) {
                $key = NULL;
            }
        }
        
        // Set key as invalid
        elseif ( 'integer' != $type ) {
            $key = NULL;
        }
        
        return $key;
    }
}
