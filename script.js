/**
 * Chá de Bebê & Revelação - Script Principal
 * Recursos: Contagem regressiva, Troca de Tema, Scroll Suave, Validações, Confete, Lightbox e localStorage
 */

document.addEventListener('DOMContentLoaded', () => {

  /* ==========================================================================
     0. RESET TEMPORÁRIO - LIMPAR CONFIRMAÇÕES DE PRESENÇA (remover depois)
     ========================================================================== */
  if (!sessionStorage.getItem('chadebebe_clear_rsvps_done')) {
    sessionStorage.setItem('chadebebe_clear_rsvps_done', '1');
    ['chadebebe_rsvps', 'chadebebe_confirmado_dev', 'chadebebe_fraldas_atribuidas', 'chadebebe_fraldas_votes', 'chadebebe_fralda_votou'].forEach(key => {
      localStorage.removeItem(key);
    });
  }

  /* ==========================================================================
     1. CONTROLE DE TEMA (CLARO / ESCURO)
     ========================================================================== */
  const themeToggleBtn = document.getElementById('themeToggle');
  const htmlElement = document.documentElement;

  // Carregar tema salvo ou preferência do sistema
  const savedTheme = localStorage.getItem('chadebebe_theme');
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

  if (savedTheme) {
    htmlElement.setAttribute('data-theme', savedTheme);
  } else if (prefersDark) {
    htmlElement.setAttribute('data-theme', 'dark');
  }

  themeToggleBtn?.addEventListener('click', () => {
    const currentTheme = htmlElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    htmlElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('chadebebe_theme', newTheme);
  });

  /* ==========================================================================
     2. CONTAGEM REGRESSIVA (COUNTDOWN TIMER)
     ========================================================================== */
  // Data do evento (vem da configuração do site no banco)
  const eventDate = new Date(window.SITE_CONFIG?.dataEvento || '2026-09-27T12:00:00').getTime();
  const daysEl = document.getElementById('days');
  const hoursEl = document.getElementById('hours');
  const minutesEl = document.getElementById('minutes');
  const secondsEl = document.getElementById('seconds');

  function updateCountdown() {
    const now = new Date().getTime();
    const distance = eventDate - now;

    if (distance < 0) {
      if (daysEl) daysEl.innerText = "00";
      if (hoursEl) hoursEl.innerText = "00";
      if (minutesEl) minutesEl.innerText = "00";
      if (secondsEl) secondsEl.innerText = "00";
      return;
    }

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    if (daysEl) daysEl.innerText = days < 10 ? `0${days}` : days;
    if (hoursEl) hoursEl.innerText = hours < 10 ? `0${hours}` : hours;
    if (minutesEl) minutesEl.innerText = minutes < 10 ? `0${minutes}` : minutes;
    if (secondsEl) secondsEl.innerText = seconds < 10 ? `0${seconds}` : seconds;
  }

  updateCountdown();
  setInterval(updateCountdown, 1000);

  /* ==========================================================================
     3. NAVEGAÇÃO & MENU HAMBÚRGUER MOBILE
     ========================================================================== */
  const navbar = document.getElementById('navbar');
  const menuToggleBtn = document.getElementById('menuToggle');
  const navMenu = document.getElementById('navMenu');
  const navLinks = document.querySelectorAll('.nav-link');

  // Efeito de sombra na navbar ao rolar
  window.addEventListener('scroll', () => {
    if (window.scrollY > 40) {
      navbar?.classList.add('scrolled');
    } else {
      navbar?.classList.remove('scrolled');
    }
  });

  // Toggle Menu Mobile
  menuToggleBtn?.addEventListener('click', () => {
    menuToggleBtn.classList.toggle('active');
    navMenu?.classList.toggle('active');
  });

  // Fechar menu ao clicar em um link
  navLinks.forEach(link => {
    link.addEventListener('click', () => {
      menuToggleBtn?.classList.remove('active');
      navMenu?.classList.remove('active');
    });
  });

  // Destacar Link Ativo no Scroll
  const sections = document.querySelectorAll('section[id]');
  const observerOptions = {
    root: null,
    rootMargin: '-20% 0px -70% 0px',
    threshold: 0
  };

  const navObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const id = entry.target.getAttribute('id');
        navLinks.forEach(link => {
          if (link.getAttribute('href') === `#${id}`) {
            link.classList.add('active');
          } else {
            link.classList.remove('active');
          }
        });
      }
    });
  }, observerOptions);

  sections.forEach(section => navObserver.observe(section));

  /* ==========================================================================
     4. ANIMAÇÕES DE REVELAÇÃO NO SCROLL (SCROLL REVEAL)
     ========================================================================== */
  const revealElements = document.querySelectorAll('[data-reveal]');

  const revealObserverOptions = {
    root: null,
    threshold: 0.12,
    rootMargin: '0px 0px -50px 0px'
  };

  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const delay = entry.target.getAttribute('data-delay') || 0;
        setTimeout(() => {
          entry.target.classList.add('revealed');
        }, parseInt(delay));
        revealObserver.unobserve(entry.target);
      }
    });
  }, revealObserverOptions);

  revealElements.forEach(el => revealObserver.observe(el));

  /* ==========================================================================
     5. BOTÃO VOLTAR AO TOPO
     ========================================================================== */
  const backToTopBtn = document.getElementById('backToTopBtn');

  window.addEventListener('scroll', () => {
    if (window.scrollY > 350) {
      backToTopBtn?.classList.add('visible');
    } else {
      backToTopBtn?.classList.remove('visible');
    }
  });

  backToTopBtn?.addEventListener('click', () => {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });

  /* ==========================================================================
     6. VOTAÇÃO INTERATIVA DE GÊNERO (MENINO OU MENINA)
     ========================================================================== */
  const voteBoyBtn = document.getElementById('voteBoyBtn');
  const voteGirlBtn = document.getElementById('voteGirlBtn');
  const boyVotesCountEl = document.getElementById('boyVotesCount');
  const girlVotesCountEl = document.getElementById('girlVotesCount');
  const pollProgressBoy = document.getElementById('pollProgressBoy');
  const pollProgressGirl = document.getElementById('pollProgressGirl');
  const pollStatusText = document.getElementById('pollStatusText');

  let votes = JSON.parse(localStorage.getItem('chadebebe_poll_votes')) || { boy: 14, girl: 18 };
  let userHasVoted = localStorage.getItem('chadebebe_user_voted');

  function renderPoll() {
    const total = votes.boy + votes.girl;
    const boyPercent = Math.round((votes.boy / total) * 100);
    const girlPercent = Math.round((votes.girl / total) * 100);

    if (boyVotesCountEl) boyVotesCountEl.innerText = votes.boy;
    if (girlVotesCountEl) girlVotesCountEl.innerText = votes.girl;

    if (pollProgressBoy) pollProgressBoy.style.width = `${boyPercent}%`;
    if (pollProgressGirl) pollProgressGirl.style.width = `${girlPercent}%`;

    if (userHasVoted) {
      if (pollStatusText) {
        pollStatusText.innerText = `Você votou no ${userHasVoted}! Resultado: ${boyPercent}% Menino vs ${girlPercent}% Menina`;
      }
    }
  }

  function registerVote(gender) {
    if (userHasVoted) {
      alert(`Você já registrou seu palpite no ${userHasVoted}! Obrigado por participar.`);
      return;
    }

    if (gender === 'Menino') {
      votes.boy++;
    } else {
      votes.girl++;
    }

    userHasVoted = gender;
    localStorage.setItem('chadebebe_poll_votes', JSON.stringify(votes));
    localStorage.setItem('chadebebe_user_voted', gender);

    // Disparar confete colorido do gênero!
    triggerGenderConfetti(gender);
    renderPoll();
    updatePalpiteStatus();
  }

  voteBoyBtn?.addEventListener('click', () => registerVote('Menino'));
  voteGirlBtn?.addEventListener('click', () => registerVote('Menina'));
  renderPoll();

  // Refletir o palpite da votação no formulário de confirmação
  const palpiteStatusEl = document.getElementById('palpiteStatus');

  function updatePalpiteStatus() {
    if (!palpiteStatusEl) return;
    const votou = localStorage.getItem('chadebebe_user_voted');
    palpiteStatusEl.innerHTML = votou
      ? `Registrado: <strong>${votou === 'Menino' ? 'Menino (Henrique)' : 'Menina (Helena)'}</strong>`
      : 'Não registrado ainda. Vote na seção "Qual o seu palpite?" no topo da página.';
  }
  updatePalpiteStatus();

  function triggerGenderConfetti(gender) {
    if (typeof confetti === 'function') {
      const colors = gender === 'Menino' ? ['#7ebc89', '#9fd5aa', '#c6ebd0'] : ['#e898ac', '#f4b8c6', '#fdebf0'];
      confetti({
        particleCount: 80,
        spread: 70,
        origin: { y: 0.6 },
        colors: colors
      });
    }
  }

  /* ==========================================================================
     7. SORTEIO DO TAMANHO DE FRALDA (contagem por tamanho)
     ========================================================================== */
  const FRALDA_DRAWN_KEY = 'chadebebe_fraldas_atribuidas';
  const FRALDA_SIZES = ['Fralda Tamanho P', 'Fralda Tamanho M', 'Fralda Tamanho G'];
  let fraldaAssignments = JSON.parse(localStorage.getItem(FRALDA_DRAWN_KEY)) || [];
  const fraldaAssignmentsFiltradas = fraldaAssignments.filter(v => FRALDA_SIZES.includes(v.tamanho));
  if (fraldaAssignmentsFiltradas.length !== fraldaAssignments.length) {
    fraldaAssignments = fraldaAssignmentsFiltradas;
    localStorage.setItem(FRALDA_DRAWN_KEY, JSON.stringify(fraldaAssignments));
  }

  function updateFraldaCounts() {
    document.querySelectorAll('.fralda-count').forEach(el => {
      const target = el.dataset.countFor;
      const count = fraldaAssignments.filter(v => v.tamanho === target).length;
      const valueEl = el.querySelector('.fralda-count-value');
      const labelEl = el.querySelector('.fralda-count-label');
      if (valueEl) valueEl.textContent = count;
      if (labelEl) labelEl.textContent = count === 1 ? 'pessoa recebeu este tamanho' : 'pessoas receberam este tamanho';
    });
  }

  // Sorteia o tamanho com menor quantidade já atribuída (empate decide aleatoriamente)
  function drawFraldaSize() {
    const counts = {};
    FRALDA_SIZES.forEach(s => counts[s] = 0);
    fraldaAssignments.forEach(v => { counts[v.tamanho] = (counts[v.tamanho] || 0) + 1; });
    const minCount = Math.min(...FRALDA_SIZES.map(s => counts[s]));
    const candidates = FRALDA_SIZES.filter(s => counts[s] === minCount);
    return candidates[Math.floor(Math.random() * candidates.length)];
  }

  updateFraldaCounts();

  /* ==========================================================================
     7.1 BOX "FRALDA + MIMO" (abre a lista de mimos em um modal)
     ========================================================================== */
  const openMimoListBtn = document.getElementById('openMimoListBtn');
  const mimoModal = document.getElementById('mimoListModal');
  const closeMimoModalBtn = document.getElementById('closeMimoModalBtn');
  const mimoModalList = document.getElementById('mimoModalList');
  const comboCard = document.querySelector('.mimo-combo-card');

  const MIMOS = [
    { name: 'Kit Higiene', icon: 'fa-pump-soap' },
    { name: 'Kit Manicure', icon: 'fa-hand-scissors' },
    { name: 'Chupeta', icon: 'fa-pacifier' },
    { name: 'Mamadeira', icon: 'fa-baby-bottle' },
    { name: 'Mordedor', icon: 'fa-tooth' },
    { name: 'Manta', icon: 'fa-blanket' },
    { name: 'Toalha de boca ou com capuz', icon: 'fa-bath' },
    { name: 'Babador', icon: 'fa-shirt' },
    { name: 'Roupinhas', icon: 'fa-tshirt' }
  ];

  if (openMimoListBtn && mimoModal && mimoModalList) {
    MIMOS.forEach(mimo => {
      const option = document.createElement('button');
      option.type = 'button';
      option.className = 'mimo-option';
      option.dataset.mimo = mimo.name;
      option.innerHTML = `<i class="fa-solid ${mimo.icon}"></i><span>${mimo.name}</span>`;
      option.addEventListener('click', () => {
        comboCard?.classList.add('selected');
        mimoModal.classList.remove('active');
      });
      mimoModalList.appendChild(option);
    });

    openMimoListBtn.addEventListener('click', () => {
      mimoModal.classList.add('active');
    });

    closeMimoModalBtn?.addEventListener('click', () => {
      mimoModal.classList.remove('active');
    });

    mimoModal.addEventListener('click', (e) => {
      if (e.target === mimoModal) {
        mimoModal.classList.remove('active');
      }
    });
  }

  /* ==========================================================================
     8. FORMULÁRIO DE CONFIRMAÇÃO (RSVP) & MODAL DE SUCESSO
     ========================================================================== */
  const rsvpForm = document.getElementById('rsvpForm');
  const successModal = document.getElementById('successModal');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const sendWhatsappBtn = document.getElementById('sendWhatsappBtn');
  const modalSummary = document.getElementById('modalSummary');

  const guestsGroup = document.getElementById('guestsGroup');
  const guestsCountInput = document.getElementById('guestsCount');
  const guestNameInput = document.getElementById('guestName');
  const alreadyConfirmed = document.getElementById('alreadyConfirmed');
  const DEVICE_CONFIRM_KEY = 'chadebebe_confirmado_dev';

  function lockRsvpForm() {
    if (!rsvpForm || !alreadyConfirmed) return;
    rsvpForm.classList.add('hidden');
    alreadyConfirmed.classList.remove('hidden');
  }

  // Cada aparelho só pode confirmar uma vez
  if (localStorage.getItem(DEVICE_CONFIRM_KEY) === 'true') {
    lockRsvpForm();
  }

  // Adicionar ao Calendário
  const addToCalendarBtn = document.getElementById('addToCalendarBtn');
  addToCalendarBtn?.addEventListener('click', () => {
    const nomeEvento = window.SITE_CONFIG?.nomeEvento || 'Chá de Bebê & Revelação';
    const endereco = window.SITE_CONFIG?.endereco || 'Rua Retiro das Aves, 90A - Capim Rasteiro, Contagem - MG';
    const title = encodeURIComponent(`${nomeEvento} (Helena ou Henrique)`);
    const details = encodeURIComponent(`Venha celebrar conosco o ${nomeEvento}!`);
    const location = encodeURIComponent(endereco);
    const dates = "20260927T150000Z/20260927T190000Z";

    const gcalUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${title}&details=${details}&location=${location}&dates=${dates}`;
    window.open(gcalUrl, '_blank');
  });

  // Salvar presenças no localStorage
  let savedRSVPs = JSON.parse(localStorage.getItem('chadebebe_rsvps')) || [];
  updateRSVPCount();

  function updateRSVPCount() {
    const rsvpCountEl = document.getElementById('rsvpCount');
    if (rsvpCountEl) rsvpCountEl.innerText = savedRSVPs.length;
  }

  // Toggle visualização de presenças
  const toggleRsvpListBtn = document.getElementById('toggleRsvpListBtn');
  const rsvpListContainer = document.getElementById('rsvpListContainer');
  const rsvpItemsEl = document.getElementById('rsvpItems');

  toggleRsvpListBtn?.addEventListener('click', () => {
    if (rsvpListContainer) {
      rsvpListContainer.classList.toggle('hidden');
      renderRsvpItems();
    }
  });

  function renderRsvpItems() {
    if (!rsvpItemsEl) return;

    if (savedRSVPs.length === 0) {
      rsvpItemsEl.innerHTML = '<p class="empty-rsvp">Nenhuma confirmação gravada neste dispositivo ainda.</p>';
      return;
    }

    rsvpItemsEl.innerHTML = savedRSVPs.map((item, idx) => `
      <div class="rsvp-item-card">
        <div>
          <strong>${idx + 1}º</strong> ${item.nome ? item.nome : item.resposta}
          <br><small style="color: var(--text-secondary);">${item.resposta}</small>
          ${item.pessoas ? `<br><small style="color: var(--text-secondary);">Acompanhantes: ${item.pessoas}</small>` : ''}
          ${item.tamanhoSorteado ? `<br><small style="color: var(--text-secondary);">Sua fralda: ${item.tamanhoSorteado}</small>` : ''}
          ${item.palpite ? `<br><small style="color: var(--text-secondary);">Palpite: ${item.palpite}</small>` : ''}
          <br><small style="color: var(--text-secondary);">${item.data}</small>
        </div>
        <span class="vote-badge">${item.resposta.startsWith('Sim') ? 'Vai' : 'Não vai'}</span>
      </div>
    `).join('');
  }

  let lastRsvpData = null;

  rsvpForm?.addEventListener('submit', (e) => {
    e.preventDefault();

    const willAttend = document.querySelector('input[name="willAttend"]:checked')?.value;
    const nome = guestNameInput?.value.trim() || '';
    const palpite = localStorage.getItem('chadebebe_user_voted') || '';
    const palpiteLabel = palpite ? (palpite === 'Menino' ? 'Menino (Henrique)' : 'Menina (Helena)') : '';

    if (!willAttend) {
      alert('Por favor, selecione se você vai comparecer ou não ao evento.');
      return;
    }

    if (!nome) {
      alert('Por favor, informe seu nome.');
      guestNameInput?.focus();
      return;
    }

    const isComing = willAttend.startsWith('Sim');
    const pessoas = isComing ? parseInt(guestsCountInput?.value || '0', 10) : 0;

    if (isComing && (isNaN(pessoas) || pessoas < 0)) {
      alert('Informe a quantidade de acompanhantes que você vai levar.');
      return;
    }

    if (isComing && !palpite) {
      alert('Registre seu palpite (Menino ou Menina) na seção "Qual o seu palpite?" no topo da página antes de confirmar.');
      document.getElementById('hero')?.scrollIntoView({ behavior: 'smooth' });
      return;
    }

    // Sorteio do tamanho de fralda (somente para quem vai)
    let tamanhoSorteado = null;
    if (isComing) {
      tamanhoSorteado = drawFraldaSize();
      fraldaAssignments.push({
        tamanho: tamanhoSorteado,
        data: new Date().toLocaleDateString('pt-BR')
      });
      localStorage.setItem(FRALDA_DRAWN_KEY, JSON.stringify(fraldaAssignments));
      updateFraldaCounts();
    }

    lastRsvpData = { nome, willAttend, pessoas, tamanhoSorteado, palpite: palpiteLabel };

    // Salvar localmente
    savedRSVPs.push({
      nome,
      resposta: willAttend,
      pessoas,
      tamanhoSorteado,
      palpite: palpiteLabel,
      data: new Date().toLocaleDateString('pt-BR')
    });
    localStorage.setItem('chadebebe_rsvps', JSON.stringify(savedRSVPs));
    localStorage.setItem(DEVICE_CONFIRM_KEY, 'true');
    updateRSVPCount();

    // Enviar os dados por e-mail (back-end PHP)
    fetch('enviar_confirmacao.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nome, resposta: willAttend, pessoas, tamanhoSorteado, palpite: palpiteLabel })
    }).catch(() => {});

    // Preencher resumo no Modal
    if (modalSummary) {
      modalSummary.innerHTML = `
        <p><strong>Nome:</strong> ${nome}</p>
        <p><strong>Resposta:</strong> ${willAttend}</p>
        ${palpiteLabel ? `<p><strong>Palpite:</strong> ${palpiteLabel}</p>` : ''}
        ${isComing ? `<p><strong>Acompanhantes:</strong> ${pessoas}</p>` : ''}
        ${isComing ? `<p><strong>Sua fralda:</strong> ${tamanhoSorteado}</p>` : ''}
      `;
    }

    // Personalizar título e mensagem conforme a resposta
    const modalSuccessTitle = document.getElementById('modalSuccessTitle');
    const modalSuccessMessage = document.getElementById('modalSuccessMessage');

    if (modalSuccessTitle) {
      modalSuccessTitle.innerText = isComing ? 'Presença Confirmada!' : 'Resposta Registrada';
    }
    if (modalSuccessMessage) {
      modalSuccessMessage.innerText = isComing
        ? 'Que alegria! Mal podemos esperar para celebrar este momento especial com você.'
        : 'Que pena que você não poderá ir! Sentiremos sua falta e ficaremos felizes com a sua torcida.';
    }

    // Exibir Modal e soltar confetes
    successModal?.classList.add('active');

    if (typeof confetti === 'function') {
      confetti({
        particleCount: 120,
        spread: 90,
        origin: { y: 0.6 }
      });
    }

    rsvpForm.reset();
    lockRsvpForm();
  });

  closeModalBtn?.addEventListener('click', () => {
    successModal?.classList.remove('active');
  });

  sendWhatsappBtn?.addEventListener('click', () => {
    if (!lastRsvpData) return;

    const text = `*Confirmação de Presença - Chá de Bebê & Revelação* 🎉\n\n` +
      `*Nome:* ${lastRsvpData.nome}\n` +
      `*Resposta:* ${lastRsvpData.willAttend}\n` +
      (lastRsvpData.palpite ? `*Palpite:* ${lastRsvpData.palpite}\n` : '') +
      (lastRsvpData.pessoas ? `*Acompanhantes:* ${lastRsvpData.pessoas}\n` : '') +
      (lastRsvpData.tamanhoSorteado ? `*Sua fralda:* ${lastRsvpData.tamanhoSorteado}\n` : '') +
      `\nNos vemos em breve! ❤️`;

    const encodedText = encodeURIComponent(text);
    const whatsappUrl = `https://api.whatsapp.com/send?text=${encodedText}`;
    window.open(whatsappUrl, '_blank');
  });

  /* ==========================================================================
     10. FUNCIONALIDADE DE COMPARTILHAMENTO DO LINK
     ========================================================================== */
  const shareModal = document.getElementById('shareModal');
  const closeShareModalBtn = document.getElementById('closeShareModalBtn');
  const navShareBtn = document.getElementById('navShareBtn');
  const heroShareBtn = document.getElementById('heroShareBtn');
  const floatingShareBtn = document.getElementById('floatingShareBtn');
  const shareUrlInput = document.getElementById('shareUrlInput');
  const copyShareUrlBtn = document.getElementById('copyShareUrlBtn');
  const shareCopyFeedback = document.getElementById('shareCopyFeedback');
  const shareWhatsapp = document.getElementById('shareWhatsapp');
  const shareTelegram = document.getElementById('shareTelegram');
  const shareFacebook = document.getElementById('shareFacebook');
  const shareEmail = document.getElementById('shareEmail');
  const shareQrCodeImg = document.getElementById('shareQrCodeImg');
  const shareNativeBtn = document.getElementById('shareNativeBtn');
  const nativeShareWrapper = document.getElementById('nativeShareWrapper');

  // Obter a URL atual
  const shareTitle = `${window.SITE_CONFIG?.nomeEvento || 'Chá de Bebê & Revelação'} - Helena ou Henrique 👶💕`;
  const shareText = "🎉 Você foi convidado(a) para o nosso Chá de Bebê & Revelação! Confirme sua presença e veja todos os detalhes no link:";
  
  function getShareUrl() {
    return window.location.href;
  }

  function setupShareLinks() {
    const url = getShareUrl();
    
    // Atualizar Input
    if (shareUrlInput) {
      shareUrlInput.value = url;
    }

    // WhatsApp
    if (shareWhatsapp) {
      const waText = encodeURIComponent(`${shareText}\n${url}`);
      shareWhatsapp.href = `https://api.whatsapp.com/send?text=${waText}`;
    }

    // Telegram
    if (shareTelegram) {
      const tgText = encodeURIComponent(shareText);
      shareTelegram.href = `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${tgText}`;
    }

    // Facebook
    if (shareFacebook) {
      shareFacebook.href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
    }

    // E-mail
    if (shareEmail) {
      const mailSubject = encodeURIComponent("Convite Especial: Chá de Bebê & Revelação");
      const mailBody = encodeURIComponent(`${shareText}\n\n${url}`);
      shareEmail.href = `mailto:?subject=${mailSubject}&body=${mailBody}`;
    }

    // QR Code Generator (QRServer API)
    if (shareQrCodeImg) {
      const qrData = encodeURIComponent(url);
      shareQrCodeImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${qrData}&color=2d3748&bgcolor=ffffff`;
    }

    // Mostrar botão de compartilhamento nativo se o navegador suportar
    if (navigator.share) {
      nativeShareWrapper?.classList.remove('hidden');
    } else {
      nativeShareWrapper?.classList.add('hidden');
    }
  }

  function openShareModal() {
    setupShareLinks();
    shareModal?.classList.add('active');
  }

  function closeShareModal() {
    shareModal?.classList.remove('active');
  }

  // Event Listeners para Abrir o Modal
  navShareBtn?.addEventListener('click', openShareModal);
  heroShareBtn?.addEventListener('click', openShareModal);
  floatingShareBtn?.addEventListener('click', openShareModal);

  closeShareModalBtn?.addEventListener('click', closeShareModal);

  shareModal?.addEventListener('click', (e) => {
    if (e.target === shareModal) {
      closeShareModal();
    }
  });

  // Copiar Link
  copyShareUrlBtn?.addEventListener('click', () => {
    const url = getShareUrl();
    
    // Tentar copiar via Clipboard API
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(url).then(showCopySuccess).catch(() => fallbackCopyText(url));
    } else {
      fallbackCopyText(url);
    }
  });

  function fallbackCopyText(text) {
    if (shareUrlInput) {
      shareUrlInput.select();
      shareUrlInput.setSelectionRange(0, 99999);
      try {
        document.execCommand('copy');
        showCopySuccess();
      } catch (err) {
        alert('Não foi possível copiar automaticamente. Selecione e copie o link manualmente.');
      }
    }
  }

  function showCopySuccess() {
    if (shareCopyFeedback) {
      shareCopyFeedback.classList.add('show');
      setTimeout(() => {
        shareCopyFeedback.classList.remove('show');
      }, 3000);
    }
  }

  // Compartilhamento Nativo (Web Share API)
  shareNativeBtn?.addEventListener('click', async () => {
    const url = getShareUrl();
    if (navigator.share) {
      try {
        await navigator.share({
          title: shareTitle,
          text: shareText,
          url: url
        });
      } catch (err) {
        console.log('Compartilhamento cancelado pelo usuário.');
      }
    }
  });

});

