<?php

$dashboardMenu = array("profile", "user", "form", "permission");

$toReturn = "<aside><ul style='list-style:none'>";

foreach($dashboardMenu as $menu){
    $class="";
    if($_GET['c']==$menu){
        $class="active";
    }
    $toReturn.="<li><a href='/settings?c=$menu' class=$class>".makeFirstLetterUpper($menu)."</a></li>";
}

$toReturn.="</ul></aside>";

return $toReturn;   
?>