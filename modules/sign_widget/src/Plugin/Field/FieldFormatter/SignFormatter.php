<?php

namespace Drupal\sign_widget\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Template\Attribute;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatter;

/**
 * Plugin implementation of the 'field_signature_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "sign_widget_formatter",
 *   module = "sign_widget",
 *   label = @Translation("Sign"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class SignFormatter extends ImageFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'dotSize' => 1,
      'minWidth' => 0.5,
      'maxWidth' => 2.5,
      'backgroundColor' => '',
      'penColor' => '#000000',
      'velocityFilterWeight' => 0.7,
      'signTool' => FALSE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['dotSize'] = [
      '#type' => 'number',
      '#title' => $this->t('Dot Size'),
      '#default_value' => $this->getSetting('dotSize'),
      '#required' => TRUE,
      '#description' => $this->t('Radius of a single dot.'),
    ];

    $elements['minWidth'] = [
      '#type' => 'number',
      '#step' => '.1',
      '#title' => $this->t('Minimum Width'),
      '#default_value' => $this->getSetting('minWidth'),
      '#required' => TRUE,
      '#description' => $this->t('Minimum width of a line'),
    ];

    $elements['maxWidth'] = [
      '#type' => 'number',
      '#step' => '.1',
      '#title' => $this->t('Maximum Width'),
      '#default_value' => $this->getSetting('maxWidth'),
      '#required' => TRUE,
      '#description' => $this->t('Maximum width of a line.'),
    ];

    $elements['backgroundColor'] = [
      '#type' => 'color',
      '#title' => $this->t('Background Color'),
      '#default_value' => $this->getSetting('backgroundColor'),
      '#description' => $this->t('Color used to clear the background.'),
    ];

    $elements['penColor'] = [
      '#type' => 'color',
      '#title' => $this->t('Pen Color'),
      '#default_value' => $this->getSetting('penColor'),
      '#required' => TRUE,
      '#description' => $this->t('Color used to draw the lines.'),
    ];

    $elements['velocityFilterWeight'] = [
      '#type' => 'number',
      '#step' => '.1',
      '#title' => $this->t('Velocity Filter Weight'),
      '#default_value' => $this->getSetting('velocityFilterWeight'),
      '#required' => TRUE,
      '#description' => $this->t('Weight used to modify new velocity based on the previous velocity.'),
    ];
    $elements['signTool'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Toolbox'),
      '#default_value' => $this->getSetting('signTool'),
      '#description' => $this->t('Show toolbox to modify dot size and color pen.'),
    ];
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    if (!empty($this->getSetting('dotSize')) && !empty($this->getSetting('minWidth')) && !empty($this->getSetting('maxWidth'))) {
      $summary[] = $this->t('Size :  (%maxWidth x %minWidth)%size', [
        '%size' => $this->getSetting('dotSize'),
        '%minWidth' => $this->getSetting('minWidth'),
        '%maxWidth' => $this->getSetting('maxWidth'),
      ]);
    }
    if (!empty($this->getSetting('backgroundColor'))) {
      $summary[] = $this->t('Background Color : %backgroundColor', ['%backgroundColor' => $this->getSetting('backgroundColor')]);
    }
    if (!empty($this->getSetting('penColor'))) {
      $summary[] = $this->t('Pen Color : %penColor', ['%penColor' => $this->getSetting('penColor')]);
    }
    if (!empty($this->getSetting('velocityFilterWeight'))) {
      $summary[] = $this->t('Velocity Filter Weight : %velocityFilterWeight', ['%velocityFilterWeight' => $this->getSetting('velocityFilterWeight')]);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);
    $total = count($elements);
    $cardinality = $this->fieldDefinition->get('fieldStorage')->get('cardinality');
    if ($cardinality == -1) {
      $cardinality = $total + 1;
    }
    if ($cardinality > $total) {
      $elements['#attached']['library'][] = 'sign_widget/sign_widget';

      $height = 180;
      $width = 288;
      $max_resolution = $this->fieldDefinition->getSetting("max_resolution");
      if (!empty($max_resolution)) {
        [$width, $height] = explode('x', $max_resolution);
      }
      $min_resolution = $this->fieldDefinition->getSetting("min_resolution");
      if (!empty($min_resolution)) {
        [$width, $height] = explode('x', $min_resolution);
      }
      $entity = $items->getEntity();
      $settings = $this->fieldDefinition->getSetting("default_image");
      $defData['data-file_directory'] = $this->fieldDefinition->getSetting("file_directory");
      $defData['data-entity_type'] = $this->fieldDefinition->getTargetEntityTypeId();
      $defData['data-bundle'] = $this->fieldDefinition->getTargetBundle();
      $defData['data-field_type'] = $this->fieldDefinition->getType();
      $defData['data-field_name'] = $this->fieldDefinition->getName();
      $defData['data-entity_id'] = $entity->id();
      if (!empty($settings['width'])) {
        $width = $settings['width'];
      }
      if (!empty($settings['height'])) {
        $height = $settings['height'];
      }
      $data = [];
      foreach ($this->settings as $attribute => $settingValue) {
        if (in_array($attribute, ['image_style', 'image_link', 'signtool'])) {
          continue;
        }
        $data['data-' . $attribute] = $settingValue;
      }
      $id = str_replace('.', '-', $this->fieldDefinition->id()) . '-' . $entity->id();
      foreach (range($total, $cardinality - 1) as $delta) {
        $id .= '-' . $delta;
        $attributes = new Attribute([
          'id' => $id,
          'width' => $width,
          'height' => $height,
          'delta' => $delta,
          'class' => ["signature-pad", "border"],
        ] + $data);
        $elements[$delta] = [
          '#type' => 'container',
          '#attributes' => ['class' => ['esign_container', 'col-sm']],
          '#weight' => $delta,
          'canvas' => [
            '#theme' => 'sign',
            '#attributes' => $attributes,
            '#canvas_id' => $id,
            '#height' => $height,
            '#width' => $width,
            '#settings' => $this->settings + ['is_formatter' => TRUE],
          ],
        ];
        $defData['data-delta'] = $delta;
        $elements[$delta]['sign'] = [
          '#type' => 'hidden',
          '#attributes' => [
            'id' => $id . '-sign',
            'class' => ['signature-storage'],
            'data-delta' => $delta,
          ] + $defData,
          '#attached' => [
            'drupalSettings' => [
              'sign_pad' => [$id => $this->settings],
            ],
          ],
        ];

      }
    }

    return $elements;
  }

}
