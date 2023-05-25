<?php

namespace Drupal\test_db\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

class FurnizorForm extends FormBase {
	
	// public function __construct(){
		// var_dump('testtttt');
		// die;
	// }
	
	public function getFormId(){
		return 'furnizor_form';
	}
	
	public function buildForm(array $form, FormStateInterface $form_state){
		//var_dump('tesssssss');
		//die;
		$form['nume_furnizor'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Denumire furnizor'),
			'#required' => TRUE,
		];
		
		$form['telefon_furnizor'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Telefon furnizor'),
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
		if($form_state->getValue('nume_furnizor') == ''){
			$form_state->setErrorByName('nume_furnizor', $this->t('Numele este obligatoriu'));
		}
		if($form_state->getValue('telefon_furnizor') == ''){
			$form_state->setErrorByName('telefon_furnizor', $this->t('Telefonul este obligatoriu'));
		}
	}
	
	public function submitForm(array & $form, FormStateInterface $form_state) {
	try{//var_dump('tesssssss');
		//die;
		$conn = Database::getConnection();
		
		$field = $form_state->getValues();
	   
		$fields["nume_furnizor"] = $field['nume_furnizor'];
		$fields["telefon_furnizor"] = $field['telefon_furnizor'];
		
		
		  $conn->insert('dashboard_furnizori')
			   ->fields($fields)->execute();
		  \Drupal::messenger()->addMessage($this->t('Datele au fost salvate'));
		  drupal_flush_all_caches();
	} catch(Exception $ex){
		\Drupal::logger('test_db')->error($ex->getMessage());
	}
    $response = new \Symfony\Component\HttpFoundation\RedirectResponse('../test_db/lista_furnizori');
	$response->send();
  }
}