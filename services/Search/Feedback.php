<?php

require_once 'Action.php';

require_once 'Mail.php';
require_once 'Mail/RFC822.php';

class Feedback extends Action
{
    function launch(){
        global $interface;
        global $configArray;
        
        // message required
        // no other fields are required
        // incoming params:
        //   useragent
        //   device
        //   browser
        //   from
        //   allowresponse
        //   message        
        
        // todo ... truncate/error check field length?
        
        
        $refillvalues=false;
        
        if(isset($_POST['message'])){
			// it's a post... validate that message is filled
			if(strlen($_POST['message'])==0){
				$interface->assign('errormessage', "Message is required");
				$refillvalues=true;
			}else{
				$body="label:mobile\n\n";
				// compose and send...	
				$body.="Message:\n";
				$body.=$_POST['message'];
				
				$body.="\n\nFrom: ";
				if(isset($_POST['from'])){
					$body.=$_POST['from'];
				}

				$body.="\n\nURL: ";
				if(isset($_POST['currenturl'])){
					$body.=$_POST['currenturl'];
				}
				
				$body.="\n\nUser Agent: ";
				if(isset($_POST['useragent'])){
					$body.=$_POST['useragent'];
				}
				
				$to=$configArray['Site']['feedback_email'];
				$from=$to;
				
				// send...
				$result = $this->sendEmail($to, $from, "HathiTrust Mobile Feedback", $body);
            	if (!PEAR::isError($result)) {
            		// success... go to search page?
                	//require_once 'Home.php';
	                //$searchhome=&new Home;
	                //$searchhome->launch();
                	header("Location: " . $configArray['Site']['url']);
            		exit();
            	} else {
            		// failure...
                	$interface->assign('errormessage', $result->getMessage());
					$refillvalues=true;
            	}
			}
        }

        if($refillvalues){
        	if(isset($_POST['device'])){
				$interface->assign('device', $_POST['device']);
			}
            if(isset($_POST['browser'])){
				$interface->assign('browser', $_POST['browser']);
			}
       	 	if(isset($_POST['from'])){
				$interface->assign('from', $_POST['from']);
			}
			if(isset($_POST['allowresponse'])){
				$interface->assign('allowresponse', $_POST['allowresponse']);
			}
        	if(isset($_POST['message'])){
				$interface->assign('message', $_POST['message']);
			}    	
        }
        
        $interface->setPageTitle('Mobile Mirlyn Feedback');
        $interface->setTemplate('feedback.tpl');        
        $interface->display('layout.tpl');
    }
    
    function sendEmail($to, $from, $subject, $message)
    {
        global $configArray;
        
        if (!Mail_RFC822::isValidInetAddress($to)) {
            return new PEAR_Error('Invalid Email Address');
        }
                                   
        $headers['From']    = $from;
        $headers['To']      = $to;
        $headers['Subject'] = $subject;

        $mail =& Mail::factory('sendmail', array('host' => $configArray['Mail']['host'],
                                             'port' => $configArray['Mail']['port']));
        
        return $mail->send($to, $headers, $message);
    }    
}

?>