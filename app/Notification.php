<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $connection = "auth_connection";
    
    protected $fillable = [
        "to", "description", "read_at", "body", "reference_number", "reference_code"
    ];

    public function updateCode(string $type = 'ST') {
        if($this->reference_number == NULL || $this->reference_code == NULL) {            
            $this->update([
                "reference_number" => $this->generateRefNumber(),
                "reference_code" => $this->generateRefCode()
            ]);
        }

        return $this;
    }

    private function generateRefNumber() {
        $today = date('Ymd', strtotime($this->created_at));
        if(strlen($this->id) >= 4) {
            return "UN-EML-" . $today . "-" . $this->id;
        }

        $len = strlen($this->id);
        $randomNumberLen = 4 - $len;
        $extras = ['', '0', '00', '000', '0000'];

        return "UN-EML-" . $today . "-" . $extras[$randomNumberLen] . $this->id;
    }

    private function generateRefCode() {
        return implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 9999))), 0, 30), 4));
    }
}
