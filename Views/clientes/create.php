<h2>Crear Cliente</h2>
<form method="post">
    <label>Nombre<br><input type="text" name="nombre" required value="<?php echo htmlspecialchars($nombre ?? ''); ?>"></label><br><br>
    <label>Email<br><input type="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>"></label><br><br>
    <label>Teléfono<br><input type="text" name="telefono" value="<?php echo htmlspecialchars($telefono ?? ''); ?>"></label><br><br>
    <label>Documento<br><input type="text" name="documento" value="<?php echo htmlspecialchars($documento ?? ''); ?>"></label><br><br>
    <button type="submit">Crear cliente</button>
</form>
