<?php

namespace Drupal\Tests\elasticsearch_connector\Unit\SearchAPI\Query;

use Drupal\Tests\UnitTestCase;
use Drupal\elasticsearch_connector\SearchAPI\Query\FacetParamBuilder;
use Drupal\search_api\Query\QueryInterface;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;

/**
 * Tests the facet param builder.
 *
 * @coversDefaultClass \Drupal\elasticsearch_connector\SearchAPI\Query\FacetParamBuilder
 * @group elasticsearch_connector
 */
class FacetParamBuilderTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * @covers ::buildFacetParams
   */
  public function testBuildFacetParams() {
    $logger = $this->prophesize(LoggerInterface::class);
    $builder = new FacetParamBuilder($logger->reveal());

    $query = $this->prophesize(QueryInterface::class);
    $query->getOption('search_api_facets', [])
      ->willReturn([
        'facet1' => [
          'field' => 'field1',
          'operator' => 'and',
        ],
        'facet2' => [
          'field' => 'field1',
          'operator' => 'or',
        ],
      ]);

    $indexFields = [
      'field1' => [],
      'field2' => [],
    ];

    $facetFilters = [
      "facet2" => "filter for facet2",
    ];

    $aggs = $builder->buildFacetParams($query->reveal(), $indexFields, $facetFilters);

    $expected = [
      'facet1_filtered' => [
        'filter' => "filter for facet2",
        'aggs' => [
          'facet1' =>
          ['terms' => ['field' => 'field1', 'size' => '10']],
        ],
      ],
      'facet2' => ['terms' => ['field' => 'field1', 'size' => '10']],
    ];

    $this->assertNotEmpty($aggs);
    $this->assertEquals($expected, $aggs);
  }

  /**
   * @covers ::buildFacetParams
   */
  public function testBuildFacetParamsAnd() {
    $logger = $this->prophesize(LoggerInterface::class);
    $builder = new FacetParamBuilder($logger->reveal());

    $query = $this->prophesize(QueryInterface::class);
    $query->getOption('search_api_facets', [])
      ->willReturn([
        'facet1' => [
          'field' => 'field1',
          'operator' => 'and',
        ],
        'facet2' => [
          'field' => 'field1',
          'operator' => 'and',
        ],
      ]);

    $indexFields = [
      'field1' => [],
      'field2' => [],
    ];

    $aggs = $builder->buildFacetParams($query->reveal(), $indexFields);

    $expected = [
      'facet1' => ['terms' => ['field' => 'field1', 'size' => '10']],
      'facet2' => ['terms' => ['field' => 'field1', 'size' => '10']],
    ];

    $this->assertNotEmpty($aggs);
    $this->assertEquals($expected, $aggs);
  }

}
