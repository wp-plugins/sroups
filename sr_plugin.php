<?php
/*
Plugin Name: Sroups
Plugin URI: http://srou.ps
Description: Sroups virtualizes the groups in a way that has never done before.
In a few minutes, the members of your groups gets access to a virtual world
specifically created for your community.
Author: Sroups
Version: 0.0.14
Author URI: http://srou.ps
*/

// administrator check
if (!defined('ABSPATH'))
   die("Please use administration panel to see this page");

// error messaging system
global $messages;

// ----------------------------------------------------------------------------
// Codes directly used by Wordpress
// ----------------------------------------------------------------------------

// show Sroups client
add_action('wp_footer', 'sr_showClient');

// shpw Sroups plugin in the admin panel
add_action('admin_menu', 'sr_admin_addToOptionsMenu');

add_action('deactivate_plugin','sr_admin_deactivation');


// ----------------------------------------------------------------------------
// Definitions
// ----------------------------------------------------------------------------

define('SROUPS_API_URL', 'http://srou.ps/api');
define("SROUPS_VERISON", "0.1");
define("SROUPS_REQUIRED_PHP_VERSION", "5.1.3");
define("SROUPS_HAS_MOD_HEADERS", in_array("mod_headers", sr_apache_get_modules()));
define("SROUPS_SWF_PATH", "http://srou.ps/sroups.js");

/* UTILITY FUCTIONS */

/**
 * Apache module check
 * @return array
 */
function sr_apache_get_modules() {
    if(function_exists("apache_get_modules"))
        return apache_get_modules();
    else
        return array('mod_rewrite','mod_headers','core');
}

/**
 * Flash Messenger
 * @global array $message
 * @param String $content
 * @param boolean $error
 */
function sr_add_message($content,$error = false) {
	global $messages; 
	if(!is_array($messages)) {
		$messages = array();
	}
	$messages[] = array("message" => $content, "error" => $error); 
}

/**
 * Displays error messages
 */
function sr_admin_showMessages() {
    global $messages;    
    if(!empty($messages)) :
	foreach($messages as $message):
            if(!$message['error']):
        ?>
            <div class="updated fade" id="message">
                <p><strong><?php echo $message['message']; ?></strong></p>
            </div>
        <?php else: ?>
            <div class="error" id="message">
                <p><strong><?php echo $message['message']; ?></strong></p>
            </div>
        <?php
            endif;
        endforeach;
    endif;
    // empty the messages queue
    $messages = array();
}


// ----------------------------------------------------------------------------
// Front Pages
// ----------------------------------------------------------------------------
function sr_showClient() {
    $option = get_option('sroups_visibility');
    if($option == 'band' || $option == 'both') {
        sr_createClientElements(sr_getFlashClient());
    }
}

function sr_createClientElements($sroupsFlashClient) {
    if (!$sroupsFlashClient) {
        return;
    }

    $outputBuffer = '';

    // load the jquery library
    // TODO: check whether jquery is already imported or not
    $outputBuffer .= '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>';

	$baseUrl = get_bloginfo('url');

    // add event handler to the click event of the band element that toggles
    // Sroups screen
    $outputBuffer .= <<<JS
<script type="text/javascript" src="$baseUrl/wp-content/plugins/sroups/js/jquery-ui-1.8.5.custom.min.js"></script>
<script type="text/javascript">
$(function() {
    $(".os_sroups").draggable({handle: '.os_sroups_header'});    
    $(".open_sroups").click(function(ev){
        ev.preventDefault();
        $('.os_sroups').toggle();
        return false;
    });
});
</script>
JS;

    $sroupsButtonBackground = $baseUrl . '/wp-content/plugins/sroups/images/sroups_footer_logo.png';

    // add styles for the band and Sroups elements
    $outputBuffer .= <<<CSS
<style type="text/css">
.os_sroups {
    display:none;
    background:none repeat scroll 0 0 #FFFFFF;
    border:1px solid #CCCCCC;
    bottom:0;
    float:left;
    margin:0 5px 22px 0;
    position:fixed;
    right:0;
    height: 645px;
    width:780px;
}
.os_sroups_header {
    background-color:#333333;
    width:100%;
    height:20px;
    text-align:right;
}
.os_sroups_header a {
    color: #EFEFEF;
    padding-right:10px;
    text-decoration:none;
}
.os_sroups_inner {
    padding:10px;
}
.os_footerband {
    background: transparent url('$sroupsButtonBackground') no-repeat top left;
    bottom:0;
    height:31px;
    padding:0;
    position:fixed;
    right:15px;
    text-align:left;
    width:92px;
    z-index: 9999;
}
.os_footerband a, .os_footerband a:visited {
    color: #FFFFFF;
    text-decoration: none;
    width: 92px;
    height: 31px;
    margin: 0;
    position: absolute;
    left: 0;
    top: 0;
}
.os_footerband a:hover {
    color: #FFFFFF;
}
.os_footerband a span {
    display: none;
}
</style>
CSS;

    // Sroups SWF container and band elements
    $outputBuffer .= <<<XHTML
<div class="os_sroups">
    <div class="os_sroups_header"><a class="open_sroups" href="#">X</a></div>
    <div class="os_sroups_inner">$sroupsFlashClient</div>
</div>
<div class="os_footerband"><a class="open_sroups" href="#sroups"><span>Sroups</span></a></div>
XHTML;

    // add buffer content into the document
    echo $outputBuffer;
}

function sr_getFlashClient() {
    // get sroups id from db (if exists)
    $sid = get_option('sroups_id');
    if (empty($sid)) {
        return FALSE;
    }

    // return the script URL that creates the flash client
    // TODO: Change url
    return sprintf('<script type="text/javascript" src="' . SROUPS_SWF_PATH . '?sid=%s"></script>', $sid);
}

// ----------------------------------------------------------------------------
// Admin Panel
// ----------------------------------------------------------------------------
function sr_admin_addToOptionsMenu() {
    add_options_page('Sroups Options', 'Sroups', 'manage_options', 'sroups_identifier', 'sr_admin_showPluginPage');
}

function sr_admin_showPluginPage() {
    // check user permissions
    if (!current_user_can('manage_options')) {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    // fetch sroups id and sroups owner id for this blog
    $sid = get_option('sroups_id');
    $uid = get_option('sroups_user_id');

    // show the related views regarding to the previous results
    if (empty($uid)) {
        // Sroups owner subscription form
        sr_common_script();
        sr_admin_showSroupsRegistrationForm();
    } else if (empty($sid)) {
        // Sroups creation form
        sr_common_script();
        sr_admin_showSroupsForm();
    } else {
        // Sroups details
        sr_common_script();
        sr_admin_showSroupsForm();
    }
    sr_admin_compatibility();
    sr_admin_information();
}

/**
 * Adds a page that includes sroups.swf
 * If the page is already added do nothing
 */
function sr_addSroupsPage() {
    if(!current_user_can('edit_posts')) {
        sr_add_message('You do not have permission to create a new page');
        return;
    }

    // check if the page is already exists
    if(sr_isSroupsPageExists()) return;

    global $current_user;
    get_currentuserinfo();

    // @todo: make the title parameterize
    $title = 'Sroups';

    // Create post object
    $post = array ();
    $post['post_title'] = $title;
    $post['post_type'] = 'page';
    $post['post_name'] = 'sroups';

    // @todo: Do hierarchy for pages
    //$post['post_parent'] = intval($_POST['post_parent']);
    $post['post_content'] = sr_getFlashClient();
    $post['post_status'] = 'publish';
    $post['post_author'] = $current_user->ID;
    
    wp_insert_post($post);

}

function sr_isSroupsPageExists() {
    global $wpdb;

    $user_query = "SELECT COUNT(1) AS total FROM $wpdb->posts WHERE post_type = 'page' and post_name = 'sroups' and post_status = 'publish'";
    $posts = $wpdb->get_results($user_query);
    return ($posts[0]->total > 0) ? true : false;
}

/**
 * Removes the sroups page from db
 */
function sr_disableSroupsPage() {
    global $wpdb;
    
    $user_query = "DELETE FROM $wpdb->posts WHERE post_type = 'page' and post_name = 'sroups' and post_status = 'publish'";
    $posts = $wpdb->get_results($user_query);
}

function sr_admin_pageHeader() {
    ?>
    <div class="wrap">
    <?php sr_admin_showMessages(); ?>
    <h2>Sroups Settings</h2>
    <div id="sr_errors"></div>
    <?php
}

function sr_admin_pageFooter() {
    ?>
    </div>
    <?php
}

function sr_admin_showSroupsRegistrationForm() {
    if(!sr_compatibilityCheck ()) {
        sr_add_message('Sorry, Your system is not compatible for the Sroups plugin. Please check the items listed in the compatibility list below');
        return;
    }

    if (!empty($_POST['sr_user_id'])) {
        if (!empty($_POST['sr_user_id']) && is_numeric($_POST['sr_user_id'])) {
            update_option('sroups_user_id', (int) trim($_POST['sr_user_id']));
            update_option('sroups_visibility', 'band'); // the default option for visibility is the band

            if(isset($_POST['sroups_id']) && isset($_POST['sroups_sig'])) {
                update_option ('sroups_id', $_POST['sroups_id']);
                update_option ('sroups_sig', $_POST['sroups_sig']);
                sr_add_message('Thank you, you have successfully srouped this blog');
            }
                
            return sr_admin_showSroupsForm();
        }
    }

    global $current_user;
    get_currentuserinfo();

    sr_admin_pageHeader();

    // set variables
    $community_url = get_bloginfo('url');
    $url = sprintf('%s%s', $community_url, '/wp-content/plugins/sroups');

    $outputBuffer = <<<XHTML
<form id="sroups_registeration_form" action="" method="post">
<table class="form-table">
    <tbody>
        <tr valign="top">
            <th scope="row"><label for="sr_register_already_member">Already a member?</label></th>
            <td><input type="checkbox" id="sr_register_already_member" value="1" name="sr_register_already_member" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="sr_register_email">E-Mail Address</label></th>
            <td><input class="regular-text" type="text" id="sr_register_email" value="{$current_user->user_email}" name="sr_register_email" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="sr_register_password">Password</label></th>
            <td><input class="regular-text" type="password"
            id="sr_register_password" value="" name="sr_register_password" /></td>
        </tr>
        <tr valign="top" class="row_profile">
            <th scope="row"><label for="sr_register_name">Full Name</label></th>
            <td><input class="regular-text" type="text" id="sr_register_name" value="{$current_user->user_firstname} {$current_user->user_lastname}" name="sr_register_name" /></td>
        </tr>
        <tr valign="top" class="row_profile">
            <th scope="row"><label for="sr_register_country">Country</label></th>
            <td>
                <select name="sr_register_country" id="sr_register_country">
                    <option value="">Please select your country</option>
                    <option value="AF">Afghanistan</option>
                    <option value="AL">Albania</option>
                    <option value="DZ">Algeria</option>
                    <option value="AS">American Samoa</option>
                    <option value="AD">Andorra</option>
                    <option value="AG">Angola</option>
                    <option value="AI">Anguilla</option>
                    <option value="AG">Antigua &amp; Barbuda</option>
                    <option value="AR">Argentina</option>
                    <option value="AA">Armenia</option>
                    <option value="AW">Aruba</option>
                    <option value="AU">Australia</option>
                    <option value="AT">Austria</option>
                    <option value="AZ">Azerbaijan</option>
                    <option value="BS">Bahamas</option>
                    <option value="BH">Bahrain</option>
                    <option value="BD">Bangladesh</option>
                    <option value="BB">Barbados</option>
                    <option value="BY">Belarus</option>
                    <option value="BE">Belgium</option>
                    <option value="BZ">Belize</option>
                    <option value="BJ">Benin</option>
                    <option value="BM">Bermuda</option>
                    <option value="BT">Bhutan</option>
                    <option value="BO">Bolivia</option>
                    <option value="BL">Bonaire</option>
                    <option value="BA">Bosnia &amp; Herzegovina</option>
                    <option value="BW">Botswana</option>
                    <option value="BR">Brazil</option>
                    <option value="BC">British Indian Ocean Ter</option>
                    <option value="BN">Brunei</option>
                    <option value="BG">Bulgaria</option>
                    <option value="BF">Burkina Faso</option>
                    <option value="BI">Burundi</option>
                    <option value="KH">Cambodia</option>
                    <option value="CM">Cameroon</option>
                    <option value="CA">Canada</option>
                    <option value="IC">Canary Islands</option>
                    <option value="CV">Cape Verde</option>
                    <option value="KY">Cayman Islands</option>
                    <option value="CF">Central African Republic</option>
                    <option value="TD">Chad</option>
                    <option value="CD">Channel Islands</option>
                    <option value="CL">Chile</option>
                    <option value="CN">China</option>
                    <option value="CI">Christmas Island</option>
                    <option value="CS">Cocos Island</option>
                    <option value="CO">Colombia</option>
                    <option value="CC">Comoros</option>
                    <option value="CG">Congo</option>
                    <option value="CK">Cook Islands</option>
                    <option value="CR">Costa Rica</option>
                    <option value="CT">Cote D'Ivoire</option>
                    <option value="HR">Croatia</option>
                    <option value="CU">Cuba</option>
                    <option value="CB">Curacao</option>
                    <option value="CY">Cyprus</option>
                    <option value="CZ">Czech Republic</option>
                    <option value="DK">Denmark</option>
                    <option value="DJ">Djibouti</option>
                    <option value="DM">Dominica</option>
                    <option value="DO">Dominican Republic</option>
                    <option value="TM">East Timor</option>
                    <option value="EC">Ecuador</option>
                    <option value="EG">Egypt</option>
                    <option value="SV">El Salvador</option>
                    <option value="GQ">Equatorial Guinea</option>
                    <option value="ER">Eritrea</option>
                    <option value="EE">Estonia</option>
                    <option value="ET">Ethiopia</option>
                    <option value="FA">Falkland Islands</option>
                    <option value="FO">Faroe Islands</option>
                    <option value="FJ">Fiji</option>
                    <option value="FI">Finland</option>
                    <option value="FR">France</option>
                    <option value="GF">French Guiana</option>
                    <option value="PF">French Polynesia</option>
                    <option value="FS">French Southern Ter</option>
                    <option value="GA">Gabon</option>
                    <option value="GM">Gambia</option>
                    <option value="GE">Georgia</option>
                    <option value="DE">Germany</option>
                    <option value="GH">Ghana</option>
                    <option value="GI">Gibraltar</option>
                    <option value="GB">Great Britain</option>
                    <option value="GR">Greece</option>
                    <option value="GL">Greenland</option>
                    <option value="GD">Grenada</option>
                    <option value="GP">Guadeloupe</option>
                    <option value="GU">Guam</option>
                    <option value="GT">Guatemala</option>
                    <option value="GN">Guinea</option>
                    <option value="GY">Guyana</option>
                    <option value="HT">Haiti</option>
                    <option value="HW">Hawaii</option>
                    <option value="HN">Honduras</option>
                    <option value="HK">Hong Kong</option>
                    <option value="HU">Hungary</option>
                    <option value="IS">Iceland</option>
                    <option value="IN">India</option>
                    <option value="ID">Indonesia</option>
                    <option value="IA">Iran</option>
                    <option value="IQ">Iraq</option>
                    <option value="IR">Ireland</option>
                    <option value="IM">Isle of Man</option>
                    <option value="IL">Israel</option>
                    <option value="IT">Italy</option>
                    <option value="JM">Jamaica</option>
                    <option value="JP">Japan</option>
                    <option value="JO">Jordan</option>
                    <option value="KZ">Kazakhstan</option>
                    <option value="KE">Kenya</option>
                    <option value="KI">Kiribati</option>
                    <option value="NK">Korea North</option>
                    <option value="KS">Korea South</option>
                    <option value="KW">Kuwait</option>
                    <option value="KG">Kyrgyzstan</option>
                    <option value="LA">Laos</option>
                    <option value="LV">Latvia</option>
                    <option value="LB">Lebanon</option>
                    <option value="LS">Lesotho</option>
                    <option value="LR">Liberia</option>
                    <option value="LY">Libya</option>
                    <option value="LI">Liechtenstein</option>
                    <option value="LT">Lithuania</option>
                    <option value="LU">Luxembourg</option>
                    <option value="MO">Macau</option>
                    <option value="MK">Macedonia</option>
                    <option value="MG">Madagascar</option>
                    <option value="MY">Malaysia</option>
                    <option value="MW">Malawi</option>
                    <option value="MV">Maldives</option>
                    <option value="ML">Mali</option>
                    <option value="MT">Malta</option>
                    <option value="MH">Marshall Islands</option>
                    <option value="MQ">Martinique</option>
                    <option value="MR">Mauritania</option>
                    <option value="MU">Mauritius</option>
                    <option value="ME">Mayotte</option>
                    <option value="MX">Mexico</option>
                    <option value="MI">Midway Islands</option>
                    <option value="MD">Moldova</option>
                    <option value="MC">Monaco</option>
                    <option value="MN">Mongolia</option>
                    <option value="MS">Montserrat</option>
                    <option value="MA">Morocco</option>
                    <option value="MZ">Mozambique</option>
                    <option value="MM">Myanmar</option>
                    <option value="NA">Nambia</option>
                    <option value="NU">Nauru</option>
                    <option value="NP">Nepal</option>
                    <option value="AN">Netherland Antilles</option>
                    <option value="NL">Netherlands (Holland, Europe)</option>
                    <option value="NV">Nevis</option>
                    <option value="NC">New Caledonia</option>
                    <option value="NZ">New Zealand</option>
                    <option value="NI">Nicaragua</option>
                    <option value="NE">Niger</option>
                    <option value="NG">Nigeria</option>
                    <option value="NW">Niue</option>
                    <option value="NF">Norfolk Island</option>
                    <option value="NO">Norway</option>
                    <option value="OM">Oman</option>
                    <option value="PK">Pakistan</option>
                    <option value="PW">Palau Island</option>
                    <option value="PS">Palestine</option>
                    <option value="PA">Panama</option>
                    <option value="PG">Papua New Guinea</option>
                    <option value="PY">Paraguay</option>
                    <option value="PE">Peru</option>
                    <option value="PH">Philippines</option>
                    <option value="PO">Pitcairn Island</option>
                    <option value="PL">Poland</option>
                    <option value="PT">Portugal</option>
                    <option value="PR">Puerto Rico</option>
                    <option value="QA">Qatar</option>
                    <option value="ME">Republic of Montenegro</option>
                    <option value="RS">Republic of Serbia</option>
                    <option value="RE">Reunion</option>
                    <option value="RO">Romania</option>
                    <option value="RU">Russia</option>
                    <option value="RW">Rwanda</option>
                    <option value="NT">St Barthelemy</option>
                    <option value="EU">St Eustatius</option>
                    <option value="HE">St Helena</option>
                    <option value="KN">St Kitts-Nevis</option>
                    <option value="LC">St Lucia</option>
                    <option value="MB">St Maarten</option>
                    <option value="PM">St Pierre &amp; Miquelon</option>
                    <option value="VC">St Vincent &amp; Grenadines</option>
                    <option value="SP">Saipan</option>
                    <option value="SO">Samoa</option>
                    <option value="AS">Samoa American</option>
                    <option value="SM">San Marino</option>
                    <option value="ST">Sao Tome &amp; Principe</option>
                    <option value="SA">Saudi Arabia</option>
                    <option value="SN">Senegal</option>
                    <option value="SC">Seychelles</option>
                    <option value="SL">Sierra Leone</option>
                    <option value="SG">Singapore</option>
                    <option value="SK">Slovakia</option>
                    <option value="SI">Slovenia</option>
                    <option value="SB">Solomon Islands</option>
                    <option value="OI">Somalia</option>
                    <option value="ZA">South Africa</option>
                    <option value="ES">Spain</option>
                    <option value="LK">Sri Lanka</option>
                    <option value="SD">Sudan</option>
                    <option value="SR">Suriname</option>
                    <option value="SZ">Swaziland</option>
                    <option value="SE">Sweden</option>
                    <option value="CH">Switzerland</option>
                    <option value="SY">Syria</option>
                    <option value="TA">Tahiti</option>
                    <option value="TW">Taiwan</option>
                    <option value="TJ">Tajikistan</option>
                    <option value="TZ">Tanzania</option>
                    <option value="TH">Thailand</option>
                    <option value="TG">Togo</option>
                    <option value="TK">Tokelau</option>
                    <option value="TO">Tonga</option>
                    <option value="TT">Trinidad &amp; Tobago</option>
                    <option value="TN">Tunisia</option>
                    <option value="TR">Turkey</option>
                    <option value="TU">Turkmenistan</option>
                    <option value="TC">Turks &amp; Caicos Is</option>
                    <option value="TV">Tuvalu</option>
                    <option value="UG">Uganda</option>
                    <option value="UA">Ukraine</option>
                    <option value="AE">United Arab Emirates</option>
                    <option value="GB">United Kingdom</option>
                    <option value="US">United States of America</option>
                    <option value="UY">Uruguay</option>
                    <option value="UZ">Uzbekistan</option>
                    <option value="VU">Vanuatu</option>
                    <option value="VS">Vatican City State</option>
                    <option value="VE">Venezuela</option>
                    <option value="VN">Vietnam</option>
                    <option value="VB">Virgin Islands (Brit)</option>
                    <option value="VA">Virgin Islands (USA)</option>
                    <option value="WK">Wake Island</option>
                    <option value="WF">Wallis &amp; Futana Is</option>
                    <option value="YE">Yemen</option>
                    <option value="ZR">Zaire</option>
                    <option value="ZM">Zambia</option>
                    <option value="ZW">Zimbabwe</option>
                </select>
            </td>
        </tr>
        <tr valign="top" class="row_profile">
            <th scope="row">Gender</th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span>Gender</span></legend>
                    <label><input type="radio" checked="checked" value="female" name="sr_register_gender" />Female</label><br />
                    <label><input type="radio" value="male" name="sr_register_gender" />Male</label>
                </fieldset>
            </td>
        </tr>
        <tr valign="top" class="row_profile">
            <th scope="row">Birthdate</th>
            <td>
                <select id="sr_register_birthday" name="sr_register_birthday">
                    <option value="1">01</option>
                    <option value="2">02</option>
                    <option value="3">03</option>
                    <option value="4">04</option>
                    <option value="5">05</option>
                    <option value="6">06</option>
                    <option value="7">07</option>
                    <option value="8">08</option>
                    <option value="9">09</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                    <option value="13">13</option>
                    <option value="14">14</option>
                    <option value="15">15</option>
                    <option value="16">16</option>
                    <option value="17">17</option>
                    <option value="18">18</option>
                    <option value="19">19</option>
                    <option value="20">20</option>
                    <option value="21">21</option>
                    <option value="22">22</option>
                    <option value="23">23</option>
                    <option value="24">24</option>
                    <option value="25">25</option>
                    <option value="26">26</option>
                    <option value="27">27</option>
                    <option value="28">28</option>
                    <option value="29">29</option>
                    <option value="30">30</option>
                    <option value="31">31</option>
                </select>
                <select id="sr_register_birthmonth" name="sr_register_birthmonth">
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
                <select id="sr_register_birthyear" name="sr_register_birthyear">
                    <option value="1997">1997</option>
                    <option value="1996">1996</option>
                    <option value="1995">1995</option>
                    <option value="1994">1994</option>
                    <option value="1993">1993</option>
                    <option value="1992">1992</option>
                    <option value="1991">1991</option>
                    <option value="1990">1990</option>
                    <option value="1989">1989</option>
                    <option value="1988">1988</option>
                    <option value="1987">1987</option>
                    <option value="1986">1986</option>
                    <option value="1985">1985</option>
                    <option value="1984">1984</option>
                    <option value="1983">1983</option>
                    <option value="1982">1982</option>
                    <option value="1981">1981</option>
                    <option value="1980">1980</option>
                    <option value="1979">1979</option>
                    <option value="1978">1978</option>
                    <option value="1977">1977</option>
                    <option value="1976">1976</option>
                    <option value="1975">1975</option>
                    <option value="1974">1974</option>
                    <option value="1973">1973</option>
                    <option value="1972">1972</option>
                    <option value="1971">1971</option>
                    <option value="1970">1970</option>
                    <option value="1969">1969</option>
                    <option value="1968">1968</option>
                    <option value="1967">1967</option>
                    <option value="1966">1966</option>
                    <option value="1965">1965</option>
                    <option value="1964">1964</option>
                    <option value="1963">1963</option>
                    <option value="1962">1962</option>
                    <option value="1961">1961</option>
                    <option value="1960">1960</option>
                    <option value="1959">1959</option>
                    <option value="1958">1958</option>
                    <option value="1957">1957</option>
                    <option value="1956">1956</option>
                    <option value="1955">1955</option>
                    <option value="1954">1954</option>
                    <option value="1953">1953</option>
                    <option value="1952">1952</option>
                    <option value="1951">1951</option>
                    <option value="1950">1950</option>
                    <option value="1949">1949</option>
                    <option value="1948">1948</option>
                    <option value="1947">1947</option>
                    <option value="1946">1946</option>
                    <option value="1945">1945</option>
                    <option value="1944">1944</option>
                    <option value="1943">1943</option>
                    <option value="1942">1942</option>
                    <option value="1941">1941</option>
                    <option value="1940">1940</option>
                    <option value="1939">1939</option>
                    <option value="1938">1938</option>
                    <option value="1937">1937</option>
                    <option value="1936">1936</option>
                    <option value="1935">1935</option>
                    <option value="1934">1934</option>
                    <option value="1933">1933</option>
                </select>
            </td>
        </tr>
    </tbody>
</table>
<p class="submit">
    <input type="hidden" id="sr_register_userId" name="sr_register_userId" value="{$current_user->ID}" />

    <input type="hidden" name="sr_create_community_url" id="sr_create_community_url" value="$community_url" />
    <input type="hidden" name="sr_create_url" id="sr_create_url" value="$url" />
    <input type="hidden" name="sr_create_communityType" id="sr_create_communityType" value="3" />

    <input class="button-primary" type="submit" value="Make me a Sroups owner" name="sr_submit_registeration" id="sr_submit_registeration" />
    <input class="button-primary" type="submit" value="Get my Sroups user data" name="sr_submit_fetch_data" id="sr_submit_fetch_data" style="display: none;" />
</p>
</form>
XHTML;

    $outputBuffer .= <<<JS
    <script type="text/javascript">
        var _sr = jQuery.noConflict();
        _sr(document).ready(function() {
            _sr("form#sroups_registeration_form").keypress(function(e) {
                var code = (e.keyCode) ? e.keyCode : e.which;
                if (13 == code) {
                    var elm = null;
                    if (_sr("input#sr_submit_registeration").is(':visible')) {
                        elm = _sr("input#sr_submit_registeration");
                    } else {
                        elm = _sr("input#sr_submit_fetch_data")
                    }
                    elm.trigger('click');
                    return false;
                }
            });
            _sr("input#sr_register_already_member").change(function() {
                _sr("h3.sroups-header").toggle();
                _sr("tr.row_profile").toggle();
                _sr("p.submit input.button-primary").toggle();
            });
            _sr("input#sr_submit_registeration").click(function() {
                // form validation
                if (validateRegistrationForm()) {
                    var requestData = {
                        email: _sr("input#sr_register_email").val(),
                        password: _sr("input#sr_register_password").val(),
                        name: _sr("input#sr_register_name").val(),
                        birthdate: buildBirthDate(),
                        gender: _sr("input[name=sr_register_gender]:checked").val(),
                        country: _sr("select#sr_register_country").val(),
                        community_uid: _sr("input#sr_register_userId").val(),
                        community_type: 3
                    };
                    _sr.ajax({
                        type: "POST",
                        url: getPostUrl() + "?url=" + Url.encode(Base64.encode(getUrl("/user"))),
                        data: requestData,
                        success: function (data) {
                            if (data) {
                                if (data.sroups_uid) {
                                    _sr("form#sroups_registeration_form").append(
                                        _sr("<input>").attr({
                                            type: 'hidden',
                                            name: 'sr_user_id'
                                        }).val(data.sroups_uid)
                                    ).submit();
                                }
                            }
                        },
                        error: function (e) {
                            var messages = _sr.parseJSON(e.responseText);
                            for(var i = 0; i < messages.errors.length; i++) {
                                sr_admin_errorMessages( messages.errors[i].message );
                            }
                            return false;
                        },
                        dataType: "json",
                        async: false
                    });
                }

                return false;
            });
            _sr("input#sr_submit_fetch_data").click(function() {
                _sr.ajax({
                    type: "POST",
                    url: getPostUrl() + "?url=" + Url.encode(Base64.encode(getUrl("/auth"))),
                    data: {
                        email: _sr("input#sr_register_email").val(),
                        password: MD5(_sr("input#sr_register_password").val()),
                        community_type: 3,
                        community_uid: _sr("input#sr_register_userId").val(),
                        community_url: _sr("input#sr_create_community_url").val(),
                        url: _sr("input#sr_create_url").val()
                    },
                    success: function (data) {
                        if (data && data.sroups_uid) {
                            _sr("form#sroups_registeration_form").append(
                                _sr("<input>").attr({
                                    type: 'hidden',
                                    name: 'sr_user_id'
                                }).val(data.sroups_uid)
                            );

                            if(data.sroups_id) {
                                _sr("form#sroups_registeration_form").append(
                                    _sr("<input>").attr({
                                        type: 'hidden',
                                        name: 'sroups_id'
                                    }).val(data.sroups_id)
                                );
                            }

                            if(data.sroups_sig) {
                                _sr("form#sroups_registeration_form").append(
                                    _sr("<input>").attr({
                                        type: 'hidden',
                                        name: 'sroups_sig'
                                    }).val(data.sroups_sig)
                                );
                            }

                            _sr("form#sroups_registeration_form").submit();
                        }
                    },
                    error: function (e) {
                        sr_admin_errorMessages(e.responseText);
                        return false;
                    },
                    dataType: "json",
                    async: false
                });

                return false;
            });
        });

        /**
         * Common helpers
         */

        function buildBirthDate() {
            var d = _sr("select#sr_register_birthday").val();
            if (d.length == 1) {
                d = "0" + d;
            }
            var m = _sr("select#sr_register_birthmonth").val();
            if (m.length == 1) {
                m = "0" + m;
            }
            var y = _sr("select#sr_register_birthyear").val();

            return y + "-" + m + "-" + d;
        }

        /**
         * Form validation functions
         */
        function validateRegistrationForm() {
            var formIsValid = false;
            if (!emailIsValid(_sr("input#sr_register_email").val())) {
                sr_admin_errorMessages('Invalid email address!');
            } else if (!nameIsValid(_sr("input#sr_register_name").val())) {
                sr_admin_errorMessages('Invalid name!');
            } else if (!genderIsValid(_sr("input[name=sr_register_gender]:checked").val())) {
                sr_admin_errorMessages('Invalid gender!');
            } else if (!birthDayIsValid(_sr("select#sr_register_birthday").val())) {
                sr_admin_errorMessages('Invalid birthdate day value!');
            } else if (!birthMonthIsValid(_sr("select#sr_register_birthmonth").val())) {
                sr_admin_errorMessages('Invalid birthdate month value!');
            } else if (!birthYearIsValid(_sr("select#sr_register_birthyear").val())) {
                sr_admin_errorMessages('Invalid birthdate year!');
            } else {
                formIsValid = true;
            };
            return formIsValid;
        }

        function emailIsValid(email) {
            return /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/.test(email);
        }

        function nameIsValid(name) {
            return _sr.trim(name).length >= 5;
        }

        function countryIsValid(country) {
            var inArray = false;
            _sr("select#sr_register_country option").each(function() {
                if (country == _sr(this).val()) {
                    inArray = true;
                }
            });
            return inArray;
        }

        function genderIsValid(gender) {
            return gender == 'male' || gender == 'female';
        }

        function birthDayIsValid(birthDay) {
            birthDay = parseInt(birthDay);
            return !isNaN(birthDay) && 1 <= birthDay && birthDay <= 31;
        }

        function birthMonthIsValid(birthMonth) {
            birthMonth = parseInt(birthMonth);
            return !isNaN(birthMonth) && 1 <= birthMonth && birthMonth <= 12;
        }

        function birthYearIsValid(birthYear) {
            birthYear = parseInt(birthYear);
            return !isNaN(birthYear) && 1933 <= birthYear && birthYear <= ((new Date()).getFullYear() - 7);
        }
    </script>
JS;

    echo $outputBuffer;

    sr_admin_pageFooter();
}

function sr_admin_showSroupsForm() {
    if(!sr_compatibilityCheck ()) {
        sr_add_message('Sorry, Your system is not compatible for the Sroups plugin. Please check the items listed in the compatibility list below');
        return;
    }
    
    // if we don't have co user id, than this form is useless
    if(!get_option('sroups_user_id')) {
        sr_add_message('There is an internal error occurred, Please try again');
        return sr_admin_showSroupsRegistrationForm();
    }

    // if the option has not been set, it is a creation form
    $creation = (get_option('sroups_id')) ? false : true;

    if (!empty($_POST['sid']) && is_numeric($_POST['sid'])) {
        update_option('sroups_id', (int) trim($_POST['sid']));
        $visibility = (isset($_POST['sr_visibility'])) ? trim($_POST['sr_visibility']) : 'band';

        if($visibility == 'both' || $visibility == 'page') {
            sr_addSroupsPage();
        } else {
            sr_disableSroupsPage();
        }

        update_option('sroups_visibility', $visibility);
        
        if($creation){
            update_option('sroups_sig', $_POST['sig']);
            sr_add_message("Thank you, you have srouped your blog successfully.");
            $creation = false;
        } else {
            sr_add_message("Sroups updated successfully");
            $creation = false;
        }
    }

    $visibility = get_option('sroups_visibility');
    $visibilityBand = ($visibility == 'band') ? 'checked="checked"' : '';
    $visibilityPage = ($visibility == 'page') ? 'checked="checked"' : '';
    $visibilityBoth = ($visibility == 'both') ? 'checked="checked"' : '';
    $visibilityHide = ($visibility == 'hide') ? 'checked="checked"' : '';

    // set variables
    $community_url = get_bloginfo('url');
    $url = sprintf('%s%s', $community_url, '/wp-content/plugins/sroups');
    $apiurl = sprintf('%s%s', $community_url, '/wp-content/plugins/sroups/rest');
    $uid = get_option('sroups_user_id');
    $current_user = wp_get_current_user();
    $wpid = $current_user->ID;

    if (empty($community_url) || empty($url) || empty($apiurl) || empty($uid)) {
        if($creation)
            sr_add_message("Failed to create Sroups, Please try again.", true);
        else
            sr_add_message("Failed to update Sroups, Please try again.", true);
        return;
    }

    $title = ($creation) ? 'Sroups Creation' : 'Update Sroups Settings';
    $submitButtonLabel = ($creation) ? 'Create My Sroups' : 'Update Sroups Settings';

    $deactivationOption = <<<XHTML
        <tr valign="top">
            <th scope="row">Visibility Options</th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span>Visibility Options</span></legend>
                    <label><input type="radio" $visibilityBand value="band" name="sr_visibility" />Display as footer band</label><br />
                    <legend class="screen-reader-text"><span>Visibility Options</span></legend>
                    <label><input type="radio" $visibilityPage value="page" name="sr_visibility" />Display as page</label><br />
                    <legend class="screen-reader-text"><span>Visibility Options</span></legend>
                    <label><input type="radio" $visibilityBoth value="both" name="sr_visibility" />Display both footer band and the page</label><br />
                    <legend class="screen-reader-text"><span>Visibility Options</span></legend>
                    <label><input type="radio" $visibilityHide value="hide" name="sr_visibility" />Hide Sroups</label><br />
                </fieldset>
            </td>
        </tr>
XHTML;

    sr_admin_pageHeader();

    $outputBuffer = <<<XHTML
<form id="sroups_creation_form" action="" method="post">
<table class="form-table">
    <tbody>
        <tr valign="top">
            <th scope="row">Sroups Package</th>
            <td>
                <fieldset id="fs_sroups_package">
                    <legend class="screen-reader-text"><span>Sroups Package</span></legend>
                </fieldset>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Sroups Theme</th>
            <td>
                <fieldset id="fs_sroups_theme">
                    <legend class="screen-reader-text"><span>Sroups Theme</span></legend>
                </fieldset>
            </td>
        </tr>
        $deactivationOption
    </tbody>
</table>
<p class="submit" style="padding:0">
    <input type="hidden" id="sr_create_community_url" name="sr_create_community_url" value="$community_url" />
    <input type="hidden" id="sr_create_url" name="sr_create_url" value="$url" />
    <input type="hidden" id="sr_create_apiurl" name="sr_create_apiurl" value="$apiurl" />
    <input type="hidden" id="sr_create_communityType" name="sr_create_communityType" value="3" />
    <input type="hidden" id="sr_create_uid" name="sr_create_uid" value="$wpid" />
    <input type="hidden" id="sr_create_sroups_uid" name="sr_create_sroups_uid" value="$uid" />
    <input class="button-primary" type="submit" value="$submitButtonLabel" id="sr_submit_creation" name="sr_submit_creation" />
</p>
</form>
XHTML;

    // if it is an update request, pass sid
    $_sid = get_option('sroups_id');
    $updateSidAddition = (!$creation) ? 'sid: ' . $_sid . ',' : '';

    $outputBuffer .= <<<JS
    <script type="text/javascript">
        var _sr = jQuery.noConflict();
        _sr(document).ready(function() {
            _sr.getJSON(getPostUrl() + "?url=" + Url.encode(Base64.encode(getUrl("/index/package"))), function(data) {
                _sr.each(data, function(k, v) {
                    _sr("fieldset#fs_sroups_package").append(
                        _sr("<label>").html('<input type="radio" value="' + v.id + '" name="sr_create_package" id="sr_create_package_' + v.id + '" />' + v.description + '</label>')
                    ).append(_sr("<br />")); 
                });
                _sr.ajax({
                    url: getGetUrl() + "?url=" + Url.encode(Base64.encode(getUrl("/sroups/sid/$_sid"))),
                    dataType: "json",
                    success: function(data) {
                        if (data && data.sroups_packageId) {
                            _sr("input#sr_create_package_" + data.sroups_packageId).attr("checked", "checked");
                        } else {
                            _sr("fieldset#fs_sroups_package label:eq(0)").children("input").attr("checked", "checked");
                        }
                    },
                    error: function(data) {
                        _sr("fieldset#fs_sroups_package label:eq(0)").children("input").attr("checked", "checked");
                    }
                });
            });
            _sr.getJSON(getPostUrl() + "?url=" + Url.encode(Base64.encode(getUrl("/index/theme"))), function(data) {
                _sr.each(data, function(k, v) {
                    _sr("fieldset#fs_sroups_theme").append(
                        _sr("<label>").html('<input type="radio" value="' + v.id + '" name="sr_create_theme" id="sr_create_theme_' + v.id + '" />' + v.name + '</label>')
                    ).append(_sr("<br />")); 
                });
                _sr.ajax({
                    url: getGetUrl() + "?url=" + Url.encode(Base64.encode(getUrl("/sroups/sid/$_sid"))),
                    dataType: "json",
                    success: function(data) {
                        if (data && data.sroups_themeId) {
                            _sr("input#sr_create_theme_" + data.sroups_themeId).attr("checked", "checked");
                        } else {
                            _sr("fieldset#fs_sroups_theme label:eq(0)").children("input").attr("checked", "checked");
                        }
                    },
                    error: function(data) {
                        _sr("fieldset#fs_sroups_theme label:eq(0)").children("input").attr("checked", "checked");
                    }
                });
            });
            _sr("input#sr_submit_creation").click(function() {
                var requestData = {
                    $updateSidAddition
                    community_uid: _sr("input#sr_create_uid").val(),
                    community_url: _sr("input#sr_create_community_url").val(),
                    community_sroupsUrl: _sr("input#sr_create_url").val(),
                    community_sroupsRestApiUrl: _sr("input#sr_create_apiurl").val(),
                    sroups_uid: _sr("input#sr_create_sroups_uid").val(),
                    sroups_communityType: _sr("input#sr_create_communityType").val(),
                    sroups_communityTheme: _sr("input[name=sr_create_theme]:checked").val(),
                    sroups_packageType: _sr("input[name=sr_create_package]:checked").val()
                };
                _sr.ajax({
                    type: "POST",
                    url: getPostUrl() + "?url=" + Url.encode(Base64.encode(getUrl("/sroups"))),
                    data: requestData,
                    success: function (data) {
                        if (data && data.sroups_id) {
                            _sr("form#sroups_creation_form").append(
                                _sr("<input>").attr({
                                    type: 'hidden',
                                    name: 'sid'
                                }).val(data.sroups_id)
                            ).append(
                                _sr("<input>").attr({
                                    type: 'hidden',
                                    name: 'sig'
                                }).val(data.sroups_sig)
                            ).submit();
                        }
                    },
                    error: function (e) {
                        var messages = _sr.parseJSON(e.responseText);
                        for(var i = 0; i < messages.errors.length; i++) {
                            sr_admin_errorMessages( messages.errors[i].message );
                        }
                        return false;
                    },
                    dataType: "json",
                    async: false
                });
                return false;
            });
        });
    </script>
JS;

    echo $outputBuffer;

    sr_admin_pageFooter();
}
// <editor-fold defaultstate="collapsed" desc="commonScript">
function sr_common_script() {
    $sroupsApiUrl = SROUPS_API_URL;
    $outputBuffer = <<<JS
<style>
    #sroups-icon-options {
        background:url("../wp-content/plugins/sroups/images/icon.png") no-repeat transparent;
    }
</style>
<script type="text/javascript">
        function sr_admin_errorMessages(msg) {
            _sr("#sr_errors").empty().html('<div class="error" id="message">' +
                                                '<p><strong>' + msg + '</strong></p>' +
                                            '</div>');
                            return false;
        }

        /**
        *
        *  Base64 encode / decode
        *  http://www.webtoolkit.info/
        *
        **/
        var Base64 = {
            // private property
            _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
            // public method for encoding
            encode : function (input) {
                var output = "";
                var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
                var i = 0;
                input = Base64._utf8_encode(input);
                while (i < input.length) {

                    chr1 = input.charCodeAt(i++);
                    chr2 = input.charCodeAt(i++);
                    chr3 = input.charCodeAt(i++);

                    enc1 = chr1 >> 2;
                    enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                    enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                    enc4 = chr3 & 63;

                    if (isNaN(chr2)) {
                        enc3 = enc4 = 64;
                    } else if (isNaN(chr3)) {
                        enc4 = 64;
                    }

                    output = output +
                    this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
                    this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

                }

                return output;
            },
            // public method for decoding
            decode : function (input) {
                var output = "";
                var chr1, chr2, chr3;
                var enc1, enc2, enc3, enc4;
                var i = 0;

                input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

                while (i < input.length) {

                    enc1 = this._keyStr.indexOf(input.charAt(i++));
                    enc2 = this._keyStr.indexOf(input.charAt(i++));
                    enc3 = this._keyStr.indexOf(input.charAt(i++));
                    enc4 = this._keyStr.indexOf(input.charAt(i++));

                    chr1 = (enc1 << 2) | (enc2 >> 4);
                    chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                    chr3 = ((enc3 & 3) << 6) | enc4;

                    output = output + String.fromCharCode(chr1);

                    if (enc3 != 64) {
                        output = output + String.fromCharCode(chr2);
                    }
                    if (enc4 != 64) {
                        output = output + String.fromCharCode(chr3);
                    }

                }

                output = Base64._utf8_decode(output);

                return output;

            },
            // private method for UTF-8 encoding
            _utf8_encode : function (string) {
                var utftext = "";
                for (var n = 0; n < string.length; n++) {

                    var c = string.charCodeAt(n);

                    if (c < 128) {
                        utftext += String.fromCharCode(c);
                    }
                    else if((c > 127) && (c < 2048)) {
                        utftext += String.fromCharCode((c >> 6) | 192);
                        utftext += String.fromCharCode((c & 63) | 128);
                    }
                    else {
                        utftext += String.fromCharCode((c >> 12) | 224);
                        utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                        utftext += String.fromCharCode((c & 63) | 128);
                    }

                }

                return utftext;
            },
            // private method for UTF-8 decoding
            _utf8_decode : function (utftext) {
                var string = "";
                var i = 0;
                var c = c1 = c2 = 0;
                while ( i < utftext.length ) {
                    c = utftext.charCodeAt(i);
                    if (c < 128) {
                        string += String.fromCharCode(c);
                        i++;
                    }
                    else if((c > 191) && (c < 224)) {
                        c2 = utftext.charCodeAt(i+1);
                        string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                        i += 2;
                    }
                    else {
                        c2 = utftext.charCodeAt(i+1);
                        c3 = utftext.charCodeAt(i+2);
                        string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                        i += 3;
                    }
                }
                return string;
            }
        }
        /**
        *
        *  URL encode / decode
        *  http://www.webtoolkit.info/
        *
        **/
        var Url = {

            // public method for url encoding
            encode : function (string) {
                return escape(this._utf8_encode(string));
            },

            // public method for url decoding
            decode : function (string) {
                return this._utf8_decode(unescape(string));
            },

            // private method for UTF-8 encoding
            _utf8_encode : function (string) {
                var utftext = "";

                for (var n = 0; n < string.length; n++) {

                    var c = string.charCodeAt(n);

                    if (c < 128) {
                        utftext += String.fromCharCode(c);
                    }
                    else if((c > 127) && (c < 2048)) {
                        utftext += String.fromCharCode((c >> 6) | 192);
                        utftext += String.fromCharCode((c & 63) | 128);
                    }
                    else {
                        utftext += String.fromCharCode((c >> 12) | 224);
                        utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                        utftext += String.fromCharCode((c & 63) | 128);
                    }

                }

                return utftext;
            },

            // private method for UTF-8 decoding
            _utf8_decode : function (utftext) {
                var string = "";
                var i = 0;
                var c = c1 = c2 = 0;

                while ( i < utftext.length ) {

                    c = utftext.charCodeAt(i);

                    if (c < 128) {
                        string += String.fromCharCode(c);
                        i++;
                    }
                    else if((c > 191) && (c < 224)) {
                        c2 = utftext.charCodeAt(i+1);
                        string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                        i += 2;
                    }
                    else {
                        c2 = utftext.charCodeAt(i+1);
                        c3 = utftext.charCodeAt(i+2);
                        string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                        i += 3;
                    }

                }

                return string;
            }

        }
        /**
        *
        *  MD5 (Message-Digest Algorithm)
        *  http://www.webtoolkit.info/
        *
        **/
        var MD5 = function (string) {

            function RotateLeft(lValue, iShiftBits) {
                return (lValue<<iShiftBits) | (lValue>>>(32-iShiftBits));
            }

            function AddUnsigned(lX,lY) {
                var lX4,lY4,lX8,lY8,lResult;
                lX8 = (lX & 0x80000000);
                lY8 = (lY & 0x80000000);
                lX4 = (lX & 0x40000000);
                lY4 = (lY & 0x40000000);
                lResult = (lX & 0x3FFFFFFF)+(lY & 0x3FFFFFFF);
                if (lX4 & lY4) {
                    return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
                }
                if (lX4 | lY4) {
                    if (lResult & 0x40000000) {
                        return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
                    } else {
                        return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
                    }
                } else {
                    return (lResult ^ lX8 ^ lY8);
                }
            }

            function F(x,y,z) { return (x & y) | ((~x) & z); }
            function G(x,y,z) { return (x & z) | (y & (~z)); }
            function H(x,y,z) { return (x ^ y ^ z); }
            function I(x,y,z) { return (y ^ (x | (~z))); }

            function FF(a,b,c,d,x,s,ac) {
                a = AddUnsigned(a, AddUnsigned(AddUnsigned(F(b, c, d), x), ac));
                return AddUnsigned(RotateLeft(a, s), b);
            };

            function GG(a,b,c,d,x,s,ac) {
                a = AddUnsigned(a, AddUnsigned(AddUnsigned(G(b, c, d), x), ac));
                return AddUnsigned(RotateLeft(a, s), b);
            };

            function HH(a,b,c,d,x,s,ac) {
                a = AddUnsigned(a, AddUnsigned(AddUnsigned(H(b, c, d), x), ac));
                return AddUnsigned(RotateLeft(a, s), b);
            };

            function II(a,b,c,d,x,s,ac) {
                a = AddUnsigned(a, AddUnsigned(AddUnsigned(I(b, c, d), x), ac));
                return AddUnsigned(RotateLeft(a, s), b);
            };

            function ConvertToWordArray(string) {
                var lWordCount;
                var lMessageLength = string.length;
                var lNumberOfWords_temp1=lMessageLength + 8;
                var lNumberOfWords_temp2=(lNumberOfWords_temp1-(lNumberOfWords_temp1 % 64))/64;
                var lNumberOfWords = (lNumberOfWords_temp2+1)*16;
                var lWordArray=Array(lNumberOfWords-1);
                var lBytePosition = 0;
                var lByteCount = 0;
                while ( lByteCount < lMessageLength ) {
                    lWordCount = (lByteCount-(lByteCount % 4))/4;
                    lBytePosition = (lByteCount % 4)*8;
                    lWordArray[lWordCount] = (lWordArray[lWordCount] | (string.charCodeAt(lByteCount)<<lBytePosition));
                    lByteCount++;
                }
                lWordCount = (lByteCount-(lByteCount % 4))/4;
                lBytePosition = (lByteCount % 4)*8;
                lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80<<lBytePosition);
                lWordArray[lNumberOfWords-2] = lMessageLength<<3;
                lWordArray[lNumberOfWords-1] = lMessageLength>>>29;
                return lWordArray;
            };

            function WordToHex(lValue) {
                var WordToHexValue="",WordToHexValue_temp="",lByte,lCount;
                for (lCount = 0;lCount<=3;lCount++) {
                    lByte = (lValue>>>(lCount*8)) & 255;
                    WordToHexValue_temp = "0" + lByte.toString(16);
                    WordToHexValue = WordToHexValue + WordToHexValue_temp.substr(WordToHexValue_temp.length-2,2);
                }
                return WordToHexValue;
            };

            function Utf8Encode(string) {
                var utftext = "";

                for (var n = 0; n < string.length; n++) {

                    var c = string.charCodeAt(n);

                    if (c < 128) {
                        utftext += String.fromCharCode(c);
                    }
                    else if((c > 127) && (c < 2048)) {
                        utftext += String.fromCharCode((c >> 6) | 192);
                        utftext += String.fromCharCode((c & 63) | 128);
                    }
                    else {
                        utftext += String.fromCharCode((c >> 12) | 224);
                        utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                        utftext += String.fromCharCode((c & 63) | 128);
                    }

                }

                return utftext;
            };

            var x=Array();
            var k,AA,BB,CC,DD,a,b,c,d;
            var S11=7, S12=12, S13=17, S14=22;
            var S21=5, S22=9 , S23=14, S24=20;
            var S31=4, S32=11, S33=16, S34=23;
            var S41=6, S42=10, S43=15, S44=21;

            string = Utf8Encode(string);

            x = ConvertToWordArray(string);

            a = 0x67452301; b = 0xEFCDAB89; c = 0x98BADCFE; d = 0x10325476;

            for (k=0;k<x.length;k+=16) {
                AA=a; BB=b; CC=c; DD=d;
                a=FF(a,b,c,d,x[k+0], S11,0xD76AA478);
                d=FF(d,a,b,c,x[k+1], S12,0xE8C7B756);
                c=FF(c,d,a,b,x[k+2], S13,0x242070DB);
                b=FF(b,c,d,a,x[k+3], S14,0xC1BDCEEE);
                a=FF(a,b,c,d,x[k+4], S11,0xF57C0FAF);
                d=FF(d,a,b,c,x[k+5], S12,0x4787C62A);
                c=FF(c,d,a,b,x[k+6], S13,0xA8304613);
                b=FF(b,c,d,a,x[k+7], S14,0xFD469501);
                a=FF(a,b,c,d,x[k+8], S11,0x698098D8);
                d=FF(d,a,b,c,x[k+9], S12,0x8B44F7AF);
                c=FF(c,d,a,b,x[k+10],S13,0xFFFF5BB1);
                b=FF(b,c,d,a,x[k+11],S14,0x895CD7BE);
                a=FF(a,b,c,d,x[k+12],S11,0x6B901122);
                d=FF(d,a,b,c,x[k+13],S12,0xFD987193);
                c=FF(c,d,a,b,x[k+14],S13,0xA679438E);
                b=FF(b,c,d,a,x[k+15],S14,0x49B40821);
                a=GG(a,b,c,d,x[k+1], S21,0xF61E2562);
                d=GG(d,a,b,c,x[k+6], S22,0xC040B340);
                c=GG(c,d,a,b,x[k+11],S23,0x265E5A51);
                b=GG(b,c,d,a,x[k+0], S24,0xE9B6C7AA);
                a=GG(a,b,c,d,x[k+5], S21,0xD62F105D);
                d=GG(d,a,b,c,x[k+10],S22,0x2441453);
                c=GG(c,d,a,b,x[k+15],S23,0xD8A1E681);
                b=GG(b,c,d,a,x[k+4], S24,0xE7D3FBC8);
                a=GG(a,b,c,d,x[k+9], S21,0x21E1CDE6);
                d=GG(d,a,b,c,x[k+14],S22,0xC33707D6);
                c=GG(c,d,a,b,x[k+3], S23,0xF4D50D87);
                b=GG(b,c,d,a,x[k+8], S24,0x455A14ED);
                a=GG(a,b,c,d,x[k+13],S21,0xA9E3E905);
                d=GG(d,a,b,c,x[k+2], S22,0xFCEFA3F8);
                c=GG(c,d,a,b,x[k+7], S23,0x676F02D9);
                b=GG(b,c,d,a,x[k+12],S24,0x8D2A4C8A);
                a=HH(a,b,c,d,x[k+5], S31,0xFFFA3942);
                d=HH(d,a,b,c,x[k+8], S32,0x8771F681);
                c=HH(c,d,a,b,x[k+11],S33,0x6D9D6122);
                b=HH(b,c,d,a,x[k+14],S34,0xFDE5380C);
                a=HH(a,b,c,d,x[k+1], S31,0xA4BEEA44);
                d=HH(d,a,b,c,x[k+4], S32,0x4BDECFA9);
                c=HH(c,d,a,b,x[k+7], S33,0xF6BB4B60);
                b=HH(b,c,d,a,x[k+10],S34,0xBEBFBC70);
                a=HH(a,b,c,d,x[k+13],S31,0x289B7EC6);
                d=HH(d,a,b,c,x[k+0], S32,0xEAA127FA);
                c=HH(c,d,a,b,x[k+3], S33,0xD4EF3085);
                b=HH(b,c,d,a,x[k+6], S34,0x4881D05);
                a=HH(a,b,c,d,x[k+9], S31,0xD9D4D039);
                d=HH(d,a,b,c,x[k+12],S32,0xE6DB99E5);
                c=HH(c,d,a,b,x[k+15],S33,0x1FA27CF8);
                b=HH(b,c,d,a,x[k+2], S34,0xC4AC5665);
                a=II(a,b,c,d,x[k+0], S41,0xF4292244);
                d=II(d,a,b,c,x[k+7], S42,0x432AFF97);
                c=II(c,d,a,b,x[k+14],S43,0xAB9423A7);
                b=II(b,c,d,a,x[k+5], S44,0xFC93A039);
                a=II(a,b,c,d,x[k+12],S41,0x655B59C3);
                d=II(d,a,b,c,x[k+3], S42,0x8F0CCC92);
                c=II(c,d,a,b,x[k+10],S43,0xFFEFF47D);
                b=II(b,c,d,a,x[k+1], S44,0x85845DD1);
                a=II(a,b,c,d,x[k+8], S41,0x6FA87E4F);
                d=II(d,a,b,c,x[k+15],S42,0xFE2CE6E0);
                c=II(c,d,a,b,x[k+6], S43,0xA3014314);
                b=II(b,c,d,a,x[k+13],S44,0x4E0811A1);
                a=II(a,b,c,d,x[k+4], S41,0xF7537E82);
                d=II(d,a,b,c,x[k+11],S42,0xBD3AF235);
                c=II(c,d,a,b,x[k+2], S43,0x2AD7D2BB);
                b=II(b,c,d,a,x[k+9], S44,0xEB86D391);
                a=AddUnsigned(a,AA);
                b=AddUnsigned(b,BB);
                c=AddUnsigned(c,CC);
                d=AddUnsigned(d,DD);
            }

            var temp = WordToHex(a)+WordToHex(b)+WordToHex(c)+WordToHex(d);

            return temp.toLowerCase();
        }
        
        var apiUrl = '$sroupsApiUrl';
        function getUrl(url) {
            return apiUrl + url;
        }

        function getPostUrl(url) {
            return "../wp-content/plugins/sroups/proxy/post.php";
        }

        function getGetUrl(url) {
            return "../wp-content/plugins/sroups/proxy/get.php";
        }
</script>
JS;

    echo $outputBuffer;
}

// </editor-fold>

function sr_compatibilityCheck() {
    return ((version_compare(PHP_VERSION, SROUPS_REQUIRED_PHP_VERSION) >= 0) && (function_exists('curl_init'))) ? true : false;
}

function sr_admin_compatibility() {
    ?>
    <div class="wrap" style="margin-top:50px;clear:both;">
    	<h2>Compatibility:</h2>
    	<table cellspacing="0" class="widefat">
            <tbody>
                    <?php if(version_compare(PHP_VERSION, SROUPS_REQUIRED_PHP_VERSION) >= 0):?>
                    <tr class="alternate">
                            <td class="import-system row-title" style="color: green;width:200px;">PHP Version</td>
                            <td class="desc">Success! You're using PHP version <?php echo PHP_VERSION; ?>.</td>
                    </tr>
                    <?php else:?>
                    <tr class="alternate">
                            <td class="import-system row-title" style="color: red;width:200px;">PHP Version</td>
                            <td class="desc">Fail! Minimum required PHP version is <?php echo PLS_REQUIRED_PHP_VERSION?>. You're using version <?php echo PHP_VERSION?>.</td>
                    </tr>
                    <?php endif;?>
                    <?php if(function_exists('curl_init')) :?>
                    <tr class="alternate">
                            <td class="import-system row-title" style="color: green;">cURL functions</td>
                            <td class="desc">Success! Your PHP installation has cURL support.</td>
                    </tr>
                    <?php else:?>
                    <tr class="alternate">
                            <td class="import-system row-title" style="color: red;">cURL functions</td>
                            <td class="desc">Fail! I require your php installation to have cURL support.</td>
                    </tr>
                    <?php endif;?>
            </tbody>
        </table>
    </div>
    <?php
}

function sr_admin_information() {
    ?>
    <div class="wrap" style="margin-top:50px;clear:both;">
    	<h2>Information Center:</h2>
        <p>
            Sroups is the short form of “Social Groups”. As its name describes the
            whole idea, Sroups’ goal is all about turning online groups (blogs, groups,
            fan pages, discussion forums etc.) into a more social platform. As Sroups
            is exclusive to the community, all of the things going on in the virtual
            world will be related to your blog’s reason of existence.
        </p>
        <p>
            To use Sroups plugin on your blog, you'll have to be a srou.ps member. You
            can both register from http://srou.ps or using our plugin as described in
            the "Installation" section.
        </p>
        <p>
            For more information about Sroups, see also: http://srou.ps
        </p>
    </div>
    <?php
}

function sr_admin_deactivation() {
    delete_option('sroups_user_id', (int) trim($_POST['sr_user_id']));
    delete_option('sroups_visibility', 'band'); // the default option for visibility is the band
    delete_option ('sroups_id', $_POST['sroups_id']);
    delete_option ('sroups_sig', $_POST['sroups_sig']);
    // delete if there is a sroups page
    sr_disableSroupsPage();
}

