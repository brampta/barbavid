<?php
$cols=5;

ini_set("user_agent", "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
ini_set("max_execution_time", 0);
ini_set("memory_limit", "10000M");

$xml = simplexml_load_file('http://www.affiliatebuzz.com/feeds/top_100_rss?ref=85299');
//print_r($xml);

$count=0;
echo '<html><body><center><table>';
$countcols=0;
foreach ($xml->children() as $child) {
    
    //echo '<hr />';
    $childname = $child->getName();
    //echo '<b>' . $childname . ':</b><br />';
    //print_r($child);

    if ($childname == 'entry') {
        $count++;
        $countcols++;
        if($countcols==1)
        {echo '<tr>';}
        $entry_title=$child->title;
        $entry_link=$child->link->attributes()->href;
        $content=$child->content;
        //echo 'title is '.$entry_title.'<br />';
        //echo 'link is '.$entry_link.'<br />';
        //echo 'content is '.htmlspecialchars($content).'<br />';
        echo '<td style="width:206px;vertical-align:top;"><center>'.html_entity_decode($content).'</center></td>';
        if($countcols==$cols)
        {echo '</tr>'; $countcols=0;}
    }
}
echo '</table></center></body></html>';
?>