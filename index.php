<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($nomeEvento); ?> - Helena ou Henrique</title>
  <meta name="description" content="Venha comemorar conosco o Chá de Bebê e Revelação! Confirme sua presença e escolha sua fralda e mimo na lista.">
  
  <!-- Open Graph / Redes Sociais & WhatsApp -->
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?php echo htmlspecialchars($nomeEvento); ?> - Helena ou Henrique 👶💕">
  <meta property="og:description" content="Venha comemorar conosco esse momento inesquecível! Confirme sua presença e confira os mimos.">
  <meta property="og:image" content="images/hero.jpg">
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:title" content="<?php echo htmlspecialchars($nomeEvento); ?> - Helena ou Henrique 👶💕">
  <meta property="twitter:description" content="Venha comemorar conosco esse momento inesquecível! Confirme sua presença e confira os mimos.">
  <meta property="twitter:image" content="images/hero.jpg">
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,700;1,600&display=swap" rel="stylesheet">
  
  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Canvas Confetti CDN -->
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php
  require_once __DIR__ . '/db.php';
  $config = obter_config();
  $nomeEvento   = $config['nome_evento'];
  $dataEvento   = $config['data_evento'];
  $horario      = $config['horario'];
  $local        = $config['local'];
  $endereco     = $config['endereco'];
  $mensagem     = $config['mensagem'];
  $dataFormatada = formatar_data_pt($dataEvento);
  $dataCompleta  = formatar_data_completa_pt($dataEvento);
  $enderecoUrl   = urlencode($endereco);
  $traje         = $config['traje'];

  // Paleta de cores de cada opção de traje
  $paletasTraje = [
    'Tons neutros: bege, creme, taupe, cinza, caramelo, nude' => [
      ['Bege', '#D2B48C'], ['Creme', '#F5F0E1'],
      ['Cinza', '#9CA3AF'], ['Caramelo', '#A67C52'], ['Nude', '#E8C7B7'],
    ],
    'Tons neutros: bege, areia, cáqui, café, nude' => [
      ['Bege', '#D2B48C'], ['Areia', '#C2B280'], ['Cáqui', '#B3A86A'],
      ['Café', '#6F4E37'], ['Nude', '#E8C7B7'],
    ],
    'Tons neutros: creme, taupe, cinza, avelã, nude' => [
      ['Creme', '#F5F0E1'], ['Cinza', '#9CA3AF'],
      ['Avelã', '#B4835B'], ['Nude', '#E8C7B7'],
    ],
    'Tons neutros: areia, caramelo, cáqui, marrom, nude' => [
      ['Areia', '#C2B280'], ['Caramelo', '#A67C52'], ['Cáqui', '#B3A86A'],
      ['Marrom', '#5C4033'], ['Nude', '#E8C7B7'],
    ],
  ];
  $paletaTraje = $paletasTraje[$traje] ?? [
    ['Bege', '#D2B48C'], ['Creme', '#F5F0E1'],
    ['Cinza', '#9CA3AF'], ['Caramelo', '#A67C52'], ['Nude', '#E8C7B7'],
  ];
  ?>

  <!-- Header / Navigation -->
  <header class="navbar" id="navbar">
    <div class="container nav-container">
      <a href="#hero" class="nav-logo">
        <span class="logo-icon"><i class="fa-solid fa-baby-carriage"></i></span>
        <span class="logo-text">Chá de Bebê <small>& Revelação</small></span>
      </a>

      <nav class="nav-menu" id="navMenu">
        <ul class="nav-list">
          <li><a href="#hero" class="nav-link active"><i class="fa-solid fa-house"></i> Início</a></li>
          <li><a href="#fraldas" class="nav-link"><i class="fa-solid fa-baby"></i> Fraldas</a></li>
          <li><a href="#confirmacao" class="nav-link"><i class="fa-solid fa-circle-check"></i> RSVP</a></li>
          <li><a href="#traje" class="nav-link"><i class="fa-solid fa-shirt"></i> Traje</a></li>
          <li><a href="#local" class="nav-link"><i class="fa-solid fa-location-dot"></i> Local</a></li>
        </ul>
      </nav>

      <div class="nav-actions">
        <!-- Share Link Button -->
        <button id="navShareBtn" class="share-nav-btn" aria-label="Compartilhar Convite" title="Compartilhar Convite">
          <i class="fa-solid fa-share-nodes"></i>
          <span class="share-nav-text">Compartilhar</span>
        </button>

        <!-- Dark/Light Theme Toggle -->
        <button id="themeToggle" class="theme-toggle-btn" aria-label="Alternar tema">
          <i class="fa-solid fa-moon icon-moon"></i>
          <i class="fa-solid fa-sun icon-sun"></i>
        </button>

        <!-- Mobile Hamburger Toggle -->
        <button id="menuToggle" class="menu-toggle-btn" aria-label="Menu de navegação">
          <i class="fa-solid fa-bars icon-bars"></i>
          <i class="fa-solid fa-xmark icon-close"></i>
        </button>
      </div>
    </div>
  </header>

  <!-- Hero Section -->
  <section id="hero" class="hero-section">
    <div class="hero-bg-overlay"></div>
    <div class="hero-img-container">
      <img src="images/hero.jpg" alt="Chá de Bebê e Revelação" class="hero-img">
    </div>

    <div class="container hero-content">
      <div class="reveal-badge">
        <span class="badge-pink"><i class="fa-solid fa-venus"></i> Helena</span>
        <span class="badge-or">ou</span>
        <span class="badge-blue"><i class="fa-solid fa-mars"></i> Henrique</span>
      </div>

      <h1 class="hero-title"><?php echo htmlspecialchars($nomeEvento); ?></h1>
      <p class="hero-subtitle">Um novo capítulo de amor está prestes a começar!</p>
      <p class="hero-message"><?php echo htmlspecialchars($mensagem); ?></p>

      <!-- Event Details Cards -->
      <div class="hero-details">
        <div class="detail-card" data-reveal="fade-up">
          <i class="fa-regular fa-calendar-days detail-icon"></i>
          <div class="detail-info">
            <span class="detail-label">Data</span>
            <strong class="detail-value"><?php echo htmlspecialchars($dataFormatada); ?></strong>
          </div>
        </div>

        <div class="detail-card" data-reveal="fade-up" data-delay="100">
          <i class="fa-regular fa-clock detail-icon"></i>
          <div class="detail-info">
            <span class="detail-label">Horário</span>
            <strong class="detail-value"><?php echo htmlspecialchars($horario); ?></strong>
          </div>
        </div>

        <div class="detail-card" data-reveal="fade-up" data-delay="200">
          <i class="fa-solid fa-map-location-dot detail-icon"></i>
          <div class="detail-info">
            <span class="detail-label">Local</span>
            <strong class="detail-value"><?php echo htmlspecialchars($endereco); ?></strong>
          </div>
        </div>
      </div>

      <!-- Live Countdown Timer -->
      <div class="countdown-container" data-reveal="zoom-in">
        <h3 class="countdown-title"><i class="fa-solid fa-hourglass-half"></i> Contagem Regressiva para o Grande Dia</h3>
        <div id="countdown" class="countdown-grid">
          <div class="count-box">
            <span id="days" class="count-num">00</span>
            <span class="count-label">Dias</span>
          </div>
          <div class="count-divider">:</div>
          <div class="count-box">
            <span id="hours" class="count-num">00</span>
            <span class="count-label">Horas</span>
          </div>
          <div class="count-divider">:</div>
          <div class="count-box">
            <span id="minutes" class="count-num">00</span>
            <span class="count-label">Minutos</span>
          </div>
          <div class="count-divider">:</div>
          <div class="count-box">
            <span id="seconds" class="count-num">00</span>
            <span class="count-label">Segundos</span>
          </div>
        </div>
      </div>

      <!-- Hero Call to Action -->
      <div class="hero-actions" data-reveal="fade-up">
        <a href="#confirmacao" class="btn btn-primary btn-lg pulse-glow">
          <i class="fa-solid fa-envelope-open-text"></i> Confirmar Presença
        </a>
        <a href="#fraldas" class="btn btn-outline btn-lg">
          <i class="fa-solid fa-gift"></i> Ver Mimo
        </a>
        <button id="heroShareBtn" class="btn btn-share btn-lg">
          <i class="fa-solid fa-share-nodes"></i> Compartilhar Link
        </button>
      </div>

      <!-- Interactive Gender Prediction Poll -->
      <div class="gender-poll-card" data-reveal="fade-up">
        <h4><i class="fa-solid fa-wand-magic-sparkles"></i> Qual o seu palpite?</h4>
        <p>Ajude-nos a adivinhar! O que você acha que vem por aí?</p>
        <div class="poll-buttons">
          <button id="voteBoyBtn" class="poll-btn poll-boy">
            <i class="fa-solid fa-child"></i> Menino (Henrique)
            <span id="boyVotesCount" class="vote-badge">0</span>
          </button>
          <button id="voteGirlBtn" class="poll-btn poll-girl">
            <i class="fa-solid fa-child-dress"></i> Menina (Helena)
            <span id="girlVotesCount" class="vote-badge">0</span>
          </button>
        </div>
        <div class="poll-progress-bar">
          <div id="pollProgressBoy" class="progress-boy" style="width: 50%;"></div>
          <div id="pollProgressGirl" class="progress-girl" style="width: 50%;"></div>
        </div>
        <span id="pollStatusText" class="poll-status">Faça seu voto para ver os palpites!</span>
      </div>
    </div>
  </section>

  <!-- Lista de Fraldas Section -->
  <section id="fraldas" class="section fraldas-section">
    <div class="container">
      <div class="section-header" data-reveal="fade-up">
        <span class="section-tag"><i class="fa-solid fa-baby"></i> Passo 1: Escolha o seu Mimo</span>
        <h2 class="section-title">Mimo</h2>
        <p class="section-subtitle">Escolha o tamanho da fralda que você vai presentear e, se preferir, adicione um <strong>mimo</strong> no card Mimo.</p>
        <div class="title-divider"><span></span><i class="fa-solid fa-baby-carriage"></i><span></span></div>
      </div>

      <div class="fraldas-grid">
        <!-- Fralda P -->
        <div class="fralda-card" data-reveal="fade-up" data-delay="0">
          <div class="fralda-badge">Fralda P</div>
          <div class="fralda-icon-box">
            <i class="fa-solid fa-baby"></i>
          </div>
          <h3 class="fralda-title">Fralda P</h3>
          <p class="fralda-weight">Indicado para bebês de 3kg a 5kg</p>
          <button class="btn btn-select-item btn-select-fralda" data-fralda="Fralda Tamanho P">
            <i class="fa-solid fa-plus"></i> Escolher este item
          </button>
        </div>

        <!-- Fralda M -->
        <div class="fralda-card popular" data-reveal="fade-up" data-delay="100">
          <div class="fralda-badge highlight">Mais Recomendado</div>
          <div class="fralda-icon-box">
            <i class="fa-solid fa-baby"></i>
          </div>
          <h3 class="fralda-title">Fralda M</h3>
          <p class="fralda-weight">Indicado para bebês de 5kg a 9kg</p>
          <button class="btn btn-primary btn-select-item btn-select-fralda" data-fralda="Fralda Tamanho M">
            <i class="fa-solid fa-plus"></i> Escolher este item
          </button>
        </div>

        <!-- Fralda G -->
        <div class="fralda-card" data-reveal="fade-up" data-delay="200">
          <div class="fralda-badge">Fralda G</div>
          <div class="fralda-icon-box">
            <i class="fa-solid fa-baby"></i>
          </div>
          <h3 class="fralda-title">Fralda G</h3>
          <p class="fralda-weight">Indicado para bebês de 9kg a 12kg</p>
          <button class="btn btn-select-item btn-select-fralda" data-fralda="Fralda Tamanho G">
            <i class="fa-solid fa-plus"></i> Escolher este item
          </button>
        </div>

        <!-- Mimo -->
        <div class="fralda-card mimo-combo-card" data-reveal="fade-up" data-delay="300">
          <div class="fralda-badge gold">Mimo</div>
          <div class="fralda-icon-box gold">
            <i class="fa-solid fa-gift"></i>
          </div>
          <h3 class="fralda-title">Mimo</h3>
          <p class="fralda-desc">Caso não tenha ideia de mimo, aqui estão algumas opções.</p>
          <button type="button" class="btn btn-select-item btn-open-mimos" id="openMimoListBtn">
            <i class="fa-solid fa-list-check"></i> Ver ideias de mimos
          </button>
        </div>

      </div>

    </div>
  </section>

  <!-- Formulário de Confirmação Section (RSVP) -->
  <section id="confirmacao" class="section confirmacao-section alt-bg">
    <div class="container">
      <div class="section-header" data-reveal="fade-up">
        <span class="section-tag"><i class="fa-solid fa-paper-plane"></i> RSVP</span>
        <h2 class="section-title">Confirmar Presença</h2>
        <p class="section-subtitle">Por favor, confirme sua presença até o dia 15 de Setembro de 2026 para que possamos organizar tudo com carinho.</p>
        <div class="title-divider"><span></span><i class="fa-solid fa-envelope"></i><span></span></div>
      </div>

      <div class="form-container" data-reveal="fade-up">
        <form id="rsvpForm" class="rsvp-form">
          <div class="form-group attendance-group">
            <label><i class="fa-solid fa-question-circle"></i> Você vai comparecer ao evento? <span class="required">*</span></label>
            <div class="attendance-options">
              <label class="attendance-card attendance-yes">
                <input type="radio" name="willAttend" value="Sim, vou comparecer" required>
                <div class="attendance-content">
                  <i class="fa-solid fa-circle-check"></i>
                  <span>Sim, vou comparecer!</span>
                </div>
              </label>

              <label class="attendance-card attendance-no">
                <input type="radio" name="willAttend" value="Não poderei comparecer" required>
                <div class="attendance-content">
                  <i class="fa-solid fa-circle-xmark"></i>
                  <span>Não poderei comparecer</span>
                </div>
              </label>
            </div>
          </div>

          <div class="form-group">
            <label for="guestName"><i class="fa-solid fa-user"></i> Seu Nome <span class="required">*</span></label>
            <input type="text" id="guestName" name="guestName" placeholder="Digite seu nome completo" required autocomplete="name">
          </div>

          <div class="form-group">
            <label><i class="fa-solid fa-wand-magic-sparkles"></i> Seu palpite (votação no topo da página)</label>
            <p id="palpiteStatus" class="palpite-status">Não registrado ainda. Vote na seção "Qual o seu palpite?" no topo da página.</p>
          </div>

          <div class="form-group guests-group" id="guestsGroup">
            <label for="guestsCount"><i class="fa-solid fa-users"></i> Quantos acompanhantes você vai levar? <span class="required">*</span></label>
            <input type="number" id="guestsCount" name="guestsCount" min="0" max="20" value="0" placeholder="0">
          </div>

          <button type="submit" id="submitBtn" class="btn btn-primary btn-block btn-lg">
            <i class="fa-solid fa-paper-plane"></i> Confirmar Resposta
          </button>
        </form>

        <!-- Aviso de confirmação única por dispositivo -->
        <div id="alreadyConfirmed" class="already-confirmed hidden">
          <div class="already-confirmed-icon"><i class="fa-solid fa-circle-check"></i></div>
          <h3>Presença já confirmada!</h3>
          <p>Obrigado! Sua resposta já foi registrada neste dispositivo.</p>
        </div>

        <!-- Confirmed RSVPs List (Admin / Local Preview) Toggle -->
        <div class="rsvp-list-wrapper">
          <button id="toggleRsvpListBtn" class="btn btn-link">
            <i class="fa-solid fa-list-check"></i> Ver respostas de presença (<span id="rsvpCount">0</span>)
          </button>
          <div id="rsvpListContainer" class="rsvp-list-container hidden">
            <h4>Respostas de Presença</h4>
            <div id="rsvpItems" class="rsvp-items">
              <p class="empty-rsvp">Nenhuma resposta gravada neste dispositivo ainda.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Success Confirmation Modal -->
  <div id="successModal" class="modal-backdrop">
    <div class="modal-card">
      <div class="modal-icon-success">
        <i class="fa-solid fa-circle-check"></i>
      </div>
      <h3 id="modalSuccessTitle">Resposta Confirmada!</h3>
      <p id="modalSuccessMessage">Obrigado por confirmar sua presença! Mal podemos esperar para celebrar este momento especial com você.</p>
      
      <div class="modal-summary" id="modalSummary"></div>

      <div class="modal-actions">
        <button id="sendWhatsappBtn" class="btn btn-primary">
          <i class="fa-brands fa-whatsapp"></i> Enviar pelo WhatsApp
        </button>

        <button id="closeModalBtn" class="btn btn-secondary">
          Concluir
        </button>
      </div>
    </div>
  </div>

  <!-- Modal Lista de Mimos -->
  <div id="mimoListModal" class="modal-backdrop">
    <div class="modal-card mimo-modal-card">
      <button class="modal-close" id="closeMimoModalBtn" aria-label="Fechar">&times;</button>
      <div class="modal-icon-success mimo-modal-icon">
        <i class="fa-solid fa-icons"></i>
      </div>
      <h3>Escolha seu Mimo</h3>
      <div class="mimo-modal-list" id="mimoModalList"></div>
    </div>
  </div>

  <!-- Traje Section -->
  <section id="traje" class="section traje-section">
    <div class="container">
      <div class="section-header" data-reveal="fade-up">
        <span class="section-tag"><i class="fa-solid fa-shirt"></i> Dress Code</span>
        <h2 class="section-title">Traje do Evento</h2>
        <p class="section-subtitle">Vista-se com muito carinho nos tons neutros! Pedimos que os convidados escolham peças nas cores abaixo, sem branco.</p>
        <div class="title-divider"><span></span><i class="fa-solid fa-palette"></i><span></span></div>
      </div>

      <div class="traje-card" data-reveal="fade-up">
        <div class="traje-head">
          <i class="fa-solid fa-shirt traje-head-icon"></i>
          <span><?php echo htmlspecialchars($traje); ?></span>
        </div>

        <div class="traje-paleta">
          <?php foreach ($paletaTraje as $tom): ?>
            <div class="traje-swatch">
              <span class="swatch-circle" style="background: <?php echo $tom[1]; ?>;"></span>
              <span class="swatch-name"><?php echo htmlspecialchars($tom[0]); ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- Local & Endereço Section -->
  <section id="local" class="section local-section alt-bg">
    <div class="container">
      <div class="section-header" data-reveal="fade-up">
        <span class="section-tag"><i class="fa-solid fa-map-pin"></i> Como Chegar</span>
        <h2 class="section-title">Local do Evento</h2>
        <div class="title-divider"><span></span><i class="fa-solid fa-location-arrow"></i><span></span></div>
      </div>

      <div class="local-grid">
        <div class="local-info-card" data-reveal="fade-right">
          <div class="venue-badge">
            <i class="fa-solid fa-champagne-glasses"></i> Endereço do Evento
          </div>
          
          <ul class="venue-details-list">
            <li>
              <i class="fa-solid fa-location-dot list-icon"></i>
              <div>
                <strong>Endereço:</strong>
                <p><?php echo htmlspecialchars($endereco); ?></p>
              </div>
            </li>
            <li>
              <i class="fa-solid fa-location-crosshairs list-icon"></i>
              <div>
                <strong>Referência:</strong>
                <p>Próximo ao trevo do bairro Darci Ribeiro</p>
              </div>
            </li>
            <li>
              <i class="fa-solid fa-clock list-icon"></i>
              <div>
                <strong>Horário de Início:</strong>
                <p><?php echo htmlspecialchars($dataCompleta); ?> às <?php echo htmlspecialchars($horario); ?>h</p>
              </div>
            </li>

          <div class="local-actions">
            <a href="https://maps.google.com/?q=<?php echo $enderecoUrl; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
              <i class="fa-solid fa-diamond-turn-right"></i> Abrir no Google Maps
            </a>
            <a href="https://waze.com/ul?q=<?php echo $enderecoUrl; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">
              <i class="fa-brands fa-waze"></i> Abrir no Waze
            </a>
            <button id="addToCalendarBtn" class="btn btn-outline">
              <i class="fa-regular fa-calendar-plus"></i> Add ao Calendário
            </button>
          </div>
        </div>

        <div class="local-map-card" data-reveal="fade-left">
          <div class="map-wrapper">
            <iframe 
              src="https://maps.google.com/maps?q=<?php echo $enderecoUrl; ?>&output=embed" 
              width="100%" 
              height="380" 
              style="border:0;" 
              allowfullscreen="" 
              loading="lazy" 
              referrerpolicy="no-referrer-when-downgrade"
              title="Mapa do Local do Evento">
            </iframe>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container footer-container">
      <div class="footer-brand">
        <div class="footer-logo">
          <i class="fa-solid fa-baby-carriage"></i> <?php echo htmlspecialchars($nomeEvento); ?>
        </div>
        <p>Helena ou Henrique • <?php echo htmlspecialchars($dataFormatada); ?></p>
      </div>

      <div class="footer-links">
        <a href="#hero">Início</a>
        <a href="#fraldas">Fraldas</a>
        <a href="#confirmacao">RSVP</a>
        <a href="#traje">Traje</a>
        <a href="#local">Local</a>
      </div>

      <div class="footer-copy">
        <p>Feito com todo carinho e amor para o nosso bebê ❤️</p>
        <small>© <?php echo date('Y'); ?> <?php echo htmlspecialchars($nomeEvento); ?>. Todos os direitos reservados.</small>
      </div>
    </div>
  </footer>

  <!-- Share Modal -->
  <div id="shareModal" class="modal-backdrop">
    <div class="modal-card share-modal-card">
      <button class="modal-close" id="closeShareModalBtn" aria-label="Fechar">&times;</button>
      <div class="share-header">
        <div class="modal-icon-share">
          <i class="fa-solid fa-share-nodes"></i>
        </div>
        <h3>Compartilhar Convite</h3>
        <p>Envie o link do Chá de Bebê & Revelação para a família e amigos!</p>
      </div>

      <!-- Copiar Link Box -->
      <div class="share-copy-box">
        <label for="shareUrlInput"><i class="fa-solid fa-link"></i> Link do Convite:</label>
        <div class="copy-input-group">
          <input type="text" id="shareUrlInput" readonly value="">
          <button id="copyShareUrlBtn" class="btn btn-primary">
            <i class="fa-regular fa-copy"></i> Copiar Link
          </button>
        </div>
        <div id="shareCopyFeedback" class="copy-feedback-toast">
          <i class="fa-solid fa-circle-check"></i> Link copiado para a área de transferência!
        </div>
      </div>

      <!-- Redes Sociais Grid -->
      <div class="share-social-title"><i class="fa-solid fa-paper-plane"></i> Compartilhar via:</div>
      <div class="share-social-grid">
        <a href="#" id="shareWhatsapp" target="_blank" rel="noopener" class="social-share-btn share-whatsapp">
          <i class="fa-brands fa-whatsapp"></i> WhatsApp
        </a>
        <a href="#" id="shareTelegram" target="_blank" rel="noopener" class="social-share-btn share-telegram">
          <i class="fa-brands fa-telegram"></i> Telegram
        </a>
        <a href="#" id="shareFacebook" target="_blank" rel="noopener" class="social-share-btn share-facebook">
          <i class="fa-brands fa-facebook"></i> Facebook
        </a>
        <a href="#" id="shareEmail" target="_blank" rel="noopener" class="social-share-btn share-email">
          <i class="fa-solid fa-envelope"></i> E-mail
        </a>
      </div>

      <!-- QR Code Container -->
      <div class="share-qr-container">
        <div class="qr-info">
          <h4><i class="fa-solid fa-qrcode"></i> QR Code do Convite</h4>
          <p>Abra a câmera do celular para abrir o convite instantaneamente</p>
        </div>
        <div class="qr-code-frame">
          <img id="shareQrCodeImg" src="" alt="QR Code do Convite">
        </div>
      </div>

      <!-- Native Share Button (se disponível) -->
      <div id="nativeShareWrapper" class="native-share-wrapper">
        <button id="shareNativeBtn" class="btn btn-outline btn-block">
          <i class="fa-solid fa-mobile-screen-button"></i> Abrir Opções do Celular
        </button>
      </div>
    </div>
  </div>

  <!-- Floating Share & Top Buttons Container -->
  <div class="floating-actions">
    <button id="floatingShareBtn" class="floating-btn floating-share-btn" aria-label="Compartilhar Convite" title="Compartilhar Convite">
      <i class="fa-solid fa-share-nodes"></i>
    </button>
    <button id="backToTopBtn" class="floating-btn back-to-top-btn" aria-label="Voltar ao topo" title="Voltar ao topo">
      <i class="fa-solid fa-arrow-up"></i>
    </button>
  </div>

  <!-- Configuração dinâmica para o JavaScript -->
  <script>
    window.SITE_CONFIG = {
      dataEvento: <?php echo json_encode($dataEvento . 'T' . $horario . ':00'); ?>,
      nomeEvento: <?php echo json_encode($nomeEvento); ?>,
      endereco: <?php echo json_encode($endereco); ?>
    };
  </script>

  <!-- JavaScript File -->
  <script src="script.js"></script>
</body>
</html>
