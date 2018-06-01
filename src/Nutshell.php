<?php

namespace Travis;

class Nutshell
{
    /**
     * Handle the API request.
     *
     * @param   string  $username
     * @param   string  $apikey
     * @param   string  $method
     * @param   array   $params
     * @return  object
     */
    public static function run($username, $apikey, $method, $params = [])
    {
        // set endpoint
        $endpoint = static::get_endpoint($username);

        // build payload
        $payload = [
            'method' => $method,
            'params' => $params,
            'id' => static::get_id(),
        ];

        // request
        return static::request($endpoint, $username, $apikey, $payload);
    }

    /**
     * Handle the uploading of files.
     *
     * @param   string  $username
     * @param   string  $apikey
     * @param   string  $entity_type
     * @param   string  $entity_id
     * @param   string  $file_name
     * @param   string  $file_path
     * @return  boolean
     */
    public static function upload_file($username, $apikey, $entity_type, $entity_id, $file_name, $file_path)
    {
        /*
        // The process for uploading a file to a lead or contact
        // involves multiple steps.  This helper method is designed
        // to automate those processes.  It will 1) notify Nutshell
        // that a file is coming, 2) will upload the file, and
        // 3) will download the file to validate success.

        // fix
        $entity_type = strtolower($entity_type);

        // catch error...
        if (!in_array($entity_type, ['lead', 'activity']))
        {
            // throw error
            throw new \Exception('Invalid entity type.');
        }

        // build payload
        $payload = [
            $entity_type.'Id' => $entity_id,
            'rev' => 'REV_IGNORE', // this ignores caching/revisions to the lead/contact bc we are just uploading a file
            $entity_type => [
                'file' => [
                    [
                        'entityType' => 'Files',
                        'name' => $file_name
                    ],
                ],
            ],
        ];

        // build method
        $method = 'edit'.ucfirst($entity_type);

        // request
        $response = static::run($username, $apikey, $method, $payload);

        // get URL for post
        $url = ex($response, 'result.file.uri');

        // catch error...
        if (!$url)
        {
            // throw error
            throw new \Exception('Something went wrong.');
        }

        // build payload
        $payload = [
            'file' => curl_file_create($file_path),
        ];

        // request
        $response = static::request($url, $username, $apikey, $method, $payload);
        */
    }

    /**
     * Return a request id.
     *
     * @return  string
     */
    protected static function get_id()
    {
        // return random unique string
        return md5(uniqid(true));
    }

    /**
     * Return URL for request.
     *
     * @param   string  $username
     * @return  string
     */
    protected static function get_endpoint($username)
    {
        // build endpoint
        $endpoint = 'https://api.nutshell.com/v1/json';

        // build payload
        $payload = [
            'method' => 'getApiForUsername',
            'params' => [
                'username' => $username,
            ],
            'id' => static::get_id(),
        ];

        // get request
        $response = static::request($endpoint, $username, null, $payload);

        // pull value
        $url = ex($response, 'result.api');

        // catch error...
        if (!$url)
        {
            // throw error
            throw new \Exception('Unable to determine endpoint.');
        }

        // return
        return 'https://'.$url.'/api/v1/json';
    }

    /**
     * Handle the API request.
     *
     * @param   string  $endpoint
     * @param   string  $username
     * @param   string  $apikey
     * @param   array   $payload
     * @return  object
     */
    protected static function request($endpoint, $username, $apikey, $payload)
    {
        // setup curl request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        if ($username and $apikey)
        {
            curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$apikey);
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        #curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // catch error...
        if (curl_errno($ch))
        {
            // report
            #$errors = curl_error($ch);

            // close
            curl_close($ch);

            // throw error
            throw new \Exception('Something went wrong.');

            // return false
            return false;
        }

        // close
        curl_close($ch);

        // decode response
        $response = json_decode($response);

        // catch error...
        if (!in_array($httpcode, [200, 201, 202]))
        {
            // throw error
            throw new \Exception(ex($response, 'error.message', 'Request failed with HTTP code '.$httpcode));

            // return false
            return false;
        }

        // return
        return $response;
    }
}