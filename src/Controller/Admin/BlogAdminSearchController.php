<?php

declare(strict_types=1);

namespace Werkl\OpenBlogware\Controller\Admin;

use Shopware\Core\Framework\Api\Serializer\JsonEntityEncoder;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Werkl\OpenBlogware\Framework\Search\Admin\BlogEntryAdminSearchHandler;

/**
 * Controller for blog-related admin search functionality
 * Provides an API endpoint to integrate blog entries into the global admin search
 */
#[Package('inventory')]
class BlogAdminSearchController extends AbstractController
{
    public function __construct(
        private readonly BlogEntryAdminSearchHandler $searchHandler,
        private readonly DefinitionInstanceRegistry $definitionRegistry,
        private readonly JsonEntityEncoder $entityEncoder
    ) {}

    /**
     * Search for blog entries and categories in the admin
     */
    #[Route(path: '/api/_admin/blog-search', name: 'api.admin.blog-search', methods: ['POST'], defaults: ['_routeScope' => ['administration']])]
    public function search(Request $request, Context $context): JsonResponse
    {
        $term = trim($request->request->getString('term', ''));
        
        if ($term === '') {
            return new JsonResponse(['data' => []]);
        }
        
        $limit = (int) $request->get('limit', 10);
        
        // Search blog entries
        $entryResults = $this->searchHandler->search($term, $context, $limit);
        
        // Search blog categories  
        $categoryResults = $this->searchHandler->searchCategories($term, $context, $limit);
        
        // Encode results
        $data = [
            'blog_entries' => $this->encodeResults(
                $entryResults->getEntities(),
                $this->searchHandler->getEntryDefinition(),
                '/api'
            ),
            'blog_categories' => $this->encodeResults(
                $categoryResults->getEntities(),
                $this->searchHandler->getCategoryDefinition(),
                '/api'
            ),
        ];
        
        return new JsonResponse([
            'data' => $data,
            'total' => [
                'blog_entries' => $entryResults->getTotal(),
                'blog_categories' => $categoryResults->getTotal(),
            ],
        ]);
    }

    /**
     * Get a single blog entry by ID
     */
    #[Route(path: '/api/blog-entry/{id}', name: 'api.blog_entry.detail', methods: ['GET'], defaults: ['_routeScope' => ['administration']])]
    public function getBlogEntry(string $id, Context $context): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return new JsonResponse(['error' => 'Invalid ID'], 400);
        }
        
        $criteria = new Criteria([$id]);
        
        // Load translations
        $criteria->addAssociations([
            'translations',
            'blogAuthor',
            'blogCategories',
            'tags',
        ]);
        
        $result = $this->searchHandler->getEntryDefinition()
            ->getRepository()
            ->search($criteria, $context);
        
        if ($result->getTotal() === 0) {
            return new JsonResponse(['error' => 'Blog entry not found'], 404);
        }
        
        $entity = $result->getEntities()->first();
        
        return new JsonResponse([
            'data' => $this->encodeResult($entity, $this->searchHandler->getEntryDefinition(), '/api'),
        ]);
    }

    /**
     * Encode a collection of entities
     *
     * @param EntityCollection $entities
     */
    private function encodeResults(EntityCollection $entities, object $definition, string $prefix): array
    {
        $criteria = new Criteria();
        $encoded = [];
        
        foreach ($entities->getElements() as $key => $entity) {
            $encoded[$key] = $this->encodeResult($entity, $definition, $prefix);
        }
        
        return $encoded;
    }

    /**
     * Encode a single entity
     */
    private function encodeResult(object $entity, object $definition, string $prefix): array
    {
        return $this->entityEncoder->encode(
            new Criteria(),
            $definition,
            $entity,
            $prefix
        );
    }
}