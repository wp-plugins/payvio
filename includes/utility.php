<?php

// Usage
//$x = 'a';
//$y = 'b';
//echo Format('1=[1], 0=[0]', $x, $y);
function Format($format /*, ... */) {
    $args = func_get_args();
    return preg_replace_callback('/\[(\\d)\]/',
        function($m) use($args) {
            // might want to add more error handling here...
            return $args[$m[1]+1];
        },
        $format
    );
}

?>