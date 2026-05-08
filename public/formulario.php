<?php
// Página pública - NO requiere login
// Accesible en: tudominio.com/public/formulario.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Analiza tu factura gratis · Optitech Europa</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
  <style>
    :root {
      --green: #00e676;
      --green-dark: #00c853;
      --green-glow: rgba(0, 230, 118, 0.15);
      --bg: #060d0a;
      --bg-card: #0d1a13;
      --bg-input: #111f16;
      --border: rgba(0, 230, 118, 0.15);
      --border-hover: rgba(0, 230, 118, 0.4);
      --text: #e8f5e9;
      --text-muted: #6b8f74;
      --error: #ff5252;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      overflow-x: hidden;
    }

    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background:
        radial-gradient(ellipse 80% 50% at 20% 20%, rgba(0, 230, 118, 0.06) 0%, transparent 60%),
        radial-gradient(ellipse 60% 40% at 80% 80%, rgba(0, 200, 83, 0.04) 0%, transparent 50%);
      pointer-events: none;
      z-index: 0;
    }

    body::after {
      content: '';
      position: fixed;
      inset: 0;
      background-image:
        linear-gradient(rgba(0, 230, 118, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 230, 118, 0.03) 1px, transparent 1px);
      background-size: 60px 60px;
      pointer-events: none;
      z-index: 0;
    }

    .wrapper {
      position: relative;
      z-index: 1;
      min-height: 100vh;
      display: grid;
      grid-template-columns: 1fr 1fr;
      max-width: 1200px;
      margin: 0 auto;
      padding: 60px 40px;
      gap: 80px;
      align-items: center;
    }

    .left {
      display: flex;
      flex-direction: column;
      gap: 32px;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 10px;
      font-family: 'Syne', sans-serif;
      font-weight: 800;
      font-size: 18px;
      letter-spacing: -0.5px;
    }

    .logo-icon {
      width: 36px;
      height: 36px;
      background: var(--green);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .logo-icon svg { width: 20px; height: 20px; }

    .badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: var(--green-glow);
      border: 1px solid var(--border);
      border-radius: 100px;
      padding: 6px 14px;
      font-size: 12px;
      font-weight: 500;
      color: var(--green);
      letter-spacing: 0.5px;
      width: fit-content;
    }

    .badge::before {
      content: '';
      width: 6px;
      height: 6px;
      background: var(--green);
      border-radius: 50%;
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.5; transform: scale(0.8); }
    }

    .headline {
      font-family: 'Syne', sans-serif;
      font-weight: 800;
      font-size: clamp(36px, 4vw, 52px);
      line-height: 1.05;
      letter-spacing: -2px;
    }

    .headline em {
      font-style: normal;
      color: var(--green);
    }

    .subtext {
      font-size: 16px;
      line-height: 1.7;
      color: var(--text-muted);
      max-width: 420px;
    }

    .stats {
      display: flex;
      gap: 32px;
    }

    .stat {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }

    .stat-number {
      font-family: 'Syne', sans-serif;
      font-weight: 700;
      font-size: 28px;
      color: var(--green);
      letter-spacing: -1px;
    }

    .stat-label {
      font-size: 12px;
      color: var(--text-muted);
      font-weight: 500;
    }

    .features {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .feature {
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 14px;
      color: var(--text-muted);
    }

    .feature-dot {
      width: 20px;
      height: 20px;
      background: var(--green-glow);
      border: 1px solid var(--border);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .feature-dot::after {
      content: '';
      width: 6px;
      height: 6px;
      background: var(--green);
      border-radius: 50%;
    }

    /* FORM */
    .form-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 40px;
      position: relative;
      overflow: hidden;
    }

    .form-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 2px;
      background: linear-gradient(90deg, transparent, var(--green), transparent);
    }

    .form-title {
      font-family: 'Syne', sans-serif;
      font-weight: 700;
      font-size: 22px;
      margin-bottom: 6px;
      letter-spacing: -0.5px;
    }

    .form-subtitle {
      font-size: 13px;
      color: var(--text-muted);
      margin-bottom: 28px;
    }

    .form-group {
      margin-bottom: 18px;
    }

    label {
      display: block;
      font-size: 12px;
      font-weight: 500;
      color: var(--text-muted);
      margin-bottom: 8px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    input[type="text"],
    input[type="tel"],
    input[type="email"] {
      width: 100%;
      background: var(--bg-input);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 12px 16px;
      font-family: 'DM Sans', sans-serif;
      font-size: 14px;
      color: var(--text);
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    input:focus {
      border-color: var(--border-hover);
      box-shadow: 0 0 0 3px var(--green-glow);
    }

    input::placeholder { color: var(--text-muted); opacity: 0.6; }

    .phone-row {
      display: flex;
      gap: 8px;
    }

    .select-pais {
      background: var(--bg-input);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 12px 10px;
      font-family: 'DM Sans', sans-serif;
      font-size: 13px;
      color: var(--text);
      outline: none;
      cursor: pointer;
      transition: border-color 0.2s, box-shadow 0.2s;
      appearance: none;
      -webkit-appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b8f74' stroke-width='2' stroke-linecap='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 8px center;
      padding-right: 26px;
      min-width: 90px;
    }

    .select-pais:focus {
      border-color: var(--border-hover);
      box-shadow: 0 0 0 3px var(--green-glow);
    }

    .select-pais option {
      background: #0d1a13;
      color: var(--text);
    }

    .select-full {
      width: 100%;
      background: var(--bg-input);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 12px 16px;
      font-family: 'DM Sans', sans-serif;
      font-size: 14px;
      color: var(--text);
      outline: none;
      cursor: pointer;
      transition: border-color 0.2s, box-shadow 0.2s;
      appearance: none;
      -webkit-appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%236b8f74' stroke-width='2' stroke-linecap='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 14px center;
      padding-right: 36px;
    }

    .select-full:focus {
      border-color: var(--border-hover);
      box-shadow: 0 0 0 3px var(--green-glow);
    }

    .select-full option {
      background: #0d1a13;
      color: var(--text);
    }

    .upload-area {
      border: 1.5px dashed var(--border);
      border-radius: 10px;
      padding: 28px 20px;
      text-align: center;
      cursor: pointer;
      transition: all 0.2s;
      background: var(--bg-input);
      position: relative;
    }

    .upload-area:hover,
    .upload-area.drag-over {
      border-color: var(--green);
      background: var(--green-glow);
    }

    .upload-area input[type="file"] {
      position: absolute;
      inset: 0;
      opacity: 0;
      cursor: pointer;
      width: 100%;
      height: 100%;
    }

    .upload-icon {
      width: 40px;
      height: 40px;
      background: var(--green-glow);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 12px;
    }

    .upload-text {
      font-size: 13px;
      color: var(--text-muted);
      line-height: 1.6;
    }

    .upload-text strong { color: var(--green); }

    .upload-preview {
      display: none;
      align-items: center;
      gap: 10px;
      background: var(--green-glow);
      border: 1px solid var(--border-hover);
      border-radius: 8px;
      padding: 10px 14px;
      margin-top: 10px;
      font-size: 13px;
    }

    .upload-preview.show { display: flex; }

    .consent {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      margin-bottom: 24px;
    }

    .consent input[type="checkbox"] {
      width: 16px;
      height: 16px;
      margin-top: 2px;
      accent-color: var(--green);
      cursor: pointer;
      flex-shrink: 0;
    }

    .consent label {
      font-size: 12px;
      color: var(--text-muted);
      text-transform: none;
      letter-spacing: 0;
      cursor: pointer;
      line-height: 1.6;
    }

    .consent a { color: var(--green); text-decoration: none; }

    .btn-submit {
      width: 100%;
      background: var(--green);
      color: #060d0a;
      border: none;
      border-radius: 10px;
      padding: 14px 24px;
      font-family: 'Syne', sans-serif;
      font-weight: 700;
      font-size: 15px;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      letter-spacing: -0.3px;
    }

    .btn-submit:hover {
      background: var(--green-dark);
      transform: translateY(-1px);
      box-shadow: 0 8px 24px rgba(0, 230, 118, 0.25);
    }

    .btn-submit:active { transform: translateY(0); }

    .btn-submit:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      transform: none;
    }

    .spinner {
      display: none;
      width: 16px;
      height: 16px;
      border: 2px solid rgba(0,0,0,0.3);
      border-top-color: #060d0a;
      border-radius: 50%;
      animation: spin 0.6s linear infinite;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    .success-state {
      display: none;
      text-align: center;
      padding: 20px 0;
    }

    .success-icon {
      width: 64px;
      height: 64px;
      background: var(--green-glow);
      border: 2px solid var(--green);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
    }

    .success-title {
      font-family: 'Syne', sans-serif;
      font-weight: 700;
      font-size: 22px;
      margin-bottom: 8px;
    }

    .success-text {
      font-size: 14px;
      color: var(--text-muted);
      line-height: 1.7;
    }

    .error-msg {
      color: var(--error);
      font-size: 11px;
      margin-top: 4px;
      display: none;
    }

    @media (max-width: 768px) {
      .wrapper {
        grid-template-columns: 1fr;
        padding: 40px 20px;
        gap: 40px;
      }
      .stats { gap: 20px; }
      .form-card { padding: 28px 20px; }
    }

    .left > * {
      opacity: 0;
      transform: translateY(20px);
      animation: fadeUp 0.6s ease forwards;
    }
    .left > *:nth-child(1) { animation-delay: 0.1s; }
    .left > *:nth-child(2) { animation-delay: 0.2s; }
    .left > *:nth-child(3) { animation-delay: 0.3s; }
    .left > *:nth-child(4) { animation-delay: 0.4s; }
    .left > *:nth-child(5) { animation-delay: 0.5s; }
    .left > *:nth-child(6) { animation-delay: 0.6s; }

    .form-card {
      opacity: 0;
      transform: translateY(20px);
      animation: fadeUp 0.6s ease 0.3s forwards;
    }

    @keyframes fadeUp {
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

<div class="wrapper">

  <!-- LEFT -->
  <div class="left">
    <div class="logo">
      <div class="logo-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="#060d0a" stroke-width="2.5" stroke-linecap="round">
          <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
        </svg>
      </div>
      Optitech Europa
    </div>

    <div class="badge">Análisis gratuito en minutos</div>

    <h1 class="headline">
      Ahorra en tu<br>
      factura de<br>
      <em>la luz hoy</em>
    </h1>

    <p class="subtext">
      Sube tu factura y nuestra IA analiza automáticamente cuánto puedes ahorrar. Sin compromiso. Sin llamadas. Solo resultados.
    </p>

    <div class="stats">
      <div class="stat">
        <span class="stat-number">€ 0</span>
        <span class="stat-label">Coste del análisis</span>
      </div>
      <div class="stat">
        <span class="stat-number">2 min</span>
        <span class="stat-label">Tiempo de respuesta</span>
      </div>
      <div class="stat">
        <span class="stat-number">100%</span>
        <span class="stat-label">Automatizado</span>
      </div>
    </div>

    <div class="features">
      <div class="feature">
        <div class="feature-dot"></div>
        Extracción automática de datos con IA
      </div>
      <div class="feature">
        <div class="feature-dot"></div>
        Comparativa con las mejores ofertas del mercado
      </div>
      <div class="feature">
        <div class="feature-dot"></div>
        Propuesta personalizada por WhatsApp
      </div>
      <div class="feature">
        <div class="feature-dot"></div>
        Nos encargamos de todo el cambio
      </div>
    </div>
  </div>

  <!-- RIGHT - FORM -->
  <div class="form-card">
    <div id="form-content">
      <div class="form-title">Analiza tu factura</div>
      <div class="form-subtitle">Rellena el formulario y recibe tu oferta por WhatsApp</div>

      <form id="captacion-form" novalidate>

        <div class="form-group">
          <label for="nombre">Nombre completo *</label>
          <input type="text" id="nombre" name="nombre" placeholder="Tu nombre y apellidos" required>
          <div class="error-msg" id="error-nombre">Este campo es obligatorio</div>
        </div>

        <div class="form-group">
          <label for="pais">País *</label>
          <select id="pais" name="pais" class="select-full" required>
            <option value="" disabled selected>Selecciona tu país</option>
            <option value="ES">🇪🇸 España</option>
            <option value="PT">🇵🇹 Portugal</option>
            <option value="FR">🇫🇷 Francia</option>
            <option value="DE">🇩🇪 Alemania</option>
            <option value="IT">🇮🇹 Italia</option>
            <option value="BE">🇧🇪 Bélgica</option>
            <option value="NL">🇳🇱 Países Bajos</option>
            <option value="AT">🇦🇹 Austria</option>
            <option value="CH">🇨🇭 Suiza</option>
            <option value="PL">🇵🇱 Polonia</option>
            <option value="CZ">🇨🇿 República Checa</option>
            <option value="RO">🇷🇴 Rumanía</option>
            <option value="HU">🇭🇺 Hungría</option>
            <option value="BG">🇧🇬 Bulgaria</option>
            <option value="HR">🇭🇷 Croacia</option>
            <option value="GR">🇬🇷 Grecia</option>
            <option value="SE">🇸🇪 Suecia</option>
            <option value="NO">🇳🇴 Noruega</option>
            <option value="DK">🇩🇰 Dinamarca</option>
            <option value="FI">🇫🇮 Finlandia</option>
            <option value="IE">🇮🇪 Irlanda</option>
            <option value="GB">🇬🇧 Reino Unido</option>
            <option value="MX">🇲🇽 México</option>
            <option value="CO">🇨🇴 Colombia</option>
            <option value="AR">🇦🇷 Argentina</option>
            <option value="CL">🇨🇱 Chile</option>
            <option value="PE">🇵🇪 Perú</option>
            <option value="VE">🇻🇪 Venezuela</option>
            <option value="EC">🇪🇨 Ecuador</option>
            <option value="BO">🇧🇴 Bolivia</option>
            <option value="PY">🇵🇾 Paraguay</option>
            <option value="UY">🇺🇾 Uruguay</option>
            <option value="DO">🇩🇴 Rep. Dominicana</option>
            <option value="MA">🇲🇦 Marruecos</option>
            <option value="OTHER">🌍 Otro</option>
          </select>
          <div class="error-msg" id="error-pais">Selecciona tu país</div>
        </div>

        <div class="form-group">
          <label for="telefono">Teléfono WhatsApp *</label>
          <div class="phone-row">
            <select id="prefijo" class="select-pais" title="Prefijo internacional">
              <option value="+34">🇪🇸 +34</option>
              <option value="+351">🇵🇹 +351</option>
              <option value="+33">🇫🇷 +33</option>
              <option value="+49">🇩🇪 +49</option>
              <option value="+39">🇮🇹 +39</option>
              <option value="+32">🇧🇪 +32</option>
              <option value="+31">🇳🇱 +31</option>
              <option value="+43">🇦🇹 +43</option>
              <option value="+41">🇨🇭 +41</option>
              <option value="+48">🇵🇱 +48</option>
              <option value="+420">🇨🇿 +420</option>
              <option value="+40">🇷🇴 +40</option>
              <option value="+36">🇭🇺 +36</option>
              <option value="+359">🇧🇬 +359</option>
              <option value="+385">🇭🇷 +385</option>
              <option value="+30">🇬🇷 +30</option>
              <option value="+46">🇸🇪 +46</option>
              <option value="+47">🇳🇴 +47</option>
              <option value="+45">🇩🇰 +45</option>
              <option value="+358">🇫🇮 +358</option>
              <option value="+353">🇮🇪 +353</option>
              <option value="+44">🇬🇧 +44</option>
              <option value="+52">🇲🇽 +52</option>
              <option value="+57">🇨🇴 +57</option>
              <option value="+54">🇦🇷 +54</option>
              <option value="+56">🇨🇱 +56</option>
              <option value="+51">🇵🇪 +51</option>
              <option value="+58">🇻🇪 +58</option>
              <option value="+593">🇪🇨 +593</option>
              <option value="+591">🇧🇴 +591</option>
              <option value="+595">🇵🇾 +595</option>
              <option value="+598">🇺🇾 +598</option>
              <option value="+1809">🇩🇴 +1809</option>
              <option value="+212">🇲🇦 +212</option>
            </select>
            <input type="tel" id="telefono" name="telefono" placeholder="6XX XXX XXX" required style="flex:1">
          </div>
          <div class="error-msg" id="error-telefono">Introduce un teléfono válido</div>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="tu@email.com">
        </div>

        <div class="form-group">
          <label>Factura de la luz *</label>
          <div class="upload-area" id="upload-area">
            <input type="file" id="factura" name="factura" accept=".pdf,image/*" required>
            <div class="upload-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2" stroke-linecap="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
              </svg>
            </div>
            <div class="upload-text">
              <strong>Haz click o arrastra</strong> tu factura aquí<br>
              PDF o imagen · Máx. 10 MB
            </div>
          </div>
          <div class="upload-preview" id="upload-preview">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            <span id="file-name">factura.pdf</span>
          </div>
          <div class="error-msg" id="error-factura">Adjunta tu factura de la luz</div>
        </div>

        <div class="consent">
          <input type="checkbox" id="consentimiento" name="consentimiento" required>
          <label for="consentimiento">
            Acepto el tratamiento de mis datos personales según la <a href="#">política de privacidad</a> de Optitech Europa para recibir mi análisis de ahorro energético.
          </label>
        </div>

        <button type="submit" class="btn-submit" id="btn-submit">
          <div class="spinner" id="spinner"></div>
          <span id="btn-text">Analizar mi factura gratis →</span>
        </button>

      </form>
    </div>

    <!-- SUCCESS -->
    <div class="success-state" id="success-state">
      <div class="success-icon">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2.5" stroke-linecap="round">
          <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
      </div>
      <div class="success-title">¡Recibido!</div>
      <div class="success-text">
        Estamos analizando tu factura con IA.<br>
        En breve recibirás tu oferta personalizada<br>
        por <strong style="color:var(--green)">WhatsApp</strong>.
      </div>
    </div>
  </div>

</div>

<script>
  // URL del webhook de n8n — sin credenciales, es pública
  const WEBHOOK_URL = 'https://practicas1.optitech-europa.online/webhook/daniel-captacion-facturas';

  // Sincronizar país → prefijo automáticamente
  const paisPrefijos = {
    ES:'+34', PT:'+351', FR:'+33', DE:'+49', IT:'+39', BE:'+32',
    NL:'+31', AT:'+43', CH:'+41', PL:'+48', CZ:'+420', RO:'+40',
    HU:'+36', BG:'+359', HR:'+385', GR:'+30', SE:'+46', NO:'+47',
    DK:'+45', FI:'+358', IE:'+353', GB:'+44', MX:'+52', CO:'+57',
    AR:'+54', CL:'+56', PE:'+51', VE:'+58', EC:'+593', BO:'+591',
    PY:'+595', UY:'+598', DO:'+1809', MA:'+212'
  };

  document.getElementById('pais').addEventListener('change', function() {
    const prefijo = paisPrefijos[this.value];
    if (prefijo) document.getElementById('prefijo').value = prefijo;
  });

  // Upload preview
  const fileInput   = document.getElementById('factura');
  const uploadArea  = document.getElementById('upload-area');
  const uploadPreview = document.getElementById('upload-preview');
  const fileName    = document.getElementById('file-name');

  fileInput.addEventListener('change', () => {
    if (fileInput.files[0]) {
      fileName.textContent = fileInput.files[0].name;
      uploadPreview.classList.add('show');
    }
  });

  uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('drag-over');
  });
  uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('drag-over'));
  uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('drag-over');
    if (e.dataTransfer.files[0]) {
      fileInput.files = e.dataTransfer.files;
      fileName.textContent = e.dataTransfer.files[0].name;
      uploadPreview.classList.add('show');
    }
  });

  // Submit
  document.getElementById('captacion-form').addEventListener('submit', async (e) => {
    e.preventDefault();

    let valid = true;

    const nombre        = document.getElementById('nombre').value.trim();
    const pais          = document.getElementById('pais').value;
    const prefijo       = document.getElementById('prefijo').value;
    const telefonoRaw   = document.getElementById('telefono').value.trim().replace(/\s/g, '');
    const telefonoCompleto = prefijo + telefonoRaw;
    const factura       = fileInput.files[0];
    const consentimiento = document.getElementById('consentimiento').checked;

    document.querySelectorAll('.error-msg').forEach(el => el.style.display = 'none');

    if (!nombre)                              { document.getElementById('error-nombre').style.display = 'block';    valid = false; }
    if (!pais)                                { document.getElementById('error-pais').style.display = 'block';      valid = false; }
    if (!telefonoRaw || telefonoRaw.length < 7) { document.getElementById('error-telefono').style.display = 'block'; valid = false; }
    if (!factura)                             { document.getElementById('error-factura').style.display = 'block';   valid = false; }
    if (!consentimiento) valid = false;

    if (!valid) return;

    const btn     = document.getElementById('btn-submit');
    const spinner = document.getElementById('spinner');
    const btnText = document.getElementById('btn-text');

    btn.disabled = true;
    spinner.style.display = 'block';
    btnText.textContent = 'Analizando...';

    try {
      const formData = new FormData();
      formData.append('nombre',     nombre);
      formData.append('telefono',   telefonoCompleto);
      formData.append('pais',       pais);
      formData.append('email',      document.getElementById('email').value.trim());
      formData.append('factura',    factura);
      formData.append('pdf_nombre', factura.name);

      await fetch(WEBHOOK_URL, { method: 'POST', body: formData });

      document.getElementById('form-content').style.display = 'none';
      document.getElementById('success-state').style.display = 'block';

    } catch (err) {
      btn.disabled = false;
      spinner.style.display = 'none';
      btnText.textContent = 'Analizar mi factura gratis →';
      alert('Ha ocurrido un error. Por favor inténtalo de nuevo.');
    }
  });
</script>

</body>
</html>
