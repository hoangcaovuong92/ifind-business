<?php
/*
Source: https://www.techiesbadi.in/2017/03/How-to-Generate-QR-Code-using-Google-Chart-API-in-PHP.html
To create the QR code you need to include the qrcode.php file in your index.php file
include "qrcode.php";

 // Create QRcode object
 $qc = new QRCODE();
 // create text QR code
 $qc->TEXT("TechiesBadi");
 // render QR code  
 $qc->QRCODE('link', 400,"qrcode.png");

Like this you can generate the remainig Qrcodes.

// URL QR code 
$qc->URL('www.techiesbadi.in');

// EMAIL QR code
$qc->EMAIL('info@techiesbadi.in', 'SUBJECT', 'MESSAGE');

// PHONE QR code 
$qc->PHONE('PHONENUMBER');

// SMS QR code 
$qc->SMS('PHONENUMBER', 'MESSAGE');

// CONTACT QR code
$qc->CONTACT('NAME', 'ADDRESS', 'PHONE', 'EMAIL');
**/

if (!class_exists('ifind_QrCode')) {
    class ifind_QrCode {
        // Google Chart API URL
        private $apiUrl = 'http://chart.apis.google.com/chart';
        private $data;
        // It generates URL type of Qr code
        public function URL($url = null){
            $this->data = preg_match("#^https?\:\/\/#", $url) ? $url : "http://{$url}";
        }
        // It generate the Text type of Qr code
        public function TEXT($text){
            $this->data = $text;
        }
        // It generates the Email type of Qr code
        public function EMAIL($email = null, $subject = null, $message = null) {
            $this->data = "MATMSG:TO:{$email};SUB:{$subject};BODY:{$message};;";
         }
        //It generates the Phone numner type of Qr Code
        public function PHONE($phone){
            $this->data = "TEL:{$phone}";
        }
        //It generates the Sms type of Qr code
        public function SMS($phone = null, $msg = null) {
            $this->data = "SMSTO:{$phone}:{$msg}";
         }
        //It generates the VCARD type of Qr code
        public function CONTACT($name = null, $address = null, $phone = null, $email = null) {
            $this->data = "MECARD:N:{$name};ADR:{$address};TEL:{$phone};EMAIL:{$email};;";
         }
        // It Generates the Image type of Qr Code
        public function CONTENT($type = null, $size = null, $content = null) {
            $this->data = "CNTS:TYPE:{$type};LNG:{$size};BODY:{$content};;";
         }
        // Saving the Qr code image 
        // $data_return: link | save_file | display
        public function QRCODE($data_return = 'link', $size = 400, $filename = 'default.png') {
            if ($data_return === 'link') {
                $link = add_query_arg('chs', $size.'x'.$size, $this->apiUrl);
                $link = add_query_arg('cht', 'qr', $link);
                $link = add_query_arg('chl', urlencode($this->data), $link);
                return $link;
            }else{
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "chs={$size}x{$size}&cht=qr&chl=" . urlencode($this->data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                $img = curl_exec($ch);
                curl_close($ch);
                if($img) {
                    if($data_return === 'save_file') {
                        if(!preg_match("#\.png$#i", $filename)) {
                            $filename .= ".png";
                        }
                        return file_put_contents($filename, $img);
                    } else {
                        header("Content-type: image/png");
                        print $img;
                        return true;
                    }
                }
                return false;
            }
        }

        public function SETCONTENT($qr_type, $qr_content){
            if ($qr_type === 'url') {
                $this->URL($qr_content);
            }elseif ($qr_type === 'text') {
                $this->TEXT($qr_content);
            }elseif ($qr_type === 'email') {
                $this->EMAIL($qr_content);
            }elseif ($qr_type === 'phone') {
                $this->PHONE($qr_content);
            }elseif ($qr_type === 'sms') {
                $this->SMS($qr_content);
            }elseif ($qr_type === 'contact') {
                $this->CONTACT($qr_content);
            }elseif ($qr_type === 'content') {
                $this->CONTENT($qr_content);
            }
        }
    }
}