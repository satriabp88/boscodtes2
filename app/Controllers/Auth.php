<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Auth extends ResourceController{
    public function login(){
        $db = \Config\Database::connect();
        $input = json_decode($this->request->getBody(), true);

        if($input['username'] !== '' &&  $input['password'] !== ''){
            $id = $db->query(
            "
            SELECT 
                id
            FROM 
                user
            WHERE
                username = {$db->escape($input['username'])}
            AND
                password = MD5({$db->escape($input['password'])})
            "
            )->getRow('id');

            if(empty($id)){
                $response = 
                [
                    'status' => 400,
                    'message' =>'invalid login',
                    'data' => ''
                ];

                return $this->respondCreated($response);
            }
        
            $data = 
            [
                'id' => $id
            ];

            $tokenData = $this->tokenGet($data);

            if($tokenData['status'] == 400){
                $response = 
                [
                    'status' => 400,
                    'message' => $tokenData['message'],
                    'data' => ''
                ];

                return $this->respondCreated($response);
            }else{
                $response = 
                [
                    'accessToken' => $tokenData['data']['accessToken'],
                    'refreshToken' => $tokenData['data']['refreshToken']
                ];
            
                return $this->respondCreated($response);
            }
        }else{
            $response = 
            [
                'status' => 400,
                'message' => 'empty input',
                'data' => ''
            ];

            return $this->respondCreated($response);
        }
    }

    public function updateToken(){
        $db = \Config\Database::connect();
        $input = json_decode($this->request->getBody(), true);

        if($input['token'] !== ''){
            try
            {
                $JWT = new \Firebase\JWT\JWT;
                $JWTDecoded = $JWT::decode($input['token'], new \Firebase\JWT\Key('rahasia', 'HS256'));

                $tokenData = $this->tokenGet($JWTDecoded->data);

                if($tokenData['status'] == 400){
                    $response = 
                    [
                        'status' => 400,
                        'message' => $tokenData['message'],
                        'data' => ''
                    ];

                    return $this->respondCreated($response);
                }else{
                    $response = 
                    [
                        'accessToken' => $tokenData['data']['accessToken'],
                        'refreshToken' => $tokenData['data']['refreshToken']
                    ];
                
                    return $this->respondCreated($response);
                }
                }
            catch(\Exception $e)
            {
                return 
                [
                    'status' => 400,
                    'message' => $e->getMessage(),
                    'data' => ''
                ];
            }
        }else{
            $response = 
            [
                'status' => 400,
                'message' =>'invalid token',
                'data' => ''
            ];

            return $this->respondCreated($response);
        }
    }

    private function tokenGet($data){        
        $accessTokenPayload = 
        [
            'iss' => base_url(),
            'aud' => base_url(),
            'iat' => time(),
            'nbf' => time(),
            'exp' =>  time() + 3600,
            'data' => $data
        ];

        $refreshTokenPayload = 
        [
            'iss' => base_url(),
            'aud' => base_url(),
            'iat' => time(),
            'nbf' => time(),
            'exp' =>  time() + 1000000,
            'data' => $data
        ];

        try
        {
            $JWT = new \Firebase\JWT\JWT;
            $accessToken = $JWT::encode($accessTokenPayload, 'rahasia', 'HS256');
            $refreshToken = $JWT::encode($refreshTokenPayload, 'rahasia', 'HS256');

            return 
            [
                'status' => 200,
                'message' => '',
                'data' => 
                [
                    'accessToken' => $accessToken,
                    'refreshToken' => $refreshToken
                ]
            ];
        }
        catch(\Exception $e)
        {
            return 
            [
                'status' => 400,
                'message' => $e->getMessage(),
                'data' => ''
            ];
        }
    }
}