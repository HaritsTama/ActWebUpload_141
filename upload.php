

<?php
// Configuration
$target_dir = "uploads/";
$message = '';

// Handle submission
if (isset($_POST['submit'])) {
    $filename = basename($_FILES['fileToUpload']['name']);
    $target_file = $target_dir . $filename;
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check for duplicate
    if (file_exists($target_file)) {
        $message .= "<p class='msg warning'>Berkas <strong>" . htmlspecialchars($filename) . "</strong> sudah ada.</p>";
        $uploadOk = 0;
    }

    // Size limit: 10 MB
    if ($_FILES['fileToUpload']['size'] > 10485760) {
        $message .= "<p class='msg warning'>Berkas terlalu besar (maksimal 10MB).</p>";
        $uploadOk = 0;
    }

    // Allowed formats: JPG, JPEG, PNG, GIF, PDF
    $allowed = ['jpg','jpeg','png','gif','pdf'];
    if (!in_array($fileType, $allowed)) {
        $message .= "<p class='msg warning'>Hanya JPG, JPEG, PNG, GIF & PDF yang diperbolehkan.</p>";
        $uploadOk = 0;
    }

    // Attempt upload
    if ($uploadOk && move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target_file)) {
        $message .= "<p class='msg success'>Berkas <strong>" . htmlspecialchars($filename) . "</strong> berhasil diunggah.</p>";
    } elseif ($uploadOk) {
        $message .= "<p class='msg danger'>Terjadi kesalahan saat mengunggah.</p>";
    } else {
        $message .= "<p class='msg danger'>Unggah dibatalkan.</p>";
    }
}

// Handle deletion
if (isset($_GET['delete_file'])) {
    $file_to_delete = $target_dir . basename($_GET['delete_file']);
    if (file_exists($file_to_delete) && !is_dir($file_to_delete)) {
        if (unlink($file_to_delete)) {
            $message .= "<p class='msg success'>Berkas <strong>" . htmlspecialchars(basename($file_to_delete)) . "</strong> berhasil dihapus.</p>";
        } else {
            $message .= "<p class='msg danger'>Gagal menghapus berkas.</p>";
        }
    } else {
        $message .= "<p class='msg danger'>Berkas tidak ditemukan atau tidak valid.</p>";
    }
}

// Below is HTML to render messages and file list
?>

<!DOCTYPE html>

<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Berkas</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Comic+Neue:ital,wght@1,700&display=swap');
        * { box-sizing: border-box; }
        body {
            font-family: 'Comic Neue', cursive;
            background: linear-gradient(120deg, #f093fb, #f5576c);
            display: flex;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }
        .container {
            background-color: #fff8f0;
            padding: 35px 45px;
            border-radius: 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            width: 350px;
            text-align: center;
            animation: popIn 0.8s ease;
        }
        @keyframes popIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        h2 { color: #ff4081; margin-bottom: 25px; font-size: 26px; }
        .msg { text-align: left; margin-top: 20px; padding: 12px; border-radius: 12px; font-size: 14px; animation: popIn 0.5s ease; }
        .msg.success { background: #d4edda; color: #155724; }
        .msg.warning { background: #fff3cd; color: #856404; }
        .msg.danger { background: #f8d7da; color: #721c24; }
        .file-list { margin-top: 30px; list-style: none; padding: 0; text-align: left; }
        .file-list li { background: #fff0f5; margin-bottom: 12px; padding: 10px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; font-size: 14px; }
        .file-list li a { text-decoration: none; padding: 6px 10px; border-radius: 8px; margin-left: 6px; color: white; font-size: 12px; }
        .view-btn { background: #6c757d; }
        .download-btn { background: #007bff; }
        .delete-btn { background: #dc3545; }
        a:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Daftar Berkas</h2>
        <?php echo $message; ?>
        <ul class="file-list">
            <?php
                $files = scandir($target_dir);
                $uploaded = array_diff($files, ['.','..']);
                foreach ($uploaded as $file) {
                    $path = $target_dir . $file;
                    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    $isImg = in_array($ext, ['jpg','jpeg','png','gif']);
                    $isPdf = ($ext === 'pdf');
                    echo "<li>" . htmlspecialchars($file) . "<div>";
                    if ($isImg || $isPdf) {
                        echo "<a href='#' class='view-btn' data-filepath='" . $path . "'>Lihat</a>";
                    }
                    echo "<a href='" . $path . "' download class='download-btn'>Unduh</a>";
                    echo "<a href='?delete_file=" . urlencode($file) . "' class='delete-btn' onclick='return confirm(\"Yakin hapus $file?\")'>Hapus</a>";
                    echo "</div></li>";
                }
            ?>
        </ul>
    </div>


<script>
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('view-btn')) {
            e.preventDefault();
            const path = e.target.getAttribute('data-filepath');
            const win = window.open('');
            if (path.endsWith('.pdf')) {
                win.document.write(`<iframe src="${path}" style="width:100%;height:100%;border:none;"></iframe>`);
            } else {
                win.document.write(`<img src="${path}" style="max-width:100%;max-height:100%;" />`);
            }
        }
    });
</script>


</body>
</html>
