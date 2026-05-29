<?php
session_start();
$db=new PDO('mysql:host=127.0.0.1;dbname=uatm_gasa','root','');
 
// TEMPORAIRE : pour tester sans passer par login.php (supprimer en production)
if(!isset($_SESSION['user'])){
	$_SESSION['user']=['id'=>1,'nom'=>'Jean DUPONT','role'=>'Étudiant'];
}
$user=$_SESSION['user'];
$user['id']   = $user['id']   ?? 0;
$user['nom']  = $user['nom']  ?? 'Inconnu';
$user['role'] = $user['role'] ?? 'Étudiant';
 
$success=''; $error='';
 
if(isset($_POST['btn']) && $_POST['btn']=='upload'){
	$titre=$_POST['titre']; $categorie=$_POST['categorie'];
	$filiere=$_POST['filiere']; $resume=$_POST['resume'];
	$file=$_FILES['fichier'];
 
	if(!$titre||!$categorie||!$filiere||!$resume){
		$error="Veuillez remplir tous les champs obligatoires.";
	}elseif($file['error']!=0){
		$error="Veuillez sélectionner un fichier PDF.";
	}elseif($file['type']!='application/pdf'){
		$error="Seuls les fichiers PDF sont acceptés.";
	}elseif($file['size']>20*1024*1024){
		$error="Le fichier ne doit pas dépasser 20 Mo.";
	}else{
		$dir=__DIR__.'/uploads/memoires/';
		if(!is_dir($dir)) mkdir($dir,0755,true);
		$filename=uniqid('mem_').'_'.basename($file['name']);
		if(move_uploaded_file($file['tmp_name'],$dir.$filename)){
			$db->query('INSERT INTO memoires(titre,categorie,filiere_departement,resume,statut,etudiant_id,date_depot) VALUES("'.$titre.'","'.$categorie.'","'.$filiere.'","'.$resume.'","en_attente","'.$user['id'].'",NOW())');
			$mid=$db->lastInsertId();
			$db->query('INSERT INTO fichiers_memoires(memoire_id,nom_fichier,chemin_fichier,taille_octets,date_upload) VALUES("'.$mid.'","'.$file['name'].'","'.$filename.'","'.$file['size'].'",NOW())');
			$success="Votre mémoire a été uploadé avec succès !";
		}else{
			$error="Erreur lors de l'enregistrement du fichier.";
		}
	}
}
 
// Notifications (mettre 0 si la table n'existe pas encore)
$notifs=0;
try{
	$req=$db->prepare('SELECT COUNT(*) FROM notifications WHERE utilisateur_id=? AND lu=0');
	$req->execute([$user['id']??0]);
	$notifs=$req->fetchColumn();
}catch(PDOException $e){ $notifs=0; }
 
$categories=['Informatique','Gestion','Droit','Sciences','Lettres','Économie','Ingénierie'];
$filieres=['Licence Informatique','Master Informatique','Licence Gestion','Master Gestion','Licence Droit','Master Droit','Licence Sciences','Master Sciences','Autre'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>UATM GASA – Uploader un mémoire</title>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{--navy:#0d3b9e;--dark:#0a2a6e;--light:#1a4fc4;--bg:#f4f6fb;--white:#fff;--border:#dde2ee;--text:#1a1a2e;--muted:#6b7280;}
body{font-family:'Source Sans 3',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;}
/* SIDEBAR */
.sidebar{width:260px;background:var(--white);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:100;}
.brand{background:linear-gradient(135deg,var(--dark),var(--light));padding:10px 16px;display:flex;align-items:center;gap:10px;color:#fff;min-height:64px;}
.brand img{width:44px;height:44px;object-fit:contain;flex-shrink:0;}
.brand h2{font-size:12.5px;font-weight:700;letter-spacing:.4px;line-height:1.25;}
.brand p{font-size:9.5px;color:rgba(255,255,255,.7);letter-spacing:.5px;margin-top:2px;}
.snav{flex:1;padding:16px 0;overflow-y:auto;}
.snav a{display:flex;align-items:center;gap:12px;padding:11px 20px;color:var(--muted);text-decoration:none;font-size:14.5px;font-weight:500;transition:background .15s,color .15s;position:relative;}
.snav a:hover{background:#f0f4ff;color:var(--navy);}
.snav a.active{background:#eef2fb;color:var(--navy);font-weight:600;}
.snav a.active::before{content:'';position:absolute;left:0;top:8px;bottom:8px;width:3px;background:var(--navy);border-radius:0 3px 3px 0;}
.ico{width:20px;text-align:center;font-size:17px;flex-shrink:0;}
.badge{margin-left:auto;background:#e53e3e;color:#fff;font-size:11px;font-weight:700;padding:1px 7px;border-radius:20px;}
.sbot{padding:16px 0;border-top:1px solid var(--border);}
/* MAIN */
.main{margin-left:260px;flex:1;display:flex;flex-direction:column;min-height:100vh;}
.topbar{height:64px;background:var(--white);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 28px;gap:16px;position:sticky;top:0;z-index:50;}
.topbar .title{font-size:19px;font-weight:600;color:var(--text);flex:1;}
.topbar .right{display:flex;align-items:center;gap:16px;}
.notif{position:relative;background:none;border:none;cursor:pointer;font-size:22px;color:var(--muted);}
.ndot{position:absolute;top:-4px;right:-4px;background:#e53e3e;color:#fff;font-size:10px;font-weight:700;width:17px;height:17px;border-radius:50%;display:flex;align-items:center;justify-content:center;}
.uchip{display:flex;align-items:center;gap:10px;cursor:pointer;}
.uav{width:36px;height:36px;background:var(--bg);border:1.5px solid var(--border);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;}
.uname{font-size:13.5px;font-weight:600;color:var(--text);}
.urole{font-size:12px;color:var(--navy);}
/* CONTENT */
.content{padding:32px 36px;flex:1;}
.ptitle{font-size:24px;font-weight:700;color:var(--text);margin-bottom:24px;}
.card{background:var(--white);border:1px solid var(--border);border-radius:14px;overflow:hidden;}
.infobanner{display:flex;align-items:flex-start;gap:12px;background:#eef4ff;border-bottom:1px solid #c8d8f8;padding:16px 24px;}
.infobanner strong{font-size:14px;color:var(--navy);display:block;margin-bottom:2px;}
.infobanner p{font-size:13.5px;color:#4a5a80;}
.fbody{padding:28px 28px 8px;}
.ok{background:#e6f9f0;border:1px solid #a3e3c0;color:#1a7a4a;padding:12px 16px;border-radius:8px;font-size:14px;margin-bottom:20px;}
.ko{background:#fdecea;border:1px solid #f5c6c6;color:#c0392b;padding:12px 16px;border-radius:8px;font-size:14px;margin-bottom:20px;}
.row{display:grid;grid-template-columns:190px 1fr;align-items:start;padding:14px 0;border-bottom:1px solid #f0f2f8;gap:16px;}
.row:last-child{border-bottom:none;}
.lbl{font-size:14px;font-weight:600;color:var(--text);padding-top:10px;}
.lbl span{color:#e53e3e;margin-left:2px;}
.fc{width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:14.5px;font-family:inherit;color:var(--text);background:var(--white);outline:none;transition:border-color .2s;}
.fc:focus{border-color:var(--navy);}
.fc::placeholder{color:#bbc;}
textarea.fc{resize:vertical;min-height:100px;}
.cc{text-align:right;font-size:12px;color:var(--muted);margin-top:4px;}
.dz{border:2px dashed #b0bfe8;border-radius:10px;background:#f7f9ff;padding:40px 20px;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;}
.dz:hover,.dz.over{border-color:var(--navy);background:#eef2fb;}
.dz .uico{font-size:38px;color:var(--navy);margin-bottom:10px;}
.dz p{font-size:14px;color:var(--muted);margin-bottom:12px;}
.dz .or{font-size:13px;color:#bbb;margin-bottom:12px;}
.brow{background:var(--navy);color:#fff;border:none;border-radius:8px;padding:10px 24px;font-size:14.5px;font-weight:600;cursor:pointer;font-family:inherit;}
.brow:hover{background:var(--dark);}
.fmeta{margin-top:10px;font-size:12.5px;color:var(--muted);}
.fmeta span{color:#e53e3e;font-weight:600;}.fmeta b{color:#1a7a4a;font-weight:600;}
#fi{display:none;}
.sf{margin-top:10px;font-size:13px;color:#1a7a4a;font-weight:600;}
.footer{display:flex;justify-content:flex-end;gap:12px;padding:20px 28px 28px;border-top:1px solid #f0f2f8;}
.bcan{padding:11px 28px;border:1.5px solid var(--border);border-radius:8px;background:var(--white);font-size:14.5px;font-weight:600;color:var(--muted);cursor:pointer;font-family:inherit;}
.bcan:hover{background:var(--bg);}
.bupl{padding:11px 28px;background:var(--navy);color:#fff;border:none;border-radius:8px;font-size:14.5px;font-weight:600;cursor:pointer;font-family:inherit;}
.bupl:hover{background:var(--dark);}
</style>
</head>
<body>
 
<aside class="sidebar">
	<div class="brand">
		<img src="logo.png" alt="UATM Logo">
		<div><h2>UATM GASA FORMATION</h2><p>GESTION DES MÉMOIRES</p></div>
	</div>
	<nav class="snav">
		<a href="dashboard.php"><span class="ico">🏠</span> Tableau de bord</a>
		<a href="upload_memoire.php" class="active"><span class="ico">⬆️</span> Uploader un mémoire</a>
		<a href="upload_multiple.php"><span class="ico">📤</span> Uploader plusieurs mémoires</a>
		<a href="commentaires.php"><span class="ico">💬</span> Commentaires</a>
		<a href="likes.php"><span class="ico">👍</span> J'aime (Likes)</a>
		<a href="soumettre.php"><span class="ico">📨</span> Soumettre un mémoire</a>
		<a href="notifications.php"><span class="ico">🔔</span> Notifications <?php if($notifs>0) echo '<span class="badge">'.$notifs.'</span>'; ?></a>
		<a href="profil.php"><span class="ico">👤</span> Profil</a>
	</nav>
	<div class="sbot">
		<a href="logout.php" class="snav" style="display:flex;align-items:center;gap:12px;padding:11px 20px;color:var(--muted);text-decoration:none;font-size:14.5px;">
			<span class="ico">🚪</span> Déconnexion
		</a>
	</div>
</aside>
 
<div class="main">
	<header class="topbar">
		<span style="font-size:22px;color:var(--muted);cursor:pointer;">☰</span>
		<span class="title">Uploader un mémoire</span>
		<div class="right">
			<button class="notif">🔔 <?php if($notifs>0) echo '<span class="ndot">'.$notifs.'</span>'; ?></button>
			<div class="uchip">
				<div style="text-align:right;"><div class="uname"><?=htmlspecialchars($user['nom'])?></div><div class="urole"><?=htmlspecialchars($user['role'])?></div></div>
				<div class="uav">👤</div>
				<span style="font-size:13px;color:var(--muted);">▼</span>
			</div>
		</div>
	</header>
 
	<main class="content">
		<h1 class="ptitle">Uploader un mémoire</h1>
		<div class="card">
			<div class="infobanner">
				<span style="font-size:20px;margin-top:1px;">ℹ️</span>
				<div><strong>Informations</strong><p>Veuillez remplir les informations ci-dessous et sélectionner le fichier de votre mémoire.</p></div>
			</div>
 
			<?php if($success) echo '<div style="padding:20px 28px 0;"><div class="ok">'.$success.'</div></div>'; ?>
			<?php if($error)   echo '<div style="padding:20px 28px 0;"><div class="ko">'.htmlspecialchars($error).'</div></div>'; ?>
 
			<form method="POST" action="" enctype="multipart/form-data">
				<div class="fbody">
 
					<div class="row">
						<div class="lbl">Titre du mémoire <span>*</span></div>
						<input type="text" name="titre" class="fc" placeholder="Entrez le titre de votre mémoire" value="<?=htmlspecialchars($_POST['titre']??'')?>">
					</div>
 
					<div class="row">
						<div class="lbl">Catégorie <span>*</span></div>
						<select name="categorie" class="fc">
							<option value="">Sélectionnez une catégorie</option>
							<?php foreach($categories as $c) echo '<option value="'.$c.'"'.(($_POST['categorie']??'')===$c?' selected':'').'>'.$c.'</option>'; ?>
						</select>
					</div>
 
					<div class="row">
						<div class="lbl">Filière / Département <span>*</span></div>
						<select name="filiere" class="fc">
							<option value="">Sélectionnez votre filière ou département</option>
							<?php foreach($filieres as $f) echo '<option value="'.$f.'"'.(($_POST['filiere']??'')===$f?' selected':'').'>'.$f.'</option>'; ?>
						</select>
					</div>
 
					<div class="row">
						<div class="lbl">Résumé <span>*</span></div>
						<div>
							<textarea name="resume" class="fc" placeholder="Entrez un résumé de votre mémoire" maxlength="500" oninput="document.getElementById('cc').textContent=this.value.length"><?=htmlspecialchars($_POST['resume']??'')?></textarea>
							<div class="cc"><span id="cc"><?=strlen($_POST['resume']??'')?></span>/500</div>
						</div>
					</div>
 
					<div class="row" style="border-bottom:none;">
						<div class="lbl">Fichier du mémoire <span>*</span></div>
						<div>
							<div class="dz" id="dz" onclick="document.getElementById('fi').click()">
								<div class="uico">☁️</div>
								<p>Glissez-déposez votre fichier ici</p>
								<div class="or">ou</div>
								<button type="button" class="brow">Parcourir les fichiers</button>
								<div id="sf" class="sf"></div>
							</div>
							<div class="fmeta">Formats acceptés : <span>PDF</span> &nbsp;|&nbsp; Taille maximale : <b>20 Mo</b></div>
							<input type="file" id="fi" name="fichier" accept="application/pdf">
						</div>
					</div>
				</div>
 
				<div class="footer">
					<button type="button" class="bcan" onclick="window.location.href='dashboard.php'">Annuler</button>
					<input type="submit" name="btn" value="Uploader" class="bupl">
				</div>
			</form>
		</div>
	</main>
</div>
 
<script>
var fi=document.getElementById('fi'),sf=document.getElementById('sf'),dz=document.getElementById('dz');
fi.addEventListener('change',function(){if(fi.files[0])sf.textContent='📄 '+fi.files[0].name;});
dz.addEventListener('dragover',function(e){e.preventDefault();dz.classList.add('over');});
dz.addEventListener('dragleave',function(){dz.classList.remove('over');});
dz.addEventListener('drop',function(e){e.preventDefault();dz.classList.remove('over');var f=e.dataTransfer.files[0];if(f&&f.type==='application/pdf'){fi.files=e.dataTransfer.files;sf.textContent='📄 '+f.name;}});
</script>
</body>
</html>