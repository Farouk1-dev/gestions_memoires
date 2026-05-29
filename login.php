<?php
session_start();
$error='';
$db=new PDO('mysql:host=127.0.0.1;dbname=uatm_gasa','root','');

if(isset($_POST['btn'])){
	$stmt=$db->prepare('SELECT * FROM utilisateurs WHERE email=? AND actif=1 LIMIT 1');
	$stmt->execute([$_POST['email']]);
	$user=$stmt->fetch();
	if($user && password_verify($_POST['password'],$user['mot_de_passe'])){
		if(isset($_POST['admin']) && $user['role']!='admin'){
			$error="Accès administrateur refusé.";
		}else{
			$_SESSION['user']=['id'=>$user['id'],'nom'=>$user['nom'].' '.$user['prenom'],'role'=>$user['role']];
			header('Location: dashboard.php'); exit;
		}
	}else{
		$error="Email ou mot de passe incorrect.";
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>UATM GASA – Connexion</title>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Source Sans 3',sans-serif;display:flex;height:100vh;background:#f4f6fb;}
.left{width:48%;background:linear-gradient(160deg,#0a2a6e,#0d3b9e,#1a4fc4);display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 40px;color:#fff;text-align:center;position:relative;overflow:hidden;}
.left::before{content:'';position:absolute;inset:0;background:url('https://images.unsplash.com/photo-1562774053-701939374585?w=900&auto=format&fit=crop&q=40') center/cover no-repeat;opacity:.13;}
.left>*{position:relative;z-index:1;}
.left img{width:130px;height:130px;object-fit:contain;margin-bottom:24px;}
.left h1{font-size:25px;font-weight:700;letter-spacing:1px;margin-bottom:6px;}
.left .sub{font-size:11px;letter-spacing:3px;color:#f0c040;margin-bottom:22px;text-transform:uppercase;}
.left .ico{font-size:42px;margin-bottom:18px;}
.left p{font-size:14.5px;line-height:1.65;color:rgba(255,255,255,.85);max-width:310px;}
.right{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 24px;background:#fff;}
.card{width:100%;max-width:450px;}
.card .top{display:flex;align-items:center;gap:14px;margin-bottom:28px;}
.card .top .av{width:54px;height:54px;background:#0d3b9e;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;}
.card .top h2{font-size:27px;font-weight:700;color:#111;}
.card .top p{font-size:13.5px;color:#777;margin-top:2px;}
.err{background:#fdecea;border:1px solid #f5c6c6;color:#c0392b;padding:10px 14px;border-radius:8px;font-size:14px;margin-bottom:18px;}
.grp{margin-bottom:18px;}
.grp label{display:block;font-size:13.5px;font-weight:600;color:#333;margin-bottom:7px;}
.wrap{display:flex;align-items:center;border:1.5px solid #dde2ee;border-radius:10px;padding:0 13px;transition:border-color .2s;}
.wrap:focus-within{border-color:#0d3b9e;}
.wrap span{color:#aab;font-size:16px;margin-right:9px;}
.wrap input{flex:1;border:none;outline:none;padding:12px 0;font-size:14.5px;font-family:inherit;color:#222;background:transparent;}
.wrap input::placeholder{color:#bbc;}
.wrap button{background:none;border:none;cursor:pointer;color:#aab;font-size:17px;padding:0;}
.forgot{display:block;text-align:right;font-size:12.5px;color:#0d3b9e;text-decoration:none;margin-top:5px;}
.forgot:hover{text-decoration:underline;}
.btn1{width:100%;padding:13px;background:#0d3b9e;color:#fff;border:none;border-radius:10px;font-size:15.5px;font-weight:600;cursor:pointer;font-family:inherit;transition:background .2s;margin-top:6px;}
.btn1:hover{background:#0a2d7e;}
.div{text-align:center;color:#bbb;font-size:13px;margin:16px 0;position:relative;}
.div::before,.div::after{content:'';position:absolute;top:50%;width:44%;height:1px;background:#e5e5e5;}
.div::before{left:0;}.div::after{right:0;}
.btn2{width:100%;padding:12px;background:#fff;color:#0d3b9e;border:1.5px solid #0d3b9e;border-radius:10px;font-size:14.5px;font-weight:600;cursor:pointer;font-family:inherit;transition:background .2s;}
.btn2:hover{background:#eef2fb;}
.hint{text-align:center;margin-top:26px;font-size:13.5px;color:#777;}
.hint a{color:#0d3b9e;text-decoration:none;font-weight:600;}
.hint a:hover{text-decoration:underline;}
@media(max-width:700px){.left{display:none;}}
</style>
</head>
<body>
<div class="left">
	<img src="logo.png" alt="UATM Logo">
	<h1>UATM GASA FORMATION</h1>
	<div class="sub">Gestion des mémoires</div>
	<div class="ico">📖</div>
	<p>Plateforme dédiée à la gestion, au dépôt, à l'évaluation et à la valorisation des mémoires de fin d'études.</p>
</div>
<div class="right">
	<div class="card">
		<div class="top">
			<div class="av">👤</div>
			<div><h2>Connexion</h2><p>Connectez-vous à votre compte</p></div>
		</div>
		<?php if($error) echo '<div class="err">'.htmlspecialchars($error).'</div>'; ?>
		<form method="POST" action="">
			<div class="grp">
				<label>Email</label>
				<div class="wrap">
					<span>✉️</span>
					<input type="email" name="email" placeholder="exemple@uatm.ga" value="<?=htmlspecialchars($_POST['email']??'')?>" required>
				</div>
			</div>
			<div class="grp">
				<label>Mot de passe</label>
				<div class="wrap">
					<span>🔒</span>
					<input type="password" id="pw" name="password" placeholder="••••••••••" required>
					<button type="button" onclick="togglePw()">👁️</button>
				</div>
				<a href="forgot_password.php" class="forgot">Mot de passe oublié ?</a>
			</div>
			<input type="submit" name="btn" value="Se connecter" class="btn1">
			<div class="div">OU</div>
			<button type="submit" name="admin" value="1" class="btn2">🛡️ &nbsp;Se connecter en tant qu'administrateur</button>
		</form>
		<p class="hint">Vous n'avez pas de compte ? <a href="mailto:admin@uatm.ga">Contactez l'administrateur</a></p>
	</div>
</div>
<script>
function togglePw(){var p=document.getElementById('pw');p.type=p.type==='password'?'text':'password';}
</script>
</body>
</html>
