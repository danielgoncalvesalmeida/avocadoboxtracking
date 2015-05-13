<?php  if ( ! defined('BASEPATH')) exit('No direct script  access allowed');
/*
 *  Helper for some miscelleneous tasks or embedded CI functions
 * 
 * 
 */

/*
 *  Write to log file
 *  If type is provided, messages are grouped in category log file
 * 
 */
function addtolog($msg, $logtype = 0)
{
    if(empty($msg)){
        return FALSE;
    }

    // Write a log file per month
    $logsuffix = date('Y').'_'.date('m').'.txt';
    // Log path
    $logpath = 'logs/';

    switch ($logtype){
        // General log
        case 0:
            $logprefix = 'log_';
            break;
        // Paiement API etc
        case 1:
            $logprefix = 'log_paiement_';
            break;
        // Lists logins
        case 2:
            $logprefix = 'log_listlogin_';
            break;
    }

    // Write to file
    $filename = $logpath.$logprefix.$logsuffix;
    // Add date time to the msg

    if(is_array($msg)){
        $result = PHP_EOL.date('Y-m-d H:i:s').PHP_EOL;
        foreach ($msg as $line){
            $result = $result.$line.PHP_EOL;
        }
    } else {
        $result = date('Y-m-d H:i:s').PHP_EOL.$msg.PHP_EOL;
    }
    file_put_contents($filename, $result, FILE_APPEND);
    return TRUE;
}
 


/*
 *  Send email
 */
function sendMail($toEmail, $subject, $message = null, $mailtemplate = null, $templateData = array(), $attachfiles = null)
{
   $CI =& get_instance();
   // gestion de l'activation des mails
   if(!(bool)$CI->config->item('send_emails'))
       return false;

   $CI->load->library('email');

   // Re-init the email and clear attachements
   $CI->email->clear(TRUE);

   // Settings are defined in config_littlefox.php
   if ($CI->config->item('use_smtp')){
       $config['protocol'] = 'smtp';
       $config['smtp_host'] = $CI->config->item('smtp_host');
       $config['smtp_port'] = $CI->config->item('smtp_port');
       $config['smtp_user'] = $CI->config->item('smtp_user');
       $config['smtp_pass'] = $CI->config->item('smtp_pass');
       $config['smtp_timeout'] = '7';
   }
   $config['crlf'] = '\r\n';
   $config['wordwrap'] = FALSE;
   $config['newline'] = '\r\n';
   $config['mailtype'] = 'html';
   $config['charset']  = 'utf-8';//iso-8859-1

   $CI->email->initialize($config);

   // preferences
   $CI->email->from($CI->config->item('from_email'), $CI->config->item('from_name'));
   $CI->email->reply_to($CI->config->item('from_email'), $CI->config->item('from_name'));
   $CI->email->to($toEmail);
   $CI->email->subject($subject);

   // Create mail body based on a template or simple message
   if(!empty($mailtemplate)){
       $mailbody = $CI->load->view('mail/'.$mailtemplate, $templateData, true);
   }else{
       $mailbody = $message;
   } 
   $mailcontent = $CI->load->view('layouts/mail', array('output' => $mailbody), true);        

   $CI->email->message($mailcontent);

   // Add attachments if requiered
   if($attachfiles){
       foreach($attachfiles as $value){
           $CI->email->attach($value);   
       }
   }


   // send mail
   if ($CI->email->send())
   {
       return true;
   }
   echo $CI->email->print_debugger(); 
   return false;
}

/*
 *  Return the given smiley as image
 */
function imgSmiley($id_smiley, $size = null, $class = null, $style = null)
{
    switch ($id_smiley) {
        case 100:
            $str = 'e_100_crying.png';
            break;
        case 101:
            $str = 'e_101_sad.png';
            break;
        case 102:
            $str = 'e_102_happy.png';
            break;
        case 103:
            $str = 'e_103_upset.png';
            break;
        case 104:
            $str = 'e_104_silent.png';
            break;
        case 105:
            $str = 'e_105_tired.png';
            break;
        default:
            return '';
            break;
    }
    $url = sbase_url().'assets/img/icons/'.$str;
    return '<img src="'.$url.'" '.(!empty($size)? 'width="'.$size.'"' : '').' '.(!empty($class)? 'class="'.$class.'"' : '' ).' '.(!empty($style)? 'style="'.$style.'"' : '' ).' >';
}

/*
 *  Return the link for the child picture
 *  Notice : The link will call the image controller
 */
function getProfileLink($id, $size = 1)
{
    $CI =& get_instance();
    $basedir = dirname(__FILE__).'/../../';
    $img_dir = $CI->config->item('imgdir_children_secure');
    
    switch ($size)
    {
        case '1':
            $s_size = 'mini';
            break;
        case '2':
            $s_size = 'medium';
            break;
        case '3':
            $s_size = 'large';
            break;
        default:
            $s_size = 'mini';
            break;
    }
    
    return sbase_url() . 'admin/img/child_'.$s_size.'/'.$id.'/profilepicture.jpg'; 
    
    /*
    if(file_exists($basedir . $img_dir.(int)$id.'_'.$s_size.'.jpg'))
        $url = $basedir . $img_dir.(int)$id.'_'.$s_size.'.jpg';
    else
        $url = $basedir . $img_dir.(int)$id.'_'.$s_size.'.jpg';
    $url = sbase_url() . 'admin/img/child/'.$id.'/profilepicture.jpg';
     * 
     */
}

/*
 *  Return the week day name
 */
function getWeekDayName($id)
{
    $week_days_name = array(
        '1' => 'lundi',
        '2' => 'mardi', 
        '3' => 'mercredi',
        '4' => 'jeudi',
        '5' => 'vendredi',
        '6' => 'samedi',
        '7' => 'dimanche'
    );
    
    if(isset($week_days_name[(int)$id]))
        return $week_days_name[(int)$id];
    else
        return false;
}

/*
*   Get the id_domain from the logged user
*/
function getUserDomain()
{
   $CI =& get_instance();
   return $CI->session->userdata('id_domain');
}
 
/*
* Get the id_user from the logged user
*/
function getUserId()
{
    if(isset($_SESSION['id_user']))
        return $_SESSION['id_user'];
    else
        return false;
        
        
   /*
    * discontinued
    * 
   $CI =& get_instance();
   return $CI->session->userdata('id_user');
    * 
    */
}

/*
* Check if user is domain admin
*/
function isDomainAdmin()
{
   $CI =& get_instance();
   return ($CI->session->userdata('is_domain_admin') == 1 ? true : false);
}
 
/*
 * Format name according to config
 * Firstname lastname or vice versa
 */
function displayUserName($firstname, $lastname, $short = false)
{
    $CI =& get_instance();
    $f = $CI->config->item('user_display_format');
    $s = $CI->config->item('user_display_format_short');
    if($f == 'firstname'){
       return trim($firstname.' '.$lastname);
    }

    if($f == 'lastname'){
       return trim($lastname.' '.$firstname);
    }

}
 
/*
 * Generate a Tag
 */
function genTag($str = null)
{
    if(empty($str))
        $str = '';

    $CI =& get_instance();
    return sha1(date('ymdhis').$CI->session->userdata['session_id'].$str);
}

/*
 *  Check if the user has the necessary right
 *  This function is a shortcut to the Myaaa library
 */
function _cr($rightName, $right = 'v')
{
    $CI =& get_instance();
    
    switch ($right) {
        case 'v':
            $_right = 'view';
            break;
        case 'c':
            $_right = 'create';
            break;
        case 'e':
            $_right = 'edit';
            break;
        case 'd':
            $_right = 'delete';
            break;
        default:
            $_right = '';
            break;
    }

    if( ((isset($CI->myaaa->rights[$rightName]))
        && (isset($CI->myaaa->rights[$rightName][$_right]))
        && $CI->myaaa->rights[$rightName][$_right] == true)
        ||
        isDomainAdmin()
    )
        return true;
    else
        return false;    
}

function warning_noaccess()
{
    $CI =& get_instance();
    $CI->display('warning-noaccess');
}

/*
 * Detect backlinks and options
 */

function getLinkBack($includeBaseUrl = true)
{
    $CI =& get_instance();
    
    // Detect back to groups view in desktop
    $backgroup = $CI->input->get('backgroup');
    if(!empty($backgroup))
    {
        $back = ($includeBaseUrl ? sbase_url().'admin/desktop/viewgroup/'.(int)$backgroup : 'admin/desktop/viewgroup/'.(int)$backgroup );
        $viewmode = $CI->input->get('viewmode');
        if(!empty($viewmode))
            $back .= '?viewmode='.(int)$viewmode;
        return $back;
    }
    return '';
}


/*
 * Make a string searchable
 */
function my_searchString($str)
{
    $str = my_prepSearch($str);
    $str = replaceAccentedChars($str);
    return $str;
}

/*
 * Remove spaces or any special caracters
 */
function my_prepSearch($str)
{
    return str_replace(array("-", " ", "_", "/", "\\"), '', $str);
}

/**
* Convert \n and \r\n and \r to <br />
*
* @param string $string String to transform
* @return string New string
*/
function my_nl2br($str)
{
   return str_replace(array("\r\n", "\r", "\n"), '<br />', $str);
}

function replaceAccentedChars($str)
{
    /* One source among others:
        http://www.tachyonsoft.com/uc0000.htm
        http://www.tachyonsoft.com/uc0001.htm
        http://www.tachyonsoft.com/uc0004.htm
    */
    $patterns = array(

        /* Lowercase */
        /* a  */ '/[\x{00E0}\x{00E1}\x{00E2}\x{00E3}\x{00E4}\x{00E5}\x{0101}\x{0103}\x{0105}\x{0430}]/u',
        /* b  */ '/[\x{0431}]/u',
        /* c  */ '/[\x{00E7}\x{0107}\x{0109}\x{010D}\x{0446}]/u',
        /* d  */ '/[\x{010F}\x{0111}\x{0434}]/u',
        /* e  */ '/[\x{00E8}\x{00E9}\x{00EA}\x{00EB}\x{0113}\x{0115}\x{0117}\x{0119}\x{011B}\x{0435}\x{044D}]/u',
        /* f  */ '/[\x{0444}]/u',
        /* g  */ '/[\x{011F}\x{0121}\x{0123}\x{0433}\x{0491}]/u',
        /* h  */ '/[\x{0125}\x{0127}]/u',
        /* i  */ '/[\x{00EC}\x{00ED}\x{00EE}\x{00EF}\x{0129}\x{012B}\x{012D}\x{012F}\x{0131}\x{0438}\x{0456}]/u',
        /* j  */ '/[\x{0135}\x{0439}]/u',
        /* k  */ '/[\x{0137}\x{0138}\x{043A}]/u',
        /* l  */ '/[\x{013A}\x{013C}\x{013E}\x{0140}\x{0142}\x{043B}]/u',
        /* m  */ '/[\x{043C}]/u',
        /* n  */ '/[\x{00F1}\x{0144}\x{0146}\x{0148}\x{0149}\x{014B}\x{043D}]/u',
        /* o  */ '/[\x{00F2}\x{00F3}\x{00F4}\x{00F5}\x{00F6}\x{00F8}\x{014D}\x{014F}\x{0151}\x{043E}]/u',
        /* p  */ '/[\x{043F}]/u',
        /* r  */ '/[\x{0155}\x{0157}\x{0159}\x{0440}]/u',
        /* s  */ '/[\x{015B}\x{015D}\x{015F}\x{0161}\x{0441}]/u',
        /* ss */ '/[\x{00DF}]/u',
        /* t  */ '/[\x{0163}\x{0165}\x{0167}\x{0442}]/u',
        /* u  */ '/[\x{00F9}\x{00FA}\x{00FB}\x{00FC}\x{0169}\x{016B}\x{016D}\x{016F}\x{0171}\x{0173}\x{0443}]/u',
        /* v  */ '/[\x{0432}]/u',
        /* w  */ '/[\x{0175}]/u',
        /* y  */ '/[\x{00FF}\x{0177}\x{00FD}\x{044B}]/u',
        /* z  */ '/[\x{017A}\x{017C}\x{017E}\x{0437}]/u',
        /* ae */ '/[\x{00E6}]/u',
        /* ch */ '/[\x{0447}]/u',
        /* kh */ '/[\x{0445}]/u',
        /* oe */ '/[\x{0153}]/u',
        /* sh */ '/[\x{0448}]/u',
        /* shh*/ '/[\x{0449}]/u',
        /* ya */ '/[\x{044F}]/u',
        /* ye */ '/[\x{0454}]/u',
        /* yi */ '/[\x{0457}]/u',
        /* yo */ '/[\x{0451}]/u',
        /* yu */ '/[\x{044E}]/u',
        /* zh */ '/[\x{0436}]/u',

        /* Uppercase */
        /* A  */ '/[\x{0100}\x{0102}\x{0104}\x{00C0}\x{00C1}\x{00C2}\x{00C3}\x{00C4}\x{00C5}\x{0410}]/u',
        /* B  */ '/[\x{0411}]]/u',
        /* C  */ '/[\x{00C7}\x{0106}\x{0108}\x{010A}\x{010C}\x{0426}]/u',
        /* D  */ '/[\x{010E}\x{0110}\x{0414}]/u',
        /* E  */ '/[\x{00C8}\x{00C9}\x{00CA}\x{00CB}\x{0112}\x{0114}\x{0116}\x{0118}\x{011A}\x{0415}\x{042D}]/u',
        /* F  */ '/[\x{0424}]/u',
        /* G  */ '/[\x{011C}\x{011E}\x{0120}\x{0122}\x{0413}\x{0490}]/u',
        /* H  */ '/[\x{0124}\x{0126}]/u',
        /* I  */ '/[\x{0128}\x{012A}\x{012C}\x{012E}\x{0130}\x{0418}\x{0406}]/u',
        /* J  */ '/[\x{0134}\x{0419}]/u',
        /* K  */ '/[\x{0136}\x{041A}]/u',
        /* L  */ '/[\x{0139}\x{013B}\x{013D}\x{0139}\x{0141}\x{041B}]/u',
        /* M  */ '/[\x{041C}]/u',
        /* N  */ '/[\x{00D1}\x{0143}\x{0145}\x{0147}\x{014A}\x{041D}]/u',
        /* O  */ '/[\x{00D3}\x{014C}\x{014E}\x{0150}\x{041E}]/u',
        /* P  */ '/[\x{041F}]/u',
        /* R  */ '/[\x{0154}\x{0156}\x{0158}\x{0420}]/u',
        /* S  */ '/[\x{015A}\x{015C}\x{015E}\x{0160}\x{0421}]/u',
        /* T  */ '/[\x{0162}\x{0164}\x{0166}\x{0422}]/u',
        /* U  */ '/[\x{00D9}\x{00DA}\x{00DB}\x{00DC}\x{0168}\x{016A}\x{016C}\x{016E}\x{0170}\x{0172}\x{0423}]/u',
        /* V  */ '/[\x{0412}]/u',
        /* W  */ '/[\x{0174}]/u',
        /* Y  */ '/[\x{0176}\x{042B}]/u',
        /* Z  */ '/[\x{0179}\x{017B}\x{017D}\x{0417}]/u',
        /* AE */ '/[\x{00C6}]/u',
        /* CH */ '/[\x{0427}]/u',
        /* KH */ '/[\x{0425}]/u',
        /* OE */ '/[\x{0152}]/u',
        /* SH */ '/[\x{0428}]/u',
        /* SHH*/ '/[\x{0429}]/u',
        /* YA */ '/[\x{042F}]/u',
        /* YE */ '/[\x{0404}]/u',
        /* YI */ '/[\x{0407}]/u',
        /* YO */ '/[\x{0401}]/u',
        /* YU */ '/[\x{042E}]/u',
        /* ZH */ '/[\x{0416}]/u');

        // ö to oe
        // å to aa
        // ä to ae

    $replacements = array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 'ss', 't', 'u', 'v', 'w', 'y', 'z', 'ae', 'ch', 'kh', 'oe', 'sh', 'shh', 'ya', 'ye', 'yi', 'yo', 'yu', 'zh',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'Y', 'Z', 'AE', 'CH', 'KH', 'OE', 'SH', 'SHH', 'YA', 'YE', 'YI', 'YO', 'YU', 'ZH'
        );

    return preg_replace($patterns, $replacements, $str);
}
 
 
 /*
  * 
  * VALIDATION OPTIONS
  */
function validate_isUnsignedInt($value)
{
    return (preg_match('#^[0-9]+$#', (string)$value) && $value < 4294967296 && $value >= 0);
}

/*
* Check for date format
*
* @param string $date Date to validate
* @return boolean Validity is ok or not
*/
function validate_isDateFormat($date)
{
   return (bool)preg_match('/^([0-9]{4})-((0?[0-9])|(1[0-2]))-((0?[0-9])|([1-2][0-9])|(3[01]))( [0-9]{2}:[0-9]{2}:[0-9]{2})?$/', $date);
}

/**
* Check for date validity
*
* @param string $date Date to validate
* @return boolean Validity is ok or not
*/
function validate_isDate($date)
{
   if (!preg_match('/^([0-9]{4})-((?:0?[0-9])|(?:1[0-2]))-((?:0?[0-9])|(?:[1-2][0-9])|(?:3[01]))( [0-9]{2}:[0-9]{2}:[0-9]{2})?$/', $date, $matches))
       return false;
   return checkdate((int)$matches[2], (int)$matches[3], (int)$matches[1]);
}
 
 /*
  * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  *     DEBUG UTILITIES FROM HERE
  * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  */
 
 /**
* Display an error with detailed object
*
* @param mixed $object
* @param boolean $kill
* @return $object if $kill = false;
*/
function dieObject($object, $kill = true)
{
   echo '<xmp style="text-align: left;">';
   print_r($object);
   echo '</xmp><br />';
   if ($kill)
       die('END');
   return $object;
}
 

 /**
* Display a var dump in firebug console
*
* @param object $object Object to display
*/
function fd($object, $type = 'log')
{
$types = array('log', 'debug', 'info', 'warn', 'error', 'assert');

if(!in_array($type, $types))
    $type = 'log';

echo '
    <script type="text/javascript">
        console.'.$type.'('.json_encode($object).');
    </script>
';
}


/**
* ALIAS OF dieObject() - Display an error with detailed object
*
* @param object $object Object to display
*/
function ddd($object, $kill = true)
{
    return (dieObject($object, $kill));
}

/**
* ALIAS OF dieObject() - Display an error with detailed object but don't stop the execution
*
* @param object $object Object to display
*/
function ppp($object)
{
    return (dieObject($object, false));
}


