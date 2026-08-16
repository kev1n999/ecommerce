<?php 

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use App\Models\CostumerModel;

use Firebase\JWT\JWT;

class Authentication extends BaseController {
    private CostumerModel $costumerModel;

    public function __construct()
    {
        $this->costumerModel = new CostumerModel();
    }

    public function signup(): ResponseInterface
    {
        $data = $this->request->getJSON(true);

        if (empty($data)) {
            return $this->sendJson(400, [
                "error" => "MISSING_ARGUMENTS",
            ]);
        }

        $costumerExists = $this->costumerModel
            ->where('email', $data["email"])
            ->first();
        
        if (!empty($costumerExists)) {
            return $this->sendJson(409, [
                "error" => "USER_ALREADY_EXISTS",
            ]);
        }

        $this->costumerModel->insert([
            'name'          => $data['username'],
            'email'         => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'phone'         => $data['phone'],
            'is_active'     => true,
        ]);

        return $this->sendJSON(201, [
            "message" => "USER_CREATED",
        ]);
    }

    public function signin(): ResponseInterface
    {
        $data = $this->request->getJSON(true);

        if (!isset($data["email"]) || !isset($data["password"])) {
            return $this->sendJson(400, [
                "error" => "MISSING_ARGUMENTS",
            ]); 
        }

        $password = $data['password'];

        $costumerExists = $this->costumerModel
            ->where('email', $data['email'])
            ->first();

        if (empty($costumerExists)) {
            return $this->sendJson(409, [
                "error" => "USER_DO_NOT_EXISTS",
            ]);
        }

        if (!password_verify($password, $costumerExists['password_hash'])) {
            return $this->sendJson(401, [
                "error" => "INCORRECT_PASSWORD",
            ]);
        }

        $payload = [
            'sub' => $costumerExists['id'],
            'iat' => time(),
            'exp' => time() + 3600,
        ];

        $token = JWT::encode($payload, env("JWT_SECRET"), 'HS256');

        return $this->sendJson(200, [
            "token" => $token,
        ]);
    }
}