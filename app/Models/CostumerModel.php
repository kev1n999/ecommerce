<?php 

namespace App\Models;

use CodeIgniter\Model;

class CostumerModel extends Model
{
    protected $table = 'ecommerce_costumer';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        "name",
        "email",
        "password_hash",
        "phone",
        "is_active",
    ];

    protected $returnType = "array";
}