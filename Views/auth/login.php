<h2>Iniciar sesión</h2>
<?php if(!empty($error)): ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
<form method="post">
    <label>Email<br><input type="email" name="email" required></label><br><br>
    <label>Password<br><input type="password" name="password" required></label><br><br>
    <button type="submit">Entrar</button>
</form>
