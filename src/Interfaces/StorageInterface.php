<?php

namespace App\Interfaces;
/**
 * This is for the implementation of a simple storage component, which takes
 * a string for the key and an array for the value.
 * It is designed to be as simple as possible, trading simplicity with flexibility,
 * that is why every data must be store in an array before giving it to the set method.
 */
interface StorageInterface {
    /**
     * @return bool returns whether result was stored successfully
     */
    public function set(string $key, array $data):bool;
    public function get(string $key) : array;
}
