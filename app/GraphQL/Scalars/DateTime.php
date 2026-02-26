<?php

namespace App\GraphQL\Scalars;

use GraphQL\Error\Error;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use Illuminate\Support\Carbon;

class DateTime extends ScalarType
{
    public string $name = 'DateTime';

    public ?string $description = 'A datetime string with format Y-m-d H:i:s, e.g. 2011-05-23 13:42:11.';

    public function serialize($value)
    {
        if ($value instanceof Carbon) {
            return $value->format('Y-m-d H:i:s');
        }

        return $value;
    }

    public function parseValue($value)
    {
        if (! is_string($value)) {
            throw new Error('DateTime must be a string');
        }

        try {
            $date = Carbon::createFromFormat('Y-m-d H:i:s', $value);
            if (! $date) {
                throw new Error('DateTime is not valid');
            }
        } catch (\Exception $e) {
            throw new Error('DateTime must be in format Y-m-d H:i:s');
        }

        return $value;
    }

    public function parseLiteral($valueNode, array $variables = null)
    {
        if (! $valueNode instanceof StringValueNode) {
            throw new Error('DateTime must be a string');
        }

        return $this->parseValue($valueNode->value);
    }
}
