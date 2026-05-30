<?php
$zip = new ZipArchive;
if ($zip->open('silkroad.zip') === TRUE) {
    $zip->extractTo('./');
    $zip->close();
    echo 'تم رفع وفك المجلدات بنجاح!';
} else {
    echo 'فشل فك الضغط';
}
