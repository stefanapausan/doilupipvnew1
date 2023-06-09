<?php

namespace Drupal\Tests\printable\Unit\Plugin\PrintableFormat;

use Drupal\Tests\UnitTestCase;
use Drupal\printable\Plugin\PrintableFormat\PrintFormat;

/**
 * Tests the print format plugin.
 *
 * @group Printable
 */
class PrintFormatTest extends UnitTestCase {

  /**
   * The plugin definition of this plugin.
   *
   * @var array
   */
  protected $pluginDefinition;

  /**
   * The ID of the plugin.
   *
   * @var string
   */
  protected $pluginId;

  /**
   * The configuration to be passed into the format plugin constructor.
   *
   * @var array
   */
  protected $configuration;

  /**
   * {@inheritdoc}
   */
  public function __construct() {
    $this->pluginDefinition = [
      'description' => 'Print description.',
      'id' => 'print',
      'module' => 'printable',
      'title' => 'Print',
      'class' => 'Drupal\printable\Plugin\PrintableFormat\PrintFormat',
      'provider' => 'printable',
    ];
    $this->pluginId = 'print';
    $this->configuration = [];
  }

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => 'Printable Format Base',
      'descriptions' => 'Tests the printable format base class.',
      'group' => 'Printable',
    ];
  }

  /**
   * Tests getting the plugin label from the plugin.
   *
   * @covers PrintFormat::GetLabel
   */
  public function testGetLabel() {
    $format = new PrintFormat($this->configuration, $this->pluginId, $this->pluginDefinition, $this->getConfigFactoryStub(), $this->getCssIncludeStub(), $this->getLinkExtractorIncludeStub());
    $this->assertEquals('Print', $format->getLabel());
  }

  /**
   * Tests getting the plugin description from the plugin.
   *
   * @covers PrintFormat::GetDescription
   */
  public function testGetDescription() {
    $format = new PrintFormat($this->configuration, $this->pluginId, $this->pluginDefinition, $this->getConfigFactoryStub(), $this->getCssIncludeStub(), $this->getLinkExtractorIncludeStub());
    $this->assertEquals('Print description.', $format->getDescription());
  }

  /**
   * Tests getting the default configuration for this plugin.
   *
   * @covers PrintFormat::DefaultConfiguration
   */
  public function testDefaultConfiguration() {
    $format = new PrintFormat($this->configuration, $this->pluginId, $this->pluginDefinition, $this->getConfigFactoryStub(), $this->getCssIncludeStub(), $this->getLinkExtractorIncludeStub());
    $this->assertEquals(['show_print_dialogue' => TRUE], $format->defaultConfiguration());
  }

  /**
   * Tests getting the current configuration for this plugin.
   *
   * @covers PrintFormat::GetConfiguration
   */
  public function testGetConfiguration() {
    $format = new PrintFormat($this->configuration, $this->pluginId, $this->pluginDefinition, $this->getConfigFactoryStub(), $this->getCssIncludeStub(), $this->getLinkExtractorIncludeStub(), $this->getLinkExtractorIncludeStub());
    $this->assertEquals(['show_print_dialogue' => TRUE], $format->getConfiguration());
  }

  /**
   * Tests that additional configuration is internally stored and accessible.
   *
   * @covers PrintFormat::GetPassedInConfiguration
   */
  public function testGetPassedInConfiguration() {
    $format = new PrintFormat(['test_configuration_value' => TRUE], $this->pluginId, $this->pluginDefinition, $this->getConfigFactoryStub(), $this->getCssIncludeStub(), $this->getLinkExtractorIncludeStub());
    $this->assertEquals(
      ['show_print_dialogue' => TRUE, 'test_configuration_value' => TRUE], $format->getConfiguration()
    );
  }

  /**
   * Test that default configuration can be modified and changes accessed.
   *
   * @covers PrintFormat::SetConfiguration
   */
  public function testSetConfiguration() {
    $new_configuration = ['show_print_dialogue' => FALSE];

    $config_mock = $this->createMock('\Drupal\Core\Config\Config');
    $config_mock->expects($this->once())
      ->method('set')
      ->with('print', $new_configuration)
      ->will($this->returnSelf());
    $config_mock->expects($this->once())
      ->method('save');

    $config_factory_mock = $this->createMock('\Drupal\Core\Config\ConfigFactory');
    $config_factory_mock->expects($this->once())
      ->method('get')
      ->with('printable.format')
      ->will($this->returnValue($config_mock));

    $format = new PrintFormat($this->configuration, $this->pluginId, $this->pluginDefinition, $config_factory_mock, $this->getCssIncludeStub(), $this->getLinkExtractorIncludeStub());
    $format->setConfiguration($new_configuration);

    $this->assertEquals($new_configuration, $format->getConfiguration());
  }

  /**
   * Get the CSS include stub.
   */
  protected function getCssIncludeStub() {
    return $this->createMock('Drupal\printable\PrintableCssIncludeInterface');
  }

  /**
   * Get the Link extractor stub.
   */
  protected function getLinkExtractorIncludeStub() {
    return $this->createMock('Drupal\printable\LinkExtractor\LinkExtractorInterface');
  }

}
