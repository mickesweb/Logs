<?php

/* ~class.log.php
 * 
 * @verson : 1.0
 * @contact : via mickesweb.se
 * @author :  Mikael Andersson <mikael@mickesweb.se>
 * @copyright (c) 2011, Mikael Andersson. All Rights Reserved.  
 * @license : http://creativecommons.org/licenses/by-nc-sa/3.0/
 * 
 * Last Updated: 2011-11-15
 * INFO: A class for log what is happening.
 * NOTE: You need a folder (/errorlog/) with write access
 * NOTE: If you want send mail then you need special class mail (class.mail.php) (if not use, set MAIL to false)
 */

define("MAIL", false);
define("ADMIN_NAME", "Mikael Andersson");
define("ADMIN_MAIL", "mikael@mickesweb.se");

class Log {

    // @var Two-dimensional Array
    private $mailAddress = array(array('name' => ADMIN_NAME, 'email' => ADMIN_MAIL));
    // @var Enum (html or txt)
    private $fileType;
    // @var Enum (day, week or month)
    private $newFileFrequency;

    /* Constructor, run when the new class is created.  */
    public function __construct($fileType="html", $newFileFrequency="day") {
        $this->fileType = $fileType;
        $this->newFileFrequency = $newFileFrequency;
    }

    /* Add email that receiving mail if something is wrong.
     * Input:
     *      @param string $name
     *      @param string $email
     */
    public function addMailAddress($name, $email) {
        array_push($this->mailAddress, array('name' => $name, 'email' => $email));
    }

    /* Format a info string and save it to file.
     * Input:
     *      @param string $message
     */
    public function reportInfo($message) {
        $messageString = date('Y-m-d')." at ".date('H:i:s');
        $messageString = $messageString. " Info: ";
        $messageString = $messageString. $message; 
        self::saveToFile($messageString);
    }

    /* Format a warning string and save it to file.
     * Input:
     *      @param exception $exception
     *      @param string $message
     */
    public function reportWarning($exception=null, $message="") {
        $messageString = date('Y-m-d')." at ".date('H:i:s');
        $messageString = $messageString. " WARNING:";
        if($exception != null) {
            $messageString = $messageString. " CODE: ".$exception->getCode();
            $messageString = $messageString. " LINE: ".$exception->getLine();
            $messageString = $messageString. " FILE: ".$exception->getFile();
            $messageString = $messageString . '<br/><span style="margin-left: 160px;">Error message: '.$exception->getMessage().'</span>';
        }        
        if($message != "") {
            $messageString = $messageString . '<br/><span style="margin-left: 160px;">Info message: '.$message.'</span>';
        }
        self::saveToFile($messageString);
    }

    /* Format a error string and save it to file and send a mail to server administrator.
     * Input:
     *      @param exception $exception
     *      @param string $message
     */
    public function reportError($exception=null, $message="") {
        $messageString = date('Y-m-d')." at ".date('H:i:s');
        $messageString = $messageString . ' <span style="font-weight: bold;">ERROR:</span> ';
        if($exception != null) {
            $messageString = $messageString. " CODE: ".$exception->getCode();
            $messageString = $messageString. " LINE: ".$exception->getLine();
            $messageString = $messageString. " FILE: ".$exception->getFile();
            $messageString = $messageString . '<br/><span style="margin-left: 160px;">Error message: '.$exception->getMessage().'</span>';
        }        
        if($message != "") {
            $messageString = $messageString . '<br/><span style="margin-left: 160px;">Info message: '.$message.'</span>';
        }

        self::saveToFile($messageString);
        self::sendEmail($messageString);
    }

    
    /* Save the input string to correct file.
     * Input:
     *      @param string $string
     */
    private function saveToFile($string) {
        $string = $string."<br/>\n";
        // Remove all html if the file should be a text file.
        if(strtolower($this->fileType) == "txt") {
            $string = str_replace("<br/>","\n",$string);
            $string = strip_tags($string);
        }
        $fileUrl = dirname(__FILE__).self::getFileName();
        /* If file exists, opend and save at the end in file. 
         * If not exists this will create a new file. */
        $file = fopen($fileUrl, 'a+');
        fwrite($file, $string);
        fclose($file);

        return true;
    }

    /* Send a email massage to system administrator.
     * Input:
     *      @param string $message
     */
    private function sendEmail($message) {
        if(MAIL) {
            include_once(dirname(__FILE__).'/class.mail.php');
            $mail = new Mail();
            $mail->setAddress($this->mailAddress);
            $mail->addMessage('Error report', $message);
            $mail->send();
        }
    }

    /* Generate the correct file name for the log file.
     * @return string 
     */
    private function getFileName() {
        $fileName = "/errorlog/";
        if ($this->newFileFrequency == "day") {
            $fileName = $fileName . "d." . date('Y.m.d');
        } else if ($this->newFileFrequency == "week") {
            $fileName = $fileName . "w." . date('Y.W');
        } else if ($this->newFileFrequency == "month") {
            $fileName = $fileName . "m." . date('Y.F');
        } else {
            $fileName = $fileName . "log";
        }
        $fileName = $fileName . "." . $this->fileType;

        return $fileName;
    }
}
?>