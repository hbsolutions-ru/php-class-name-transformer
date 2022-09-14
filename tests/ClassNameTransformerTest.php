<?php declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use HBS\ClassNameTransformer\ClassNameTransformer;
use Tests\App\{
    Controller\GetUserAction,
    Service\GetUserProvider,
    Service\ServiceInterface,
};

final class ClassNameTransformerTest extends TestCase
{
    public function testResolve(): void
    {
        $transformer = new ClassNameTransformer(
            'Tests\\App\\Controller\\*Action',
            [
                ServiceInterface::class => 'Tests\\App\\Service\\*Provider',
            ]
        );

        $className = $transformer->resolve(GetUserAction::class, ServiceInterface::class);

        $this->assertEquals(GetUserProvider::class, $className);
    }
}
