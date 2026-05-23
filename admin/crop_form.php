<?php
require_once __DIR__ . '/common.php';
require_login();
$mysqli = connect_db();
$errors = [];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$crop = ['crop_name' => '', 'description' => '', 'quantity' => 0, 'unit_price' => '', 'image_path' => ''];
if ($id > 0) {
    $stmt = $mysqli->prepare('SELECT * FROM crop_stock WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $crop = $result->fetch_assoc() ?: $crop;
    $stmt->close();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $crop_name = trim($_POST['crop_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $quantity = (int)($_POST['quantity'] ?? 0);
    $unit_price = trim($_POST['unit_price'] ?? '');
    $image_path = $crop['image_path'];

    if ($crop_name === '') {
        $errors[] = 'Crop name is required.';
    }
    if ($quantity < 0) {
        $errors[] = 'Quantity cannot be negative.';
    }
    if (!is_numeric($unit_price) || $unit_price < 0) {
        $errors[] = 'Enter a valid price.';
    }

    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload = $_FILES['image'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($upload['type'], $allowed, true)) {
            $errors[] = 'Only JPG, PNG, and GIF images are allowed.';
        } else {
            $extension = pathinfo($upload['name'], PATHINFO_EXTENSION);
            $filename = uniqid('crop_', true) . '.' . $extension;
            if (!is_dir(UPLOAD_DIR) && !mkdir(UPLOAD_DIR, 0755, true)) {
                $errors[] = 'Unable to create upload directory.';
            } else {
                $destination = UPLOAD_DIR . $filename;
                if (move_uploaded_file($upload['tmp_name'], $destination)) {
                    if ($image_path && is_file(__DIR__ . '/../' . $image_path)) {
                        unlink(__DIR__ . '/../' . $image_path);
                    }
                    $image_path = UPLOAD_URL . $filename;
                } else {
                    $errors[] = 'Failed to move uploaded image.';
                }
            }
        }
    }

    if (empty($errors)) {
        if ($id > 0) {
            $stmt = $mysqli->prepare('UPDATE crop_stock SET crop_name = ?, description = ?, quantity = ?, unit_price = ?, image_path = ? WHERE id = ?');
            $stmt->bind_param('ssidsi', $crop_name, $description, $quantity, $unit_price, $image_path, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $mysqli->prepare('INSERT INTO crop_stock (crop_name, description, quantity, unit_price, image_path) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('ssids', $crop_name, $description, $quantity, $unit_price, $image_path);
            $stmt->execute();
            $stmt->close();
        }
        header('Location: crops.php');
        exit;
    }
    $crop = ['crop_name' => $crop_name, 'description' => $description, 'quantity' => $quantity, 'unit_price' => $unit_price, 'image_path' => $image_path];
}
render_admin_header($id > 0 ? 'Edit Crop' : 'Add Crop');
?>
<div class="card">
    <h2><?php echo $id > 0 ? 'Edit Crop' : 'Add Crop'; ?></h2>
    <?php if ($errors): ?><div class="alert alert-error"><ul><?php foreach ($errors as $error): ?><li><?php echo htmlspecialchars($error); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-row">
            <label for="crop_name">Crop Name</label>
            <input type="text" id="crop_name" name="crop_name" value="<?php echo htmlspecialchars($crop['crop_name']); ?>" required>
        </div>
        <div class="form-row">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($crop['description']); ?></textarea>
        </div>
        <div class="form-row">
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($crop['quantity']); ?>" min="0" required>
        </div>
        <div class="form-row">
            <label for="unit_price">Unit Price</label>
            <input type="number" id="unit_price" name="unit_price" step="0.01" value="<?php echo htmlspecialchars($crop['unit_price']); ?>" required>
        </div>
        <div class="form-row">
            <label for="image">Crop Image</label>
            <input type="file" id="image" name="image" accept="image/*">
            <?php if ($crop['image_path']): ?>
                <p>Current image:<br><img src="../<?php echo htmlspecialchars($crop['image_path']); ?>" alt="Crop Image" style="max-width:160px; margin-top: 8px;"></p>
            <?php endif; ?>
        </div>
        <button class="button" type="submit"><?php echo $id > 0 ? 'Update Crop' : 'Save Crop'; ?></button>
    </form>
</div>
<?php render_admin_footer();
