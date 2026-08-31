<?php
session_start();
require_once __DIR__ . '/db.php';
criar_tabelas();

$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'login') {
        if (hash_equals(ADMIN_PASSWORD, (string)($_POST['senha'] ?? ''))) {
            $_SESSION['admin_logado'] = true;
        } else {
            $erro = 'Senha incorreta.';
        }
    } elseif ($acao === 'logout') {
        unset($_SESSION['admin_logado']);
    } elseif ($acao === 'salvar_config' && !empty($_SESSION['admin_logado'])) {
        $stmt = db()->prepare('UPDATE config SET nome_evento=?, data_evento=?, horario=?, local=?, endereco=?, traje=?, mensagem=? WHERE id=1');
        $stmt->execute([
            trim($_POST['nome_evento'] ?? ''),
            trim($_POST['data_evento'] ?? ''),
            trim($_POST['horario'] ?? ''),
            trim($_POST['local'] ?? ''),
            trim($_POST['endereco'] ?? ''),
            trim($_POST['traje'] ?? ''),
            trim($_POST['mensagem'] ?? '')
        ]);
        $mensagem = 'Configuração salva com sucesso!';
    }
}

$logado = !empty($_SESSION['admin_logado']);
$config = obter_config();
$confirmacoes = db()->query('SELECT * FROM confirmacoes ORDER BY data_hora DESC')->fetchAll();
$total = count($confirmacoes);
$presentes = 0;
foreach ($confirmacoes as $c) {
    if (strpos($c['resposta'], 'Sim') === 0) {
        $presentes += 1 + (int)$c['pessoas'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Chá de Bebê</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f3fa; color: #2d3748; padding: 2rem 1rem; }
  .wrap { max-width: 960px; margin: 0 auto; }
  .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.08); padding: 1.5rem; margin-bottom: 1.5rem; }
  h1 { font-size: 1.4rem; margin-bottom: .3rem; }
  h2 { font-size: 1.1rem; margin-bottom: 1rem; color: #b0306a; }
  p.sub { color: #718096; font-size: .9rem; margin-bottom: 1rem; }
  label { display: block; font-weight: 600; font-size: .85rem; margin: .9rem 0 .3rem; }
  input[type=text], input[type=date], input[type=password], input[type=time], textarea {
    width: 100%; padding: .6rem .8rem; border: 1px solid #d4d4dd; border-radius: 8px; font-size: .95rem;
  }
  textarea { min-height: 90px; resize: vertical; }
  .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 1rem; }
  .btn {
    display: inline-block; border: none; cursor: pointer; border-radius: 8px; padding: .65rem 1.2rem;
    font-size: .95rem; font-weight: 600; color: #fff; background: #e8869c; text-decoration: none; margin-top: 1rem;
  }
  .btn:hover { background: #d96d86; }
  .btn.sec { background: #718096; }
  .btn.sec:hover { background: #5c6a7a; }
  .msg { padding: .8rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: .9rem; }
  .msg.ok { background: #e6f7ee; color: #1e7a46; border: 1px solid #b7e6cd; }
  .msg.err { background: #fdecec; color: #c0392b; border: 1px solid #f5c6c6; }
  table { width: 100%; border-collapse: collapse; font-size: .9rem; }
  th, td { text-align: left; padding: .6rem .5rem; border-bottom: 1px solid #eee; }
  th { background: #faf7fb; color: #555; font-size: .8rem; text-transform: uppercase; }
  .badge { display: inline-block; padding: .15rem .6rem; border-radius: 99px; font-size: .75rem; font-weight: 600; }
  .badge.sim { background: #e6f7ee; color: #1e7a46; }
  .badge.nao { background: #fdecec; color: #c0392b; }
  .stats { display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem; }
  .stat { flex: 1; min-width: 140px; background: #faf7fb; border: 1px solid #eee; border-radius: 10px; padding: .8rem; text-align: center; }
  .stat b { display: block; font-size: 1.4rem; color: #b0306a; }
  .stat span { font-size: .78rem; color: #718096; }
  .login-box { max-width: 380px; margin: 6rem auto; text-align: center; }
  .logout { float: right; }
  .empty { color: #718096; text-align: center; padding: 1.5rem 0; }
  a.back { color: #b0306a; text-decoration: none; font-size: .9rem; }
  @media (max-width: 640px) { .grid { grid-template-columns: 1fr; } }
</style>
</head>
<body>
<div class="wrap">

<?php if (!$logado): ?>
  <div class="card login-box">
    <h1>🔐 Área Administrativa</h1>
    <p class="sub">Entre para gerenciar a configuração do site e as confirmações dos convidados.</p>
    <?php if ($erro): ?><div class="msg err"><?php echo htmlspecialchars($erro); ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="acao" value="login">
      <label for="senha">Senha</label>
      <input type="password" id="senha" name="senha" required autofocus>
      <button type="submit" class="btn">Entrar</button>
    </form>
  </div>
<?php else: ?>

  <div class="card">
    <a href="index.php" class="back">← Ver o site</a>
    <form method="post" style="display:inline; float:right;">
      <input type="hidden" name="acao" value="logout">
      <button type="submit" class="btn sec logout">Sair</button>
    </form>
    <h1>🎉 Administração do Chá de Bebê</h1>
    <p class="sub">Edite as configurações do site e acompanhe as confirmações dos convidados.</p>
    <?php if ($mensagem): ?><div class="msg ok"><?php echo htmlspecialchars($mensagem); ?></div><?php endif; ?>
    <?php if ($erro): ?><div class="msg err"><?php echo htmlspecialchars($erro); ?></div><?php endif; ?>
  </div>

  <div class="card">
    <h2>⚙️ Configuração do Site</h2>
    <form method="post">
      <input type="hidden" name="acao" value="salvar_config">
      <div class="grid">
        <div>
          <label>Nome do Evento</label>
          <input type="text" name="nome_evento" value="<?php echo htmlspecialchars($config['nome_evento']); ?>" required>
        </div>
        <div>
          <label>Data do Evento</label>
          <input type="date" name="data_evento" value="<?php echo htmlspecialchars($config['data_evento']); ?>" required>
        </div>
        <div>
          <label>Horário</label>
          <input type="time" name="horario" value="<?php echo htmlspecialchars($config['horario']); ?>" required>
        </div>
        <div>
          <label>Local (nome)</label>
          <input type="text" name="local" value="<?php echo htmlspecialchars($config['local']); ?>" required>
        </div>
        <div>
          <label>Traje</label>
          <select name="traje" style="width:100%; padding:.6rem .8rem; border:1px solid #d4d4dd; border-radius:8px; font-size:.95rem;">
            <option value="">— Selecione —</option>
            <option value="Tons neutros: bege, creme, taupe, cinza, caramelo, nude"<?php echo $config['traje'] === 'Tons neutros: bege, creme, taupe, cinza, caramelo, nude' ? ' selected' : ''; ?>>Tons neutros: bege, creme, taupe, cinza, caramelo, nude</option>
            <option value="Tons neutros: bege, areia, cáqui, café, nude"<?php echo $config['traje'] === 'Tons neutros: bege, areia, cáqui, café, nude' ? ' selected' : ''; ?>>Tons neutros: bege, areia, cáqui, café, nude</option>
            <option value="Tons neutros: creme, taupe, cinza, avelã, nude"<?php echo $config['traje'] === 'Tons neutros: creme, taupe, cinza, avelã, nude' ? ' selected' : ''; ?>>Tons neutros: creme, taupe, cinza, avelã, nude</option>
            <option value="Tons neutros: areia, caramelo, cáqui, marrom, nude"<?php echo $config['traje'] === 'Tons neutros: areia, caramelo, cáqui, marrom, nude' ? ' selected' : ''; ?>>Tons neutros: areia, caramelo, cáqui, marrom, nude</option>
            <option value="Roupas neutras"<?php echo $config['traje'] === 'Roupas neutras' ? ' selected' : ''; ?>>Roupas neutras</option>
          </select>
        </div>
      </div>
      <label>Endereço</label>
      <input type="text" name="endereco" value="<?php echo htmlspecialchars($config['endereco']); ?>" required>
      <label>Mensagem do Convite</label>
      <textarea name="mensagem" required><?php echo htmlspecialchars($config['mensagem']); ?></textarea>
      <button type="submit" class="btn">Salvar Configuração</button>
    </form>
  </div>

  <div class="card">
    <h2>👥 Convidados Confirmados</h2>
    <div class="stats">
      <div class="stat"><b><?php echo $total; ?></b><span>respostas</span></div>
      <div class="stat"><b><?php echo $presentes; ?></b><span>pessoas presentes</span></div>
    </div>
    <?php if ($confirmacoes): ?>
      <table>
        <thead>
          <tr><th>#</th><th>Nome</th><th>Resposta</th><th>Acompanhantes</th><th>Fralda</th><th>Palpite</th><th>Data/Hora</th></tr>
        </thead>
        <tbody>
          <?php foreach ($confirmacoes as $i => $c): ?>
            <tr>
              <td><?php echo $i + 1; ?></td>
              <td><?php echo htmlspecialchars($c['nome']); ?></td>
              <td><span class="badge <?php echo strpos($c['resposta'], 'Sim') === 0 ? 'sim' : 'nao'; ?>">
                <?php echo htmlspecialchars($c['resposta']); ?>
              </span></td>
              <td><?php echo $c['pessoas'] > 0 ? $c['pessoas'] : '—'; ?></td>
              <td><?php echo htmlspecialchars($c['tamanho_fralda'] ?? '—'); ?></td>
              <td><?php echo htmlspecialchars($c['palpite'] ?? '—'); ?></td>
              <td><?php echo date('d/m/Y H:i', strtotime($c['data_hora'])); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="empty">Nenhuma confirmação registrada ainda.</p>
    <?php endif; ?>
  </div>

<?php endif; ?>
</div>
</body>
</html>
