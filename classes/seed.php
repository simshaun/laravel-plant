<?php namespace S2;

abstract class Seed {

    protected $references = array();

    // Determines when a seed is grown among other seeds
    public $order = PHP_INT_MAX;

    /**
     * Executed after a seed is planted.
     */
    public function grow() {}

    protected function addReference($id, $object)
    {
        $this->references[$id] = $object;
    }

    protected function getReference($id)
    {
        if (!isset($this->references[$id]))
        {
            throw new \Exception('The reference "'.$id.'" is not stored. Perhaps that seed was not ran.');
        }

        return $this->references[$id];
    }

    public function getReferences()
    {
        return $this->references;
    }

    public function setReferences($refs)
    {
        $this->references = $refs;
    }

}