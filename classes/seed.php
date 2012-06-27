<?php namespace S2;

abstract class Seed {

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

}