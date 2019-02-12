<?php
function displayTitle() {
    if (defined('SITE_NAME')) {
        if (SITE_NAME != 'BackRunner\'s ShortLink') {
            echo SITE_NAME;
            return;
        }
    }
    echo "B<mhide>ack</mhide>R<mhide>unner</mhide>'s ShortLink";
}

