<?php

namespace App\GraphQL\Scalars;

use GraphQL\Error\Error;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;
use Illuminate\Support\Carbon;

class Date extends ScalarType
{
    public string $name = 'Date';

    public ?string $description = 'A date string with format Y-m-d, e.g. 2011-05-23.';

    public function serialize($value)
    {
        if ($value instanceof Carbon) {
            return $value->format('Y-m-d'); 
        }

        return $value;
    }

    public function parseValue($value)
    {
        if (! is_string($value)) {
            throw new Error('Date must be a string');
        }

        if (! preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $matches)) {
            throw new Error('Date must be in format Y-m-d');
        }

        if (! checkdate($matches[2], $matches[3], $matches[1])) {
            throw new Error('Date is not valid');
        }

        return $value;
    }

    public function parseLiteral($valueNode, array $variables = null)
    {
        if (! $valueNode instanceof StringValueNode) {
            throw new Error('Date must be a string');
        }

        return $this->parseValue($valueNode->value);
    }
}
