<h2>Registro</h2>
<?php if(!empty($error)): ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
<form method="post">
    <label>Nombre<br><input type="text" name="nombre" required value="<?php echo htmlspecialchars($nombre ?? ''); ?>"></label><br><br>
    <label>Email<br><input type="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>"></label><br><br>
    <label>Password<br><input type="password" name="password" required></label><br><br>
    <button type="submit">Registrar</button>
</form>
