<?php



error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once '../include/DbHandler.php';
require_once '../include/PassHash.php';
require '../../vendor/autoload.php';

$app = new Slim\App();

$app->get('/hello', function ($request, $response) {
    return $response->write("Hello slim is working now ");
});

/**
 * ----------- METHODS WITHOUT AUTHENTICATION ---------------------------------
 */
/**
 * User Registration
 * url - /register
 * method - POST
 * params - name, email, password
 */
$app->post('/register_user', function() use ($app) {

     $headers = request_headers();
    
 
        // if(!$authorized){ //key is false
                // dont return 403 if you request the home page
                // check for required params
            verifyRequiredParams(array('first_name', 'middle_name',
             'last_name', 'dob', 'height', 'weight', 'mobile_no', 'home_no','address', 'note',
              'member_type', 'date_of_join', 'email'));


            $response = array();

            // reading post params
            $first_name = $app->request->post('first_name');
            $middle_name = $app->request->post('middle_name');
            $last_name = $app->request->post('last_name');
            $dob = $app->request->post('dob');
            $height = $app->request->post('height');
            $weight = $app->request->post('weight');
            $mobile_no = $app->request->post('mobile_no');
            $home_no = $app->request->post('home_no');
            $address = $app->request->post('address');
            $note = $app->request->post('note');
            $member_type = $app->request->post('member_type');
            $date_of_join = $app->request->post('date_of_join');
            $email = $app->request->post('email');


            // validating email address
            validateEmail($email);

            // $db = new DbHandler();
            // $res = $db->createUser($name, $email, $password, $mobile_no);

            // if ($res == USER_CREATED_SUCCESSFULLY) {
            //     $response["error"] = false;
            //     $response["message"] = "You are successfully registered";
            // } else if ($res == USER_CREATE_FAILED) {
            //     $response["error"] = true;
            //     $response["message"] = "Oops! An error occurred while registereing";
            // } else if ($res == USER_ALREADY_EXISTED) {
            //     $response["error"] = true;
            //     $response["message"] = "Sorry, this email already existed";
            // }else if ($res == PHONE_ALREADY_EXISTED) {
            //     $response["error"] = true;
            //     $response["message"] = "Sorry, this phone no is already registered";
            // }
            // echo json response
            echoRespnse(201, $response);
        // }



            
        });

function request_headers() {
        $arh = array();
        $rx_http = '/\AHTTP_/';
        foreach($_SERVER as $key => $val) {
                if( preg_match($rx_http, $key) ) {
                        $arh_key = preg_replace($rx_http, '', $key);
                        $rx_matches = array();
                        // do string manipulations to restore the original letter case
                        $rx_matches = explode('_', $arh_key);
                        if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
                                foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
                                $arh_key = implode('-', $rx_matches);
                        }
                        $arh[$arh_key] = $val;
                }
        }
        return( $arh );
}


/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields) {


    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        // $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }



    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }



    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        // $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
}


/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

$app->run();