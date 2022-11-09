<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Transaction extends ResourceController{
    public function transfer(){
        $db = \Config\Database::connect();
        $input = json_decode($this->request->getBody(), true);
        $bearerToken = [];

        try
		{
            if($this->request->getServer('HTTP_AUTHORIZATION') !== ''){
                preg_match('/Bearer\s(\S+)/',  $this->request->getServer('HTTP_AUTHORIZATION'), $bearerToken);
                
                if(is_null($bearerToken) || empty($bearerToken)){
                    $db->transRollback();

                    $response = 
                    [
                        'status' => 400,
                        'message' => 'empty token',
                        'data' => ''
                    ];
            
                    return $this->respondCreated($response);
                }else{
                    try
                    {
                        $JWT = new \Firebase\JWT\JWT;
                        $JWTDecoded = $JWT::decode($bearerToken[1], new \Firebase\JWT\Key('rahasia', 'HS256'));

                        if($JWTDecoded->data->id == ''){
                            $response = 
                            [
                                'status' => 400,
                                'message' => 'invalid token',
                                'data' => ''
                            ];
                    
                            return $this->respondCreated($response);
                        }else{                        
                            $id = $db->query(
                            "
                            SELECT 
                                id
                            FROM 
                                user
                            WHERE
                                id = {$db->escape($JWTDecoded->data->id)}
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
                            
                            if
                            (
                                $input['nilai_transfer'] !== '' 
                                &&  
                                $input['bank_tujuan'] !== ''
                                &&  
                                $input['rekening_tujuan'] !== ''
                                &&  
                                $input['atasnama_tujuan'] !== ''
                                &&  
                                $input['bank_pengirim'] !== ''
                            )
                            {
                                // cek nilai transfer
                                if($input['nilai_transfer'] <= 0){
                                    $response = 
                                    [
                                        'status' => 400,
                                        'message' =>'invalid nilai transfer',
                                        'data' => ''
                                    ];
                    
                                    return $this->respondCreated($response);
                                }
                                // end cek nilai transfer

                                // cek bank tujuan
                                $id = $db->query(
                                "
                                SELECT 
                                    id
                                FROM 
                                    bank
                                WHERE
                                    nama = {$db->escape($input['bank_tujuan'])}
                                AND
                                    rekening = {$db->escape($input['rekening_tujuan'])}
                                AND
                                    atas_nama = {$db->escape($input['atasnama_tujuan'])}
                                "
                                )->getRow('id');
                                
                                if(empty($id)){
                                    $response = 
                                    [
                                        'status' => 400,
                                        'message' =>'invalid bank tujuan',
                                        'data' => ''
                                    ];
                    
                                    return $this->respondCreated($response);
                                }
                                // end cek bank tujuan

                                // cek bank pengirim
                                $bankPengirim = $db->query(
                                "
                                SELECT 
                                    id,
                                    bank,
                                    rekening,
                                    atas_nama
                                FROM 
                                    rekening_admin
                                WHERE
                                    bank = {$db->escape($input['bank_pengirim'])}
                                "
                                )->getRowArray();
                                
                                if(empty($bankPengirim)){
                                    $response = 
                                    [
                                        'status' => 400,
                                        'message' =>'invalid bank pengirim',
                                        'data' => ''
                                    ];
                    
                                    return $this->respondCreated($response);
                                }
                                // end cek bank pengirim

                                $UUID = $db->query("SELECT UUID() AS uuid")->getRow('uuid');

                                // !!! kode random untuk keperluan test saja !!!
                                $idTransaksi = rand(100000, 99999);
                                $kodeUnik = rand(100, 999);
                                $berlakuHingga = new \DateTime();
                                $berlakuHingga->modify('+1 day');

                                $data = 
                                [
                                    'id_transaksi' => 'TF' . date_format(date_create(date('ymd')), 'ymd') . $idTransaksi,
                                    'nilai_transfer' => $input['nilai_transfer'],
                                    'kode_unik' => $kodeUnik,
                                    'biaya_admin' => '0',
                                    'total_transfer' => intval($input['nilai_transfer']) + intval($kodeUnik),
                                    'bank_perantara' => $bankPengirim['bank'],
                                    'rekening_perantara' => $bankPengirim['rekening'],
                                    'berlaku_hingga' => date_format($berlakuHingga, 'Y-m-d') . 'T' . date_format($berlakuHingga->modify('+1 day'), 'H:i:s') . date_format($berlakuHingga->modify('+1 day'), 'O')
                                ];

                                $db->query(
                                "
                                INSERT INTO 
                                    transaksi_transfer
                                (
                                    id,
                                    id_transaksi,
                                    nilai_transfer,
                                    kode_unik,
                                    total_transfer,
                                    berlaku_hingga
                                )
                                VALUES
                                (
                                    {$db->escape($UUID)},
                                    {$db->escape($data['id_transaksi'])},
                                    {$db->escape($data['nilai_transfer'])},
                                    {$db->escape($data['kode_unik'])},
                                    {$db->escape($data['total_transfer'])},
                                    {$db->escape($data['berlaku_hingga'])}
                                )
                                "
                                );
                        
                                return $this->respondCreated($data);
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
                    }
                    catch(\Exception $e)
                    {
                        return 
                        [
                            'status' => 400,
                            'message' => $e->getMessage(),
                            'data' => ''
                        ];

                        return $this->respondCreated($response);
                    }
                }
            }else{
                $db->transRollback();

                $response = 
                [
                    'status' => 400,
                    'message' => 'empty authorization',
                    'data' => ''
                ];
        
                return $this->respondCreated($response);
            }
        }
		catch(\Exception $e)
		{
			$db->transRollback();

			$response = 
			[
				'status' => 400,
				'message' => $e->getMessage(),
				'data' => ''
			];
	
			return $this->respondCreated($response);
		}
    }
}