<?php


namespace Drupal\test_db\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use \Drupal\Core\Routing\TrustedRedirectResponse;


class FurnizoriView extends ControllerBase {

  /**
   * showdata.
   *
   * @return string
   *   Return Table format data.
   */
  public function showdata() {

    $result = \Drupal::database()->select('dashboard_furnizori', 'n')
            ->fields('n', array('id_furnizor','nume_furnizor',  'telefon_furnizor'))
            ->execute()->fetchAllAssoc('id_furnizor');
    $data=array();
	foreach ($result as $row){
		$data[]= [
			'nume_furnizor' => $row->nume_furnizor,
			'telefon_furnizor' => $row->telefon_furnizor,
			'Edit' => t("<a  class='buton_modif_albastru'  href='edit_furnizor/$row->id_furnizor'>Modifica</a><br><br><a  class='buton_modif_rosu' href='delete_furnizor/$row->id_furnizor'>Sterge</a>")
			//'Delete' => t("<a href='delete_furnizor/$row->id_furnizor'>Sterge</a>")
			];
	}	
	
// Create the header.
    $header = array('Denumire furnizor',  'Telefon furnizor','Optiuni');
    $output = array(
      '#theme' => 'table',    // Here you can write #type also instead of #theme.
      '#header' => $header,
      '#rows' => $data
	  
	  
    );
    return $output;
  }
  
  public function deleteFurnizor($id_furnizor) {
	  $query = \Drupal::database();
	  $query->delete('dashboard_furnizori')
			->condition('id_furnizor',$id_furnizor,'=')
			->execute();
			
	  drupal_flush_all_caches();
	  $response = new \Symfony\Component\HttpFoundation\RedirectResponse('../lista_furnizori');
	  $response->send();
	  
	  drupal_set_message(t('Datele au fost sterse!'),'error',TRUE);
 }
  
}