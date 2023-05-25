<?php

namespace Drupal\test_db\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;


use \Drupal\Core\Routing\TrustedRedirectResponse;

/**
 * Provides the form for adding countries.
 */
class FinalizareProcesVerbal extends FormBase {


  public function getFormId() {
    return 'finalizare_proces_verbal';
  }


  	public function buildForm(array $form, FormStateInterface $form_state){
		//var_dump('tesssssss');
		//die;

		$id_proces = \Drupal:: routeMatch()->getParameter('id_proces');
		
		//$query = \Drupal::database();
		
		$form['markup1'] = [
			'#markup' => '<h5>Date client</h5><hr />',
			'#title' => $this->t('Date client'),
		
		];
		
		/*$form['group1'] = array(
			'#tree' => TRUE,
		);*/
		$form['id_proces'] = [
			'#type' => 'textfield',
			'#prefix'=>'<table><tr><td>',
            '#suffix'=>'</td>',
			'#default_value'=>$id_proces,
			'#title' => $this->t('Numar proces verbal'),
			'#attributes' => array('disabled' => 'disabled'),
		];
		
		$form['nume_client_ridicare'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr></table>',
			'#default_value'=>'',
			'#title' => $this->t('Nume semnatar'),
			'#required' => TRUE,
			
		];
		
		$form['semnatura_ridicare'] = [
			'#type' => 'textarea',			
			'#title' => $this->t('Semnatura'),
			
		];
		
		$form['actions']['#type'] = 'actions';
		$form['actions']['submit'] = [
			'#type' => 'submit',
			'#value' => $this->t('Finalizare proces'),
			'#button_type' => 'primary',
		];
		
		return $form;
	}
  
   /**
   * {@inheritdoc}
   */
  public function validateForm(array & $form, FormStateInterface $form_state) {
       $field = $form_state->getValues();
	   
		/*$fields["nume_furnizor"] = $field['nume_furnizor'];
		if (!$form_state->getValue('nume_furnizor') || empty($form_state->getValue('nume_furnizor'))) {
            $form_state->setErrorByName('nume_furnizor', $this->t('Nume furnizor e obligatoriu'));
        }
		
		$fields["telefon_furnizor"] = $field['telefon_furnizor'];
		if (!$form_state->getValue('telefon_furnizor') || empty($form_state->getValue('telefon_furnizor'))) {
            $form_state->setErrorByName('telefon_furnizor', $this->t('Telefonul e obligatoriu'));
        }*/
		
		
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array & $form, FormStateInterface $form_state) {
	try{//var_dump('tesssssss');
		//die;
		
		$id_proces = \Drupal:: routeMatch()->getParameter('id_proces');
		$conn = Database::getConnection();
		
		$field = $form_state->getValues();
	   
		$fields["nume_client_ridicare"] = $field['nume_client_ridicare'];
		$fields["semnatura_ridicare"] = $field['semnatura_ridicare'];
		$fields["finalizat"]='1';
		
		
		$current_user = \Drupal::currentUser();
		$current_time = new DrupalDateTime('now', 'UTC');
		$userTimezone = new \DateTimeZone($current_user->getTimeZone());
		$date_time = new DrupalDateTime();
		$timezone_offset = $userTimezone->getOffset($date_time->getPhpDateTime());
		$time_interval = \DateInterval::createFromDateString($timezone_offset . 'seconds');
		$current_time->add($time_interval);
		$result = $current_time->format('Y-m-d h:i:s');

		$fields["data_ridicare"] = $result;
		
		$conn->update('dashboard_procese_verbale')
			   ->fields($fields)
			   ->condition('id_proces',$id_proces)
			   ->execute();
			   
		\Drupal::messenger()->addMessage($this->t('Datele au fost salvate'));
		drupal_flush_all_caches();
	} catch(Exception $ex){
		\Drupal::logger('test_db')->error($ex->getMessage());
	}
    $response = new \Symfony\Component\HttpFoundation\RedirectResponse('../../test_db');
	$response->send();
  }

}