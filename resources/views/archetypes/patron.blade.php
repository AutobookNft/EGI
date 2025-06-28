<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlorenceEGI per il Mecenate: Ponte tra Arte e Futuro Virtuoso</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Source+Sans+Pro:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Definizione dei colori e font basati sulle Brand Guidelines di FlorenceEGI */
        :root {
            --oro-fiorentino: #D4A574;
            --verde-rinascita: #2D5016;
            --blu-algoritmo: #1B365D;
            --grigio-pietra: #6B6B6B;
            --rosso-urgenza: #C13120;
            --arancio-energia: #E67E22;
            --viola-innovazione: #8E44AD;
            --white: #ffffff;
            --light-grey: #F8F8F8;
            --dark-grey: #333333;
        }

        /* Stili globali per il corpo della pagina (Mobile-First) */
        body {
            font-family: 'Source Sans Pro', sans-serif;
            line-height: 1.6;
            color: var(--grigio-pietra);
            margin: 0;
            padding: 0;
            background-color: var(--white);
            overflow-x: hidden; /* Evita lo scroll orizzontale */
        }

        /* Contenitore principale per simulare una "finestra" nel canvas */
        #app-container {
            max-width: 1200px; /* Larghezza massima del contenuto dell'applicazione */
            margin: 20px auto; /* Centra il contenitore e aggiunge margine intorno */
            background-color: var(--white);
            border-radius: 15px; /* Bordi arrotondati per l'intera app */
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); /* Ombra per dare profondità */
            overflow: hidden; /* Assicura che i bordi arrotondati funzionino con i contenuti */
            display: flex; /* Layout flessibile per sidebar e contenuto */
            flex-direction: column; /* Mobile-first: sidebar sopra o sotto il contenuto */
            min-height: calc(100vh - 40px); /* Altezza minima per la visibilità */
        }

        /* Sidebar per la navigazione (Mobile-First) */
        .sidebar {
            width: 100%; /* Larghezza piena su mobile */
            background-color: var(--blu-algoritmo);
            color: var(--white);
            padding: 20px;
            box-sizing: border-box;
            transform: translateX(-100%); /* Nascondi la sidebar su mobile */
            position: fixed; /* Fisso per l'overlay mobile */
            top: 0;
            left: 0;
            height: 100%;
            z-index: 1000; /* Sopra tutto il resto */
            transition: transform 0.3s ease-in-out;
            border-radius: 0 15px 15px 0; /* Bordi arrotondati solo su un lato */
            overflow-y: auto; /* Permette lo scroll interno della sidebar su mobile */
        }

        .sidebar.open {
            transform: translateX(0); /* Mostra la sidebar */
        }

        /* FIXED: Logo styling migliorato */
        .sidebar .logo-container {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 30px;
            padding: 0;
        }

        .sidebar .logo-container .logo {
            height: 40px; /* Altezza fissa controllata */
            width: auto; /* Mantiene proporzioni */
            max-width: 150px; /* Limite massimo larghezza */
            object-fit: contain; /* Mantiene proporzioni senza distorsione */
        }

        .sidebar .logo-container .logo-text {
            margin-left: 10px;
            font-size: 0.9em;
            font-weight: 600;
            color: var(--white);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar .logo-container a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: inherit;
            transition: opacity 0.3s ease;
        }

        .sidebar .logo-container a:hover {
            opacity: 0.8;
        }

        .sidebar nav {
            position: static;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        /* Styling per i link della sidebar (NON BOTTONI) */
        .sidebar nav a {
            color: var(--white);
            text-decoration: none;
            padding: 10px 15px; /* Padding più sottile */
            display: block;
            width: 100%;
            transition: background-color 0.3s ease, color 0.3s ease, border-left 0.3s ease;
            border-radius: 0; /* Rimuove bordi arrotondati */
            margin-bottom: 2px; /* Margine minimale tra le voci */
            border-left: 5px solid transparent; /* Bordo iniziale trasparente */
            font-weight: 400; /* Peso normale */
        }

        .sidebar nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--oro-fiorentino);
            border-left-color: var(--oro-fiorentino); /* Bordo a sinistra all'hover */
        }

        .sidebar nav a.active {
            background-color: rgba(255, 255, 255, 0.15); /* Sfondo leggermente più scuro per attivo */
            font-weight: 600; /* Semibold per attivo */
            border-left-color: var(--verde-rinascita); /* Bordo a sinistra per attivo */
            color: var(--verde-rinascita); /* Colore verde per attivo */
        }

        /* FIXED: Hamburger Menu Icon con Font Awesome */
        .hamburger {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            cursor: pointer;
            padding: 12px;
            background-color: rgba(27, 54, 93, 0.9); /* Blu algoritmo con trasparenza */
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: var(--white);
            font-size: 18px;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hamburger:hover {
            background-color: var(--blu-algoritmo);
            transform: scale(1.05);
        }

        .hamburger:active {
            transform: scale(0.95);
        }

        /* Contenuto principale della pagina */
        .main-content {
            flex-grow: 1;
            padding-top: 60px; /* Spazio per l'hamburger menu in mobile */
            padding-left: 0; /* Reset per mobile */
            position: relative; /* Necessario per posizionare header-top-nav-mobile-hamburger */
        }

        /* Stili per l'header principale della pagina */
        header {
            /* Immagine di sfondo aggiornata (placeholder per il canvas) */
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('{{ asset('images/default/patron_banner_background_rinascimento_1.png') }}') no-repeat center center/cover;
            color: var(--white);
            padding: 80px 20px;
            text-align: center;
            position: relative;
            border-radius: 0 0 15px 15px;
            box-sizing: border-box; /* Include padding nel width/height */
        }

        /* Navbar globale per desktop */
        .header-top-nav {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10;
            display: none; /* Nascosta su mobile per default */
        }
        .header-top-nav a {
            color: var(--white);
            text-decoration: none;
            margin-left: 20px;
            font-weight: 400;
            transition: color 0.3s ease;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .header-top-nav a:hover {
            color: var(--oro-fiorentino);
        }
        .header-top-nav a.active-actor {
            color: var(--verde-rinascita);
            font-weight: 600;
            background-color: rgba(255,255,255,0.1);
        }

        /* FIXED: Hamburger per la Navbar Globale con Font Awesome */
        .top-nav-hamburger {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1001; /* Sopra tutto, anche la sidebar hamburger */
            cursor: pointer;
            padding: 12px;
            background-color: rgba(27, 54, 93, 0.9); /* Blu algoritmo con trasparenza */
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: var(--white);
            font-size: 18px;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .top-nav-hamburger:hover {
            background-color: var(--blu-algoritmo);
            transform: scale(1.05);
        }

        .top-nav-hamburger:active {
            transform: scale(0.95);
        }

        /* Overlay per la Navbar Globale Mobile */
        .top-nav-overlay {
            position: fixed;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background-color: var(--blu-algoritmo); /* Stesso colore della sidebar */
            z-index: 999; /* Appena sotto la sidebar hamburger */
            transform: translateX(100%); /* Nascosto a destra */
            transition: transform 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-radius: 15px 0 0 15px;
        }
        .top-nav-overlay.open {
            transform: translateX(0);
        }
        .top-nav-overlay a {
            color: var(--white);
            text-decoration: none;
            padding: 15px 20px;
            font-size: 1.5em;
            display: block;
            width: 80%; /* Larghezza per i link nell'overlay */
            text-align: center;
            margin-bottom: 10px;
            border-radius: 8px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .top-nav-overlay a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--oro-fiorentino);
        }
        .top-nav-overlay a.active-actor {
            background-color: var(--verde-rinascita);
            font-weight: 700;
        }

        .header-content {
            max-width: 800px;
            margin: 0 auto;
        }

        header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5em;
            margin-bottom: 10px;
            color: var(--white);
        }

        header p {
            font-family: 'Playfair Display', serif;
            font-size: 1.2em;
            margin-top: 0;
            color: var(--white);
        }

        .cta-button {
            display: inline-block;
            background-color: var(--verde-rinascita);
            color: var(--white);
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 30px;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .cta-button:hover {
            background-color: var(--oro-fiorentino);
            transform: translateY(-3px);
        }

        /* Stili per le sezioni generiche della pagina */
        .section {
            padding: 60px 20px;
            max-width: 960px;
            margin: 0 auto;
            box-sizing: border-box; /* Include padding nel width/height */
        }

        .section:nth-child(even) {
            background-color: var(--light-grey);
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .section h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2em;
            color: var(--blu-algoritmo);
            text-align: center;
            margin-bottom: 30px;
        }

        .section h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5em;
            color: var(--verde-rinascita);
            margin-bottom: 15px;
            text-align: center;
        }

        .text-block {
            text-align: center;
            margin-bottom: 30px;
        }

        .text-block p {
            max-width: 700px;
            margin: 0 auto;
            font-size: 1em;
            color: var(--dark-grey);
        }

        /* Stili per le liste di icone/feature */
        .icon-list {
            display: grid;
            grid-template-columns: 1fr; /* Una colonna su mobile */
            gap: 20px;
            margin-top: 30px;
        }

        .icon-item {
            text-align: center;
            background-color: var(--white);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.07);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid var(--light-grey);
        }

        .icon-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .icon-item i {
            font-size: 2.5em;
            margin-bottom: 15px;
            color: var(--oro-fiorentino);
        }

        .icon-item h4 {
            font-family: 'Playfair Display', serif;
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .icon-item p {
            font-size: 0.9em;
            color: var(--grigio-pietra);
        }

        /* Stili per layout a due colonne (Mobile-First: stack in colonna) */
        .two-column {
            flex-direction: column;
            gap: 30px;
            margin-top: 30px;
            justify-content: center;
        }

        .col-left, .col-right {
            flex: 1;
            min-width: 300px;
            background-color: var(--white);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .col-left h4, .col-right h4 {
            font-family: 'Playfair Display', serif;
            color: var(--blu-algoritmo);
            font-size: 1.3em;
            margin-top: 0;
            text-align: center;
            margin-bottom: 15px;
        }

        .col-left ul, .col-right ul {
            list-style: none;
            padding: 0;
        }

        .col-left ul li, .col-right ul li {
            font-size: 1em;
            margin-bottom: 10px;
            display: flex;
            align-items: flex-start;
        }

        .col-left ul li::before {
            content: '❌';
            color: var(--rosso-urgenza);
            margin-right: 15px;
            font-size: 1.2em;
        }

        .col-right ul li::before {
            content: '✅';
            color: var(--verde-rinascita);
            margin-right: 15px;
            font-size: 1.2em;
        }

        /* Stili per la timeline */
        .timeline {
            position: relative;
            margin: 40px 0;
            padding-left: 15px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 5px;
            top: 0;
            width: 4px;
            height: 100%;
            background-color: var(--verde-rinascita);
            border-radius: 2px;
        }

        .timeline-item {
            margin-bottom: 30px;
            position: relative;
            padding-left: 30px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0px;
            top: 5px;
            width: 16px;
            height: 16px;
            background-color: var(--verde-rinascita);
            border-radius: 50%;
            border: 3px solid var(--white);
            box-shadow: 0 0 0 1px var(--verde-rinascita);
            z-index: 1;
        }

        .timeline-item h4 {
            font-family: 'Playfair Display', serif;
            color: var(--blu-algoritmo);
            font-size: 1.2em;
            margin-bottom: 5px;
        }

        .timeline-item p {
            color: var(--grigio-pietra);
            font-size: 0.9em;
        }

        /* Stili per le tabelle del modello economico */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 40px 0;
            font-size: 0.95em;
        }

        table th, table td {
            border: 1px solid #ddd; /* Bordo sottile */
            padding: 12px 15px;
            text-align: left;
        }

        table th {
            background-color: var(--blu-algoritmo);
            color: var(--white);
            font-weight: 600;
        }

        table tr:nth-child(even) {
            background-color: var(--light-grey); /* Strisce chiare */
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        table td strong {
            color: var(--verde-rinascita);
            font-weight: 700; /* Reso più forte come nello screenshot */
        }

        /* Stili per la griglia delle storie di successo */
        .stories-grid {
            display: grid;
            grid-template-columns: 1fr; /* Una colonna su mobile */
            gap: 20px;
            margin-top: 30px;
        }

        .story-card {
            background-color: var(--white);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            text-align: left;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-top: 4px solid var(--oro-fiorentino);
        }

        .story-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }

        .story-card h4 {
            font-family: 'Playfair Display', serif;
            color: var(--blu-algoritmo);
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .story-card ul {
            list-style: none;
            padding: 0;
            font-size: 0.9em;
            color: var(--grigio-pietra);
        }

        .story-card ul li {
            margin-bottom: 8px;
        }

        /* Stili per le feature box degli strumenti */
        .feature-boxes {
            display: grid;
            grid-template-columns: 1fr; /* Una colonna su mobile */
            gap: 20px;
            margin-top: 30px;
        }

        .feature-box {
            background-color: var(--white);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            text-align: left;
            border-top: 4px solid var(--verde-rinascita);
        }

        .feature-box h3 {
            color: var(--blu-algoritmo);
            text-align: left;
            margin-top: 0;
            font-size: 1.3em;
        }

        .feature-box ul {
            list-style: none;
            padding: 0;
            font-size: 0.95em;
        }

        .feature-box ul li {
            margin-bottom: 10px;
            display: flex;
            align-items: flex-start;
        }

        .feature-box ul li::before {
            content: '✔️';
            color: var(--verde-rinascita);
            margin-right: 10px;
            font-size: 1.1em;
        }

        /* Stili per l'accordion delle FAQ */
        .accordion-container {
            margin-top: 30px;
        }

        .accordion-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .accordion-item:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .accordion-header {
            background-color: var(--white);
            padding: 15px 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1em;
            font-weight: 600;
            color: var(--dark-grey);
            transition: background-color 0.3s ease;
            border: none;
            width: 100%;
            text-align: left;
        }

        .accordion-header:hover {
            background-color: var(--light-grey);
        }

        .accordion-header .icon {
            font-size: 1.5em;
            color: var(--grigio-pietra);
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .accordion-header.active .icon {
            transform: rotate(180deg);
            color: var(--verde-rinascita);
        }

        .accordion-content {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease-out, padding 0.4s ease-out;
            background-color: var(--light-grey);
            color: var(--grigio-pietra);
            border-top: 1px solid #eee;
        }

        .accordion-content p {
            padding: 15px 0;
            margin: 0;
        }

        .accordion-content.open {
            max-height: 500px;
            padding-bottom: 20px;
        }

        /* Stili per il programma Pioneer */
        .pioneer-program {
            background-color: var(--verde-rinascita);
            color: var(--white);
            padding: 40px 20px;
            text-align: center;
            border-radius: 12px;
            margin: 60px auto;
            max-width: 960px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.25);
        }

        .pioneer-program h2 {
            color: var(--white);
            font-size: 2em;
            margin-bottom: 20px;
        }

        .pioneer-program h3 {
            color: var(--oro-fiorentino);
            font-size: 1.5em;
            margin-bottom: 15px;
        }

        .pioneer-benefits-grid {
            display: grid;
            grid-template-columns: 1fr; /* Una colonna su mobile */
            gap: 15px;
            margin-top: 30px;
            text-align: left;
        }

        .pioneer-benefit-item {
            background-color: rgba(255, 255, 255, 0.15);
            padding: 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            color: var(--white);
            font-size: 1em;
        }

        .pioneer-benefit-item .icon {
            font-size: 1.8em;
            margin-right: 10px;
            color: var(--oro-fiorentino);
        }

        .pioneer-program h4 {
            font-family: 'Source Sans Pro', sans-serif;
            font-size: 1.1em;
            margin-top: 15px;
            color: var(--white);
            text-align: center;
        }

        .pioneer-requirements ul {
            list-style: none;
            padding: 0;
            margin-top: 15px;
            text-align: center;
        }

        .pioneer-requirements ul li {
            margin-bottom: 8px;
            font-size: 1em;
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pioneer-requirements ul li::before {
            content: '✅';
            color: var(--oro-fiorentino);
            margin-right: 10px;
        }

        /* Stili per i passi dell'onboarding */
        .onboarding-steps {
            display: grid;
            grid-template-columns: 1fr; /* Una colonna su mobile */
            gap: 20px;
            margin-top: 30px;
        }

        .onboarding-step-card {
            background-color: var(--white);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            text-align: center;
            border-bottom: 4px solid var(--oro-fiorentino);
            transition: transform 0.3s ease;
        }

        .onboarding-step-card:hover {
            transform: translateY(-5px);
        }

        .onboarding-step-card .step-number {
            font-family: 'Playfair Display', serif;
            font-size: 2em;
            color: var(--oro-fiorentino);
            margin-bottom: 10px;
            display: block;
        }

        .onboarding-step-card .step-icon {
            font-size: 2.5em;
            color: var(--verde-rinascita);
            margin-bottom: 10px;
        }

        .onboarding-step-card h4 {
            font-family: 'Playfair Display', serif;
            color: var(--blu-algoritmo);
            font-size: 1.3em;
            margin-bottom: 10px;
        }

        .onboarding-step-card ul {
            list-style: none;
            padding: 0;
            font-size: 0.9em;
            color: var(--grigio-pietra);
        }

        .onboarding-step-card ul li {
            margin-bottom: 5px;
        }

        /* Stili per contatti e risorse */
        .contact-resources {
            flex-direction: column; /* Stack in colonna su mobile */
            gap: 20px;
            margin-top: 30px;
        }

        .contact-block, .download-block {
            background-color: var(--white);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .contact-block h3, .download-block h3 {
            color: var(--blu-algoritmo);
            margin-top: 0;
            text-align: left;
            font-size: 1.3em;
        }

        .contact-block ul {
            list-style: none;
            padding: 0;
        }

        .contact-block ul li, .download-block ul li {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            font-size: 1em;
            color: var(--dark-grey);
        }

        .contact-block ul li .icon {
            font-size: 1.3em;
            color: var(--verde-rinascita);
            margin-right: 10px;
        }
        .contact-block ul li a, .download-block ul li a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .contact-block ul li a:hover, .download-block ul li a:hover {
            color: var(--oro-fiorentino);
        }

        /* Stili per il messaggio personale */
        .personal-message {
            background-color: var(--light-grey);
            padding: 40px 20px;
            margin-top: 60px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }

        .personal-message h2 {
            color: var(--blu-algoritmo);
            font-size: 2em;
            margin-bottom: 20px;
        }

        .personal-message p {
            font-family: 'Playfair Display', serif;
            font-size: 1.1em;
            margin-bottom: 30px;
        }

        .signature {
            font-size: 1.4em;
        }

        .final-slogan {
            font-size: 1.1em;
            font-weight: 600;
            color: var(--blu-algoritmo);
            margin-top: 20px;
        }

        /* Footer */
        footer {
            background-color: var(--blu-algoritmo);
            color: var(--white);
            text-align: center;
            padding: 40px 20px;
            font-size: 0.9em;
            border-radius: 15px 15px 0 0;
        }

        footer a {
            color: var(--oro-fiorentino);
            text-decoration: none;
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: var(--white);
        }

        /* Desktop specific styles (overrides for larger screens) */
        @media (min-width: 769px) {
            #app-container {
                flex-direction: row; /* Sidebar a sinistra, contenuto a destra */
            }

            .hamburger {
                display: none; /* Nascondi l'hamburger della sidebar su desktop */
            }
            .top-nav-hamburger {
                display: none; /* Nascondi l'hamburger della top nav su desktop */
            }
            .header-top-nav {
                display: block; /* Mostra la top nav su desktop */
            }

            .sidebar {
                position: fixed; /* Sidebar fissa in viewport */
                top: 0;
                left: 0;
                width: 250px; /* Larghezza fissa della sidebar */
                height: 100vh; /* Altezza piena della viewport */
                padding: 30px;
                transform: translateX(0); /* Sempre visibile su desktop */
                border-radius: 15px 0 0 15px; /* Bordi arrotondati solo su un lato */
                overflow-y: auto; /* Permette lo scroll interno della sidebar se il contenuto supera l'altezza */
                z-index: 900; /* Assicurati che sia sopra il main-content ma sotto gli overlay mobili */
            }

            .main-content {
                margin-left: 250px; /* Spazio per la sidebar fissa */
                padding-top: 0; /* Nessun padding superiore dovuto agli hamburger */
            }

            header {
                padding: 100px 0; /* Torna al padding originale */
            }

            header h1 {
                font-size: 3.5em; /* Torna alla dimensione originale */
            }

            header p {
                font-size: 1.5em; /* Torna alla dimensione originale */
            }

            .section {
                padding: 80px 20px; /* Torna al padding originale */
            }

            .section h2 {
                font-size: 2.5em; /* Torna alla dimensione originale */
            }

            .section h3 {
                font-size: 1.8em; /* Torna alla dimensione originale */
            }

            .text-block p {
                font-size: 1.1em;
            }

            .icon-list, .stories-grid, .feature-boxes, .onboarding-steps {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 30px;
            }

            .two-column {
                flex-direction: row; /* Torna a layout a due colonne */
            }

            .timeline {
                padding-left: 20px;
            }
            .timeline::before {
                left: 0;
            }
            .timeline-item::before {
                left: -8px;
                width: 20px;
                height: 20px;
                border: 4px solid var(--white);
                box-shadow: 0 0 0 2px var(--verde-rinascita);
            }

            table {
                font-size: 0.95em;
            }
            table th, table td {
                padding: 12px 15px;
            }

            .pioneer-program {
                padding: 60px 20px;
            }
            .pioneer-program h2 {
                font-size: 2.8em;
            }

            .pioneer-benefits-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }

            .contact-resources {
                flex-direction: row;
            }
            .personal-message h2 {
                font-size: 2.8em;
            }
            .personal-message p {
                font-size: 1.4em;
            }
        }
    </style>
    <!-- Importazione di Font Awesome per le icone -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <!-- Contenitore esterno per simulare la "finestra" dell'app nel Canvas -->
    <div id="app-container" itemscope itemtype="https://schema.org/WebPage">

        <!-- FIXED: Hamburger Icon per Sidebar con Font Awesome -->
        <button class="hamburger" id="sidebar-hamburger" aria-label="Apri menu sezioni" aria-controls="sidebar-menu" aria-expanded="false">
            <i class="fas fa-bars" aria-hidden="true"></i>
        </button>

        <!-- FIXED: Hamburger Icon per Top Nav con Font Awesome -->
        <button class="top-nav-hamburger" id="top-nav-hamburger" aria-label="Apri menu attori" aria-controls="top-nav-overlay" aria-expanded="false">
            <i class="fas fa-users" aria-hidden="true"></i>
        </button>

        <!-- Sidebar per la navigazione interna (si trasforma in Hamburger su mobile) -->
        <aside class="sidebar" id="sidebar" role="navigation" aria-label="Menu di navigazione della pagina Mecenate">
            <!-- FIXED: Logo container con classi e styling appropriati -->
            <div class="logo-container">
                <a href="{{ url('/home') }}" aria-label="{{ __('guest_layout.logo_aria_label') }}">
                    <img src="{{ asset('images/logo/logo_1.webp') }}" alt="{{ __('guest_layout.logo_alt_text') }}" class="logo" loading="lazy" decoding="async">
                    <span class="logo-text">{{ __('guest_layout.navbar_brand_name') }}</span>
                </a>
            </div>
            <nav id="sidebar-menu">
                <!-- Links della sidebar con target alle sezioni interne -->
                <a href="#intro" class="sidebar-link" role="menuitem">Introduzione</a>
                <a href="#ruolo" class="sidebar-link" role="menuitem">Il Tuo Ruolo Concreto</a>
                <a href="#diventare" class="sidebar-link" role="menuitem">Diventare Mecenate</a>
                <a href="#percorso" class="sidebar-link" role="menuitem">Percorso di Successo</a>
                <a href="#economico" class="sidebar-link" role="menuitem">Modello Economico</a>
                <a href="#storie" class="sidebar-link" role="menuitem">Storie di Successo</a>
                <a href="#strumenti" class="sidebar-link" role="menuitem">Strumenti e Supporto</a>
                <a href="#sfaccettature" class="sidebar-link" role="menuitem">Sfaccettature del Ruolo</a>
                <a href="#faq" class="sidebar-link" role="menuitem">Domande Frequenti</a>
                <a href="#pioneer" class="sidebar-link" role="menuitem">Programma Pioneer</a>
                <a href="#inizia" class="sidebar-link" role="menuitem">Inizia Oggi</a>
                <a href="#contatti" class="sidebar-link" role="menuitem">Contatti e Risorse</a>
            </nav>
        </aside>

        <!-- Overlay per la Navbar Globale Mobile -->
        <div class="top-nav-overlay" id="top-nav-overlay" role="dialog" aria-modal="true" aria-labelledby="top-nav-label">
            <h2 id="top-nav-label" class="sr-only">Navigazione Attori Principali</h2>
            <nav role="navigation" aria-label="Navigazione tra gli attori della piattaforma">
                <a href="#">Creator</a>
                <a href="#">Collezionisti</a>
                <a href="#">Trader Pro</a>
                <a href="#">EPP</a>
                <a href="#">Aziende</a>
                <a href="#">VIP</a>
            </nav>
        </div>

        <!-- Contenuto principale della pagina -->
        <div class="main-content" role="main">
            <!-- Header della pagina con navbar globale (desktop) -->
            <header>
                <div class="header-top-nav" role="navigation" aria-label="Navigazione tra gli attori della piattaforma">
                    <!-- Navbar globale per navigare tra gli attori -->
                    <a href="/home">Home</a>
                    <a href="#">Creator</a>
                    <a href="#">Collezionisti</a>
                    <a href="#">Trader Pro</a>
                    <a href="#">EPP</a>
                    <a href="#">Aziende</a>
                    <a href="#">VIP</a>
                </div>
                <div class="header-content">
                    <h1 itemprop="name">FlorenceEGI – Il nuovo Rinascimento ecologico digitale</h1>
                    <p itemprop="description">Trasformare la passione per l'arte in una curatela dall'impatto globale.</p>
                    <a href="#intro" class="cta-button" role="button">Scopri il Tuo Ruolo nel Nuovo Rinascimento</a>
                </div>
            </header>

            <main>
                <!-- Sezione Introduzione: Chi è il Mecenate in FlorenceEGI -->
                <section id="intro" class="section" itemscope itemtype="https://schema.org/AboutPage">
                    <h2 itemprop="headline">Il Mecenate in FlorenceEGI: Una Nuova Espressione di Curatela e Impatto</h2>
                    <h3>Oltre il collezionista. Oltre l'investitore. Un catalizzatore di valore.</h3>
                    <div class="text-block">
                        <p itemprop="abstract">Il Mecenate in FlorenceEGI incarna una figura innovativa e dinamica nel panorama dell'arte digitale. Non è un gallerista nel senso tradizionale, né unicamente un investitore; è molto di più.</p>
                    </div>
                    <div class="icon-list">
                        <div class="icon-item" itemprop="disambiguatingDescription">
                            <i class="fas fa-handshake" aria-hidden="true"></i>
                            <h4>Un Facilitatore Culturale</h4>
                            <p>Aiuta gli artisti a esplorare e a prosperare nel contesto digitale, offrendo supporto nel navigare le nuove frontiere espressive.</p>
                        </div>
                        <div class="icon-item" itemprop="disambiguatingDescription">
                            <i class="fas fa-bullhorn" aria-hidden="true"></i>
                            <h4>Un Promotore Illuminato</h4>
                            <p>Utilizza la propria rete di relazioni per amplificare la risonanza delle opere, connettendo artisti e collezionisti.</p>
                        </div>
                        <div class="icon-item" itemprop="disambiguatingDescription">
                            <i class="fas fa-chart-line" aria-hidden="true"></i>
                            <h4>Un Partner per la Crescita</h4>
                            <p>Cresce in sinergia con gli artisti che decide di sostenere, creando relazioni durature e significative.</p>
                        </div>
                        <div class="icon-item" itemprop="disambiguatingDescription">
                            <i class="fas fa-leaf" aria-hidden="true"></i>
                            <h4>Un Agente di Trasformazione</h4>
                            <p>Ogni azione intrapresa genera un impatto ambientale positivo e misurabile, contribuendo alla rigenerazione del pianeta.</p>
                        </div>
                    </div>
                </section>

                <!-- Sezione Ruolo e Contributo: Il Tuo Ruolo Concreto nel Nuovo Rinascimento -->
                <section id="ruolo" class="section" itemscope itemtype="https://schema.org/Role">
                    <h2 itemprop="name">Il Tuo Ruolo Concreto nel Nuovo Rinascimento</h2>
                    <div class="icon-list">
                        <div class="icon-item" itemprop="description">
                            <i class="fas fa-lightbulb" aria-hidden="true" style="color: var(--verde-rinascita);"></i>
                            <h4>Scoperta e Valorizzazione</h4>
                            <p><strong>Identificare e valorizzare talenti</strong> artistici nel tuo contesto locale o attraverso le piattaforme digitali.</p>
                        </div>
                        <div class="icon-item" itemprop="description">
                            <i class="fas fa-cogs" aria-hidden="true" style="color: var(--verde-rinascita);"></i>
                            <h4>Assistenza Tecnica</h4>
                            <p><strong>Assistere gli artisti nella dimensione tecnica</strong>, permettendo loro di focalizzarsi sulla creazione.</p>
                        </div>
                        <div class="icon-item" itemprop="description">
                            <i class="fas fa-upload" aria-hidden="true" style="color: var(--verde-rinascita);"></i>
                            <h4>Pubblicazione Opere</h4>
                            <p><strong>Contestualizzare e pubblicare le opere</strong> sulla piattaforma FlorenceEGI.</p>
                        </div>
                        <div class="icon-item" itemprop="description">
                            <i class="fas fa-network-wired" aria-hidden="true" style="color: var(--verde-rinascita);"></i>
                            <h4>Attivazione Network</h4>
                            <p><strong>Attivare la tua rete di contatti</strong> per agevolare la vendita delle opere.</p>
                        </div>
                        <div class="icon-item" itemprop="description">
                            <i class="fas fa-comments" aria-hidden="true" style="color: var(--verde-rinascita);"></i>
                            <h4>Costruzione Relazioni</h4>
                            <p><strong>Costruire relazioni durature</strong> con artisti e collezionisti, diventando un punto di riferimento nel settore.</p>
                        </div>
                        <div class="icon-item" itemprop="description">
                            <i class="fas fa-dollar-sign" aria-hidden="true" style="color: var(--verde-rinascita);"></i>
                            <h4>Guadagno Attivo</h4>
                            <p><strong>Realizzare un guadagno percentuale</strong> su ogni successo generato. E tutto questo è accessibile <strong>SENZA richiedere alcun investimento economico iniziale</strong>.</p>
                        </div>
                    </div>
                </section>

                <!-- Sezione Percorso di Partecipazione: Diventare Mecenate -->
                <section id="diventare" class="section">
                    <h2>Diventare Mecenate: La Via al Nuovo Rinascimento aperta a Tutti</h2>
                    <h3>Non è richiesto un percorso accademico tradizionale o esperienze pregresse nel settore.</h3>
                    <div class="two-column">
                        <div class="col-left">
                            <h4>Non Servono:</h4>
                            <ul>
                                <li>Laurea in storia dell'arte o discipline affini.</li>
                                <li>Esperienza consolidata nel mercato dell'arte.</li>
                                <li>Capitale iniziale da investire.</li>
                                <li>Ufficio o uno spazio espositivo fisico.</li>
                                <li>Connessioni preesistenti nell'élite artistica.</li>
                            </ul>
                        </div>
                        <div class="col-right">
                            <h4>Ciò che davvero alimenta il successo di un Mecenate:</h4>
                            <ul>
                                <li>Una sincera e profonda passione per l'arte.</li>
                                <li>Spiccate capacità relazionali e di networking.</li>
                                <li>La volontà di apprendere e di evolvere in un contesto dinamico.</li>
                                <li>La disponibilità di poche ore alla settimana.</li>
                                <li>Un semplice smartphone o un computer.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="text-block" style="margin-top: 40px;">
                        <p>FlorenceEGI democratizza l'accesso al mondo dell'arte, permettendo a chiunque, con la giusta dedizione, di diventare un Mecenate.</p>
                    </div>
                </section>

                <!-- Sezione Percorso da zero a Mecenate di Successo: Passo dopo Passo -->
                <section id="percorso" class="section">
                    <h2>Il Percorso da zero a Mecenate di Successo: Passo dopo Passo</h2>
                    <div class="timeline" role="list">
                        <div class="timeline-item" role="listitem">
                            <h4>Fase 1: Inizia (Prime 1-2 Settimane)</h4>
                            <p>Iscrizione gratuita, training online di 2 ore, ricezione del kit digitale completo, ingresso nella community Mecenati.</p>
                        </div>
                        <div class="timeline-item" role="listitem">
                            <h4>Fase 2: Scopri (Primo Mese)</h4>
                            <p>Identificazione di 2-3 artisti nel tuo network o territorio, contatto e spiegazione dell'opportunità, utilizzo dei materiali forniti da FlorenceEGI. Nessuna spesa richiesta.</p>
                        </div>
                        <div class="timeline-item" role="listitem">
                            <h4>Fase 3: Attiva (Mesi 2-3)</h4>
                            <p>Aiuto agli artisti nel caricamento delle prime opere, redazione descrizioni con AI, condivisione nel network personale, sfruttamento marketing gratuito della piattaforma.</p>
                        </div>
                        <div class="timeline-item" role="listitem">
                            <h4>Fase 4: Cresci (Dal Mese 4 in poi)</h4>
                            <p>Le prime vendite generano commissioni, possibilità di reinvestire (opzionalmente) in strategie di marketing più mirate, espansione del portfolio artisti, costruzione della tua reputazione.</p>
                        </div>
                    </div>
                </section>

                <!-- Sezione Modello Economico: Come Generare Valore Fin da Subito -->
                <section id="economico" class="section">
                    <h2>Il Modello Economico: Come Generare Valore Fin da Subito</h2>
                    <h3>Le Tue Entrate come Mecenate</h3>
                    <p style="text-align: center; margin-bottom: 30px;">Quando faciliti una vendita, sia attraverso la TUA rete di contatti che sfruttando quella di FlorenceEGI, il valore viene distribuito secondo lo schema seguente:</p>

                    <h4>Mercato Primario (Prima Vendita dell'Opera)</h4>
                    <table itemprop="offers" itemscope itemtype="https://schema.org/Table">
                        <thead>
                            <tr>
                                <th scope="col">Volume Globale</th>
                                <th scope="col">Creator + Mecenate</th>
                                <th scope="col">EPP</th>
                                <th scope="col">Piattaforma</th>
                                <th scope="col">Associazione</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>Fino a 100M €</td><td><strong>65%</strong> *</td><td>20%</td><td>10%</td><td>5%</td></tr>
                            <tr><td>100M - 200M €</td><td><strong>68%</strong> *</td><td>20%</td><td>8%</td><td>4%</td></tr>
                            <tr><td>201M - 400M €</td><td><strong>70%</strong> *</td><td>20%</td><td>7%</td><td>3%</td></tr>
                            <tr><td>401M - 1B €</td><td><strong>73%</strong> *</td><td>20%</td><td>5%</td><td>2%</td></tr>
                            <tr><td>Oltre 1B €</td><td><strong>77%</strong> *</td><td>20%</td><td>2.5%</td><td>0.5%</td></tr>
                        </tbody>
                    </table>
                    <p style="font-size: 0.9em; text-align: center;">* _Tu e l'artista definirete liberamente la suddivisione di questa percentuale. Tipicamente, la quota del Mecenate si attesta tra il 15% e il 30%._</p>

                    <h4>Mercato Secondario (Rivendite) - Rendita Passiva Perpetua</h4>
                    <table itemprop="offers" itemscope itemtype="https://schema.org/Table">
                        <thead>
                            <tr>
                                <th scope="col">Volume Globale</th>
                                <th scope="col">Creator + Mecenate</th>
                                <th scope="col">EPP</th>
                                <th scope="col">Piattaforma</th>
                                <th scope="col">Associazione</th>
                                <th scope="col">Totale</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>Fino a 100M €</td><td><strong>4.50%</strong> *</td><td>1%</td><td>1%</td><td>0.5%</td><td>7%</td></tr>
                            <tr><td>100M - 200M €</td><td><strong>4.50%</strong> *</td><td>1%</td><td>1%</td><td>0.5%</td><td>7%</td></tr>
                            <tr><td>201M - 400M €</td><td><strong>4.70%</strong> *</td><td>1%</td><td>0.9%</td><td>0.4%</td><td>7%</td></tr>
                            <tr><td>401M - 1B €</td><td><strong>5.00%</strong> *</td><td>1%</td><td>0.7%</td><td>0.3%</td><td>7%</td></tr>
                            <tr><td>Oltre 1B €</td><td><strong>5.20%</strong> *</td><td>1%</td><td>0.6%</td><td>0.2%</td><td>7%</td></tr>
                        </tbody>
                    </table>
                    <p style="font-size: 0.9em; text-align: center;">* _Anche in questo caso, la ripartizione è a discrezione tua e dell'artista. Le royalty sono automatiche e garantite a vita!_</p>
                </section>

                <!-- Sezione Storie di Successo -->
                <section id="storie" class="section">
                    <h2>Storie di Successo: Da Appassionato a Imprenditore dell'Arte Virtuosa</h2>
                    <p style="text-align: center;">Scopri come diverse persone hanno trasformato la loro passione in una professione gratificante, contribuendo attivamente al nostro Rinascimento Ecologico Digitale.</p>
                    <div class="stories-grid">
                        <div class="story-card">
                            <h4>Maria, 35 anni, Social Media Manager</h4>
                            <ul>
                                <li><strong>Contesto</strong>: Lavora part-time, ama l'arte.</li>
                                <li><strong>Investimento</strong>: 0€, solo 10 ore a settimana.</li>
                                <li><strong>Azione</strong>: Contatta 5 artisti locali, ne convince 3.</li>
                                <li><strong>Risultato Anno 1</strong>: 15.000€ di commissioni.</li>
                                <li><strong>Anno 2</strong>: Lascia il part-time, 40.000€ di ricavi.</li>
                            </ul>
                        </div>
                        <div class="story-card">
                            <h4>Giuseppe, 28 anni, Neolaureato in Economia</h4>
                            <ul>
                                <li><strong>Contesto</strong>: Economia, zero esperienza arte.</li>
                                <li><strong>Investimento</strong>: 0€ iniziali, poi 500€ in ads.</li>
                                <li><strong>Azione</strong>: Si specializza in arte digitale/gaming.</li>
                                <li><strong>Risultato Anno 1</strong>: 8.000€ + network nel gaming.</li>
                                <li><strong>Oggi</strong>: Mecenate di riferimento per crypto art.</li>
                            </ul>
                        </div>
                        <div class="story-card">
                            <h4>Anna, 45 anni, Insegnante</h4>
                            <ul>
                                <li><strong>Contesto</strong>: Conosce molti artisti amatoriali.</li>
                                <li><strong>Investimento</strong>: Solo tempo nel weekend.</li>
                                <li><strong>Azione</strong>: Organizza mostre virtuali su FlorenceEGI.</li>
                                <li><strong>Risultato</strong>: 12.000€ extra all'anno.</li>
                                <li><strong>Impatto</strong>: 50 alberi piantati grazie alle sue vendite.</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Sezione Strumenti e Supporto -->
                <section id="strumenti" class="section">
                    <h2>Gli Strumenti e il Supporto che FlorenceEGI ti Offre GRATUITAMENTE</h2>
                    <div class="feature-boxes">
                        <div class="feature-box">
                            <h3>Formazione Completa e Certificata</h3>
                            <ul>
                                <li>Video corso "Da Zero a Mecenate".</li>
                                <li>Workshop mensili con top performer.</li>
                                <li>Certificazione FlorenceEGI Mecenate.</li>
                                <li>Aggiornamenti continui sul mercato.</li>
                            </ul>
                        </div>
                        <div class="feature-box">
                            <h3>Kit Marketing Professionale</h3>
                            <ul>
                                <li>Template per presentazioni.</li>
                                <li>Materiali social media.</li>
                                <li>Email sequences testate.</li>
                                <li>Media kit per artisti.</li>
                            </ul>
                        </div>
                        <div class="feature-box">
                            <h3>Tecnologia All-Inclusive</h3>
                            <ul>
                                <li>Dashboard gestione artisti.</li>
                                <li>Analytics e reportistica.</li>
                                <li>AI per descrizioni opere.</li>
                                <li>Tool per virtual gallery.</li>
                            </ul>
                        </div>
                        <div class="feature-box">
                            <h3>Supporto e Comunità Dedicata</h3>
                            <ul>
                                <li>Gruppo privato Mecenati.</li>
                                <li>Mentorship dai senior.</li>
                                <li>Co-marketing opportunità.</li>
                                <li>Eventi networking.</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Sezione Diverse Sfaccettature del Ruolo di Mecenate -->
                <section id="sfaccettature" class="section">
                    <h2>Le Diverse Sfaccettature del Ruolo di Mecenate</h2>
                    <div class="feature-boxes">
                        <div class="feature-box">
                            <h3>Il Mecenate Locale</h3>
                            <ul>
                                <li>Focus su artisti del territorio.</li>
                                <li>Organizza eventi fisici + digitali.</li>
                                <li>Diventa punto di riferimento locale.</li>
                                <li><strong>Guadagno tipico</strong>: 20-30K€/anno.</li>
                            </ul>
                        </div>
                        <div class="feature-box">
                            <h3>Il Mecenate Digitale</h3>
                            <ul>
                                <li>Lavora 100% online.</li>
                                <li>Si specializza in nicchie (AI art, fotografia, etc).</li>
                                <li>Scala velocemente.</li>
                                <li><strong>Potenziale</strong>: 50-100K€/anno.</li>
                            </ul>
                        </div>
                        <div class="feature-box">
                            <h3>Il Mecenate Social</h3>
                            <ul>
                                <li>Usa Instagram/TikTok per promuovere.</li>
                                <li>Crea contenuti virali sull'arte.</li>
                                <li>Monetizza following.</li>
                                <li><strong>Mix</strong>: commissioni + sponsorship.</li>
                            </ul>
                        </div>
                        <div class="feature-box">
                            <h3>Il Mecenate Formatore (Educator)</h3>
                            <ul>
                                <li>Organizza workshop per artisti.</li>
                                <li>Crea contenuti educativi.</li>
                                <li>Percepito come esperto.</li>
                                <li><strong>Revenue</strong>: commissioni + formazione.</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Sezione Domande Frequenti (FAQ) con Accordion -->
                <section id="faq" class="section">
                    <h2>Domande Frequenti: Chiarimenti sul Tuo Percorso di Mecenate</h2>
                    <div class="accordion-container" role="presentation">
                        <div class="accordion-item">
                            <button class="accordion-header" aria-expanded="false" aria-controls="faq-content-1" id="faq-header-1" role="button">
                                <span>"Ma io non capisco nulla di blockchain e NFT..."</span>
                                <i class="fas fa-chevron-down icon" aria-hidden="true"></i>
                            </button>
                            <div class="accordion-content" id="faq-content-1" role="region" aria-labelledby="faq-header-1">
                                <p>Assolutamente no. Il 90% degli artisti, così come molti aspiranti Mecenati, non ha questa conoscenza approfondita. Il tuo ruolo è proprio quello di facilitare l'aspetto umano e relazionale. La complessità tecnologica è interamente gestita da FlorenceEGI.</p>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <button class="accordion-header" aria-expanded="false" aria-controls="faq-content-2" id="faq-header-2" role="button">
                                <span>"Non ho soldi da investire in marketing..."</span>
                                <i class="fas fa-chevron-down icon" aria-hidden="true"></i>
                            </button>
                            <div class="accordion-content" id="faq-content-2" role="region" aria-labelledby="faq-header-2">
                                <p>Non è richiesto alcun investimento iniziale. Potrai sfruttare il TUO network, i TUOI canali social e le strategie di marketing gratuito offerte da FlorenceEGI. I guadagni arriveranno una volta che le opere saranno vendute.</p>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <button class="accordion-header" aria-expanded="false" aria-controls="faq-content-3" id="faq-header-3" role="button">
                                <span>"Non conosco artisti..."</span>
                                <i class="fas fa-chevron-down icon" aria-hidden="true"></i>
                            </button>
                            <div class="accordion-content" id="faq-content-3" role="region" aria-labelledby="faq-header-3">
                                <p>Gli artisti sono ovunque: su Instagram, nei mercatini d'arte locali, nelle scuole d'arte, nelle associazioni culturali. Ti forniremo guide e script per contattarli e presentarli l'opportunità.</p>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <button class="accordion-header" aria-expanded="false" aria-controls="faq-content-4" id="faq-header-4" role="button">
                                <span>"E se l'artista poi non vuole più lavorare con me?"</span>
                                <i class="fas fa-chevron-down icon" aria-hidden="true"></i>
                            </button>
                            <div class="accordion-content" id="faq-content-4" role="region" aria-labelledby="faq-header-4">
                                <p>Gli accordi sono tutelati tramite blockchain. Le royalty che hai già maturato rimangono tue in modo permanente. Avrai inoltre acquisito un'esperienza preziosa per le future collaborazioni.</p>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <button class="accordion-header" aria-expanded="false" aria-controls="faq-content-5" id="faq-header-5" role="button">
                                <span>"Quanto tempo serve davvero?"</span>
                                <i class="fas fa-chevron-down icon" aria-hidden="true"></i>
                            </button>
                            <div class="accordion-content" id="faq-content-5" role="region" aria-labelledby="faq-header-5">
                                <p>La flessibilità è uno dei punti di forza. Alcuni Mecenati dedicano 5 ore settimanali e generano un reddito extra. Altri ne fanno una vera e propria carriera a tempo pieno. Dipende dalle tue ambizioni e dalla tua disponibilità.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Sezione Programma Pioneer -->
                <section id="pioneer" class="pioneer-program">
                    <h2>Il Programma Pioneer: Un Vantaggio Esclusivo per i Primi Mecenati</h2>
                    <p>Per coloro che abbracciano questa visione fin da subito, abbiamo riservato un programma con benefici unici.</p>
                    <h3>Vantaggi Esclusivi per i Primi 100 Mecenati:</h3>
                    <div class="pioneer-benefits-grid">
                        <div class="pioneer-benefit-item"><span class="icon" aria-hidden="true">🏆</span> Status "Pioneer Mecenate" - Badge permanente</div>
                        <div class="pioneer-benefit-item"><span class="icon" aria-hidden="true">📚</span> Formazione VIP - Accesso a materiali avanzati</div>
                        <div class="pioneer-benefit-item"><span class="icon" aria-hidden="true">🎯</span> Lead qualificate - Artisti interessati nella tua zona</div>
                        <div class="pioneer-benefit-item"><span class="icon" aria-hidden="true">💰</span> Bonus performance - +5% commissioni per 6 mesi</div>
                        <div class="pioneer-benefit-item"><span class="icon" aria-hidden="true">🛡️</span> Protezione territorio - Priorità nella tua area</div>
                        <div class="pioneer-benefit-item"><span class="icon" aria-hidden="true">🤝</span> Mentorship diretta - Dal team FlorenceEGI</div>
                    </div>
                    <h4>Requisiti per Accedere al Programma Pioneer:</h4>
                    <div class="pioneer-requirements">
                        <ul>
                            <li>Completare il training entro 30 giorni.</li>
                            <li>Attivare almeno 1 artista entro 60 giorni.</li>
                            <li>Partecipare alla community attivamente.</li>
                        </ul>
                    </div>
                </section>

                <!-- Sezione Inizia Oggi il Tuo Percorso -->
                <section id="inizia" class="section">
                    <h2>Inizia Oggi il Tuo Percorso nel Nuovo Rinascimento</h2>
                    <div class="onboarding-steps">
                        <div class="onboarding-step-card">
                            <span class="step-number">1</span>
                            <i class="fas fa-book-open step-icon" aria-hidden="true"></i>
                            <h4>Informati</h4>
                            <ul>
                                <li>Scarica la guida "Mecenate in 30 Giorni".</li>
                                <li>Guarda il webinar introduttivo.</li>
                                <li>Leggi le storie di successo.</li>
                            </ul>
                        </div>
                        <div class="onboarding-step-card">
                            <span class="step-number">2</span>
                            <i class="fas fa-user-plus step-icon" aria-hidden="true"></i>
                            <h4>Candidati</h4>
                            <ul>
                                <li>Compila il form di candidatura.</li>
                                <li>Racconta la tua motivazione.</li>
                                <li>Non serve CV o esperienza.</li>
                            </ul>
                        </div>
                        <div class="onboarding-step-card">
                            <span class="step-number">3</span>
                            <i class="fas fa-graduation-cap step-icon" aria-hidden="true"></i>
                            <h4>Formati</h4>
                            <ul>
                                <li>Accedi al corso online.</li>
                                <li>Partecipa al primo workshop.</li>
                                <li>Entra nella community.</li>
                            </ul>
                        </div>
                        <div class="onboarding-step-card">
                            <span class="step-number">4</span>
                            <i class="fas fa-rocket step-icon" aria-hidden="true"></i>
                            <h4>Lancia</h4>
                            <ul>
                                <li>Identifica i tuoi primi artisti.</li>
                                <li>Usa i tool forniti.</li>
                                <li>Inizia a guadagnare!</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Sezione Contatti e Risorse -->
                <section id="contatti" class="section">
                    <h2>Contatti e Risorse: Siamo Qui per Te</h2>
                    <div class="contact-resources">
                        <div class="contact-block">
                            <h3>Contatti</h3>
                            <ul>
                                <li><i class="fas fa-envelope icon" aria-hidden="true"></i> <a href="mailto:academy@florenceegi.com" itemprop="email">academy@florenceegi.com</a></li>
                                <li><i class="fab fa-whatsapp icon" aria-hidden="true"></i> <a href="https://wa.me/[numero]?text=MECENATE" itemprop="url">[numero] - Scrivi "MECENATE"</a></li>
                                <li><i class="fas fa-globe icon" aria-hidden="true"></i> <a href="https://www.florenceegi.com/diventa-mecenate" target="_blank" itemprop="url">www.florenceegi.com/diventa-mecenate</a></li>
                                <li><i class="fab fa-youtube icon" aria-hidden="true"></i> <a href="https://www.youtube.com/c/FlorenceEGIAcademy" target="_blank" itemprop="url">FlorenceEGI Academy</a></li>
                            </ul>
                        </div>
                        <div class="download-block">
                            <h3>Download Gratuiti:</h3>
                            <ul>
                                <li><i class="fas fa-book icon" aria-hidden="true"></i> eBook: "Da Zero a Mecenate"</li>
                                <li><i class="fas fa-file-alt icon" aria-hidden="true"></i> Template: "Proposta Artista"</li>
                                <li><i class="fas fa-video icon" aria-hidden="true"></i> Video: "Il Tuo Primo Artista"</li>
                                <li><i class="fas fa-clipboard-list icon" aria-hidden="true"></i> Checklist: "30 Giorni al Successo"</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Messaggio Personale e Slogan Finale -->
                <section class="personal-message">
                    <h2>Un Messaggio Personale: Il Tuo Ruolo nel Nuovo Rinascimento</h2>
                    <p>Non è necessario essere nati nel mondo dell'arte per diventarne parte attiva. I grandi Mecenati della storia erano spesso mercanti, banchieri, o semplici individui con una visione lungimirante.</p>
                    <p>Oggi, FlorenceEGI ti offre l'opportunità unica di scrivere la TUA storia nel mondo dell'arte. Senza investimenti iniziali, senza rischi, ma solo con la tua passione e il tuo impegno.</p>
                    <p>Ogni grande artista, ogni genio creativo, ha bisogno di qualcuno che creda nel suo potenziale. <strong>Quel qualcuno potresti essere tu.</strong></p>
                    <p>Il Nuovo Rinascimento ha origine da persone ordinarie che compiono scelte straordinarie.</p>
                    <p style="font-family: 'Playfair Display', serif; font-size: 1.8em; font-weight: 700; color: var(--oro-fiorentino);">La tua scelta inizia oggi.</p>
                    <p class="final-slogan">_FlorenceEGI - Dove chiunque può diventare Mecenate e trasformare l'arte in professione, impatto e soddisfazione._</p>
                    <p class="signature">Padmin D. Curtis</p>
                </section>
            </main>
        </div>
    </div>

    <!-- Footer della pagina (fuori dal main content per design) -->
    <footer role="contentinfo">
        <p>FlorenceEGI - Dove l'arte diventa valore virtuoso</p>
        <p>&copy; 2025 FlorenceEGI. Tutti i diritti riservati. <a href="#">Privacy Policy</a> | <a href="#">Termini di Servizio</a></p>
    </footer>

    <!-- Script JavaScript per la funzionalità dell'accordion nelle FAQ e dei menu hamburger -->
    <script>
        // Funzionalità per l'accordion delle FAQ
        document.querySelectorAll('.accordion-header').forEach(header => {
            header.addEventListener('click', () => {
                const content = header.nextElementSibling;
                const isExpanded = header.getAttribute('aria-expanded') === 'true' || false;
                header.setAttribute('aria-expanded', !isExpanded);

                if (content.classList.contains('open')) {
                    content.classList.remove('open');
                    content.style.maxHeight = null;
                    content.style.paddingBottom = '0';
                    header.classList.remove('active');
                } else {
                    content.classList.add('open');
                    content.style.maxHeight = content.scrollHeight + 'px';
                    content.style.paddingBottom = '25px';
                    header.classList.add('active');
                }
            });
        });

        // Funzionalità per il menu hamburger della sidebar
        const sidebarHamburger = document.getElementById('sidebar-hamburger');
        const sidebar = document.getElementById('sidebar');
        const sidebarMenu = document.getElementById('sidebar-menu');

        sidebarHamburger.addEventListener('click', () => {
            const isSidebarOpen = sidebar.classList.toggle('open');
            sidebarHamburger.setAttribute('aria-expanded', isSidebarOpen);
            if (isSidebarOpen) {
                document.body.style.overflow = 'hidden'; // Blocca lo scroll del body
            } else {
                document.body.style.overflow = '';
            }
        });

        // Funzionalità per il menu hamburger della top nav
        const topNavHamburger = document.getElementById('top-nav-hamburger');
        const topNavOverlay = document.getElementById('top-nav-overlay');

        topNavHamburger.addEventListener('click', () => {
            const isTopNavOpen = topNavOverlay.classList.toggle('open');
            topNavHamburger.setAttribute('aria-expanded', isTopNavOpen);
            if (isTopNavOpen) {
                document.body.style.overflow = 'hidden'; // Blocca lo scroll del body
            } else {
                document.body.style.overflow = '';
            }
        });

        // Chiudi la sidebar quando si clicca su un link della sidebar o fuori da essa (solo su mobile)
        document.addEventListener('click', (event) => {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnSidebarHamburger = sidebarHamburger.contains(event.target);
            const isClickInsideTopNav = topNavOverlay.contains(event.target);
            const isClickOnTopNavHamburger = topNavHamburger.contains(event.target);

            // Chiudi sidebar se aperta e click esterno
            if (!isClickInsideSidebar && !isClickOnSidebarHamburger && sidebar.classList.contains('open') && window.innerWidth <= 768) {
                sidebar.classList.remove('open');
                sidebarHamburger.setAttribute('aria-expanded', false);
                document.body.style.overflow = '';
            }
            // Chiudi top nav overlay se aperta e click esterno
            if (!isClickInsideTopNav && !isClickOnTopNavHamburger && topNavOverlay.classList.contains('open') && window.innerWidth <= 768) {
                topNavOverlay.classList.remove('open');
                topNavHamburger.setAttribute('aria-expanded', false);
                document.body.style.overflow = '';
            }
        });

        // Chiudi la sidebar e top nav quando si clicca su un link (per il routing interno)
        document.querySelectorAll('.sidebar-link, .top-nav-overlay a').forEach(link => {
            link.addEventListener('click', () => {
                // Chiudi sidebar se aperta e mobile
                if (sidebar.classList.contains('open') && window.innerWidth <= 768) {
                    sidebar.classList.remove('open');
                    sidebarHamburger.setAttribute('aria-expanded', false);
                    document.body.style.overflow = '';
                }
                // Chiudi top nav se aperta e mobile
                if (topNavOverlay.classList.contains('open') && window.innerWidth <= 768) {
                    topNavOverlay.classList.remove('open');
                    topNavHamburger.setAttribute('aria-expanded', false);
                    document.body.style.overflow = '';
                }
            });
        });

        // Scroll liscio per i link interni
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();

                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);

                if (targetElement) {
                    // Calcola lo scroll offset (escludendo l'altezza di eventuali header/navbar fisse)
                    // Non applichiamo offset fissi qui, ma lasciamo che il browser gestisca scrollIntoView
                    // che di solito è sufficiente per elementi non coperti da barre fisse con position: fixed
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Gestione active link nella sidebar quando si scorre
        const sections = document.querySelectorAll('.section[id]');
        const sidebarLinks = document.querySelectorAll('.sidebar-link');

        window.addEventListener('scroll', () => {
            let current = '';
            // Determine the current section based on scroll position
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                // Adjusting the offset for better active link detection (e.g., 1/3 of the screen height)
                const offset = window.innerHeight * 0.33;
                if (pageYOffset >= sectionTop - offset) {
                    current = section.getAttribute('id');
                }
            });

            // Update active class for sidebar links
            sidebarLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').includes(current)) {
                    link.classList.add('active');
                }
            });
        });

        // Imposta il link attivo all'avvio (per il caricamento della pagina su una sezione specifica o su intro)
        document.addEventListener('DOMContentLoaded', () => {
            const hash = window.location.hash;
            if (hash) {
                const targetElement = document.querySelector(hash);
                if (targetElement) {
                    // Scroll into view behavior is handled by the general smooth scroll, just set active link
                    sidebarLinks.forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === hash) {
                            link.classList.add('active');
                        }
                    });
                }
            } else {
                // Attiva il link 'Introduzione' se non c'è hash all'avvio
                const firstLink = document.querySelector('.sidebar-link[href="#intro"]');
                if (firstLink) {
                    firstLink.classList.add('active');
                }
            }
        });
    </script>

</body>
</html>
