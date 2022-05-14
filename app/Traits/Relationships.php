<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;

trait Relationships
{
/**
 * Get eloquent relationships
 *
 * @return array
 */
public static function getRelationships()
{
    $instance = new static;

    // Get public methods declared without parameters and non inherited
    $class = get_class($instance);
    $allMethods = (new \ReflectionClass($class))->getMethods(\ReflectionMethod::IS_PUBLIC);
    $methods = array_filter(
        $allMethods,
        function ($method) use ($class) {
            return $method->class === $class
                   && !$method->getParameters()                  // relationships have no parameters
                   && $method->getName() !== 'getRelationships'; // prevent infinite recursion
        }
    );

    \DB::beginTransaction();

    $relations = [];
    foreach ($methods as $method) {
        try {
            $methodName = $method->getName();
            $methodReturn = $instance->$methodName();
            if (!$methodReturn instanceof Relation) {
                continue;
            }
        } catch (\Throwable $th) {
            continue;
        }

        $type = (new \ReflectionClass($methodReturn))->getShortName();
        $model = get_class($methodReturn->getRelated());
        $relations[$methodName] = [$type, $model];
    }

    \DB::rollBack();

    return $relations;
}
}