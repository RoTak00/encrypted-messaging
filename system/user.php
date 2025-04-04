<?php

class User
{

    private $registry = [];


    public $id = null;
    public $name = null;

    public $email = null;

    public function LoggedIn()
    {
        return $this->id;
    }

    public function __construct($registry)
    {
        $this->registry = $registry;

        $this->RemoveExpiredAuthTokens("1 HOUR");

        if (empty($this->session->data['auth_token'])) {
            unset($this->session->data['auth_token']);
            return;
        }

        $token = $this->GetAuthToken($this->session->data['auth_token']);

        if (is_null($token)) {
            unset($this->session->data['auth_token']);
            return;
        }



        if ($this->ValidateAuthToken($token)) {

            // this should actualy check the user etc.
            $this->id = $token['user_id'];


            $this->name = "admin";
        } else {

            $this->RemoveAuthToken($token);

            unset($this->session->data['auth_token']);
            $this->id = null;
            $this->username = null;
        }


    }

    public function __get($name)
    {
        // Fetch from the registry if it exists
        if (isset($this->registry->registry[$name])) {
            return $this->registry->registry[$name];
        }
        return null; // Or throw an error if you want strict behavior
    }

    public function login($email, $password)
    {
        $sqlq = "SELECT id, name, email, password FROM users WHERE email = '" . $this->db->escape($email) . "'";

        $result = $this->db->query($sqlq);

        if (!($result->num_rows > 0)) {
            return false;
        }

        $result = $result->fetch_assoc();

        if (password_verify($password, $result['password'])) {

            $token = $this->InsertAuthToken($result['id']);

            $this->id = $result['id'];

            $this->name = $result['name'];

            $this->email = $result['email'];

            return true;
        }
        return false;

    }

    public function logout()
    {
        $this->RemoveAuthToken($this->session->data['auth_token']);
        unset($this->session->data['auth_token']);

        $this->id = null;
        $this->name = null;
        $this->email = null;

    }




    private function InsertAuthToken($id)
    {
        $token = uniqid("auth_");

        $sqlq = "INSERT INTO auth_session(token, user_id, original_ip) VALUES ('" . $this->db->escape($token) . "', '" . (int) $id . "', '" . $this->db->escape($this->request->ip) . "')";

        $result = $this->db->query($sqlq);


        $this->session->data['auth_token'] = $token;

        return $token;
    }

    // selects the authentication token from the database
// or returns null if none are found
// null allows to be used with coercion (??) operator
    private function GetAuthToken($token)
    {
        $sqlq = "SELECT a.valid, a.token, a.user_id, u.name, u.email FROM auth_session a INNER JOIN 
        users u ON u.id = a.user_id WHERE token = '" . $token . "' AND original_ip = '" . $this->db->escape($this->request->ip) . "'";

        $result = $this->db->query($sqlq);


        if (!($result->num_rows > 0)) {
            return null;
        }

        if ($result->num_rows > 0) {
            $result = $result->fetch_assoc();
        }

        return ($result['valid'] == 1 ? $result : null);
    }

    // Validate an authentication token array (defined as "token": token, "valid": isvalid)
// Can also be null if the token was not found at all
// If valid, authenticate user
// If not valid, alert user that session has expired
// If null, an error has occured or malicious use exists
    private function ValidateAuthToken($authToken, $unsetSessionTokenOnInvalid = true)
    {

        // probably malicious use, the tokens added to the session should always be existent in the database, even if invalid
        if (is_null($authToken)) {
            session_unset();
            die();
        }

        // authentication token does exist but is invalid because too much time has passed
        // redirect the user to the login page and ask to reconnect
        if ($authToken['valid'] == 0) {
            if ($unsetSessionTokenOnInvalid)
                unset($this->session->data['auth_token']);

            // transform the previous query string to allow the browser to know where to redirect going forward
            // it needs to be transformed in order to fit into one single value
            // so / is turned to custom &sl;
            header("location: login");
            die();
        }

        // authentication token exists and is valid, login success
        // reset last validation date of auth token 
        if ($authToken['valid'] == 1) {
            $this->ResetValidationToken($authToken['token']);
            return true;
        }


    }

    // Reset the last_validated date for the authentication token
// This means that it will not be invalidated for the next hour
    private function ResetValidationToken($token)
    {
        $sqlq = "UPDATE auth_session SET last_validated = NOW() WHERE token = '" . $token . "'";

        $result = $this->db->query($sqlq);

        return true;
    }

    // checks for auth tokens older than interval
    // and marks them as invalid
    private function RemoveExpiredAuthTokens($interval)
    {
        $sqlq = "UPDATE auth_session SET valid = 0 WHERE last_validated < NOW() - INTERVAL " . $interval;

        $result = $this->db->query($sqlq);
        return true;
    }

    private function RemoveAuthToken($token)
    {
        $sqlq = "UPDATE auth_session SET valid = 0 WHERE token = '" . $token . "'";

        $result = $this->db->query($sqlq);

        return true;
    }


}