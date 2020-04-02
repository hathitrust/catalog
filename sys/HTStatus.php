<?php


class HTStatus {

   public $logged_in = false;
   public $institution_code = "Guest";
   public $auth_type  = "NONE";
   public $display_name = "Guest";
   public $affiliation = "Unaffiliated";
   public $institution_name = "Unaffiliated";
   public $u = "";
   public $emergency_access = false;
   public $provider_name = "Unauthenticated";
   public $mapped_institution_code = null;


   function __construct() {
     if (isset($_COOKIE['HTstatus'])) {
       $c = json_decode($_COOKIE['HTstatus'], true);
       $this->institution_code = $c['institution_code'];
       $this->auth_type = $c['auth_type'];
       $this->display_name = $c['displayName'];
       $this->affiliation = $c['affiliation'];
       $this->insitution_name = $c['institution_name'];
       $this->u = $c['u'];
       if (isset($c['x']) && $c['x'] == 1) {
         $this->emergency_access = true;
       }
       $this->provider_name = $c['providerName'];
       if (isset($c['mappedInstitutionCode'])) {
         $this->mapped_institution_code = $c['mapped_institution_code'];
       } else {
         $this->mapped_institution_code = $this->institution_code;
       }
     }
   }

   function fakefill($instcode) {
     $this->institution_code = $instcode;
     $this->auth_type = "Fake/debug";
     $this->affiliation = "Fake/member";
     $this->instituion_name = "$instcode (fake)";
     $this->emergency_access = true;
     $this->provider_name = "Debug";
   }

   function fakefill_mapped($mapped_code) {
     $this->fakefill($mapped_code);
     $this->institution_code = "INVALID";
     $this->mapped_institution_code = $mapped_code;
   }

}


?>
