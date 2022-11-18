<?php
    declare(strict_types=1);

    namespace sbf\components;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\extensions\Extension;

    trait ComponentCountableIteratorTraits {
        /**
         * Returns the current element.
         * @return mixed Can return any type.
         */
        public function current() {
            if (!$this->valid())
                return null;
            
            return $this[$this->GetComponentNames()[$this->iteratorIndex]];
            //return $this->components[$this->GetComponentNames()[$this->currentElement]];
        }
        
        /**
         * Returns the key of the current element.
         * @return mixed Returns `scalar` on success, or `null` on failure.
         */
        public function key() {
            if (!$this->valid())            
                return null;

            return array_keys($this->components)[$this->iteratorIndex];
        }
        
        /**
         * Move forward to next element
         * Moves the current position to the next element.
         */
        public function next() {
            $this->iteratorIndex ++;
            
            return $this->current();
        }
        
        /**
         * Rewind the Iterator to the first element
         * Rewinds back to the first element of the Iterator.
         */
        public function rewind() {
            $this->iteratorIndex = 0;
        }
        
        /**
         * Checks if current position is valid
         * This method is called after Iterator::rewind() and Iterator::next() to check if the current position is valid.
         * @return bool The return value will be casted to `bool` and then evaluated. Returns `true` on success or `false` on failure.
         */
        public function valid() {            
            if ($this->iteratorIndex >= count($this->components))
                return false;

            return true;
        }
        
        /**
         * Count elements of an object
         * This method is executed when using the count() function on an object implementing Countable.
         * @return int The custom count as an `int`.
         */
        public function count() {
            return count(
                array_filter(
                    $this->GetComponents("\\sbf\\components\\Component"), 
                    function ($v, $k) { return !($v instanceof Extension);}, 
                    ARRAY_FILTER_USE_BOTH
                )
            );            
        }
    }


?>