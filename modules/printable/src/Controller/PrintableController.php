<?php

namespace Drupal\printable\Controller;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\printable\PrintableFormatPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller to display an entity in a particular printable format.
 */
class PrintableController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The printable format plugin manager.
   *
   * @var \Drupal\printable\PrintableFormatPluginManager
   */
  protected $printableFormatManager;

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * The format plugin instance.
   *
   * @var \Drupal\printable\Plugin\PrintableFormatInterface
   */
  protected $formatPlugin;

  /**
   * Constructs a \Drupal\printable\Controller\PrintableController object.
   *
   * @param \Drupal\printable\PrintableFormatPluginManager $printable_format_manager
   *   The printable format plugin manager.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The config factory class instance.
   */
  public function __construct(PrintableFormatPluginManager $printable_format_manager, ConfigFactory $config_factory) {
    $this->printableFormatManager = $printable_format_manager;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('printable.format_plugin_manager'),
      $container->get('config.factory')
    );
  }

  /**
   * Get the entity title.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to be printed.
   *
   * @return string
   *   The entity label.
   */
  public function getTitle(EntityInterface $entity) {
    return $entity->label();
  }

  /**
   * Returns a string representing the generated output.
   *
   * Either a filename or the content itself, depending on the format.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to be printed.
   * @param string $printable_format
   *   The identifier of the hardcopy format plugin.
   * @param array $configuration
   *   Custom configuration for the plugin.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The printable response.
   */
  private function prepareFormat(
    EntityInterface $entity,
    $printable_format,
    array $configuration = []) {
    if (!$this->printableFormatManager->getDefinition($printable_format, FALSE)) {
      throw new NotFoundHttpException();
    }

    if (!$this->formatPlugin || $this->formatPlugin->getPluginId() != $printable_format) {
      $this->formatPlugin = $this->printableFormatManager->createInstance($printable_format, $configuration);
    }
    else {
      $this->formatPlugin->setConfigurationNoSave($configuration);
    }

    $content = $this->entityTypeManager()
      ->getViewBuilder($entity->getEntityTypeId())
      ->view($entity, 'printable');
    $this->formatPlugin->setContent($content);
    $this->formatPlugin->setEntity($entity);
    return $this->formatPlugin;
  }

  /**
   * Returns the entity rendered via the given printable format.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to be printed.
   * @param string $printable_format
   *   The identifier of the hadcopy format plugin.
   * @param array $configuration
   *   Custom configuration for the printable plugin.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The printable response.
   */
  public function getOutput(
    EntityInterface $entity,
    $printable_format,
    array $configuration = []) {
    $format = $this->prepareFormat($entity, $printable_format, $configuration);
    return $format->getOutput();
  }

  /**
   * Returns the entity rendered via the given printable format.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to be printed.
   * @param string $printable_format
   *   The identifier of the hadcopy format plugin.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The printable response.
   */
  public function showFormat(EntityInterface $entity, $printable_format) {
    $format = $this->prepareFormat($entity, $printable_format);
    try {
      return $format->getResponse();
    }
    catch (\Exception $e) {
      return new RedirectResponse('/', 307);
    }
  }

}
