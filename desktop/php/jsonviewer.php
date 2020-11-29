<style type="text/css">
    * {
        font-family: Helvetica, Arial, sans-serif;
    }

    ul, li {
        line-height: 1.2;
    }
</style>


<?php

function make_tree($var)
{
    global $tree;
    foreach ($var as $key => $value) {
        if (is_array($value)) {
            // Check if the value is empty, show 'empty' arrow then
            if (empty($value)) {
                $arrow = "arrow_open";
                $title = "This node has no children";
                $class = 'arrow empty';
            } else {
                $arrow = "arrow";
                $title = "Click on the arrow to view its children";
                $class = 'arrow children';
            }

            //$tree .= '<li><img src="img/' . $arrow . '.png" class="' . $class .'" ' . 'alt="+" title="' . $title .'" /><FONT COLOR="#FF0000">'.$key."</FONT>\t<ul>";

            $tree .= '<img src="./plugins/alexaapi/desktop/php/img/' . $arrow . '.png" ' .
                'alt="+" title="' . $title . '" /><FONT COLOR="#FF0000"> ' . $key . "</FONT>\t<ul>";

            make_tree($value);
        } else {
            $tree .= '<li><FONT COLOR="#0000FF">' . $key . '</FONT> :';

            switch ($value) {
                case "true":
                    $tree .= ' <FONT COLOR="#336600">true</FONT></li>';
                    break;
                case "false":
                    $tree .= ' <FONT COLOR="#336600">false</FONT></li>';
                    break;
                case "":
                    $tree .= ' <FONT COLOR="#808080">null</FONT></li>';
                    break;
                default:
                    $tree .= ' <FONT COLOR="#FF00FF">' . $value . '</FONT></li>';
            }


            if ($value == "true")
                $value = "true";


        }
    }
    $tree .= "</ul></li>";
    return $tree;
}

function json_viewer($json)
{


    // get first occurence of curly brace and square brace
    $curly = strpos($json, "{");
    $square = strpos($json, "[");

    // No curly or square bracket means this is not JSON data
    if (($curly === false) && ($square === false)) {
        return "Invalid JSON data (no '{' or '[' found)";
    } else {
        // There is a case when you have a feed with [{
        // so get the first one
        if (($curly !== false) && ($square !== false)) {
            if ($curly < $square) {
                $square = false;
            } else {
                $curly = false;
            }
        }

        // get the last curly or square brace
        if ($curly !== false) {
            $firstchar = $curly;
            $lastchar = strrpos($json, "}");
        } else if ($square !== false) {
            $firstchar = $square;
            $lastchar = strrpos($json, "]");
        }

        if ($lastchar === false) {
            return "Invalid JSON data (no closing '}' or ']' found)";
        }

        // Give warning if $firstchar is not the first character
        if ($firstchar > 0) {
            $warning = "---WARNING---\n";
            $warning .= "Invalid JSON data that does not begin with '{' or '[' might give unexpected results\n";
        }
    }
    // get the JSON data between the first and last curly or square brace
    $json = substr($json, $firstchar, ($lastchar - $firstchar) + 1);

    // decode json data
    $data = json_decode($json, true);

    if (!$data) {
        if (isset($_POST['showinvalidjson'])) {
            // Show invalid JSON anyway, do sanitize some stuff
            return "This JSON data is invalid, but we show it anyway: <br />" .
                htmlentities($json);
        } else {
            return "Invalid JSON data (could not decode JSON data)";
        }
    }

    if (isset($warning)) {
        echo $warning;
    }

    // Check for 'raw output'
    if (isset($_POST['rawoutput'])) {
        die(print_r($data, false));
    }

    // we need to make the first 'root' tree element
    //$out  = '<ul id="root"><li><img src="img/arrow.png" class="arrow" alt="+" />ROOT<ul id="first">';
    //$out  = '<ul id="root"><li><img src="./plugins/alexaapi/desktop/php/img/arrow.png" class="arrow" alt="+" /><ul id="first">';
    $out = '<ul id="root"><ul id="first">';
    $out .= make_tree($data);
    $out .= "</ul></li></ul>";

    $tree = '';
    return $out;

}

//echo getcwd();//=/var/www/html


// $tree = '';

?>