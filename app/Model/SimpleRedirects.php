<?php

namespace App\Model;
use App\Model\OmaModel;

class SimpleRedirects extends OmaModel
{
    protected $table = 'omatech_simple_redirects';
  
    public function __construct()
    {
      if (env('REDIRECTS_TABLE')!==$this->table)
      {
        $this->table=env('REDIRECTS_TABLE');
      }
    }
    
	
    public function recreateTable ($str=null)
    {   
        if ($str!=null) return parent::recreateTable($str);
           
        return parent::recreateTable('CREATE TABLE '.$this->table.' (
          `id` int(28) NOT NULL AUTO_INCREMENT,
          `original_uri` varchar(190) DEFAULT NULL,
          `redirect_uri` varchar(255) DEFAULT NULL,
          `created_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
          `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY '.$this->table.'_u1 (`original_uri`) USING BTREE
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;');
    }
}