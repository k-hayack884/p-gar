<?php

namespace Tests\Architecture;

use App\Domain\ValueObjects\UserId;
use App\Infrastructure\Repositories\Concerns\ScopesByOwnUser;
use App\Infrastructure\Repositories\Concerns\ScopesByParentUser;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use Symfony\Component\Finder\Finder;
use Tests\TestCase;

class UserScopeTest extends TestCase
{
    public function test_every_eloquent_repository_declares_a_user_scope_trait_and_user_id_argument(): void
    {
        $repositoryDirectory = app_path('Infrastructure/Repositories');
        $repositoryClasses = [];

        foreach ((new Finder)->files()->in($repositoryDirectory)->name('*EloquentRepository.php') as $file) {
            $relativePath = $file->getRelativePathname();
            $relativeClass = str_replace(['/', '.php'], ['\\', ''], $relativePath);
            $repositoryClasses[] = "App\\Infrastructure\\Repositories\\{$relativeClass}";
        }

        $this->assertNotEmpty($repositoryClasses, 'At least one Eloquent repository must exist.');

        foreach ($repositoryClasses as $repositoryClass) {
            $reflection = new ReflectionClass($repositoryClass);
            $traits = class_uses($repositoryClass) ?: [];

            $this->assertTrue(
                isset($traits[ScopesByOwnUser::class]) || isset($traits[ScopesByParentUser::class]),
                "{$repositoryClass} must use an explicit user-scope trait.",
            );

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->isConstructor() || $method->getDeclaringClass()->getName() !== $repositoryClass) {
                    continue;
                }

                $hasUserIdParameter = collect($method->getParameters())
                    ->contains(function ($parameter): bool {
                        $type = $parameter->getType();

                        return $type instanceof ReflectionNamedType && $type->getName() === UserId::class;
                    });

                $this->assertTrue(
                    $hasUserIdParameter,
                    "{$repositoryClass}::{$method->getName()} must accept UserId.",
                );
            }
        }
    }
}
