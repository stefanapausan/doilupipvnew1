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
class EditAspect extends FormBase {


  public function getFormId() {
    return 'edit_aspect_form';
  }


  	public function buildForm(array $form, FormStateInterface $form_state){
		//var_dump('tesssssss');
		//die;

		$id_aspect = \Drupal:: routeMatch()->getParameter('id_aspect');
		
		$query = \Drupal::database();
		$data = $query->select('dashboard_aspect','e')
					  ->fields('e',['id_aspect','aspect_estetic'])
					  ->condition('e.id_aspect',$id_aspect,'=')
					  ->execute()->fetchAll(\PDO::FETCH_OBJ);
		//print_r($data); exit;

		$form['aspect_estetic'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Nume aspect'),
			'#default_value'=>$data[0]->aspect_estetic,
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
	   
		$fields["aspect_estetic"] = $field['aspect_estetic'];
		if (!$form_state->getValue('aspect_estetic') || empty($form_state->getValue('aspect_estetic'))) {
            $form_state->setErrorByName('aspect_estetic', $this->t('Nume aspect e obligatoriu'));
        }
		
		
		
		
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array & $form, FormStateInterface $form_state) {
	try{//var_dump('tesssssss');
		//die;
		
		$id_aspect = \Drupal:: routeMatch()->getParameter('id_aspect');
		$conn = Database::getConnection();
		
		$field = $form_state->getValues();
	   
		$fields["aspect_estetic"] = $field['aspect_estetic'];
		
	
		
		$conn->update('dashboard_aspect')
			   ->fields($fields)
			   ->condition('id_aspect',$id_aspect)
			   ->execute();
			   
		\Drupal::messenger()->addMessage($this->t('Datele au fost salvate'));
		drupal_flush_all_caches();
	} catch(Exception $ex){
		\Drupal::logger('test_db')->error($ex->getMessage());
	}
    $response = new \Symfony\Component\HttpFoundation\RedirectResponse('../lista_aspect');
	$response->send();
  }

}