<?php
class Model
{

    public static $conn = '';
    public $totalMenu = array();


    function __construct()

    {

        $servername = 'localhost';
        $username = 'root';
        $password = '';
        $dbname = 'store1db';
        $attr = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
        self::$conn = new PDO('mysql:host=' . $servername . ';dbname=' . $dbname, $username, $password, $attr);
        self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    }

    function setSelling($basket){
        foreach ($basket as $item){
            $sql='update tbl_product set selling=selling+ ? WHERE id=?';
            $this->doQuery($sql,[$item['teadad'],$item['id']]);
        }
    }


    function getMenu(){
        $sql='select * from tbl_category where id_parent=0';
        $level_0= $this->myFetchAll($sql);
        $ids_level_0=[];
        foreach ($level_0 as $key=>$item){
            $ids_level_0[]=$item['id'];
        }

        $sql='select * from tbl_category where find_in_set(id_parent,?)';
        $level_1= $this->myFetchAll($sql,[join(',',$ids_level_0)]);

        foreach ($level_0 as $key=>$item){
            foreach ($level_1 as $key_=>$item_){
                if ($item['id']==$item_['id_parent']){
                    $level_0[$key]['level_1']=$level_1;
                }
            }
        }
        return $level_0;


    }
    public static function getoption()
    {

        $sql = "select * from tbl_option";
        $stmt = self::$conn->prepare($sql);
        $stmt->execute();
        $options = $stmt->fetchAll();
        $options_new = array();
        foreach ($options as $option) {
            $setting = $option['setting'];
            $value = $option['value'];
            $options_new[$setting] = $value;
        }
        return $options_new;
    }

    function create_thumbnail($file, $pathToSave = '', $w="", $h = '', $crop = FALSE)
    {

        $new_height = $h;

        list($width, $height) = getimagesize($file);

        $r = $width / $height;

        if ($crop) {
            if ($width > $height) {
                $width = ceil($width - ($width * abs($r - $w / $h)));
            } else {
                $height = ceil($height - ($height * abs($r - $w / $h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w / $h > $r) {
                $newwidth = $h * $r;
                $newheight = $h;
            } else {
                $newheight = $w / $r;
                $newwidth = $w;
            }
        }

        $what = getimagesize($file);

        switch (strtolower($what['mime'])) {
            case 'image/png':
                $src = imagecreatefrompng($file);

                break;
            case 'image/jpeg':
                $src = imagecreatefromjpeg($file);
                break;
            case 'image/gif':
                $src = imagecreatefromgif($file);
                break;
            default:
                //die();
        }

        if ($new_height != '') {
            $newheight = $new_height;
        }

        $dst = imagecreatetruecolor($newwidth, $newheight);//the new image
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);//az function

        imagejpeg($dst, $pathToSave, 95);//pish farz in tabe 75 darsad quality ast

        return $dst;


    }

    public static function sessionInit()
    {

        @session_start();
    }

    public static function sessionSet($name, $value)
    {

        $_SESSION[$name] = $value;
    }

    public static function sessionGet($name)
    {

        if (isset($_SESSION[$name])) {

            return $_SESSION[$name];
        } else {
            return false;
        }
    }





    public static function miladiDate($format = 'Y - m - d')
    {
        $date = date($format);
        return $date;
    }

    public static function jaliliDate($format = 'Y/n/j')
    {

        $date = jdate($format);
        return $date;
    }

    public static function jaliliToMiladi($jalili, $format = '/')
    {

        $jalili = explode('/', $jalili);
        $year = $jalili[0];
        $month = $jalili[1];
        $day = $jalili[2];
        $date = jalali_to_gregorian($year, $month, $day);
        $date = implode($format, $date);
        $date = new DateTime($date);
        $date = $date->format('Y/m/d');

        return $date;
    }

    public static function MiladiTojalili($miladi, $format = '/')
    {

        $miladi = explode('/', $miladi);
        $year = $miladi[0];
        $month = $miladi[1];
        $day = $miladi[2];
        $date = gregorian_to_jalali($year, $month, $day);
        $date = implode($format, $date);
        return $date;
    }

    public static function cleanInput($input)
    {
        $search = array(
            '@<script[^>]*?>.*?</script>@si', // Strip out javascript
            '@<[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
            '@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
            '@<![\s\S]*?--[ \t\n\r]*>@' // Strip multi-line comments
        );
        $output = preg_replace($search, '', $input);
        return $output;
    }

//Data sanitizing
    public static function sanitize($input)
    {
        if (is_array($input)) {
            foreach ($input as $var => $val) {
                $output[$var] = Model::sanitize($val);
            }
        } else {
            if (get_magic_quotes_gpc()) {
                $input = stripslashes($input);
            }
            $input = Model::cleanInput($input);
            $output = addslashes($input);
        }
        return $output;
    }


    function myFetch($sql,$param=[],$typeFetch=3){
        $stmt=self::$conn->prepare($sql);
        foreach ($param as $key=>$val){
            $stmt->bindValue($key+1,$val);
        }
        $stmt->execute();
        if ($typeFetch==1){
            $result=$stmt->fetch(PDO::FETCH_NUM);
        }
        if ($typeFetch==2){
            $result=$stmt->fetch(PDO::FETCH_NAMED);
        }
        if ($typeFetch==3){
            $result=$stmt->fetch(PDO::FETCH_BOTH);
        }
        return $result;
    }
    function myFetchAll($sql,$param=[],$typeFetch=3){
        $stmt=self::$conn->prepare($sql);
        foreach ($param as $key=>$val){
            $stmt->bindValue($key+1,$val);
        }
        $stmt->execute();
        if ($typeFetch==1){
            $result=$stmt->fetchAll(PDO::FETCH_NUM);
        }
        if ($typeFetch==2){
            $result=$stmt->fetchAll(PDO::FETCH_NAMED);
        }
        if ($typeFetch==3){
            $result=$stmt->fetchAll(PDO::FETCH_BOTH);
        }
        return $result;
    }
    function doQuery($sql,$param=[]){
        $stmt=self::$conn->prepare($sql);
        foreach ($param as $key=>$val){
            $stmt->bindValue($key+1,$val);
        }
        $stmt->execute();
    }

    public static function uploadFile($file, $dir, $allowFormat,$maxMeg=2,$name="")
    {
        $fileName = $file['name'];
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $fileSize = $file['size'];
        $fileTmp = $file['tmp_name'];
        $fileType = $file['type'];
        $uploadOk = 1;
//        echo $ext;
//        $imgArrType = ['jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG'];
//        $pdfArrType = ['pdf', 'PDF'];
//        $fontArrType = ['fft', 'eot'];
//        $videoArrType=['mp4'];
        $err = '';

        if ($name==""){
            $newName = time()."_".rand(1,1000000) . "." . strtolower($ext);
        }else{
            $newName = $name . "." . strtolower($ext);
        }
        echo $newName;
//        $newName = time()."_" . "." . $ext;
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        if (!self::alfo($allowFormat,$ext)) {
            $uploadOk = 0;
            $err = 'پسوند فایل انتخابی نامناسب است';
        }
        if ($fileSize > ($maxMeg*1024*1024)) {
            $uploadOk = 0;
            $err = 'حداکثر حجم فایل انتخابی 20 مگابایت است';
        }
        if (!empty($fileSize) && !empty($fileType) && $uploadOk == 1) {
            $target = $dir . $newName;
            move_uploaded_file($fileTmp, $target);
            $err = 'آپلود فایل با موفقیت انجام شد';
        }
        return $newName;
    }
    public static function alfo ($allowFormat,$ext){
        $imgArrType = ['jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG'];
        $pdfArrType = ['pdf', 'PDF'];
        $vidArrType=['mp4'];
        $img=true;
        $vid=true;
        $pdf=true;
        foreach ($allowFormat as $format){
            if ($format=='img'){
                $img=in_array($ext,$imgArrType);

            }elseif ($format=='pdf'){
                $pdf=in_array($ext,$pdfArrType);

            }elseif ($format=='vid'){
                $vid=in_array($ext,$vidArrType);
            }

        }
        return $img or $pdf or $vid;
    }


    public static function getBasketCookie()
    {
        if (isset($_COOKIE['basket_bonny'])) {
            $cookie = $_COOKIE['basket_bonny'];
        } else {
            $expire = time() + 7 * 24 * 3600;
            $value = time()."_".rand(100,10000000);
            setcookie('basket_bonny', $value, $expire, '/');
            $cookie = $value;
        }
        return $cookie;
    }
    public static function is_image($path)
    {
        $a = getimagesize($path);
        $image_type = @$a['mime'];

        if(in_array($image_type , array('image/gif' , 'image/jpeg','image/tiff' , 'image/bmp','image/png')))
        {
            return true;
        }
        return false;
    }



    function phpMailer($toMail,$htmlMsg,$textMsg,$subject,$fromMail="info@selectpersia.com",$passFromMail="Mehdijd+select4820*1374*",$nameFromMail="Select Persia Company"){
        require_once('public/lib/PHPMailer/class.smtp.php');
        require_once('public/lib/PHPMailer/class.phpmailer.php');
        $mail=new SMTP();
        $mail=new PHPMailer();
        $mail->isSMTP();
        $mail->Host='mail.selectpersia.com';
        $mail->SMTPAuth=true;
        $mail->Username=$fromMail;
        $mail->Password=$passFromMail;
// $mail->SMTPSecure='ssl';
        $mail->Port=587;
        $mail->Subject=$subject;
        $mail->From=$fromMail;
        $mail->CharSet='utf-8';
        $mail->FromName=$nameFromMail;
        $mail->ContentType='text/html;charset=utf-8';
        $mail->isHTML(true);
        $mail->addAddress($toMail);
        $mail->Body=$htmlMsg;
        $mail->AltBody=$textMsg;
        $mail->send();
//        if($mail->isError()){
//            echo 'error';
//        }
        $mail->smtpClose();
    }
    static function remove_special_character($string) {
        $t = $string;
        $specChars = array(
            ' ' => '-',    '!' => '',    '"' => '',
            '#' => '',    '$' => '',    '%' => '',
            '&amp;' => '',    '\'' => '',   '(' => '',
            ')' => '',    '*' => '',    '+' => '',
            ',' => '',    '₹' => '',    '.' => '',
            '/-' => '',    ':' => '',    ';' => '',
            '<' => '',    '=' => '',    '>' => '',
            '?' => '',    '@' => '',    '[' => '',
            '\\' => '',   ']' => '',    '^' => '',
            '_' => '',    '`' => '',    '{' => '',
            '|' => '',    '}' => '',    '~' => '',
            '-----' => '-',    '----' => '-',    '---' => '-',
            '/' => '',    '--' => '-',   '/_' => '-',

        );
        foreach ($specChars as $k => $v) {
            $t = str_replace($k, $v, $t);
        }
        return $t;
    }
    static function randomString()
    {
        $characters = '0123456789ab6c0d1e1f5g2h7i5j7kl88mdkn7n6op4qrs3tuvw2xy44z';
        $randstring = '';
        for ($i = 0; $i < 8; $i++) {
            $randstring .= $characters[rand(0, strlen($characters))];
        }
        return $randstring;
    }
    public static function deleteDir($dirPath) {
        if (is_file($dirPath)){
            unlink($dirPath);
        }else{
            if (! is_dir($dirPath)) {
                throw new InvalidArgumentException("$dirPath must be a directory");
            }
            if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
                $dirPath .= '/';
            }
            $files = glob($dirPath . '*', GLOB_MARK);
            foreach ($files as $file) {
                if (is_dir($file)) {
                    self::deleteDir($file);
                } else {
                    unlink($file);
                }
            }
            rmdir($dirPath);
        }

    }
    public static function convert2english($string) {
        $newNumbers = range(0, 9);
        // 1. Persian HTML decimal
        $persianDecimal = array('&#1776;', '&#1777;', '&#1778;', '&#1779;', '&#1780;', '&#1781;', '&#1782;', '&#1783;', '&#1784;', '&#1785;');
        // 2. Arabic HTML decimal
        $arabicDecimal = array('&#1632;', '&#1633;', '&#1634;', '&#1635;', '&#1636;', '&#1637;', '&#1638;', '&#1639;', '&#1640;', '&#1641;');
        // 3. Arabic Numeric
        $arabic = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
        // 4. Persian Numeric
        $persian = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');

        $string =  str_replace($persianDecimal, $newNumbers, $string);
        $string =  str_replace($arabicDecimal, $newNumbers, $string);
        $string =  str_replace($arabic, $newNumbers, $string);
        return str_replace($persian, $newNumbers, $string);
    }



    function getitemspecialsale($id)
    {

        $sql="select * from tbl_spacialsale where id=?";
        return $this->myFetchAll($sql,[$id]);

    }















}












