<?php

namespace App\Model;
use DB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OmaModel extends Model
{

    public static function cleanKey($input)
    {
      $output = self::stripAccents($input); 
      $output = Str::before($output, '(');
      $output = trim($output);
      $output = str_replace(' ', '_', $output);
      $output= strtolower($output);

      return $output;
    }

    public static function stripAccents($str) {
        return strtr(utf8_decode($str), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    }

    public static function getCSVRecords ($file, $field_separator=',')
    {
        if (($handle = fopen($file, "r")) !== FALSE) {
          $keys = fgetcsv($handle, 0, $field_separator);
          $keys = array_map(['self', 'cleanKey'], $keys);
          $i = 1;
          $res=array();
          while (($line = fgetcsv($handle, 10000, $field_separator)) !== FALSE) 
          {
            $row=array_combine($keys, $line);
            if (!isset($row['created_at'])) $row['created_at']='now()';
            if (!isset($row['updated_at'])) $row['updated_at']='now()';
            $res[] = $row;
            $i++;
          }
          fclose($handle);
        }  
        //print_r($res);die;
        return $res;      
    }

    public static function getTableName()
    {
        return (new static())->getTable();
    }

    public static function quote($string)
    {
        return DB::connection()->getPdo()->quote($string);
    }

    public function recreateTable ($create_sql)
    {      
        DB::select('drop table if exists '.$this->getTableName());
        DB::select($create_sql);
    }

    public static function bulkImport($rows, $bulk_records = 1000)
    {
        $fields_array = array();
        if (!$rows) return;
        foreach ($rows[0] as $key => $val) {
            $fields_array[] = $key;
        }
        $fields = implode(',', $fields_array);

        $i = 1;
        $initial_sql = "insert into ".self::getTableName()." ($fields) values ";
        $sql = $initial_sql;
        $num_values = 0;
        foreach ($rows as $row) 
        {
            $sql .= '(';
            foreach ($row as $val) 
            {
                if (isset($val) && trim($val) != '' && !is_numeric($val) && $val != 'now()') 
                {
                    $val = mb_convert_encoding($val, "UTF-8", mb_detect_encoding($val, "UTF-8, ISO-8859-1, ISO-8859-15", true));
                    $val = self::quote(trim($val));
                }
                if (trim($val) == '') $val = 'null';

                $sql .= "$val,";
            }
            $sql = substr($sql, 0, -1);
            $sql .= "),";


            if ($i % $bulk_records == 0) { // ja portem 1000, executem
                $sql = substr($sql, 0, -1); // eliminem la ultima ,
                //echo "***$i - $sql!!!\n";
                DB::insert($sql);
                $sql = $initial_sql;
                $num_values = 0;
            } else {
                $num_values++;
                //echo '.';
            }
            $i++;
        }
        if ($num_values > 0) {
            $sql = substr($sql, 0, -1); // eliminem la ultima ,
            //echo "***$sql!!!\n";
            //$this->last_massive_sql = $sql;
            DB::insert($sql);
        }
        return $i;
    }
}
