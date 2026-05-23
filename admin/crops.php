<?php
require_once __DIR__ . '/common.php';
require_login();
$mysqli = connect_db();
$message = '';
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $mysqli->prepare('SELECT image_path FROM crop_stock WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $crop = $result->fetch_assoc();
    $stmt->close();
    if ($crop && $crop['image_path']) {
        $file = __DIR__ . '/../' . $crop['image_path'];
        if (is_file($file)) {
            unlink($file);
        }
    }
    $stmt = $mysqli->prepare('DELETE FROM crop_stock WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    $message = 'Crop stock item deleted successfully.';
}
$result = $mysqli->query('SELECT * FROM crop_stock ORDER BY created_at DESC');
render_admin_header('Crop Stock');
?>
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Crop Stock</h2>
        <a class="button" href="crop_form.php">Add Crop</a>
    </div>
    <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <table>
        <thead>
            <tr><th>#</th><th>Name</th><th>Quantity</th><th>Unit Price</th><th>Image</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php while ($crop = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $crop['id']; ?></td>
                <td><?php echo htmlspecialchars($crop['crop_name']); ?></td>
                <td><?php echo (int)$crop['quantity']; ?></td>
                <td><?php echo number_format($crop['unit_price'], 2); ?></td>
                <td>
                    <?php if ($crop['image_path']): ?>
                        <img src="../<?php echo htmlspecialchars($crop['image_path']); ?>" alt="<?php echo htmlspecialchars($crop['crop_name']); ?>" style="max-width:100px; max-height:80px;">
                    <?php else: ?>
                        No image
                    <?php endif; ?>
                </td>
                <td>
                    <a class="button" href="crop_form.php?id=<?php echo $crop['id']; ?>">Edit</a>
                    <a class="button" href="crops.php?action=delete&id=<?php echo $crop['id']; ?>" onclick="return confirm('Delete this crop stock item?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php
$result->free();
$mysqli->close();
render_admin_footer();
