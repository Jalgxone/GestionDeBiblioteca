<h2>Crear Préstamo</h2>
<?php if(!empty($error)): ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
<form method="post">
    <label>Libro<br>
        <select name="libro_id" required>
            <option value="">-- Seleccione un libro --</option>
            <?php foreach($libros as $l): ?>
                <option value="<?php echo $l['id']; ?>"><?php echo htmlspecialchars($l['titulo']) . ' (Disponibles: ' . $l['cantidad_disponible'] . ')'; ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <br><br>
    <label>Cliente<br>
        <select name="cliente_id" required>
            <option value="">-- Seleccione un cliente --</option>
            <?php foreach($clientes as $c): ?>
                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nombre']) . ($c['email'] ? ' <' . htmlspecialchars($c['email']) . '>' : ''); ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <br><br>
    <button type="submit" class="btn">Prestar</button>
</form>
