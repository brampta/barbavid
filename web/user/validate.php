<?php
include(dirname(dirname(dirname(__FILE__))).'/include/init.php');

if(isset($_GET['code'])){
    $user_validation = $user->validate($_GET['code']);
}

include(BP.'/include/head_start.php');
include(BP.'/include/head_end.php');
include(BP.'/include/header.php');
?>

<?php
if(isset($user_validation) && $user_validation['success']==true){
    ?>
    <p>successfully validated email</p>
    <?php
}else{
    if(isset($user_validation) && in_array('invalid_code',$user_validation['errors'])){
        ?>
        <p>invalid code</p>
        <?php
    }

}

include(BP.'/include/footer.php');
include(BP.'/include/page_end.php');