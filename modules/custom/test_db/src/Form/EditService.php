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
class EditService extends FormBase {


  public function getFormId() {
    return 'edit_service_form';
  }


  	public function buildForm(array $form, FormStateInterface $form_state){
		//var_dump('tesssssss');
		//die;

		$id_service = \Drupal:: routeMatch()->getParameter('id_service');
		
		$query = \Drupal::database();
		$data = $query->select('dashboard_service','e')
					  ->fields('e',['id_service','nume_service','telefon_service','adresa_service','detalii_producator'])
					  ->condition('e.id_service',$id_service,'=')
					  ->execute()->fetchAll(\PDO::FETCH_OBJ);
		//print_r($data); exit;

		$form['nume_service'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Nume service'),
			'#default_value'=>$data[0]->nume_service,
			'#required' => TRUE,
		];
		
		$form['telefon_service'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Telefon service'),
			'#default_value'=>$data[0]->telefon_service,
			'#required' => TRUE,
		];
		
		$form['adresa_service'] = [
			'#type' => 'textfield',
			'#default_value'=>$data[0]->adresa_service,
			'#title' => $this->t('Adresa service'),
			
		];
		
		$form['detalii_producator'] = [
			'#type' => 'textfield',
			'#default_value'=>$data[0]->detalii_producator,
			'#title' => $this->t('Detalii producator'),
			
		];
		
		
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
		
		$id_service = \Drupal:: routeMatch()->getParameter('id_service');
		$conn = Database::getConnection();
		
		$field = $form_state->getValues();
	   
		$fields["nume_service"] = $field['nume_service'];
		$fields["telefon_service"] = $field['telefon_service'];
		$fields["adresa_service"] = $field['adresa_service'];
		$fields["detalii_producator"] = $field['detalii_producator'];
		
		$conn->update('dashboard_service')
			   ->fields($fields)
			   ->condition('id_service',$id_service)
			   ->execute();
			   
		\Drupal::messenger()->addMessage($this->t('Datele au fost salvate'));
		drupal_flush_all_caches();
	} catch(Exception $ex){
		\Drupal::logger('test_db')->error($ex->getMessage());
	}
    $response = new \Symfony\Component\HttpFoundation\RedirectResponse('../lista_service');
	$response->send();
  }

}