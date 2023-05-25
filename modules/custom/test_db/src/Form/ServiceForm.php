<?php

namespace Drupal\test_db\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;


use \Drupal\Core\Routing\TrustedRedirectResponse;

/**
 * Provides the form for adding countries.
 */
class ServiceForm extends FormBase {


  public function getFormId() {
    return 'service_form';
  }


/*  public function buildForm(array $form, FormStateInterface $form_state) {

//var_dump('tesssssss');
		//die;
    
    $form['nume_service'] = [
      '#type' => 'varchar',
      '#title' => $this->t('Denumire service'),
      '#required' => TRUE,
      '#maxlength' => 50,
      '#default_value' =>  '',
    ];
	 $form['telefon_service'] = [
      '#type' => 'varchar',
      '#title' => $this->t('Telefon service'),
      '#required' => TRUE,
      '#maxlength' => 50,
      '#default_value' =>  '',
    ];
	$form['adresa_service'] = [
      '#type' => 'varchar',
      '#title' => $this->t('Adresa service'),
      '#required' => TRUE,
      '#maxlength' => 50,
      '#default_value' => '',
    ];
	 $form['detalii_producator'] = [
      '#type' => 'text',
      '#title' => $this->t('Marks'),
      '#required' => FALSE,
      '#default_value' => '',
    ];
	
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#default_value' => $this->t('Salveaza') ,
    ];
	
	

    return $form;

  }*/
  
  	public function buildForm(array $form, FormStateInterface $form_state){
		//var_dump('tesssssss');
		//die;

		$form['nume_service'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Nume service'),
			'#required' => TRUE,
		];
		
		$form['telefon_service'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Telefon service'),
			'#required' => TRUE,
		];
		
		$form['adresa_service'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Adresa service'),
			
		];
		
		$form['detalii_producator'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Detalii producator'),
			
		];
		
		
		$form['actions']['#type'] = 'actions';
		$form['actions']['submit'] = [
			'#type' => 'submit',
			'#value' => $this->t('Save'),
			'#button_type' => 'primary',
		];
		return $form;
	}
  
   /**
   * {@inheritdoc}
   */
  public function validateForm(array & $form, FormStateInterface $form_state) {
       $field = $form_state->getValues();
	   
		$fields["nume_service"] = $field['nume_service'];
		if (!$form_state->getValue('nume_service') || empty($form_state->getValue('nume_service'))) {
            $form_state->setErrorByName('nume_service', $this->t('Nume service e obligatoriu'));
        }
		
		$fields["telefon_service"] = $field['telefon_service'];
		if (!$form_state->getValue('telefon_service') || empty($form_state->getValue('telefon_service'))) {
            $form_state->setErrorByName('telefon_service', $this->t('Telefonul e obligatoriu'));
        }
		
		
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array & $form, FormStateInterface $form_state) {
	try{//var_dump('tesssssss');
		//die;
		$conn = Database::getConnection();
		
		$field = $form_state->getValues();
	   
		$fields["nume_service"] = $field['nume_service'];
		$fields["telefon_service"] = $field['telefon_service'];
		$fields["adresa_service"] = $field['adresa_service'];
		$fields["detalii_producator"] = $field['detalii_producator'];
		
		$conn->insert('dashboard_service')
			   ->fields($fields)->execute();
			   
		\Drupal::messenger()->addMessage($this->t('Datele au fost salvate'));
		drupal_flush_all_caches();
	} catch(Exception $ex){
		\Drupal::logger('test_db')->error($ex->getMessage());
	}
    $response = new \Symfony\Component\HttpFoundation\RedirectResponse('../test_db/lista_service');
	$response->send();
  }

}