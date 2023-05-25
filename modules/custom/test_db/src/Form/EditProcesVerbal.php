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
class EditProcesVerbal extends FormBase {


  public function getFormId() {
    return 'edit_proces';
  }

  
  	public function buildForm(array $form, FormStateInterface $form_state){
		
		$id_proces = \Drupal:: routeMatch()->getParameter('id_proces');
		
		$query = \Drupal::database();
		$data = $query->select('dashboard_procese_verbale','e')
					  ->fields('e')
					  ->condition('e.id_proces',$id_proces,'=')
					  ->execute()->fetchAll(\PDO::FETCH_OBJ);
		
		$form['markup1'] = [
			'#markup' => '<h5>Date client</h5><hr />',
			'#title' => $this->t('Date client'),
		
		];
		
		/*$form['group1'] = array(
			'#tree' => TRUE,
		);*/
		
		$form['nume_client'] = [
			'#type' => 'textfield',
			'#prefix'=>'<table><tr><td>',
            '#suffix'=>'</td>',
			'#default_value'=>$data[0]->nume_client,
			'#title' => $this->t('Nume client'),
			'#required' => TRUE,
			
		];
		
		$form['telefon_client'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr>',
			'#default_value'=>$data[0]->telefon_client,
			'#title' => $this->t('Telefon client'),
			'#required' => TRUE,
		];
		
		$form['id_proces'] = [
			'#type' => 'hidden',
			'#default_value'=>$id_proces,
			
		];
		
		
		
		$form['adresa_client'] = [
			'#type' => 'textfield',
			'#prefix'=>'<tr><td>',
            '#suffix'=>'</td>',
			'#default_value'=>$data[0]->adresa_client,
			'#title' => $this->t('Adresa client'),
			'#required' => TRUE,
			
		];
		
		$form['email_client'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
			'#default_value'=>$data[0]->email_client,
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
			'#default_value'=>$data[0]->descriere,
			'#title' => $this->t('Descriere produs'),
			'#required' => TRUE,
		];
		
		$form['brand'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr>',
			'#default_value'=>$data[0]->brand,
			'#title' => $this->t('Brand'),
			'#required' => TRUE,
		];
		
		
		
		$form['model'] = [
			'#type' => 'textfield',
			'#prefix'=>'<tr><td>',
            '#suffix'=>'</td>',
			'#default_value'=>$data[0]->model,
			'#title' => $this->t('Model produs'),
			'#required' => TRUE,
			
		];
		
		$form['serie'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr>',
			'#default_value'=>$data[0]->serie,
			'#title' => $this->t('Serie'),
			'#required' => TRUE,
			
		];
		
		$form['IMEI1'] = [
			'#type' => 'textfield',
			'#prefix'=>'<tr><td>',
            '#suffix'=>'</td>',
			'#default_value'=>$data[0]->IMEI1,
			'#title' => $this->t('IMEI1'),
			
			
		];
		
		$form['IMEI2'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr>',
			'#default_value'=>$data[0]->IMEI2,
			'#title' => $this->t('IMEI2'),
			
		];
		
		$form['nr_garantie'] = [
			'#type' => 'textfield',
			'#prefix'=>'<tr><td>',
            '#suffix'=>'</td>',
			'#default_value'=>$data[0]->nr_garantie,
			'#title' => $this->t('Numar certificat garantie'),
			
			
		];
		
		$form['nr_factura'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr>',
			'#default_value'=>$data[0]->nr_factura,
			'#title' => $this->t('Numar factura'),
			'#required' => TRUE,
			
		];
		
		$form['accesorii'] = [
			'#type' => 'textfield',
			'#prefix'=>'<tr><td>',
            '#suffix'=>'</td>',
			'#default_value'=>$data[0]->accesorii,
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
			'#default_value'=>$data[0]->aspect,
			'#title' => $this->t('Aspect estetic'),
			'#required' => TRUE,
			
		];
		
		$form['markup3'] = [
			'#markup' => '<h5>Defect reclamat client</h5><hr />',
			'#title' => $this->t('Defect reclamat client'),
		
		];
		
		$form['defect'] = [
			'#type' => 'textarea',
			'#default_value'=>$data[0]->defect,
			'#title' => $this->t('Descrierea defectului'),
			'#required' => TRUE,
			
			
		];
		
		
		
		$form['markup4'] = [
			'#markup' => '<h5>Date service</h5><hr />',
			'#title' => $this->t('Date service'),
		
		];
		
		$query = \Drupal::database();
		$data1 = $query->select('dashboard_service','s')
					  ->fields('s')
					  ->condition('s.id_service',$data[0]->service,'=')
					  ->execute()->fetchAll(\PDO::FETCH_OBJ);
					  
					  
		//print_r($data[0]->service); exit;
			$form['nume_service'] = [
			'#type' => 'textfield',
			'#prefix'=>'<table><tr><td>',
            '#suffix'=>'</td>',
			'#default_value'=>$data1[0]->nume_service,
			'#title' => $this->t('Denumire service'),
			'#required' => TRUE,
			'#attributes' => array('disabled' => 'disabled'),
		];
		
		$form['telefon_service'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr>',
			'#default_value'=>$data1[0]->telefon_service,
			'#title' => $this->t('Telefon service'),
			'#required' => TRUE,
			'#attributes' => array('disabled' => 'disabled'),
		];
		
		
		
		$form['adresa_service'] = [
			'#type' => 'textfield',
			'#prefix'=>'<tr><td>',
            '#suffix'=>'</td></tr></table>',
			'#default_value'=>$data1[0]->adresa_service,
			'#title' => $this->t('Adresa service'),
			'#required' => TRUE,
			'#attributes' => array('disabled' => 'disabled'),
		];
		
	
		
	    $form['markup5'] = [
			'#markup' => '<h5>Alte date</h5><hr />',
			'#title' => $this->t('Alte date'),
		
		];
		
		$query = \Drupal::database();
		$result = \Drupal::database()->select('dashboard_curieri', 'n')
            ->fields('n', array('id_curier','nume_curier'))
            ->execute()->fetchAllAssoc('id_curier');
					  
		$options1 = array(
		'curierat' => t('Curierat'),
		'furnizor' => t('Furnzior'), 
		);
		
			$form['trimis_prin'] = [
			'#type' => 'radios',
			'#prefix'=>'<table><tr><td>',
            '#suffix'=>'</td>',
			'#options' => $options1,
		    '#default_value' => 'curierat',
			'#title' => $this->t('Trimis prin'),
		
		];
		//print_r($options1); exit;
		$form['RMA'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr>',
			//'#default_value'=>$data[0]->RMA,
			'#title' => $this->t('Numar RMA'),
			
		];
		
		$query = \Drupal::database();
		$result = \Drupal::database()->select('dashboard_curieri', 'n')
            ->fields('n', array('id_curier','nume_curier'))
            ->execute()->fetchAllAssoc('id_curier');
		$options2 = array();
		foreach($result as $row) {
		$options2[$row->id_curier] = $row->nume_curier;
		}		
		
		
		//$form['service']['#default_value']='Alege service';
		$default = 'Alege service';
		$form['service'] = [
			'#type' => 'select',
			'#prefix'=>'<tr><td  style="width: 50%;">',
            '#suffix'=>'</td>',
			'#attributes' => array('class' => array('radio_service')),
			'#options' => $options2,  
			'#default_value' => $default,			
			'#title' => $this->t('Alege curier'),
		
			
		];
		
		$form['AWB'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr></table>',
			//'#default_value'=>$data[0]->AWB,
			'#title' => $this->t('Numar AWB'),
		
		];
		
		
		
		$form['numar_aviz'] = [
			'#type' => 'hidden',
			'#attributes' => array('class' => array('numar_aviz')),
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr>',
			//'#default_value'=>$data[0]->RMA,
			'#title' => $this->t('Numar aviz'),
			//'#access' => FALSE
		];
		
		$query = \Drupal::database();
		$result = \Drupal::database()->select('dashboard_furnizori', 'n')
            ->fields('n', array('id_furnizor','nume_furnizor'))
            ->execute()->fetchAllAssoc('id_furnizor');
		$options2 = array();
		foreach($result as $row) {
		$options2[$row->id_furnizor] = $row->nume_furnizor;
		}		
		
		
		//$form['service']['#default_value']='Alege service';
		$default = '';
		$form['nume_furnizor'] = [
			'#type' => 'hidden',
			'#attributes' => array('class' => array('nume_furnizor')),
			'#prefix'=>'<tr><td  style="width: 50%;">',
            '#suffix'=>'</td>',
			//'#attributes' => array('class' => array('radio_service')),
			'#options' => $options2,  
			'#default_value' => $default,			
			'#title' => $this->t('Alege furnizor'),
			//'#access' => FALSE
			'#validated' => TRUE,
		];
		
		$form['nume_reprezentant'] = [
			'#type' => 'hidden',
			'#attributes' => array('class' => array('nume_reprezentant')),
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr></table>',
			//'#default_value'=>$data[0]->AWB,
			'#title' => $this->t('Nume reprezentant'),
			//'#access' => FALSE
		];
		
	$form['markup6'] = [
			'#markup' => '<h5>Istoric produs actual</h5><hr />',
			'#title' => $this->t('Istoric produs actual'),
		
		];
		
		$form['istoric_produs'] = [
			'#type' => 'textfield',
			'#prefix'=>'<table><tr><td>',
            '#suffix'=>'</td></tr></table>',
			//'#default_value'=>$data[0]->AWB,
			'#title' => $this->t('Adaugati Istoric produs'),
			
		];
		
		$form['actions']['#type'] = 'actions';
		$form['actions']['submit'] = [
			'#type' => 'submit',
			'#value' => $this->t('Salveaza'),
			'#button_type' => 'primary',
		];
		
		$form['final'] = [
      '#type' => 'submit',
	  '#attributes' => array('class' => array('finalizare')),
      '#value' => $this->t('Finalizare proces'),
      '#submit' => [[$this, 'submitPass']],

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
  
    public function submitPass(array & $form, FormStateInterface $form_state) {
     
		/*$conn = Database::getConnection();
		$id_proces = \Drupal:: routeMatch()->getParameter('id_proces');
		$fields['finalizat']= '1';
		$conn->update('dashboard_procese_verbale')
			   ->fields($fields)->execute();*/
		$conn = Database::getConnection();
		$id_proces = \Drupal:: routeMatch()->getParameter('id_proces');
		$field = $form_state->getValues();
		//print_r($field['id_proces']);exit;
		$response = new \Symfony\Component\HttpFoundation\RedirectResponse('../finalizare_proces/'.$field['id_proces'].'');
	$response->send();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array & $form, FormStateInterface $form_state) {
	try{//var_dump('tesssssss');
		//die;
		$conn = Database::getConnection();
		$id_proces = \Drupal:: routeMatch()->getParameter('id_proces');
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
		//$fields["tip_produs"] = $field['tip_produs'];
		
		//$fields["service"] = $field['service'];
		//$fields["semnatura"] = $field['semnatura'];
		
		$fields1["nume_curier"] = $field['service'];
		$fields1["RMA"] = $field['RMA'];
		$fields1["AWB"] = $field['AWB'];
		$fields1["id_proces"] = $id_proces;
		$fields1["detalii_istoric"] = $field['istoric_produs'];
		
		$fields1["nume_furnizor"] = $field['nume_furnizor'];
		$fields1["nume_reprezentant"] = $field['nume_reprezentant'];
		$fields1["numar_aviz"] = $field['numar_aviz'];
		
		
		$current_user = \Drupal::currentUser();
		$current_time = new DrupalDateTime('now', 'UTC');
		$userTimezone = new \DateTimeZone($current_user->getTimeZone());
		$date_time = new DrupalDateTime();
		$timezone_offset = $userTimezone->getOffset($date_time->getPhpDateTime());
		$time_interval = \DateInterval::createFromDateString($timezone_offset . 'seconds');
		$current_time->add($time_interval);
		$result = $current_time->format('Y-m-d h:i:s');

		$fields1["data_istoric"] = $result;
		
		$conn->insert('dashboard_istoric_produs')
			   ->fields($fields1)->execute();
			   
		$conn->update('dashboard_procese_verbale')
			   ->fields($fields)->execute();
			   
		\Drupal::messenger()->addMessage($this->t('Datele au fost salvate'));
		drupal_flush_all_caches();
	} catch(Exception $ex){
		\Drupal::logger('test_db')->error($ex->getMessage());
	}
    $response = new \Symfony\Component\HttpFoundation\RedirectResponse('../');
	$response->send();
  }

}