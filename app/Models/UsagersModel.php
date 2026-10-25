<?php

namespace App\Models;

/**
 * Port mecanique de Usagers_model (CI3) — requetes SQL et
 * HTML generes identiques. Base Ci3Model fournit db/input/
 * session/data/objData. A relire : Query Builder, upload, email.
 */



/**
 * @name: Login model
 * @author: Serge Dallaire
 */
class UsagersModel extends Ci3Model
{
  /** @var array $data */
	public $data = array();
    function __construct() {
        parent::__construct();
        
				
    }

    public function validate_user($data) {
        return (new \App\Libraries\WebuserAuth())->verify(
            (string) ($data['email'] ?? ''),
            (string) ($data['password'] ?? '')
        );
    }
    public function getPrivilege($privilege = '')
    {
      /** @var string $returnval */
      $returnval = '';
      /** @var string $strSQL */
      $strSQL = "SELECT idprivileges, description FROM `privileges` ORDER BY description";
      $camions = $this->db->query($strSQL);
      foreach($camions->getResult() as $row)
      {
        $selected = (($privilege == $row->idprivileges)? ' selected ' : '');
        $returnval .= '<option value="'.$row->idprivileges.'" '.$selected.'>'.$row->description.'</option>';
      }
      return $returnval;
    }
    public function getUsager($id)
    {
			$id = $id;
      /** @var object $utilisateurs */
      $utilisateurs = new \stdClass();
      $id = $this->db->escape($id);
      /** @var object $utilisateurs */
      $utilisateurs = new \stdClass();
      if ($utilisateurs =  $this->db->query("Select * From webuser where id_user=$id Order By nom")->getRow(0))
      {
				$this->data['nommodif'] = $utilisateurs->nom;
				$this->data['prenommodif'] = $utilisateurs->prenom;
				$this->data['emailmodif'] = $utilisateurs->email;
				$this->data['id'] = $utilisateurs->id_user;
        $this->data['result'] = 'OK';
        $this->data['privilege'] = $this->getPrivilege($utilisateurs->privilege);
      }
			else
			{
				$this->data['nommodif'] = '';
				$this->data['prenommodif'] = '';
				$this->data['emailmodif'] = '';
				$this->data['id'] = '';
        $this->data['result'] = 'NON';
			}
			return $this->data;
    }
    public function getAllUsagers()
    {
      $this->getInfoUsagers();
      return $this->data;
    }
    private function getInfoUsagers()
    {
      /** @var string $returnval */
      $returnval = '' ;
      /** @var object $utilisateurs */
      $utilisateurs = new \stdClass();
      $utilisateurs =  $this->db->query('Select webuser.id_user, webuser.nom, webuser.prenom, webuser.email, `privileges`.description From webuser INNER JOIN `privileges` ON `privileges`.idprivileges = webuser.privilege   Order By nom');
      /** @var object $row */
      foreach ($utilisateurs->getResult() as $row)
      {
        $returnval .= '<tr>
                          <td>'.$row->nom.'</td>
                          <td>'.$row->prenom.'</td>
                          <td>'.$row->email.'</td>
                          <td>'.$row->description.'</td>
                          <td class="supprime"><button data-id="'.$row->id_user.'" type="button" onclick="ShowUsager('.$row->id_user.')"
		                      class="btn btn-default btn-sm btnedit" aria-label="Edit" title="Éditer l\'usager" style="outline:none;">
		                      <span class="glyphicon glyphicon-edit" aria-hidden="true"></span></button></td>
	                      </tr>
                          ';
      }
      $this->data['nbrecord'] = $this->db->count_all_results('webuser');
      $this->data['tbody'] = $returnval ;
    }
    function __destruct() {
        $this->db->close();
    }
}
