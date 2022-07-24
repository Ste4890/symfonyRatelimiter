<?php

namespace App\Services;

use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisStorage implements \App\Interfaces\StorageInterface {
    /** @var \Redis adapter */
    public \Redis $adapter;

    public function __construct(string $redisUrl) {
        $this->adapter = RedisAdapter::createConnection($redisUrl);
    }

    public function set(string $key, array $data): bool {
        $result = true;
        try {
            $data = json_encode($data, JSON_THROW_ON_ERROR);
            $this->adapter->set($key, $data);
        } catch (\JsonException $e) {
            $result = false;
            //fixme: handle improper json formatting

        } catch (\Exception $e) {
            $result = false;
            //fixme: handle other kind of errors (like redis server gone away...)
        } finally {
            return $result;
        }

    }

    public function get(string $key): array {
        // $storedValue could also be an empty array,
        // but also the value "false" if the key was not stored
        $storedValue = $this->adapter->get($key);
        if (empty($storedValue)) {
            return [];
        }
        return json_decode($storedValue, true);

    }
}
