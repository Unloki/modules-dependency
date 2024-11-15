<?php

declare(strict_types=1);

namespace Drupal\Tests\elasticsearch_connector\Functional;

use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests for situations when backend is down.
 *
 * @group elasticsearch_connector
 */
class ElasticsearchConnectorBackendTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'dblog',
    'elasticsearch_connector',
    'elasticsearch_connector_test',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create an admin user.
    $admin_user = $this->drupalCreateUser([
      'access administration pages',
      'access site reports',
      'administer search_api',
    ]);
    $this->drupalLogin($admin_user);
  }

  /**
   * Tests that no exception is thrown when visiting the Search API routes.
   */
  public function testSearchApiRoutes() {
    $assert_session = $this->assertSession();

    // Alter the Elasticsearch server configuration to cause failure to connect
    // to Elasticsearch server.
    $config = $this->config('search_api.server.elasticsearch_server');
    $config->set('backend_config.connector_config.url', 'http://elasticsearch:9999');
    $config->save();

    // Assert "search_api.overview" route loads without errors.
    $url = Url::fromRoute('search_api.overview');
    $this->drupalGet($url);
    $this->assertSession()->statusCodeEquals(200);
    $assert_session->elementTextContains('css', '.search-api-server-elasticsearch-server .search-api-status', 'Unavailable');

    // Assert "entity.search_api_server.canonical" route loads without errors.
    $url = Url::fromRoute('entity.search_api_server.canonical', [
      'search_api_server' => 'elasticsearch_server',
    ]);
    $this->drupalGet($url);
    $this->assertSession()->statusCodeEquals(200);
    $assert_session->pageTextContains('Local test server');

    // Assert "entity.search_api_index.canonical" route loads without errors.
    $url = Url::fromRoute('entity.search_api_index.canonical', [
      'search_api_index' => 'test_elasticsearch_index',
    ]);
    $this->drupalGet($url);
    $assert_session->statusCodeEquals(200);
    $assert_session->pageTextContains('Test Index');
    $assert_session->elementTextContains('css', '.search-api-index-summary--server-index-status', 'Error while checking server index status');

    // Assert error produced on "search_api.overview" route is logged.
    $this->drupalGet('/admin/reports/dblog');
    $assert_session->pageTextContains('Elastic\Transport\Exception\NoNodeAvailableException');
  }

}
