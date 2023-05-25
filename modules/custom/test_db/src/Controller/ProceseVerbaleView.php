<?php


namespace Drupal\test_db\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use \Drupal\Core\Routing\TrustedRedirectResponse;


class ProceseVerbaleView extends ControllerBase {

  /**
   * showdata.
   *
   * @return string
   *   Return Table format data.
   */
  public function showdata() {

    $result = \Drupal::database()->select('dashboard_procese_verbale', 'n')
            ->fields('n', array('id_proces','data_reclamatie','nume_client', 'descriere', 'brand', 'model', 'serie','defect','service','data_trimitere_service','finalizat'))
			->orderBy('id_proces', 'DESC')
            ->execute()->fetchAllAssoc('id_proces');
    $data=array();
	foreach ($result as $row){
		//print_r($row); exit;
		$id_service = $row->service;
		
		$query = \Drupal::database();
		$service = $query->select('dashboard_service','e')
					  ->fields('e',['id_service','nume_service'])
					  ->condition('e.id_service',$id_service,'=')
					 // orderBy('id_service', 'DESC'),
					  ->execute()->fetchAll(\PDO::FETCH_OBJ);
		
		if($row->finalizat){
		$optiuni = 	t("<a class='buton_modif_albastru' href='test_db/view_proces/$row->id_proces'>Vizualizeaza</a>");
		$class='finalizat';
		}
		else {
			$optiuni = 	t("<a class='buton_modif_albastru'  href='test_db/view_proces/$row->id_proces'>Vizualizeaza</a><br><a class='buton_modif_albastru' href='test_db/edit_proces/$row->id_proces'>   Modifica   </a>");
			$class='nefinalizat';
		}
		
		
		$data[]= 
		
		[
		
		    'data' => ['id_proces' => $row->id_proces,
			'data_reclamatie' => $row->data_reclamatie,
			'nume_client' => $row->nume_client,
			'descriere' => $row->descriere." ".$row->brand." ".$row->model." ".$row->serie,
			//'brand' => $row->brand,
			//'model' => $row->model,
			//'serie' => $row->serie,
			'defect' => $row->defect,
			'service' => $service[0]->nume_service,
			'data_trimitere_service' => $row->data_trimitere_service,
			'Edit' => $optiuni],
			'class' => [$class],
			
			//'#attributes' => array ('class' => array($class),)
		];
			
			
		//$data['#attributes']['class'][] = $class;
		
	}	
	//print_r($data);print_r('<br><br>');
	 //exit;
// Create the header.
    $header = array('Numar','Data reclamatiei', 'Nume client', 'Produs defect','Problema reclamata','Unitate service','Data trimiterii','Optiuni');
    $output = array(
      '#theme' => 'table',    // Here you can write #type also instead of #theme.
      '#header' => $header,
      '#rows' => $data
	  
	  
    );
    return $output;
  }
  
  public function deleteService($id_proces) {
	  $query = \Drupal::database();
	  $query->delete('dashboard_procese_verbale')
			->condition('id_proces',$id_proces,'=')
			->execute();
			
	  drupal_flush_all_caches();
	  $response = new \Symfony\Component\HttpFoundation\RedirectResponse('..');
	  $response->send();
	  
	  drupal_set_message(t('Datele au fost sterse!'),'error',TRUE);
 }
  
}

