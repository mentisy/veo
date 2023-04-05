<?php
declare(strict_types=1);

namespace Avolle\Veo\Entity;

use ReflectionClass;

/**
 * Base entity class
 */
abstract class Entity
{
    /**
     * Constructor method. Set fields based on provided properties array.
     *
     * @param array $properties Properties to set in entity
     */
    public function __construct(array $properties)
    {
        foreach ($properties as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Get property names for class
     *
     * @return array
     */
    public static function getProperties(): array
    {
        $reflection = new ReflectionClass(self::class);

        return array_keys($reflection->getDefaultProperties());
    }

    /**
     * Get properties in class as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        $props = [];
        foreach ($this->getProperties() as $key) {
            $props[$key] = $this->$key;
        }

        return $props;
    }
}
