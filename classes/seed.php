<?php namespace S2;

abstract class Seed {

    protected $references = array();

    /**
     * Executed after a seed is planted.
     */
    public function grow() {}

    /**
     * Determines when a seed is grown among other seeds.
     * @return int
     */
    public function order() {
        return 999999999;
    }

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