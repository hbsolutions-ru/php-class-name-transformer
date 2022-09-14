<?php declare(strict_types=1);

namespace HBS\ClassNameTransformer;

class ClassNameTransformer
{
    public const PATTERN_DELIMITER = '*';

    protected const NAMESPACE_PART_PATTERN = '([a-zA-Z_\\d\\\\]*)';

    protected string $classNamePattern;

    protected array $typeResolvers = [];

    public function __construct(string $classNamePattern, array $typeResolvers)
    {
        $this->classNamePattern = $this->parseClassNamePattern($classNamePattern);
        $this->typeResolvers = $typeResolvers;
    }

    public function resolve(string $sourceClassName, string $targetType): string
    {
        if (!isset($this->typeResolvers[$targetType])) {
            throw new Exception\ResolvingException(sprintf("Resolver for type '%s' not found", $targetType));
        }

        $matchResult = preg_match($this->classNamePattern, $sourceClassName, $match);

        if (!($matchResult === 1  && isset($match[1]) && is_string($match[1]))) {
            throw new Exception\ParsingException('Class Name pattern not matched');
        }

        if (!strlen($match[1])) {
            throw new Exception\ParsingException('Class Name matched part is empty');
        }

        $className = str_replace(self::PATTERN_DELIMITER, $match[1], $this->typeResolvers[$targetType]);

        if (!class_exists($className)) {
            throw new Exception\ResolvingException(sprintf("Class '%s' not found", $className));
        }

        return $className;
    }

    protected function parseClassNamePattern(string $classNamePattern): string
    {
        $classNamePattern = str_replace('\\', '\\\\', $classNamePattern);
        $parts = explode(self::PATTERN_DELIMITER, $classNamePattern);

        if (count($parts) > 2) {
            throw new Exception\InitException('Only one delimiter allowed for Class Name pattern');
        }

        return implode('', [
            '/^',
            $parts[0],
            self::NAMESPACE_PART_PATTERN,
            $parts[1] ?? '',
            '$/',
        ]);
    }
}
