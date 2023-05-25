<?php

namespace Drupal\test_db\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

class AspectForm extends FormBase {
	
	// public function __construct(){
		// var_dump('testtttt');
		// die;
	// }
	
	public function getFormId(){
		return 'aspect_form';
	}
	
	public function buildForm(array $form, FormStateInterface $form_state){
		//var_dump('tesssssss');
		//die;
		$form['aspect_estetic'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Aspect estetic'),
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
		if($form_state->getValue('aspect_estetic') == ''){
			$form_state->setErrorByName('aspect_estetic', $this->t('Campul este obligatoriu'));
		}
		
	}
	
	public function submitForm(array & $form, FormStateInterface $form_state) {
	try{//var_dump('tesssssss');
		//die;
		$conn = Database::getConnection();
		
		$field = $form_state->getValues();
	   
		$fields["aspect_estetic"] = $field['aspect_estetic'];
		
		
		
		  $conn->insert('dashboard_aspect')
			   ->fields($fields)->execute();
		  \Drupal::messenger()->addMessage($this->t('Datele au fost salvate'));
		  drupal_flush_all_caches();
	} catch(Exception $ex){
		\Drupal::logger('test_db')->error($ex->getMessage());
	}
    $response = new \Symfony\Component\HttpFoundation\RedirectResponse('../test_db/lista_aspect');
	$response->send();
  }
}