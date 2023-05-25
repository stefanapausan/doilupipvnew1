<?php

namespace Drupal\test_db\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use Drupal\node\Entity\Node;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;


use \Drupal\Core\Routing\TrustedRedirectResponse;

/**
 * Provides the form for adding countries.
 */
class ProcesVerbalForm extends FormBase {


  public function getFormId() {
    return 'proces_verbal_form';
  }

  
  	public function buildForm(array $form, FormStateInterface $form_state){
		
		
		
		$form['markup1'] = [
			'#markup' => '<h5>Date client</h5><hr />',
			'#title' => $this->t('Date client'),
		
		];
		
		
		
		$form['nume_client'] = [
			'#type' => 'textfield',
			'#prefix'=>'<table><tr><td>',
            '#suffix'=>'</td>',
			'#title' => $this->t('Nume client'),
			'#required' => TRUE,
		];
		
		$form['telefon_client'] = [
			'#type' => 'textfield',
			'#prefix'=>'<div id="result"></div><td>',
            '#suffix'=>'</td></tr>',
			'#title' => $this->t('Telefon client'),
			'#required' => TRUE,
		];
		
		
		
		$form['adresa_client'] = [
			'#type' => 'textfield',
			'#prefix'=>'<tr><td>',
            '#suffix'=>'</td>',
			'#title' => $this->t('Adresa client'),
			'#required' => TRUE,
			
		];
		
		$form['email_client'] = [
			'#type' => 'email',
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr></table>',
			'#title' => $this->t('Email client'),
			
		];
		
		$form['markup2'] = [
			'#markup' => '<h5>Date produs predare</h5><hr />',
			'#title' => $this->t('Date produs predare'),
		
		];
		
		$form['descriere'] = [
			'#type' => 'textfield',
			'#prefix'=>'<table><tr><td>',
            '#suffix'=>'</td>',
			'#title' => $this->t('Descriere produs'),
			'#required' => TRUE,
		];
		
		$form['brand'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr>',
			'#title' => $this->t('Brand'),
			'#required' => TRUE,
		];
		
		
		
		$form['model'] = [
			'#type' => 'textfield',
			'#prefix'=>'<tr><td>',
            '#suffix'=>'</td>',
			'#title' => $this->t('Model produs'),
			'#required' => TRUE,
			
		];
		
		$form['serie'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr>',
			'#title' => $this->t('Serie'),
			'#required' => TRUE,
			
		];
		
		$form['IMEI1'] = [
			'#type' => 'textfield',
			'#prefix'=>'<tr><td>',
            '#suffix'=>'</td>',
			'#title' => $this->t('IMEI1'),
			
			
		];
		
		$form['IMEI2'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr>',
			'#title' => $this->t('IMEI2'),
			
		];
		
		$form['nr_garantie'] = [
			'#type' => 'textfield',
			'#prefix'=>'<tr><td>',
            '#suffix'=>'</td>',
			'#title' => $this->t('Numar certificat garantie'),
			
			
		];
		
		$form['nr_factura'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr>',
			'#title' => $this->t('Numar factura'),
			'#required' => TRUE,
			
		];
		
		$form['accesorii'] = [
			'#type' => 'textfield',
			'#prefix'=>'<tr><td>',
            '#suffix'=>'</td>',
			'#title' => $this->t('Accesorii'),
			'#required' => TRUE,
			
			
		];
		
		$result = \Drupal::database()->select('dashboard_aspect', 'n')
            ->fields('n', array('id_aspect','aspect_estetic'))
            ->execute()->fetchAllAssoc('id_aspect');
		$options = array();
		foreach($result as $row) {
		$options[$row->id_aspect] = $row->aspect_estetic;
		}		
		
		$form['aspect'] = [
			'#type' => 'select',
			'#prefix'=>'<td>',
			'#options' => $options,
			'#default_value' => 'Alege aspect estetic',	
            '#suffix'=>'</td></tr></table>',
			'#title' => $this->t('Aspect estetic'),
			'#required' => TRUE,
			
		];
		
		$form['markup3'] = [
			'#markup' => '<h5>Defect reclamat client</h5><hr />',
			'#title' => $this->t('Defect reclamat client'),
		
		];
		
		$form['defect'] = [
			'#type' => 'textarea',
			'#title' => $this->t('Descrierea defectului'),
			'#required' => TRUE,
			
			
		];
		
		$options1 = array(
		'reparabil' => t('Produs reparabil'),
		'inlocuibil' => t('Produs inlocuibil'), 
		);
		
		$form['tip_produs'] = array(
		'#title' => $this->t('Tipul produsului'),
		'#type' => 'radios',
		'#options' => $options1,
		'#default_value' => $options1['reparabil'],
		
		);
		
		$form['markup4'] = [
			'#markup' => '<h5>Date service</h5><hr />',
			'#title' => $this->t('Date service'),
		
		];
		
		$result = \Drupal::database()->select('dashboard_service', 'n')
            ->fields('n', array('id_service','nume_service'))
            ->execute()->fetchAllAssoc('id_service');
		$options2 = array();
		foreach($result as $row) {
		$options2[$row->id_service] = $row->nume_service;
		}		
		
		
		//$form['service']['#default_value']='Alege service';
		$default = 'Alege service';
		$form['service'] = [
			'#type' => 'select',
			'#prefix'=>'<div>',
            '#suffix'=>'</div>',
			'#options' => $options2,  
			'#default_value' => $default,			
			'#title' => $this->t('Nume service'),
			
			
		];
		
		
		$form['markup5'] = [
			'#markup' => '<h5>Semnatura</h5><hr />',
			'#title' => $this->t('Date service'),
		
		];
	
		
		$form['signature'] = array(
		
		'#prefix'=>'<div id="canvasDiv">',
            '#suffix'=>'</div><br><button type="button" class="btn btn-danger" id="reset-btn">Clear</button>
				<input type="hidden" id="signature" name="signature">
                <input type="hidden" name="signaturesubmit" value="1">',
		
		'#type' => 'html_tag',
        '#tag' => 'canvas',
		'#attributes' => array(    
			'id' => 'canvas',   
			)
		
		); 
		
		
		
		
		$form['actions']['#type'] = 'actions';
		$form['actions']['submit'] = [
			'#type' => 'submit',
			'#value' => $this->t('Salveaza'),
			'#button_type' => 'primary',
		];
		return $form;
	}
  
   /**
   * {@inheritdoc}
   */
  public function validateForm(array & $form, FormStateInterface $form_state) {
       $field = $form_state->getValues();
	   
		/*$fields["nume_service"] = $field['nume_service'];
		if (!$form_state->getValue('nume_service') || empty($form_state->getValue('nume_service'))) {
            $form_state->setErrorByName('nume_service', $this->t('Nume service e obligatoriu'));
        }
		
		$fields["telefon_service"] = $field['telefon_service'];
		if (!$form_state->getValue('telefon_service') || empty($form_state->getValue('telefon_service'))) {
            $form_state->setErrorByName('telefon_service', $this->t('Telefonul e obligatoriu'));
        }*/
		
		
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array & $form, FormStateInterface $form_state) {
	try{
		$conn = Database::getConnection();
		
		$field = $form_state->getValues();
	   
		$fields["nume_client"] = $field['nume_client'];
		$fields["telefon_client"] = $field['telefon_client'];
		$fields["adresa_client"] = $field['adresa_client'];
		$fields["email_client"] = $field['email_client'];
		
		$fields["descriere"] = $field['descriere'];
		$fields["brand"] = $field['brand'];
		$fields["model"] = $field['model'];
		$fields["serie"] = $field['serie'];
		
		$fields["IMEI1"] = $field['IMEI1'];
		$fields["IMEI2"] = $field['IMEI2'];
		$fields["nr_garantie"] = $field['nr_garantie'];
		$fields["nr_factura"] = $field['nr_factura'];
		
		$fields["accesorii"] = $field['accesorii'];
		$fields["aspect"] = $field['aspect'];
		$fields["defect"] = $field['defect'];
		$fields["tip_produs"] = $field['tip_produs'];
		
		$fields["service"] = $field['service'];
		
		
		$signature = $field['signature'];
		
		print_r($field['signature']); exit;
		$signatureFileName = uniqid().'.png';
		$signature = str_replace('data:image/png;base64,', '', $signature);
		$signature = str_replace(' ', '+', $signature);
		$data = base64_decode($signature);
		$file = '../test_db/signatures/'.$signatureFileName;
		file_put_contents($file, $data);
		
		$fields["semnatura"] = $signatureFileName;
		
		$current_user = \Drupal::currentUser();
		$current_time = new DrupalDateTime('now', 'UTC');
		$userTimezone = new \DateTimeZone($current_user->getTimeZone());
		$date_time = new DrupalDateTime();
		$timezone_offset = $userTimezone->getOffset($date_time->getPhpDateTime());
		$time_interval = \DateInterval::createFromDateString($timezone_offset . 'seconds');
		$current_time->add($time_interval);
		$result = $current_time->format('Y-m-d h:i:s');

		$fields["data_reclamatie"] = $result;
		
		$conn->insert('dashboard_procese_verbale')
			   ->fields($fields)->execute();
			   
		\Drupal::messenger()->addMessage($this->t('Datele au fost salvate'));
		drupal_flush_all_caches();
	} catch(Exception $ex){
		\Drupal::logger('test_db')->error($ex->getMessage());
	}
    $response = new \Symfony\Component\HttpFoundation\RedirectResponse('../test_db');
	$response->send();
  }
  
  
 

}