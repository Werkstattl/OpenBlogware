<?php declare(strict_types=1);

namespace Sas\BlogModule\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TextExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('truncate', [$this, 'twig_truncate_filter']),
        ];
    }

    function twig_truncate_filter($value, $length = 30, $preserve = false, $separator = '...')
    {
        if (mb_strlen($value) > $length) {
            if ($preserve) {
                // If breakpoint is on the last word, return the value without separator.
                if (false === ($breakpoint = mb_strpos($value, ' ', $length))) {
                    return $value;
                }

                $length = $breakpoint;
            }

            return rtrim(mb_substr($value, 0, $length)).$separator;
        }

        return $value;
    }

}
