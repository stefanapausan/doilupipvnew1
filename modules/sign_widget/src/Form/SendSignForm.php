<?php

namespace Drupal\sign_widget\Form;

use Drupal\Component\Render\PlainTextOutput;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\sign_widget\Ajax\SendSignCommand;
use Symfony\Component\HttpFoundation\Request;

/**
 * In view mode send signature.
 */
class SendSignForm extends ConfigFormBase {

  /**
   * Save Signature.
   */
  public static function saveSign(string $selector, Request $request) : AjaxResponse {

    $encoded_image = $request->request->get('sign');
    $url = '';
    if (!empty($encoded_image)) {
      // Create image directory.
      $file_directory = trim($request->request->get("file_directory"), '/');
      $destination = PlainTextOutput::renderFromHtml(\Drupal::token()->replace($file_directory, []));
      $uri = \Drupal::config('system.file')->get('default_scheme') . '://';
      if (!empty($destination)) {
        $uri .= $destination . '/';
        $fileService = \Drupal::service('file_system');
        $path = $fileService->realpath($uri);
        $fileService->prepareDirectory($path, FileSystemInterface::CREATE_DIRECTORY);
      }
      // Convert image base64 to file.
      $encoded_image = explode(",", $encoded_image)[1];
      $encoded_image = str_replace(' ', '+', $encoded_image);
      $decoded_image = base64_decode($encoded_image);
      $filename = date('ymd') . '_' . rand(1000, 9999) . '.png';
      // Saves a file to the specified destination and creates a database entry.
      $file = \Drupal::service('file.repository')->writeData($decoded_image, $uri . $filename, FileSystemInterface::EXISTS_REPLACE);
      $uri = $file->getFileUri();
      $url = \Drupal::service('file_url_generator')->generate($uri)->toString();
      [$width, $height] = getimagesize($uri);

      $entity_type = $request->request->get("entity_type");
      $entityLoad = \Drupal::entityTypeManager()->getStorage($entity_type);

      $id = $request->request->get("entity_id");
      $entity = $entityLoad->load($id);

      $field_name = $request->request->get("field_name");
      /*
       * Reserved for future feature.
      $delta = $request->request->get("delta");
      $bundle = $request->request->get("bundle");
       */
      $value = [
        'target_id' => $file->id(),
        'width' => $width,
        'height' => $height,
      ];
      $entity->$field_name->appendItem($value);
      $entity->save();
    }
    if (!empty($url)) {
      $response = new AjaxResponse();
      $response->addCommand(new SendSignCommand($selector, $url));
    }

    return $response;
  }

  /**
   * Form id.
   */
  public function getFormId() {
    return 'sign_widget_sendSign_form';
  }

  /**
   * Get editable configuration name.
   */
  protected function getEditableConfigNames() {
    return [$this->getFormId() . '.setting'];
  }

}
