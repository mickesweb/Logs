 <?php
    // Including class file.
    include_once(dirname(__FILE__).'/class.log.php');
    // Create a new instans of the class.
    $log = new Log("html","week");
    
    // Write a info message in the log
    $log->reportInfo("A nice message.");
    
    // Write a warning into the log
    $log->reportWarning(null, "This is a warning.");
     
    // Write a error in the log and send mail to system administrator.
    try {
        // Do somthing wrong here.
    } catch (Exception $exception) {
        $log->reportError($exception, "And a message");
    }
?>