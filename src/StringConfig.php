<?php

declare(strict_types=1);

namespace SamMcDonald\Stringfluent;

class StringConfig
{
    public function __construct(private string|false|null $encoding, private bool $containsMultiByteChars)
    {
    }

    public function getEncoding(): string|false|null
    {
        return $this->encoding;
    }

    public function containsMultiByteCharacters(): bool
    {
        return $this->containsMultiByteChars;
    }
}