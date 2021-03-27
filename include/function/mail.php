<?php


function send_mail($to, $toname, $from, $fromname, $subject, $message_html, $message_text){
    global $sendgrid_api_key, $message;

    require_once (dirname(dirname(__FILE__)) . '/sendgrid-php/sendgrid-php.php');
    $email = new \SendGrid\Mail\Mail();

    $email->addTo($to,$toname);
    $email->setFrom($from,$fromname);
    $email->setSubject($subject);
    if($message_html){
        $email->addContent("text/html", $message_html);
    }
    if($message_text){
        $email->addContent("text/plain", $message_text);
    }

    $sendgrid = new \SendGrid($sendgrid_api_key);
    try {
        $response = $sendgrid->send($email);

        /*
        echo "<h4>status code</h4>";
        print $response->statusCode() . "\n";
        echo "<h4>headers</h4>";
        print_r($response->headers());
        echo "<h4>body</h4>";
        print $response->body() . "\n";
        echo "<hr>";
        */
        $message->add_message('notice',__('sendgrid responded with %1',$response->statusCode()));

        return $response;
    } catch (Exception $e) {
        echo 'Caught exception: '. $e->getMessage() ."\n";
    }
}