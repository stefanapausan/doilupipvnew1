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
class EditFurnizor extends FormBase {


  public function getFormId() {
    return 'edit_furnizor_form';
  }


  	public function buildForm(array $form, FormStateInterface $form_state){
		//var_dump('tesssssss');
		//die;

		$id_furnizor = \Drupal:: routeMatch()->getParameter('id_furnizor');
		
		$query = \Drupal::database();
		$data = $query->select('dashboard_furnizori','e')
					  ->fields('e',['id_furnizor','nume_furnizor','telefon_furnizor'])
					  ->condition('e.id_furnizor',$id_furnizor,'=')
					  ->execute()->fetchAll(\PDO::FETCH_OBJ);
		//print_r($data); exit;

		$form['nume_furnizor'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Nume furnizor'),
			'#default_value'=>$data[0]->nume_furnizor,
			'#required' => TRUE,
		];
		
		$form['telefon_furnizor'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Telefon furnizor'),
			'#default_value'=>$data[0]->telefon_furnizor,
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
	   
		$fields["nume_furnizor"] = $field['nume_furnizor'];
		if (!$form_state->getValue('nume_furnizor') || empty($form_state->getValue('nume_furnizor'))) {
            $form_state->setErrorByName('nume_furnizor', $this->t('Nume furnizor e obligatoriu'));
        }
		
		$fields["telefon_furnizor"] = $field['telefon_furnizor'];
		if (!$form_state->getValue('telefon_furnizor') || empty($form_state->getValue('telefon_furnizor'))) {
            $form_state->setErrorByName('telefon_furnizor', $this->t('Telefonul e obligatoriu'));
        }
		
		
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array & $form, FormStateInterface $form_state) {
	try{//var_dump('tesssssss');
		//die;
		
		$id_furnizor = \Drupal:: routeMatch()->getParameter('id_furnizor');
		$conn = Database::getConnection();
		
		$field = $form_state->getValues();
	   
		$fields["nume_furnizor"] = $field['nume_furnizor'];
		$fields["telefon_furnizor"] = $field['telefon_furnizor'];
	
		
		$conn->update('dashboard_furnizori')
			   ->fields($fields)
			   ->condition('id_furnizor',$id_furnizor)
			   ->execute();
			   
		\Drupal::messenger()->addMessage($this->t('Datele au fost salvate'));
		drupal_flush_all_caches();
	} catch(Exception $ex){
		\Drupal::logger('test_db')->error($ex->getMessage());
	}
    $response = new \Symfony\Component\HttpFoundation\RedirectResponse('../lista_furnizori');
	$response->send();
  }

}