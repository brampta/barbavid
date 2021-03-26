<?php


class Message{
    public $messages = array();

    public function add_message($type,$message){
        //types: error, success, notice
        $this->messages[]=array(
            'type'=>$type,
            'message'=>$message,
        );
    }
    public function show_messages(){
        foreach($this->messages as $message){
            ?>
            <p class="message <?php echo $message['type'] ?>"><?php echo $message['message'] ?></p>
            <?php
        }
    }
}