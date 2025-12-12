<?php


class HTStatus {

   public $logged_in = false;
   public $institution_code = "Guest";
   public $auth_type  = "NONE";
   public $display_name = "Guest";
   public $affiliation = "Unaffiliated";
   public $r = "";
   public $u = "";
   public $emergency_access = false;
   public $provider_name = "Unauthenticated";
   public $mapped_institution_code = null;
   public $has_activated_role = false;


   function __construct() {
     if (isset($_COOKIE['HTstatus'])) {
       $c = json_decode($_COOKIE['HTstatus'], true);
       $this->institution_code = $c['institution_code'];
       $this->auth_type = $c['auth_type'];
       $this->display_name = $c['displayName'];
       $this->affiliation = $c['affiliation'];
       $this->u = $c['u'];
			 $this->r = $c['r'];
       $this->emergency_access = $this->determine_emergency_access($c);
       $this->has_activated_role = $this->determine_activated_role($c);
       $this->provider_name = $c['providerName'];
       if (isset($c['mapped_institution_code'])) {
         $this->mapped_institution_code = $c['mapped_institution_code'];
       } else {
         $this->mapped_institution_code = $this->institution_code;
       }
     }
   }

   function determine_emergency_access($c) {
     return (isset($c['x']) && $c['x'] == 1);
   }

   function determine_activated_role($c) {
     return isset($c['u']) ? $c['u'] : FALSE;
   }
}


?>
