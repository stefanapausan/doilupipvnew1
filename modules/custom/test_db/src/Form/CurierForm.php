<?php

namespace Drupal\test_db\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

class CurierForm extends FormBase {
	
	// public function __construct(){
		// var_dump('testtttt');
		// die;
	// }
	
	public function getFormId(){
		return 'curier_form';
	}
	
	public function buildForm(array $form, FormStateInterface $form_state){
		//var_dump('tesssssss');
		//die;
		$form['nume_curier'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Denumire curier'),
			'#required' => TRUE,
		];
		
		$form['telefon_curier'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Telefon curier'),
			'#required' => TRUE,
		];
		
		$form['actions']['#type'] = 'actions';
		$form['actions']['submit'] = [
			'#type' => 'submit',
			'#value' => $this->t('Salveaza'),
			'#button_type' => 'primary',
		];
		return $form;
	}
	
	public function validateForm(array &$form, FormStateInterface $form_state){
		if($form_state->getValue('nume_curier') == ''){
			$form_state->setErrorByName('nume_curier', $this->t('Numele este obligatoriu'));
		}
		if($form_state->getValue('telefon_curier') == ''){
			$form_state->setErrorByName('telefon_curier', $this->t('Telefonul este obligatoriu'));
		}
	}
	
	public function submitForm(array & $form, FormStateInterface $form_state) {
	try{//var_dump('tesssssss');
		//die;
		$conn = Database::getConnection();
		
		$field = $form_state->getValues();
	   
		$fields["nume_curier"] = $field['nume_curier'];
		$fields["telefon_curier"] = $field['telefon_curier'];
		
		
		  $conn->insert('dashboard_curieri')
			   ->fields($fields)->execute();
		  \Drupal::messenger()->addMessage($this->t('Datele au fost salvate'));
		  drupal_flush_all_caches();
	} catch(Exception $ex){
		\Drupal::logger('test_db')->error($ex->getMessage());
	}
    
  }
}