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
class EditCurier extends FormBase {


  public function getFormId() {
    return 'edit_curier_form';
  }


  	public function buildForm(array $form, FormStateInterface $form_state){
		//var_dump('tesssssss');
		//die;

		$id_curier = \Drupal:: routeMatch()->getParameter('id_curier');
		
		$query = \Drupal::database();
		$data = $query->select('dashboard_curieri','e')
					  ->fields('e',['id_curier','nume_curier','telefon_curier'])
					  ->condition('e.id_curier',$id_curier,'=')
					  ->execute()->fetchAll(\PDO::FETCH_OBJ);
		//print_r($data); exit;

		$form['nume_curier'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Nume curier'),
			'#default_value'=>$data[0]->nume_curier,
			'#required' => TRUE,
		];
		
		$form['telefon_curier'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Telefon curier'),
			'#default_value'=>$data[0]->telefon_curier,
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
  
   /**
   * {@inheritdoc}
   */
  public function validateForm(array & $form, FormStateInterface $form_state) {
       $field = $form_state->getValues();
	   
		$fields["nume_curier"] = $field['nume_curier'];
		if (!$form_state->getValue('nume_curier') || empty($form_state->getValue('nume_curier'))) {
            $form_state->setErrorByName('nume_curier', $this->t('Nume curier e obligatoriu'));
        }
		
		$fields["telefon_curier"] = $field['telefon_curier'];
		if (!$form_state->getValue('telefon_curier') || empty($form_state->getValue('telefon_curier'))) {
            $form_state->setErrorByName('telefon_curier', $this->t('Telefonul e obligatoriu'));
        }
		
		
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array & $form, FormStateInterface $form_state) {
	try{//var_dump('tesssssss');
		//die;
		
		$id_curier = \Drupal:: routeMatch()->getParameter('id_curier');
		$conn = Database::getConnection();
		
		$field = $form_state->getValues();
	   
		$fields["nume_curier"] = $field['nume_curier'];
		$fields["telefon_curier"] = $field['telefon_curier'];
	
		
		$conn->update('dashboard_curieri')
			   ->fields($fields)
			   ->condition('id_curier',$id_curier)
			   ->execute();
			   
		\Drupal::messenger()->addMessage($this->t('Datele au fost salvate'));
		drupal_flush_all_caches();
	} catch(Exception $ex){
		\Drupal::logger('test_db')->error($ex->getMessage());
	}
    $response = new \Symfony\Component\HttpFoundation\RedirectResponse('../lista_curieri');
	$response->send();
  }

}