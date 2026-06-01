<?php
session_start();
require 'config/connexion.php';

// Protection : réservé aux professeurs
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'professeur') {
    header("Location: login.php"); exit;
}

$uid = $_SESSION['user_id'];
$nom = $_SESSION['user_nom'];

// Statistiques
$stmt = $pdo->prepare("SELECT COUNT(*) FROM memoires WHERE professeur_id = ? AND statut = 'soumis'");
$stmt->execute([$uid]); $a_evaluer = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM memoires WHERE professeur_id = ? AND statut = 'valide'");
$stmt->execute([$uid]); $valides = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM memoires WHERE professeur_id = ? AND statut = 'en_retour'");
$stmt->execute([$uid]); $en_retour = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE utilisateur_id = ? AND lu = 0");
$stmt->execute([$uid]); $total_notifs = $stmt->fetchColumn();

// Liste des mémoires à évaluer
$stmt = $pdo->prepare("
    SELECT m.id, m.titre, m.date_soumission, m.statut,
           f.nom AS filiere,
           u.nom AS etudiant_nom, u.prenom AS etudiant_prenom
    FROM memoires m
    LEFT JOIN filieres f    ON m.filiere_id   = f.id
    LEFT JOIN utilisateurs u ON m.etudiant_id = u.id
    WHERE m.professeur_id = ? AND m.statut = 'soumis'
    ORDER BY m.date_soumission DESC
    LIMIT 10
");
$stmt->execute([$uid]);
$liste_memoires = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tableau de bord – Professeur | UATM GASA</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    :root{--navy:#1a2b5e;--accent:#4f75ff;--accent-2:#f59e0b;--green:#16a34a;--red:#dc2626;--bg:#f4f6fb;--card:#fff;--text:#1e293b;--muted:#64748b;--border:#e2e8f0;--sidebar-w:260px;--radius:14px}
    body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex}
    .sidebar{width:var(--sidebar-w);background:var(--navy);min-height:100vh;display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:100}
    .sidebar-brand{padding:20px 20px 18px;border-bottom:1px solid rgba(255,255,255,.08);display:flex;align-items:center;gap:12px}
    .brand-logo{width:52px;height:52px;border-radius:10px;overflow:hidden;background:#fff;padding:2px;flex-shrink:0}
    .brand-logo img{width:100%;height:100%;object-fit:contain;border-radius:8px}
    .brand-name{font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:13.5px;color:#fff;line-height:1.2}
    .brand-sub{font-size:10px;color:rgba(255,255,255,.5);letter-spacing:.06em;text-transform:uppercase;margin-top:2px}
    .sidebar-nav{flex:1;padding:18px 12px;display:flex;flex-direction:column;gap:2px}
    .nav-item{display:flex;align-items:center;gap:11px;padding:10px 14px;border-radius:10px;cursor:pointer;color:rgba(255,255,255,.65);font-size:14px;font-weight:500;transition:background .18s,color .18s;text-decoration:none;user-select:none}
    .nav-item:hover{background:rgba(255,255,255,.08);color:#fff}
    .nav-item.active{background:rgba(79,117,255,.22);color:#fff}
    .nav-icon{width:20px;height:20px;flex-shrink:0}
    .nav-badge{margin-left:auto;background:var(--red);color:#fff;font-size:11px;font-weight:700;border-radius:20px;padding:1px 7px}
    .sidebar-footer{padding:12px;border-top:1px solid rgba(255,255,255,.08)}
    .nav-item.logout{color:rgba(255,255,255,.45)}
    .nav-item.logout:hover{background:rgba(220,38,38,.15);color:#fca5a5}
    .main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column}
    .topbar{height:64px;background:var(--card);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 28px;gap:16px;position:sticky;top:0;z-index:50}
    .topbar-menu-btn{background:none;border:none;cursor:pointer;color:var(--muted)}
    .topbar-title{font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;font-size:16px;color:var(--text)}
    .topbar-right{margin-left:auto;display:flex;align-items:center;gap:12px}
    .topbar-notif{position:relative;width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:50%;border:1px solid var(--border);color:var(--muted);cursor:pointer}
    .notif-dot{position:absolute;top:3px;right:3px;background:var(--red);color:#fff;font-size:9px;font-weight:700;border-radius:10px;padding:1px 4px;line-height:12px}
    .topbar-avatar{width:38px;height:38px;border-radius:50%;background:var(--border);overflow:hidden;border:2px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--muted);flex-shrink:0}
    .topbar-avatar img{width:100%;height:100%;object-fit:cover}
    .user-name{font-weight:600;font-size:14px;color:var(--text);line-height:1.2}
    .user-role{font-size:12px;color:var(--muted)}
    .content{padding:32px 28px;flex:1}
    .page-greeting{font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin-bottom:24px}
    .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:32px}
    .stat-card{background:var(--card);border-radius:var(--radius);padding:20px 22px 18px;border:1px solid var(--border);animation:fadeUp .4s ease both}
    .stat-card:nth-child(1){animation-delay:.05s}.stat-card:nth-child(2){animation-delay:.10s}.stat-card:nth-child(3){animation-delay:.15s}.stat-card:nth-child(4){animation-delay:.20s}
    @keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
    .stat-card.blue{background:#eff3ff;border-color:#c7d4fd}.stat-card.green{background:#f0fdf4;border-color:#bbf7d0}.stat-card.orange{background:#fff7ed;border-color:#fed7aa}.stat-card.red{background:#fff1f2;border-color:#fecdd3}
    .stat-header{display:flex;align-items:center;gap:10px;margin-bottom:12px}
    .stat-label{font-size:13px;font-weight:600}
    .stat-card.blue .stat-label{color:var(--accent)}.stat-card.green .stat-label{color:var(--green)}.stat-card.orange .stat-label{color:var(--accent-2)}.stat-card.red .stat-label{color:var(--red)}
    .stat-value{font-family:'Plus Jakarta Sans',sans-serif;font-size:36px;font-weight:800;line-height:1}
    .stat-card.blue .stat-value{color:var(--accent)}.stat-card.green .stat-value{color:var(--green)}.stat-card.orange .stat-value{color:var(--accent-2)}.stat-card.red .stat-value{color:var(--red)}
    .section-title{font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;font-size:16px;color:var(--text);margin-bottom:14px}
    .table-card{background:var(--card);border-radius:var(--radius);border:1px solid var(--border);overflow:hidden;animation:fadeUp .4s .25s ease both}
    table{width:100%;border-collapse:collapse}
    thead tr{background:#f8fafc}
    th{text-align:left;padding:12px 16px;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid var(--border)}
    td{padding:14px 16px;font-size:13.5px;border-bottom:1px solid var(--border);vertical-align:middle}
    tr:last-child td{border-bottom:none}
    tr:hover td{background:#f8fafc}
    .pdf-icon{width:34px;height:34px;background:#fee2e2;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .memoire-cell{display:flex;align-items:center;gap:10px}
    .memoire-name{font-weight:600;font-size:13.5px;color:var(--text)}
    .badge-evaluer{background:#dbeafe;color:#1d4ed8;font-size:12px;font-weight:600;padding:4px 10px;border-radius:20px;white-space:nowrap}
    .btn-evaluer{display:inline-flex;align-items:center;gap:6px;background:var(--navy);color:#fff;font-size:13px;font-weight:600;padding:7px 14px;border-radius:8px;border:none;cursor:pointer;transition:background .18s;white-space:nowrap;text-decoration:none}
    .btn-evaluer:hover{background:#243570}
    .voir-tous{display:flex;align-items:center;justify-content:center;gap:8px;padding:16px;border-top:1px solid var(--border);color:var(--accent);font-weight:600;font-size:14px;cursor:pointer;transition:background .15s;text-decoration:none}
    .voir-tous:hover{background:#f0f4ff}
    .empty-state{padding:32px;text-align:center;color:var(--muted);font-size:14px}
    svg{display:block}
  </style>
</head>
<body>
<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="brand-logo"><img src="assets/logo.jpg" alt="Logo"/></div>
    <div><div class="brand-name">UATM GASA FORMATION</div><div class="brand-sub">Gestion des mémoires</div></div>
  </div>
  <nav class="sidebar-nav">
    <a class="nav-item active" href="dashboard_professeur.php">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
      Tableau de bord
    </a>
    <a class="nav-item" href="memoires_evaluer.php">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      Mémoires à évaluer
    </a>
    <a class="nav-item" href="memoires_valides.php">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      Mémoires validés
    </a>
    <a class="nav-item" href="en_retour.php">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.95"/></svg>
      En retour
    </a>
    <a class="nav-item" href="notifications.php">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
      Notifications
      <?php if ($total_notifs > 0): ?><span class="nav-badge"><?= $total_notifs ?></span><?php endif; ?>
    </a>
    <a class="nav-item" href="profil.php">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Profil
    </a>
  </nav>
  <div class="sidebar-footer">
    <a class="nav-item logout" href="logout.php">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      Déconnexion
    </a>
  </div>
</aside>

<div class="main">
  <header class="topbar">
    <button class="topbar-menu-btn">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <span class="topbar-title">Tableau de bord - Professeur / Maître de mémoire</span>
    <div class="topbar-right">
      <a href="notifications.php" class="topbar-notif">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        <?php if ($total_notifs > 0): ?><span class="notif-dot"><?= $total_notifs ?></span><?php endif; ?>
      </a>
      <div class="topbar-avatar">
        <?php if ($_SESSION['user_photo']): ?>
          <img src="assets/photos/<?= htmlspecialchars($_SESSION['user_photo']) ?>" alt="photo"/>
        <?php else: ?>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <?php endif; ?>
      </div>
      <div>
        <div class="user-name">Pr. <?= htmlspecialchars($nom) ?></div>
        <div class="user-role">Maître de mémoire</div>
      </div>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
    </div>
  </header>

  <main class="content">
    <h1 class="page-greeting">Bienvenue Pr. <?= htmlspecialchars($nom) ?> 👋</h1>

    <div class="stats-grid">
      <div class="stat-card blue">
        <div class="stat-header">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4f75ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
          <span class="stat-label">À évaluer</span>
        </div>
        <div class="stat-value"><?= $a_evaluer ?></div>
      </div>
      <div class="stat-card green">
        <div class="stat-header">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="20 6 9 17 4 12"/></svg>
          <span class="stat-label">Validés</span>
        </div>
        <div class="stat-value"><?= $valides ?></div>
      </div>
      <div class="stat-card orange">
        <div class="stat-header">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.95"/></svg>
          <span class="stat-label">En retour</span>
        </div>
        <div class="stat-value"><?= $en_retour ?></div>
      </div>
      <div class="stat-card red">
        <div class="stat-header">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          <span class="stat-label">Notifications</span>
        </div>
        <div class="stat-value"><?= $total_notifs ?></div>
      </div>
    </div>

    <p class="section-title">Mémoires à évaluer</p>
    <div class="table-card">
      <?php if (empty($liste_memoires)): ?>
        <div class="empty-state">✅ Aucun mémoire en attente d'évaluation.</div>
      <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Mémoire</th>
            <th>Étudiant</th>
            <th>Date de soumission</th>
            <th>Catégorie</th>
            <th>Statut</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($liste_memoires as $m): ?>
          <tr>
            <td>
              <div class="memoire-cell">
                <div class="pdf-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <span class="memoire-name"><?= htmlspecialchars($m['titre']) ?></span>
              </div>
            </td>
            <td><?= htmlspecialchars($m['etudiant_prenom'] . ' ' . $m['etudiant_nom']) ?></td>
            <td><?= $m['date_soumission'] ? date('d/m/Y \à H:i', strtotime($m['date_soumission'])) : '—' ?></td>
            <td><?= htmlspecialchars($m['filiere'] ?? '—') ?></td>
            <td><span class="badge-evaluer">À évaluer</span></td>
            <td>
              <a href="evaluer_memoire.php?id=<?= $m['id'] ?>" class="btn-evaluer">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                Évaluer
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
      <a class="voir-tous" href="memoires_evaluer.php">
        Voir tous les mémoires
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </a>
    </div>
  </main>
</div>
</body>
</html>
