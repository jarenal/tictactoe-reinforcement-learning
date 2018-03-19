<?php

namespace Jarenal\Core;

use Jarenal\Core\View\View;
use Jarenal\Core\Model\Game;
use Jarenal\Core\IA\IA;

class Container
{
    private $instances = [];
    private static $instance;

    protected function __construct(array $settings = [])
    {
        $default = [
            'view' => new View(),
            'game' => Game::getInstance(),
            'IA' => IA::getInstance(__DIR__ . "/../../bin/q_table.csv", 0.8),
        ];

        $this->instances = array_merge($default, $settings);
    }

    public static function getInstance(array $settings = [])
    {
        if(!isset(self::$instance)) {
            $className = get_called_class();
            self::$instance = new $className($settings);
        }

        return self::$instance;
    }

    private function __clone()
    {
        // Forbidden
    }

    private function __wakeup()
    {
        // Forbidden
    }

    public function get($instance)
    {

        if (array_key_exists($instance, $this->instances)) {
            return $this->instances[$instance];
        } else {
            return null;
        }

    }

    public function set($alias, $instance)
    {
        $this->instances[$alias] = $instance;
    }
}