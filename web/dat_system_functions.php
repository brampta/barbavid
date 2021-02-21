<?php
include('settings.php');

@ini_set('max_execution_time', 300); //300 seconds = 5 minutes

$datfiles_size_in_bytes = (1024 * 1024) / 5;
$sleep_howlong_after_settingtobusy=5;

function set_elements($index_name, $hash, $keyvalue_toset_array) {
    $datfile_num = find_place_according_to_index($hash, $index_name);
    $data = get_element_info($hash, $datfile_num);
    $data_array = unserialize($data);
    foreach ($keyvalue_toset_array as $key => $value) {
        if ($value == '+inc+') {
            $data_array[$key] = $data_array[$key] + 1;
        } else {
            $data_array[$key] = $value;
        }
    }

    $datfile_num = find_place_according_to_index($hash, $index_name);
    add_or_update_element($hash, serialize($data_array), $datfile_num, $index_name);
}

function get_element_info($hash, $datfile_num) {
    $resull = false;
    //echo '$datfile_num: '.$datfile_num.'<br />';
    $file = fopen(DAT_SYSTEM_HOME . "/dat_system/" . $datfile_num . ".dat", "r") or exit("Unable to open file!");
    flock($file, LOCK_SH) or exit("Unable to lock file!");

    while (!feof($file)) {
        $line_data = trim(fgets($file));
        $exploded_line_data = explode(' ', $line_data);
        $thishash = $exploded_line_data[0];

        if ($thishash == $hash) {
            $resull = $exploded_line_data[1];
            break;
        }

        if (strcmp($thishash, $hash) > 0) {
            break;
        }
    }

    flock($file, LOCK_UN) or exit("Unable to unlock file!");
    fclose($file);
    return $resull;
}

function add_or_update_element($hash, $data, $datfile_num, $indexfilename) {
    if (stripos($data, ' ') !== false) {
        die('error, data can not contain any spaces!');
    }

    global $datfiles_size_in_bytes;

    $datfilename = DAT_SYSTEM_HOME."/dat_system/" . $datfile_num . ".dat";

    $rebuilt_file_data = '';
    $stuff_written = 0;
    $file = fopen($datfilename, "r+") or exit("Unable to open file!");
    flock($file, LOCK_EX) or exit("Unable to lock file!");

    while (!feof($file)) {
        $line_data = trim(fgets($file));
//        echo '$line_data: '.$line_data.'<br />';
        if ($line_data != '') {
            $exploded_line_data = explode(' ', $line_data);
            $thisdomain = $exploded_line_data[0];
//            echo '$thisdomain: '.$thisdomain.'<br />';

            if ($thisdomain == $hash) {
//                    echo 'this domain "'.$thisdomain.'" is same than "'.$reverse_domain.'", will replace<br />';
                $rebuilt_file_data = $rebuilt_file_data . $hash . ' ' . $data . '
';
                $stuff_written = 1;
            } else if (strcmp($thisdomain, $hash) > 0 && $stuff_written == 0) {
//                echo 'this domain "'.$thisdomain.'" comes after "'.$reverse_domain.'", will add before<br />';
                $rebuilt_file_data = $rebuilt_file_data . $hash . ' ' . $data . '
' . $line_data . '
';
                $stuff_written = 1;
            } else {
                $rebuilt_file_data = $rebuilt_file_data . $line_data . '
';
            }
        }
    }
    if ($stuff_written == 0) {
//        echo 'no place was found for domain "'.$reverse_domain.'" in file, will add at the end<br />';
        $rebuilt_file_data = $rebuilt_file_data . $hash . ' ' . $data . '
';
    }

    fseek($file, 0);
    ftruncate($file, 0);
    fwrite($file, $rebuilt_file_data);



    //get size of that file now...
    clearstatcache();
    $thisfilesize = filesize($datfilename);
//    echo '$thisfilesize: '.$thisfilesize.' bytes (max for .dat files is currently '.$datfiles_size_in_bytes.' bytes)<br />';

    if ($thisfilesize > $datfiles_size_in_bytes) {
        //file is over size limit, we will split it in two
        $current_len_oftextforfile = strlen($rebuilt_file_data);
        $find_first_linebreakaftermiddle = stripos($rebuilt_file_data, '
', $current_len_oftextforfile / 2);
        $stuff_for_first_half = substr($rebuilt_file_data, 0, $find_first_linebreakaftermiddle + 1);
        $stuff_for_second_half = substr($rebuilt_file_data, $find_first_linebreakaftermiddle + 1);
        if (trim($stuff_for_second_half) != '') {
            $first_space_in_second_half = stripos($stuff_for_second_half, ' ');
            $first_domain_in_second_half = trim(substr($stuff_for_second_half, 0, $first_space_in_second_half));
//            echo '$first_domain_in_second_half: "'.$first_domain_in_second_half.'"<br />';
            if ($first_domain_in_second_half != '') {
                //write first half to current .dat file
                fseek($file, 0);
                ftruncate($file, 0);
                fwrite($file, $stuff_for_first_half);

                //write second half to new dat file
                $dat_number_for_newfile = get_new_dat_number();
                $newdatefilename = DAT_SYSTEM_HOME."/dat_system/" . $dat_number_for_newfile . ".dat";
                $file2 = fopen($newdatefilename, "w") or exit("Unable to open file!");
                flock($file2, LOCK_EX) or exit("Unable to lock file!");

                fwrite($file2, $stuff_for_second_half);

                flock($file2, LOCK_UN) or exit("Unable to unlock file!");
                fclose($file2);

                //add new dat file to index
                add_or_update_to_index($first_domain_in_second_half, $dat_number_for_newfile, $indexfilename);
            }
        }
    }

    flock($file, LOCK_UN) or exit("Unable to unlock file!");
    fclose($file);
}

function remove_element($hash, $datfile_num, $indexfilename) {
    $delete_file = 0;
    $datfilename = DAT_SYSTEM_HOME."/dat_system/" . $datfile_num . ".dat";

    $rebuilt_file_data = '';
    $hittheonetoremove = 0;
    $file = fopen($datfilename, "r+") or exit("Unable to open file!");
    flock($file, LOCK_EX) or exit("Unable to lock file!");

    $countlinez = 0;
    $removed_domain_was_first = 0;
    $second_domain = '';
    while (!feof($file)) {
        $countlinez++;

        $pure_line_data = fgets($file);
        $line_data = trim($pure_line_data);
//        echo '$line_data: '.$line_data.'<br />';
        $exploded_line_data = explode(' ', $line_data);
        $thiselement = $exploded_line_data[0];
//        echo '$thiselement: '.$thiselement.'<br />';

        if ($countlinez == 2) {
            $second_domain = $thiselement;
        }

        if ($thiselement == $hash) {
            $hittheonetoremove = 1;
            if ($countlinez == 1) {
                $removed_domain_was_first = 1;
            }
        } else {
            $rebuilt_file_data = $rebuilt_file_data . $pure_line_data;
        }
    }

    if (trim($rebuilt_file_data) == '') {
        $delete_file = 1;
        remove_from_index($datfile_num, $indexfilename);
    } else if ($hittheonetoremove == 1) {
        if ($removed_domain_was_first == 1) {
//            echo 'will rewrite the file but first, first domain on this dat will now be changed, will update index<br />';
            add_or_update_to_index($second_domain, $datfile_num, $indexfilename);
        }

        fseek($file, 0);
        ftruncate($file, 0);
        fwrite($file, $rebuilt_file_data);
    } else {
        echo 'error, domain ' . $hash . ' was not listed in ' . $datfile_num . '.dat<br />';
    }

    flock($file, LOCK_UN) or exit("Unable to unlock file!");
    fclose($file);

    if ($delete_file == 1) {
        unlink($datfilename);
        recycle_unique_number($datfile_num);
    }
}

function dat_system_is_busy() {
    $isbusy = true;

    $file = fopen(DAT_SYSTEM_HOME."/dat_system/busy.dat", "r") or exit("Unable to open file!");
    flock($file, LOCK_SH) or exit("Unable to lock file!");
    $file_contents = fread($file, 10);
    //echo 'busy file contents: '.$file_contents.'<br />';
    flock($file, LOCK_UN) or exit("Unable to unlock file!");
    fclose($file);

    $oldestacceptabletime = time() - (15 * 60);
    if ($file_contents < $oldestacceptabletime) {
        $isbusy = false;
    }

    return $isbusy;
}

function find_place_according_to_index($element, $index_file_name, $element_is_array = 0) {
//    echo 'find_place_according_to_index(\''.$element.'\',\''.$index_file_name.'\','.$element_is_array.');<br />';
    $file = fopen(DAT_SYSTEM_HOME."/dat_system/" . $index_file_name, "r") or exit("Unable to open file!");
    flock($file, LOCK_SH) or exit("Unable to lock file!");

    while (!feof($file)) {
        $line_data = trim(fgets($file));
//        echo '$line_data: '.$line_data.'<br />';
        if ($line_data != '') {
            $exploded_line_data = explode(' ', $line_data);
            $firstdomain = $exploded_line_data[0];

            if ($element_is_array == 0) {
                if (strcmp($firstdomain, $element) > 0) {
                    break;
                }
            } else if ($element_is_array == 1) {
                foreach ($element as $key => $value) {
                    if (strcmp($firstdomain, $value) > 0) {
//                        echo '$rezu[\''.$key.'\']=\''.$datfile_num.'\';<br />';
                        $rezu[$key] = $datfile_num;
                        unset($element[$key]);
                        $howmanyleftinarray = count($element);
                        if ($howmanyleftinarray == 0) {
                            break;
                        }
                    }
                }
            }

            $datfile_num = $exploded_line_data[1];
        }
    }
    flock($file, LOCK_UN) or exit("Unable to unlock file!");
    fclose($file);

    if ($element_is_array == 1) {
        //assign last datfile_num to all elements still left in array
        foreach ($element as $key => $value) {
            $rezu[$key] = $datfile_num;
        }
    }

    if ($element_is_array == 0) {
        return $datfile_num;
    } else if ($element_is_array == 1) {
        return $rezu;
    }
}

function find_previous_dat_in_index($datfile_num, $index_file_name) {
    $file = fopen(DAT_SYSTEM_HOME."/dat_system/" . $index_file_name, "r") or exit("Unable to open file!");
    flock($file, LOCK_SH) or exit("Unable to lock file!");
    $previous_dat_num = false;
    while (!feof($file)) {
        $line_data = trim(fgets($file));
        if ($line_data != '') {
//            echo '$line_data: '.$line_data.'<br />';
            $exploded_line_data = explode(' ', $line_data);
            $this_dat_num = $exploded_line_data[1];

            if ($this_dat_num == $datfile_num) {
                break;
            } else {
                $previous_dat_num = $this_dat_num;
            }
        }
    }
    flock($file, LOCK_UN) or exit("Unable to unlock file!");
    fclose($file);
    return $previous_dat_num;
}

function find_next_dat_in_index($datfile_num, $index_file_name) {
    $file = fopen(DAT_SYSTEM_HOME."/dat_system/" . $index_file_name, "r") or exit("Unable to open file!");
    flock($file, LOCK_SH) or exit("Unable to lock file!");
    $next_dat_num = false;
    $getnext = 0;
    while (!feof($file)) {
        $line_data = trim(fgets($file));
        if ($line_data != '') {
//            echo '$line_data: '.$line_data.'<br />';
            $exploded_line_data = explode(' ', $line_data);
            $this_dat_num = $exploded_line_data[1];

            if ($getnext == 1) {
                $next_dat_num = $this_dat_num;
                break;
            }

            if ($this_dat_num == $datfile_num) {
                $getnext = 1;
            }
        }
    }
    flock($file, LOCK_UN) or exit("Unable to unlock file!");
    fclose($file);
    return $next_dat_num;
}

function add_or_update_to_index($element, $datfile_num, $index_file_name) {
    if ($element == '') {
        die('error in add_or_update_to_index() $element is empty');
    }
    if ($datfile_num == '') {
        die('error in add_or_update_to_index() $datfile_num is empty');
    }
    if ($index_file_name == '') {
        die('error in add_or_update_to_index() $index_file_name is empty');
    }

    $indexfilename = DAT_SYSTEM_HOME."/dat_system/" . $index_file_name;


    $rebuilt_file_data = '';
    $stuff_written = 0;
    $file = fopen($indexfilename, "r+") or exit("Unable to open file!");
    flock($file, LOCK_EX) or exit("Unable to lock file!");

    while (!feof($file)) {
        $pure_line_data = fgets($file);
        $line_data = trim($pure_line_data);
//        echo '$line_data: '.$line_data.'<br />';
        $exploded_line_data = explode(' ', $line_data);
        $thiselement = $exploded_line_data[0];
        $thisdatfile_num = $exploded_line_data[1];
//        echo '$thiselement: '.$thiselement.'<br />';
//        echo '$thisdatfile_num: '.$thisdatfile_num.'<br />';

        if ($thisdatfile_num != $datfile_num) {
            if (strcmp($thiselement, $element) > 0 && $stuff_written == 0) {
//                echo 'this $thiselement "'.$thiselement.'" comes after "'.$element.'", will add before<br />';
                $rebuilt_file_data = $rebuilt_file_data . $element . ' ' . $datfile_num . '
' . $pure_line_data;
                $stuff_written = 1;
            } else {
                $rebuilt_file_data = $rebuilt_file_data . $pure_line_data;
            }
        }
    }
    if ($stuff_written == 0) {
//        echo 'no place was found for element "'.$element.'" in index "'.$index_file_name.'", will add at the end<br />';
        $rebuilt_file_data = $rebuilt_file_data . $element . ' ' . $datfile_num . '
';
    }

    fseek($file, 0);
    ftruncate($file, 0);
    fwrite($file, $rebuilt_file_data);

    flock($file, LOCK_UN) or exit("Unable to unlock file!");
    fclose($file);
}

function remove_from_index($datfile_num, $index_file_name) {
    if ($datfile_num == '') {
        die('error in add_or_update_to_index() $datfile_num is empty');
    }
    if ($index_file_name == '') {
        die('error in add_or_update_to_index() $index_file_name is empty');
    }

    $indexfilename = DAT_SYSTEM_HOME."/dat_system/" . $index_file_name;


    $rebuilt_file_data = '';
    $hittheonetoremove = 0;
    $file = fopen($indexfilename, "r+") or exit("Unable to open file!");
    flock($file, LOCK_EX) or exit("Unable to lock file!");

    while (!feof($file)) {
        $pure_line_data = fgets($file);
        $line_data = trim($pure_line_data);
//        echo '$line_data: '.$line_data.'<br />';
        $exploded_line_data = explode(' ', $line_data);
        $thiselement = $exploded_line_data[0];
        $thisdatfile_num = $exploded_line_data[1];
//        echo '$thiselement: '.$thiselement.'<br />';
//        echo '$thisdatfile_num: '.$thisdatfile_num.'<br />';

        if ($thisdatfile_num == $datfile_num) {
            $hittheonetoremove = 1;
        } else {
            $rebuilt_file_data = $rebuilt_file_data . $pure_line_data;
        }
    }

    if ($hittheonetoremove == 1) {
        fseek($file, 0);
        ftruncate($file, 0);
        fwrite($file, $rebuilt_file_data);
    } else {
        echo 'error, dat #' . $datfile_num . ' was not listed in ' . $index_file_name . '<br />';
    }

    flock($file, LOCK_UN) or exit("Unable to unlock file!");
    fclose($file);
}

function get_new_dat_number() {
    //first try to get one of these old numbers to reuse
    $numbertouse = false;
    $file = fopen(DAT_SYSTEM_HOME.'/dat_system/unique_numbers_to_reuse.dat', "r+") or exit("Unable to open file!");
    flock($file, LOCK_EX) or exit("Unable to lock file!");

    $remember_data_torewrite = '';
    while (!feof($file)) {
        $thisline = fgets($file);
        $line_data = trim($thisline);
        if ($line_data != '' && $numbertouse == false) {
            $numbertouse = $line_data;
        } else {
            $remember_data_torewrite = $remember_data_torewrite . $thisline;
        }
    }

    //if you did find an old number to reuse rewrite file where theyre kept without the one you took
    if ($numbertouse != false) {

        fseek($file, 0);
        ftruncate($file, 0);
        fwrite($file, $remember_data_torewrite);

        flock($file, LOCK_UN) or exit("Unable to unlock file!");
        fclose($file);
    } else {

        flock($file, LOCK_UN) or exit("Unable to unlock file!");
        fclose($file);


        //otherwise, if you still havent found a number yet get a brand new one
        $uniquenumbersfile = DAT_SYSTEM_HOME.'/dat_system/unique_number.dat';
        $file = fopen($uniquenumbersfile, "r+") or exit("Unable to open file!");
        flock($file, LOCK_EX) or exit("Unable to lock file!");

        $thecontents = fread($file, 99);
        $numbertouse = abs($thecontents);

        $nextnumber = $numbertouse + 1;

        fseek($file, 0);
        ftruncate($file, 0);
        fwrite($file, $nextnumber);

        flock($file, LOCK_UN) or exit("Unable to unlock file!");
        fclose($file);
    }


    return $numbertouse;
}

function recycle_unique_number($number) {
    if (trim($number) == '') {
        die('error, number to recycle is empty<br />');
    }

//    echo 'will put unique number '.$number.' in unique_numbers_to_reuse.dat<br />';

    $file = fopen(DAT_SYSTEM_HOME.'/dat_system/unique_numbers_to_reuse.dat', "a") or exit("Unable to open file!");
    flock($file, LOCK_EX) or exit("Unable to lock file!");

    fwrite($file, $number . '
');

    flock($file, LOCK_UN) or exit("Unable to unlock file!");
    fclose($file);
}

function select($index_file_name, $where, $orderby) {
    $tempdata='';
    
    //read index
    $file = fopen(DAT_SYSTEM_HOME."/dat_system/" . $index_file_name, "r") or exit("Unable to open file!");
    flock($file, LOCK_SH) or exit("Unable to lock file!");

    while (!feof($file)) {
        $line_data = trim(fgets($file));
//        echo '$line_data: '.$line_data.'<br />';
        if ($line_data != '') {
            $exploded_line_data = explode(' ', $line_data);
            $datfile_num = $exploded_line_data[1];


            //read dat
            $file2 = fopen(DAT_SYSTEM_HOME."/dat_system/" . $datfile_num . ".dat", "r") or exit("Unable to open file!");
            flock($file2, LOCK_SH) or exit("Unable to lock file!");

            while (!feof($file2)) {
                $line_data2 = trim(fgets($file2));
                $exploded_line_data2 = explode(' ', $line_data2);
                $thishash = $exploded_line_data2[0];
                $resull = $exploded_line_data2[1];

                $data_array = unserialize($resull);

            }

            flock($file2, LOCK_UN) or exit("Unable to unlock file!");
            fclose($file2);
        }
    }
    flock($file, LOCK_UN) or exit("Unable to unlock file!");
    fclose($file);
}

?>