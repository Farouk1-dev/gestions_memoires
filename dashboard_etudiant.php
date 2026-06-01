<?php
session_start();
require 'config/connexion.php';

// Protection : réservé aux étudiants
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'etudiant') {
    header("Location: login.php"); exit;
}

$uid = $_SESSION['user_id'];
$nom = $_SESSION['user_nom'];

// Statistiques
$stmt = $pdo->prepare("SELECT COUNT(*) FROM memoires WHERE etudiant_id = ?");
$stmt->execute([$uid]); $total_memoires = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM memoires WHERE etudiant_id = ? AND statut NOT IN ('brouillon')");
$stmt->execute([$uid]); $total_soumissions = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM memoires WHERE etudiant_id = ? AND statut = 'valide'");
$stmt->execute([$uid]); $total_valides = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE utilisateur_id = ? AND lu = 0");
$stmt->execute([$uid]); $total_notifs = $stmt->fetchColumn();

// Mémoires récents
$stmt = $pdo->prepare("
    SELECT m.id, m.titre, m.statut, m.date_creation, f.nom AS filiere
    FROM memoires m
    LEFT JOIN filieres f ON m.filiere_id = f.id
    WHERE m.etudiant_id = ?
    ORDER BY m.date_creation DESC
    LIMIT 3
");
$stmt->execute([$uid]);
$memoires = $stmt->fetchAll();

// Badge couleur selon statut
function badgeEtudiant($statut) {
    switch ($statut) {
        case 'soumis':     return ['label'=>'Soumis',     'class'=>'badge-blue'];
        case 'en_attente': return ['label'=>'En attente', 'class'=>'badge-orange'];
        case 'valide':     return ['label'=>'Validé',     'class'=>'badge-green'];
        case 'rejete':     return ['label'=>'Rejeté',     'class'=>'badge-red'];
        case 'en_retour':  return ['label'=>'En retour',  'class'=>'badge-orange'];
        default:           return ['label'=>'Brouillon',  'class'=>'badge-gray'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tableau de bord – Étudiant | UATM GASA</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    :root{--navy:#1a2b5e;--accent:#4f75ff;--accent-2:#f59e0b;--green:#16a34a;--red:#dc2626;--bg:#f4f6fb;--card:#fff;--text:#1e293b;--muted:#64748b;--border:#e2e8f0;--sidebar-w:260px;--radius:14px}
    body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex}
    /* SIDEBAR */
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
    /* MAIN */
    .main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column}
    .topbar{height:64px;background:var(--card);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 28px;gap:16px;position:sticky;top:0;z-index:50}
    .topbar-menu-btn{background:none;border:none;cursor:pointer;color:var(--muted)}
    .topbar-title{font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;font-size:17px;color:var(--text)}
    .topbar-right{margin-left:auto;display:flex;align-items:center;gap:8px}
    .topbar-avatar{width:36px;height:36px;border-radius:50%;background:var(--border);display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:18px;overflow:hidden}
    .topbar-avatar img{width:100%;height:100%;object-fit:cover}
    .user-name{font-weight:600;font-size:14px;color:var(--text);line-height:1.2}
    .user-role{font-size:12px;color:var(--muted)}
    .content{padding:32px 28px;flex:1}
    .page-greeting{font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin-bottom:24px}
    /* STATS */
    .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:32px}
    .stat-card{background:var(--card);border-radius:var(--radius);padding:20px 22px 18px;border:1px solid var(--border);animation:fadeUp .4s ease both}
    .stat-card:nth-child(1){animation-delay:.05s}.stat-card:nth-child(2){animation-delay:.10s}.stat-card:nth-child(3){animation-delay:.15s}.stat-card:nth-child(4){animation-delay:.20s}
    @keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
    .stat-card.blue{background:#eff3ff;border-color:#c7d4fd}.stat-card.orange{background:#fff7ed;border-color:#fed7aa}.stat-card.green{background:#f0fdf4;border-color:#bbf7d0}.stat-card.red{background:#fff1f2;border-color:#fecdd3}
    .stat-header{display:flex;align-items:center;gap:10px;margin-bottom:12px}
    .stat-label{font-size:13px;font-weight:600}
    .stat-card.blue .stat-label{color:var(--accent)}.stat-card.orange .stat-label{color:var(--accent-2)}.stat-card.green .stat-label{color:var(--green)}.stat-card.red .stat-label{color:var(--red)}
    .stat-value{font-family:'Plus Jakarta Sans',sans-serif;font-size:36px;font-weight:800;line-height:1}
    .stat-card.blue .stat-value{color:var(--accent)}.stat-card.orange .stat-value{color:var(--accent-2)}.stat-card.green .stat-value{color:var(--green)}.stat-card.red .stat-value{color:var(--red)}
    /* LISTE */
    .section-title{font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;font-size:16px;color:var(--text);margin-bottom:14px}
    .memoires-list{background:var(--card);border-radius:var(--radius);border:1px solid var(--border);overflow:hidden;animation:fadeUp .4s .25s ease both}
    .memoire-item{display:flex;align-items:center;gap:16px;padding:18px 20px;border-bottom:1px solid var(--border);transition:background .15s}
    .memoire-item:last-of-type{border-bottom:none}
    .memoire-item:hover{background:#f8fafc}
    .memoire-icon{width:38px;height:38px;background:var(--bg);border-radius:9px;display:flex;align-items:center;justify-content:center;color:var(--muted);flex-shrink:0}
    .memoire-info{flex:1;min-width:0}
    .memoire-title{font-weight:600;font-size:14.5px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .memoire-meta{font-size:12px;color:var(--muted);margin-top:3px}
    .badge{font-size:12px;font-weight:600;padding:4px 12px;border-radius:20px;flex-shrink:0}
    .badge-blue{background:#dbeafe;color:#1d4ed8}.badge-orange{background:#ffedd5;color:#c2410c}.badge-green{background:#dcfce7;color:#15803d}.badge-red{background:#fee2e2;color:#991b1b}.badge-gray{background:#e2e8f0;color:#475569}
    .voir-tous{display:flex;align-items:center;justify-content:center;gap:8px;padding:15px;border-top:1px solid var(--border);color:var(--accent);font-weight:600;font-size:14px;cursor:pointer;transition:background .15s;text-decoration:none}
    .voir-tous:hover{background:#f0f4ff}
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
    <a class="nav-item active" href="dashboard_etudiant.php">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
      Tableau de bord
    </a>
    <a class="nav-item" href="mes_memoires.php">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      Mes mémoires
    </a>
    <a class="nav-item" href="nouveau_memoire.php">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
      Nouveau mémoire
    </a>
    <a class="nav-item" href="mes_soumissions.php">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
      Mes soumissions
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
    <span class="topbar-title">Tableau de bord - Étudiant</span>
    <div class="topbar-right">
      <div class="topbar-avatar">
        <?php if ($_SESSION['user_photo']): ?>
          <img src="assets/photos/<?= htmlspecialchars($_SESSION['user_photo']) ?>" alt="photo"/>
        <?php else: ?>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <?php endif; ?>
      </div>
      <div>
        <div class="user-name"><?= htmlspecialchars($nom) ?></div>
        <div class="user-role">Étudiant</div>
      </div>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
    </div>
  </header>

  <main class="content">
    <h1 class="page-greeting">Bienvenue <?= htmlspecialchars($nom) ?> 👋</h1>

    <div class="stats-grid">
      <div class="stat-card blue">
        <div class="stat-header">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4f75ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          <span class="stat-label">Mes mémoires</span>
        </div>
        <div class="stat-value"><?= $total_memoires ?></div>
      </div>
      <div class="stat-card orange">
        <div class="stat-header">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          <span class="stat-label">Soumissions</span>
        </div>
        <div class="stat-value"><?= $total_soumissions ?></div>
      </div>
      <div class="stat-card green">
        <div class="stat-header">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          <span class="stat-label">Mémoires validés</span>
        </div>
        <div class="stat-value"><?= $total_valides ?></div>
      </div>
      <div class="stat-card red">
        <div class="stat-header">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          <span class="stat-label">Notifications</span>
        </div>
        <div class="stat-value"><?= $total_notifs ?></div>
      </div>
    </div>

    <p class="section-title">Mes mémoires récents</p>
    <div class="memoires-list">
      <?php if (empty($memoires)): ?>
        <div style="padding:24px;text-align:center;color:#64748b;font-size:14px;">Aucun mémoire pour l'instant. <a href="nouveau_memoire.php" style="color:#4f75ff;font-weight:600;">Créer un mémoire</a></div>
      <?php else: ?>
        <?php foreach ($memoires as $m): $badge = badgeEtudiant($m['statut']); ?>
        <div class="memoire-item">
          <div class="memoire-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          </div>
          <div class="memoire-info">
            <div class="memoire-title"><?= htmlspecialchars($m['titre']) ?></div>
            <div class="memoire-meta"><?= date('d/m/Y', strtotime($m['date_creation'])) ?> &nbsp;•&nbsp; <?= htmlspecialchars($m['filiere'] ?? 'Non défini') ?></div>
          </div>
          <span class="badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
      <a class="voir-tous" href="mes_memoires.php">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
        Voir tous mes mémoires
      </a>
    </div>
  </main>
</div>
</body>
</html>
