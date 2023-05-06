<?php

declare(strict_types=1);

namespace SamMcDonald\Stringfluent;

use http\Exception\UnexpectedValueException;
use Stringable;

/**
 * Stringfluent
 *
 * Licence
 * @url https://opensource.org/licenses/MIT
 *
 * Copyright 2023 Sam McDonald
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */

final class Stringfluent implements Stringable
{
    private const EMPTY_STRING_VALUE = '';

    private const USE_STRICT_ENCODING = true;

    private const SPLIT_AS_ARRAY_REGEX = '//u';

    private bool $containsMultiByte;

    private string $encoding;

    private StringConfig $config;

    private function __construct(
        private Stringable|string $value
    ) {
        $this->encoding = self::detectEncoding($this->toString());
        $this->containsMultiByte = self::detectContainsMultibyteCharacters($this->toString());
        $this->config = new StringConfig(
            $this->encoding,
            $this->containsMultiByte
        );
    }

    public static function create(Stringable|string $string): Stringfluent
    {
        return new Stringfluent($string);
    }

    public static function empty(): Stringfluent
    {
        return self::create(self::EMPTY_STRING_VALUE);
    }

    public function trim() : Stringfluent
    {
        if ($this->config->containsMultiByteCharacters()) {
            return Stringfluent::create(
                $this->mbTrim($this->toString())
            );
        }

        return Stringfluent::create(
            trim($this->toString())
        );
    }

    private function mbTrim(string $str): string
    {
        return (string) preg_replace(
            "/^\s+|\s+$/u",
            "", $str
        );
    }

    public function toUpperCase() : Stringfluent
    {
        if ($this->config->containsMultiByteCharacters()) {
            return Stringfluent::create(
                mb_strtoupper($this->toString())
            );
        }

        return Stringfluent::create(
            strtoupper($this->toString())
        );
    }

    public function toLowerCase() : Stringfluent
    {
        if ($this->config->containsMultiByteCharacters()) {
            return Stringfluent::create(
                mb_strtolower($this->toString())
            );
        }

        return Stringfluent::create(
            strtolower($this->toString())
        );
    }

    public function toUcFirst() : Stringfluent
    {
        if ($this->config->containsMultiByteCharacters()) {
            $ucfirst = mb_strtoupper(mb_substr($this->value, 0, 1), $this->config->getEncoding());
            return Stringfluent::create(
                $ucfirst.mb_substr($this->value, 1, ($this->length() - 1), $this->config->getEncoding())
            );
        }
        return Stringfluent::create(
            ucfirst($this->toString())
        );
    }

    public function toTitleCase(): Stringfluent
    {
        if ($this->config->containsMultiByteCharacters()) {
            $string = \mb_convert_case($this->toString(), \MB_CASE_TITLE, $this->config->getEncoding());
            return Stringfluent::create($string);
        }

        return Stringfluent::create(
            ucwords($this->toLowerCase()->toString())
        );
    }

    public function prepend(Stringable|string ...$string) : Stringfluent
    {
        return Stringfluent::create(
            implode(
                self::EMPTY_STRING_VALUE,
                [
                    ...$string,
                    $this->toString(),
                ]
            )
        );
    }

    public function append(Stringable|string ...$string) : Stringfluent
    {
        return Stringfluent::create(
            implode(
                self::EMPTY_STRING_VALUE,
                [
                    $this->toString(),
                    ...$string
                ]
            )
        );
    }

    public function concat(Stringable|string ...$string): Stringfluent
    {
        return Stringfluent::create($this->toString())->append($string);
    }

    public function encase(Stringable|string $start, ?string $end = null) : Stringfluent
    {
        $end = ($end == null) ? $start : $end;
        return Stringfluent::create(
            $start . $this->toString() . $end
        );
    }

    public function truncate(int $maxLength) : Stringfluent
    {
        if($this->length() > $maxLength) {
            return Stringfluent::create($this->substring(0, $maxLength)->toString());
        }

        return $this;
    }

    public function substring(int $start = 0, int $length = null): Stringfluent
    {
        if ($this->config->containsMultiByteCharacters()) {
            return Stringfluent::create(\mb_substr($this->value, $start, $length, $this->config));
        }

        return Stringfluent::create(substr($this->value, $start, $length));
    }

    public function padLeft(Stringable|string $char = '0', $length = 2): Stringfluent
    {
        return Stringfluent::create(str_pad($this->toString(), $length, (string)$char, STR_PAD_LEFT));
    }

    public function padRight(Stringable|string $char = '0', $length = 2): Stringfluent
    {
        return Stringfluent::create(str_pad($this->toString(), $length, $char, STR_PAD_RIGHT));
    }

    public function reverse(): Stringfluent
    {
        $reversed = '';

        if ($this->config->containsMultiByteCharacters()) {
            $strLength = $this->length();

            // Loop starting from last index of string to first
            for ($i = $strLength - 1; $i >= 0; $i--) {
                $reversed .= \mb_substr($this->value, $i, 1, $this->config->getEncoding());
            }
        } else {
            $reversed = strrev($this->value);
        }

        return Stringfluent::create($reversed);
    }

    public function repeat(int $by = 2): Stringfluent
    {
        return Stringfluent::Create(
            str_repeat($this->value, $by)
        );
    }

    public function replace(string $dirty): Stringfluent
    {
        return Stringfluent::create(
            str_replace($dirty, '', $this->toString())
        );
    }

    public function stripHtmlTags(Stringable|string|array $allowableTags = ''): Stringfluent
    {
        $allowableTags = (is_array($allowableTags)) ? $allowableTags: (string) $allowableTags;

        return Stringfluent::create(
            strip_tags($this->toString(), $allowableTags)
        );
    }

    public function encodeHtml(int $flags = ENT_COMPAT): Stringfluent
    {
        return Stringfluent::create(
            htmlentities(
                $this->toString(),
                $flags,
                $this->config->getEncoding()
            )
        );
    }

    public function decodeHtml(int $flags = ENT_COMPAT): Stringfluent
    {
        return Stringfluent::create(
            html_entity_decode(
                $this->toString(),
                $flags,
                $this->config->getEncoding()
            )
        );
    }

    public function shuffleCharacters(): Stringfluent
    {
        $shuffled = '';
        if ($this->config->containsMultiByteCharacters()) {
            $indexes = range(0, $this->length() - 1);

            shuffle($indexes);

            foreach ($indexes as $i) {
                $shuffled .= \mb_substr($this->value, $i, 1, $this->config->getEncoding());
            }
        } else {
            $shuffled = str_shuffle($this->value);
        }

        return Stringfluent::create($shuffled);
    }

    public function charAt(int $index): string
    {
        return $this->substring($index, 1)->toString();
    }

    public function equalTo(Stringable|string $string) : bool
    {
        if($string instanceof Stringfluent) {
            return $this->toString() === $string->toString();
        }

        return ((string)($string) === $this->toString());
    }

    public function startsWith(Stringable|string $string) : bool
    {
        return str_starts_with($this->toString(), $string);
    }

    public function endsWith(Stringable|string $string) : bool
    {
        return str_ends_with($this->toString(), $string);
    }

    public function contains(Stringable|string $string, $case_sensitive = true) : bool
    {
        $str = (string) $string;
        if ($this->config->containsMultiByteCharacters()) {
            return (bool) \mb_substr_count($this->toString(), $str, $this->config->getEncoding());
        }
        return (bool) substr_count($this->toString(), $str);
    }

    public function containsMultibyteCharacters() : bool
    {
        return $this->config->containsMultiByteCharacters();
    }

    public function containsPunctuation() : bool
    {
        $result = preg_match(
            "/^[a-z0-9.,!?:;\"\-_\']+$/i",
            $this->toString()
        );
        if ($result === false) {
            return false;
        }
        return true;
    }

    public function isAllPrintableCharacters() : bool
    {
        return ctype_graph($this->toString());
    }

    public function isAllPunctuationCharacters() : bool
    {
        return ctype_punct($this->toString());
    }

    public function isAlphaCharacters() : bool
    {
        return ctype_alpha($this->toString());
    }

    public function isAlphaNumericCharacters() : bool
    {
        return ctype_alnum($this->toString());
    }

    public function isDigitCharacters() : bool
    {
        return ctype_digit($this->toString());
    }

    public function compare(Stringable|string $string) : int
    {
        if($string instanceof Stringfluent) {
            return -strcmp($this->toString(), $string->toString());
        }

        if(is_string($string)) {
            return -strcmp($this->value, $string);
        }

        return (int) ($this->toString() === (string) $string);

    }

    public function indexOf($needle, $offset = 0, bool $case_sensitive = true) : int
    {
        if ($this->config->containsMultiByteCharacters()) {
            return ($case_sensitive) ? mb_strpos($this->value, $needle, $offset) : mb_stripos($this->value, $needle, $offset);
        }

        return ($case_sensitive) ? strpos($this->value, $needle, $offset) : stripos($this->value, $needle, $offset);
    }

    public function lastIndexOf(string $needle = '', int $offset = 0, bool $case_sensitive = true): int
    {
        if ($this->config->containsMultiByteCharacters()) {
            return ($case_sensitive) ? mb_strrpos($this->value, $needle, $offset) : mb_strripos($this->value, $needle, $offset);
        }

        return ($case_sensitive) ? strrpos($this->value, $needle, $offset) : strripos($this->value, $needle, $offset);
    }

    public function charCount(): int
    {
        return count($this->toArray());
    }

    public function wordCount(): int
    {
        return str_word_count($this->toString());
    }

    public function count(): int
    {
        if ($this->config->containsMultiByteCharacters()) {
            return mb_strlen($this->toString());
        }
        return strlen($this->toString());
    }

    public function length(): int
    {
        return $this->count();
    }

    public function enCrypt(string $salt): Stringfluent
    {
        return Stringfluent::create(crypt($this->toString(), $salt));
    }

    public function enHashToMd5(): Stringfluent
    {
        return Stringfluent::create(
            md5($this->toString())
        );
    }

    public function enHashToSha1(): Stringfluent
    {
        return Stringfluent::create(
            sha1($this->toString())
        );
    }

    public function enHashToSha256(): Stringfluent
    {
        return Stringfluent::create(
            hash("sha256" , $this->toString(),false)
        );
    }

    public function toArray(): array
    {
        if ($this->config->containsMultiByteCharacters()) {
             $arrayResult = mb_split(self::SPLIT_AS_ARRAY_REGEX, $this->toString());
        } else {
            $arrayResult = preg_split(self::SPLIT_AS_ARRAY_REGEX, $this->toString());
        }

        if (!is_array($arrayResult)) {
            throw new UnexpectedValueException('Unable to split string into array');
        }

        return $arrayResult;
    }

    public function toString(): string
    {
        return (string) $this->value;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    private static function detectEncoding(string $string): string
    {
        return \mb_detect_encoding(
            $string,
            \mb_detect_order(),
            self::USE_STRICT_ENCODING
        );
    }

    private static function detectContainsMultibyteCharacters(string $string) : bool
    {
        return (mb_strlen($string) != strlen($string));
    }
}

