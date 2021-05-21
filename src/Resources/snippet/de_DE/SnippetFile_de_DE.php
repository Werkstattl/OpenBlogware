<?php declare(strict_types=1);

namespace Sas\BlogModule\Resources\snippet\de_DE;

use Shopware\Core\System\Snippet\Files\SnippetFileInterface;

class SnippetFile_de_DE implements SnippetFileInterface
{
    public function getName(): string
    {
        return 'storefront.de-DE';
    }

    public function getPath(): string
    {
        return __DIR__ . '/storefront.de-DE.json';
    }

    public function getIso(): string
    {
        return 'de-DE';
    }

    public function getAuthor(): string
    {
        return 'ChristopherDosin';
    }

    public function isBase(): bool
    {
        return false;
    }
}
