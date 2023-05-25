<?php

/**
 * @file
 * Contains \Drupal\pdf_api\Plugin\DompdfGenerator.
 */

namespace Drupal\puphpeteer\Plugin\PdfGenerator;

use Drupal\Core\Access\CsrfTokenGenerator;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Http\RequestStack;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\pdf_api\Annotation\PdfGenerator;
use Drupal\pdf_api\Plugin\PdfGeneratorBase;
use Drupal\pdf_api\Plugin\PdfGeneratorInterface;
use Nesk\Puphpeteer\Puppeteer;
use Nesk\Rialto\Data\JsFunction;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A PDF generator plugin for Puphpeteer.
 *
 * @PdfGenerator(
 *   id = "puphpeteer",
 *   module = "puphpeteer",
 *   title = @Translation("Puphpeteer"),
 *   description = @Translation("PDF generator using Puphpeteer."),
 *   required_class = "Nesk\Puphpeteer\Puppeteer",
 * )
 */
class PuphpeteerGenerator extends PdfGeneratorBase implements ContainerFactoryPluginInterface {

  /**
   * Instance of the DOMPDF class library.
   *
   * @var \nesk\puphpeteer\Puppeteer
   */
  protected $generator;

  /**
   * Logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Route Match
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * URL Generator service instance.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * CSRF token generator.
   *
   * @var \Drupal\Core\Access\CsrfTokenGenerator
   */
  protected $csrfTokenGenerator;

  /**
   * Current user.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $currentUser;

  /**
   * Route provider.
   *
   * @var \Drupal\Core\Routing\RouteProviderInterface
   */
  protected $routeProvider;

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * Settings for our generated CSS.
   *
   * @var array
   */
  protected $css = [];

  /**
   * Page orientation.
   *
   * @var string
   */
  protected $landscape = FALSE;

  /**
   * Page size.
   *
   * @var string
   */
  protected $page_size = 'A4';

  /**
   * Header.
   *
   * @var string
   */
  protected $header = '';

  /**
   * Footer.
   *
   * @var string
   */
  protected $footer = '';

  /**
   * HTML content.
   *
   * @var string
   */
  protected $html = '';

  /**
   * The Browser intsance.
   *
   * @var object
   */
  protected $browser = NULL;

  /**
   * Is the browser running as a service?
   *
   * @var boolean
   */
  protected $isService = FALSE;

  /**
   * Is the browser running headlessly?
   *
   * @var boolean
   */
  protected $isHeadless = FALSE;

  /**
   * The current web page being visited. I'm calling them tabs to try to avoid confusion.
   *
   * @var object
   */
  protected $tab;

  /**
   * Puppeteer is running?
   *
   * @var boolean
   */
  protected $puppeteerRunning = FALSE;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, ConfigFactory $configFactory, LoggerInterface $logger, CurrentRouteMatch $currentRouteMatch, RouteProviderInterface $routeProvider, UrlGeneratorInterface $urlGenerator, CsrfTokenGenerator $csrfTokenGenerator, AccountInterface $currentUser, RequestStack $requestStack) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->settings = $configFactory->get('puphpeteer.settings');
    $this->logger = $logger;
    $this->currentRouteMatch = $currentRouteMatch;
    $this->routeProvider = $routeProvider;
    $this->urlGenerator = $urlGenerator;
    $this->csrfTokenGenerator = $csrfTokenGenerator;
    $this->currentUser = $currentUser;
    $this->request = $requestStack->getCurrentRequest();

    $settings = $this->settings->get();

    $options = [
      'logger' => $settings['debug'] ? $logger : NULL,
      'log_browser_console' => $settings['log_to_browser_console'],
      'log_node_console' => $settings['log_to_node_console'],
      'executable_path' => $settings['executable_path'],
      'read_timeout' => $settings['read_timeout'],
      'idle_timeout' => $settings['idle_timeout'],
      'debug' => $settings['debug'],
      'leave_running' => $this->settings->get('leave_running'),
    ];

    if ($settings['debug']) {
      putenv('DEBUG="puppeteer:*"');
    }

    try {
      $this->generator = new Puppeteer($options);
    }
    catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('logger.factory')->get('puphpeteer'),
      $container->get('current_route_match'),
      $container->get('router.route_provider'),
      $container->get('url_generator'),
      $container->get('csrf_token'),
      $container->get('current_user'),
      $container->get('request_stack')
    );
  }

  /**
   * Destructor - stop puppeteer if running as a service.
   */
  public function __destruct()
  {
    if (!$this->isService) {
      $this->closeBrowser();
    }
  }

  /**
   * Update the generator configuration (API use).
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = $configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setter($pdf_content, $pdf_location, $save_pdf, $paper_orientation, $paper_size, $footer_content, $header_content, $path_to_binary = '') {
    $this->setPageOrientation($paper_orientation);
    $this->setHeader($header_content);
    $this->addPage($pdf_content);
  }

  /**
   * {@inheritdoc}
   */
  public function getObject() {
    return $this->generator;
  }

  /**
   * We don't use the pre-rendered HTML.
   */
  public function usePrintableDisplay() {
    return $this->settings->get('source') == 'printable';
  }

  /**
   * {@inheritdoc}
   */
  public function setHeader($text) {
    $this->header = $text;
  }

  /**
   * {@inheritdoc}
   */
  public function addPage($html) {
    $this->html = $html;
  }

  /**
   * {@inheritdoc}
   */
  public function setPageOrientation($orientation = PdfGeneratorInterface::PORTRAIT) {
    $this->landscape = ($orientation == PdfGeneratorInterface::LANDSCAPE);
  }

  /**
   * {@inheritdoc}
   */
  public function setPageSize($page_size) {
    if ($this->isValidPageSize($page_size)) {
      $this->page_size = $page_size;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setFooter($text) {
    $this->footer = $text;
  }

  /**
   * Create the Chrome / Puppeteer instance.
   *
   * @throws \Exception
   *   If fails to start the browser.
   */
  public function startBrowser() {
    // Start the browser, as configured.
    $this->isService = $this->settings->get('service');
    if ($this->isService) {
      $this->isHeadless = false;
      $launchParams = [
        'browserURL' => $this->settings->get('service_url'),
      ];
    }
    else {
      $this->isHeadless = $this->settings->get('headless');
      $launchParams = [
        'args' => [
          '--no-sandbox',
          '--disable-setuid-sandbox',
          '--start-maximized'
        ],
        'headless' => $this->isHeadless,
        'ignoreHTTPSErrors' => TRUE,
        'defaultViewport' => NULL,
      ];

      if ($this->settings->get('devTools')) {
        $launchParams['args'][] = '--auto-open-devtools-for-tabs';
      }

      if ($this->settings->get('debug')) {
        $launchParams['args'][] = '--dumpio';
      }
    }

    $launchParams['sloMo'] = $this->settings->get('slowMo');

    try {
      if (!$this->browser) {
        if ($this->isService) {
          $this->browser = $this->generator->connect($launchParams);
        } else {
          $this->browser = $this->generator->launch($launchParams);
        }
        $this->tab = NULL;
      }
    }
    catch (\Exception $exception) {
      $this->messenger()
        ->addError('We failed to generate the PDF, sorry. Please try again later.');
      $this->logger
        ->alert($this->t("Puphpeteer failed to start the browser (:message).", [
          ':message' => $exception->getMessage(),
        ]));
      throw($exception);
    }

    if (!$this->tab) {
      $tabs = [];
      if ($this->isService) {
        $this->tab = $this->browser->newPage();
      } else {
        while (empty($tabs)) {
          $tabs = $this->browser->pages();
        }
        $this->tab = $tabs[0];
      }
    }
  }

  /**
   * Close the browser.
   */
  public function closeBrowser() {
    if ($this->isService) {
      if ($this->tab) {
        $this->tab->close();
        $this->tab = NULL;
      }
    }
    else {
      if ($this->browser) {
        $this->browser->close();
        $this->browser = NULL;
      }
    }
  }

  /**
   * Visit a URL and configure Chrome for PDF generation.
   */
  public function setContent()
  {
    if (!$this->browser || !$this->tab) {
      $this->startBrowser();
    }

    // Give Chrome in Puppeteer the same access the current user has.
    $cookies = $this->request->cookies->all();

    // All an external user of the printable service to specify cookies to be provided to a URL.
    if (!empty($this->configuration['cookies'])) {
      $cookies = array_merge($cookies, $this->configuration['cookies']);
    }

    $arg = [];
    foreach ($cookies as $name => $value) {
      $arg[] = [
        'name' => $name,
        'value' => $value,
        'domain' => $this->request->getHost(),
      ];
    }

    if (!empty($arg)) {
      $this->tab->setCookie(... $arg);
    }

    // Is Basic Auth needed?
    if ($this->request->headers->get('authorization')) {
      $this->tab->setExtraHTTPHeaders(['authorization' => $this->request->headers->get('authorization')]);
    }

    if (!empty($this->settings->get('basic_auth_username'))) {
      $this->tab->authenticate([
        'username' => $this->settings->get('basic_auth_username'),
        'password' => $this->settings->get('basic_auth_password')
      ]);
    }

    $url = null;
    // Let an external user of the printable service to specify a URL they want us to visit.
    if (!empty($this->configuration['url'])) {
      $url = $this->configuration['url'];
    } else {
      switch ($this->settings->get('source')) {
        case 'printable':
          break;

        case 'canonical':
          $route_name = 'entity.' . $this->entity->getEntityTypeId() . '.canonical';
          $route = $this->routeProvider->getRouteByName($route_name);
          $options = [];
          foreach ($route->getOptions()['parameters'] as $name => $details) {
            if ($name == $this->entity->getEntityTypeId()) {
              $options[$name] = $this->entity->id();
            }
            if ($name == 'webform_submission') {
              $options['webform'] = $this->entity->getWebform()->id();
            }
          }
          $url = $this->urlGenerator->generateFromRoute(
            $route_name, $options, ['absolute' => TRUE]);
          break;

        case 'print':
          $url = $this->urlGenerator->generateFromRoute(
            'printable.show_format.' . $this->entity->getEntityTypeId(), [
            'printable_format' => 'print',
            'entity' => $this->entity->id(),
          ], [
            'absolute' => TRUE,
          ]);
          break;
      }
    }

    if ($url) {
      $this->tab->goto($url);
    } else {
      $this->tab->setContent($this->html);
    }

    if ($this->settings->get('triggerDebugging')) {
      $this->tab->evaluate(JsFunction::createWithBody("debugger")->async(true));
    }

    if ($this->settings->get('pagedjs')) {
      $this->tab->addScriptTag([
        'url' => 'https://unpkg.com/pagedjs/dist/paged.polyfill.js',
        'text' => 'text/javascript',
      ]);
    }

    $this->tab->emulateMediaType('print');

    if (!$this->isService && !$this->isHeadless) {
      // Wait until browser is closed.
      while ($this->browser->isConnected()) {
        sleep(1);
      }
      exit(0);
    }

    if ($this->settings->get('pagedjs')) {
      $this->tab->waitForXPath('//template');
    } else {
      $this->tab->waitForNetworkIdle();
    }
  }

  /**
   * Retrieve a PDF from Chrome.
   */
  public function getPdfContent() {
    $options = [
      'printBackground' => TRUE,
      'preferCSSPageSize' => TRUE,
      'displayHeaderFooter' => TRUE,
    ];
    if ($this->landscape) {
      $options['landscape'] = TRUE;
    }
    if ($this->page_size !== 'Letter') {
      $options['format'] = $this->page_size;
    }
    if ($this->header) {
      $options['headerTemplate'] = (string)$this->header;
    }
    if ($this->footer) {
      $options['footerTemplate'] = (string)$this->footer;
    }

    // To output from Chrome directly to the filesystem:
    // $options['path'] = $location;
    $buffer = $this->tab->pdf($options);

    // Don't just cast to a string - that messes up the encoding.
    return base64_decode($buffer->toString('base64'));
  }

  /**
   * {@inheritdoc}
   */
  public function save($location) {
    $this->setContent();
    file_put_contents($location, $this->getPdfContent());

    if (!$this->settings->get('leave_running')) {
      $this->browser = NULL;
      $this->tab = NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function send() {
    $this->generator->stream("pdf", array('Attachment' => 0));
  }

  /**
   * {@inheritdoc}
   */
  public function stream($filelocation) {
    $this->generator->Output($filelocation, "F");
  }

}
