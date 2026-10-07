<?php

// Dependency Injection sederhana: membuat object sekaligus mengisi dependency
// constructor-nya berdasarkan type-hint.
//   MahasiswaController(MahasiswaRepository) -> MahasiswaRepository(Database) -> Database::getInstance()
class Container
{
    public static function make(string $class): object
    {
        $constructor = (new ReflectionClass($class))->getConstructor();

        if ($constructor === null) {
            return new $class();
        }

        $args = [];
        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();

            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                throw new RuntimeException("Dependency \${$param->getName()} pada $class tidak bisa di-resolve.");
            }

            $dependency = $type->getName();
            $args[]     = $dependency === Database::class
                ? Database::getInstance()
                : self::make($dependency);
        }

        return new $class(...$args);
    }
}
