<?php

namespace Drupal\sign_widget\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * AJAX command to send an image base64 field formatter.
 */
class SendSignCommand implements CommandInterface {

  /**
   * The ID for the sign element.
   *
   * @var string
   */
  protected $selector;

  /**
   * The url for the sign element.
   *
   * @var string
   */
  protected $signSrc;

  /**
   * Constructor.
   *
   * @param string $selector
   *   The ID for the sign element.
   * @param string $sign_src
   *   The url sign element.
   */
  public function __construct($selector, $sign_src) {
    $this->selector = $selector;
    $this->signSrc = $sign_src;
  }

  /**
   * Implements Drupal\Core\Ajax\CommandInterface:render().
   */
  public function render() {
    return [
      'command' => 'SendSign',
      'selector' => $this->selector,
      'sign_src' => $this->signSrc,
    ];
  }

}
