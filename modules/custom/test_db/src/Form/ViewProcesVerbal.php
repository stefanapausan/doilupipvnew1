<?php

namespace Drupal\test_db\Form;


use Dompdf\Dompdf;
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
class ViewProcesVerbal extends FormBase {


  public function getFormId() {
    return 'view_proces';
  }

  
  	public function buildForm(array $form, FormStateInterface $form_state){
		
		$id_proces = \Drupal:: routeMatch()->getParameter('id_proces');
		
		$query = \Drupal::database();
		$data = $query->select('dashboard_procese_verbale','e')
					  ->fields('e')
					  ->condition('e.id_proces',$id_proces,'=')
					  ->execute()->fetchAll(\PDO::FETCH_OBJ);
					  
	
		
		$form['id_proces'] = [
			'#type' => 'textfield',
			'#prefix'=>'<h5 class="titlu">Proces verbal de predare-primire produs defect <br>
(in vederea trimiterii in service)</h5><hr /><table><tr><td class="numar_titlu">',
            '#suffix'=>'</td>',
			'#default_value'=>$data[0]->id_proces,
			'#required' => TRUE,
			'#title' => $this->t('Numar'),
			'#attributes' => array('disabled' => 'disabled'),
		];
		
		$form['data_reclamatie'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td class="data_titlu">',
            '#suffix'=>'</td></tr></table>',
			'#default_value'=>$data[0]->data_reclamatie,
			'#title' => $this->t('Din data'),
			'#required' => TRUE,
			'#attributes' => array('disabled' => 'disabled'),
		];
		
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
			'#attributes' => array('disabled' => 'disabled'),
		];
		
		$form['telefon_client'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr>',
			'#default_value'=>$data[0]->telefon_client,
			'#title' => $this->t('Telefon client'),
			'#required' => TRUE,
			'#attributes' => array('disabled' => 'disabled'),
		];
		
		
		
		$form['adresa_client'] = [
			'#type' => 'textfield',
			'#prefix'=>'<tr><td>',
            '#suffix'=>'</td>',
			'#default_value'=>$data[0]->adresa_client,
			'#title' => $this->t('Adresa client'),
			'#required' => TRUE,
			'#attributes' => array('disabled' => 'disabled'),
			
		];
		
		$form['email_client'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
			'#default_value'=>$data[0]->email_client,
            '#suffix'=>'</td></tr></table>',
			'#title' => $this->t('Email client'),
			'#attributes' => array('disabled' => 'disabled'),
			
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
			'#attributes' => array('disabled' => 'disabled'),
		];
		
		$form['brand'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr>',
			'#default_value'=>$data[0]->brand,
			'#title' => $this->t('Brand'),
			'#required' => TRUE,
			'#attributes' => array('disabled' => 'disabled'),
		];
		
		
		
		$form['model'] = [
			'#type' => 'textfield',
			'#prefix'=>'<tr><td>',
            '#suffix'=>'</td>',
			'#default_value'=>$data[0]->model,
			'#title' => $this->t('Model produs'),
			'#required' => TRUE,
			'#attributes' => array('disabled' => 'disabled'),
			
		];
		
		$form['serie'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr>',
			'#default_value'=>$data[0]->serie,
			'#title' => $this->t('Serie'),
			'#required' => TRUE,
			'#attributes' => array('disabled' => 'disabled'),
			
		];
		
		$form['IMEI1'] = [
			'#type' => 'textfield',
			'#prefix'=>'<tr><td>',
            '#suffix'=>'</td>',
			'#default_value'=>$data[0]->IMEI1,
			'#title' => $this->t('IMEI1'),
			'#attributes' => array('disabled' => 'disabled'),
			
			
		];
		
		$form['IMEI2'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr>',
			'#default_value'=>$data[0]->IMEI2,
			'#title' => $this->t('IMEI2'),
			'#attributes' => array('disabled' => 'disabled'),
			
		];
		
		$form['nr_garantie'] = [
			'#type' => 'textfield',
			'#prefix'=>'<tr><td>',
            '#suffix'=>'</td>',
			'#default_value'=>$data[0]->nr_garantie,
			'#title' => $this->t('Numar certificat garantie'),
			'#attributes' => array('disabled' => 'disabled'),
			
		];
		
		$form['nr_factura'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr>',
			'#default_value'=>$data[0]->nr_factura,
			'#title' => $this->t('Numar factura'),
			'#required' => TRUE,
			'#attributes' => array('disabled' => 'disabled'),
			
		];
		
		$form['accesorii'] = [
			'#type' => 'textfield',
			'#prefix'=>'<tr><td>',
            '#suffix'=>'</td>',
			'#default_value'=>$data[0]->accesorii,
			'#title' => $this->t('Accesorii'),
			'#required' => TRUE,
			'#attributes' => array('disabled' => 'disabled'),
			
			
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
			'#attributes' => array('disabled' => 'disabled'),
			
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
			'#attributes' => array('disabled' => 'disabled'),
			
			
		];
		
		
		
		$form['markup4'] = [
			'#markup' => '<h5>Date service</h5><hr />',
			'#title' => $this->t('Date service'),
		
		];
		
		$query = \Drupal::database();
		$data = $query->select('dashboard_service','s')
					  ->fields('s')
					  ->condition('s.id_service',$data[0]->service,'=')
					  ->execute()->fetchAll(\PDO::FETCH_OBJ);
		
			$form['nume_service'] = [
			'#type' => 'textfield',
			'#prefix'=>'<table><tr><td>',
            '#suffix'=>'</td>',
			'#default_value'=>$data[0]->nume_service,
			'#title' => $this->t('Denumire service'),
			'#required' => TRUE,
			'#attributes' => array('disabled' => 'disabled'),
		];
		
		$form['telefon_service'] = [
			'#type' => 'textfield',
			'#prefix'=>'<td>',
            '#suffix'=>'</td></tr>',
			'#default_value'=>$data[0]->telefon_service,
			'#title' => $this->t('Telefon service'),
			'#required' => TRUE,
			'#attributes' => array('disabled' => 'disabled'),
		];
		
		
		
		$form['adresa_service'] = [
			'#type' => 'textfield',
			'#prefix'=>'<tr><td>',
            '#suffix'=>'</td></tr></table>',
			'#default_value'=>$data[0]->adresa_service,
			'#title' => $this->t('Adresa service'),
			'#required' => TRUE,
			'#attributes' => array('disabled' => 'disabled'),
			
		];
		
	
		
	    $form['markup5'] = [
			'#markup' => '<h5>Alte date</h5><hr />',
			'#title' => $this->t('Alte date'),
		
		];
		
		
		
		$form['actions']['#type'] = 'actions';
		$form['actions']['submit'] = [
			'#type' => 'submit',
			'#value' => $this->t('Genereaza pdf'),
			'#button_type' => 'primary',
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

  /**
   * {@inheritdoc}
   */
  public function submitForm(array & $form, FormStateInterface $form_state) {
	
	$id_proces = \Drupal:: routeMatch()->getParameter('id_proces');
		
		$query = \Drupal::database();
		$data = $query->select('dashboard_procese_verbale','e')
					  ->fields('e')
					  ->condition('e.id_proces',$id_proces,'=')
					  ->execute()->fetchAll(\PDO::FETCH_OBJ);
					  
		$data1 = $query->select('dashboard_service','s')
					  ->fields('s')
					  ->condition('s.id_service',$data[0]->service,'=')
					  ->execute()->fetchAll(\PDO::FETCH_OBJ);
	
	$date=substr($data[0]->data_reclamatie, 0, 10); 
	
  $html=' <div class="layout-container">
      
        <div class="region region-header">
    <div id="block-header" class="block block-block-content block-block-content373844b8-693e-45b8-a75e-6f92213dea42">  
            <div class="clearfix text-formatted field field--name-body field--type-text-with-summary field--label-hidden field__item"><img src="http://localhost/drupal_test/sites/default/files/inline-images/service%20magazin%20mic.png" width="400" height="90"  />
			<div style="float:right;display: inline-block">S.C. Doi Lupi Prest S.R.L.<br> CUI: RO8060318 | Nr. Reg. Com: J05/72/1996<br> Str. Ioan Ciordas Nr. 9, Beius, Bihor, 415200<br> 0359 413131 / 0359 413135<br>informatii@doilupi.ro www.doilupi.ro</div>
            </div></div><hr style="color:black">
			
		<h4 style="text-align:center;margin-bottom:-20px">Fisa nr. '.$data[0]->id_proces.' din '.$date.'</h4>
		
		<h4 style="margin-bottom:-2px;margin-left: 5px;">Date client</h4>
		<table>
		<th "><td style="width:240px;border: 1px solid;">Nume client<br>'.$data[0]->nume_client.'</td><td style="width:240px;border: 1px solid;">Adresa<br>'.$data[0]->adresa_client.'</td><td style="width:240px;border: 1px solid;">Telefon client<br>'.$data[0]->telefon_client.'</td></th>
		</table>
		
		<h4 style="margin-bottom:-2px;margin-left: 5px;">Detalii produs</h4>
		<table>
		<th><td style="width:240px;border: 1px solid;">Denumire produs<br>'.$data[0]->descriere.'</td><td style="width:240px;border: 1px solid;">Serie produs<br>'.$data[0]->serie.'</td><td style="width:240px;border: 1px solid;">Nr. certificat garantie<br>'.$data[0]->nr_garantie.'</td></th>
		</table><table><th><td style="width:240px;border: 1px solid;">Numar factura<br>'.$data[0]->nr_factura.'</td></th>
		</table>
		
		<h4 style="margin-bottom:-2px;margin-left: 5px;">Detalii produs</h4>
		<table><th><td style="width:730px;height: 30px;border: 1px solid;">'.$data[0]->accesorii.'</td></th>
		</table>
		
		<h4 style="margin-bottom:-2px;margin-left: 5px;">Stare produs receptionat</h4>
		<table><th><td style="width:730px;height: 30px;border: 1px solid;">'.$data[0]->aspect.'</td></th>
		</table>
		
		<h4 style="margin-bottom:-2px;margin-left: 5px;">Defect reclamat</h4>
		<table><th><td style="width:730px;height: 30px;border: 1px solid;">'.$data[0]->defect.'</td></th>
		</table>
		
		<h4 style="margin-bottom:-2px;margin-left: 5px;">Date service</h4>
		<table>
		<th "><td style="width:240px;border: 1px solid;">Nume service<br>'.$data1[0]->nume_service.'</td><td style="width:240px;border: 1px solid;">Adresa service<br>'.$data[0]->adresa_service.'</td><td style="width:240px;border: 1px solid;">Telefon service<br>'.$data[0]->telefon_service.'</td></th>
		</table>
		
		<h4 style="margin-bottom:-2px;margin-left: 5px;">Istoric produs</h4>
		<table><th><td style="width:730px;height: 30px;border: 1px solid;">'.$data[0]->data_reclamatie.' - Produs primit de la clientul '.$data[0]->nume_client.' - proces verbal nr. '.$data[0]->id_proces.'<br><b>Semnatura client</b></td></th>
		</table>
		
		<h4 style="margin-bottom:-2px;margin-left: 5px;">Finalizare fisa</h4>
		<table>
		<th "><td style="width:240px;border: 1px solid;">Ridicat de client:<br>'.$data[0]->nume_client_ridicare.'</td><td style="width:240px;border: 1px solid;">La data de:<br>'.$data[0]->data_ridicare.'</td><td style="width:240px;border: 1px solid;">Avand semnatura<br></td></th>
		</table>
			';
			
  //print_r($html);exit;
 
  $dompdf = new Dompdf();
   $options = $dompdf->getOptions(); 
    $options->set(array('isHtml5ParserEnabled' => true));
	$options->set(array('isRemoteEnabled' => true));
    $dompdf->setOptions($options);
  $dompdf->load_html($html);
  $dompdf->render();
  $dompdf->stream("ckl-sample.pdf", array("Attachment" => false));
  
  drupal_exit();
  
}

}