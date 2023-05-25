<?php


namespace Drupal\test_db\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use \Drupal\Core\Routing\TrustedRedirectResponse;


class ServiceView extends ControllerBase {

  /**
   * showdata.
   *
   * @return string
   *   Return Table format data.
   */
  public function showdata() {

    $result = \Drupal::database()->select('dashboard_service', 'n')
            ->fields('n', array('id_service','nume_service', 'adresa_service', 'telefon_service'))
            ->execute()->fetchAllAssoc('id_service');
    $data=array();
	foreach ($result as $row){
		$data[]= [
			'nume_service' => $row->nume_service,
			'adresa_service' => $row->adresa_service,
			'telefon_service' => $row->telefon_service,
			'Edit' => t("<a   class='buton_modif_albastru' href='edit_service/$row->id_service'>Modifica</a><br><br><a  class='buton_modif_rosu' href='delete_service/$row->id_service'>Sterge</a>")
			//'Delete' => t("<a href='delete_service/$row->id_service'>Sterge</a>")
			];
	}	
	
// Create the header.
    $header = array('Denumire service', 'Adresa service', 'Telefon service','Optiuni');
    $output = array(
      '#theme' => 'table',    // Here you can write #type also instead of #theme.
      '#header' => $header,
      '#rows' => $data
	  
	  
    );
    return $output;
  }
  
  public function deleteService($id_service) {
	  $query = \Drupal::database();
	  $query->delete('dashboard_service')
			->condition('id_service',$id_service,'=')
			->execute();
			
	  drupal_flush_all_caches();
	  $response = new \Symfony\Component\HttpFoundation\RedirectResponse('../lista_service');
	  $response->send();
	  
	  drupal_set_message(t('Datele au fost sterse!'),'error',TRUE);
 }
  
}