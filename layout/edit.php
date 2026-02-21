<?php
include "../config/db.php";
include "../layout/header.php";

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM tbGuest WHERE GuestID=$id"));

if(isset($_POST['update'])){
    $name = $_POST['name'];
    $sex = $_POST['sex'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $passport = $_POST['passport'];
    $card = $_POST['card'];

    mysqli_query($conn,"UPDATE tbGuest SET
    GuestName='$name',
    Sex='$sex',
    Phone='$phone',
    GuestAddress='$address',
    PassportNo='$passport',
    CardNo='$card'
    WHERE GuestID=$id");

    header("Location:list.php");
}
?>

<h2>Edit Guest</h2>
<form method="post">
<label for="name">Name:</label>
<input type="text" id="name" name="name" value="<?= $data['GuestName'] ?>" required><br>

<label for="sex">Sex:</label>
<select id="sex" name="sex" required>
<option value="Male" <?= $data['Sex']=="Male"?"selected":"" ?>>Male</option>
<option value="Female" <?= $data['Sex']=="Female"?"selected":"" ?>>Female</option>
</select><br>

<label for="phone">Phone:</label>
<input type="text" id="phone" name="phone" value="<?= $data['Phone'] ?>"><br>

<label for="address">Address:</label>
<textarea id="address" name="address"><?= $data['GuestAddress'] ?></textarea><br>

<label for="passport">Passport:</label>
<input type="text" id="passport" name="passport" value="<?= $data['PassportNo'] ?>"><br>

<label for="card">Card:</label>
<input type="text" id="card" name="card" value="<?= $data['CardNo'] ?>"><br><br>

<button type="submit" name="update">Update</button>
<a href="list.php" style="display: inline-block; padding: 12px 25px; background-color: #6c757d; color: white; border-radius: 4px; margin-left: 10px; text-decoration: none;">← Back</a>
</form>

<?php include "../layout/footer.php"; ?>

