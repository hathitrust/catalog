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
   public $is_NFB = false;
   public $has_activated_role = false;


   function __construct() {
     if (isset($_COOKIE['HTstatus'])) {
       $c = json_decode($_COOKIE['HTstatus'], true);
       $this->institution_code = $c['institution_code'];
       $this->auth_type = $c['auth_type'];
       $this->display_name = $c['displayName'];
       $this->affiliation = $c['affiliation'];
       $this->insitution_name = $c['institution_name'];
       $this->u = $c['u'];
       $this->emergency_access = $this->determine_emergency_access($c);
       $this->is_NFB = $this->determine_NFB($c);
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

   function determine_NFB($c) {
     if (isset($_REQUEST['nfb']) && $_REQUEST['nfb'] == "true") {
       return true;
     }
     $special_access = $c['r'];
     if (!is_array($special_access)) {
       return false;
     }

     return (isset($special_access['enhancedTextUser']) && $special_access['enhancedTextUser'] == 1) ||
            (isset($special_access['enhancedTextProxy']) && $special_access['enhancedTextProxy'] == 1);
   }

   function determine_activated_role($c) {
     error_log("AHOY HAS ACTIVATED ROLE " . $c['u']);
     return isset($c['u']) ? $c['u'] : FALSE;
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
