<?php

declare(strict_types=1);

namespace Werkl\OpenBlogware\Framework\Search\Admin;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Term\MultiFieldScoreFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Term\SearchTermInterpreter;
use Werkl\OpenBlogware\Content\Blog\BlogEntryDefinition;
use Werkl\OpenBlogware\Content\BlogCategory\BlogCategoryDefinition;

/**
 * Search handler for blog entries in the Shopware Admin global search.
 * Uses DAL-based searching without requiring Elasticsearch.
 *
 * This handler enables blog entries and categories to be searchable
 * through a custom API endpoint that can be integrated with the admin search.
 */
class BlogEntryAdminSearchHandler
{
    public function __construct(
        private readonly BlogEntryDefinition $blogEntryDefinition,
        private readonly BlogCategoryDefinition $blogCategoryDefinition,
        private readonly SearchTermInterpreter $interpreter
    ) {}

    /**
     * Search for blog entries matching the given term
     */
    public function search(string $term, Context $context, int $limit = 10): EntitySearchResult
    {
        $criteria = $this->buildCriteria($term, $limit);
        
        return $this->blogEntryDefinition->getRepository()
            ->search($criteria, $context);
    }

    /**
     * Search for blog categories matching the given term
     */
    public function searchCategories(string $term, Context $context, int $limit = 10): EntitySearchResult
    {
        $criteria = $this->buildCategoryCriteria($term, $limit);
        
        return $this->blogCategoryDefinition->getRepository()
            ->search($criteria, $context);
    }

    /**
     * Build search criteria for blog entries
     */
    private function buildCriteria(string $term, int $limit): Criteria
    {
        $criteria = new Criteria();
        
        // Parse the search term using Shopware's interpreter
        $parsedTerms = $this->interpreter->interpret($term);
        
        if (!empty($parsedTerms)) {
            // Build score query from parsed terms using ScoreQueryBuilder
            $scoreFields = [
                'title' => 500,
                'slug' => 400,
                'teaser' => 300,
                'metaTitle' => 200,
                'metaDescription' => 150,
            ];
            
            // Use MultiFieldQuery for multi-field search
            $queries = [];
            foreach ($parsedTerms as $parsedTerm) {
                $term = $parsedTerm->getTerm();
                
                foreach ($scoreFields as $field => $boost) {
                    $queries[] = new \Shopware\Core\Framework\DataAbstractionLayer\Search\Term\MultiFieldScoreFilter(
                        $term,
                        [$field => (float) $boost],
                        $parsedTerm->isAnd()
                    );
                }
            }
            
            if (!empty($queries)) {
                $criteria->addFilter(new OrFilter($queries));
            }
        } else {
            // Fallback: simple contains filter for each searchable field
            $filters = [
                new \Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter('title', $term),
                new \Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter('slug', $term),
                new \Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter('teaser', $term),
            ];
            
            $criteria->addFilter(new OrFilter($filters));
        }
        
        // Only show active entries
        $criteria->addFilter(
            new \Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter('active', true)
        );
        
        // Only show published entries
        $criteria->addFilter(
            new \Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter('publishedAt', [
                'lte' => (new \DateTime())->format('Y-m-d H:i:s'),
            ])
        );
        
        $criteria->setLimit($limit);
        
        return $criteria;
    }

    /**
     * Build search criteria for blog categories
     */
    private function buildCategoryCriteria(string $term, int $limit): Criteria
    {
        $criteria = new Criteria();
        
        $parsedTerms = $this->interpreter->interpret($term);
        
        if (!empty($parsedTerms)) {
            $scoreFields = [
                'name' => 500,
                'description' => 300,
            ];
            
            $queries = [];
            foreach ($parsedTerms as $parsedTerm) {
                $termValue = $parsedTerm->getTerm();
                
                foreach ($scoreFields as $field => $boost) {
                    $queries[] = new \Shopware\Core\Framework\DataAbstractionLayer\Search\Term\MultiFieldScoreFilter(
                        $termValue,
                        [$field => (float) $boost],
                        $parsedTerm->isAnd()
                    );
                }
            }
            
            if (!empty($queries)) {
                $criteria->addFilter(new OrFilter($queries));
            }
        } else {
            $filters = [
                new \Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter('name', $term),
                new \Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter('description', $term),
            ];
            
            $criteria->addFilter(new OrFilter($filters));
        }
        
        $criteria->setLimit($limit);
        
        return $criteria;
    }

    /**
     * Get the entity definition for blog entries
     */
    public function getEntryDefinition(): EntityDefinition
    {
        return $this->blogEntryDefinition;
    }

    /**
     * Get the entity definition for blog categories
     */
    public function getCategoryDefinition(): EntityDefinition
    {
        return $this->blogCategoryDefinition;
    }
}