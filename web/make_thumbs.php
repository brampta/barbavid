<?php

$videos_vault_dir='/home/goblet/barbavid/videos';
//$file_info['file_md5']='4b1a2d0896ebb06a0275fa0eca572c42';
//$chunk_basename='0000000025_0000000050';


$file_info['file_md5']=$_GET['fi'];
$chunk_basename=$_GET['cb'];



$firstchar=substr($file_info['file_md5'],0,1);
$secondchar=substr($file_info['file_md5'],1,1);




$chunk_name=$videos_vault_dir.'/'.$firstchar.'/'.$secondchar.'/'.$file_info['file_md5'].'/'.$chunk_basename.'.mp4';
$temp_thumb_name=$videos_vault_dir.'/'.$firstchar.'/'.$secondchar.'/'.$file_info['file_md5'].'/'.$chunk_basename.'_temp.jpg';

if(!file_exists($chunk_name))
{die('chunk not existing..');}


$makepic="/var/www/barbavid/uploadserver1/ffmpest -v 0 -ss 750 -i ".escapeshellarg($chunk_name)." -f image2 -vframes 1 -y ".escapeshellarg($temp_thumb_name);
echo htmlspecialchars($makepic).'<br />';
unset($outputz);
exec('nice -n 19 '.$makepic.' 2>&1',$outputz);
foreach($outputz as $key => $value)
{echo $value.'<br />';}



//make pics from screenshot with GD cos fucking imagemagick is bugged
$size=getimagesize($temp_thumb_name);
$thumbwidth=120;
$thumbheight=50;
$largewidth=728;
$largeheight=305;

//make thumb
$width_pourun=$size[0]/$thumbwidth;
$height_pourun=$size[1]/$thumbheight;
if($width_pourun>$height_pourun)
{
    $optimal_width=$thumbwidth;
    $optimal_height=($thumbwidth*$size[1])/$size[0];
    $dst_x=0;
    $dst_y=($thumbheight-$optimal_height)/2;
}
else
{
    $optimal_width=($thumbheight*$size[0])/$size[1];
    $optimal_height=$thumbheight;
    $dst_x=($thumbwidth-$optimal_width)/2;
    $dst_y=0;
}
$img=imagecreatetruecolor($thumbwidth,$thumbheight);
$bg=@imagecreate($thumbwidth,$thumbheight);
$background_color=imagecolorallocate($bg,0,0,0);
imagecopy($img,$bg,0,0,0,0,$thumbwidth,$thumbheight);
$src_img=imagecreatefromjpeg($temp_thumb_name);
//imagecopyresampled ( resource $dst_image , resource $src_image , int $dst_x , int $dst_y , int $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h )
imagecopyresampled($img, $src_img, $dst_x, $dst_y, 0, 0, $optimal_width, $optimal_height, $size[0], $size[1]);
$thumb_name=$videos_vault_dir.'/'.$firstchar.'/'.$secondchar.'/'.$file_info['file_md5'].'/'.$chunk_basename.'_thumb.png';
//imagejpeg($img,$thumb_name);
imagepng($img,$thumb_name);
imagedestroy($img);


//make large
$width_pourun=$size[0]/$largewidth;
$height_pourun=$size[1]/$largeheight;
if($width_pourun>$height_pourun)
{
    $optimal_width=$largewidth;
    $optimal_height=($largewidth*$size[1])/$size[0];
    $dst_x=0;
    $dst_y=($largeheight-$optimal_height)/2;
}
else
{
    $optimal_width=($largeheight*$size[0])/$size[1];
    $optimal_height=$largeheight;
    $dst_x=($largewidth-$optimal_width)/2;
    $dst_y=0;
}
$img=imagecreatetruecolor($largewidth,$largeheight);
$bg=@imagecreate($largewidth,$largeheight);
$background_color=imagecolorallocate($bg,0,0,0);
imagecopy($img,$bg,0,0,0,0,$largewidth,$largeheight);
$src_img=imagecreatefromjpeg($temp_thumb_name);
imagecopyresampled($img, $src_img, $dst_x, $dst_y, 0, 0, $optimal_width, $optimal_height, $size[0], $size[1]);
$thumb_name=$videos_vault_dir.'/'.$firstchar.'/'.$secondchar.'/'.$file_info['file_md5'].'/'.$chunk_basename.'_large.png';
//imagejpeg($img,$thumb_name);
imagepng($img,$thumb_name);
imagedestroy($img);





$remove_tempthumb='rm '.escapeshellarg($temp_thumb_name);
echo htmlspecialchars($remove_tempthumb).'<br />';
unset($outputz);
exec('nice -n 19 '.$remove_tempthumb.' 2>&1',$outputz);
foreach($outputz as $key => $value)
{echo $value.'<br />';}



?>