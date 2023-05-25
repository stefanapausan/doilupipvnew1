<?php


namespace Drupal\test_db\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use \Drupal\Core\Routing\TrustedRedirectResponse;


class AspectView extends ControllerBase {

  /**
   * showdata.
   *
   * @return string
   *   Return Table format data.
   */
  public function showdata() {

    $result = \Drupal::database()->select('dashboard_aspect', 'n')
            ->fields('n', array('id_aspect','aspect_estetic'))
            ->execute()->fetchAllAssoc('id_aspect');
    $data=array();
	foreach ($result as $row){
		$data[]= [
			'aspect_estetic' => $row->aspect_estetic,
			'Edit' => t("<a class='buton_modif_albastru' href='edit_aspect/$row->id_aspect'>Modifica</a><br><br><a class='buton_modif_rosu' href='delete_aspect/$row->id_aspect'>Sterge</a>")
			//'Delete' => t("<a href='delete_aspect/$row->id_aspect'>Sterge</a>")
			];
	}	
	
// Create the header.
    $header = array('Denumire aspect','Optiuni');
    $output = array(
      '#theme' => 'table',    // Here you can write #type also instead of #theme.
      '#header' => $header,
      '#rows' => $data
	  
	  
    );
    return $output;
  }
  
  public function deleteAspect($id_aspect) {
	  $query = \Drupal::database();
	  $query->delete('dashboard_aspect')
			->condition('id_aspect',$id_aspect,'=')
			->execute();
			
	  drupal_flush_all_caches();
	  $response = new \Symfony\Component\HttpFoundation\RedirectResponse('../lista_aspect');
	  $response->send();
	  
	  drupal_set_message(t('Datele au fost sterse!'),'error',TRUE);
 }
  
}