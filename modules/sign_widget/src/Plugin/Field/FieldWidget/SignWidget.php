<?php

namespace Drupal\sign_widget\Plugin\Field\FieldWidget;

use Drupal\Component\Render\PlainTextOutput;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Template\Attribute;
use Drupal\file\Entity\File;
use Drupal\image\Plugin\Field\FieldWidget\ImageWidget;

/**
 * Plugin implementation of the 'image_image' widget.
 *
 * @FieldWidget(
 *   id = "sign_widget",
 *   label = @Translation("Sign"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class SignWidget extends ImageWidget {

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
      'show_remove_btn' => FALSE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

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
      '#title' => $this->t('Show toolbox'),
      '#default_value' => $this->getSetting('signTool'),
      '#description' => $this->t('Show toolbox to modify dot size and color pen.'),
    ];
    $elements['show_remove_btn'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show remove button'),
      '#default_value' => $this->getSetting('show_remove_btn'),
      '#description' => $this->t('Show remove button in edit.'),
    ];
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
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
    if (!empty($this->getSetting('signTool'))) {
      $summary[] = $this->t('Show toolbox');
    }
    if (!empty($this->getSetting('show_remove_btn'))) {
      $summary[] = $this->t('Show remove button');
    }

    return $summary;
  }

  /**
   * Overrides FileWidget::formMultipleElements().
   *
   * Special handling for draggable multiple widgets and 'add more' button.
   */
  protected function formMultipleElements(FieldItemListInterface $items, array &$form, FormStateInterface $form_state) {
    $cardinality = $this->fieldDefinition->getFieldStorageDefinition()->getCardinality();
    switch ($cardinality) {
      case FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED:
        $max = count($items);
        break;

      default:
        $max = $cardinality - 1;
        break;
    }

    $elements = [
      '#type' => 'container',
      '#attributes' => ['class' => ['row']],
    ];
    $element = [];

    // Add an element for every existing item.
    foreach (range(0, $max) as $delta) {
      $elements[$delta] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['esign_container', 'col-sm']],
        '#weight' => $delta,
      ] + $this->formSingleElement($items, $delta, $element, $form, $form_state);
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   *
   * @todo remove button doesn't work it must rewrite #submit class.
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $field_settings = $this->getFieldSettings();

    $values = $items->getValue();
    $max_resolution = $field_settings["max_resolution"];
    $min_resolution = $field_settings["min_resolution"];
    $width = 288;
    $height = 180;
    if (!empty($max_resolution)) {
      [$width, $height] = explode('x', $max_resolution);
    }
    if (!empty($min_resolution)) {
      [$width, $height] = explode('x', $min_resolution);
    }
    $settings = $field_settings["default_image"];
    if (!empty($settings['width'])) {
      $width = $settings['width'];
    }
    if (!empty($settings['height'])) {
      $height = $settings['height'];
    }
    if (empty($element['#upload_validators'])) {
      $element['#upload_validators'] = FALSE;
    }
    $field_name = $this->fieldDefinition->getName();
    $id = Html::getUniqueId($field_name);
    $fid = $img = '';
    if (!empty($values[$delta])) {
      $fid = $values[$delta]['target_id'];
      $file = File::load($fid);
    }
    if (empty($file) && !empty($settings["uuid"])) {
      $file = \Drupal::service('entity.repository')
        ->loadEntityByUuid('file', $settings["uuid"]);
    }
    $imgSrc = !empty($file) ? $file->createFileUrl() : '';
    if ($this->settings['backgroundColor'] == '#ffffff' || !empty($img)) {
      $this->settings['backgroundColor'] = 'rgba(255, 255, 255, 0)';
    }
    $attributes = new Attribute([
      'id' => $id,
      'width' => $width,
      'height' => $height,
      'class' => ["signature-pad", "border"],
    ]);
    foreach ($this->settings as $attribute => $settingValue) {
      $attributes['data-' . $attribute] = $settingValue;
    }
    $sign_pad = [
      '#theme' => 'sign',
      '#attributes' => $attributes,
      '#label' => $this->fieldDefinition->getLabel(),
      '#canvas_id' => $id,
      '#sign_src' => $imgSrc,
      '#height' => $height,
      '#width' => $width,
      '#settings' => $this->settings,
    ];

    $element['#attached']['drupalSettings']['sign_pad'][$id] = $this->settings;

    $element += [
      '#type' => 'hidden',
      '#required' => FALSE,
      '#attributes' => [
        'id' => $id . '-sign',
        'class' => ['signature-storage'],
      ],
      '#title_field' => $field_settings['title_field'],
      '#title_field_required' => $field_settings['title_field_required'],
    ];

    $element['#attached']['library'][] = 'sign_widget/sign_widget';

    $file_extensions = $this->fieldDefinition->getSetting("file_extensions");
    $elements = [
      '#title' => $this->fieldDefinition->getLabel(),
      '#title_display' => 'before',
      'value' => $element,
      'canvas' => $sign_pad,
      '#field_parents' => $element['#field_parents'],
      '#upload_validators' => [
        'file_validate_extensions' => [$file_extensions],
      ],
      '#required' => FALSE,
    ];

    // Add the additional alt and title fields.
    if ($field_settings['alt_field']) {
      $elements['alt'] = [
        '#title' => $this->t('Alternative text'),
        '#type' => 'textfield',
        '#default_value' => !empty($values[$delta]['alt']) ? $values[$delta]['alt'] : '',
        '#description' => $this->t('Short description of the image used by screen readers and displayed when the image is not loaded. This is important for accessibility.'),
        '#maxlength' => 512,
        '#required' => $field_settings['alt_field_required'],
      ];
    }
    if ($field_settings['title_field']) {
      $elements['title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#default_value' => !empty($values[$delta]['title']) ? $values[$delta]['title'] : '',
        '#description' => $this->t('The title is used as a tool tip when the user hovers the mouse over the image.'),
        '#maxlength' => 1024,
        '#required' => $field_settings['title_field_required'],
      ];
    }

    // In case modify.
    if (!empty($fid)) {
      $file = File::load($fid);
      $elements['value']['#default_value'] = $fid;
      $array_parents = array_merge(
        $element["#field_parents"],
        [$field_name, $delta]
      );
      $parents_prefix = implode('_', $array_parents);
      // Generate a unique wrapper HTML ID.
      $ajax_settings = [
        'callback' => ['Drupal\file\Element\ManagedFile', 'uploadAjaxCallback'],
        'options' => [
          'query' => [
            'element_parents' => implode('/', array_merge(
              $array_parents, ['widget', $delta]
            )),
          ],
        ],
        'wrapper' => Html::getId('edit-' .
          implode('-', $array_parents)
        ),
        'effect' => 'none',
        'progress' => [
          'type' => 'throbber',
          'message' => NULL,
        ],
      ];
      $remove_button = $this->fieldDefinition->getSetting("show_remove_btn");

      $user = \Drupal::currentUser();
      if ($user->hasPermission('administer site configuration')) {
        $remove_button = TRUE;
      }
      // Force the progress indicator for the remove btn to be either 'none' or
      // 'throbbed', even if the upload button is using something else.
      $elements['remove_button'] = [
        '#name' => $parents_prefix . '_remove_button',
        '#type' => 'submit',
        '#value' => $this->t('Remove'),
        '#access' => $remove_button,
        '#submit' => [
          'file_managed_file_submit',
          ['Drupal\file\Plugin\Field\FieldWidget\FileWidget' , 'submit'],
        ],
        '#ajax' => $ajax_settings,
      ];
      if ($file) {
        $elements['#files'] = [$fid => $file->setTemporary()];
        $elements['#multiple'] = FALSE;
        $elements['#prefix'] = '';
        $elements['#suffix'] = '';
      }
      $elements['fids'] = [
        '#type' => 'hidden',
        '#value' => [$fid],
      ];
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $file_directory = trim($this->fieldDefinition->getSetting("file_directory"), '/');
    $destination = PlainTextOutput::renderFromHtml(\Drupal::token()
      ->replace($file_directory, []));
    $uri = \Drupal::config('system.file')->get('default_scheme') . '://';
    if (!empty($destination)) {
      $uri .= $destination . '/';
      $fileSystem = \Drupal::service('file_system');
      $path = $fileSystem->realpath($uri);
      $fileSystem->prepareDirectory($path, FileSystemInterface::CREATE_DIRECTORY);
    }
    if (empty($form["#validated"])) {
      return $values;
    }
    foreach ($values as &$value) {
      if (!empty($value['value']) && empty($values['fids'])) {
        if (is_numeric($value['value'])) {
          $file = File::load($value['value']);
        }
        else {
          $encoded_image = explode(",", $value['value'])[1];
          $encoded_image = str_replace(' ', '+', $encoded_image);
          $decoded_image = base64_decode($encoded_image);
          $filename = date('ymd') . '_' . rand(1000, 9999) . '.png';
          // Saves a file to the specified folder & creates a database entry.
          $file = \Drupal::service('file.repository')->writeData($decoded_image, $uri . $filename, FileSystemInterface::EXISTS_REPLACE);
        }
        if (!empty($file)) {
          [$value['width'], $value['height']] = getimagesize($file->getFileUri());
          $value['target_id'] = $file->id();
        }
        if (!empty($value['fids'])) {
          unset($value['fids']);
        }
        if (!empty($value['remove_button'])) {
          unset($value['remove_button']);
        }
        unset($value['value']);
      }
    }
    return $values;
  }

}
