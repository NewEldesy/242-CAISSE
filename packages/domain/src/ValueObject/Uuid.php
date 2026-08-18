<?php

declare(strict_types=1);

namespace Caisse\Domain\ValueObject;

use Caisse\Domain\Exception\InvalidUuidException;

final class Uuid
{
    private function __construct(
        private string $value
    ) {
        if (!self::isValid($value)) {
            throw new InvalidUuidException("Invalid UUID: {$value}");
        }
    }

    public static function generate(): self
    {
        return new self(self::v4());
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    private static function v4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    private static function isValid(string $value): bool
    {
        return (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
