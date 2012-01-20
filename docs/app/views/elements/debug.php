<?php
if (DEBUG) {
    echo '<div id="debug">';

    Log::in(get_execution_time(), 'Execution time');
    Log::out();
    
    echo '</div>';
}